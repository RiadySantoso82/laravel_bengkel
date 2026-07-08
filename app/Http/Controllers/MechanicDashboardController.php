<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\ServiceType;
use App\Models\Sparepart;
use App\Models\ChecklistItem;
use App\Models\ServiceOrderChecklist;
use App\Models\ServiceOrderPhoto;
use App\Models\PartRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\PartRequestDetail;
use App\Models\PartReturn;

class MechanicDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'mekanik') {
                abort(403);
            }
            return $next($request);
        });
    }

    private function getMechanic()
    {
        return Auth::user()->mechanic;
    }

    private function mechanicId()
    {
        $m = $this->getMechanic();
        return $m ? $m->id : 0;
    }

    public function index()
    {
        $mid = $this->mechanicId();
        $activeCount = ServiceOrder::where('mechanic_id', $mid)->whereIn('status', ['queued', 'in_progress', 'waiting_part'])->count();
        $completedCount = ServiceOrder::where('mechanic_id', $mid)->whereIn('status', ['done', 'picked_up'])->count();

        $chartDays = [];
        $chartCounts = [];
        $heatmap = [];
        $slots = ['Sebelum 08:00'];
        for ($h = 8; $h <= 20; $h += 2) {
            $slots[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00-' . str_pad($h + 2, 2, '0', STR_PAD_LEFT) . ':00';
        }
        $slots[] = '22:00+';

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayLabel = date('d/m', strtotime($date));

            $count = ServiceOrder::where('mechanic_id', $mid)
                ->whereIn('status', ['done', 'picked_up'])
                ->whereDate('actual_finish', $date)
                ->count();
            $chartDays[] = $dayLabel;
            $chartCounts[] = $count;

            $orders = ServiceOrder::where('mechanic_id', $mid)
                ->whereIn('status', ['done', 'picked_up'])
                ->whereNotNull('actual_finish')
                ->whereDate('actual_finish', $date)
                ->pluck('actual_finish');

            $slotData = [];
            $cntBefore8 = 0;
            foreach ($orders as $finish) {
                $hour = (int) date('G', strtotime($finish));
                if ($hour < 8) { $cntBefore8++; continue; }
                if ($hour >= 22) { continue; }
                for ($h = 8; $h <= 20; $h += 2) {
                    if ($hour >= $h && $hour < $h + 2) {
                        $slotData[$h] = ($slotData[$h] ?? 0) + 1;
                        break;
                    }
                }
            }
            $cntAfter22 = 0;
            foreach ($orders as $finish) {
                $hour = (int) date('G', strtotime($finish));
                if ($hour >= 22) $cntAfter22++;
            }
            $row = [$cntBefore8];
            for ($h = 8; $h <= 20; $h += 2) {
                $row[] = $slotData[$h] ?? 0;
            }
            $row[] = $cntAfter22;
            $slotData = $row;
            $heatmap[] = $slotData;
        }

        return view('mechanic.dashboard', compact('activeCount', 'completedCount', 'chartDays', 'chartCounts', 'heatmap', 'slots'));
    }

    public function myServices()
    {
        $data = ServiceOrder::with(['customer', 'vehicle', 'partRequests.details'])->where('mechanic_id', $this->mechanicId())->latest()->paginate(20);
        return view('mechanic.services', compact('data'));
    }

    public function detail(ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->mechanic_id !== $this->mechanicId()) {
            abort(403);
        }
        $serviceOrder->load(['customer', 'vehicle', 'details']);
        $serviceTypes = ServiceType::pluck('name', 'id');
        $spareparts = Sparepart::pluck('name', 'id');

        $masterItems = ChecklistItem::where('is_active', true)->orderBy('name')->get();
        $existingChecklist = ServiceOrderChecklist::where('order_id', $serviceOrder->id)->get()->keyBy('checklist_item_id');
        $photos = ServiceOrderPhoto::where('order_id', $serviceOrder->id)->get();
        $partRequests = PartRequest::with('details.sparepart')->where('order_id', $serviceOrder->id)->latest()->get();

        return view('mechanic.detail', compact('serviceOrder', 'masterItems', 'existingChecklist', 'photos', 'partRequests', 'serviceTypes', 'spareparts'));
    }

    public function history(Request $request)
    {
        $from = $request->date_from;
        $to = $request->date_to;

        if ($from && $to) {
            $d1 = min($from, $to);
            $d2 = max($from, $to);
            if (strtotime($d2) - strtotime($d1) > 31 * 86400) {
                $d2 = date('Y-m-d', strtotime($d1 . ' +31 days'));
            }
        } elseif ($from) {
            $d1 = $from;
            $d2 = date('Y-m-d', strtotime($d1 . ' +31 days'));
        } elseif ($to) {
            $d2 = $to;
            $d1 = date('Y-m-d', strtotime($d2 . ' -31 days'));
        } else {
            $d1 = date('Y-m-d', strtotime('-30 days'));
            $d2 = date('Y-m-d');
        }

        $query = ServiceOrder::with(['customer', 'vehicle'])->where('mechanic_id', $this->mechanicId())->whereIn('status', ['done', 'picked_up']);

        if ($d1) {
            $query->whereDate('actual_finish', '>=', $d1);
        }
        if ($d2) {
            $query->whereDate('actual_finish', '<=', $d2);
        }

        $data = $query->latest('actual_finish')->paginate(20)->appends(request()->only(['date_from', 'date_to']));
        return view('mechanic.history', compact('data', 'd1', 'd2'));
    }

    public function saveProgress(Request $request, ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->mechanic_id !== $this->mechanicId()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:queued,in_progress,waiting_part,done,picked_up',
            'checklist' => 'nullable|array',
            'checklist.*.id' => 'required|exists:checklist_items,id',
            'checklist.*.checked' => 'boolean',
            'checklist.*.notes' => 'nullable|string|max:500',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $mid = $this->mechanicId();
        $now = now();

        DB::transaction(function () use ($request, $serviceOrder, $mid, $now) {
            if ($request->has('checklist')) {
                foreach ($request->checklist as $item) {
                    ServiceOrderChecklist::updateOrCreate(
                        ['order_id' => $serviceOrder->id, 'checklist_item_id' => $item['id']],
                        [
                            'mechanic_id' => $mid,
                            'is_checked' => !empty($item['checked']),
                            'notes' => $item['notes'] ?? null,
                            'checked_at' => !empty($item['checked']) ? $now : null,
                        ]
                    );
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $img = Image::make($file);
                    $img->resize(1200, null, function ($c) { $c->aspectRatio(); });
                    $img->encode('jpg', 75);
                    $filename = uniqid() . '.jpg';
                    $relPath = 'service-photos/' . $serviceOrder->id . '/' . $filename;
                    Storage::disk('public')->put($relPath, $img->getEncoded());
                    ServiceOrderPhoto::create([
                        'order_id' => $serviceOrder->id,
                        'user_id' => Auth::id(),
                        'type' => 'before',
                        'photo_url' => Storage::url($relPath),
                        'caption' => null,
                    ]);
                }
            }

            $data = ['status' => $request->status];
            if (in_array($request->status, ['done', 'picked_up'])) {
                $data['actual_finish'] = $now;
            }
            if ($request->status === 'in_progress' && $serviceOrder->status === 'queued') {
                $data['status'] = 'in_progress';
            }
            $serviceOrder->update($data);
        });

        return redirect()->route('mechanic.detail', $serviceOrder)->with('success', 'Progress berhasil disimpan.');
    }

    public function requestPart(Request $request, ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->mechanic_id !== $this->mechanicId()) {
            abort(403);
        }

        $request->validate([
            'parts' => 'required|array|min:1',
            'parts.*.part_id' => 'required|exists:spareparts,id',
            'parts.*.qty' => 'required|integer|min:1',
        ]);

        $pr = PartRequest::create([
            'order_id' => $serviceOrder->id,
            'mechanic_id' => $this->mechanicId(),
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        foreach ($request->parts as $p) {
            PartRequestDetail::create([
                'part_request_id' => $pr->id,
                'part_id' => $p['part_id'],
                'qty_requested' => $p['qty'],
                'qty_fulfilled' => 0,
                'status' => 'pending',
            ]);
        }

        $serviceOrder->update(['status' => 'waiting_part']);

        return redirect()->route('mechanic.detail', $serviceOrder)->with('success', 'Request part berhasil dikirim.');
    }

    public function profile()
    {
        $mechanic = $this->getMechanic();
        return view('mechanic.profile', compact('mechanic'));
    }

    public function updateProfile(Request $request)
    {
        $mechanic = $this->getMechanic();
        $request->validate(['phone' => 'nullable|max:50', 'specialization' => 'nullable|max:255']);
        if ($mechanic) {
            $mechanic->update($request->only(['phone', 'specialization']));
        }
        return redirect()->route('mechanic.profile')->with('success', 'Profil berhasil diupdate.');
    }

    public function returnPart(Request $request, PartRequestDetail $detail)
    {
        $order = ServiceOrder::findOrFail($detail->partRequest->order_id);
        if ($order->mechanic_id !== $this->mechanicId()) abort(403);

        $request->validate([
            'qty_returned' => 'required|integer|min:1|max:' . ($detail->qty_fulfilled - $detail->qty_returned),
            'reason' => 'required|in:tidak_cocok,tidak_dipakai',
        ]);

        $qtyReturn = (int) $request->qty_returned;

        PartReturn::create([
            'part_request_detail_id' => $detail->id,
            'mechanic_id' => $this->mechanicId(),
            'qty_returned' => $qtyReturn,
            'reason' => $request->reason,
            'returned_at' => now(),
        ]);

        $detail->increment('qty_returned', $qtyReturn);
        $detail->refresh();

        $used = $detail->qty_fulfilled - $detail->qty_returned;
        if ($used <= 0) {
            $detail->update(['status' => 'returned']);
        } elseif ($detail->qty_returned > 0) {
            $detail->update(['status' => 'partial']);
        }

        return redirect()->route('mechanic.detail', $order)->with('success', 'Part berhasil diretur, menunggu konfirmasi admin.');
    }
}

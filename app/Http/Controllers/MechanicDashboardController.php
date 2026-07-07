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
        return view('mechanic.dashboard', compact('activeCount', 'completedCount'));
    }

    public function myServices()
    {
        $data = ServiceOrder::with(['customer', 'vehicle', 'partRequests.details'])->where('mechanic_id', $this->mechanicId())->latest()->get();
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

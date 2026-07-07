<?php

namespace App\Http\Controllers;

use App\Models\PartRequest;
use App\Models\PartRequestDetail;
use App\Models\StockMovement;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['admin', 'kasir'])) abort(403);
            return $next($request);
        });
    }

    public function index()
    {
        $data = PartRequest::with(['order.customer', 'details.sparepart', 'mechanic'])
            ->whereIn('status', ['requested', 'partial'])
            ->latest()
            ->get();
        return view('part_request.index', compact('data'));
    }

    public function fulfill(Request $request, PartRequestDetail $detail)
    {
        $request->validate([
            'qty_fulfilled' => 'required|integer|min:0|max:' . $detail->qty_requested,
        ]);

        DB::transaction(function () use ($request, $detail) {
            $qty = (int) $request->qty_fulfilled;
            $detail->update([
                'qty_fulfilled' => $qty,
                'status' => $qty >= $detail->qty_requested ? 'fulfilled' : ($qty > 0 ? 'partial' : 'pending'),
                'fulfilled_by' => Auth::id(),
                'fulfilled_at' => now(),
            ]);

            if ($qty > 0) {
                StockMovement::create([
                    'part_id' => $detail->part_id,
                    'type' => 'out',
                    'qty' => $qty,
                    'reference_id' => $detail->id,
                ]);
                Sparepart::where('id', $detail->part_id)->decrement('stock_qty', $qty);
            }

            $pr = $detail->partRequest;
            $allDetails = $pr->details;
            $statuses = $allDetails->pluck('status')->unique();
            if ($statuses->every(fn($s) => $s === 'fulfilled')) {
                $pr->update(['status' => 'fulfilled']);
            } elseif ($statuses->contains('fulfilled') || $statuses->contains('partial')) {
                $pr->update(['status' => 'partial']);
            }
        });

        return redirect()->route('part-requests.index')->with('success', 'Fulfillment berhasil disimpan.');
    }
}

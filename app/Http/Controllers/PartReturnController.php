<?php

namespace App\Http\Controllers;

use App\Models\PartReturn;
use App\Models\StockMovement;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartReturnController extends Controller
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
        $data = PartReturn::with(['detail.partRequest.order.customer', 'detail.sparepart', 'mechanic', 'confirmedBy'])
            ->whereNull('confirmed_at')
            ->latest()
            ->get();
        return view('part_return.index', compact('data'));
    }

    public function confirm(PartReturn $partReturn)
    {
        DB::transaction(function () use ($partReturn) {
            $partReturn->update([
                'confirmed_by' => Auth::id(),
                'confirmed_at' => now(),
            ]);

            StockMovement::create([
                'part_id' => $partReturn->detail->part_id,
                'type' => 'in',
                'qty' => $partReturn->qty_returned,
                'reference_id' => $partReturn->detail->id,
            ]);
            Sparepart::where('id', $partReturn->detail->part_id)->increment('stock_qty', $partReturn->qty_returned);
        });

        return redirect()->route('part-returns.index')->with('success', 'Retur dikonfirmasi, stok sudah dikembalikan.');
    }
}

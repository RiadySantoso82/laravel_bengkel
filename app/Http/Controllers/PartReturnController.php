<?php

namespace App\Http\Controllers;

use App\Models\PartReturn;
use App\Models\StockMovement;
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
                'movement_type' => 'in',
                'source_type' => 'part_return',
                'source_id' => $partReturn->detail->id,
                'qty' => $partReturn->qty_returned,
                'transaction_date' => now(),
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('part-returns.index')->with('success', 'Retur dikonfirmasi, stok sudah dikembalikan.');
    }
}

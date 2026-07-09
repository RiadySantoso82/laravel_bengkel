<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['admin', 'kasir'])) abort(403);
            return $next($request);
        });
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with('sparepart')
            ->when($request->part_id, fn($q, $v) => $q->where('part_id', $v))
            ->when($request->type, fn($q, $v) => $q->where('movement_type', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        $data = $query->latest()->paginate(50);
        $spareparts = Sparepart::orderBy('name')->pluck('name', 'id');

        return view('report.movements', compact('data', 'spareparts'));
    }

    public function stock()
    {
        $data = Sparepart::with(['category', 'unit'])->orderBy('name')->get();
        return view('report.stock', compact('data'));
    }
}

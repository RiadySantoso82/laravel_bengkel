<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
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
        $data = StockAdjustment::with(['sparepart', 'user'])->latest()->paginate(20);
        return view('stock_adjustment.index', compact('data'));
    }

    public function create()
    {
        return view('stock_adjustment.create');
    }

    public function search(Request $request)
    {
        $q = $request->q;
        $categoryId = $request->category_id;
        $page = $request->input('page', 1);
        $query = Sparepart::query();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%");
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $total = $query->count();
        $results = $query->with('category', 'unit')->orderBy('name')->skip(($page - 1) * 15)->take(15)->get();

        $categories = \App\Models\SparepartCategory::orderBy('name')->get();

        if ($request->ajax()) {
            $items = $results->map(function ($r) {
                $stock = $r->stock_qty;
                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'code' => $r->code,
                    'category' => $r->category ? $r->category->name : '',
                    'unit' => $r->unit ? ($r->unit->symbol ?: $r->unit->name ?: '') : '',
                    'stock_qty' => $stock,
                    'min_stock' => $r->min_stock,
                ];
            });
            return response()->json([
                'results' => $items,
                'categories' => $categories,
                'total' => $total,
                'page' => $page,
                'last_page' => ceil($total / 15),
            ]);
        }

        return view('stock_adjustment.search', compact('results', 'categories', 'q', 'categoryId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'part_id' => 'required|exists:spareparts,id',
            'adjustment_type' => 'required|in:in,out',
            'qty' => 'required|integer|min:1',
            'reason' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        $qty = $request->adjustment_type === 'in' ? $request->qty : -$request->qty;

        DB::transaction(function () use ($request, $qty) {
            $adj = StockAdjustment::create([
                'part_id' => $request->part_id,
                'user_id' => Auth::id(),
                'qty' => $qty,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'transaction_date' => $request->transaction_date,
            ]);

            StockMovement::create([
                'part_id' => $request->part_id,
                'movement_type' => $request->adjustment_type,
                'source_type' => 'stock_adjustment',
                'source_id' => $adj->id,
                'qty' => $request->qty,
                'transaction_date' => $request->transaction_date,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjustment berhasil disimpan.');
    }
}

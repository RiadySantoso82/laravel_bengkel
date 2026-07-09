<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\StockBatch;
use App\Models\Sparepart;
use App\Services\FifoService;
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

    public function index(Request $request)
    {
        $from = $request->date_from ?? date('Y-m-d', strtotime('-30 days'));
        $to = $request->date_to ?? date('Y-m-d');

        $query = StockAdjustment::with(['sparepart', 'user']);

        if ($from) $query->whereDate('transaction_date', '>=', $from);
        if ($to) $query->whereDate('transaction_date', '<=', $to);

        $data = $query->latest('created_at')->paginate(20)->appends(['date_from' => $from, 'date_to' => $to]);

        return view('stock_adjustment.index', compact('data', 'from', 'to'));
    }

    public function create()
    {
        return view('stock_adjustment.create');
    }

    public function search(Request $request)
    {
        $q = $request->q;
        $categoryId = $request->category_id;
        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $query = Sparepart::query();
        $countQuery = Sparepart::query();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
            });
            $countQuery->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
            $countQuery->where('category_id', $categoryId);
        }

        $total = $countQuery->count();
        $results = $query->with('category', 'unit')->orderBy('name')->skip(($page - 1) * $perPage)->take($perPage)->get();

        $categories = \App\Models\SparepartCategory::orderBy('name')->get();

        $items = $results->map(function ($r) {
            $stock = $r->stock_qty;
            $catName = $r->category ? $r->category->name : '';
            $unitStr = $r->unit ? ($r->unit->symbol ?: $r->unit->name ?: '') : '';
            return [
                'id' => $r->id,
                'name' => $r->name,
                'code' => $r->code,
                'category' => $catName,
                'unit' => $unitStr,
                'stock_qty' => $stock,
                'min_stock' => $r->min_stock,
            ];
        });
        return response()->json([
            'results' => $items,
            'categories' => $categories,
            'total' => $total,
            'page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'part_id' => 'required|exists:spareparts,id',
            'adjustment_type' => 'required|in:in,out',
            'qty' => 'required|integer|min:1',
            'buy_price' => 'nullable|numeric|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        $sparepart = Sparepart::findOrFail($request->part_id);
        if ($request->adjustment_type === 'out' && $sparepart->stock_qty < $request->qty) {
            return back()->withInput()->withErrors(['qty' => 'Stok tidak mencukupi. Stok saat ini: ' . $sparepart->stock_qty]);
        }

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

            $movement = StockMovement::create([
                'part_id' => $request->part_id,
                'movement_type' => $request->adjustment_type,
                'source_type' => 'stock_adjustment',
                'source_id' => $adj->id,
                'qty' => $request->qty,
                'transaction_date' => $request->transaction_date,
                'created_by' => Auth::id(),
            ]);

            if ($request->adjustment_type === 'in') {
                FifoService::createBatch(
                    $request->part_id,
                    $request->qty,
                    $request->buy_price ?? 0,
                    $request->transaction_date
                );
            } else {
                $result = FifoService::allocateOut($movement, $request->part_id, $request->qty);
                if ($result['remaining'] > 0) {
                    throw new \Exception("Stok tidak mencukupi. Kurang {$result['remaining']} pcs.");
                }
            }
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjustment berhasil disimpan.');
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['sparepart', 'user']);
        return view('stock_adjustment.show', ['adjustment' => $stockAdjustment]);
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        $sparepart = $stockAdjustment->sparepart;
        $qty = $stockAdjustment->qty;

        if ($qty > 0) {
            $currentStock = $sparepart->stock_qty;
            if ($currentStock - $qty < 0) {
                return back()->with('error', "Tidak bisa dihapus. Stok {$sparepart->name} saat ini {$currentStock}, lebih kecil dari qty adjustment ({$qty}). Hapus adjustment ini akan membuat stok minus.");
            }
        }

        DB::transaction(function () use ($stockAdjustment) {
            StockMovement::where('source_type', 'stock_adjustment')
                ->where('source_id', $stockAdjustment->id)
                ->delete();
            $stockAdjustment->delete();
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjustment berhasil dihapus.');
    }
}

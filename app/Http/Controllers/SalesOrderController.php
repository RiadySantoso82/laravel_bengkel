<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\StockMovement;
use App\Models\Sparepart;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrderController extends Controller
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

        $query = SalesOrder::with('customer', 'user', 'details');
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);

        $data = $query->latest('created_at')->paginate(20)->appends(['date_from' => $from, 'date_to' => $to]);

        return view('sales_order.index', compact('data', 'from', 'to'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();
        return view('sales_order.create', compact('customers', 'paymentMethods'));
    }

    public function searchPart(Request $request)
    {
        $q = $request->q;
        $query = Sparepart::query();
        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%");
            });
        }
        $results = $query->with('category', 'unit')->whereHas('batches', fn($q) => $q->where('qty_remaining', '>', 0))->orderBy('name')->limit(20)->get();
        $items = $results->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'code' => $r->code,
                'category' => $r->category ? $r->category->name : '',
                'unit' => $r->unit ? ($r->unit->symbol ?: $r->unit->name ?: '') : '',
                'stock_qty' => $r->stock_qty,
                'sell_price' => $r->sell_price,
            ];
        });
        return response()->json(['results' => $items]);
    }

    public function store(Request $request)
    {
        $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;
        if (!is_array($items)) $items = [];

        $request->merge(['items' => $items]);
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_status' => 'required|in:pending,paid',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.part_id' => 'required|exists:spareparts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.sell_price' => 'required|numeric|min:0',
        ]);

        $discount = (float) ($request->discount ?? 0);
        $isPaid = $request->payment_status === 'paid';

        DB::transaction(function () use ($request, $items, $discount, $isPaid) {
            $so = SalesOrder::create([
                'customer_id' => $request->customer_id,
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'discount' => $discount,
                'payment_status' => $request->payment_status,
            ]);

            if ($request->payment_method_id && $isPaid) {
                PaymentTransaction::create([
                    'reference_type' => 'sales_order',
                    'reference_id' => $so->id,
                    'payment_method_id' => $request->payment_method_id,
                    'created_by' => Auth::id(),
                    'amount' => max(0, $request->total_amount - $discount),
                    'paid_at' => now(),
                ]);
            }

            $totalCost = 0;
            foreach ($items as $item) {
                $costPrice = 0;

                if ($isPaid) {
                    $movement = StockMovement::create([
                        'part_id' => $item['part_id'],
                        'movement_type' => 'out',
                        'source_type' => 'sales_order_detail',
                        'source_id' => $so->id,
                        'qty' => $item['qty'],
                        'transaction_date' => now(),
                        'created_by' => Auth::id(),
                    ]);

                    $result = FifoService::allocateOut($movement, $item['part_id'], $item['qty']);
                    if ($result['remaining'] > 0) {
                        throw new \Exception("Stok {$item['part_id']} tidak mencukupi. Kurang {$result['remaining']} pcs.");
                    }
                    $costPrice = $result['total_cost'];
                    $totalCost += $costPrice;
                }

                SalesOrderDetail::create([
                    'sales_order_id' => $so->id,
                    'part_id' => $item['part_id'],
                    'qty' => $item['qty'],
                    'sell_price' => $item['sell_price'],
                    'cost_price' => $costPrice,
                ]);
            }
        });

        $msg = $isPaid ? 'Penjualan berhasil diproses.' : 'Penjualan pending berhasil disimpan.';
        return redirect()->route('sales-orders.index')->with('success', $msg);
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'user', 'details.sparepart', 'payments.paymentMethod', 'payments.creator']);
        return view('sales_order.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        if ($salesOrder->payment_status !== 'pending') {
            return redirect()->route('sales-orders.index')->with('error', 'Hanya order pending yang bisa diedit.');
        }
        $salesOrder->load('details.sparepart');
        $savedItems = $salesOrder->details->map(function($d) {
            $sp = $d->sparepart;
            return [
                'id' => $d->part_id,
                'name' => $sp->name ?? 'Part #'.$d->part_id,
                'code' => $sp->code ?? '',
                'stock_qty' => $sp->stock_qty ?? 0,
                'stock' => $sp->stock_qty ?? 0,
                'sell_price' => (float) $d->sell_price,
                'qty' => $d->qty,
            ];
        })->values();
        $customers = Customer::orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();
        return view('sales_order.edit', compact('salesOrder', 'savedItems', 'customers', 'paymentMethods'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        Log::error('masuk sini');
        if ($salesOrder->payment_status !== 'pending') {
            return redirect()->route('sales-orders.index')->with('error', 'Hanya order pending yang bisa diupdate.');
        }

        $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;
        if (!is_array($items)) $items = [];
        $request->merge(['items' => $items]);

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.part_id' => 'required|exists:spareparts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.sell_price' => 'required|numeric|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        $processPayment = $request->filled('payment_method_id');

        DB::transaction(function () use ($request, $salesOrder, $items, $processPayment) {
            $salesOrder->update([
                'customer_id' => $request->customer_id,
            ]);

            $salesOrder->details()->delete();
            $newDetails = [];
            foreach ($items as $item) {
                $newDetails[] = SalesOrderDetail::create([
                    'sales_order_id' => $salesOrder->id,
                    'part_id' => $item['part_id'],
                    'qty' => $item['qty'],
                    'sell_price' => $item['sell_price'],
                    'cost_price' => 0,
                ]);
            }
            $newTotal = collect($items)->sum(fn($i) => $i['qty'] * $i['sell_price']);
            $salesOrder->update(['total_amount' => $newTotal, 'discount' => $request->discount ?? 0]);

            if ($processPayment) {
                $total = $newTotal - ($request->discount ?? 0);
                PaymentTransaction::create([
                    'reference_type' => 'sales_order',
                    'reference_id' => $salesOrder->id,
                    'payment_method_id' => $request->payment_method_id,
                    'created_by' => Auth::id(),
                    'amount' => max(0, $total),
                    'amount_received' => $request->amount_received,
                    'change_amount' => $request->change_amount,
                    'paid_at' => now(),
                ]);

                foreach ($newDetails as $d) {
                    $movement = StockMovement::create([
                        'part_id' => $d->part_id,
                        'movement_type' => 'out',
                        'source_type' => 'sales_order_detail',
                        'source_id' => $salesOrder->id,
                        'qty' => $d->qty,
                        'transaction_date' => now(),
                        'created_by' => Auth::id(),
                    ]);
                    $result = FifoService::allocateOut($movement, $d->part_id, $d->qty);
                    if ($result['remaining'] > 0) {
                        throw new \Exception("Stok part ID {$d->part_id} tidak mencukupi.");
                    }
                    $d->update(['cost_price' => $result['total_cost']]);
                }
                $salesOrder->update(['payment_status' => 'paid']);
            }
        });

        $msg = $processPayment ? 'Order berhasil diupdate & pembayaran diproses.' : 'Order pending berhasil diupdate.';
        return redirect()->route('sales-orders.show', $salesOrder)->with('success', $msg);
    }

    public function processPayment(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->payment_status !== 'pending') {
            return redirect()->route('sales-orders.index')->with('error', 'Order ini sudah diproses.');
        }

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount_received' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $salesOrder) {
            $total = $salesOrder->total_amount - $salesOrder->discount;

            PaymentTransaction::create([
                'reference_type' => 'sales_order',
                'reference_id' => $salesOrder->id,
                'payment_method_id' => $request->payment_method_id,
                'created_by' => Auth::id(),
                'amount' => $total,
                'amount_received' => $request->amount_received,
                'change_amount' => $request->change_amount,
                'paid_at' => now(),
            ]);

            foreach ($salesOrder->details as $d) {
                $movement = StockMovement::create([
                    'part_id' => $d->part_id,
                    'movement_type' => 'out',
                    'source_type' => 'sales_order_detail',
                    'source_id' => $salesOrder->id,
                    'qty' => $d->qty,
                    'transaction_date' => now(),
                    'created_by' => Auth::id(),
                ]);

                $result = FifoService::allocateOut($movement, $d->part_id, $d->qty);
                if ($result['remaining'] > 0) {
                    throw new \Exception("Stok part ID {$d->part_id} tidak mencukupi. Kurang {$result['remaining']} pcs.");
                }

                $d->update(['cost_price' => $result['total_cost']]);
            }

            $salesOrder->update(['payment_status' => 'paid']);
        });

        return redirect()->route('sales-orders.show', $salesOrder)->with('success', 'Pembayaran berhasil diproses.');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        DB::transaction(function () use ($salesOrder) {
            foreach ($salesOrder->details as $d) {
                $movements = StockMovement::where('source_type', 'sales_order_detail')
                    ->where('source_id', $salesOrder->id)
                    ->get();
                foreach ($movements as $m) {
                    foreach ($m->allocations as $alloc) {
                        $alloc->batch()->increment('qty_remaining', $alloc->qty_taken);
                    }
                    $m->allocations()->delete();
                    $m->delete();
                }
            }
            $salesOrder->details()->delete();
            $salesOrder->delete();
        });

        return redirect()->route('sales-orders.index')->with('success', 'Penjualan berhasil dihapus.');
    }
}

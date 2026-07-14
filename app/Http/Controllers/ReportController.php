<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Sparepart;
use App\Models\SalesOrder;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Invoice;
use App\Models\PaymentTransaction;
use App\Models\ServiceOrder;
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

    public function revenue(Request $request)
    {
        $from = $request->date_from ?? date('Y-m-d', strtotime('-30 days'));
        $to = $request->date_to ?? date('Y-m-d');

        $salesTotal = SalesOrder::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->sum(\DB::raw('total_amount - discount'));
        $salesCount = SalesOrder::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->count();

        $invoiceTotal = Invoice::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->sum(\DB::raw('total_amount - discount'));
        $invoiceCount = Invoice::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->count();

        $salesOrders = SalesOrder::with(['customer'])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->get()->map(function ($o) {
                return (object) [
                    'id' => $o->id,
                    'date' => $o->created_at->format('Y-m-d H:i:s'),
                    'code' => 'SO' . str_pad($o->id, 5, '0', STR_PAD_LEFT),
                    'customer' => $o->customer->name ?? 'Walk-in',
                    'total' => $o->total_amount - $o->discount,
                    'type' => 'Penjualan',
                    'route' => route('sales-orders.show', $o) . '?from=revenue',
                ];
            });

        $invoices = Invoice::with(['order.customer'])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->get()->map(function ($inv) {
                return (object) [
                    'id' => $inv->id,
                    'date' => $inv->created_at->format('Y-m-d H:i:s'),
                    'code' => 'INV' . str_pad($inv->id, 4, '0', STR_PAD_LEFT),
                    'customer' => $inv->order->customer->name ?? '-',
                    'total' => $inv->total_amount - $inv->discount,
                    'type' => 'Servis',
                    'route' => route('invoices.show', $inv) . '?from=revenue',
                ];
            });

        $allTransactions = $salesOrders->concat($invoices)->sortByDesc('date');
        $page = $request->input('page', 1);
        $perPage = 20;
        $total = $allTransactions->count();
        $items = $allTransactions->slice(($page - 1) * $perPage, $perPage)->values();

        return view('report.revenue', compact(
            'salesTotal', 'salesCount', 'invoiceTotal', 'invoiceCount',
            'items', 'total', 'page', 'perPage', 'from', 'to'
        ));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with('sparepart', 'creator')
            ->when($request->part_id, fn($q, $v) => $q->where('part_id', $v))
            ->when($request->type, fn($q, $v) => $q->where('movement_type', $v))
            ->when($request->source_type, fn($q, $v) => $q->where('source_type', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('transaction_date', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('transaction_date', '<=', $v));

        $sortOrder = $request->sort ?? 'asc';

        $movements = $query->orderBy('transaction_date', $sortOrder)->get();
        $grouped = $movements->groupBy('part_id')->sortBy(fn($items, $partId) => optional($items->first()->sparepart)->name);

        $spareparts = Sparepart::orderBy('name')->pluck('name', 'id');

        return view('report.movements', compact('grouped', 'spareparts', 'sortOrder'));
    }

    public function stock()
    {
        $data = Sparepart::with(['category', 'unit'])->orderBy('name')->get();
        return view('report.stock', compact('data'));
    }

    public function services(Request $request)
    {
        $from = $request->date_from ?? date('Y-m-d', strtotime('-30 days'));
        $to = $request->date_to ?? date('Y-m-d');

        $query = ServiceOrder::with(['customer', 'vehicle', 'mechanic', 'details']);

        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        if ($request->status) $query->where('status', $request->status);

        $data = $query->latest('created_at')->paginate(20)->appends([
            'date_from' => $from, 'date_to' => $to, 'status' => $request->status,
        ]);

        return view('report.services', compact('data', 'from', 'to'));
    }

    public function cash(Request $request)
    {
        $from = $request->date_from ?? date('Y-m-d', strtotime('-30 days'));
        $to = $request->date_to ?? date('Y-m-d');

        $fromDt = $from;
        $toDt = $to;

        $salesTotal = SalesOrder::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $fromDt)->whereDate('created_at', '<=', $toDt)
            ->sum(\DB::raw('total_amount - discount'));
        $salesCount = SalesOrder::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $fromDt)->whereDate('created_at', '<=', $toDt)
            ->count();

        $invoiceTotal = Invoice::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $fromDt)->whereDate('created_at', '<=', $toDt)
            ->sum(\DB::raw('total_amount - discount'));
        $invoiceCount = Invoice::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $fromDt)->whereDate('created_at', '<=', $toDt)
            ->count();

        $cashQuery = CashTransaction::with(['category', 'user']);
        if ($fromDt) $cashQuery->whereDate('transaction_date', '>=', $fromDt);
        if ($toDt) $cashQuery->whereDate('transaction_date', '<=', $toDt);
        if ($request->type) $cashQuery->where('type', $request->type);
        if ($request->cash_category_id) $cashQuery->where('cash_category_id', $request->cash_category_id);
        $data = $cashQuery->latest('transaction_date')->paginate(20)->appends($request->only(['date_from', 'date_to', 'type', 'cash_category_id']));

        $salesTransactions = SalesOrder::with(['customer'])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->get()->map(function ($o) {
                return (object) [
                    'date' => $o->created_at->format('d/m/Y H:i'),
                    'source' => 'Penjualan Retail',
                    'description' => 'SO' . str_pad($o->id, 5, '0', STR_PAD_LEFT) . ' - ' . ($o->customer->name ?? 'Walk-in'),
                    'amount_in' => $o->total_amount - $o->discount,
                    'amount_out' => 0,
                ];
            });

        $invoiceTransactions = Invoice::with(['order.customer'])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->get()->map(function ($inv) {
                return (object) [
                    'date' => $inv->created_at->format('d/m/Y H:i'),
                    'source' => 'Pendapatan Servis',
                    'description' => 'INV' . str_pad($inv->id, 4, '0', STR_PAD_LEFT) . ' - ' . ($inv->order->customer->name ?? '-'),
                    'amount_in' => $inv->total_amount - $inv->discount,
                    'amount_out' => 0,
                ];
            });

        $manualTransactions = CashTransaction::with('category')
            ->get()->map(function ($t) {
                return (object) [
                    'date' => $t->transaction_date ? date('d/m/Y', strtotime($t->transaction_date)) : '-',
                    'source' => ($t->category->name ?? 'Manual') . ' (' . ($t->type === 'in' ? 'Masuk' : 'Keluar') . ')',
                    'description' => $t->description ?? '-',
                    'amount_in' => $t->type === 'in' ? $t->amount : 0,
                    'amount_out' => $t->type === 'out' ? $t->amount : 0,
                ];
            });

        $all = collect($salesTransactions)->concat($invoiceTransactions)->concat($manualTransactions)->sortByDesc('date');

        $manualIn = CashTransaction::where('type', 'in')->sum('amount');
        $manualOut = CashTransaction::where('type', 'out')->sum('amount');

        $totalIn = $manualIn + $salesTotal + $invoiceTotal;
        $totalOut = $manualOut;
        $balance = $totalIn - $totalOut;

        $categories = CashCategory::orderBy('name')->get();

        return view('report.cash', compact('data', 'from', 'to', 'totalIn', 'totalOut', 'balance', 'categories',
            'salesTotal', 'salesCount', 'invoiceTotal', 'invoiceCount', 'manualIn', 'manualOut', 'all'));
    }

    public function sales(Request $request)
    {
        $from = $request->date_from ?? date('Y-m-d', strtotime('-30 days'));
        $to = $request->date_to ?? date('Y-m-d');

        $query = SalesOrder::with(['customer', 'user', 'details', 'payments.paymentMethod']);

        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        if ($request->payment_status) $query->where('payment_status', $request->payment_status);

        $clone = clone $query;
        $grandTotal = $clone->get()->sum(fn($o) => $o->total_amount - $o->discount);
        $grandCount = $clone->count();

        $data = $query->latest('created_at')->paginate(20)->appends([
            'date_from' => $from, 'date_to' => $to,
            'payment_status' => $request->payment_status,
        ]);

        return view('report.sales', compact('data', 'from', 'to', 'grandTotal', 'grandCount'));
    }
}

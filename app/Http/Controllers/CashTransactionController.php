<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\CashCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashTransactionController extends Controller
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

        $query = CashTransaction::with(['category', 'user']);

        if ($from) $query->whereDate('transaction_date', '>=', $from);
        if ($to) $query->whereDate('transaction_date', '<=', $to);
        if ($request->type) $query->where('type', $request->type);
        if ($request->cash_category_id) $query->where('cash_category_id', $request->cash_category_id);

        $data = $query->latest('transaction_date')->paginate(20)->appends($request->only(['date_from', 'date_to', 'type', 'cash_category_id']));

        $categories = CashCategory::where('is_active', true)->orderBy('name')->get();
        $totalIn = CashTransaction::where('type', 'in')->sum('amount');
        $totalOut = CashTransaction::where('type', 'out')->sum('amount');

        return view('cash_transaction.index', compact('data', 'from', 'to', 'categories', 'totalIn', 'totalOut'));
    }

    public function create()
    {
        $categories = CashCategory::where('is_active', true)->orderBy('name')->get();
        return view('cash_transaction.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cash_category_id' => 'required|exists:cash_categories,id',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        CashTransaction::create([
            'cash_category_id' => $request->cash_category_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
        ]);

        return redirect()->route('cash-transactions.index')->with('success', 'Transaksi kas berhasil disimpan.');
    }

    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();
        return redirect()->route('cash-transactions.index')->with('success', 'Transaksi kas berhasil dihapus.');
    }
}

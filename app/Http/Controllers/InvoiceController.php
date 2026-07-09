<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $data = Invoice::with(['order.customer', 'payments.paymentMethod'])->latest()->get();
        return view('invoice.index', compact('data'));
    }

    public function create()
    {
        $orders = ServiceOrder::with('customer')->whereNotIn('id', function ($q) {
            $q->select('order_id')->from('invoices');
        })->latest()->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();
        return view('invoice.form', compact('orders', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:service_orders,id|unique:invoices,order_id',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_status' => 'required|in:pending,paid,partial',
        ]);

        DB::transaction(function () use ($request) {
            $inv = Invoice::create($request->only(['order_id', 'total_amount', 'discount', 'payment_status']));

            if ($request->payment_method_id && $request->payment_status !== 'pending') {
                PaymentTransaction::create([
                    'reference_type' => 'invoice',
                    'reference_id' => $inv->id,
                    'payment_method_id' => $request->payment_method_id,
                    'created_by' => Auth::id(),
                    'amount' => max(0, $request->total_amount - ($request->discount ?? 0)),
                    'paid_at' => now(),
                ]);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function edit(Invoice $invoice)
    {
        $orders = ServiceOrder::with('customer')->latest()->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();
        return view('invoice.form', compact('invoice', 'orders', 'paymentMethods'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'order_id' => 'required|exists:service_orders,id|unique:invoices,order_id,' . $invoice->id,
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_status' => 'required|in:pending,paid,partial',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $invoice->update($request->only(['order_id', 'total_amount', 'discount', 'payment_status']));

            if ($request->payment_method_id && $request->payment_status !== 'pending' && $invoice->payments()->count() === 0) {
                PaymentTransaction::create([
                    'reference_type' => 'invoice',
                    'reference_id' => $invoice->id,
                    'payment_method_id' => $request->payment_method_id,
                    'created_by' => Auth::id(),
                    'amount' => max(0, $request->total_amount - ($request->discount ?? 0)),
                    'paid_at' => now(),
                ]);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diubah.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dihapus.');
    }

    public function getOrderDetail(ServiceOrder $order)
    {
        $order->load(['details', 'customer', 'vehicle']);
        return response()->json($order);
    }
}

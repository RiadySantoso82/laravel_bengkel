<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $data = Invoice::with(['order.customer', 'paymentMethod'])->latest()->get();
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

        Invoice::create($request->only(['order_id', 'total_amount', 'discount', 'payment_method_id', 'payment_status']));

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

        $invoice->update($request->only(['order_id', 'total_amount', 'discount', 'payment_method_id', 'payment_status']));

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

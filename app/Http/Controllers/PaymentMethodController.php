<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $data = PaymentMethod::latest()->get();
        return view('master.payment_method.index', compact('data'));
    }

    public function create()
    {
        return view('master.payment_method.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'is_active' => 'boolean']);
        PaymentMethod::create($request->only(['name', 'is_active']));
        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('master.payment_method.form', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate(['name' => 'required|max:255', 'is_active' => 'boolean']);
        $paymentMethod->update($request->only(['name', 'is_active']));
        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil diubah.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}

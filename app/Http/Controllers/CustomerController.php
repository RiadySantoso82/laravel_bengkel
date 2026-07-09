<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::latest()->get();
        return view('customer.index', compact('data'));
    }

    public function create()
    {
        return view('customer.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'phone' => 'nullable|max:50', 'email' => 'nullable|email|max:255', 'address' => 'nullable', 'is_walk_in' => 'boolean']);
        Customer::create($request->only(['name', 'phone', 'email', 'address', 'is_walk_in']));
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('customer.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate(['name' => 'required|max:255', 'phone' => 'nullable|max:50', 'email' => 'nullable|email|max:255', 'address' => 'nullable', 'is_walk_in' => 'boolean']);
        $customer->update($request->only(['name', 'phone', 'email', 'address', 'is_walk_in']));
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diubah.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function demo(Request $request)
    {
        $count = (int) $request->input('count', 5);
        $names = ['Budi Santoso', 'Siti Rahmawati', 'Ahmad Hidayat', 'Dewi Lestari', 'Rudi Hartono', 'Ani Kusuma', 'Doni Prasetyo', 'Rina Wulandari', 'Agus Wijaya', 'Maya Sari', 'Hendra Gunawan', 'Tuti Handayani', 'Eko Saputra', 'Nina Puspita', 'Adi Nugroho', 'Rita Indah', 'Irfan Hakim', 'Dian Permata', 'Yoga Pratama', 'Fani Marlina'];
        $cities = ['Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Medan', 'Makassar', 'Denpasar', 'Palembang', 'Malang'];

        $data = [['name' => 'Pelanggan Umum', 'phone' => null, 'email' => null, 'address' => null, 'is_walk_in' => true]];
        for ($i = 0; $i < min($count - 1, count($names)); $i++) {
            $data[] = [
                'name' => $names[$i],
                'phone' => '08' . rand(100000000, 999999999),
                'email' => strtolower(str_replace(' ', '', $names[$i])) . '@email.com',
                'address' => 'Jl. ' . $cities[$i % count($cities)] . ' No. ' . rand(1, 100) . ', ' . $cities[$i % count($cities)],
                'is_walk_in' => false,
            ];
        }

        Customer::insert($data);
        return redirect()->route('customers.index')->with('success', 'Berhasil menambahkan ' . count($data) . ' data pelanggan (1 Pelanggan Umum + ' . (count($data) - 1) . ' demo).');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $data = Supplier::latest()->get();
        return view('supplier.index', compact('data'));
    }

    public function create()
    {
        return view('supplier.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'phone' => 'nullable|max:50', 'address' => 'nullable', 'contact_person' => 'nullable|max:255']);
        Supplier::create($request->only(['name', 'phone', 'address', 'contact_person']));
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate(['name' => 'required|max:255', 'phone' => 'nullable|max:50', 'address' => 'nullable', 'contact_person' => 'nullable|max:255']);
        $supplier->update($request->only(['name', 'phone', 'address', 'contact_person']));
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diubah.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }

    public function demo(Request $request)
    {
        $count = $request->input('count', 5);
        $names = ['PT Maju Bersama', 'CV Sentosa Abadi', 'UD Jaya Makmur', 'PT Prima Sejahtera', 'CV Karya Mandiri', 'UD Berkah Jaya', 'PT Indah Jaya Abadi', 'CV Multi Karya', 'UD Sinar Terang', 'PT Bintang Utama', 'CV Sumber Rezeki', 'UD Maju Jaya', 'PT Karya Agung', 'CV Bumi Sejahtera', 'UD Cahaya Baru', 'PT Terang Jaya', 'CV Mandiri Utama', 'UD Sejahtera Abadi', 'PT Kencana Sakti', 'CV Permata Indah'];
        $contacts = ['Herman', 'Rina', 'Surya', 'Devi', 'Agung', 'Fitri', 'Bayu', 'Dewi', 'Adi', 'Rini'];

        $data = [];
        for ($i = 0; $i < min($count, count($names)); $i++) {
            $data[] = [
                'name' => $names[$i],
                'phone' => '021' . rand(1000000, 9999999),
                'address' => 'Jl. Industri No. ' . rand(1, 200) . ', Jakarta',
                'contact_person' => $contacts[$i % count($contacts)],
            ];
        }

        Supplier::insert($data);
        return redirect()->route('suppliers.index')->with('success', $count . ' data supplier demo berhasil ditambahkan.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartCategory;
use App\Models\Unit;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index()
    {
        $data = Sparepart::with(['category', 'unit'])->latest()->get();
        return view('sparepart.index', compact('data'));
    }

    public function create()
    {
        $categories = SparepartCategory::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('sparepart.form', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|max:50|unique:spareparts,code',
            'name' => 'required|max:255',
            'category_id' => 'nullable|exists:sparepart_categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'nullable|numeric|min:0',
            'stock_qty' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
        ]);
        Sparepart::create($request->only(['code', 'name', 'category_id', 'unit_id', 'buy_price', 'sell_price', 'stock_qty', 'min_stock']));
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil ditambahkan.');
    }

    public function edit(Sparepart $sparepart)
    {
        $categories = SparepartCategory::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('sparepart.form', compact('sparepart', 'categories', 'units'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'code' => 'required|max:50|unique:spareparts,code,' . $sparepart->id,
            'name' => 'required|max:255',
            'category_id' => 'nullable|exists:sparepart_categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price' => 'nullable|numeric|min:0',
            'stock_qty' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
        ]);
        $sparepart->update($request->only(['code', 'name', 'category_id', 'unit_id', 'buy_price', 'sell_price', 'stock_qty', 'min_stock']));
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil diubah.');
    }

    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil dihapus.');
    }
}

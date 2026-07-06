<?php

namespace App\Http\Controllers;

use App\Models\SparepartCategory;
use Illuminate\Http\Request;

class SparepartCategoryController extends Controller
{
    public function index()
    {
        $data = SparepartCategory::latest()->get();
        return view('master.sparepart_category.index', compact('data'));
    }

    public function create()
    {
        return view('master.sparepart_category.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'description' => 'nullable']);
        SparepartCategory::create($request->only(['name', 'description']));
        return redirect()->route('sparepart-categories.index')->with('success', 'Kategori sparepart berhasil ditambahkan.');
    }

    public function edit(SparepartCategory $sparepartCategory)
    {
        return view('master.sparepart_category.form', compact('sparepartCategory'));
    }

    public function update(Request $request, SparepartCategory $sparepartCategory)
    {
        $request->validate(['name' => 'required|max:255', 'description' => 'nullable']);
        $sparepartCategory->update($request->only(['name', 'description']));
        return redirect()->route('sparepart-categories.index')->with('success', 'Kategori sparepart berhasil diubah.');
    }

    public function destroy(SparepartCategory $sparepartCategory)
    {
        $sparepartCategory->delete();
        return redirect()->route('sparepart-categories.index')->with('success', 'Kategori sparepart berhasil dihapus.');
    }
}

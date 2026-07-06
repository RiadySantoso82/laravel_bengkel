<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $data = Unit::latest()->get();
        return view('master.unit.index', compact('data'));
    }

    public function create()
    {
        return view('master.unit.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'symbol' => 'nullable|max:50']);
        Unit::create($request->only(['name', 'symbol']));
        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('master.unit.form', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate(['name' => 'required|max:255', 'symbol' => 'nullable|max:50']);
        $unit->update($request->only(['name', 'symbol']));
        return redirect()->route('units.index')->with('success', 'Satuan berhasil diubah.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Mechanic;
use Illuminate\Http\Request;

class MechanicController extends Controller
{
    public function index()
    {
        $data = Mechanic::latest()->get();
        return view('mechanic.index', compact('data'));
    }

    public function create()
    {
        return view('mechanic.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'specialization' => 'nullable|max:255',
            'phone' => 'nullable|max:50',
            'status' => 'required|in:active,inactive',
        ]);
        Mechanic::create($request->only(['name', 'specialization', 'phone', 'status']));
        return redirect()->route('mechanics.index')->with('success', 'Mekanik berhasil ditambahkan.');
    }

    public function edit(Mechanic $mechanic)
    {
        return view('mechanic.form', compact('mechanic'));
    }

    public function update(Request $request, Mechanic $mechanic)
    {
        $request->validate([
            'name' => 'required|max:255',
            'specialization' => 'nullable|max:255',
            'phone' => 'nullable|max:50',
            'status' => 'required|in:active,inactive',
        ]);
        $mechanic->update($request->only(['name', 'specialization', 'phone', 'status']));
        return redirect()->route('mechanics.index')->with('success', 'Mekanik berhasil diubah.');
    }

    public function destroy(Mechanic $mechanic)
    {
        $mechanic->delete();
        return redirect()->route('mechanics.index')->with('success', 'Mekanik berhasil dihapus.');
    }
}

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

    public function demo(Request $request)
    {
        $count = (int) $request->input('count', 5);
        $mechanics = [
            ['Ahmad Syahputra', 'Mesin & Transmisi', '081234567890'],
            ['Bambang Wijaya', 'Kelistrikan', '081234567891'],
            ['Citra Dewi', 'Body Repair & Cat', '081234567892'],
            ['Deni Kurniawan', 'AC & Pendingin', '081234567893'],
            ['Eko Prasetyo', 'Mesin Diesel', '081234567894'],
            ['Farhan Maulana', 'Suspensi & Kemudi', '081234567895'],
            ['Gunawan Saputra', 'Rem & Kopling', '081234567896'],
            ['Hendra Lesmana', 'Mesin Bensin', '081234567897'],
            ['Indra Permana', 'Kelistrikan Body', '081234567898'],
            ['Joko Susilo', 'Motor Karburator', '081234567899'],
        ];

        $data = [];
        for ($i = 0; $i < min($count, count($mechanics)); $i++) {
            $data[] = [
                'name' => $mechanics[$i][0],
                'specialization' => $mechanics[$i][1],
                'phone' => $mechanics[$i][2],
                'status' => 'active',
            ];
        }

        Mechanic::insert($data);
        return redirect()->route('mechanics.index')->with('success', count($data) . ' data mekanik demo berhasil ditambahkan.');
    }
}

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

    public function demo(Request $request)
    {
        $count = (int) $request->input('count', 10);
        $cats = SparepartCategory::pluck('id')->toArray();
        $uids = Unit::pluck('id')->toArray();

        $items = [
            ['Oli Mesin 10W-40', 'OLI', 15000, 25000, 24],
            ['Oli Mesin 20W-50', 'OLI', 14000, 23000, 20],
            ['Oli Gardan 80W-90', 'OLI', 18000, 30000, 12],
            ['Oli Rem DOT 4', 'OLI', 12000, 20000, 15],
            ['Filter Oli', 'FLT', 8000, 15000, 30],
            ['Filter Udara', 'FLT', 10000, 18000, 25],
            ['Filter Bensin', 'FLT', 9000, 16000, 20],
            ['Kampas Rem Depan', 'REM', 12000, 22000, 15],
            ['Kampas Rem Belakang', 'REM', 11000, 20000, 15],
            ['Cakram Rem', 'REM', 25000, 45000, 8],
            ['Minyak Rem', 'OLI', 10000, 18000, 18],
            ['Ban Luar 90/80-17', 'BAN', 80000, 130000, 10],
            ['Ban Luar 80/90-17', 'BAN', 75000, 120000, 10],
            ['Ban Luar 70/90-16', 'BAN', 70000, 115000, 10],
            ['Ban Dalam 17 inch', 'BAN', 25000, 45000, 15],
            ['Ban Dalam 16 inch', 'BAN', 22000, 40000, 15],
            ['Velg Racing 17 inch', 'BAN', 150000, 250000, 4],
            ['Bohlam Lampu Depan', 'BDY', 8000, 15000, 20],
            ['Bohlam Lampu Sen', 'BDY', 5000, 10000, 20],
            ['Spion Kanan', 'BDY', 15000, 28000, 8],
            ['Spion Kiri', 'BDY', 15000, 28000, 8],
            ['Kabel Gas', 'BDY', 8000, 15000, 12],
            ['Kabel Kopling', 'BDY', 8000, 15000, 12],
            ['Aki Basah 12V 7Ah', 'AKI', 60000, 100000, 8],
            ['Aki Kering 12V 5Ah', 'AKI', 70000, 120000, 8],
            ['Busi Standar', 'BUS', 5000, 10000, 30],
            ['Busi Iridium', 'BUS', 15000, 30000, 15],
            ['Koil Pengapian', 'BUS', 25000, 45000, 6],
            ['CDI Unit', 'BUS', 40000, 75000, 5],
            ['Rantai Mesin', 'RAN', 30000, 55000, 8],
            ['Gear Depan', 'RAN', 10000, 20000, 10],
            ['Gear Belakang', 'RAN', 15000, 30000, 10],
            ['Oli Shock Depan', 'OLI', 12000, 22000, 14],
            ['Seal Shock Depan', 'BDY', 8000, 15000, 10],
            ['Laher Roda Depan', 'LHR', 10000, 20000, 10],
            ['Laher Roda Belakang', 'LHR', 12000, 22000, 10],
            ['Kampas Kopling', 'REM', 15000, 28000, 8],
            ['Paking Klep', 'REM', 5000, 10000, 15],
            ['Radiator Coolant', 'OLI', 15000, 28000, 10],
            ['Kipas Radiator', 'BDY', 35000, 60000, 5],
            ['Selang Radiator', 'BDY', 10000, 20000, 8],
            ['Kampas Rem Tromol', 'REM', 10000, 18000, 12],
            ['Seher 58mm', 'REM', 40000, 70000, 6],
            ['Ring Seher', 'REM', 15000, 28000, 8],
            ['Mur Roda', 'BDY', 2000, 5000, 40],
            ['Baut Spion', 'BDY', 1000, 3000, 40],
            ['Karet Handle Gas', 'BDY', 5000, 10000, 15],
            ['Handle Rem Kanan', 'REM', 8000, 15000, 10],
            ['Handle Rem Kiri', 'REM', 8000, 15000, 10],
            ['Dudukan Plat Nomor', 'BDY', 5000, 10000, 15],
        ];

        $catMap = ['OLI' => 'Oli & Pelumas', 'FLT' => 'Filter', 'BAN' => 'Ban & Velg', 'REM' => 'Rem', 'BDY' => 'Body & Aksesoris', 'AKI' => null, 'BUS' => null, 'RAN' => null, 'LHR' => null];

        $data = [];
        for ($i = 0; $i < min($count, count($items)); $i++) {
            $name = $items[$i][0];
            $prefix = $items[$i][1];
            $buy = $items[$i][2];
            $sell = $items[$i][3];
            $stock = $items[$i][4];

            $catId = null;
            $catName = $catMap[$prefix];
            if ($catName && !empty($cats)) {
                $c = SparepartCategory::where('name', 'like', "%$catName%")->first();
                if ($c) $catId = $c->id;
            }

            $data[] = [
                'code' => $prefix . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'name' => $name,
                'category_id' => $catId,
                'unit_id' => !empty($uids) ? $uids[array_rand($uids)] : null,
                'buy_price' => $buy,
                'sell_price' => $sell,
                'stock_qty' => $stock,
                'min_stock' => 5,
            ];
        }

        Sparepart::insert($data);
        return redirect()->route('spareparts.index')->with('success', count($data) . ' data sparepart demo berhasil ditambahkan.');
    }
}

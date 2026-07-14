<?php

namespace App\Http\Controllers;

use App\Models\CashCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') abort(403);
            return $next($request);
        });
    }

    public function index()
    {
        $data = CashCategory::latest()->get();
        return view('cash_category.index', compact('data'));
    }

    public function create()
    {
        return view('cash_category.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|in:in,out',
            'is_active' => 'boolean',
        ]);
        CashCategory::create($request->only(['name', 'type', 'is_active']));
        return redirect()->route('cash-categories.index')->with('success', 'Kategori kas berhasil ditambahkan.');
    }

    public function edit(CashCategory $cashCategory)
    {
        return view('cash_category.form', compact('cashCategory'));
    }

    public function update(Request $request, CashCategory $cashCategory)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|in:in,out',
            'is_active' => 'boolean',
        ]);
        $cashCategory->update($request->only(['name', 'type', 'is_active']));
        return redirect()->route('cash-categories.index')->with('success', 'Kategori kas berhasil diubah.');
    }

    public function destroy(CashCategory $cashCategory)
    {
        $cashCategory->delete();
        return redirect()->route('cash-categories.index')->with('success', 'Kategori kas berhasil dihapus.');
    }

    public function seedDefault()
    {
        if (CashCategory::count() > 0) {
            return redirect()->route('cash-categories.index')->with('error', 'Data kategori kas sudah ada.');
        }

        $defaults = [
            ['name' => 'Modal Tambahan', 'type' => 'in', 'is_active' => true],
            ['name' => 'Penjualan Retail', 'type' => 'in', 'is_active' => true],
            ['name' => 'Pendapatan Servis', 'type' => 'in', 'is_active' => true],
            ['name' => 'Listrik & Air', 'type' => 'out', 'is_active' => true],
            ['name' => 'Gaji Pegawai', 'type' => 'out', 'is_active' => true],
            ['name' => 'Sewa Tempat', 'type' => 'out', 'is_active' => true],
            ['name' => 'Pembelian Sparepart', 'type' => 'out', 'is_active' => true],
            ['name' => 'Operasional Lainnya', 'type' => 'out', 'is_active' => true],
        ];

        DB::table('cash_categories')->insert($defaults);
        return redirect()->route('cash-categories.index')->with('success', 'Data default kategori kas berhasil ditambahkan.');
    }
}

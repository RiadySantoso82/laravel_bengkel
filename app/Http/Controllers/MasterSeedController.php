<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterSeedController extends Controller
{
    public function seed($table)
    {
        $defaults = [
            'sparepart-categories' => [
                ['name' => 'Oli & Pelumas', 'description' => 'Oli mesin, oli gardan, pelumas'],
                ['name' => 'Filter', 'description' => 'Filter oli, filter udara, filter bensin'],
                ['name' => 'Ban & Velg', 'description' => 'Ban luar, ban dalam, velg'],
                ['name' => 'Rem', 'description' => 'Kampas rem, cakram, minyak rem'],
                ['name' => 'Body & Aksesoris', 'description' => 'Spion, lampu, bumper'],
            ],
            'units' => [
                ['name' => 'Pieces', 'symbol' => 'pcs'],
                ['name' => 'Liter', 'symbol' => 'L'],
                ['name' => 'Box', 'symbol' => 'box'],
                ['name' => 'Set', 'symbol' => 'set'],
                ['name' => 'Meter', 'symbol' => 'm'],
            ],
            'service-categories' => [
                ['name' => 'Servis Ringan', 'description' => 'Ganti oli, filter, tune up ringan'],
                ['name' => 'Servis Berat', 'description' => 'Overhaul mesin, turun mesin'],
                ['name' => 'Body Repair', 'description' => 'Cat, dempul, perbaikan bodi'],
                ['name' => 'AC Service', 'description' => 'Perbaikan dan perawatan AC'],
                ['name' => 'Kelistrikan', 'description' => 'Perbaikan sistem kelistrikan'],
            ],
            'payment-methods' => [
                ['name' => 'Tunai', 'is_active' => true],
                ['name' => 'Transfer Bank', 'is_active' => true],
                ['name' => 'QRIS', 'is_active' => true],
                ['name' => 'Kartu Debit', 'is_active' => true],
                ['name' => 'Kartu Kredit', 'is_active' => true],
            ],
        ];

        if (!isset($defaults[$table])) {
            return redirect()->back()->with('error', 'Tabel tidak dikenal.');
        }

        $mapped = [
            'sparepart-categories' => 'sparepart_categories',
            'units' => 'units',
            'service-categories' => 'service_categories',
            'payment-methods' => 'payment_methods',
        ];

        if (DB::table($mapped[$table])->count() === 0) {
            DB::table($mapped[$table])->insert($defaults[$table]);
        }

        return redirect()->back()->with('success', 'Data default berhasil ditambahkan.');
    }
}

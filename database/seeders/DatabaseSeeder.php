<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                [
                    'username' => 'admin',
                    'name' => 'Owner Admin',
                    'email' => 'admin@bengkel.test',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                ],
                [
                    'username' => 'kasir',
                    'name' => 'Front Office',
                    'email' => 'kasir@bengkel.test',
                    'password' => Hash::make('kasir123'),
                    'role' => 'kasir',
                ],
                [
                    'username' => 'mekanik',
                    'name' => 'Mekanik',
                    'email' => 'mekanik@bengkel.test',
                    'password' => Hash::make('mekanik123'),
                    'role' => 'mekanik',
                ],
            ]);
        }

        if (DB::table('customers')->count() === 0) {
            DB::table('customers')->insert([
                'name' => 'Pelanggan Umum',
                'phone' => null,
                'email' => null,
                'address' => null,
            ]);
        }

        if (DB::table('sparepart_categories')->count() === 0) {
            DB::table('sparepart_categories')->insert([
                ['name' => 'Oli & Pelumas', 'description' => 'Oli mesin, oli gardan, pelumas'],
                ['name' => 'Filter', 'description' => 'Filter oli, filter udara, filter bensin'],
                ['name' => 'Ban & Velg', 'description' => 'Ban luar, ban dalam, velg'],
                ['name' => 'Rem', 'description' => 'Kampas rem, cakram, minyak rem'],
                ['name' => 'Body & Aksesoris', 'description' => 'Spion, lampu, bumper'],
            ]);
        }

        if (DB::table('units')->count() === 0) {
            DB::table('units')->insert([
                ['name' => 'Pieces', 'symbol' => 'pcs'],
                ['name' => 'Liter', 'symbol' => 'L'],
                ['name' => 'Box', 'symbol' => 'box'],
                ['name' => 'Set', 'symbol' => 'set'],
                ['name' => 'Meter', 'symbol' => 'm'],
            ]);
        }

        if (DB::table('service_categories')->count() === 0) {
            DB::table('service_categories')->insert([
                ['name' => 'Servis Ringan', 'description' => 'Ganti oli, filter, tune up ringan'],
                ['name' => 'Servis Berat', 'description' => 'Overhaul mesin, turun mesin'],
                ['name' => 'Body Repair', 'description' => 'Cat, dempul, perbaikan bodi'],
                ['name' => 'AC Service', 'description' => 'Perbaikan dan perawatan AC'],
                ['name' => 'Kelistrikan', 'description' => 'Perbaikan sistem kelistrikan'],
            ]);
        }

        if (DB::table('payment_methods')->count() === 0) {
            DB::table('payment_methods')->insert([
                ['name' => 'Tunai', 'is_active' => true],
                ['name' => 'Transfer Bank', 'is_active' => true],
                ['name' => 'QRIS', 'is_active' => true],
                ['name' => 'Kartu Debit', 'is_active' => true],
                ['name' => 'Kartu Kredit', 'is_active' => true],
            ]);
        }
    }
}

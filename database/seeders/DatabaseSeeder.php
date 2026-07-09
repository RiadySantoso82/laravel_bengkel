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

        if (DB::table('service_types')->count() === 0) {
            DB::table('service_types')->insert([
                ['name' => 'Ganti Oli Mesin', 'base_price' => 25000, 'estimated_duration' => 30],
                ['name' => 'Ganti Filter Oli', 'base_price' => 10000, 'estimated_duration' => 15],
                ['name' => 'Tune Up Ringan', 'base_price' => 75000, 'estimated_duration' => 60],
                ['name' => 'Servis Rem Depan', 'base_price' => 50000, 'estimated_duration' => 45],
                ['name' => 'Servis Rem Belakang', 'base_price' => 45000, 'estimated_duration' => 45],
                ['name' => 'Ganti Ban Luar', 'base_price' => 30000, 'estimated_duration' => 30],
                ['name' => 'Ganti Kampas Kopling', 'base_price' => 80000, 'estimated_duration' => 90],
                ['name' => 'Overhaul Mesin', 'base_price' => 500000, 'estimated_duration' => 480],
                ['name' => 'Servis AC', 'base_price' => 100000, 'estimated_duration' => 120],
                ['name' => 'Ganti Aki', 'base_price' => 15000, 'estimated_duration' => 15],
            ]);
        }
    }
}

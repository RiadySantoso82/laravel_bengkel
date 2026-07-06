<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function index()
    {
        return view('setting.index');
    }

    public function reset(Request $request)
    {
        $request->validate(['confirm' => 'required|in:1']);

        $tables = [
            'purchase_order_details', 'purchase_orders', 'stock_movements',
            'service_order_details', 'service_orders', 'invoices',
            'vehicles', 'customers', 'suppliers', 'mechanics',
            'spareparts', 'sparepart_categories', 'units',
            'service_types', 'service_categories', 'payment_methods',
        ];

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            DB::statement("TRUNCATE TABLE `$table`");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Artisan::call('db:seed', ['--force' => true]);

        return redirect()->route('login');
    }
}

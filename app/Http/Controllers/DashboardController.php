<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $revenueMonths = [];
        $revenueTotals = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));

            $sales = SalesOrder::where('payment_status', 'paid')
                ->whereYear('created_at', date('Y', strtotime($month)))
                ->whereMonth('created_at', date('m', strtotime($month)))
                ->sum(DB::raw('total_amount - discount'));

            $invoices = Invoice::where('payment_status', 'paid')
                ->whereYear('created_at', date('Y', strtotime($month)))
                ->whereMonth('created_at', date('m', strtotime($month)))
                ->sum(DB::raw('total_amount - discount'));

            $revenueMonths[] = $monthName;
            $revenueTotals[] = $sales + $invoices;
        }

        $chartDays = [];
        $serviceCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartDays[] = date('d/m', strtotime($date));

            $count = ServiceOrder::whereIn('status', ['done', 'picked_up'])
                ->whereDate('actual_finish', $date)
                ->count();
            $serviceCounts[] = $count;
        }

        $totalSales = SalesOrder::where('payment_status', 'paid')->sum(DB::raw('total_amount - discount'));
        $totalService = Invoice::where('payment_status', 'paid')->sum(DB::raw('total_amount - discount'));

        return view('dashboard.index', compact(
            'revenueMonths', 'revenueTotals',
            'chartDays', 'serviceCounts',
            'totalSales', 'totalService'
        ));
    }
}

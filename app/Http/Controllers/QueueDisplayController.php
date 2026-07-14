<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class QueueDisplayController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');

        $inProgress = ServiceOrder::with(['customer', 'vehicle', 'mechanic'])
            ->whereDate('created_at', $today)
            ->whereIn('status', ['in_progress'])
            ->orderBy('created_at')
            ->get();

        $queued = ServiceOrder::with(['customer', 'vehicle'])
            ->whereDate('created_at', $today)
            ->whereIn('status', ['queued'])
            ->orderBy('created_at')
            ->get();

        $done = ServiceOrder::with(['customer', 'vehicle'])
            ->whereDate('created_at', $today)
            ->whereIn('status', ['done', 'picked_up'])
            ->orderBy('created_at')
            ->get();

        $waitingPart = ServiceOrder::with(['customer', 'vehicle'])
            ->whereDate('created_at', $today)
            ->where('status', 'waiting_part')
            ->orderBy('created_at')
            ->get();

        return view('queue.display', compact('inProgress', 'queued', 'done', 'waitingPart'));
    }
}

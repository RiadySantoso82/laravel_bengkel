<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\ServiceType;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MechanicDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'mekanik') {
                abort(403);
            }
            return $next($request);
        });
    }

    private function getMechanic()
    {
        return Auth::user()->mechanic;
    }

    private function mechanicId()
    {
        $m = $this->getMechanic();
        return $m ? $m->id : 0;
    }

    public function index()
    {
        $mid = $this->mechanicId();
        $activeCount = ServiceOrder::where('mechanic_id', $mid)->whereIn('status', ['queued', 'in_progress', 'waiting_part'])->count();
        $completedCount = ServiceOrder::where('mechanic_id', $mid)->whereIn('status', ['done', 'picked_up'])->count();
        return view('mechanic.dashboard', compact('activeCount', 'completedCount'));
    }

    public function myServices()
    {
        $data = ServiceOrder::with(['customer', 'vehicle'])->where('mechanic_id', $this->mechanicId())->latest()->get();
        return view('mechanic.services', compact('data'));
    }

    public function updateStatus(Request $request, ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->mechanic_id !== $this->mechanicId()) {
            abort(403);
        }
        $request->validate(['status' => 'required|in:queued,in_progress,waiting_part,done,picked_up']);
        $data = ['status' => $request->status];
        if (in_array($request->status, ['done', 'picked_up'])) {
            $data['actual_finish'] = now();
        }
        $serviceOrder->update($data);
        return redirect()->route('mechanic.services')->with('success', 'Status berhasil diupdate.');
    }

    public function detail(ServiceOrder $serviceOrder)
    {
        if ($serviceOrder->mechanic_id !== $this->mechanicId()) {
            abort(403);
        }
        $serviceOrder->load(['customer', 'vehicle', 'details']);
        $serviceTypes = ServiceType::pluck('name', 'id');
        $spareparts = Sparepart::pluck('name', 'id');
        $checklistItems = [
            'Cek kondisi oli mesin',
            'Cek tekanan & kondisi ban',
            'Cek kampas rem depan & belakang',
            'Cek aki & kelistrikan',
            'Cek rantai & gir set',
            'Cek lampu & sein',
            'Cek sistem pendingin',
            'Cek filter udara',
        ];
        return view('mechanic.detail', compact('serviceOrder', 'checklistItems', 'serviceTypes', 'spareparts'));
    }

    public function history()
    {
        $data = ServiceOrder::with(['customer', 'vehicle'])->where('mechanic_id', $this->mechanicId())->whereIn('status', ['done', 'picked_up'])->latest()->get();
        return view('mechanic.history', compact('data'));
    }

    public function profile()
    {
        $mechanic = $this->getMechanic();
        return view('mechanic.profile', compact('mechanic'));
    }

    public function updateProfile(Request $request)
    {
        $mechanic = $this->getMechanic();
        $request->validate(['phone' => 'nullable|max:50', 'specialization' => 'nullable|max:255']);
        if ($mechanic) {
            $mechanic->update($request->only(['phone', 'specialization']));
        }
        return redirect()->route('mechanic.profile')->with('success', 'Profil berhasil diupdate.');
    }
}

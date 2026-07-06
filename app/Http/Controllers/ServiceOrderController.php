<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\ServiceOrderDetail;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Mechanic;
use App\Models\ServiceType;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceOrderController extends Controller
{
    public function index()
    {
        $data = ServiceOrder::with(['customer', 'vehicle', 'mechanic'])->latest()->get();
        return view('service_order.index', compact('data'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $mechanics = Mechanic::where('status', 'active')->orderBy('name')->get();
        $serviceTypes = ServiceType::orderBy('name')->get();
        $spareparts = Sparepart::orderBy('name')->get();
        return view('service_order.form', compact('customers', 'mechanics', 'serviceTypes', 'spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'mechanic_id' => 'nullable|exists:mechanics,id',
            'complaint' => 'nullable',
            'estimated_finish' => 'nullable|date',
            'vehicle_plate_manual' => 'nullable|max:20',
            'vehicle_info_manual' => 'nullable|max:255',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:jasa,part',
            'items.*.item_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $order = ServiceOrder::create([
            'vehicle_id' => $request->vehicle_id,
            'customer_id' => $request->customer_id,
            'mechanic_id' => $request->mechanic_id,
            'user_id' => Auth::id(),
            'complaint' => $request->complaint,
            'status' => 'queued',
            'estimated_finish' => $request->estimated_finish,
            'vehicle_plate_manual' => $request->vehicle_plate_manual,
            'vehicle_info_manual' => $request->vehicle_info_manual,
        ]);

        foreach ($request->items as $item) {
            ServiceOrderDetail::create([
                'order_id' => $order->id,
                'type' => $item['type'],
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
            ]);
        }

        return redirect()->route('service-orders.index')->with('success', 'Service order berhasil dibuat.');
    }

    public function edit(ServiceOrder $serviceOrder)
    {
        $customers = Customer::orderBy('name')->get();
        $vehicles = Vehicle::where('customer_id', $serviceOrder->customer_id)->orderBy('plate_number')->get();
        $mechanics = Mechanic::where('status', 'active')->orderBy('name')->get();
        $serviceTypes = ServiceType::orderBy('name')->get();
        $spareparts = Sparepart::orderBy('name')->get();
        $serviceOrder->load('details');
        return view('service_order.form', compact('serviceOrder', 'customers', 'vehicles', 'mechanics', 'serviceTypes', 'spareparts'));
    }

    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'mechanic_id' => 'nullable|exists:mechanics,id',
            'complaint' => 'nullable',
            'status' => 'required|in:queued,in_progress,waiting_part,done,picked_up',
            'estimated_finish' => 'nullable|date',
            'actual_finish' => 'nullable|date',
            'vehicle_plate_manual' => 'nullable|max:20',
            'vehicle_info_manual' => 'nullable|max:255',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:jasa,part',
            'items.*.item_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $serviceOrder->update([
            'vehicle_id' => $request->vehicle_id,
            'customer_id' => $request->customer_id,
            'mechanic_id' => $request->mechanic_id,
            'complaint' => $request->complaint,
            'status' => $request->status,
            'estimated_finish' => $request->estimated_finish,
            'actual_finish' => $request->actual_finish,
            'vehicle_plate_manual' => $request->vehicle_plate_manual,
            'vehicle_info_manual' => $request->vehicle_info_manual,
        ]);

        $serviceOrder->details()->delete();
        foreach ($request->items as $item) {
            ServiceOrderDetail::create([
                'order_id' => $serviceOrder->id,
                'type' => $item['type'],
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
            ]);
        }

        return redirect()->route('service-orders.index')->with('success', 'Service order berhasil diubah.');
    }

    public function destroy(ServiceOrder $serviceOrder)
    {
        $serviceOrder->delete();
        return redirect()->route('service-orders.index')->with('success', 'Service order berhasil dihapus.');
    }

    public function getVehicles(Request $request)
    {
        $vehicles = Vehicle::where('customer_id', $request->customer_id)->orderBy('plate_number')->get();
        return response()->json($vehicles);
    }
}

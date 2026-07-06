<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $data = Vehicle::with('customer')->latest()->get();
        return view('vehicle.index', compact('data'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('vehicle.form', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plate_number' => 'required|max:20',
            'brand' => 'required|max:100',
            'model' => 'required|max:100',
            'year' => 'nullable|digits:4',
            'chassis_number' => 'nullable|max:100',
            'engine_number' => 'nullable|max:100',
        ]);
        Vehicle::create($request->only(['customer_id', 'plate_number', 'brand', 'model', 'year', 'chassis_number', 'engine_number']));
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::orderBy('name')->get();
        return view('vehicle.form', compact('vehicle', 'customers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plate_number' => 'required|max:20',
            'brand' => 'required|max:100',
            'model' => 'required|max:100',
            'year' => 'nullable|digits:4',
            'chassis_number' => 'nullable|max:100',
            'engine_number' => 'nullable|max:100',
        ]);
        $vehicle->update($request->only(['customer_id', 'plate_number', 'brand', 'model', 'year', 'chassis_number', 'engine_number']));
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil diubah.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}

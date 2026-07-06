<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $data = ServiceCategory::latest()->get();
        return view('master.service_category.index', compact('data'));
    }

    public function create()
    {
        return view('master.service_category.form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:255', 'description' => 'nullable']);
        ServiceCategory::create($request->only(['name', 'description']));
        return redirect()->route('service-categories.index')->with('success', 'Kategori servis berhasil ditambahkan.');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        return view('master.service_category.form', compact('serviceCategory'));
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $request->validate(['name' => 'required|max:255', 'description' => 'nullable']);
        $serviceCategory->update($request->only(['name', 'description']));
        return redirect()->route('service-categories.index')->with('success', 'Kategori servis berhasil diubah.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->route('service-categories.index')->with('success', 'Kategori servis berhasil dihapus.');
    }
}

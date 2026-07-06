@extends('layouts.app')

@section('title', isset($vehicle) ? 'Edit Kendaraan' : 'Tambah Kendaraan')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding: 30px; max-width: 600px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.3s; }
    .form-control:focus { border-color: #0f3460; }
    select.form-control { background: #fff; }
    .row { display: flex; gap: 16px; }
    .row .form-group { flex: 1; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .btn-secondary:hover { background: #cbd5e1; }
    .form-actions { display: flex; gap: 10px; margin-top: 24px; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-truck"></i> {{ isset($vehicle) ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($vehicle) ? route('vehicles.update', $vehicle) : route('vehicles.store') }}">
                @csrf @if (isset($vehicle)) @method('PUT') @endif
                <div class="form-group"><label for="customer_id">Pelanggan</label>
                    <select id="customer_id" name="customer_id" class="form-control" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $vehicle->customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label for="plate_number">Plat Nomor</label><input type="text" id="plate_number" name="plate_number" class="form-control" value="{{ old('plate_number', $vehicle->plate_number ?? '') }}" required></div>
                <div class="row">
                    <div class="form-group"><label for="brand">Merk</label><input type="text" id="brand" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand ?? '') }}" required></div>
                    <div class="form-group"><label for="model">Model</label><input type="text" id="model" name="model" class="form-control" value="{{ old('model', $vehicle->model ?? '') }}" required></div>
                </div>
                <div class="row">
                    <div class="form-group"><label for="year">Tahun</label><input type="text" id="year" name="year" class="form-control" value="{{ old('year', $vehicle->year ?? '') }}" maxlength="4"></div>
                    <div class="form-group"><label for="chassis_number">No. Rangka</label><input type="text" id="chassis_number" name="chassis_number" class="form-control" value="{{ old('chassis_number', $vehicle->chassis_number ?? '') }}"></div>
                </div>
                <div class="form-group"><label for="engine_number">No. Mesin</label><input type="text" id="engine_number" name="engine_number" class="form-control" value="{{ old('engine_number', $vehicle->engine_number ?? '') }}"></div>
                <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="{{ route('vehicles.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
            </form>
        </div></div>
    </main>
</div>
@endsection

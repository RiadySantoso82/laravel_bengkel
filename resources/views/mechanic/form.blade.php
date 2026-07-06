@extends('layouts.app')

@section('title', isset($mechanic) ? 'Edit Mekanik' : 'Tambah Mekanik')

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
        <div class="page-header"><h1><i class="fas fa-user-cog"></i> {{ isset($mechanic) ? 'Edit Mekanik' : 'Tambah Mekanik' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($mechanic) ? route('mechanics.update', $mechanic) : route('mechanics.store') }}">
                @csrf @if (isset($mechanic)) @method('PUT') @endif
                <div class="form-group"><label for="name">Nama Mekanik</label><input type="text" id="name" name="name" class="form-control" value="{{ old('name', $mechanic->name ?? '') }}" required></div>
                <div class="form-group"><label for="specialization">Spesialisasi</label><input type="text" id="specialization" name="specialization" class="form-control" value="{{ old('specialization', $mechanic->specialization ?? '') }}" placeholder="Contoh: Mesin, Kelistrikan, Body Repair"></div>
                <div class="form-group"><label for="phone">Telepon</label><input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $mechanic->phone ?? '') }}"></div>
                <div class="form-group"><label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" {{ old('status', $mechanic->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $mechanic->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="{{ route('mechanics.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
            </form>
        </div></div>
    </main>
</div>
@endsection

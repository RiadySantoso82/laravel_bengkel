@extends('layouts.app')

@section('title', isset($cashCategory) ? 'Edit Kategori Kas' : 'Tambah Kategori Kas')

@push('styles')
<style>
    .page-header h1 { font-size:24px;color:#1a1a2e;margin-bottom:24px; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding:30px;max-width:600px; }
    .form-group { margin-bottom:20px; }
    .form-group label { display:block;margin-bottom:6px;font-weight:600;color:#333;font-size:14px; }
    .form-control { width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-size:14px;outline:none; }
    .form-control:focus { border-color:#0f3460; }
    select.form-control { background:#fff; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-secondary { background:#e2e8f0;color:#475569; }
    .btn-secondary:hover { background:#cbd5e1; }
    .form-actions { display:flex;gap:10px;margin-top:24px; }
    .form-check { display:flex;align-items:center;gap:8px; }
    .form-check input { width:18px;height:18px; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-money-bill-wave"></i> {{ isset($cashCategory) ? 'Edit Kategori Kas' : 'Tambah Kategori Kas' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($cashCategory) ? route('cash-categories.update', $cashCategory) : route('cash-categories.store') }}">
                @csrf @if (isset($cashCategory)) @method('PUT') @endif
                <div class="form-group">
                    <label for="name">Nama Kategori</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $cashCategory->name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label for="type">Tipe</label>
                    <select id="type" name="type" class="form-control" required>
                        <option value="in" {{ old('type', $cashCategory->type ?? '') === 'in' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="out" {{ old('type', $cashCategory->type ?? '') === 'out' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $cashCategory->is_active ?? true) ? 'checked' : '' }}>
                        <span>Aktif</span>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('cash-categories.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div></div>
    </main>
</div>
@endsection

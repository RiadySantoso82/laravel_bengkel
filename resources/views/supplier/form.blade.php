@extends('layouts.app')

@section('title', isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding: 30px; max-width: 600px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.3s; }
    .form-control:focus { border-color: #0f3460; }
    textarea.form-control { resize: vertical; min-height: 80px; }
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
        <div class="page-header"><h1><i class="fas fa-truck-loading"></i> {{ isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($supplier) ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
                @csrf @if (isset($supplier)) @method('PUT') @endif
                <div class="form-group"><label for="name">Nama Supplier</label><input type="text" id="name" name="name" class="form-control" value="{{ old('name', $supplier->name ?? '') }}" required></div>
                <div class="form-group"><label for="phone">Telepon</label><input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}"></div>
                <div class="form-group"><label for="contact_person">Kontak Person</label><input type="text" id="contact_person" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person ?? '') }}"></div>
                <div class="form-group"><label for="address">Alamat</label><textarea id="address" name="address" class="form-control">{{ old('address', $supplier->address ?? '') }}</textarea></div>
                <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="{{ route('suppliers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
            </form>
        </div></div>
    </main>
</div>
@endsection

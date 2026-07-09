@extends('layouts.app')

@section('title', isset($sparepart) ? 'Edit Sparepart' : 'Tambah Sparepart')

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
        <div class="page-header"><h1><i class="fas fa-cogs"></i> {{ isset($sparepart) ? 'Edit Sparepart' : 'Tambah Sparepart' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($sparepart) ? route('spareparts.update', $sparepart) : route('spareparts.store') }}">
                @csrf @if (isset($sparepart)) @method('PUT') @endif
                <div class="row">
                    <div class="form-group"><label for="code">Kode</label><input type="text" id="code" name="code" class="form-control" value="{{ old('code', $sparepart->code ?? '') }}" required></div>
                    <div class="form-group"><label for="name">Nama</label><input type="text" id="name" name="name" class="form-control" value="{{ old('name', $sparepart->name ?? '') }}" required></div>
                </div>
                <div class="row">
                    <div class="form-group"><label for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id', $sparepart->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label for="unit_id">Satuan</label>
                        <select id="unit_id" name="unit_id" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id', $sparepart->unit_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->symbol ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group"><label for="buy_price">Harga Beli</label><input type="number" id="buy_price" name="buy_price" class="form-control" value="{{ old('buy_price', $sparepart->buy_price ?? 0) }}" min="0" step="100"></div>
                    <div class="form-group"><label for="sell_price">Harga Jual</label><input type="number" id="sell_price" name="sell_price" class="form-control" value="{{ old('sell_price', $sparepart->sell_price ?? 0) }}" min="0" step="100"></div>
                </div>
                <div class="row">
                    <div class="form-group"><label for="min_stock">Min. Stok</label><input type="number" id="min_stock" name="min_stock" class="form-control" value="{{ old('min_stock', $sparepart->min_stock ?? 0) }}" min="0"></div>
                </div>
                <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="{{ route('spareparts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
            </form>
        </div></div>
    </main>
</div>
@endsection

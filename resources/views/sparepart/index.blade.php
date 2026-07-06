@extends('layouts.app')

@section('title', 'Sparepart')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-warning { background: #d97706; color: #fff; }
    .btn-warning:hover { background: #b45309; }
    .btn-danger { background: #dc2626; color: #fff; }
    .btn-danger:hover { background: #b91c1c; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; }
    tr:hover td { background: #f8fafc; }
    .actions { display: flex; gap: 6px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #e2e8f0; color: #475569; }
    .badge-low { background: #fde8e8; color: #991b1b; }
    .text-right { text-align: right; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-cogs"></i> Sparepart</h1>
            <a href="{{ route('spareparts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                @if ($data->isEmpty())
                    <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                        <i class="fas fa-cogs" style="font-size:48px;margin-bottom:16px;"></i>
                        <h3 style="color:#475569;margin-bottom:8px;">Belum ada data sparepart</h3>
                        <p style="margin-bottom:24px;font-size:14px;">Isi dengan data demo atau input manual.</p>
                        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-bottom:20px;">
                            <form method="POST" action="{{ route('spareparts.demo') }}" style="display:inline;">
                                @csrf <input type="hidden" name="count" value="10">
                                <button class="btn btn-success" style="background:#059669;color:#fff;"><i class="fas fa-database"></i> Demo 10</button>
                            </form>
                            <form method="POST" action="{{ route('spareparts.demo') }}" style="display:inline;">
                                @csrf <input type="hidden" name="count" value="20">
                                <button class="btn btn-success" style="background:#059669;color:#fff;"><i class="fas fa-database"></i> Demo 20</button>
                            </form>
                            <form method="POST" action="{{ route('spareparts.demo') }}" style="display:inline;">
                                @csrf <input type="hidden" name="count" value="50">
                                <button class="btn btn-success" style="background:#059669;color:#fff;"><i class="fas fa-database"></i> Demo 50</button>
                            </form>
                        </div>
                        <a href="{{ route('spareparts.create') }}" class="btn btn-primary" style="background:#0f3460;color:#fff;"><i class="fas fa-plus"></i> Input Manual</a>
                    </div>
                @else
                    <table>
                        <thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>Kategori</th><th>Satuan</th><th style="text-align:right;">Harga Jual</th><th style="text-align:right;">Stok</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($data as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge">{{ $item->code }}</span></td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->category->name ?? '-' }}</td>
                                <td>{{ $item->unit->symbol ?? $item->unit->name ?? '-' }}</td>
                                <td class="text-right">{{ number_format($item->sell_price, 0) }}</td>
                                <td class="text-right"><span class="badge {{ $item->stock_qty <= $item->min_stock ? 'badge-low' : '' }}">{{ $item->stock_qty }}</span></td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('spareparts.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('spareparts.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection

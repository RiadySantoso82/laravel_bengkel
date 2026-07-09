@extends('layouts.app')

@section('title', 'Laporan Sisa Stok')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; font-size: 12px; }
    tr:hover td { background: #f8fafc; }
    .text-right { text-align: right; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .badge-normal { background: #d1fae5; color: #065f46; }
    .badge-low { background: #fef3c7; color: #92400e; }
    .badge-empty { background: #fde8e8; color: #991b1b; }
    .summary { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
    .summary-item { background: #fff; border-radius: 10px; padding: 16px 20px; flex: 1; min-width: 150px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .summary-item .num { font-size: 24px; font-weight: 700; color: #1a1a2e; }
    .summary-item .label { font-size: 12px; color: #888; margin-top: 4px; }
    .progress-bar { height: 6px; border-radius: 3px; background: #e2e8f0; margin-top: 8px; overflow: hidden; }
    .progress-bar .fill { height: 100%; border-radius: 3px; transition: width 0.3s; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header" style="margin-bottom:20px;">
            <h1><i class="fas fa-boxes"></i> Laporan Sisa Stok</h1>
        </div>

        @php
            $totalItems = $data->count();
            $totalStock = $data->sum('stock_qty');
            $lowStock = $data->filter(fn($s) => $s->stock_qty <= $s->min_stock && $s->stock_qty > 0)->count();
            $emptyStock = $data->filter(fn($s) => $s->stock_qty <= 0)->count();
        @endphp

        <div class="summary">
            <div class="summary-item">
                <div class="num">{{ $totalItems }}</div>
                <div class="label">Total Part</div>
            </div>
            <div class="summary-item">
                <div class="num">{{ $totalStock }}</div>
                <div class="label">Total Stok (dari batch)</div>
            </div>
            <div class="summary-item">
                <div class="num">{{ $lowStock }}</div>
                <div class="label">Stok Menipis</div>
            </div>
            <div class="summary-item">
                <div class="num">{{ $emptyStock }}</div>
                <div class="label">Stok Habis</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table>
                    <thead><tr><th>Kode</th><th>Nama Part</th><th>Kategori</th><th>Satuan</th><th class="text-right">Batch Aktif</th><th class="text-right">Stok (batch)</th><th class="text-right">Min</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($data as $s)
                        @php
                            $stock = $s->stock_qty;
                            $batchCount = $s->batches()->where('qty_remaining', '>', 0)->count();
                            $status = $stock <= 0 ? 'empty' : ($stock <= $s->min_stock ? 'low' : 'normal');
                            $statusText = $stock <= 0 ? 'Habis' : ($stock <= $s->min_stock ? 'Menipis' : 'Normal');
                            $pct = $s->min_stock > 0 ? min(100, ($stock / $s->min_stock) * 100) : 100;
                        @endphp
                        <tr>
                            <td><span style="background:#e2e8f0;padding:2px 6px;border-radius:4px;font-size:11px;">{{ $s->code }}</span></td>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->category->name ?? '-' }}</td>
                            <td>{{ $s->unit->symbol ?? $s->unit->name ?? '-' }}</td>
                            <td class="text-right">{{ $batchCount }}</td>
                            <td class="text-right"><strong>{{ $stock }}</strong></td>
                            <td class="text-right">{{ $s->min_stock }}</td>
                            <td>
                                <span class="badge badge-{{ $status }}">{{ $statusText }}</span>
                                @if ($s->min_stock > 0)
                                <div class="progress-bar"><div class="fill" style="width:{{ $pct }}%;background:{{ $status === 'normal' ? '#059669' : ($status === 'low' ? '#d97706' : '#dc2626') }};"></div></div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada data sparepart</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection

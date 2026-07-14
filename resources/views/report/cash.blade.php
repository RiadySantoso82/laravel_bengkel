@extends('layouts.app')

@section('title', 'Laporan Kas')

@push('styles')
<style>
    .page-header h1 { font-size:24px; color:#1a1a2e; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:16px; }
    .card-body { padding:20px; }
    .card-body.p-0 { padding:0;overflow-x:auto; }
    table { width:100%;border-collapse:collapse; }
    th,td { text-align:left;padding:10px 14px;border-bottom:1px solid #f0f0f0;font-size:13px; }
    th { background:#f8fafc;font-weight:600;color:#475569;font-size:12px; }
    tr:hover td { background:#f8fafc; }
    .badge { display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-in { background:#d1fae5;color:#065f46; }
    .badge-out { background:#fde8e8;color:#991b1b; }
    .filter-bar { display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px;padding:16px 20px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .filter-bar label { display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px; }
    .filter-bar select,.filter-bar input { padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none; }
    .summary-item .num-small { font-size:14px;font-weight:600; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;text-decoration:none; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-secondary { background:#e2e8f0;color:#475569; }
    .summary { display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap; }
    .summary-item { background:#fff;border-radius:10px;padding:16px 20px;flex:1;min-width:140px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .summary-item .num { font-size:22px;font-weight:700; }
    .summary-item .label { font-size:12px;color:#888;margin-top:4px; }
    .text-right { text-align:right; }
    .pagination { display:flex;gap:4px;justify-content:center;margin-top:16px; }
    .pagination a,.pagination span { padding:6px 12px;border-radius:6px;font-size:13px;text-decoration:none;color:#475569;background:#fff;border:1px solid #e2e8f0; }
    .pagination .active { background:#0f3460;color:#fff;border-color:#0f3460; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header" style="margin-bottom:20px;">
            <h1><i class="fas fa-money-check-alt"></i> Laporan Kas</h1>
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="num" style="color:#059669;">Rp {{ number_format($totalIn, 0) }}</div>
                <div class="label">Total Pemasukan</div>
            </div>
            <div class="summary-item">
                <div class="num" style="color:#dc2626;">Rp {{ number_format($totalOut, 0) }}</div>
                <div class="label">Total Pengeluaran</div>
            </div>
            <div class="summary-item">
                <div class="num" style="color:{{ $balance >= 0 ? '#059669' : '#dc2626' }};">Rp {{ number_format($balance, 0) }}</div>
                <div class="label">Saldo Akhir</div>
            </div>
            <div class="summary-item" style="background:#f0fdf4;">
                <div class="num" style="color:#0f3460;">{{ number_format($salesTotal, 0) }}</div>
                <div class="label">Penjualan Retail ({{ $salesCount }}x)</div>
            </div>
            <div class="summary-item" style="background:#f0fdf4;">
                <div class="num" style="color:#0f3460;">{{ number_format($invoiceTotal, 0) }}</div>
                <div class="label">Pendapatan Servis ({{ $invoiceCount }}x)</div>
            </div>
            <div class="summary-item" style="background:#f8fafc;">
                <div class="num" style="color:#64748b;">Rp {{ number_format($manualIn, 0) }} / Rp {{ number_format($manualOut, 0) }}</div>
                <div class="section-label">Manual Masuk/Keluar</div>
            </div>
        </div>

        <form class="filter-bar" method="GET">
            <div><label>Dari</label><input type="date" name="date_from" value="{{ $from }}"></div>
            <div><label>Sampai</label><input type="date" name="date_to" value="{{ $to }}"></div>
            <div><label>Tipe</label>
                <select name="type">
                    <option value="">Semua</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div><label>Kategori</label>
                <select name="cash_category_id">
                    <option value="">Semua</option>
                    @foreach ($categories as $c)
                    <option value="{{ $c->id }}" {{ request('cash_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('reports.cash') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>

        <div class="card">
            <div class="card-body p-0">
                <table>
                    <thead><tr><th>Tanggal</th><th>Sumber</th><th>Keterangan</th><th class="text-right">Masuk</th><th class="text-right">Keluar</th></tr></thead>
                    <tbody>
                        @forelse ($all as $t)
                        <tr>
                            <td style="white-space:nowrap;">{{ $t->date }}</td>
                            <td>{{ $t->source }}</td>
                            <td>{{ Str::limit($t->description, 60) }}</td>
                            <td class="text-right" style="color:#059669;">{{ $t->amount_in > 0 ? number_format($t->amount_in, 0) : '-' }}</td>
                            <td class="text-right" style="color:#dc2626;">{{ $t->amount_out > 0 ? number_format($t->amount_out, 0) : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection

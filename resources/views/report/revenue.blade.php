@extends('layouts.app')

@section('title', 'Laporan Pendapatan')

@push('styles')
<style>
    .page-header h1 { font-size:24px; color:#1a1a2e; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:16px; }
    .card-body { padding:20px; }
    .summary { display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
    .summary-item { background:#fff; border-radius:12px; padding:20px 24px; flex:1; min-width:180px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .summary-item .icon { font-size:24px; margin-bottom:8px; }
    .summary-item .num { font-size:26px; font-weight:700; color:#1a1a2e; }
    .summary-item .label { font-size:12px; color:#888; margin-top:4px; }
    .filter-bar { display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px;padding:16px 20px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .filter-bar label { display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px; }
    .filter-bar input { padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;text-decoration:none; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-primary:hover { background:#1a1a2e; }
    .btn-secondary { background:#e2e8f0;color:#475569; }
    table { width:100%;border-collapse:collapse; }
    th,td { text-align:left;padding:10px 14px;border-bottom:1px solid #f0f0f0;font-size:13px; }
    th { background:#f8fafc;font-weight:600;color:#475569;font-size:12px; }
    tr:hover td { background:#f8fafc;cursor:pointer; }
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
            <h1><i class="fas fa-chart-line"></i> Laporan Pendapatan</h1>
        </div>

        <form class="filter-bar" method="GET">
            <div><label>Dari</label><input type="date" name="date_from" value="{{ $from }}"></div>
            <div><label>Sampai</label><input type="date" name="date_to" value="{{ $to }}"></div>
            <div>
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('reports.revenue') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>

        <div class="summary">
            <div class="summary-item">
                <div class="icon" style="color:#059669;"><i class="fas fa-shopping-cart"></i></div>
                <div class="num">Rp {{ number_format($salesTotal, 0) }}</div>
                <div class="label">Penjualan Retail ({{ $salesCount }} transaksi)</div>
            </div>
            <div class="summary-item">
                <div class="icon" style="color:#0f3460;"><i class="fas fa-wrench"></i></div>
                <div class="num">Rp {{ number_format($invoiceTotal, 0) }}</div>
                <div class="label">Servis ({{ $invoiceCount }} invoice)</div>
            </div>
            <div class="summary-item">
                <div class="icon" style="color:#d97706;"><i class="fas fa-money-bill-wave"></i></div>
                <div class="num">Rp {{ number_format($salesTotal + $invoiceTotal, 0) }}</div>
                <div class="label">Total Pendapatan</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table>
                    <thead><tr><th>Tanggal</th><th>#</th><th>Tipe</th><th>Pelanggan</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                        @forelse ($items as $item)
                        <tr onclick="window.location='{{ $item->route }}'" style="cursor:pointer;">
                            <td style="white-space:nowrap;">{{ date('d/m/Y H:i', strtotime($item->date)) }}</td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->customer }}</td>
                            <td class="text-right">{{ number_format($item->total, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada data pendapatan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($total > $perPage)
        <div class="pagination">
            @for ($i = 1; $i <= ceil($total / $perPage); $i++)
                @if ($i == $page)
                <span class="active">{{ $i }}</span>
                @else
                <a href="{{ route('reports.revenue', array_merge(request()->all(), ['page' => $i])) }}">{{ $i }}</a>
                @endif
            @endfor
        </div>
        @endif
    </main>
</div>
@endsection

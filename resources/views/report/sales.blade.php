@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 16px; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; font-size: 12px; }
    tr:hover td { background: #f8fafc; cursor: pointer; }
    .text-right { text-align: right; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .badge-paid { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px; padding: 16px 20px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .filter-bar label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .filter-bar select, .filter-bar input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; outline: none; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; text-decoration: none; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .summary { display: flex; gap: 16px; margin-bottom: 16px; }
    .summary-item { background: #fff; border-radius: 10px; padding: 16px 20px; flex: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .summary-item .num { font-size: 22px; font-weight: 700; color: #1a1a2e; }
    .summary-item .label { font-size: 12px; color: #888; margin-top: 4px; }
    .pagination { display: flex; gap: 4px; justify-content: center; margin-top: 16px; }
    .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; color: #475569; background: #fff; border: 1px solid #e2e8f0; }
    .pagination .active { background: #0f3460; color: #fff; border-color: #0f3460; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header" style="margin-bottom:20px;">
            <h1><i class="fas fa-shopping-cart"></i> Laporan Penjualan</h1>
        </div>

        <form class="filter-bar" method="GET">
            <div>
                <label>Dari</label>
                <input type="date" name="date_from" value="{{ $from ?? date('Y-m-d', strtotime('-30 days')) }}">
            </div>
            <div>
                <label>Sampai</label>
                <input type="date" name="date_to" value="{{ $to ?? date('Y-m-d') }}">
            </div>
            <div>
                <label>Status</label>
                <select name="payment_status">
                    <option value="">Semua</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>

        <div class="summary">
            <div class="summary-item">
                <div class="num">{{ $grandCount }}</div>
                <div class="label">Total Transaksi</div>
            </div>
            <div class="summary-item">
                <div class="num">Rp {{ number_format($grandTotal, 0) }}</div>
                <div class="label">Total Omzet</div>
            </div>
            <div class="summary-item">
                <div class="num">Rp {{ number_format($data->where('payment_status', 'paid')->sum(fn($o) => $o->total_amount - $o->discount), 0) }}</div>
                <div class="label">Omzet Lunas</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table>
                    <thead><tr><th>Tanggal</th><th>#</th><th>Pelanggan</th><th>Item</th><th class="text-right">Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($data as $o)
                        <tr onclick="window.location='{{ route('sales-orders.show', $o) . '?from=report' }}'">
                            <td style="white-space:nowrap;">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                            <td>SO{{ str_pad($o->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $o->customer->name ?? 'Walk-in' }}</td>
                            <td>{{ $o->details->sum('qty') }} pcs</td>
                            <td class="text-right">{{ number_format($o->total_amount - $o->discount, 0) }}</td>
                            <td><span class="badge badge-{{ $o->payment_status }}">{{ $o->payment_status === 'paid' ? 'Lunas' : 'Pending' }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada data penjualan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($data->hasPages())
        <div class="pagination">{{ $data->links() }}</div>
        @endif
    </main>
</div>
@endsection

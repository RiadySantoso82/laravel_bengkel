@extends('layouts.app')

@section('title', 'Laporan Stock Movement')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; font-size: 12px; }
    tr:hover td { background: #f8fafc; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .badge-in { background: #d1fae5; color: #065f46; }
    .badge-out { background: #fde8e8; color: #991b1b; }
    .filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px; }
    .filter-bar label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .filter-bar select, .filter-bar input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; outline: none; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; text-decoration: none; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .pagination { display: flex; gap: 4px; justify-content: center; margin-top: 16px; }
    .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; color: #475569; background: #fff; border: 1px solid #e2e8f0; }
    .pagination .active { background: #0f3460; color: #fff; border-color: #0f3460; }
    .source-label { font-size: 11px; padding: 2px 6px; border-radius: 4px; background: #e2e8f0; color: #475569; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header" style="margin-bottom:20px;">
            <h1><i class="fas fa-exchange-alt"></i> Laporan Stock Movement</h1>
        </div>

        <div class="card" style="margin-bottom:20px;">
            <div class="card-body">
                <form class="filter-bar" method="GET">
                    <div class="form-group">
                        <label>Part</label>
                        <select name="part_id">
                            <option value="">Semua Part</option>
                            @foreach ($spareparts as $id => $name)
                            <option value="{{ $id }}" {{ request('part_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="type">
                            <option value="">Semua</option>
                            <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
                            <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sumber</label>
                        <select name="source_type">
                            <option value="">Semua</option>
                            <option value="stock_adjustment" {{ request('source_type') === 'stock_adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="part_request" {{ request('source_type') === 'part_request' ? 'selected' : '' }}>Part Request</option>
                            <option value="part_return" {{ request('source_type') === 'part_return' ? 'selected' : '' }}>Retur Part</option>
                            <option value="sales_order_detail" {{ request('source_type') === 'sales_order_detail' ? 'selected' : '' }}>Penjualan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dari</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="form-group">
                        <label>Sampai</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                        <a href="{{ route('reports.movements') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table>
                    <thead><tr><th>Tgl Transaksi</th><th>Part</th><th>Tipe</th><th>Qty</th><th>Sumber</th><th>Oleh</th></tr></thead>
                    <tbody>
                        @forelse ($data as $m)
                        <tr>
                            <td style="white-space:nowrap;">{{ $m->transaction_date ? date('d/m/Y', strtotime($m->transaction_date)) : $m->created_at->format('d/m/Y') }}</td>
                            <td>{{ $m->sparepart->name ?? 'Part #'.$m->part_id }}</td>
                            <td><span class="badge badge-{{ $m->movement_type }}">{{ $m->movement_type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                            <td>{{ $m->qty }}</td>
                            <td><span class="source-label">{{ str_replace('_', ' ', $m->source_type ?? 'langsung') }}</span></td>
                            <td style="font-size:12px;color:#888;">{{ $m->creator->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada data stock movement</td></tr>
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

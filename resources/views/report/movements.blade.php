@extends('layouts.app')

@section('title', 'Laporan Stock Movement')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 16px; }
    .card-header { padding: 14px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.2s; }
    .card-header:hover { background: #f8fafc; }
    .card-header .part-title { font-size: 14px; font-weight: 600; color: #1a1a2e; margin: 0; }
    .card-header .part-meta { font-size: 12px; color: #64748b; }
    .card-header .toggle-icon { color: #94a3b8; transition: transform 0.2s; }
    .card-header .toggle-icon.open { transform: rotate(180deg); }
    .card-body { padding: 0; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 8px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; font-size: 12px; }
    tr:hover td { background: #f8fafc; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .badge-in { background: #d1fae5; color: #065f46; }
    .badge-out { background: #fde8e8; color: #991b1b; }
    .filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px; padding: 16px 20px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .filter-bar label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .filter-bar select, .filter-bar input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 13px; outline: none; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; text-decoration: none; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
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

        <form class="filter-bar" method="GET">
            <div>
                <label>Part</label>
                <select name="part_id">
                    <option value="">Semua Part</option>
                    @foreach ($spareparts as $id => $name)
                    <option value="{{ $id }}" {{ request('part_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Tipe</label>
                <select name="type">
                    <option value="">Semua</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div>
                <label>Sumber</label>
                <select name="source_type">
                    <option value="">Semua</option>
                    <option value="stock_adjustment">Adjustment</option>
                    <option value="part_request">Part Request</option>
                    <option value="part_return">Retur Part</option>
                    <option value="sales_order_detail">Penjualan</option>
                </select>
            </div>
            <div>
                <label>Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div>
                <label>Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div>
                <label>Urut</label>
                <select name="sort">
                    <option value="asc" {{ ($sortOrder ?? 'asc') === 'asc' ? 'selected' : '' }}>Terlama</option>
                    <option value="desc" {{ ($sortOrder ?? 'asc') === 'desc' ? 'selected' : '' }}>Terbaru</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('reports.movements') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>

        @forelse ($grouped as $partId => $movements)
        @php
            $first = $movements->first()->sparepart;
            $totalIn = $movements->where('movement_type', 'in')->sum('qty');
            $totalOut = $movements->where('movement_type', 'out')->sum('qty');
            $currentStock = $first ? $first->stock_qty : 0;
        @endphp
        <div class="card">
            <div class="card-header" onclick="this.nextElementSibling.style.display=(this.nextElementSibling.style.display==='none'?'':'none');this.querySelector('.toggle-icon').classList.toggle('open')">
                <div>
                    <p class="part-title">{{ $first->name ?? 'Part #'.$partId }} <span style="font-weight:400;color:#94a3b8;">({{ $first->code ?? '' }})</span></p>
                    <p class="part-meta">{{ $movements->count() }} transaksi · Masuk: {{ $totalIn }} · Keluar: {{ $totalOut }} · <strong>Sisa: {{ $currentStock }}</strong></p>
                </div>
                <i class="fas fa-chevron-down toggle-icon open"></i>
            </div>
            <div class="card-body" style="display:block;">
                <table>
                    <thead><tr><th>Tgl</th><th>Tipe</th><th>Qty</th><th>Sisa Stok</th><th>Sumber</th><th>Oleh</th></tr></thead>
                    <tbody>
                        @php $runningStock = 0; @endphp
                        @foreach ($movements as $m)
                            @if ($m->movement_type === 'in') @php $runningStock += $m->qty; @endphp
                            @else @php $runningStock -= $m->qty; @endphp
                            @endif
                        <tr>
                            <td style="white-space:nowrap;">{{ $m->transaction_date ? date('d-M-Y', strtotime($m->transaction_date)) : $m->created_at->format('d-M-Y') }}</td>
                            <td><span class="badge badge-{{ $m->movement_type }}">{{ $m->movement_type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                            <td>{{ $m->qty }}</td>
                            <td><strong>{{ $runningStock }}</strong></td>
                            <td><span class="source-label">{{ str_replace('_', ' ', $m->source_type ?? 'langsung') }}</span></td>
                            <td style="font-size:12px;color:#888;">{{ $m->creator->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="card"><div class="card-body" style="padding:40px;text-align:center;color:#94a3b8;">Belum ada data stock movement</div></div>
        @endforelse
    </main>
</div>
<script>
document.querySelectorAll('.card-header').forEach(h => {
    if (h.nextElementSibling) {
        h.nextElementSibling.style.display = 'block';
    }
});
</script>
@endsection

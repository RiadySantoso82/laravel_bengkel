@extends('layouts.app')

@section('title', 'Riwayat Stock Adjustment')

@push('styles')
<style>
    .page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px; }
    .page-header h1 { font-size:24px;color:#1a1a2e; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-primary:hover { background:#1a1a2e; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden; }
    .card-body { padding:20px; }
    table { width:100%;border-collapse:collapse; }
    th,td { text-align:left;padding:10px 14px;border-bottom:1px solid #f0f0f0;font-size:13px; }
    th { background:#f8fafc;font-weight:600;color:#475569;font-size:12px; }
    tr:hover td { background:#f8fafc; }
    .badge { display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-in { background:#d1fae5;color:#065f46; }
    .badge-out { background:#fde8e8;color:#991b1b; }
    .alert { padding:12px;border-radius:10px;margin-bottom:16px;font-size:13px; }
    .alert-success { background:#d1fae5;color:#065f46;border:1px solid #a7f3d0; }
    .pagination { display:flex;gap:4px;justify-content:center;margin-top:16px; }
    .pagination a,.pagination span { padding:6px 12px;border-radius:6px;font-size:13px;text-decoration:none;color:#475569;background:#fff;border:1px solid #e2e8f0; }
    .pagination .active { background:#0f3460;color:#fff;border-color:#0f3460; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-balance-scale"></i> Stock Adjustment</h1>
            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Baru</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table>
                    <thead><tr><th>Tanggal</th><th>Sparepart</th><th>Qty</th><th>Alasan</th><th>Catatan</th><th>Oleh</th></tr></thead>
                    <tbody>
                        @forelse ($data as $a)
                        <tr>
                            <td style="white-space:nowrap;">{{ $a->transaction_date ? date('d/m/Y', strtotime($a->transaction_date)) : '-' }}</td>
                            <td>{{ $a->sparepart->name ?? '-' }}</td>
                            <td><span class="badge badge-{{ $a->qty > 0 ? 'in' : 'out' }}">{{ $a->qty > 0 ? '+' . $a->qty : $a->qty }}</span></td>
                            <td>{{ $a->reason }}</td>
                            <td>{{ $a->notes ?? '-' }}</td>
                            <td>{{ $a->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada data adjustment</td></tr>
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

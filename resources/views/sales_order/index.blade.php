@extends('layouts.app')

@section('title', 'Penjualan')

@push('styles')
<style>
    .page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px; }
    .page-header h1 { font-size:24px;color:#1a1a2e;margin:0; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-primary:hover { background:#1a1a2e; }
    .btn-secondary { background:#e2e8f0;color:#475569; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden; }
    .card-body { padding:0;overflow-x:auto; }
    table { width:100%;border-collapse:collapse; }
    th,td { text-align:left;padding:10px 14px;border-bottom:1px solid #f0f0f0;font-size:13px; }
    th { background:#f8fafc;font-weight:600;color:#475569;font-size:12px; }
    tr:hover td { background:#f8fafc;cursor:pointer; }
    .badge { display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-paid { background:#d1fae5;color:#065f46; }
    .badge-pending { background:#fef3c7;color:#92400e; }
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
            <h1><i class="fas fa-shopping-cart"></i> Penjualan</h1>
            <a href="{{ route('sales-orders.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Baru</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px;">
            <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px;">Dari</label><input type="date" name="date_from" value="{{ $from ?? date('Y-m-d', strtotime('-30 days')) }}" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none;"></div>
            <div><label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px;">Sampai</label><input type="date" name="date_to" value="{{ $to ?? date('Y-m-d') }}" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none;"></div>
            <button class="btn btn-primary" style="padding:8px 16px;"><i class="fas fa-search"></i></button>
            <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary" style="padding:8px 16px;"><i class="fas fa-undo"></i></a>
        </form>

        <div class="card">
            <div class="card-body">
                <table>
                    <thead><tr><th>#</th><th>Tanggal</th><th>Pelanggan</th><th>Item</th><th style="text-align:right;">Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($data as $o)
                        <tr onclick="window.location='{{ route('sales-orders.show', $o) }}'">
                            <td>SO{{ str_pad($o->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td style="white-space:nowrap;">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $o->customer->name ?? 'Walk-in' }}</td>
                            <td>{{ $o->details->sum('qty') }} pcs</td>
                            <td style="text-align:right;">{{ number_format($o->total_amount - $o->discount, 0) }}</td>
                            <td><span class="badge badge-{{ $o->payment_status }}">{{ $o->payment_status === 'paid' ? 'Lunas' : 'Pending' }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada penjualan</td></tr>
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

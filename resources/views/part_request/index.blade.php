@extends('layouts.app')

@section('title', 'Part Request')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 16px; }
    .card-header { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .card-header h3 { font-size: 15px; color: #1a1a2e; margin: 0; }
    .card-body { padding: 16px 20px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-requested { background: #fef3c7; color: #92400e; }
    .badge-partial { background: #dbeafe; color: #1e40af; }
    .badge-fulfilled { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fde8e8; color: #991b1b; }
    .badge-returned { background: #e0e7ff; color: #3730a3; }
    .detail-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
    .detail-row:last-child { border-bottom: none; }
    .detail-row .name { flex: 1; font-size: 14px; }
    .detail-row .qty { font-size: 13px; color: #666; white-space: nowrap; }
    .detail-row input { width: 70px; padding: 6px 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; text-align: center; }
    .btn { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-sm { padding: 4px 10px; font-size: 12px; }
    .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-boxes"></i> Part Request</h1></div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        @forelse ($data as $pr)
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>SO-{{ str_pad($pr->order_id, 4, '0', STR_PAD_LEFT) }} — {{ $pr->order->customer->name ?? '-' }}</h3>
                    <span style="font-size:12px;color:#888;">Mekanik: {{ $pr->mechanic->name ?? '-' }} · {{ $pr->created_at->format('d/m H:i') }}</span>
                </div>
                <span class="badge badge-{{ $pr->status }}">{{ $pr->status === 'requested' ? 'Menunggu' : ($pr->status === 'partial' ? 'Sebagian' : ($pr->status === 'fulfilled' ? 'Terpenuhi' : 'Diretur')) }}</span>
            </div>
            <div class="card-body">
                @foreach ($pr->details as $d)
                <div class="detail-row">
                    <span class="name">{{ $d->sparepart->name ?? 'Part #'.$d->part_id }}</span>
                    <span class="qty">Diminta: {{ $d->qty_requested }} | Terpenuhi: {{ $d->qty_fulfilled }}</span>
                    <span class="badge badge-{{ $d->status }}">{{ $d->status === 'pending' ? 'Pending' : ($d->status === 'fulfilled' ? 'Tersedia' : ($d->status === 'partial' ? 'Sebagian' : ($d->status === 'returned' ? 'Diretur' : $d->status))) }}</span>
                    @if (!in_array($d->status, ['fulfilled']))
                    <form method="POST" action="{{ route('part-requests.fulfill', $d) }}" style="display:flex;gap:6px;align-items:center;" onsubmit="confirmForm(this, 'Simpan fulfillment untuk part ini?')">
                        @csrf
                        <input type="number" name="qty_fulfilled" value="{{ $d->qty_requested }}" min="0" max="{{ $d->qty_requested }}" required>
                        <button class="btn btn-primary btn-sm"><i class="fas fa-check"></i></button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:60px 20px;color:#94a3b8;">
            <i class="fas fa-boxes" style="font-size:48px;margin-bottom:16px;"></i>
            <h3 style="color:#475569;font-size:16px;font-weight:500;">Tidak ada part request aktif</h3>
        </div>
        @endforelse
    </main>
</div>
@endsection

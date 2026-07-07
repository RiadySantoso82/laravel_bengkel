@extends('layouts.app')

@section('title', 'Retur Part')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 16px; }
    .card-header { padding: 16px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .card-header h3 { font-size: 15px; color: #1a1a2e; margin: 0; }
    .card-body { padding: 16px 20px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-tidak_cocok { background: #fef3c7; color: #92400e; }
    .badge-tidak_dipakai { background: #dbeafe; color: #1e40af; }
    .btn { display: inline-flex; align-items: center; gap: 4px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-undo-alt"></i> Retur Part</h1></div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        @forelse ($data as $r)
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>SO-{{ str_pad($r->detail->partRequest->order_id, 4, '0', STR_PAD_LEFT) }} — {{ $r->detail->partRequest->order->customer->name ?? '-' }}</h3>
                    <span style="font-size:12px;color:#888;">Mekanik: {{ $r->mechanic->name ?? '-' }} · {{ $r->returned_at ? date('d/m H:i', strtotime($r->returned_at)) : '-' }}</span>
                </div>
                <span class="badge badge-{{ $r->reason }}">{{ $r->reason === 'tidak_cocok' ? 'Tidak cocok' : 'Tidak dipakai' }}</span>
            </div>
            <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <strong>{{ $r->detail->sparepart->name ?? 'Part #'.$r->detail->part_id }}</strong>
                    <span style="color:#666;margin-left:12px;">Qty: {{ $r->qty_returned }}</span>
                </div>
                <form method="POST" action="{{ route('part-returns.confirm', $r) }}" onsubmit="confirmForm(this, 'Konfirmasi retur ini? Stok akan kembali ke gudang.')">
                    @csrf
                    <button class="btn btn-primary"><i class="fas fa-check"></i> Konfirmasi Retur</button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:60px 20px;color:#94a3b8;">
            <i class="fas fa-undo-alt" style="font-size:48px;margin-bottom:16px;"></i>
            <h3 style="color:#475569;font-size:16px;font-weight:500;">Tidak ada retur pending</h3>
        </div>
        @endforelse
    </main>
</div>
@endsection

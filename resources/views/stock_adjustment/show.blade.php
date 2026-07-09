@extends('layouts.app')

@section('title', 'Detail Stock Adjustment')

@push('styles')
<style>
    .detail-container { max-width:500px; margin:0 auto; padding:20px 16px; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding:24px; }
    .row-detail { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
    .row-detail:last-child { border-bottom:none; }
    .row-detail .label { color:#64748b; }
    .row-detail .value { font-weight:500; color:#1a1a2e; text-align:right; }
    .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600; }
    .badge-in { background:#d1fae5; color:#065f46; }
    .badge-out { background:#fde8e8; color:#991b1b; }
    .back-btn { width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:none;background:transparent;cursor:pointer;color:#1a1a2e;font-size:18px; }
    .back-btn:hover { background:#f0f2f5; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="detail-container">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
                <a href="{{ route('stock-adjustments.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <p style="font-weight:500;font-size:15px;margin:0;">Detail Stock Adjustment</p>
            </div>

            <div class="card"><div class="card-body">
                <div class="row-detail">
                    <span class="label">Sparepart</span>
                    <span class="value">{{ $adjustment->sparepart->name ?? '-' }}</span>
                </div>
                <div class="row-detail">
                    <span class="label">Kode</span>
                    <span class="value">{{ $adjustment->sparepart->code ?? '-' }}</span>
                </div>
                <div class="row-detail">
                    <span class="label">Penyesuaian</span>
                    <span class="value"><span class="badge badge-{{ $adjustment->qty > 0 ? 'in' : 'out' }}">{{ $adjustment->qty > 0 ? '+'.$adjustment->qty : $adjustment->qty }}</span></span>
                </div>
                <div class="row-detail">
                    <span class="label">Alasan</span>
                    <span class="value">{{ $adjustment->reason }}</span>
                </div>
                <div class="row-detail">
                    <span class="label">Catatan</span>
                    <span class="value">{{ $adjustment->notes ?? '-' }}</span>
                </div>
                <div class="row-detail">
                    <span class="label">Tanggal</span>
                    <span class="value">{{ $adjustment->transaction_date ? date('d/m/Y', strtotime($adjustment->transaction_date)) : '-' }}</span>
                </div>
                <div class="row-detail">
                    <span class="label">Oleh</span>
                    <span class="value">{{ $adjustment->user->name ?? '-' }}</span>
                </div>
                <div class="row-detail">
                    <span class="label">Dibuat</span>
                    <span class="value">{{ $adjustment->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div></div>

            @if (session('error'))
            <div style="padding:12px;border-radius:10px;margin-top:16px;font-size:13px;background:#fde8e8;color:#991b1b;border:1px solid #f8c0c0;"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <div style="margin-top:16px;">
                <form method="POST" action="{{ route('stock-adjustments.destroy', $adjustment) }}" onsubmit="confirmForm(this, 'Yakin ingin menghapus adjustment ini? Stok akan menyesuaikan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn" style="width:100%;padding:12px;border-radius:10px;border:none;background:#dc2626;color:#fff;font-size:14px;font-weight:600;cursor:pointer;"><i class="fas fa-trash"></i> Hapus Adjustment</button>
                </form>
            </div>
        </div>
    </main>
</div>
@endsection

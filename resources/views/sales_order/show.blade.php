@extends('layouts.app')

@section('title', 'Detail Penjualan')

@push('styles')
<style>
    .detail-container { max-width:500px; margin:0 auto; padding:20px 16px; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding:24px; }
    .row-detail { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
    .row-detail:last-child { border-bottom:none; }
    .row-detail .label { color:#64748b; }
    .row-detail .value { font-weight:500; color:#1a1a2e; text-align:right; }
    .badge { display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-paid { background:#d1fae5; color:#065f46; }
    .badge-pending { background:#fef3c7; color:#92400e; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
    .btn-danger { background:#dc2626; color:#fff; }
    .btn-danger:hover { background:#b91c1c; }
    .back-btn { width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:none;background:transparent;cursor:pointer;color:#1a1a2e;font-size:18px; }
    .back-btn:hover { background:#f0f2f5; }
    .alert { padding:12px;border-radius:10px;margin-bottom:16px;font-size:13px; }
    .alert-error { background:#fde8e8; color:#991b1b; border:1px solid #f8c0c0; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="detail-container">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
                <a href="{{ route('sales-orders.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <p style="font-weight:500;font-size:15px;margin:0;">Detail Penjualan</p>
            </div>

            @if (session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            <div class="card" style="margin-bottom:16px;"><div class="card-body">
                <div class="row-detail"><span class="label">No. Transaksi</span><span class="value">SO{{ str_pad($salesOrder->id, 5, '0', STR_PAD_LEFT) }}</span></div>
                <div class="row-detail"><span class="label">Tanggal</span><span class="value">{{ $salesOrder->created_at->format('d/m/Y H:i') }}</span></div>
                <div class="row-detail"><span class="label">Pelanggan</span><span class="value">{{ $salesOrder->customer->name ?? 'Walk-in' }}</span></div>
                <div class="row-detail"><span class="label">Kasir</span><span class="value">{{ $salesOrder->user->name ?? '-' }}</span></div>
                <div class="row-detail"><span class="label">Status</span><span class="value"><span class="badge badge-{{ $salesOrder->payment_status }}">{{ $salesOrder->payment_status === 'paid' ? 'Lunas' : 'Pending' }}</span></span></div>
            </div></div>

            <div class="card" style="margin-bottom:16px;"><div class="card-body">
                <p style="font-size:13px;font-weight:600;color:#475569;margin:0 0 12px;">Item</p>
                @foreach ($salesOrder->details as $d)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:13px;">
                    <div><span style="font-weight:500;">{{ $d->sparepart->name ?? 'Part #'.$d->part_id }}</span><br><span style="font-size:12px;color:#94a3b8;">{{ $d->qty }} pcs × Rp {{ number_format($d->sell_price, 0) }}</span></div>
                    <div style="text-align:right;"><span style="font-weight:500;">Rp {{ number_format($d->qty * $d->sell_price, 0) }}</span><br><span style="font-size:11px;color:#94a3b8;">HPP: Rp {{ number_format($d->cost_price, 0) }}</span></div>
                </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;">
                    <span>Subtotal</span><span>Rp {{ number_format($salesOrder->total_amount, 0) }}</span>
                </div>
                @if ($salesOrder->discount > 0)
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:14px;color:#dc2626;">
                    <span>Diskon</span><span>-Rp {{ number_format($salesOrder->discount, 0) }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;padding:10px 0;font-size:16px;font-weight:600;">
                    <span>Total</span><span>Rp {{ number_format($salesOrder->total_amount - $salesOrder->discount, 0) }}</span>
                </div>
            </div></div>

            <div class="card" style="margin-bottom:16px;"><div class="card-body">
                <p style="font-size:13px;font-weight:600;color:#475569;margin:0 0 12px;">Pembayaran</p>
                @forelse ($salesOrder->payments as $pt)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:13px;">
                    <div><span style="font-weight:500;">{{ $pt->paymentMethod->name ?? '-' }}</span><br><span style="font-size:12px;color:#94a3b8;">{{ $pt->paid_at ? $pt->paid_at->format('d/m/Y H:i') : '-' }}</span></div>
                    <div style="text-align:right;"><span>Rp {{ number_format($pt->amount, 0) }}</span></div>
                </div>
                @empty
                <p style="font-size:13px;color:#94a3b8;">Belum ada pembayaran</p>
                @endforelse
                <div class="row-detail" style="margin-top:8px;"><span class="label">Total HPP</span><span class="value">Rp {{ number_format($salesOrder->details->sum('cost_price'), 0) }}</span></div>
                <div class="row-detail"><span class="label">Margin</span><span class="value" style="color:#059669;">Rp {{ number_format(($salesOrder->total_amount - $salesOrder->discount) - $salesOrder->details->sum('cost_price'), 0) }}</span></div>
            </div></div>

            <form method="POST" action="{{ route('sales-orders.destroy', $salesOrder) }}" onsubmit="confirmForm(this, 'Yakin ingin menghapus penjualan ini? Stok akan dikembalikan.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;"><i class="fas fa-trash"></i> Hapus Penjualan</button>
            </form>
        </div>
    </main>
</div>
@endsection

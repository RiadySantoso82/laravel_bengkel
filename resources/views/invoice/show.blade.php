@extends('layouts.app')

@section('title', 'Detail Invoice')

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
    .badge-partial { background:#dbeafe; color:#1e40af; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
    .btn-secondary { background:#e2e8f0; color:#475569; }
    .back-btn { width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;border:none;background:transparent;cursor:pointer;color:#1a1a2e;font-size:18px; }
    .back-btn:hover { background:#f0f2f5; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="detail-container">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
                <a href="{{ request('from') === 'revenue' ? route('reports.revenue') : route('invoices.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <p style="font-weight:500;font-size:15px;margin:0;">Detail Invoice</p>
            </div>

            <div class="card" style="margin-bottom:16px;"><div class="card-body">
                <div class="row-detail"><span class="label">No. Invoice</span><span class="value">INV{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</span></div>
                <div class="row-detail"><span class="label">Service Order</span><span class="value">SO-{{ str_pad($invoice->order_id, 4, '0', STR_PAD_LEFT) }}</span></div>
                <div class="row-detail"><span class="label">Pelanggan</span><span class="value">{{ $invoice->order->customer->name ?? '-' }}</span></div>
                <div class="row-detail"><span class="label">Kendaraan</span><span class="value">{{ $invoice->order->vehicle->plate_number ?? $invoice->order->vehicle_plate_manual ?? '-' }}</span></div>
                <div class="row-detail"><span class="label">Tanggal</span><span class="value">{{ $invoice->created_at->format('d/m/Y H:i') }}</span></div>
                <div class="row-detail"><span class="label">Status</span><span class="value"><span class="badge badge-{{ $invoice->payment_status }}">{{ $invoice->payment_status === 'paid' ? 'Lunas' : ($invoice->payment_status === 'partial' ? 'Cicil' : 'Pending') }}</span></span></div>
            </div></div>

            <div class="card" style="margin-bottom:16px;"><div class="card-body">
                <p style="font-size:13px;font-weight:600;color:#475569;margin:0 0 12px;">Pembayaran</p>
                @forelse ($invoice->payments as $pt)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:13px;">
                    <div><span style="font-weight:500;">{{ $pt->paymentMethod->name ?? '-' }}</span><br><span style="font-size:12px;color:#94a3b8;">{{ $pt->paid_at ? date('d/m/Y H:i', strtotime($pt->paid_at)) : '-' }}</span></div>
                    <div style="text-align:right;"><span>Rp {{ number_format($pt->amount, 0) }}</span></div>
                </div>
                @empty
                <p style="font-size:13px;color:#94a3b8;">Belum ada pembayaran</p>
                @endforelse
                <div class="row-detail" style="margin-top:8px;"><span class="label">Total Tagihan</span><span class="value">Rp {{ number_format($invoice->total_amount, 0) }}</span></div>
                @if ($invoice->discount > 0)
                <div class="row-detail"><span class="label">Diskon</span><span class="value" style="color:#dc2626;">-Rp {{ number_format($invoice->discount, 0) }}</span></div>
                @endif
                <div class="row-detail"><span class="label">Dibayar</span><span class="value" style="color:#059669;">Rp {{ number_format($invoice->total_amount - $invoice->discount, 0) }}</span></div>
            </div></div>
        </div>
    </main>
</div>
@endsection

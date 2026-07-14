@extends('layouts.app')

@section('title', isset($invoice) && $invoice ? 'Edit Invoice' : 'Buat Invoice')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding: 30px; max-width: 600px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.3s; }
    .form-control:focus { border-color: #0f3460; }
    select.form-control { background: #fff; }
    .row { display: flex; gap: 16px; }
    .row .form-group { flex: 1; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .btn-secondary:hover { background: #cbd5e1; }
    .form-actions { display: flex; gap: 10px; margin-top: 24px; }
    .info-box { background: #f8fafc; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 14px; }
    .info-box strong { color: #1a1a2e; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-file-invoice-dollar"></i> {{ isset($invoice) ? 'Edit Invoice' : 'Buat Invoice' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($invoice) && $invoice ? route('invoices.update', $invoice) : route('invoices.store') }}">
                @csrf @if (isset($invoice) && $invoice) @method('PUT') @endif

                <div class="form-group">
                <label for="order_id">Service Order</label>
                @if (isset($invoice))
                <input type="text" class="form-control" value="SO-{{ str_pad($invoice->order_id, 4, '0', STR_PAD_LEFT) }}" readonly>
                <input type="hidden" name="order_id" value="{{ $invoice->order_id }}">
                @else
                <select id="order_id" name="order_id" class="form-control" required onchange="loadOrder(this.value)">
                    <option value="">-- Pilih --</option>
                    @foreach ($orders as $o)
                        <option value="{{ $o->id }}" data-total="{{ $o->details->sum(function($d) { return $d->qty * $d->price; }) }}" {{ old('order_id', $invoice->order_id ?? '') == $o->id ? 'selected' : '' }}>
                            SO-{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }} - {{ $o->customer->name ?? '' }} - {{ $o->vehicle->plate_number ?? '' }}
                        </option>
                    @endforeach
                </select>
                @endif
            </div>

            <div class="info-box" id="order-info" style="{{ isset($invoice) && $invoice->order_id ? 'display:block' : 'display:none' }};">
                <strong>Ringkasan Service Order</strong><br>
                <span id="order-detail"></span>
            </div>

                <div class="row">
                    <div class="form-group">
                        <label for="total_amount">Total Tagihan</label>
                        <input type="number" id="total_amount" name="total_amount" class="form-control" value="{{ old('total_amount', $invoice->total_amount ?? 0) }}" min="0" step="100" required>
                    </div>
                    <div class="form-group">
                        <label for="discount">Diskon</label>
                        <input type="number" id="discount" name="discount" class="form-control" value="{{ old('discount', $invoice->discount ?? 0) }}" min="0" step="100">
                    </div>
                </div>

                <div class="info-box">
                    <strong>Total Dibayar: Rp <span id="grand-total">{{ number_format((old('total_amount', $invoice->total_amount ?? 0)) - (old('discount', $invoice->discount ?? 0)), 0) }}</span></strong>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="payment_method_id">Metode Pembayaran</label>
                        <select id="payment_method_id" name="payment_method_id" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach ($paymentMethods as $pm)
                                <option value="{{ $pm->id }}" {{ old('payment_method_id', optional($invoice->payments->first())->payment_method_id ?? '') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_status">Status Pembayaran</label>
                        <select id="payment_status" name="payment_status" class="form-control" required>
                            <option value="pending" {{ old('payment_status', $invoice->payment_status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('payment_status', $invoice->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="partial" {{ old('payment_status', $invoice->payment_status ?? '') === 'partial' ? 'selected' : '' }}>Cicil</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div></div>
    </main>
</div>
@endsection

@push('scripts')
<script>
function loadOrder(id) {
    const opt = document.querySelector('#order_id option[value="' + id + '"]');
    const info = document.getElementById('order-info');
    if (opt) {
        info.style.display = 'block';
        document.getElementById('order-detail').textContent = opt.textContent;
        const total = parseFloat(opt.dataset.total) || 0;
        document.getElementById('total_amount').value = total;
        calcTotal();
    } else {
        info.style.display = 'none';
    }
}

function calcTotal() {
    const total = parseFloat(document.getElementById('total_amount').value) || 0;
    const disc = parseFloat(document.getElementById('discount').value) || 0;
    document.getElementById('grand-total').textContent = (total - disc).toLocaleString();
}

document.getElementById('total_amount').addEventListener('change', calcTotal);
document.getElementById('total_amount').addEventListener('keyup', calcTotal);
document.getElementById('discount').addEventListener('change', calcTotal);
document.getElementById('discount').addEventListener('keyup', calcTotal);

@if (isset($invoice) && $invoice->order_id)
    loadOrder({{ $invoice->order_id }});
@endif
</script>
@endpush

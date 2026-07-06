@extends('layouts.app')

@section('title', isset($serviceOrder) ? 'Edit Service Order' : 'Buat Service Order')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding: 30px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.3s; }
    .form-control:focus { border-color: #0f3460; }
    select.form-control { background: #fff; }
    textarea.form-control { resize: vertical; min-height: 80px; }
    .row { display: flex; gap: 16px; }
    .row .form-group { flex: 1; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .btn-secondary:hover { background: #cbd5e1; }
    .btn-success { background: #059669; color: #fff; }
    .btn-success:hover { background: #047857; }
    .btn-danger { background: #dc2626; color: #fff; }
    .btn-danger:hover { background: #b91c1c; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .form-actions { display: flex; gap: 10px; margin-top: 24px; }
    .items-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    .items-table th { background: #f8fafc; font-weight: 600; color: #475569; padding: 10px 12px; font-size: 13px; border-bottom: 2px solid #e2e8f0; text-align: left; }
    .items-table td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .items-table select, .items-table input { width: 100%; padding: 6px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; outline: none; }
    .items-table select:focus, .items-table input:focus { border-color: #0f3460; }
    .item-total { font-weight: 600; color: #0f3460; }
    .grand-total { font-size: 18px; font-weight: 700; color: #1a1a2e; text-align: right; margin-top: 16px; padding-top: 16px; border-top: 2px solid #e2e8f0; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-wrench"></i> {{ isset($serviceOrder) ? 'Edit Service Order' : 'Buat Service Order' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($serviceOrder) ? route('service-orders.update', $serviceOrder) : route('service-orders.store') }}">
                @csrf @if (isset($serviceOrder)) @method('PUT') @endif

                <div class="row">
                    <div class="form-group">
                        <label for="customer_id">Pelanggan</label>
                        <select id="customer_id" name="customer_id" class="form-control" required onchange="handleCustomerChange()">
                            <option value="">-- Pilih --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" data-walkin="{{ $c->is_walk_in ? '1' : '0' }}" {{ old('customer_id', $serviceOrder->customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}{{ $c->is_walk_in ? ' (Walk-in)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <div style="display:flex;gap:8px;align-items:flex-end;">
                            <div style="flex:1;">
                                <label for="vehicle_id">Kendaraan</label>
                                <select id="vehicle_id" name="vehicle_id" class="form-control">
                                    <option value="">-- Pilih Pelanggan dulu --</option>
                                </select>
                            </div>
                            <div style="padding-bottom:4px;">
                                <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:13px;cursor:pointer;">
                                    <input type="checkbox" id="walkin-toggle" onchange="toggleWalkin()">
                                    Walk-in
                                </label>
                            </div>
                        </div>
                        <div id="walkin-fields" style="display:none;margin-top:8px;padding:12px;background:#f8fafc;border-radius:8px;">
                            <div class="row" style="gap:12px;">
                                <div class="form-group" style="margin-bottom:0;flex:1;">
                                    <label style="font-size:12px;">Plat Nomor</label>
                                    <input type="text" name="vehicle_plate_manual" class="form-control" style="font-size:13px;" value="{{ old('vehicle_plate_manual', $serviceOrder->vehicle_plate_manual ?? '') }}" placeholder="Contoh: B 1234 AB">
                                </div>
                                <div class="form-group" style="margin-bottom:0;flex:2;">
                                    <label style="font-size:12px;">Info Kendaraan</label>
                                    <input type="text" name="vehicle_info_manual" class="form-control" style="font-size:13px;" value="{{ old('vehicle_info_manual', $serviceOrder->vehicle_info_manual ?? '') }}" placeholder="Contoh: Honda Beat hitam 2019">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="mechanic_id">Mekanik</label>
                        <select id="mechanic_id" name="mechanic_id" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach ($mechanics as $m)
                                <option value="{{ $m->id }}" {{ old('mechanic_id', $serviceOrder->mechanic_id ?? '') == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->specialization ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estimated_finish">Estimasi Selesai</label>
                        <input type="datetime-local" id="estimated_finish" name="estimated_finish" class="form-control" value="{{ old('estimated_finish', isset($serviceOrder) && $serviceOrder->estimated_finish ? date('Y-m-d\TH:i', strtotime($serviceOrder->estimated_finish)) : '') }}">
                    </div>
                </div>

                @if (isset($serviceOrder))
                <div class="row">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="queued" {{ $serviceOrder->status === 'queued' ? 'selected' : '' }}>Antri</option>
                            <option value="in_progress" {{ $serviceOrder->status === 'in_progress' ? 'selected' : '' }}>Dikerjakan</option>
                            <option value="waiting_part" {{ $serviceOrder->status === 'waiting_part' ? 'selected' : '' }}>Tunggu Part</option>
                            <option value="done" {{ $serviceOrder->status === 'done' ? 'selected' : '' }}>Selesai</option>
                            <option value="picked_up" {{ $serviceOrder->status === 'picked_up' ? 'selected' : '' }}>Diambil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="actual_finish">Selesai Aktual</label>
                        <input type="datetime-local" id="actual_finish" name="actual_finish" class="form-control" value="{{ old('actual_finish', isset($serviceOrder) && $serviceOrder->actual_finish ? date('Y-m-d\TH:i', strtotime($serviceOrder->actual_finish)) : '') }}">
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label for="complaint">Keluhan</label>
                    <textarea id="complaint" name="complaint" class="form-control">{{ old('complaint', $serviceOrder->complaint ?? '') }}</textarea>
                </div>

                <hr style="margin:24px 0;border:none;border-top:1px solid #e2e8f0;">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <h3 style="font-size:16px;color:#1a1a2e;"><i class="fas fa-list"></i> Item Pekerjaan / Part</h3>
                    <button type="button" class="btn btn-success btn-sm" onclick="addItem()"><i class="fas fa-plus"></i> Tambah Item</button>
                </div>

                <table class="items-table" id="items-table">
                    <thead><tr><th style="width:100px;">Tipe</th><th>Item</th><th style="width:80px;">Qty</th><th style="width:150px;">Harga</th><th style="width:100px;">Subtotal</th><th style="width:40px;"></th></tr></thead>
                    <tbody id="items-body">
                    </tbody>
                </table>

                <div class="grand-total">Total: Rp <span id="grand-total">0</span></div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('service-orders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div></div>
    </main>
</div>
@endsection

@push('scripts')
<script>
const serviceTypes = @json($serviceTypes ?? []);
const spareparts = @json($spareparts ?? []);

let itemIndex = 0;

function loadVehicles(customerId) {
    fetch('{{ route("service-orders.vehicles") }}?customer_id=' + customerId)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('vehicle_id');
            sel.innerHTML = '<option value="">-- Pilih --</option>';
            data.forEach(v => { sel.innerHTML += '<option value="' + v.id + '">' + v.plate_number + ' - ' + v.brand + ' ' + v.model + '</option>'; });
            const selected = '{{ old("vehicle_id", $serviceOrder->vehicle_id ?? "") }}';
            if (selected) sel.value = selected;
        });
}

function addItem(data) {
    data = data || { type: 'jasa', item_id: '', qty: 1, price: 0 };
    const i = itemIndex++;
    const tr = document.createElement('tr');
    tr.id = 'item-' + i;
    tr.innerHTML = `
        <td>
            <select name="items[` + i + `][type]" class="form-control" onchange="updateItemOptions(` + i + `)" style="font-size:13px;padding:6px 8px;">
                <option value="jasa" ` + (data.type === 'jasa' ? 'selected' : '') + `>Jasa</option>
                <option value="part" ` + (data.type === 'part' ? 'selected' : '') + `>Part</option>
            </select>
        </td>
        <td>
            <select name="items[` + i + `][item_id]" class="form-control" onchange="updateItemPrice(` + i + `)" style="font-size:13px;padding:6px 8px;">
                <option value="">-- Pilih --</option>
            </select>
        </td>
        <td><input type="number" name="items[` + i + `][qty]" value="` + data.qty + `" min="1" class="form-control" style="font-size:13px;padding:6px 8px;" onchange="calcRow(` + i + `)" onkeyup="calcRow(` + i + `)"></td>
        <td><input type="number" name="items[` + i + `][price]" value="` + data.price + `" min="0" class="form-control" style="font-size:13px;padding:6px 8px;" onchange="calcRow(` + i + `)" onkeyup="calcRow(` + i + `)"></td>
        <td class="item-total" id="subtotal-` + i + `">` + (data.qty * data.price).toLocaleString() + `</td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('item-` + i + `').remove(); calcGrandTotal();"><i class="fas fa-times"></i></button></td>
    `;
    document.getElementById('items-body').appendChild(tr);
    updateItemOptions(i, data.type, data.item_id);
    calcGrandTotal();
}

function updateItemOptions(i, forceType, forceItem) {
    const row = document.getElementById('item-' + i);
    const typeSel = row.querySelector('select[name$="[type]"]');
    const itemSel = row.querySelector('select[name$="[item_id]"]');
    const type = forceType || typeSel.value;
    const items = type === 'jasa' ? serviceTypes : spareparts;
    const label = type === 'jasa' ? 'Nama Jasa' : 'Nama Part';
    itemSel.innerHTML = '<option value="">-- ' + label + ' --</option>';
    items.forEach(it => {
        const name = type === 'jasa' ? it.name : (it.name + ' (' + it.code + ')');
        itemSel.innerHTML += '<option value="' + it.id + '" data-price="' + (type === 'jasa' ? it.base_price : it.sell_price) + '">' + name + '</option>';
    });
    if (forceItem) { itemSel.value = forceItem; }
    updateItemPrice(i);
}

function updateItemPrice(i) {
    const row = document.getElementById('item-' + i);
    const itemSel = row.querySelector('select[name$="[item_id]"]');
    const priceInput = row.querySelector('input[name$="[price]"]');
    const selected = itemSel.options[itemSel.selectedIndex];
    if (selected && selected.dataset.price) {
        priceInput.value = selected.dataset.price;
    }
    calcRow(i);
}

function calcRow(i) {
    const row = document.getElementById('item-' + i);
    const qty = parseFloat(row.querySelector('input[name$="[qty]"]').value) || 0;
    const price = parseFloat(row.querySelector('input[name$="[price]"]').value) || 0;
    document.getElementById('subtotal-' + i).textContent = (qty * price).toLocaleString();
    calcGrandTotal();
}

function calcGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-total').forEach(el => {
        total += parseFloat(el.textContent.replace(/,/g, '')) || 0;
    });
    document.getElementById('grand-total').textContent = total.toLocaleString();
}

function toggleWalkin(forceDisable) {
    const cb = document.getElementById('walkin-toggle');
    const isWalkin = cb.checked;
    document.getElementById('walkin-fields').style.display = isWalkin ? 'block' : 'none';
    document.getElementById('vehicle_id').style.display = isWalkin ? 'none' : 'block';
    cb.disabled = !!forceDisable;
    if (isWalkin) {
        document.getElementById('vehicle_id').value = '';
    } else {
        const cust = document.getElementById('customer_id').value;
        if (cust) loadVehicles(cust);
    }
}

function handleCustomerChange() {
    const sel = document.getElementById('customer_id');
    const opt = sel.options[sel.selectedIndex];
    const isWalkin = opt && opt.dataset.walkin === '1';
    const cb = document.getElementById('walkin-toggle');
    if (isWalkin) {
        cb.checked = true;
        toggleWalkin(true);
    } else {
        cb.checked = false;
        toggleWalkin(false);
        if (sel.value) loadVehicles(sel.value);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @if (isset($serviceOrder))
        handleCustomerChange();
        @if ($serviceOrder->vehicle_plate_manual || $serviceOrder->vehicle_info_manual)
            document.getElementById('walkin-toggle').checked = true;
            toggleWalkin(true);
        @endif
        @foreach ($serviceOrder->details as $d)
            addItem({ type: '{{ $d->type }}', item_id: {{ $d->item_id }}, qty: {{ $d->qty }}, price: {{ $d->price }} });
        @endforeach
    @else
        handleCustomerChange();
        addItem();
    @endif
});
</script>
@endpush

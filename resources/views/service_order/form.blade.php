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

<div class="modal-overlay" id="partSearchModal">
    <div class="modal-box" style="text-align:left;max-width:480px;height:80vh;display:flex;flex-direction:column;padding:16px 20px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-shrink:0;">
            <button type="button" onclick="closePartSearch()" style="width:32px;height:32px;border-radius:50%;border:none;background:var(--surface-2);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">&larr;</button>
            <p style="font-weight:500;font-size:15px;margin:0;">Pilih sparepart</p>
        </div>
        <div style="position:relative;margin-bottom:12px;flex-shrink:0;">
            <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--text-muted);"></i>
            <input type="text" id="partSearchInput" placeholder="Cari kode atau nama sparepart..." style="width:100%;padding:10px 12px 10px 36px;border:1px solid #ddd;border-radius:8px;font-size:14px;outline:none;" onkeyup="searchParts()">
        </div>
        <div style="font-size:12px;color:var(--text-secondary);margin-bottom:8px;flex-shrink:0;" id="partResultCount"></div>
        <div id="partSearchResults" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
    </div>
</div>

@push('scripts')
<script>
const serviceTypes = @json($serviceTypes ?? []);
let partSearchData = [], pendingPartTarget = null, itemIndex = 0;

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

function openPartSearch(targetIdx) {
    pendingPartTarget = targetIdx;
    document.getElementById('partSearchModal').classList.add('active');
    document.getElementById('partSearchInput').value = '';
    searchParts();
}
function closePartSearch() { document.getElementById('partSearchModal').classList.remove('active'); pendingPartTarget = null; }

function searchParts() {
    const q = document.getElementById('partSearchInput').value;
    fetch('{{ route("sales-orders.search-part") }}?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
            partSearchData = data.results;
            document.getElementById('partResultCount').textContent = partSearchData.length + ' hasil';
            const list = document.getElementById('partSearchResults');
            list.innerHTML = partSearchData.map((r, idx) => {
                const stock = r.stock_qty || 0;
                const sellPrice = r.sell_price || 0;
                return '<div class="search-item" data-idx="' + idx + '" style="cursor:pointer;">' +
                    '<div style="flex:1;"><p style="font-size:14px;font-weight:500;margin:0;color:#1a1a2e;">' + r.name + '</p>' +
                    '<p style="font-size:12px;color:var(--text-secondary);margin:0;">' + (r.code || '') + ' · ' + (r.category || '') + ' · ' + (r.unit || '') + ' · Stok: ' + stock + '</p></div>' +
                    '<div style="text-align:right;"><p style="font-size:11px;color:var(--text-secondary);margin:0;">Harga</p><p style="font-size:16px;font-weight:500;margin:0;">Rp ' + (sellPrice ? Number(sellPrice).toLocaleString('id-ID') : '0') + '</p></div></div>';
            }).join('');
            if (!partSearchData.length) list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ditemukan</div>';
        });
}

document.getElementById('partSearchResults').addEventListener('click', function(e) {
    const item = e.target.closest('.search-item');
    if (!item) return;
    const idx = parseInt(item.dataset.idx);
    const r = partSearchData[idx];
    if (!r) return;
    if (pendingPartTarget !== null) {
        const row = document.getElementById('item-' + pendingPartTarget);
        if (row) {
            const idInput = row.querySelector('input[name$="[item_id]"]');
            const priceInput = row.querySelector('input[name$="[price]"]');
            const nameSpan = row.querySelector('.part-name');
            if (idInput) idInput.value = r.id;
            if (priceInput) priceInput.value = r.sell_price || 0;
            if (nameSpan) nameSpan.textContent = r.name + ' (' + (r.code || '') + ')';
            calcRow(pendingPartTarget);
        }
    }
    closePartSearch();
});

function addItem(data) {
    data = data || { type: 'jasa', item_id: '', qty: 1, price: 0 };
    const i = itemIndex++;
    const isPart = data.type === 'part';
    const tr = document.createElement('tr');
    tr.id = 'item-' + i;
    tr.innerHTML = `
        <td>
            <select name="items[` + i + `][type]" class="form-control" onchange="updateItemOptions(` + i + `)" style="font-size:13px;padding:6px 8px;">
                <option value="jasa" ` + (data.type === 'jasa' ? 'selected' : '') + `>Jasa</option>
                <option value="part" ` + (isPart ? 'selected' : '') + `>Part</option>
            </select>
        </td>
        <td>
            <input type="hidden" name="items[` + i + `][item_id]" value="` + (data.item_id || '') + `">
            <div style="display:flex;gap:4px;align-items:center;">
                <span class="part-name" style="font-size:13px;flex:1;color:` + (data.item_id ? '#1a1a2e' : '#94a3b8') + `;">` + (data.item_id ? '(Selected)' : (isPart ? 'Klik cari part' : '')) + `</span>
                <button type="button" class="btn btn-sm btn-primary" onclick="openPartSearch(` + i + `)" style="font-size:11px;padding:4px 8px;display:` + (isPart ? '' : 'none') + `;"><i class="fas fa-search"></i> Cari</button>
            </div>
            <select name="_jasa_select" class="form-control" onchange="selectJasa(` + i + `, this)" style="font-size:13px;padding:6px 8px;display:` + (isPart ? 'none' : '') + `;">
                <option value="">-- Pilih Jasa --</option>
                ` + serviceTypes.map(j => '<option value="' + j.id + '" data-price="' + (j.base_price || 0) + '">' + j.name + '</option>').join('') + `
            </select>
        </td>
        <td><input type="number" name="items[` + i + `][qty]" value="` + data.qty + `" min="1" class="form-control no-cleave" style="font-size:13px;padding:6px 8px;" onchange="calcRow(` + i + `)" onkeyup="calcRow(` + i + `)"></td>
        <td><input type="number" name="items[` + i + `][price]" value="` + data.price + `" min="0" class="form-control" style="font-size:13px;padding:6px 8px;" onchange="calcRow(` + i + `);var evt=document.createEvent('HTMLEvents');evt.initEvent('cleave-update',true,false);document.dispatchEvent(evt);" onkeyup="calcRow(` + i + `)"></td>
        <td class="item-total" id="subtotal-` + i + `">` + (data.qty * data.price).toLocaleString() + `</td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('item-' + i).remove(); calcGrandTotal();"><i class="fas fa-times"></i></button></td>
    `;
    document.getElementById('items-body').appendChild(tr);
    if (data.item_id && isPart) {
        tr.querySelector('.part-name').textContent = '(Selected #' + data.item_id + ')';
    }
    if (data.item_id && !isPart) {
        const sel = tr.querySelector('select[name="_jasa_select"]');
        if (sel) sel.value = data.item_id;
    }
    calcGrandTotal();
}

function updateItemOptions(i) {
    const row = document.getElementById('item-' + i);
    const typeSel = row.querySelector('select[name$="[type]"]');
    const type = typeSel.value;
    const btn = row.querySelector('.btn-primary');
    const jasaSel = row.querySelector('select[name="_jasa_select"]');
    const nameSpan = row.querySelector('.part-name');
    const idInput = row.querySelector('input[name$="[item_id]"]');
    if (type === 'part') {
        if (btn) btn.style.display = '';
        if (jasaSel) jasaSel.style.display = 'none';
        if (nameSpan) { nameSpan.style.display = ''; nameSpan.textContent = '(Klik cari part)'; }
        if (idInput) idInput.value = '';
    } else {
        if (btn) btn.style.display = 'none';
        if (jasaSel) jasaSel.style.display = '';
        if (nameSpan) nameSpan.style.display = 'none';
    }
}

function selectJasa(i, sel) {
    const row = document.getElementById('item-' + i);
    const idInput = row.querySelector('input[name$="[item_id]"]');
    const priceInput = row.querySelector('input[name$="[price]"]');
    const opt = sel.options[sel.selectedIndex];
    if (idInput) idInput.value = sel.value;
    if (priceInput && opt && opt.dataset.price) priceInput.value = opt.dataset.price;
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

document.getElementById('partSearchModal').addEventListener('click', function(e) { if (e.target === this) closePartSearch(); });
</script>
<style>
.search-item { display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:12px;transition:all 0.2s; }
.search-item:hover { background:var(--surface-2); }
</style>
@endpush

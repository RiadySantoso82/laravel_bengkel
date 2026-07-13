@extends('layouts.app')

@section('title', 'Edit Penjualan')

@push('styles')
<style>
    :root { --radius:12px; --surface-1:#fff; --surface-2:#f0f2f5; --text-secondary:#64748b; --text-muted:#94a3b8; --fill-primary:#0f3460; --on-primary:#fff; --border:#e2e8f0; }
    .form-container { max-width:100%; margin:0 auto; }
    .card-form { background:var(--surface-1); border-radius:20px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .section-title { font-weight:500; font-size:16px; margin:0 0 2px; color:#1a1a2e; }
    .section-sub { font-size:13px; color:var(--text-secondary); margin:0 0 20px; }
    .form-group { margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:500; display:block; margin-bottom:6px; color:#333; }
    .form-control { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:var(--radius); font-size:14px; outline:none; background:var(--surface-1); }
    .form-control:focus { border-color:var(--fill-primary); }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:var(--radius); font-size:14px; font-weight:500; border:none; cursor:pointer; transition:all 0.3s; }
    .btn-primary { background:var(--fill-primary); color:var(--on-primary); width:100%; justify-content:center; padding:12px; }
    .btn-primary:hover { background:#1a1a2e; }
    .btn-sm { padding:6px 10px; font-size:12px; border-radius:8px; }
    .search-btn { width:100%; display:flex; align-items:center; justify-content:space-between; padding:10px 12px; text-align:left; border-radius:var(--radius); border:1px solid #ddd; background:var(--surface-1); cursor:pointer; }
    .search-btn:hover { border-color:var(--fill-primary); }
    .cart-item { background:var(--surface-2); border-radius:12px; padding:12px 14px; }
    .cart-item .top { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
    .cart-item .top .name { font-size:14px; font-weight:500; margin:0; }
    .cart-item .top .meta { font-size:12px; color:var(--text-secondary); margin:0; }
    .cart-item .qty-row { display:flex; align-items:center; justify-content:space-between; }
    .cart-item .qty-controls { display:flex; align-items:center; gap:8px; }
    .cart-item .qty-controls button { width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:6px; border:1px solid #ddd; background:#fff; cursor:pointer; font-weight:600; }
    .cart-item .qty-controls span { font-size:14px; width:20px; text-align:center; }
    .cart-item .subtotal { font-size:14px; font-weight:500; margin:0; }
    .summary { display:flex; flex-direction:column; gap:6px; padding:12px 14px; background:var(--surface-2); border-radius:12px; }
    .summary .row { display:flex; align-items:center; justify-content:space-between; }
    .summary .row p { margin:0; font-size:13px; color:var(--text-secondary); }
    .summary .total p { font-size:14px; font-weight:500; }
    @media (min-width:769px) {
        .pos-layout { display:flex; gap:20px; align-items:flex-start; max-width:1000px; margin:0 auto; }
        .pos-cart { flex:1.4; }
        .pos-checkout { width:340px; position:sticky; top:20px; }
        .card-form { padding:1.5rem; }
    }
    @media (max-width:768px) { .form-container { padding:0; } .card-form { border-radius:16px; padding:1rem; } }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="form-container">
            <div class="pos-layout">
                <div class="pos-cart">
                    <div class="card-form">
                        <div style="margin-bottom:1rem;">
                            <p class="section-title">Edit Penjualan</p>
                            <p class="section-sub">Order #SO{{ str_pad($salesOrder->id, 5, '0', STR_PAD_LEFT) }} (Pending)</p>
                        </div>
                        <form method="POST" action="{{ route('sales-orders.update', $salesOrder) }}" id="salesForm">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="total_amount" id="totalAmount" value="{{ $salesOrder->total_amount }}">
                            <input type="hidden" name="discount" id="discountVal" value="{{ $salesOrder->discount }}">
                            <div class="form-group">
                                <label>Pelanggan</label>
                                <select name="customer_id" class="form-control">
                                    <option value="">Pelanggan Umum (walk-in)</option>
                                    @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" {{ $salesOrder->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tambah sparepart</label>
                                <button type="button" class="search-btn" onclick="openPartSearch()">
                                    <span style="font-size:14px;color:var(--text-secondary);">Cari kode atau nama sparepart...</span>
                                    <i class="fas fa-search" style="font-size:16px;color:var(--text-muted);"></i>
                                </button>
                            </div>
                            <p style="font-size:13px;font-weight:500;color:var(--text-secondary);margin:0 0 8px;">Keranjang (<span id="cartCount">{{ $salesOrder->details->count() }}</span> item)</p>
                            <div id="cartItems" style="display:flex;flex-direction:column;gap:10px;margin-bottom:1rem;"></div>
                        </form>
                    </div>
                </div>
                <div class="pos-checkout">
                    <div class="card-form">
                        <p style="font-size:13px;font-weight:500;color:#475569;margin:0 0 12px;">Ringkasan</p>
                        <div class="summary" style="margin-bottom:1rem;">
                            <div class="row"><p>Subtotal</p><p id="subtotalDisplay">Rp {{ number_format($salesOrder->total_amount, 0) }}</p></div>
                            <div class="row"><p>Diskon</p><p id="discountDisplay">Rp {{ number_format($salesOrder->discount, 0) }}</p></div>
                            <div class="row total"><p style="font-weight:500;">Total</p><p id="totalDisplay" style="font-weight:600;">Rp {{ number_format($salesOrder->total_amount - $salesOrder->discount, 0) }}</p></div>
                        </div>
                        <div class="form-group">
                            <label>Diskon (Rp)</label>
                            <input type="number" id="discountInput" class="form-control" value="{{ $salesOrder->discount }}" min="0" step="100" oninput="updateTotals()" form="salesForm">
                        </div>
                        <hr style="margin:16px 0;border:none;border-top:1px solid #e2e8f0;">
                        <p style="font-size:13px;font-weight:500;color:#475569;margin:0 0 8px;">Pembayaran</p>
                        <p style="font-size:12px;color:var(--text-secondary);margin:0 0 12px;">Total tagihan: <strong>Rp <span id="totalForPayment">{{ number_format($salesOrder->total_amount - $salesOrder->discount, 0) }}</span></strong></p>
                        <input type="hidden" name="payment_method_id" id="pmId" value="" form="salesForm">
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-bottom:12px;">
                            <button type="button" id="tab-cash" onclick="selectMethod('cash')" style="padding:8px 4px;text-align:center;background:var(--fill-primary);color:var(--on-primary);border:none;border-radius:var(--radius);font-size:10px;font-weight:500;cursor:pointer;">
                                <i class="fas fa-money-bill-wave" style="font-size:14px;display:block;margin:0 auto 2px;"></i> Tunai
                            </button>
                            <button type="button" id="tab-card" onclick="selectMethod('card')" style="padding:8px 4px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:10px;background:#fff;color:#333;cursor:pointer;">
                                <i class="fas fa-credit-card" style="font-size:14px;display:block;margin:0 auto 2px;"></i> Kartu
                            </button>
                            <button type="button" id="tab-qris" onclick="selectMethod('qris')" style="padding:8px 4px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:10px;background:#fff;color:#333;cursor:pointer;">
                                <i class="fas fa-qrcode" style="font-size:14px;display:block;margin:0 auto 2px;"></i> QRIS
                            </button>
                            <button type="button" id="tab-transfer" onclick="selectMethod('transfer')" style="padding:8px 4px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:10px;background:#fff;color:#333;cursor:pointer;">
                                <i class="fas fa-university" style="font-size:14px;display:block;margin:0 auto 2px;"></i> Transfer
                            </button>
                        </div>
                        <div id="form-cash" style="display:flex;flex-direction:column;gap:8px;">
                            <input type="number" id="cashReceived" oninput="calcChange()" value="0" min="0" class="form-control" placeholder="Uang diterima" form="salesForm">
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <button type="button" onclick="setCash(0)" style="padding:4px 10px;font-size:10px;border-radius:999px;border:1px solid #ddd;background:#fff;cursor:pointer;">Uang pas</button>
                            </div>
                            <div style="background:var(--surface-2);border-radius:var(--radius);padding:8px 12px;display:flex;align-items:center;justify-content:space-between;">
                                <p style="font-size:12px;color:var(--text-secondary);margin:0;">Kembalian</p>
                                <p id="changeAmount" style="font-size:16px;font-weight:600;margin:0;">Rp 0</p>
                            </div>
                            <input type="hidden" name="amount_received" id="amountReceived" value="0" form="salesForm">
                            <input type="hidden" name="change_amount" id="changeAmountHidden" value="0" form="salesForm">
                        </div>
                        <div id="form-card" style="display:none;flex-direction:column;gap:8px;">
                            <div style="display:flex;gap:6px;">
                                <button type="button" id="card-debit" onclick="selectCardType('debit')" style="flex:1;padding:8px;text-align:center;background:var(--fill-primary);color:var(--on-primary);border:none;border-radius:var(--radius);font-size:12px;cursor:pointer;">Debit</button>
                                <button type="button" id="card-credit" onclick="selectCardType('credit')" style="flex:1;padding:8px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:12px;background:#fff;color:#333;cursor:pointer;">Kredit</button>
                            </div>
                            <input type="hidden" name="card_type" id="cardType" value="debit" form="salesForm">
                            <select name="issuing_bank" class="form-control" form="salesForm"><option value="">Bank penerbit</option><option>BCA</option><option>Mandiri</option><option>BNI</option><option>BRI</option></select>
                            <input type="text" name="card_last4" maxlength="4" class="form-control" placeholder="4 digit kartu" form="salesForm">
                            <input type="text" name="reference_number" class="form-control" placeholder="Kode approval EDC" form="salesForm">
                        </div>
                        <div id="form-qris" style="display:none;flex-direction:column;gap:8px;align-items:center;">
                            <p style="font-size:12px;color:var(--text-secondary);margin:0;">Scan QR untuk bayar <strong>Rp <span id="qrisAmount">{{ number_format($salesOrder->total_amount - $salesOrder->discount, 0) }}</span></strong></p>
                            <input type="text" name="reference_number" class="form-control" placeholder="No. referensi transaksi" form="salesForm">
                        </div>
                        <div id="form-transfer" style="display:none;flex-direction:column;gap:8px;">
                            <div style="background:var(--surface-2);border-radius:var(--radius);padding:8px 12px;font-size:12px;">Transfer ke BCA 1234567890 a.n. Bengkel</div>
                            <select name="issuing_bank" class="form-control" form="salesForm"><option value="">Bank asal</option><option>BCA</option><option>Mandiri</option><option>BNI</option><option>BRI</option></select>
                            <input type="text" name="reference_number" class="form-control" placeholder="No. referensi" form="salesForm">
                        </div>

                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <button type="button" class="btn" style="flex:1;justify-content:center;background:#e2e8f0;color:#475569;" onclick="addFormData(); document.getElementById('pmId').value=''; document.getElementById('salesForm').submit();"><i class="fas fa-save"></i> Simpan (Pending)</button>
                            <button type="button" class="btn btn-primary" style="flex:1;justify-content:center;" onclick="confirmSimpanBayar()"><i class="fas fa-credit-card"></i> Simpan & Bayar</button>
                        </div>
                        <a href="{{ route('sales-orders.show', $salesOrder) }}" class="btn" style="width:100%;justify-content:center;margin-top:8px;background:#e2e8f0;color:#475569;text-decoration:none;">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

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

<script>
let cart = [], partSearchData = [];

@php $savedItemsJs = $savedItems->toJson(); @endphp
const savedItems = {!! $savedItemsJs !!};

function openPartSearch() {
    document.getElementById('partSearchModal').classList.add('active');
    document.getElementById('partSearchInput').value = '';
    searchParts();
}
function closePartSearch() { document.getElementById('partSearchModal').classList.remove('active'); }

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
                return '<div class="search-item" data-idx="' + idx + '">' +
                    '<div style="flex:1;"><p style="font-size:14px;font-weight:500;margin:0;color:#1a1a2e;">' + r.name + '</p>' +
                    '<p style="font-size:12px;color:var(--text-secondary);margin:0;">' + (r.code || '') + ' · ' + (r.category || '') + ' · ' + (r.unit || '') + ' · Stok: ' + stock + '</p></div>' +
                    '<div style="text-align:right;flex-shrink:0;"><p style="font-size:11px;color:var(--text-secondary);margin:0;">Harga</p><p style="font-size:16px;font-weight:500;margin:0;color:' + (sellPrice > 0 ? '#1a1a2e' : '#94a3b8') + ';">' + (sellPrice > 0 ? 'Rp ' + Number(sellPrice).toLocaleString('id-ID') : 'N/A') + '</p></div></div>';
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
    addToCart(r);
    closePartSearch();
});

function addToCart(r) {
    const existing = cart.find(c => c.part_id === r.id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ part_id: r.id, name: r.name, code: r.code || '', stock: r.stock_qty || 0, sell_price: r.sell_price || 0, qty: r.qty || 1 });
    }
    renderCart();
}

function removeFromCart(idx) { cart.splice(idx, 1); renderCart(); }
function changeQty(idx, delta) {
    const item = cart[idx];
    const newQty = item.qty + delta;
    if (newQty < 1 || newQty > item.stock) return;
    item.qty = newQty;
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    document.getElementById('cartCount').textContent = cart.length;
    if (!cart.length) { container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">Belum ada item</div>'; updateTotals(); return; }
    container.innerHTML = cart.map((item, idx) => {
        const subtotal = item.qty * item.sell_price;
        return '<div class="cart-item">' +
            '<div class="top"><div><p class="name">' + item.name + '</p><p class="meta">' + item.code + ' · stok ' + item.stock + '</p></div>' +
            '<button onclick="removeFromCart(' + idx + ')" style="width:28px;height:28px;border-radius:50%;border:none;background:transparent;cursor:pointer;font-size:16px;color:#94a3b8;">&times;</button></div>' +
            '<div class="qty-row"><div class="qty-controls">' +
            '<button onclick="changeQty(' + idx + ', -1)">&minus;</button><span>' + item.qty + '</span><button onclick="changeQty(' + idx + ', +1)">+</button></div>' +
            '<p class="subtotal">Rp ' + subtotal.toLocaleString('id-ID') + '</p></div></div>';
    }).join('');
    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, c) => sum + c.qty * c.sell_price, 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = Math.max(0, subtotal - discount);
    document.getElementById('subtotalDisplay').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('discountDisplay').textContent = 'Rp ' + discount.toLocaleString('id-ID');
    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalAmount').value = total;
    document.getElementById('discountVal').value = discount;
    document.getElementById('totalForPayment').textContent = total.toLocaleString('id-ID');
    document.getElementById('qrisAmount').textContent = total.toLocaleString('id-ID');
}

const pmMap = {
    cash: {{ $paymentMethods->where('name', 'Tunai')->first()->id ?? 1 }},
    card: {{ $paymentMethods->whereIn('name', ['Kartu Debit', 'Kartu Kredit'])->first()->id ?? 4 }},
    qris: {{ $paymentMethods->where('name', 'QRIS')->first()->id ?? 3 }},
    transfer: {{ $paymentMethods->where('name', 'Transfer Bank')->first()->id ?? 2 }},
};

function selectMethod(method) { ['cash','card','qris','transfer'].forEach(t => {
    const tab = document.getElementById('tab-' + t);
    const form = document.getElementById('form-' + t);
    if (tab) { tab.style.background = t === method ? 'var(--fill-primary)' : ''; tab.style.color = t === method ? 'var(--on-primary)' : ''; tab.style.border = t === method ? 'none' : '1px solid #ddd'; }
    if (form) form.style.display = t === method ? 'flex' : 'none';
}); document.getElementById('pmId').value = pmMap[method] || ''; calcChange(); }

function setCash(val) { document.getElementById('cashReceived').value = val; calcChange(); }

function calcChange() {
    const total = parseFloat(document.getElementById('totalAmount').value) || 0;
    const received = parseFloat(document.getElementById('cashReceived').value) || 0;
    const change = Math.max(0, received - total);
    document.getElementById('changeAmount').textContent = 'Rp ' + change.toLocaleString('id-ID');
    document.getElementById('amountReceived').value = received;
    document.getElementById('changeAmountHidden').value = change;
}

function addFormData() {
    var f = document.getElementById('salesForm');
    function add(n, v) { var i = document.createElement('input'); i.type = 'hidden'; i.name = n; i.value = v; f.appendChild(i); }
    add('items', JSON.stringify(cart.map(function(c) { return { part_id: c.part_id, qty: c.qty, sell_price: c.sell_price }; })));
    add('payment_method_id', document.getElementById('pmId').value);
    add('amount_received', document.getElementById('cashReceived').value);
    add('change_amount', document.getElementById('changeAmountHidden').value);
}

function confirmSimpanBayar() {
    const cashForm = document.getElementById('form-cash');
    const isCashVisible = cashForm && cashForm.style.display !== 'none';
    if (isCashVisible) {
        const received = parseFloat(document.getElementById('cashReceived').value) || 0;
        if (received <= 0) {
            alert('Untuk pembayaran tunai, isi jumlah uang yang diterima.');
            return;
        }
    }
    if (!document.getElementById('pmId').value) {
        alert('Pilih metode pembayaran terlebih dahulu.');
        return;
    }
    addFormData();
    showConfirmModal('Yakin ingin memproses pembayaran? Stok akan berkurang sesuai item yang dijual.', function() {
        document.getElementById('salesForm').submit();
    });
}

function selectCardType(type) {
    document.getElementById('cardType').value = type;
    document.getElementById('card-debit').style.background = type === 'debit' ? 'var(--fill-primary)' : '';
    document.getElementById('card-debit').style.color = type === 'debit' ? 'var(--on-primary)' : '';
    document.getElementById('card-credit').style.background = type === 'credit' ? 'var(--fill-primary)' : '';
    document.getElementById('card-credit').style.color = type === 'credit' ? 'var(--on-primary)' : '';
}

// Data form ditambahkan lewat addFormData() sebelum submit

document.getElementById('partSearchModal').addEventListener('click', function(e) { if (e.target === this) closePartSearch(); });

document.addEventListener('DOMContentLoaded', function() {
    savedItems.forEach(function(item) { addToCart(item); });
    selectMethod('cash');
});
</script>
<style>
.search-item { display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:12px;transition:all 0.2s; }
.search-item:hover { background:var(--surface-2); }
</style>
@endsection

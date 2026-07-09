@extends('layouts.app')

@section('title', 'Jual Sparepart')

@push('styles')
<style>
    :root { --radius:12px; --surface-1:#fff; --surface-2:#f0f2f5; --text-secondary:#64748b; --text-muted:#94a3b8; --fill-primary:#0f3460; --on-primary:#fff; --border:#e2e8f0; }
    .form-container { max-width:420px; margin:0 auto; }
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
    .cart-item .fifo-info { font-size:11px; color:var(--text-muted); margin:6px 0 0; }
    .summary { display:flex; flex-direction:column; gap:6px; padding:12px 14px; background:var(--surface-2); border-radius:12px; }
    .summary .row { display:flex; align-items:center; justify-content:space-between; }
    .summary .row p { margin:0; font-size:13px; color:var(--text-secondary); }
    .summary .total p { font-size:14px; font-weight:500; }
    .summary .grand p { font-size:16px; font-weight:600; }
    .alert-success { padding:12px; border-radius:10px; margin-bottom:16px; font-size:13px; background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
    @media (min-width:769px) {
        .pos-layout { display:flex; gap:20px; align-items:flex-start; max-width:1000px; margin:0 auto; }
        .pos-cart { flex:1.4; }
        .pos-checkout { width:340px; position:sticky; top:20px; }
        .form-container { max-width:100%; }
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
                            <p class="section-title">Jual Sparepart</p>
                            <p class="section-sub">Penjualan tanpa servis</p>
                        </div>

                        <form method="POST" action="{{ route('sales-orders.store') }}" id="salesForm">
                            @csrf
                            <input type="hidden" name="total_amount" id="totalAmount" value="0">
                            <input type="hidden" name="discount" id="discountVal" value="0">
                            <input type="hidden" name="payment_method_id" id="pmId" value="">

                            <div class="form-group">
                                <label>Pelanggan</label>
                                <select name="customer_id" class="form-control">
                                    <option value="">Pelanggan Umum (walk-in)</option>
                                    @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
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

                            <p style="font-size:13px;font-weight:500;color:var(--text-secondary);margin:0 0 8px;">Keranjang (<span id="cartCount">0</span> item)</p>
                            <div id="cartItems" style="display:flex;flex-direction:column;gap:10px;margin-bottom:1rem;"></div>
                        </form>
                    </div>
                </div>

                <div class="pos-checkout">
                    <div class="card-form">
                        <p style="font-size:13px;font-weight:500;color:#475569;margin:0 0 12px;">Ringkasan</p>

                        <div class="summary" style="margin-bottom:1rem;">
                            <div class="row"><p>Subtotal</p><p id="subtotalDisplay">Rp 0</p></div>
                            <div class="row"><p>Diskon</p><p id="discountDisplay">Rp 0</p></div>
                            <div class="row total" style="padding-top:6px;border-top:0.5px solid var(--border);"><p style="font-weight:500;">Total</p><p id="totalDisplay" style="font-weight:600;">Rp 0</p></div>
                        </div>

                        <div class="form-group">
                            <label>Diskon (Rp)</label>
                            <input type="number" id="discountInput" class="form-control" value="0" min="0" step="100" oninput="updateTotals()" form="salesForm">
                        </div>

                        <p style="font-size:13px;font-weight:500;color:#475569;margin:16px 0 8px;">Pembayaran</p>
                        <p style="font-size:12px;color:var(--text-secondary);margin:0 0 12px;">Total tagihan: <strong>Rp <span id="totalForPayment">0</span></strong></p>
                        <input type="hidden" name="payment_method_id" id="pmId" value="" form="salesForm">
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-bottom:1rem;">
                            <button type="button" id="tab-cash" onclick="selectMethod('cash')" style="padding:10px 4px;text-align:center;background:var(--fill-primary);color:var(--on-primary);border:none;border-radius:var(--radius);font-size:11px;font-weight:500;">
                                <i class="fas fa-money-bill-wave" style="font-size:18px;display:block;margin:0 auto 4px;"></i> Tunai
                            </button>
                            <button type="button" id="tab-card" onclick="selectMethod('card')" style="padding:10px 4px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:11px;background:#fff;color:#333;">
                                <i class="fas fa-credit-card" style="font-size:18px;display:block;margin:0 auto 4px;"></i> Kartu
                            </button>
                            <button type="button" id="tab-qris" onclick="selectMethod('qris')" style="padding:10px 4px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:11px;background:#fff;color:#333;">
                                <i class="fas fa-qrcode" style="font-size:18px;display:block;margin:0 auto 4px;"></i> QRIS
                            </button>
                            <button type="button" id="tab-transfer" onclick="selectMethod('transfer')" style="padding:10px 4px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:11px;background:#fff;color:#333;">
                                <i class="fas fa-university" style="font-size:18px;display:block;margin:0 auto 4px;"></i> Transfer
                            </button>
                        </div>

                        <div id="form-cash" style="display:flex;flex-direction:column;gap:12px;">
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Uang diterima</label>
                                <input type="number" id="cashReceived" oninput="calcChange()" value="0" min="0" class="form-control" form="salesForm">
                            </div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <button type="button" onclick="setCash(0)" style="padding:6px 12px;font-size:11px;border-radius:999px;border:1px solid #ddd;background:#fff;cursor:pointer;">Uang pas</button>
                            </div>
                            <div style="background:var(--surface-2);border-radius:var(--radius);padding:0.75rem 1rem;display:flex;align-items:center;justify-content:space-between;">
                                <p style="font-size:13px;color:var(--text-secondary);margin:0;">Kembalian</p>
                                <p id="changeAmount" style="font-size:18px;font-weight:600;margin:0;color:#1a1a2e;">Rp 0</p>
                            </div>
                            <input type="hidden" name="amount_received" id="amountReceived" value="0" form="salesForm">
                            <input type="hidden" name="change_amount" id="changeAmountHidden" value="0" form="salesForm">
                        </div>

                        <div id="form-card" style="display:none;flex-direction:column;gap:12px;">
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Jenis kartu</label>
                                <div style="display:flex;gap:8px;">
                                    <button type="button" id="card-debit" onclick="selectCardType('debit')" style="flex:1;padding:10px;text-align:center;background:var(--fill-primary);color:var(--on-primary);border:none;border-radius:var(--radius);font-size:13px;font-weight:500;cursor:pointer;">Debit</button>
                                    <button type="button" id="card-credit" onclick="selectCardType('credit')" style="flex:1;padding:10px;text-align:center;border:1px solid #ddd;border-radius:var(--radius);font-size:13px;background:#fff;color:#333;cursor:pointer;">Kredit</button>
                                </div>
                                <input type="hidden" name="card_type" id="cardType" value="debit" form="salesForm">
                            </div>
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Bank penerbit</label>
                                <select name="issuing_bank" class="form-control" form="salesForm">
                                    <option value="">-- Pilih --</option>
                                    <option>BCA</option><option>Mandiri</option><option>BNI</option><option>BRI</option><option>Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">4 digit terakhir kartu</label>
                                <input type="text" name="card_last4" maxlength="4" placeholder="1234" class="form-control" form="salesForm">
                            </div>
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Kode approval EDC</label>
                                <input type="text" name="reference_number" placeholder="Dari struk mesin EDC" class="form-control" form="salesForm">
                            </div>
                            <p style="font-size:11px;color:var(--text-muted);margin:0;">Nomor kartu lengkap tidak disimpan — cukup 4 digit terakhir untuk referensi.</p>
                        </div>

                        <div id="form-qris" style="display:none;flex-direction:column;gap:12px;align-items:center;text-align:center;">
                            <div style="width:160px;height:160px;background:var(--surface-2);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-qrcode" style="font-size:64px;color:var(--text-muted);"></i>
                            </div>
                            <p style="font-size:13px;color:var(--text-secondary);margin:0;">Pelanggan scan QR untuk bayar <strong>Rp <span id="qrisAmount">0</span></strong></p>
                            <div style="width:100%;text-align:left;">
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">No. referensi transaksi</label>
                                <input type="text" name="reference_number" placeholder="Diisi setelah pembayaran masuk" class="form-control" form="salesForm">
                            </div>
                        </div>

                        <div id="form-transfer" style="display:none;flex-direction:column;gap:12px;">
                            <div class="form-group" style="margin-bottom:0;">
                                <div style="background:var(--surface-2);border-radius:var(--radius);padding:0.75rem 1rem;">
                                    <p style="font-size:12px;color:var(--text-secondary);margin:0 0 2px;">Transfer ke</p>
                                    <p style="font-size:14px;font-weight:500;margin:0;">BCA 1234567890 a.n. Bengkel Jaya Motor</p>
                                </div>
                            </div>
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Bank asal transfer</label>
                                <select name="issuing_bank" class="form-control" form="salesForm">
                                    <option value="">-- Pilih --</option>
                                    <option>BCA</option><option>Mandiri</option><option>BNI</option><option>BRI</option><option>Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">No. referensi transfer</label>
                                <input type="text" name="reference_number" placeholder="Dari bukti transfer" class="form-control" form="salesForm">
                            </div>
                            <div>
                                <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px;">Bukti transfer</label>
                                <div style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;border-radius:var(--radius);border:1px dashed #ddd;cursor:pointer;" onclick="document.getElementById('proofUpload').click()">
                                    <i class="fas fa-upload" style="font-size:16px;color:var(--text-muted);"></i>
                                    <span style="font-size:13px;color:var(--text-secondary);">Upload foto bukti</span>
                                </div>
                                <input type="file" id="proofUpload" name="proof_url" accept="image/*" style="display:none;" form="salesForm">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="payment_status" class="form-control" form="salesForm">
                                <option value="paid">Lunas</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" form="salesForm"><i class="fas fa-shopping-cart"></i> Proses & cetak struk</button>
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
            <input type="text" id="partSearchInput" placeholder="Cari kode atau nama sparepart..." style="width:100%;padding:10px 12px 10px 36px;border:1px solid #ddd;border-radius:var(--radius);font-size:14px;outline:none;" onkeyup="searchParts()">
        </div>
        <div style="font-size:12px;color:var(--text-secondary);margin-bottom:8px;flex-shrink:0;" id="partResultCount"></div>
        <div id="partSearchResults" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
    </div>
</div>

<script>
let cart = [], partSearchData = [];
const pmMap = {
    cash: {{ $paymentMethods->where('name', 'Tunai')->first()->id ?? 1 }},
    card: {{ $paymentMethods->whereIn('name', ['Kartu Debit', 'Kartu Kredit'])->first()->id ?? 4 }},
    qris: {{ $paymentMethods->where('name', 'QRIS')->first()->id ?? 3 }},
    transfer: {{ $paymentMethods->where('name', 'Transfer Bank')->first()->id ?? 2 }},
};

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
                return '<div class="search-item" data-idx="' + idx + '" style="cursor:pointer;">' +
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
        cart.push({ part_id: r.id, name: r.name, code: r.code || '', stock: r.stock_qty || 0, sell_price: r.sell_price || 0, qty: 1 });
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
            '<p class="subtotal">Rp ' + subtotal.toLocaleString('id-ID') + '</p></div>' +
            '<p class="fifo-info">FIFO: dialokasikan dari batch tertua</p></div>';
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
    const cashBtn = document.querySelector('#form-cash button');
    if (cashBtn) { cashBtn.onclick = function() { setCash(total); }; cashBtn.textContent = 'Rp ' + total.toLocaleString('id-ID') + ' (uang pas)'; }
}

function selectMethod(method) {
    const tabs = ['cash','card','qris','transfer'];
    document.getElementById('pmId').value = pmMap[method] || '';
    tabs.forEach(t => {
        const tab = document.getElementById('tab-' + t);
        const form = document.getElementById('form-' + t);
        if (tab) { tab.style.background = t === method ? 'var(--fill-primary)' : ''; tab.style.color = t === method ? 'var(--on-primary)' : ''; tab.style.border = t === method ? 'none' : '1px solid #ddd'; }
        if (form) form.style.display = t === method ? 'flex' : 'none';
    });
    calcChange();
}

function setCash(val) {
    document.getElementById('cashReceived').value = val;
    calcChange();
}

function calcChange() {
    const total = parseFloat(document.getElementById('totalAmount').value) || 0;
    const received = parseFloat(document.getElementById('cashReceived').value) || 0;
    const change = Math.max(0, received - total);
    document.getElementById('changeAmount').textContent = 'Rp ' + change.toLocaleString('id-ID');
    document.getElementById('amountReceived').value = received;
    document.getElementById('changeAmountHidden').value = change;
}

function selectCardType(type) {
    document.getElementById('cardType').value = type;
    document.getElementById('card-debit').style.background = type === 'debit' ? 'var(--fill-primary)' : '';
    document.getElementById('card-debit').style.color = type === 'debit' ? 'var(--on-primary)' : '';
    document.getElementById('card-credit').style.background = type === 'credit' ? 'var(--fill-primary)' : '';
    document.getElementById('card-credit').style.color = type === 'credit' ? 'var(--on-primary)' : '';
}

document.getElementById('salesForm').addEventListener('submit', function(e) {
    const itemsInput = document.createElement('input');
    itemsInput.type = 'hidden';
    itemsInput.name = 'items';
    itemsInput.value = JSON.stringify(cart.map(c => ({ part_id: c.part_id, qty: c.qty, sell_price: c.sell_price })));
    this.appendChild(itemsInput);
});

document.getElementById('partSearchModal').addEventListener('click', function(e) { if (e.target === this) closePartSearch(); });
</script>
<style>
.search-item { display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:12px;transition:all 0.2s; }
.search-item:hover { background:var(--surface-2); }
</style>
@endsection

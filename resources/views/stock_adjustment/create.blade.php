@extends('layouts.app')

@section('title', 'Stock Adjustment')

@push('styles')
<style>
    :root { --radius: 12px; --surface-1: #fff; --surface-2: #f0f2f5; --text-secondary: #64748b; --text-muted: #94a3b8; --fill-primary: #0f3460; --on-primary: #fff; }
    .form-container { max-width: 420px; margin: 0 auto; }
    .card-form { background: var(--surface-1); border-radius: 20px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .section-title { font-weight: 500; font-size: 16px; margin: 0 0 2px; color: #1a1a2e; }
    .section-sub { font-size: 13px; color: var(--text-secondary); margin: 0 0 20px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px; color: #333; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: var(--radius); font-size: 14px; outline: none; background: var(--surface-1); }
    .form-control:focus { border-color: var(--fill-primary); }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: var(--radius); font-size: 14px; font-weight: 500; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: var(--fill-primary); color: var(--on-primary); }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-outline { background: transparent; border: 1px solid #ddd; color: #333; }
    .btn-outline:hover { background: var(--surface-2); }
    .part-card { background: var(--surface-2); border-radius: var(--radius); padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; }
    .part-card .info p { margin: 0; }
    .part-card .info .name { font-size: 13px; font-weight: 500; color: #1a1a2e; }
    .part-card .info .meta { font-size: 12px; color: var(--text-secondary); }
    .part-card .stock-val { text-align: right; }
    .part-card .stock-val .label { font-size: 11px; color: var(--text-secondary); }
    .part-card .stock-val .num { font-size: 18px; font-weight: 500; color: #1a1a2e; }
    .type-toggle { display: flex; gap: 8px; }
    .type-toggle .btn { flex: 1; padding: 10px; text-align: center; font-size: 14px; font-weight: 500; }
    .type-toggle .btn.active { background: var(--fill-primary); color: var(--on-primary); }
    .search-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; text-align: left; border-radius: var(--radius); border: 1px solid #ddd; background: var(--surface-1); cursor: pointer; }
    .search-btn:hover { border-color: var(--fill-primary); }
    .result-stok { background: var(--surface-2); border-radius: var(--radius); padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; margin-top: 4px; }
    .result-stok p { margin: 0; font-size: 13px; color: var(--text-secondary); }
    .result-stok .val { font-size: 16px; font-weight: 500; color: #1a1a2e; }
    .alert-success { padding: 12px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="form-container">
            <div class="card-form">
                <div style="margin-bottom:1.25rem;">
                    <p class="section-title">Stock Adjustment</p>
                    <p class="section-sub">Koreksi manual stok sparepart</p>
                </div>

                <form method="POST" action="{{ route('stock-adjustments.store') }}" id="adjForm">
                    @csrf
                    <input type="hidden" name="part_id" id="partId" value="{{ old('part_id') }}">
                    <input type="hidden" name="adjustment_type" id="adjType" value="in">

                    <div class="form-group">
                        <label>Sparepart</label>
                        <div id="partDisplay">
                            <button type="button" class="search-btn" onclick="openSearch()">
                                <span style="font-size:14px;color:var(--text-secondary);" id="partPlaceholder">Cari kode atau nama sparepart...</span>
                                <i class="fas fa-search" style="font-size:16px;color:var(--text-muted);"></i>
                            </button>
                        </div>
                        <div id="selectedPart" style="display:none;"></div>
                    </div>

                    <div class="form-group">
                        <label>Jenis penyesuaian</label>
                        <div class="type-toggle">
                            <button type="button" class="btn active" id="toggleIn" onclick="setType('in')">+ Tambah stok</button>
                            <button type="button" class="btn" id="toggleOut" onclick="setType('out')">− Kurangi stok</button>
                        </div>
                    </div>

                    <div class="form-group" id="buyPriceGroup">
                        <label>Harga beli (per pcs)</label>
                        <input type="number" name="buy_price" class="form-control" value="{{ old('buy_price', 0) }}" min="0" step="100">
                    </div>

                    <div class="form-group">
                        <label>Jumlah (qty)</label>
                        <input type="number" name="qty" class="form-control" value="{{ old('qty', 1) }}" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Alasan</label>
                        <select name="reason" class="form-control" required>
                            <option value="saldo_awal" {{ old('reason') === 'saldo_awal' ? 'selected' : '' }}>Saldo awal</option>
                            <option value="koreksi_stock_opname" {{ old('reason') === 'koreksi_stock_opname' ? 'selected' : '' }}>Koreksi stock opname</option>
                            <option value="barang_rusak" {{ old('reason') === 'barang_rusak' ? 'selected' : '' }}>Barang rusak</option>
                            <option value="barang_hilang" {{ old('reason') === 'barang_hilang' ? 'selected' : '' }}>Barang hilang</option>
                            <option value="lainnya" {{ old('reason') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Opsional, misal: hasil stock opname bulan Juli">{{ old('notes') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Tanggal transaksi</label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="result-stok" id="resultStok">
                        <p>Stok setelah penyesuaian</p>
                        <p class="val" id="stokAfter">-</p>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;padding:12px;"><i class="fas fa-save"></i> Simpan penyesuaian</button>
                </form>
            </div>
        </div>
    </main>
</div>

<div class="modal-overlay" id="searchModal">
    <div class="modal-box" style="text-align:left;max-width:480px;height:80vh;display:flex;flex-direction:column;padding:16px 20px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-shrink:0;">
            <button type="button" onclick="closeSearch()" style="width:32px;height:32px;border-radius:50%;border:none;background:var(--surface-2);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">&larr;</button>
            <p style="font-weight:500;font-size:15px;margin:0;">Pilih sparepart</p>
        </div>

        <div style="position:relative;margin-bottom:12px;flex-shrink:0;">
            <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--text-muted);"></i>
            <input type="text" id="searchInput" placeholder="Cari kode atau nama sparepart..." style="width:100%;padding:10px 12px 10px 36px;border:1px solid #ddd;border-radius:var(--radius);font-size:14px;outline:none;" onkeyup="doSearch()">
        </div>

        <div id="categoryFilters" style="display:flex;gap:6px;margin-bottom:12px;overflow-x:auto;flex-shrink:0;padding-bottom:4px;"></div>

        <div style="font-size:12px;color:var(--text-secondary);margin-bottom:8px;flex-shrink:0;" id="resultCount"></div>

        <div id="searchResults" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
    </div>
</div>

<style>
.modal-box#searchModal .modal-box { max-width:480px; }
</style>

<script>
let currentStock = 0, searchPage = 1, searchCat = 0, searchData = [];

function openSearch() {
    document.getElementById('searchModal').classList.add('active');
    document.getElementById('searchInput').value = '';
    searchPage = 1; searchCat = 0;
    doSearch();
}

function closeSearch() {
    document.getElementById('searchModal').classList.remove('active');
}

function renderResults() {
    document.getElementById('resultCount').textContent = searchData.length + ' hasil ditemukan' + (searchPage > 1 ? ' (halaman ' + searchPage + ')' : '');
    const list = document.getElementById('searchResults');
    list.innerHTML = searchData.map((r, idx) => {
        const stock = r.stock_qty || 0;
        return '<div class="search-item" data-idx="' + idx + '">' +
            '<div style="flex:1;"><p style="font-size:14px;font-weight:500;margin:0;color:#1a1a2e;">' + r.name + '</p>' +
            '<p style="font-size:12px;color:var(--text-secondary);margin:0;">' + (r.code || '') + ' · ' + (r.category || '') + ' · ' + (r.unit || '') + '</p></div>' +
            '<div style="text-align:right;flex-shrink:0;"><p style="font-size:11px;color:var(--text-secondary);margin:0;">Stok</p><p style="font-size:16px;font-weight:500;margin:0;color:#1a1a2e;">' + stock + '</p></div></div>';
    }).join('');
    if (!searchData.length) list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);">Tidak ditemukan</div>';
}

function doSearch() {
    const q = document.getElementById('searchInput').value;
    const url = '{{ route("stock-adjustments.search") }}?q=' + encodeURIComponent(q) + '&page=1' + (searchCat ? '&category_id=' + searchCat : '');
    fetch(url).then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); }).then(data => {
        const cats = data.categories;
        searchData = data.results;
        document.getElementById('categoryFilters').innerHTML = '<button onclick="searchCat=0;doSearch()" class="' + (searchCat === 0 ? 'active' : '') + '">Semua kategori</button>' +
            cats.map(c => '<button onclick="searchCat=' + c.id + ';doSearch()" class="' + (searchCat == c.id ? 'active' : '') + '">' + c.name + '</button>').join('');
        searchPage = 1;
        renderResults();
    });
}

document.getElementById('searchResults').addEventListener('click', function(e) {
    const item = e.target.closest('.search-item');
    if (!item) return;
    const idx = parseInt(item.dataset.idx);
    const r = searchData[idx];
    if (!r) return;
    const stock = r.stock_qty || 0;
    document.getElementById('partId').value = r.id;
    document.getElementById('partPlaceholder').textContent = r.name + ' (' + (r.code || '') + ')';
    document.getElementById('selectedPart').innerHTML = '<div class="part-card"><div class="info"><p class="name">' + r.name + '</p><p class="meta">' + (r.code || '') + ' · ' + (r.category || '') + ' · ' + (r.unit || '') + '</p></div><div class="stock-val"><p class="label">Stok sistem saat ini</p><p class="num">' + stock + '</p></div></div>';
    document.getElementById('selectedPart').style.display = 'block';
    document.getElementById('partDisplay').style.display = 'none';
    currentStock = stock;
    updateStokAfter();
    closeSearch();
});

function setType(type) {
    document.getElementById('adjType').value = type;
    document.getElementById('toggleIn').classList.toggle('active', type === 'in');
    document.getElementById('toggleOut').classList.toggle('active', type === 'out');
    document.getElementById('buyPriceGroup').style.display = type === 'in' ? 'block' : 'none';
    updateStokAfter();
}

function updateStokAfter() {
    const qty = parseFloat(document.querySelector('input[name="qty"]').value) || 0;
    const type = document.getElementById('adjType').value;
    const after = type === 'in' ? currentStock + qty : currentStock - qty;
    document.getElementById('stokAfter').textContent = after;
}

document.querySelector('input[name="qty"]').addEventListener('input', updateStokAfter);
document.getElementById('searchModal').addEventListener('click', function(e) { if (e.target === this) closeSearch(); });
</script>
<style>
.search-item { display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:12px;transition:all 0.2s; }
.search-item:hover { background:var(--surface-2); }
#categoryFilters button { padding:6px 14px;font-size:13px;white-space:nowrap;border-radius:999px;border:none;cursor:pointer;background:var(--surface-2);color:#475569; }
#categoryFilters button.active { background:var(--fill-primary);color:var(--on-primary); }
</style>
@endsection

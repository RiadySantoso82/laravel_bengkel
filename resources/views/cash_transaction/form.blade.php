@extends('layouts.app')

@section('title', 'Transaksi Kas Baru')

@push('styles')
<style>
    .page-header h1 { font-size:24px;color:#1a1a2e;margin-bottom:24px; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding:30px;max-width:600px; }
    .form-group { margin-bottom:20px; }
    .form-group label { display:block;margin-bottom:6px;font-weight:600;color:#333;font-size:14px; }
    .form-control { width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-size:14px;outline:none; }
    .form-control:focus { border-color:#0f3460; }
    select.form-control { background:#fff; }
    textarea.form-control { resize:vertical;min-height:80px; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-secondary { background:#e2e8f0;color:#475569; }
    .form-actions { display:flex;gap:10px;margin-top:24px; }
    .type-toggle { display:flex;gap:8px; }
    .type-toggle .btn { flex:1;padding:10px;text-align:center;font-size:14px;font-weight:500;border:1px solid #ddd;background:#fff;color:#333; }
    .type-toggle .btn.active { background:#0f3460;color:#fff;border-color:#0f3460; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-money-check-alt"></i> Transaksi Kas Baru</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('cash-transactions.store') }}">
                @csrf
                <div class="form-group">
                    <label>Tipe</label>
                    <div class="type-toggle">
                        <button type="button" class="btn active" id="toggleIn" onclick="setType('in')"><i class="fas fa-arrow-down" style="color:#059669;"></i> Pemasukan</button>
                        <button type="button" class="btn" id="toggleOut" onclick="setType('out')"><i class="fas fa-arrow-up" style="color:#dc2626;"></i> Pengeluaran</button>
                    </div>
                    <input type="hidden" name="type" id="typeVal" value="in">
                </div>

                <div class="form-group">
                    <label for="cash_category_id">Kategori</label>
                    <select id="cash_category_id" name="cash_category_id" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($categories as $c)
                        <option value="{{ $c->id }}" data-type="{{ $c->type }}">{{ $c->name }} ({{ $c->type === 'in' ? 'Pemasukan' : 'Pengeluaran' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="amount">Jumlah (Rp)</label>
                    <input type="number" id="amount" name="amount" class="form-control cleave-number" value="{{ old('amount') }}" min="1" required>
                </div>

                <div class="form-group">
                    <label for="description">Keterangan</label>
                    <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="transaction_date">Tanggal Transaksi</label>
                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('cash-transactions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div></div>
    </main>
</div>
<script>
function setType(type) {
    document.getElementById('typeVal').value = type;
    document.getElementById('toggleIn').classList.toggle('active', type === 'in');
    document.getElementById('toggleOut').classList.toggle('active', type === 'out');
}
</script>
@endsection

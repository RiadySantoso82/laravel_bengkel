@extends('layouts.app')

@section('title', 'Kas')

@push('styles')
<style>
    .page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px; }
    .page-header h1 { font-size:24px;color:#1a1a2e;margin:0; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-primary:hover { background:#1a1a2e; }
    .btn-success { background:#059669;color:#fff; }
    .btn-danger { background:#dc2626;color:#fff; }
    .btn-sm { padding:5px 10px;font-size:11px; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:16px; }
    .card-body { padding:20px; }
    .filter-bar { display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px;padding:16px 20px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .filter-bar label { display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px; }
    .filter-bar select,.filter-bar input { padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none; }
    table { width:100%;border-collapse:collapse; }
    th,td { text-align:left;padding:10px 14px;border-bottom:1px solid #f0f0f0;font-size:13px; }
    th { background:#f8fafc;font-weight:600;color:#475569;font-size:12px; }
    tr:hover td { background:#f8fafc; }
    .badge { display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-in { background:#d1fae5;color:#065f46; }
    .badge-out { background:#fde8e8;color:#991b1b; }
    .summary { display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap; }
    .summary-item { background:#fff;border-radius:10px;padding:16px 20px;flex:1;min-width:140px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .summary-item .num { font-size:22px;font-weight:700; }
    .summary-item .label { font-size:12px;color:#888;margin-top:4px; }
    .text-right { text-align:right; }
    .pagination { display:flex;gap:4px;justify-content:center;margin-top:16px; }
    .pagination a,.pagination span { padding:6px 12px;border-radius:6px;font-size:13px;text-decoration:none;color:#475569;background:#fff;border:1px solid #e2e8f0; }
    .pagination .active { background:#0f3460;color:#fff;border-color:#0f3460; }
    .alert { padding:12px;border-radius:10px;margin-bottom:16px;font-size:13px; }
    .alert-success { background:#d1fae5;color:#065f46;border:1px solid #a7f3d0; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-money-check-alt"></i> Kas</h1>
            <a href="{{ route('cash-transactions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Transaksi Baru</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <div class="summary">
            <div class="summary-item">
                <div class="num" style="color:#059669;">Rp {{ number_format($totalIn, 0) }}</div>
                <div class="label">Total Pemasukan</div>
            </div>
            <div class="summary-item">
                <div class="num" style="color:#dc2626;">Rp {{ number_format($totalOut, 0) }}</div>
                <div class="label">Total Pengeluaran</div>
            </div>
            <div class="summary-item">
                <div class="num" style="color:{{ $totalIn - $totalOut >= 0 ? '#059669' : '#dc2626' }};">Rp {{ number_format($totalIn - $totalOut, 0) }}</div>
                <div class="label">Saldo Kas</div>
            </div>
        </div>

        <form class="filter-bar" method="GET">
            <div><label>Dari</label><input type="date" name="date_from" value="{{ $from }}"></div>
            <div><label>Sampai</label><input type="date" name="date_to" value="{{ $to }}"></div>
            <div><label>Tipe</label>
                <select name="type">
                    <option value="">Semua</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div><label>Kategori</label>
                <select name="cash_category_id">
                    <option value="">Semua</option>
                    @foreach ($categories as $c)
                    <option value="{{ $c->id }}" {{ request('cash_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button class="btn btn-primary" style="padding:8px 16px;"><i class="fas fa-search"></i></button>
                <a href="{{ route('cash-transactions.index') }}" class="btn" style="padding:8px 16px;background:#e2e8f0;color:#475569;text-decoration:none;"><i class="fas fa-undo"></i></a>
            </div>
        </form>

        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table>
                    <thead><tr><th>Tanggal</th><th>Kategori</th><th>Tipe</th><th class="text-right">Jumlah</th><th>Keterangan</th><th>Oleh</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($data as $t)
                        <tr>
                            <td style="white-space:nowrap;">{{ $t->transaction_date ? date('d/m/Y', strtotime($t->transaction_date)) : '-' }}</td>
                            <td>{{ $t->category->name ?? '-' }}</td>
                            <td><span class="badge badge-{{ $t->type }}">{{ $t->type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                            <td class="text-right">{{ number_format($t->amount, 0) }}</td>
                            <td>{{ Str::limit($t->description, 40) ?? '-' }}</td>
                            <td style="font-size:12px;color:#888;">{{ $t->user->name ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('cash-transactions.destroy', $t) }}" onsubmit="confirmForm(this, 'Hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada transaksi kas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($data->hasPages())
        <div class="pagination">{{ $data->links() }}</div>
        @endif
    </main>
</div>
@endsection

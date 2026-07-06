@extends('layouts.app')

@section('title', 'Pengaturan')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding: 30px; max-width: 700px; }
    .card + .card { margin-top: 20px; }
    .card h2 { font-size: 18px; color: #1a1a2e; margin-bottom: 12px; }
    .card p { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .btn-secondary:hover { background: #cbd5e1; }
    .btn-danger { background: #dc2626; color: #fff; }
    .btn-danger:hover { background: #b91c1c; }
    .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .form-group { margin-bottom: 16px; }
    .form-check { display: flex; align-items: center; gap: 8px; }
    .form-check input { width: 18px; height: 18px; }
    .form-check label { font-size: 14px; color: #333; cursor: pointer; }
    .info-list { list-style: none; padding: 0; }
    .info-list li { padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #555; display: flex; align-items: center; gap: 8px; }
    .info-list li i { color: #0f3460; width: 20px; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> Pengaturan</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <h2><i class="fas fa-info-circle"></i> Informasi Sistem</h2>
                <ul class="info-list">
                    <li><i class="fas fa-box"></i> Total Sparepart: {{ \App\Models\Sparepart::count() }}</li>
                    <li><i class="fas fa-users"></i> Total Pelanggan: {{ \App\Models\Customer::count() }}</li>
                    <li><i class="fas fa-truck"></i> Total Kendaraan: {{ \App\Models\Vehicle::count() }}</li>
                    <li><i class="fas fa-truck-loading"></i> Total Supplier: {{ \App\Models\Supplier::count() }}</li>
                    <li><i class="fas fa-user-cog"></i> Total Mekanik: {{ \App\Models\Mechanic::count() }}</li>
                    <li><i class="fas fa-credit-card"></i> Total Metode Bayar: {{ \App\Models\PaymentMethod::count() }}</li>
                </ul>
            </div>
        </div>

        @if (Auth::user()->role === 'admin')
        <div class="card" style="border: 2px solid #fecaca;">
            <div class="card-body">
                <h2 style="color:#dc2626;"><i class="fas fa-exclamation-triangle"></i> Reset Data</h2>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><strong>Peringatan!</strong> Fitur ini akan menghapus <strong>semua data transaksi</strong> (service order, invoice, kendaraan, pelanggan, supplier). Data master (kategori, satuan, mekanik, user) tetap aman. Setelah reset, data default akan diisi ulang secara otomatis.</span>
                </div>
                <form method="POST" action="{{ route('settings.reset') }}" onsubmit="confirmForm(this, 'Yakin ingin mereset semua data? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="confirm" name="confirm" value="1" required>
                            <label for="confirm">Saya mengerti dan ingin mereset semua data</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Reset Data Sekarang</button>
                </form>
            </div>
        </div>
        @endif
    </main>
</div>
@endsection

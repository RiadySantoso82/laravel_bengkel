@extends('layouts.app')

@section('title', 'Dashboard - Bengkel')

@push('styles')
<style>
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }
    .menu-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px 20px;
        text-decoration: none;
        color: #333;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.3s;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .menu-card i {
        font-size: 36px;
        color: #0f3460;
        margin-bottom: 12px;
    }
    .menu-card h3 {
        font-size: 15px;
        margin-bottom: 4px;
    }
    .menu-card p {
        font-size: 12px;
        color: #888;
    }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')

    <main class="main-content">
        <h1>Selamat Datang, {{ Auth::user()->name }}</h1>
        <p class="subtitle">Pilih menu untuk memulai</p>

        <div class="menu-grid">
            <a href="#" class="menu-card">
                <i class="fas fa-wrench"></i>
                <h3>Service Order</h3>
                <p>Work order servis</p>
            </a>
            <a href="#" class="menu-card">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>Invoice</h3>
                <p>Kasir & pembayaran</p>
            </a>
            <a href="#" class="menu-card">
                <i class="fas fa-users"></i>
                <h3>Pelanggan</h3>
                <p>Data pelanggan & kendaraan</p>
            </a>
            <a href="#" class="menu-card">
                <i class="fas fa-truck"></i>
                <h3>Kendaraan</h3>
                <p>Data kendaraan</p>
            </a>
            <a href="#" class="menu-card">
                <i class="fas fa-cogs"></i>
                <h3>Sparepart</h3>
                <p>Stok & sparepart</p>
            </a>
            <a href="#" class="menu-card">
                <i class="fas fa-user-hard-hat"></i>
                <h3>Mekanik</h3>
                <p>Data mekanik</p>
            </a>
            <a href="{{ route('sparepart-categories.index') }}" class="menu-card">
                <i class="fas fa-tags"></i>
                <h3>Kategori Sparepart</h3>
                <p>Master kategori</p>
            </a>
            <a href="{{ route('units.index') }}" class="menu-card">
                <i class="fas fa-ruler"></i>
                <h3>Satuan</h3>
                <p>Master satuan</p>
            </a>
            <a href="{{ route('service-categories.index') }}" class="menu-card">
                <i class="fas fa-toolbox"></i>
                <h3>Kategori Servis</h3>
                <p>Master kategori servis</p>
            </a>
            <a href="{{ route('payment-methods.index') }}" class="menu-card">
                <i class="fas fa-credit-card"></i>
                <h3>Metode Bayar</h3>
                <p>Master pembayaran</p>
            </a>
            <a href="#" class="menu-card">
                <i class="fas fa-chart-bar"></i>
                <h3>Laporan</h3>
                <p>Omzet & analitik</p>
            </a>
            @if (Auth::user()->role === 'admin')
            <a href="#" class="menu-card">
                <i class="fas fa-cog"></i>
                <h3>Pengaturan</h3>
                <p>User & konfigurasi</p>
            </a>
            @endif
        </div>
    </main>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard Mekanik')

@push('styles')
<style>
    .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 10px; }
    .menu-card { background: #fff; border-radius: 12px; padding: 24px 20px; text-decoration: none; color: #333; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s; }
    .menu-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    .menu-card i { font-size: 36px; color: #0f3460; margin-bottom: 12px; }
    .menu-card h3 { font-size: 15px; margin-bottom: 4px; }
    .menu-card p { font-size: 12px; color: #888; }
    .stats { display: flex; gap: 20px; margin-bottom: 30px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 20px 28px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); flex: 1; }
    .stat-card i { font-size: 32px; }
    .stat-card h3 { font-size: 28px; color: #1a1a2e; margin-top: 6px; }
    .stat-card p { font-size: 13px; color: #888; }
    .stat-blue i { color: #0f3460; } .stat-green i { color: #059669; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <h1>Halo, {{ Auth::user()->name }}</h1>
        <p class="subtitle">Panel Mekanik</p>

        <div class="stats">
            <div class="stat-card stat-blue">
                <i class="fas fa-wrench"></i>
                <h3>{{ $activeCount }}</h3>
                <p>Servis Aktif</p>
            </div>
            <div class="stat-card stat-green">
                <i class="fas fa-check-circle"></i>
                <h3>{{ $completedCount }}</h3>
                <p>Servis Selesai</p>
            </div>
        </div>

        <div class="menu-grid">
            <a href="{{ route('mechanic.services') }}" class="menu-card">
                <i class="fas fa-list"></i>
                <h3>Daftar Servis Saya</h3>
                <p>Lihat & update status servis</p>
            </a>
            <a href="{{ route('mechanic.history') }}" class="menu-card">
                <i class="fas fa-history"></i>
                <h3>Riwayat Servis</h3>
                <p>Servis yang sudah selesai</p>
            </a>
            <a href="{{ route('mechanic.profile') }}" class="menu-card">
                <i class="fas fa-user-circle"></i>
                <h3>Profil Saya</h3>
                <p>Update profil mekanik</p>
            </a>
        </div>
    </main>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard - Bengkel')

@push('styles')
<style>
    .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
    .menu-card { background: #fff; border-radius: 12px; padding: 24px 20px; text-decoration: none; color: #333; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.3s; }
    .menu-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    .menu-card i { font-size: 36px; color: #0f3460; margin-bottom: 12px; }
    .menu-card h3 { font-size: 15px; margin-bottom: 4px; }
    .menu-card p { font-size: 12px; color: #888; }
    .stats { display: flex; gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 18px 24px; flex: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .stat-card .num { font-size: 24px; font-weight: 700; color: #1a1a2e; }
    .stat-card .label { font-size: 12px; color: #888; margin-top: 2px; }
    .stat-card i { font-size: 20px; margin-right: 8px; }
    .chart-row { display: flex; gap: 20px; margin-bottom: 24px; }
    .chart-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); flex: 1; }
    .chart-card h3 { font-size: 14px; color: #1a1a2e; margin-bottom: 12px; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <h1>Selamat Datang, {{ Auth::user()->name }}</h1>
        <p class="subtitle">Panel Admin</p>

        <div class="stats">
            <div class="stat-card">
                <div class="num"><i class="fas fa-shopping-cart" style="color:#059669;"></i> Rp {{ number_format($totalSales, 0) }}</div>
                <div class="label">Total Penjualan Retail</div>
            </div>
            <div class="stat-card">
                <div class="num"><i class="fas fa-wrench" style="color:#0f3460;"></i> Rp {{ number_format($totalService, 0) }}</div>
                <div class="label">Total Pendapatan Servis</div>
            </div>
            <div class="stat-card">
                <div class="num"><i class="fas fa-money-bill-wave" style="color:#d97706;"></i> Rp {{ number_format($totalSales + $totalService, 0) }}</div>
                <div class="label">Total Pendapatan</div>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-card">
                <h3><i class="fas fa-chart-bar" style="color:#0f3460;"></i> Pendapatan per Bulan</h3>
                <canvas id="chartRevenue" height="120"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fas fa-chart-line" style="color:#059669;"></i> Servis Selesai (7 hari)</h3>
                <canvas id="chartService" height="120"></canvas>
            </div>
        </div>

        <div class="menu-grid">
            <a href="{{ route('service-orders.index') }}" class="menu-card">
                <i class="fas fa-wrench"></i><h3>Service Order</h3><p>Work order servis</p>
            </a>
            <a href="{{ route('invoices.index') }}" class="menu-card">
                <i class="fas fa-file-invoice-dollar"></i><h3>Invoice</h3><p>Kasir & pembayaran</p>
            </a>
            <a href="{{ route('customers.index') }}" class="menu-card">
                <i class="fas fa-users"></i><h3>Pelanggan</h3><p>Data pelanggan</p>
            </a>
            <a href="{{ route('vehicles.index') }}" class="menu-card">
                <i class="fas fa-truck"></i><h3>Kendaraan</h3><p>Data kendaraan</p>
            </a>
            <a href="{{ route('spareparts.index') }}" class="menu-card">
                <i class="fas fa-cogs"></i><h3>Sparepart</h3><p>Stok & sparepart</p>
            </a>
            <a href="{{ route('mechanics.index') }}" class="menu-card">
                <i class="fas fa-user-cog"></i><h3>Mekanik</h3><p>Data mekanik</p>
            </a>
            <a href="{{ route('sparepart-categories.index') }}" class="menu-card">
                <i class="fas fa-tags"></i><h3>Kategori Sparepart</h3><p>Master kategori</p>
            </a>
            <a href="{{ route('units.index') }}" class="menu-card">
                <i class="fas fa-ruler"></i><h3>Satuan</h3><p>Master satuan</p>
            </a>
            <a href="{{ route('service-categories.index') }}" class="menu-card">
                <i class="fas fa-toolbox"></i><h3>Kategori Servis</h3><p>Master kategori servis</p>
            </a>
            <a href="{{ route('payment-methods.index') }}" class="menu-card">
                <i class="fas fa-credit-card"></i><h3>Metode Bayar</h3><p>Master pembayaran</p>
            </a>
            <a href="{{ route('suppliers.index') }}" class="menu-card">
                <i class="fas fa-truck-loading"></i><h3>Supplier</h3><p>Data supplier</p>
            </a>
            <a href="#!" class="menu-card">
                <i class="fas fa-chart-bar"></i><h3>Laporan</h3><p>Omzet & analitik</p>
            </a>
            @if (Auth::user()->role === 'admin')
            <a href="{{ route('settings.index') }}" class="menu-card">
                <i class="fas fa-cog"></i><h3>Pengaturan</h3><p>User & konfigurasi</p>
            </a>
            @endif
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartRevenue'), {
    type: 'bar',
    data: {
        labels: @json($revenueMonths),
        datasets: [{ label: 'Pendapatan', data: @json($revenueTotals), backgroundColor: '#0f3460', borderRadius: 6 }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: function(v) { return 'Rp' + v.toLocaleString('id-ID'); } } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('chartService'), {
    type: 'line',
    data: {
        labels: @json($chartDays),
        datasets: [{
            label: 'Servis selesai',
            data: @json($serviceCounts),
            borderColor: '#059669',
            backgroundColor: 'rgba(5,150,105,0.1)',
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#059669',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endsection

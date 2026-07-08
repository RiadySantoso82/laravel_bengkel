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
    .stats { display: flex; gap: 20px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 20px 28px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06); flex: 1; }
    .stat-card i { font-size: 32px; }
    .stat-card h3 { font-size: 28px; color: #1a1a2e; margin-top: 6px; }
    .stat-card p { font-size: 13px; color: #888; }
    .stat-blue i { color: #0f3460; } .stat-green i { color: #059669; }
    .chart-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .chart-card h3 { font-size: 14px; color: #1a1a2e; margin-bottom: 12px; }
    .chart-row { display: flex; gap: 20px; margin-bottom: 20px; }
    .chart-row > div { flex: 1; }
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
                <p>Servis Selesai (7 hari)</p>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-card">
                <h3><i class="fas fa-chart-bar" style="color:#0f3460;"></i> Servis Selesai per Hari</h3>
                <canvas id="chartServis" height="120"></canvas>
            </div>
            <div class="chart-card" style="overflow-x:auto;">
                <h3><i class="fas fa-fire" style="color:#e94560;"></i> Jam Aktif Servis</h3>
                <div style="font-size:10px;">
                    <div style="display:flex;gap:2px;margin-bottom:3px;">
                        <div style="width:36px;flex-shrink:0;"></div>
                        @foreach ($chartDays as $d)
                        <div style="flex:1;text-align:center;font-weight:600;color:#475569;font-size:9px;">{{ $d }}</div>
                        @endforeach
                    </div>
                    @foreach ($slots as $si => $slot)
                    <div style="display:flex;gap:2px;align-items:center;margin-bottom:2px;">
                        <div style="width:36px;flex-shrink:0;font-size:9px;color:#888;">{{ $slot }}</div>
                        @foreach ($heatmap as $hi => $row)
                        @php
                            $val = $row[$si] ?? 0;
                            if ($val == 0) $color = '#f0f2f5';
                            elseif ($val <= 5) $color = '#c7d2fe';
                            elseif ($val <= 10) $color = '#818cf8';
                            elseif ($val <= 15) $color = '#4f46e5';
                            else $color = '#312e81';
                        @endphp
                        <div style="flex:1;aspect-ratio:2;border-radius:3px;background:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:600;color:{{ $val > 5 ? '#fff' : '#475569' }};" title="{{ $chartDays[$hi] }} {{ $slot }}: {{ $val }} servis">{{ $val > 0 ? $val : '' }}</div>
                        @endforeach
                    </div>
                    @endforeach
                    <div style="display:flex;gap:3px;align-items:center;margin-top:6px;flex-wrap:wrap;">
                        <span style="font-size:9px;color:#888;">Skala:</span>
                        <span style="width:12px;height:12px;border-radius:2px;background:#f0f2f5;display:inline-block;"></span><span style="font-size:9px;color:#888;margin-right:6px;">0</span>
                        <span style="width:12px;height:12px;border-radius:2px;background:#c7d2fe;display:inline-block;"></span><span style="font-size:9px;color:#888;margin-right:6px;">1-5</span>
                        <span style="width:12px;height:12px;border-radius:2px;background:#818cf8;display:inline-block;"></span><span style="font-size:9px;color:#888;margin-right:6px;">6-10</span>
                        <span style="width:12px;height:12px;border-radius:2px;background:#4f46e5;display:inline-block;"></span><span style="font-size:9px;color:#888;margin-right:6px;">11-15</span>
                        <span style="width:12px;height:12px;border-radius:2px;background:#312e81;display:inline-block;"></span><span style="font-size:9px;color:#888;">>15</span>
                    </div>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartServis'), {
    type: 'bar',
    data: {
        labels: @json($chartDays),
        datasets: [{
            label: 'Servis selesai',
            data: @json($chartCounts),
            backgroundColor: '#0f3460',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, max: 50, ticks: { stepSize: 5 } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endsection

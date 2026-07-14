<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Antrian Servis - Bengkel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #fff; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        .header { text-align: center; margin-bottom: 24px; }
        .header i { font-size: 40px; color: #e94560; }
        .header h1 { font-size: 28px; margin-top: 6px; }
        .header p { color: #94a3b8; font-size: 14px; margin-top: 2px; }

        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .clock { font-size: 18px; color: #94a3b8; font-weight: 500; }
        .clock i { margin-right: 6px; color: #e94560; }
        .refresh-control { display: flex; align-items: center; gap: 8px; }
        .refresh-control label { font-size: 12px; color: #64748b; }
        .refresh-control select { padding: 6px 10px; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #fff; font-size: 13px; outline: none; cursor: pointer; }

        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 28px; }
        .stat-card { background: #1e293b; border-radius: 14px; padding: 16px; text-align: center; }
        .stat-card .num { font-size: 32px; font-weight: 700; }
        .stat-card .label { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        .stat-card .icon { font-size: 20px; margin-bottom: 6px; }
        .stat-red .num, .stat-red .icon { color: #e94560; }
        .stat-yellow .num, .stat-yellow .icon { color: #d97706; }
        .stat-green .num, .stat-green .icon { color: #059669; }
        .stat-blue .num, .stat-blue .icon { color: #3b82f6; }

        .now-serving { text-align: center; padding: 36px; background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 20px; border: 2px solid #e94560; margin-bottom: 28px; }
        .now-serving .label { font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 3px; }
        .now-serving .number { font-size: 64px; font-weight: 700; color: #e94560; margin: 6px 0; line-height: 1; }
        .now-serving .detail { font-size: 18px; }
        .now-serving .mechanic { color: #94a3b8; font-size: 14px; margin-top: 4px; }

        .section { margin-bottom: 24px; }
        .section-title { font-size: 13px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
        .section-title .count { background: #1e293b; padding: 2px 10px; border-radius: 999px; font-size: 12px; }

        .card { background: #1e293b; border-radius: 14px; padding: 16px 20px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; }
        .card .info { flex: 1; }
        .card .no { font-size: 26px; font-weight: 700; color: #e94560; min-width: 56px; }
        .card .vehicle { font-size: 16px; font-weight: 600; }
        .card .plate { color: #94a3b8; font-size: 13px; margin-top: 2px; }
        .card .status { font-size: 11px; padding: 4px 12px; border-radius: 999px; font-weight: 600; white-space: nowrap; }
        .status-progress { background: #059669; color: #fff; }
        .status-queued { background: #d97706; color: #fff; }
        .status-waiting { background: #dc2626; color: #fff; }
        .status-done { background: #1e40af; color: #fff; }

        @media (max-width: 600px) {
            .container { padding: 20px 12px; }
            .header h1 { font-size: 20px; }
            .stats { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .stat-card .num { font-size: 24px; }
            .now-serving .number { font-size: 44px; }
            .now-serving { padding: 24px; }
            .card { padding: 12px 14px; }
            .card .no { font-size: 20px; min-width: 40px; }
            .card .vehicle { font-size: 14px; }
            .top-bar { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-car-side"></i>
            <h1>Bengkel Jaya Motor</h1>
            <p>Sistem Informasi Antrian Servis</p>
        </div>

        <div class="top-bar">
            <div class="clock"><i class="fas fa-clock"></i> <span id="clockDisplay">{{ now()->format('H:i:s') }}</span></div>
            <div class="refresh-control">
                <label><i class="fas fa-sync-alt"></i> Refresh</label>
                <select id="refreshInterval" onchange="saveRefresh()">
                    <option value="10">10 detik</option>
                    <option value="15">15 detik</option>
                    <option value="30" selected>30 detik</option>
                    <option value="60">1 menit</option>
                    <option value="120">2 menit</option>
                    <option value="300">5 menit</option>
                </select>
            </div>
        </div>

        @php
            $queuedCount = $queued->count();
            $inProgressCount = $inProgress->count();
            $waitingCount = $waitingPart->count();
            $doneCount = $done->count();
            $remaining = $queuedCount + $waitingCount;
        @endphp

        <div class="stats">
            <div class="stat-card stat-red">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="num">{{ $remaining }}</div>
                <div class="label">Sisa Antrian</div>
            </div>
            <div class="stat-card stat-yellow">
                <div class="icon"><i class="fas fa-wrench"></i></div>
                <div class="num">{{ $inProgressCount }}</div>
                <div class="label">Dikerjakan</div>
            </div>
            <div class="stat-card stat-blue">
                <div class="icon"><i class="fas fa-box"></i></div>
                <div class="num">{{ $waitingCount }}</div>
                <div class="label">Tunggu Part</div>
            </div>
            <div class="stat-card stat-green">
                <div class="icon"><i class="fas fa-check"></i></div>
                <div class="num">{{ $doneCount }}</div>
                <div class="label">Selesai Hari Ini</div>
            </div>
        </div>

        @if ($inProgress->isNotEmpty())
        @php $current = $inProgress->first(); @endphp
        <div class="now-serving">
            <div class="label"><i class="fas fa-wrench"></i> Sedang Dikerjakan</div>
            <div class="number">#{{ str_pad($current->id, 3, '0', STR_PAD_LEFT) }}</div>
            <div class="detail">{{ $current->vehicle->brand ?? 'Kendaraan' }} · {{ $current->vehicle->plate_number ?? $current->vehicle_plate_manual ?? '-' }}</div>
            <div class="mechanic"><i class="fas fa-user-cog"></i> {{ $current->mechanic->name ?? '-' }}</div>
        </div>
        @endif

        @if ($queued->isNotEmpty())
        <div class="section">
            <div class="section-title"><i class="fas fa-clock"></i> Menunggu <span class="count">{{ $queuedCount }}</span></div>
            @foreach ($queued as $o)
            <div class="card">
                <div class="no">#{{ str_pad($o->id, 3, '0', STR_PAD_LEFT) }}</div>
                <div class="info">
                    <div class="vehicle">{{ $o->vehicle->brand ?? 'Kendaraan' }} · {{ $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? '-' }}</div>
                    <div class="plate">{{ $o->complaint ? Str::limit($o->complaint, 50) : '-' }}</div>
                </div>
                <span class="status status-queued">Antri</span>
            </div>
            @endforeach
        </div>
        @endif

        @if ($waitingPart->isNotEmpty())
        <div class="section">
            <div class="section-title"><i class="fas fa-box"></i> Tunggu Part <span class="count">{{ $waitingCount }}</span></div>
            @foreach ($waitingPart as $o)
            <div class="card" style="opacity:0.8;">
                <div class="no">#{{ str_pad($o->id, 3, '0', STR_PAD_LEFT) }}</div>
                <div class="info">
                    <div class="vehicle">{{ $o->vehicle->brand ?? 'Kendaraan' }} · {{ $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? '-' }}</div>
                </div>
                <span class="status status-waiting">Tunggu Part</span>
            </div>
            @endforeach
        </div>
        @endif

        @if ($done->isNotEmpty())
        <div class="section">
            <div class="section-title"><i class="fas fa-check-circle"></i> Selesai <span class="count">{{ $doneCount }}</span></div>
            @foreach ($done as $o)
            <div class="card" style="opacity:0.55;">
                <div class="no">#{{ str_pad($o->id, 3, '0', STR_PAD_LEFT) }}</div>
                <div class="info">
                    <div class="vehicle">{{ $o->vehicle->brand ?? 'Kendaraan' }} · {{ $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? '-' }}</div>
                </div>
                <span class="status status-done">{{ $o->status === 'picked_up' ? 'Diambil' : 'Selesai' }}</span>
            </div>
            @endforeach
        </div>
        @endif

        @if ($queued->isEmpty() && $inProgress->isEmpty() && $waitingPart->isEmpty() && $done->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:#64748b;">
            <i class="fas fa-car-side" style="font-size:64px;margin-bottom:16px;opacity:0.5;"></i>
            <h2 style="font-weight:400;">Belum ada antrian hari ini</h2>
        </div>
        @endif
    </div>

    <script>
        function updateClock() {
            var now = new Date();
            var h = String(now.getHours()).padStart(2, '0');
            var m = String(now.getMinutes()).padStart(2, '0');
            var s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clockDisplay').textContent = h + ':' + m + ':' + s;
        }
        setInterval(updateClock, 1000);

        var savedInterval = localStorage.getItem('queueRefreshInterval');
        if (savedInterval) {
            document.getElementById('refreshInterval').value = savedInterval;
        }
        var currentInterval = parseInt(document.getElementById('refreshInterval').value) * 1000;

        function saveRefresh() {
            var val = document.getElementById('refreshInterval').value;
            localStorage.setItem('queueRefreshInterval', val);
            currentInterval = parseInt(val) * 1000;
            clearTimeout(window._refreshTimer);
            window._refreshTimer = setTimeout(function() { location.reload(); }, currentInterval);
        }

        document.getElementById('refreshInterval').addEventListener('change', saveRefresh);

        window._refreshTimer = setTimeout(function() { location.reload(); }, currentInterval);
    </script>
</body>
</html>

@extends('layouts.app')

@section('title', 'Riwayat Servis')

@push('styles')
<style>
    :root {
        --surface-1: #fff;
        --surface-2: #f0f2f5;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --bg-success: #d1fae5;
        --text-success: #065f46;
        --bg-info: #e0e7ff;
        --text-info: #3730a3;
    }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .page-header h1 { font-size: 16px; font-weight: 500; color: #1a1a2e; margin: 0; }
    .page-header .count { font-size: 13px; color: var(--text-secondary); }
    .job-list { display: flex; flex-direction: column; gap: 10px; }
    .job-card {
        display: flex; flex-direction: column; gap: 8px; padding: 14px; text-align: left;
        border-radius: 12px; background: var(--surface-1); opacity: 0.7;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04); cursor: pointer; transition: all 0.2s;
    }
    .job-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .job-card .top { display: flex; align-items: center; justify-content: space-between; }
    .job-card .vehicle { font-size: 14px; font-weight: 500; color: #1a1a2e; margin: 0; }
    .job-card .status {
        font-size: 11px; padding: 2px 8px; border-radius: 999px; font-weight: 500; white-space: nowrap;
    }
    .status-done { background: var(--bg-success); color: var(--text-success); }
    .status-picked { background: var(--bg-info); color: var(--text-info); }
    .job-card .complaint { font-size: 12px; color: var(--text-secondary); margin: 0; }
    .job-card .time { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content" style="max-width:600px;padding:30px 20px;">
        <div class="page-header" style="max-width:380px;margin:0 auto 1rem;">
            <h1>Riwayat servis</h1>
            <span class="count">{{ $data->count() }} job</span>
        </div>

        <form method="GET" style="max-width:380px;margin:0 auto 1rem;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:120px;">
                <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">Dari</label>
                <input type="date" name="date_from" value="{{ $d1 ?? '' }}" style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none;">
            </div>
            <div style="flex:1;min-width:120px;">
                <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">Sampai</label>
                <input type="date" name="date_to" value="{{ $d2 ?? '' }}" style="width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none;">
            </div>
            <button type="submit" style="padding:8px 16px;border-radius:8px;border:none;background:#0f3460;color:#fff;font-size:13px;font-weight:600;cursor:pointer;"><i class="fas fa-search"></i></button>
            @if ($d1 || $d2)
            <a href="{{ route('mechanic.history') }}" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;background:#fff;color:#475569;font-size:13px;text-decoration:none;"><i class="fas fa-undo"></i></a>
            @endif
            @if ($d1 && $d2)
            <div style="width:100%;font-size:11px;color:#888;margin-top:4px;">Maksimal rentang 31 hari</div>
            @endif
        </form>

        <div class="job-list" style="max-width:380px;margin:0 auto;">
            @forelse ($data as $o)
            @php
                $plate = $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? 'walk-in';
                $vehicleInfo = $o->vehicle->brand ?? $o->vehicle_info_manual ?? 'Kendaraan';
            @endphp
            <div class="job-card" onclick="window.location='{{ route('mechanic.detail', $o) . '?from=history' }}'">
                <div class="top">
                    <p class="vehicle">{{ $vehicleInfo }} · {{ $plate }}</p>
                    <span class="status status-{{ $o->status === 'picked_up' ? 'picked' : 'done' }}">
                        {{ $o->status === 'picked_up' ? 'Diambil' : 'Selesai' }}
                    </span>
                </div>
                <p class="complaint">{{ $o->complaint ? Str::limit($o->complaint, 60) : '-' }}</p>
                <div class="time">
                    <i class="fas fa-clock" style="font-size:14px;"></i>
                    <span>Selesai {{ $o->actual_finish ? date('d/m/Y H:i', strtotime($o->actual_finish)) : '-' }}</span>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:60px 20px;color:#94a3b8;">
                <i class="fas fa-history" style="font-size:48px;margin-bottom:16px;"></i>
                <h3 style="color:#475569;font-size:16px;font-weight:500;">Belum ada riwayat servis</h3>
            </div>
            @endforelse
        </div>

        <div style="max-width:380px;margin:16px auto 0;display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
            {{ $data->links() }}
        </div>
    </main>
</div>
@endsection

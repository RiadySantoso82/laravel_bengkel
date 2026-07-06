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
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
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

        <div class="job-list" style="max-width:380px;margin:0 auto;">
            @forelse ($data as $o)
            @php
                $plate = $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? 'walk-in';
                $vehicleInfo = $o->vehicle->brand ?? $o->vehicle_info_manual ?? 'Kendaraan';
            @endphp
            <div class="job-card">
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
    </main>
</div>
@endsection

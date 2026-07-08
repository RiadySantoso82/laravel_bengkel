@extends('layouts.app')

@section('title', 'Daftar Servis Saya')

@push('styles')
<style>
    :root {
        --surface-1: #fff;
        --surface-2: #f0f2f5;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --fill-primary: #0f3460;
        --on-primary: #fff;
        --bg-danger: #fde8e8;
        --text-danger: #991b1b;
        --bg-warning: #fef3c7;
        --text-warning: #92400e;
        --bg-success: #d1fae5;
        --text-success: #065f46;
    }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .page-header h1 { font-size: 18px; font-weight: 600; color: #1a1a2e; margin: 0; }
    .page-header .count { font-size: 13px; color: var(--text-secondary); }
    .filter-bar { display: flex; gap: 8px; margin-bottom: 1.25rem; overflow-x: auto; padding-bottom: 4px; }
    .filter-bar button {
        padding: 6px 14px; font-size: 13px; white-space: nowrap; border-radius: 999px; border: none;
        cursor: pointer; transition: all 0.2s; background: #e2e8f0; color: #475569;
    }
    .filter-bar button.active { background: var(--fill-primary); color: var(--on-primary); }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; }
    tr:hover td { background: #f8fafc; }
    .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
    .alert-success { background: var(--bg-success); color: var(--text-success); border: 1px solid #a7f3d0; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-queued { background: #e2e8f0; color: #475569; }
    .badge-progress { background: var(--bg-danger); color: var(--text-danger); }
    .badge-waiting { background: var(--bg-warning); color: var(--text-warning); }
    .badge-done { background: var(--bg-success); color: var(--text-success); }
    .badge-picked { background: #e0e7ff; color: #3730a3; }

    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: var(--fill-primary); color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }

    .job-list-mobile { display: none; flex-direction: column; gap: 10px; }
    .job-card {
        display: flex; flex-direction: column; gap: 8px; padding: 14px; text-align: left;
        border-radius: 12px; background: var(--surface-1); box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .job-card.completed { opacity: 0.7; }
    .job-card .top { display: flex; align-items: center; justify-content: space-between; }
    .job-card .vehicle { font-size: 14px; font-weight: 500; color: #1a1a2e; margin: 0; }
    .job-card .status { font-size: 11px; padding: 2px 8px; border-radius: 999px; font-weight: 500; white-space: nowrap; }
    .job-card .status.queued { background: #e2e8f0; color: #475569; }
    .job-card .status.progress { background: var(--bg-danger); color: var(--text-danger); }
    .job-card .status.waiting { background: var(--bg-warning); color: var(--text-warning); }
    .job-card .status.done { background: var(--bg-success); color: var(--text-success); }
    .job-card .status.picked { background: #e0e7ff; color: #3730a3; }
    .job-card .complaint { font-size: 12px; color: var(--text-secondary); margin: 0; }
    .job-card .time { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); }
    .job-card select { font-size: 12px; padding: 4px 8px; border: 1px solid #ddd; border-radius: 6px; outline: none; background: #fff; width: 100%; }
    .pagination-wrap { max-width:380px; margin: 16px auto 0; }
    .pagination-wrap nav { display: flex; gap: 4px; justify-content: center; flex-wrap: wrap; }
    .pagination-wrap a, .pagination-wrap span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; color: #475569; background: #fff; border: 1px solid #e2e8f0; }
    .pagination-wrap .active { background: #0f3460; color: #fff; border-color: #0f3460; }

    @media (max-width: 768px) {
        .table-desktop { display: none; }
        .job-list-mobile { display: flex; }
        .filter-bar button { font-size: 12px; padding: 5px 12px; }
    }
    @media (min-width: 769px) {
        .job-list-mobile { display: none; }
        .table-desktop { display: block; }
    }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <div class="page-header">
            <h1>Servis saya</h1>
            <span class="count">{{ $data->count() }} job</span>
        </div>

        <div class="filter-bar">
            <button class="active" data-filter="all" onclick="filterJobs('all', this)">Semua</button>
            <button data-filter="queued" onclick="filterJobs('queued', this)">Antri</button>
            <button data-filter="in_progress" onclick="filterJobs('in_progress', this)">Dikerjakan</button>
            <button data-filter="waiting_part" onclick="filterJobs('waiting_part', this)">Tunggu part</button>
            <button data-filter="done" onclick="filterJobs('done', this)">Selesai</button>
            <button data-filter="picked_up" onclick="filterJobs('picked_up', this)">Diambil</button>
        </div>

        <div class="table-desktop">
            <div class="card"><div class="card-body">
                @if ($data->isEmpty())
                    <div style="text-align:center;padding:40px 20px;color:#94a3b8;"><i class="fas fa-wrench" style="font-size:40px;margin-bottom:12px;"></i><h3 style="color:#475569;font-size:16px;font-weight:500;">Belum ada servis ditugaskan</h3></div>
                @else
                    <table>
                        <thead><tr><th>#</th><th>Kendaraan</th><th>Pelanggan</th><th>Keluhan</th><th>Status</th><th>Waktu</th></tr></thead>
                        <tbody>
                            @foreach ($data as $o)
                            @php
                                $statusMap = ['queued'=>'Antri','in_progress'=>'Dikerjakan','waiting_part'=>'Tunggu part','done'=>'Selesai','picked_up'=>'Diambil'];
                                $statusBadge = $o->status === 'in_progress' ? 'progress' : ($o->status === 'waiting_part' ? 'waiting' : ($o->status === 'done' ? 'done' : ($o->status === 'picked_up' ? 'picked' : 'queued')));
                                $plate = $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? '-';
                                $readyParts = $o->partRequests->sum(fn($pr) => $pr->details->whereIn('status', ['fulfilled','partial'])->count());
                            @endphp
                            <tr data-status="{{ $o->status }}" class="job-row" onclick="window.location='{{ route('mechanic.detail', $o) }}'" style="cursor:pointer;">
                                <td>SO-{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }} {!! $readyParts > 0 ? '<span style="background:#059669;color:#fff;font-size:10px;padding:1px 6px;border-radius:999px;margin-left:4px;">Part</span>' : '' !!}</td>
                                <td>{{ $o->vehicle->brand ?? $o->vehicle_info_manual ?? '-' }} · {{ $plate }}</td>
                                <td>{{ $o->customer->name ?? '-' }}</td>
                                <td>{{ Str::limit($o->complaint, 30) }}</td>
                                <td><span class="badge badge-{{ $statusBadge }}">{{ $statusMap[$o->status] }}</span></td>
                                <td style="font-size:13px;color:#666;">{{ $o->created_at->format('d/m H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div></div>
        </div>

        <div class="job-list-mobile" id="job-list">
            @forelse ($data as $o)
            @php
                $statusMap = ['queued'=>'Antri','in_progress'=>'Dikerjakan','waiting_part'=>'Tunggu part','done'=>'Selesai','picked_up'=>'Diambil'];
                $statusClass = $o->status === 'in_progress' ? 'progress' : ($o->status === 'waiting_part' ? 'waiting' : ($o->status === 'done' ? 'done' : ($o->status === 'picked_up' ? 'picked' : 'queued')));
                $plate = $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? 'walk-in';
                $vehicleInfo = $o->vehicle->brand ?? $o->vehicle_info_manual ?? 'Kendaraan';
                $readyParts = $o->partRequests->sum(fn($pr) => $pr->details->whereIn('status', ['fulfilled','partial'])->count());
                $timeText = $o->created_at->format('H:i');
                if ($o->status === 'done' || $o->status === 'picked_up') {
                    $targetText = 'Selesai ' . ($o->actual_finish ? date('H:i', strtotime($o->actual_finish)) : '-');
                } elseif ($o->status === 'waiting_part') {
                    $targetText = 'Menunggu part dari gudang';
                } elseif ($o->estimated_finish) {
                    $targetText = 'Target selesai ' . date('H:i', strtotime($o->estimated_finish));
                } else {
                    $targetText = 'Belum dimulai';
                }
            @endphp
            <div class="job-card {{ in_array($o->status, ['done','picked_up']) ? 'completed' : '' }}" data-status="{{ $o->status }}" onclick="window.location='{{ route('mechanic.detail', $o) }}'" style="cursor:pointer;">
                <div class="top">
                    <p class="vehicle">{{ $vehicleInfo }} · {{ $plate }}{!! $readyParts > 0 ? ' <span style="background:#059669;color:#fff;font-size:10px;padding:1px 6px;border-radius:999px;">Part</span>' : '' !!}</p>
                    <span class="status {{ $statusClass }}">{{ $statusMap[$o->status] }}</span>
                </div>
                <p class="complaint">{{ $o->complaint ? Str::limit($o->complaint, 60) : '-' }}</p>
                <div class="time"><i class="fas fa-clock" style="font-size:14px;"></i><span>Masuk {{ $timeText }} · {{ $targetText }}</span></div>
            </div>
            @empty
            <div style="text-align:center;padding:60px 20px;color:#94a3b8;">
                <i class="fas fa-wrench" style="font-size:48px;margin-bottom:16px;"></i>
                <h3 style="color:#475569;font-size:16px;font-weight:500;">Belum ada servis ditugaskan</h3>
            </div>
            @endforelse
        </div>

        <div class="pagination-wrap">
            {{ $data->links() }}
        </div>
    </main>
</div>
<script>
function filterJobs(filter, btn) {
    document.querySelectorAll('.filter-bar button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.job-row, .job-card').forEach(el => {
        if (filter === 'all') { el.style.display = ''; }
        else { el.style.display = el.dataset.status === filter ? '' : 'none'; }
    });
}
</script>
@endsection

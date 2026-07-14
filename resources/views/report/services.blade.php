@extends('layouts.app')

@section('title', 'Laporan Servis')

@push('styles')
<style>
    .page-header h1 { font-size:24px; color:#1a1a2e; }
    .card { background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:16px; }
    .card-body { padding:20px; }
    table { width:100%;border-collapse:collapse; }
    th,td { text-align:left;padding:10px 14px;border-bottom:1px solid #f0f0f0;font-size:13px; }
    th { background:#f8fafc;font-weight:600;color:#475569;font-size:12px; }
    tr:hover td { background:#f8fafc; }
    .badge { display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600; }
    .badge-queued { background:#e2e8f0;color:#475569; }
    .badge-progress { background:#fde8e8;color:#991b1b; }
    .badge-waiting { background:#fef3c7;color:#92400e; }
    .badge-done { background:#d1fae5;color:#065f46; }
    .badge-picked { background:#e0e7ff;color:#3730a3; }
    .filter-bar { display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px;padding:16px 20px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .filter-bar label { display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:4px; }
    .filter-bar select,.filter-bar input { padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none; }
    .btn { display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;text-decoration:none; }
    .btn-primary { background:#0f3460;color:#fff; }
    .btn-primary:hover { background:#1a1a2e; }
    .btn-secondary { background:#e2e8f0;color:#475569; }
    .pagination { display:flex;gap:4px;justify-content:center;margin-top:16px; }
    .pagination a,.pagination span { padding:6px 12px;border-radius:6px;font-size:13px;text-decoration:none;color:#475569;background:#fff;border:1px solid #e2e8f0; }
    .pagination .active { background:#0f3460;color:#fff;border-color:#0f3460; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header" style="margin-bottom:20px;">
            <h1><i class="fas fa-wrench"></i> Laporan Servis</h1>
        </div>
        <form class="filter-bar" method="GET">
            <div><label>Dari</label><input type="date" name="date_from" value="{{ $from }}"></div>
            <div><label>Sampai</label><input type="date" name="date_to" value="{{ $to }}"></div>
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="">Semua</option>
                    <option value="queued" {{ request('status')==='queued' ? 'selected' : '' }}>Antri</option>
                    <option value="in_progress" {{ request('status')==='in_progress' ? 'selected' : '' }}>Dikerjakan</option>
                    <option value="waiting_part" {{ request('status')==='waiting_part' ? 'selected' : '' }}>Tunggu Part</option>
                    <option value="done" {{ request('status')==='done' ? 'selected' : '' }}>Selesai</option>
                    <option value="picked_up" {{ request('status')==='picked_up' ? 'selected' : '' }}>Diambil</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('reports.services') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>
        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table>
                    <thead><tr><th>Tanggal</th><th>#</th><th>Pelanggan</th><th>Kendaraan</th><th>Mekanik</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($data as $o)
                        <tr>
                            <td style="white-space:nowrap;">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                            <td>SO-{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $o->customer->name ?? '-' }}</td>
                            <td>{{ $o->vehicle->plate_number ?? $o->vehicle_plate_manual ?? '-' }}</td>
                            <td>{{ $o->mechanic->name ?? '-' }}</td>
                            <td><span class="badge badge-{{ $o->status === 'in_progress' ? 'progress' : ($o->status === 'waiting_part' ? 'waiting' : ($o->status === 'done' ? 'done' : ($o->status === 'picked_up' ? 'picked' : 'queued'))) }}">{{ ['queued'=>'Antri','in_progress'=>'Dikerjakan','waiting_part'=>'Tunggu Part','done'=>'Selesai','picked_up'=>'Diambil'][$o->status] ?? $o->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada data servis</td></tr>
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

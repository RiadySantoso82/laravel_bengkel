@extends('layouts.app')

@section('title', 'Service Order')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-warning { background: #d97706; color: #fff; }
    .btn-warning:hover { background: #b45309; }
    .btn-danger { background: #dc2626; color: #fff; }
    .btn-danger:hover { background: #b91c1c; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; }
    tr:hover td { background: #f8fafc; }
    .actions { display: flex; gap: 6px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-queued { background: #fef3c7; color: #92400e; }
    .badge-progress { background: #dbeafe; color: #1e40af; }
    .badge-waiting { background: #fce7f3; color: #9d174d; }
    .badge-done { background: #d1fae5; color: #065f46; }
    .badge-picked { background: #e0e7ff; color: #3730a3; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-wrench"></i> Service Order</h1>
            <a href="{{ route('service-orders.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Baru</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                @if ($data->isEmpty())
                    <div style="text-align:center;padding:60px 20px;color:#94a3b8;"><i class="fas fa-wrench" style="font-size:48px;margin-bottom:16px;"></i><h3 style="color:#475569;">Belum ada service order</h3><p style="margin-bottom:20px;">Buat service order baru.</p><a href="{{ route('service-orders.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Service Order</a></div>
                @else
                    <table>
                        <thead><tr><th>#</th><th>Pelanggan</th><th>Kendaraan</th><th>Mekanik</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($data as $i => $o)
                            <tr>
                                <td>SO-{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $o->customer->name ?? '-' }}</td>
                                <td>{{ $o->vehicle->plate_number ?? '-' }}</td>
                                <td>{{ $o->mechanic->name ?? '-' }}</td>
                                <td>
                                    @php $map = ['queued'=>'Antri','in_progress'=>'Dikerjakan','waiting_part'=>'Tunggu Part','done'=>'Selesai','picked_up'=>'Diambil'] @endphp
                                    <span class="badge badge-{{ $o->status === 'in_progress' ? 'progress' : ($o->status === 'waiting_part' ? 'waiting' : ($o->status === 'done' ? 'done' : ($o->status === 'picked_up' ? 'picked' : 'queued'))) }}">{{ $map[$o->status] ?? $o->status }}</span>
                                </td>
                                <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('service-orders.edit', $o) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('service-orders.destroy', $o) }}" onsubmit="confirmForm(this, '')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection

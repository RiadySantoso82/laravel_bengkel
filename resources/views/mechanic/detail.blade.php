@extends('layouts.app')

@section('title', 'Detail Servis')

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
        --border: #e2e8f0;
        --radius: 12px;
    }
    .detail-container { max-width: 500px; margin: 0 auto; padding: 20px 16px; }
    .back-btn {
        width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
        border-radius: 50%; border: none; background: transparent; cursor: pointer; color: #1a1a2e;
        font-size: 18px; transition: background 0.2s;
    }
    .back-btn:hover { background: var(--surface-2); }
    .info-box {
        background: var(--surface-2); border-radius: var(--radius); padding: 1rem; margin-bottom: 1rem;
    }
    .info-box .row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .info-box .vehicle { font-size: 15px; font-weight: 500; color: #1a1a2e; margin: 0; }
    .info-box .detail { font-size: 13px; color: var(--text-secondary); margin: 0 0 4px; }
    .badge {
        display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 500;
    }
    .badge-queued { background: var(--surface-2); color: var(--text-secondary); }
    .badge-progress { background: var(--bg-danger); color: var(--text-danger); }
    .badge-waiting { background: var(--bg-warning); color: var(--text-warning); }
    .badge-done { background: var(--bg-success); color: var(--text-success); }
    .badge-picked { background: #e0e7ff; color: #3730a3; }
    .section-title { font-size: 13px; font-weight: 600; color: var(--text-secondary); margin: 0 0 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .checklist label {
        display: flex; align-items: center; gap: 10px; padding: 10px 4px;
        border-bottom: 0.5px solid var(--border); font-size: 14px; cursor: pointer;
    }
    .checklist label:last-child { border-bottom: none; }
    .checklist input { width: 18px; height: 18px; }
    .photo-area { display: flex; gap: 8px; margin-bottom: 1.25rem; }
    .photo-box {
        width: 64px; height: 64px; border-radius: var(--radius); background: var(--surface-2);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: var(--text-muted);
    }
    .photo-add {
        width: 64px; height: 64px; padding: 0; border-radius: var(--radius);
        display: flex; align-items: center; justify-content: center;
        border: 2px dashed var(--border); background: transparent; cursor: pointer;
        font-size: 20px; color: var(--text-muted);
    }
    .action-btn {
        width: 100%; padding: 12px; text-align: center; border-radius: var(--radius);
        font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;
    }
    .action-btn.primary { background: var(--fill-primary); color: var(--on-primary); }
    .action-btn.primary:hover { background: #1a1a2e; }
    .action-btn.danger { background: var(--bg-warning); color: var(--text-warning); }
    .action-btn.danger:hover { background: #fde68a; }
    .items-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1rem; }
    .item-row {
        display: flex; align-items: center; gap: 10px; padding: 10px 12px;
        background: var(--surface-2); border-radius: 8px; font-size: 14px;
    }
    .item-row .icon { font-size: 16px; width: 24px; text-align: center; }
    .item-row .name { flex: 1; color: #1a1a2e; }
    .item-row .qty { color: var(--text-secondary); font-size: 13px; white-space: nowrap; }
    .item-row .icon-jasa { color: var(--fill-primary); }
    .item-row .icon-part { color: var(--text-warning); }

    @media (min-width: 769px) {
        .detail-container { max-width: 600px; padding: 30px 20px; }
        .info-box { padding: 1.25rem; }
        .checklist { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
        .checklist label { border-bottom: none; padding: 8px 4px; }
    }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="detail-container">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:1rem;">
                <a href="{{ route('mechanic.services') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <p style="font-weight:500;font-size:15px;margin:0;">Detail servis order</p>
            </div>

            <div class="info-box">
                <div class="row">
                    <p class="vehicle">{{ $serviceOrder->vehicle->brand ?? $serviceOrder->vehicle_info_manual ?? 'Kendaraan' }} · {{ $serviceOrder->vehicle->plate_number ?? $serviceOrder->vehicle_plate_manual ?? '-' }}</p>
                    @php
                        $statusMap = ['queued'=>'Antri','in_progress'=>'Dikerjakan','waiting_part'=>'Tunggu part','done'=>'Selesai','picked_up'=>'Diambil'];
                        $statusBadge = $serviceOrder->status === 'in_progress' ? 'progress' : ($serviceOrder->status === 'waiting_part' ? 'waiting' : ($serviceOrder->status === 'done' ? 'done' : ($serviceOrder->status === 'picked_up' ? 'picked' : 'queued')));
                    @endphp
                    <span class="badge badge-{{ $statusBadge }}">{{ $statusMap[$serviceOrder->status] }}</span>
                </div>
                <p class="detail"><strong>Keluhan:</strong> {{ $serviceOrder->complaint ?? '-' }}</p>
                <p class="detail"><strong>Pelanggan:</strong> {{ $serviceOrder->customer->name ?? '-' }} · Masuk {{ $serviceOrder->created_at->format('H:i') }} · {{ $serviceOrder->created_at->format('d/m/Y') }}</p>
                @if ($serviceOrder->estimated_finish)
                <p class="detail"><strong>Target selesai:</strong> {{ date('d/m/Y H:i', strtotime($serviceOrder->estimated_finish)) }}</p>
                @endif
            </div>

            @if ($serviceOrder->details->isNotEmpty())
            <p class="section-title">Item pekerjaan</p>
            <div class="items-list">
                @foreach ($serviceOrder->details as $d)
                @php
                    $name = $d->type === 'jasa' ? ($serviceTypes[$d->item_id] ?? 'Jasa #' . $d->item_id) : ($spareparts[$d->item_id] ?? 'Part #' . $d->item_id);
                @endphp
                <div class="item-row">
                    <span class="icon icon-{{ $d->type }}">{{ $d->type === 'jasa' ? '🔧' : '🔩' }}</span>
                    <span class="name">{{ $name }}</span>
                    <span class="qty">×{{ $d->qty }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <p class="section-title">Checklist pengecekan</p>
            <div class="checklist" style="margin-bottom:1.25rem;">
                @foreach ($checklistItems as $item)
                <label>
                    <input type="checkbox" name="checklist[]" value="{{ $loop->index }}">
                    <span>{{ $item }}</span>
                </label>
                @endforeach
            </div>

            <p class="section-title">Foto kondisi kendaraan</p>
            <div class="photo-area">
                <div class="photo-box"><i class="fas fa-camera"></i></div>
                <div class="photo-box"><i class="fas fa-camera"></i></div>
                <button class="photo-add" onclick="alert('Fitur upload foto akan segera hadir')"><i class="fas fa-plus"></i></button>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;">
                @if (!in_array($serviceOrder->status, ['done', 'picked_up']))
                @if ($serviceOrder->status === 'queued')
                <form method="POST" action="{{ route('mechanic.update-status', $serviceOrder) }}" onsubmit="confirmForm(this, 'Mulai mengerjakan servis ini?')">
                    @csrf
                    <input type="hidden" name="status" value="in_progress">
                    <button class="action-btn primary"><i class="fas fa-play"></i> Mulai Kerjakan</button>
                </form>
                @endif
                @if ($serviceOrder->status !== 'waiting_part')
                <form method="POST" action="{{ route('mechanic.update-status', $serviceOrder) }}" onsubmit="confirmForm(this, 'Ajukan request part tambahan untuk servis ini?')">
                    @csrf
                    <input type="hidden" name="status" value="waiting_part">
                    <button class="action-btn danger"><i class="fas fa-box"></i> Request part tambahan</button>
                </form>
                @endif
                <form method="POST" action="{{ route('mechanic.update-status', $serviceOrder) }}" onsubmit="confirmForm(this, 'Yakin ingin menandai servis ini selesai?')">
                    @csrf
                    <input type="hidden" name="status" value="done">
                    <button class="action-btn primary"><i class="fas fa-check-circle"></i> Tandai selesai</button>
                </form>
                @else
                <p style="text-align:center;color:var(--text-muted);font-size:14px;padding:20px 0;">Servis ini sudah selesai</p>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection

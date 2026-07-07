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
    .back-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: none; background: transparent; cursor: pointer; color: #1a1a2e; font-size: 18px; transition: background 0.2s; }
    .back-btn:hover { background: var(--surface-2); }
    .info-box { background: var(--surface-2); border-radius: var(--radius); padding: 1rem; margin-bottom: 1rem; }
    .info-box .row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .info-box .vehicle { font-size: 15px; font-weight: 500; color: #1a1a2e; margin: 0; }
    .info-box .detail { font-size: 13px; color: var(--text-secondary); margin: 0 0 4px; }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 500; }
    .badge-queued { background: var(--surface-2); color: var(--text-secondary); }
    .badge-progress { background: var(--bg-danger); color: var(--text-danger); }
    .badge-waiting { background: var(--bg-warning); color: var(--text-warning); }
    .badge-done { background: var(--bg-success); color: var(--text-success); }
    .badge-picked { background: #e0e7ff; color: #3730a3; }
    .section-title { font-size: 13px; font-weight: 600; color: var(--text-secondary); margin: 0 0 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .checklist-item {
        display: flex; align-items: flex-start; gap: 10px; padding: 10px 4px;
        border-bottom: 0.5px solid var(--border);
    }
    .checklist-item:last-child { border-bottom: none; }
    .checklist-item input[type="checkbox"] { width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0; }
    .checklist-item .item-body { flex: 1; }
    .checklist-item .item-name { font-size: 14px; color: #1a1a2e; }
    .checklist-item .item-notes { width: 100%; margin-top: 6px; padding: 6px 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 12px; outline: none; }
    .checklist-item .item-notes:focus { border-color: var(--fill-primary); }
    .photo-area { display: flex; gap: 8px; margin-bottom: 1.25rem; flex-wrap: wrap; }
    .photo-box { width: 64px; height: 64px; border-radius: var(--radius); background: var(--surface-2); display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--text-muted); overflow: hidden; }
    .photo-box img { width: 100%; height: 100%; object-fit: cover; }
    .photo-add {
        width: 64px; height: 64px; padding: 0; border-radius: var(--radius);
        display: flex; align-items: center; justify-content: center;
        border: 2px dashed var(--border); background: transparent; cursor: pointer;
        font-size: 20px; color: var(--text-muted); position: relative; overflow: hidden;
    }
    .photo-add input[type="file"] { position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
    .action-btn { width: 100%; padding: 12px; text-align: center; border-radius: var(--radius); font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; }
    .action-btn.primary { background: var(--fill-primary); color: var(--on-primary); }
    .action-btn.primary:hover { background: #1a1a2e; }
    .action-btn.danger { background: var(--bg-warning); color: var(--text-warning); }
    .action-btn.danger:hover { background: #fde68a; }
    .action-btn.success { background: var(--bg-success); color: var(--text-success); }
    .action-btn.success:hover { background: #a7f3d0; }
    .items-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1rem; }
    .item-row { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: var(--surface-2); border-radius: 8px; font-size: 14px; }
    .item-row .icon { font-size: 16px; width: 24px; text-align: center; }
    .item-row .name { flex: 1; color: #1a1a2e; }
    .item-row .qty { color: var(--text-secondary); font-size: 13px; white-space: nowrap; }
    .item-row .icon-jasa { color: var(--fill-primary); }
    .item-row .icon-part { color: var(--text-warning); }
    .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
    .alert-success { background: var(--bg-success); color: var(--text-success); border: 1px solid #a7f3d0; }
    .preview-img { display: none; }

    @media (min-width: 769px) {
        .detail-container { max-width: 600px; padding: 30px 20px; }
        .info-box { padding: 1.25rem; }
        .checklist { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
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

            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif

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
                <p class="detail"><strong>Pelanggan:</strong> {{ $serviceOrder->customer->name ?? '-' }} · Masuk {{ $serviceOrder->created_at->format('H:i') }}</p>
                @if ($serviceOrder->estimated_finish)
                <p class="detail"><strong>Target:</strong> {{ date('d/m/Y H:i', strtotime($serviceOrder->estimated_finish)) }}</p>
                @endif
            </div>

            @if ($serviceOrder->details->isNotEmpty())
            <p class="section-title">Item pekerjaan</p>
            <div class="items-list">
                @foreach ($serviceOrder->details as $d)
                @php $name = $d->type === 'jasa' ? ($serviceTypes[$d->item_id] ?? 'Jasa #'.$d->item_id) : ($spareparts[$d->item_id] ?? 'Part #'.$d->item_id); @endphp
                <div class="item-row">
                    <span class="icon icon-{{ $d->type }}">{{ $d->type === 'jasa' ? '🔧' : '🔩' }}</span>
                    <span class="name">{{ $name }}</span>
                    <span class="qty">×{{ $d->qty }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('mechanic.save-progress', $serviceOrder) }}" enctype="multipart/form-data" id="progressForm">
                @csrf
                <input type="hidden" name="status" id="actionStatus" value="{{ $serviceOrder->status }}">

                <p class="section-title">Checklist pengecekan</p>
                <div class="checklist" style="margin-bottom:1.25rem;">
                    @forelse ($masterItems as $item)
                    @php $existing = $existingChecklist->get($item->id); $isChecked = $existing && $existing->is_checked; @endphp
                    <div class="checklist-item">
                        <input type="checkbox" {{ $isChecked ? 'checked' : '' }} onchange="document.getElementById('chk_{{ $item->id }}').value = this.checked ? '1' : '0'">
                        <input type="hidden" name="checklist[{{ $item->id }}][checked]" id="chk_{{ $item->id }}" value="{{ $isChecked ? '1' : '0' }}">
                        <input type="hidden" name="checklist[{{ $item->id }}][id]" value="{{ $item->id }}">
                        <div class="item-body">
                            <div class="item-name">{{ $item->name }}</div>
                            <input type="text" name="checklist[{{ $item->id }}][notes]" class="item-notes" placeholder="Catatan (opsional)" value="{{ $existing->notes ?? '' }}">
                        </div>
                    </div>
                    @empty
                    <p style="color:var(--text-muted);font-size:13px;">Belum ada item checklist. Admin perlu menambahkan checklist item terlebih dahulu.</p>
                    @endforelse
                </div>

                <p class="section-title">Foto kondisi kendaraan</p>
                <div class="photo-area" id="photoArea">
                    @foreach ($photos as $photo)
                    <div class="photo-box" onclick="openLightbox('{{ $photo->photo_url }}')" style="cursor:pointer;"><img src="{{ $photo->photo_url }}" alt="{{ $photo->caption ?? '' }}"></div>
                    @endforeach
                    <div class="photo-add">
                        <i class="fas fa-camera"></i>
                        <input type="file" name="photos[]" accept="image/*" multiple onchange="previewPhotos(this)">
                    </div>
                </div>

                <div id="photoPreview" class="photo-area"></div>

                <div style="display:flex;flex-direction:column;gap:8px;">
                    @if ($serviceOrder->status === 'queued')
                    <button type="button" class="action-btn primary" onclick="submitWithStatus('in_progress')"><i class="fas fa-play"></i> Mulai Kerjakan</button>
                    @endif

                    @if (in_array($serviceOrder->status, ['in_progress', 'waiting_part']))
                    <button type="button" class="action-btn success" onclick="submitWithStatus('{{ $serviceOrder->status }}')"><i class="fas fa-save"></i> Simpan Progress</button>
                    <button type="button" class="action-btn danger" onclick="submitWithStatus('waiting_part')"><i class="fas fa-box"></i> Request Part</button>
                    <button type="button" class="action-btn primary" onclick="confirmForm(event, 'Yakin ingin menandai servis ini selesai?', 'done')"><i class="fas fa-check-circle"></i> Tandai Selesai</button>
                    @endif

                    @if (in_array($serviceOrder->status, ['done', 'picked_up']))
                    <p style="text-align:center;color:var(--text-muted);font-size:14px;padding:20px 0;">Servis ini sudah selesai</p>
                    @endif
                </div>
            </form>
        </div>
    </main>
</div>

<div class="modal-overlay" id="lightboxModal" onclick="closeLightbox()" style="z-index:10000;">
    <div style="position:relative;max-width:90vw;max-height:90vh;display:flex;align-items:center;justify-content:center;">
        <img id="lightboxImg" src="" style="max-width:100%;max-height:90vh;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
        <button onclick="closeLightbox()" style="position:absolute;top:-40px;right:0;background:none;border:none;color:#fff;font-size:28px;cursor:pointer;">&times;</button>
    </div>
</div>

<script>
function openLightbox(url) {
    document.getElementById('lightboxImg').src = url;
    document.getElementById('lightboxModal').classList.add('active');
}
function closeLightbox() {
    document.getElementById('lightboxModal').classList.remove('active');
}
function submitWithStatus(status) {
    document.getElementById('actionStatus').value = status;
    document.getElementById('progressForm').submit();
}
function confirmForm(event, message, status) {
    event.preventDefault();
    showConfirmModal(message, function() { submitWithStatus(status); });
}
function previewPhotos(input) {
    const container = document.getElementById('photoPreview');
    container.innerHTML = '';
    for (const file of input.files) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'photo-box';
            div.innerHTML = '<img src="' + e.target.result + '">';
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection

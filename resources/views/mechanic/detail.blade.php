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

                @if ($partRequests->isNotEmpty())
                <p class="section-title">Part Request</p>
                <div style="margin-bottom:1.25rem;">
                    @foreach ($partRequests as $pr)
                    <div style="background:var(--surface-2);border-radius:8px;padding:10px 12px;margin-bottom:8px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                            <span style="font-size:13px;font-weight:500;">Request #{{ $pr->id }}</span>
                            <span class="badge badge-{{ $pr->status }}">{{ $pr->status === 'requested' ? 'Menunggu' : ($pr->status === 'partial' ? 'Sebagian' : ($pr->status === 'fulfilled' ? 'Terpenuhi' : 'Diretur')) }}</span>
                        </div>
                        @foreach ($pr->details as $d)
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#666;padding:4px 0;">
                            <span>{{ $d->sparepart->name ?? 'Part #'.$d->part_id }}</span>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span>{{ $d->qty_fulfilled }}/{{ $d->qty_requested }} · <span class="badge badge-{{ $d->status }}" style="font-size:10px;">{{ $d->status === 'pending' ? 'Pending' : ($d->status === 'fulfilled' ? 'Tersedia' : ($d->status === 'partial' ? 'Sebagian' : ($d->status === 'returned' ? 'Diretur' : $d->status))) }}</span></span>
                                @if (in_array($d->status, ['fulfilled', 'partial']))
                                <button type="button" onclick="openReturnModal({{ $d->id }}, '{{ $d->sparepart->name ?? 'Part' }}', {{ $d->qty_fulfilled - $d->qty_returned }})" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:14px;padding:0;" title="Kembalikan part"><i class="fas fa-undo"></i></button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endif

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
                    <button type="button" class="action-btn danger" onclick="openPartRequestModal()"><i class="fas fa-box"></i> Request Part</button>
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

<div class="modal-overlay" id="partRequestModal">
    <div class="modal-box" style="text-align:left;max-width:500px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:18px;color:#1a1a2e;margin:0;">Request Part</h3>
            <button onclick="closePartRequestModal()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#666;">&times;</button>
        </div>
        <form method="POST" action="{{ route('mechanic.request-part', $serviceOrder) }}">
            @csrf
            <div id="partRows">
                <div class="part-row" style="display:flex;gap:8px;margin-bottom:10px;">
                    <select name="parts[0][part_id]" class="form-control" style="flex:2;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:13px;" required>
                        <option value="">-- Pilih Part --</option>
                        @foreach ($spareparts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="parts[0][qty]" class="form-control" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:13px;" placeholder="Qty" min="1" value="1" required>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:18px;">&times;</button>
                </div>
            </div>
            <button type="button" class="action-btn primary" style="margin-bottom:12px;padding:8px;" onclick="addPartRow()"><i class="fas fa-plus"></i> Tambah Part</button>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="action-btn primary" style="flex:1;">Kirim Request</button>
                <button type="button" class="action-btn secondary" style="flex:1;background:#e2e8f0;color:#475569;" onclick="closePartRequestModal()">Batal</button>
            </div>
        </form>
    </div>
</div>
<div class="modal-overlay" id="returnModal">
    <div class="modal-box" style="text-align:left;max-width:420px;padding:28px 24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:40px;height:40px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#92400e;font-size:18px;"><i class="fas fa-undo-alt"></i></div>
                <div>
                    <h3 style="font-size:16px;color:#1a1a2e;margin:0;font-weight:600;">Kembalikan Part</h3>
                    <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Retur part yang sudah di-fulfill</p>
                </div>
            </div>
            <button onclick="closeReturnModal()" style="width:32px;height:32px;border-radius:50%;border:none;background:#f0f2f5;font-size:16px;cursor:pointer;color:#666;display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>
        <form id="returnForm" method="POST" action="">
            @csrf
            <div style="background:#f8fafc;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:14px;color:#1a1a2e;font-weight:500;" id="returnPartName"></span>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:8px;">Jumlah dikembalikan</label>
                <div style="display:flex;align-items:center;gap:6px;">
                    <button type="button" onclick="let q=document.getElementById('returnQty');if(q.value>1)q.value--" style="width:36px;height:36px;border-radius:8px;border:1px solid #ddd;background:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#475569;">−</button>
                    <input type="number" id="returnQty" name="qty_returned" min="1" required style="flex:1;text-align:center;padding:8px;border:1px solid #ddd;border-radius:8px;font-size:16px;font-weight:600;outline:none;">
                    <button type="button" onclick="let q=document.getElementById('returnQty');if(q.value<q.max)q.value++" style="width:36px;height:36px;border-radius:8px;border:1px solid #ddd;background:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#475569;">+</button>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:8px;">Alasan retur</label>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <label class="reason-option" data-idx="0" onclick="pickReason(0)">
                        <input type="radio" name="reason" value="tidak_cocok" checked>
                        <div><strong style="font-size:14px;">Tidak cocok</strong><br><span style="font-size:12px;color:#64748b;">Part tidak sesuai, perlu diganti dengan part lain</span></div>
                    </label>
                    <label class="reason-option" data-idx="1" onclick="pickReason(1)">
                        <input type="radio" name="reason" value="tidak_dipakai">
                        <div><strong style="font-size:14px;">Tidak dipakai</strong><br><span style="font-size:12px;color:#64748b;">Part tidak jadi digunakan, stok dikembalikan ke gudang</span></div>
                    </label>
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" style="flex:1;padding:12px;border-radius:10px;border:none;background:#0f3460;color:#fff;font-size:14px;font-weight:600;cursor:pointer;"><i class="fas fa-paper-plane"></i> Kirim Retur</button>
                <button type="button" onclick="closeReturnModal()" style="flex:1;padding:12px;border-radius:10px;border:1px solid #ddd;background:#fff;color:#475569;font-size:14px;font-weight:500;cursor:pointer;">Batal</button>
            </div>
        </form>
    </div>
</div>
<style>
.modal-box .form-control { width:100%; }
.part-row select, .part-row input { width:auto; }
.reason-option { display:flex;align-items:center;gap:10px;padding:12px 14px;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all 0.2s; }
.reason-option:hover { border-color:#0f3460; }
.reason-option.active { border-color:#0f3460;background:#f8fafc; }
.reason-option input[type="radio"] { width:16px;height:16px;pointer-events:none; }
</style>

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
let partRowIndex = 1;
function openPartRequestModal() { document.getElementById('partRequestModal').classList.add('active'); }
function closePartRequestModal() { document.getElementById('partRequestModal').classList.remove('active'); }
function openReturnModal(detailId, partName, maxQty) {
    document.getElementById('returnForm').action = '/mechanic/part-detail/' + detailId + '/return';
    document.getElementById('returnPartName').textContent = '× ' + maxQty + ' ' + partName;
    document.getElementById('returnQty').max = maxQty;
    document.getElementById('returnQty').value = maxQty;
    document.getElementById('returnModal').classList.add('active');
    pickReason(0);
}
function pickReason(idx) {
    document.querySelectorAll('.reason-option').forEach((l, i) => {
        l.classList.toggle('active', i === idx);
        l.querySelector('input[type="radio"]').checked = i === idx;
    });
}
function closeReturnModal() { document.getElementById('returnModal').classList.remove('active'); }
document.getElementById('returnModal').addEventListener('click', function(e) { if (e.target === this) closeReturnModal(); });
function addPartRow() {
    const container = document.getElementById('partRows');
    const row = document.createElement('div');
    row.className = 'part-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:10px;';
    row.innerHTML = `
        <select name="parts[${partRowIndex}][part_id]" class="form-control" style="flex:2;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:13px;" required>
            <option value="">-- Pilih Part --</option>
            @foreach ($spareparts as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <input type="number" name="parts[${partRowIndex}][qty]" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:13px;" placeholder="Qty" min="1" value="1" required>
        <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:18px;">&times;</button>
    `;
    container.appendChild(row);
    partRowIndex++;
}
document.getElementById('partRequestModal').addEventListener('click', function(e) { if (e.target === this) closePartRequestModal(); });
</script>
@endsection

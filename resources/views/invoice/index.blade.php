@extends('layouts.app')

@section('title', 'Invoice')

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
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-paid { background: #d1fae5; color: #065f46; }
    .badge-partial { background: #dbeafe; color: #1e40af; }
    .text-right { text-align: right; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-file-invoice-dollar"></i> Invoice</h1>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Baru</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                @if ($data->isEmpty())
                    <div style="text-align:center;padding:60px 20px;color:#94a3b8;"><i class="fas fa-file-invoice-dollar" style="font-size:48px;margin-bottom:16px;"></i><h3 style="color:#475569;">Belum ada invoice</h3><p style="margin-bottom:20px;">Buat invoice dari service order.</p><a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Invoice</a></div>
                @else
                    <table>
                        <thead><tr><th>#</th><th>Service Order</th><th>Pelanggan</th><th style="text-align:right;">Total</th><th>Metode Bayar</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($data as $i => $inv)
                            <tr>
                                <td>INV-{{ str_pad($inv->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>SO-{{ str_pad($inv->order_id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $inv->order->customer->name ?? '-' }}</td>
                                <td class="text-right">{{ number_format($inv->total_amount - $inv->discount, 0) }}</td>
                                <td>{{ $inv->paymentMethod->name ?? '-' }}</td>
                                <td><span class="badge badge-{{ $inv->payment_status }}">{{ $inv->payment_status === 'paid' ? 'Lunas' : ($inv->payment_status === 'partial' ? 'Cicil' : 'Pending') }}</span></td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" onsubmit="confirmForm(this, 'Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
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

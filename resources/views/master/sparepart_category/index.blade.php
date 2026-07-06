@extends('layouts.app')

@section('title', 'Kategori Sparepart')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 24px; color: #1a1a2e; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-success { background: #059669; color: #fff; }
    .btn-success:hover { background: #047857; }
    .btn-warning { background: #d97706; color: #fff; }
    .btn-warning:hover { background: #b45309; }
    .btn-danger { background: #dc2626; color: #fff; }
    .btn-danger:hover { background: #b91c1c; }
    .btn-sm { padding: 6px 12px; font-size: 12px; }
    .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .alert-error { background: #fde8e8; color: #991b1b; border: 1px solid #f8c0c0; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; }
    tr:hover td { background: #f8fafc; }
    .actions { display: flex; gap: 6px; }
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state i { font-size: 48px; color: #94a3b8; margin-bottom: 16px; }
    .empty-state h3 { font-size: 18px; color: #475569; margin-bottom: 8px; }
    .empty-state p { color: #94a3b8; margin-bottom: 20px; font-size: 14px; }
    .empty-actions { display: flex; gap: 12px; justify-content: center; }
    .btn-outline { background: transparent; border: 1px solid #0f3460; color: #0f3460; }
    .btn-outline:hover { background: #0f3460; color: #fff; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tags"></i> Kategori Sparepart</h1>
            <a href="{{ route('sparepart-categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                @if ($data->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-tags"></i>
                        <h3>Belum ada kategori sparepart</h3>
                        <p>Tambahkan kategori sparepart atau isi dengan data default.</p>
                        <div class="empty-actions">
                            <form method="POST" action="{{ url('master/seed/sparepart-categories') }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-success"><i class="fas fa-database"></i> Isi Data Default</button>
                            </form>
                            <a href="{{ route('sparepart-categories.create') }}" class="btn btn-outline"><i class="fas fa-plus"></i> Input Manual</a>
                        </div>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->description ?? '-' }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('sparepart-categories.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('sparepart-categories.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
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

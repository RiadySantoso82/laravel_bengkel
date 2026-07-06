@extends('layouts.app')

@section('title', 'Pelanggan')

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
    .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; }
    tr:hover td { background: #f8fafc; }
    .actions { display: flex; gap: 6px; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> Pelanggan</h1>
            <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                @if ($data->isEmpty())
                    <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
                        <i class="fas fa-users" style="font-size:48px;margin-bottom:16px;"></i>
                        <h3 style="color:#475569;margin-bottom:8px;">Belum ada data pelanggan</h3>
                        <p style="margin-bottom:24px;font-size:14px;">Isi dengan data demo atau input manual.</p>
                        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-bottom:20px;">
                            <form method="POST" action="{{ route('customers.demo') }}" style="display:inline;">
                                @csrf <input type="hidden" name="count" value="5">
                                <button class="btn btn-success" style="background:#059669;color:#fff;"><i class="fas fa-database"></i> Demo 5</button>
                            </form>
                            <form method="POST" action="{{ route('customers.demo') }}" style="display:inline;">
                                @csrf <input type="hidden" name="count" value="10">
                                <button class="btn btn-success" style="background:#059669;color:#fff;"><i class="fas fa-database"></i> Demo 10</button>
                            </form>
                            <form method="POST" action="{{ route('customers.demo') }}" style="display:inline;">
                                @csrf <input type="hidden" name="count" value="20">
                                <button class="btn btn-success" style="background:#059669;color:#fff;"><i class="fas fa-database"></i> Demo 20</button>
                            </form>
                        </div>
                        <a href="{{ route('customers.create') }}" class="btn btn-primary" style="background:#0f3460;color:#fff;"><i class="fas fa-plus"></i> Input Manual</a>
                    </div>
                @else
                    <table>
                        <thead><tr><th>No</th><th>Nama</th><th>Telepon</th><th>Email</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @foreach ($data as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->phone ?? '-' }}</td>
                                <td>{{ $item->email ?? '-' }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('customers.edit', $item) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('customers.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
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

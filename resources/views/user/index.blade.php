@extends('layouts.app')

@section('title', 'User')

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
    .alert-error { background: #fde8e8; color: #991b1b; border: 1px solid #f8c0c0; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .card-body { padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    th { background: #f8fafc; font-weight: 600; color: #475569; }
    tr:hover td { background: #f8fafc; }
    .actions { display: flex; gap: 6px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-admin { background: #fef3c7; color: #92400e; }
    .badge-kasir { background: #dbeafe; color: #1e40af; }
    .badge-mekanik { background: #d1fae5; color: #065f46; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-users-cog"></i> User</h1>
            <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                <table>
                    <thead><tr><th>No</th><th>Username</th><th>Nama</th><th>Email</th><th>Role</th><th>Link Mekanik</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($data as $i => $u)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $u->username }}</strong></td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="badge badge-{{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                            <td>{{ $u->mechanic->name ?? ($u->role === 'mekanik' ? 'Belum di-link' : '-') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('users.edit', $u) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    @if ($u->id !== Auth::id())
                                    <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="confirmForm(this, 'Hapus user ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection

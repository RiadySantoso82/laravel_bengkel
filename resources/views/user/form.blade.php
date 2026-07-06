@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding: 30px; max-width: 600px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.3s; }
    .form-control:focus { border-color: #0f3460; }
    select.form-control { background: #fff; }
    .row { display: flex; gap: 16px; }
    .row .form-group { flex: 1; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .btn-secondary:hover { background: #cbd5e1; }
    .form-actions { display: flex; gap: 10px; margin-top: 24px; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-users-cog"></i> {{ isset($user) ? 'Edit User' : 'Tambah User' }}</h1></div>
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}">
                @csrf @if (isset($user)) @method('PUT') @endif

                <div class="row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="kasir" {{ old('role', $user->role ?? '') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                            <option value="mekanik" {{ old('role', $user->role ?? '') === 'mekanik' ? 'selected' : '' }}>Mekanik</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                </div>

                <div class="form-group" id="mechanic-group" style="{{ old('role', $user->role ?? '') === 'mekanik' ? '' : 'display:none' }}">
                    <label for="mechanic_id">Link ke Mekanik</label>
                    <select id="mechanic_id" name="mechanic_id" class="form-control">
                        <option value="">-- Buat Mekanik Baru --</option>
                        @foreach ($mechanics as $m)
                            <option value="{{ $m->id }}" {{ old('mechanic_id', $user->mechanic->id ?? '') == $m->id ? 'selected' : '' }}>{{ $m->name }} {{ $m->specialization ? '(' . $m->specialization . ')' : '' }}</option>
                        @endforeach
                    </select>
                    <small style="color:#888;font-size:12px;">Pilih mekanik yang sudah ada atau biarkan kosong untuk membuat mekanik baru dengan nama yang sama.</small>
                </div>

                <div class="form-group">
                    <label for="password">{{ isset($user) ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}</label>
                    <input type="password" id="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div></div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('role').addEventListener('change', function() {
    document.getElementById('mechanic-group').style.display = this.value === 'mekanik' ? '' : 'none';
});
</script>
@endpush

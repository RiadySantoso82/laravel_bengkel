@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    .page-header h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card-body { padding: 30px; max-width: 600px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 14px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.3s; }
    .form-control:focus { border-color: #0f3460; }
    .form-control[readonly] { background: #f8fafc; color: #666; cursor: not-allowed; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary { background: #0f3460; color: #fff; }
    .btn-primary:hover { background: #1a1a2e; }
    .btn-secondary { background: #e2e8f0; color: #475569; }
    .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-error { background: #fde8e8; color: #991b1b; border: 1px solid #f8c0c0; }
    .section-title { font-size: 15px; font-weight: 600; color: #1a1a2e; margin: 24px 0 16px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
    .row { display: flex; gap: 12px; }
    .row .form-group { flex: 1; }
</style>
@endpush

@section('content')
<div class="dashboard">
    @include('layouts.sidebar')
    <main class="main-content">
        <div class="page-header"><h1><i class="fas fa-user-circle"></i> Profil Saya</h1></div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
        @endif
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('mechanic.update-profile') }}">
                @csrf

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->username }}" readonly>
                </div>
                <div class="form-group">
                    <label>Spesialisasi</label>
                    <input type="text" class="form-control" value="{{ $mechanic->specialization ?? '-' }}" readonly>
                </div>
                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $mechanic->phone ?? '') }}">
                </div>

                <div class="section-title"><i class="fas fa-key"></i> Ganti Password</div>

                <div class="form-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Masukkan password saat ini">
                </div>
                <div class="row">
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="form-actions" style="margin-top:24px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div></div>
    </main>
</div>
@endsection

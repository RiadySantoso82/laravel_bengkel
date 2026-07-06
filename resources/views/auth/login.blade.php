@extends('layouts.app')

@section('title', 'Login - Bengkel')

@push('styles')
<style>
    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    }
    .login-card {
        background: #fff;
        border-radius: 12px;
        padding: 40px;
        width: 400px;
        max-width: 100%;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    .login-card .logo {
        text-align: center;
        margin-bottom: 30px;
    }
    .login-card .logo i {
        font-size: 48px;
        color: #0f3460;
    }
    .login-card .logo h1 {
        font-size: 24px;
        color: #1a1a2e;
        margin-top: 10px;
    }
    .login-card .logo p {
        color: #666;
        font-size: 14px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    .input-group {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        transition: border-color 0.3s;
    }
    .input-group:focus-within {
        border-color: #0f3460;
    }
    .input-group i {
        padding: 12px 14px;
        color: #999;
        font-size: 16px;
        min-width: 42px;
        text-align: center;
    }
    .input-group input {
        flex: 1;
        border: none;
        outline: none;
        padding: 12px 14px 12px 0;
        font-size: 14px;
    }
    .btn-login {
        width: 100%;
        padding: 14px;
        background: #0f3460;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-login:hover {
        background: #1a1a2e;
    }
    .btn-login i {
        margin-right: 8px;
    }
    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    .alert-error {
        background: #fde8e8;
        color: #c53030;
        border: 1px solid #f8c0c0;
    }
</style>
@endpush

@section('content')
<div class="login-wrapper">
    <div class="login-card">
        <div class="logo">
            <i class="fas fa-car-side"></i>
            <h1>Bengkel</h1>
            <p>Sistem Manajemen Bengkel</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password">
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</div>
@endsection

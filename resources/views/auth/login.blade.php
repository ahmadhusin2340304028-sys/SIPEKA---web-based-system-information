@extends('layouts.app')
@section('title', 'Login - SIPEKA')
@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height:100vh; background:linear-gradient(135deg,#1e3c72,#2a5298);">
    <div class="card shadow-lg" style="width:380px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="{{ asset('assets/image/dinsos_logo.png') }}" height="60" alt="">
                <h4 class="mt-2 mb-0">DINSOS - PM</h4>
                <small class="text-muted">Kota Tarakan</small>
            </div>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

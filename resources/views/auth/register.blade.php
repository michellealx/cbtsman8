@extends('layouts.app')

@section('title', 'Register CBT')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Daftar</h2>
                    <p class="text-muted mb-0">Buat akun baru untuk mengakses dashboard sesuai role Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama</label>
                            <input id="name" name="name" type="text" class="form-control form-control-lg" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">NIS (Murid) / Nama Unik (Guru)</label>
                            <input id="username" name="username" type="text" class="form-control form-control-lg" value="{{ old('username') }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" name="password" type="password" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control form-control-lg" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select form-select-lg" required>
                            <option value="murid" selected>Murid</option>
                            <option value="guru">Guru</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">Daftar Sekarang</button>
                </form>

                <p class="text-center text-muted mt-4 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Login CBT')

@section('content')
<!-- Decorative Blobs -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="container min-vh-100 d-flex flex-column justify-content-center py-5 position-relative z-1">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            
            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="text-decoration-none d-inline-block mb-3">
                    <span class="badge bg-white text-primary rounded-pill py-2 px-3 shadow-sm border">
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </span>
                </a>
            </div>

            <!-- Floating Card -->
            <div class="card glass-card rounded-4 border-0 floating-card" style="padding: 10px;">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width:72px; height:72px; background: linear-gradient(135deg, #1152b4, #a855f7); color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                        </div>
                        <h2 class="fw-bold mb-1">Selamat Datang</h2>
                        <p class="text-muted small">Silakan masuk menggunakan NIS atau Nama Anda.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-warning rounded-3 border-0 shadow-sm small">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/login') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="username" class="form-label fw-semibold text-dark">NIS (Siswa) / Nama (Guru)</label>
                            <input id="username" name="username" type="text" class="form-control form-control-lg bg-light border-0" value="{{ old('username') }}" required autofocus placeholder="Masukkan NIS atau Nama" style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-dark">Password</label>
                            <input id="password" name="password" type="password" class="form-control form-control-lg bg-light border-0" required placeholder="••••••••" style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm fw-bold" style="background: linear-gradient(135deg, #1152b4, #0f3460); border:none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                            Masuk <i class="bi bi-box-arrow-in-right ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
            <!-- End Floating Card -->
            
            <p class="text-center text-muted mt-5 small">
                &copy; {{ date('Y') }} SMAN 8 Tangerang Selatan. Hak Cipta Dilindungi.
            </p>

        </div>
    </div>
</div>
@endsection

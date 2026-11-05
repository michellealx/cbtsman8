@extends('layouts.dashboard')

@section('title', 'Daftar Kelas')
@section('page-title', 'Daftar Kelas')

@section('content')
<div class="mb-4">
    <h1 class="h4 mb-1 fw-bold">Daftar Kelas</h1>
    <p class="text-muted mb-0">Lihat semua kelas yang terdaftar di sistem.</p>
</div>

<div class="row g-4 justify-content-center">
    @forelse($classrooms as $class)
        <div class="col-md-5 col-lg-4">
            <a href="{{ route('guru.students', ['kelas' => $class->name]) }}" class="card h-100 border-0 shadow-sm text-decoration-none" style="border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 30px rgba(15, 23, 42, 0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
                <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center position-relative overflow-hidden">
                    <!-- Decorative background element -->
                    <div class="position-absolute" style="top: -20px; right: -20px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(99,102,241,0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%;"></div>
                    
                    <div class="mb-4 position-relative z-1">
                        <div class="d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px; border-radius: 20px; background: linear-gradient(135deg, #f8fafc, #f1f5f9); color: #3b82f6; border: 1px solid #e2e8f0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                            </svg>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mb-2 position-relative z-1" style="font-size: 1.4rem; letter-spacing: -0.5px;">{{ $class->name }}</h5>
                    <p class="text-muted mb-0 position-relative z-1" style="font-size: 1rem;">Lihat Siswa &rarr;</p>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                <div class="card-body text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="mb-3 opacity-50" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                    <h5>Belum ada data kelas</h5>
                    <p class="mb-0">Daftar kelas yang terdaftar akan muncul di sini.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Hasil Ujian')
@section('page-title', 'Hasil Ujian')

@section('content')
<div class="mb-4">
    <h1 class="h4 mb-1 fw-bold">Hasil Ujian</h1>
    <p class="text-muted mb-0">Pilih kelas untuk melihat hasil ujian siswa.</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    @forelse($classrooms as $classroom)
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('guru.results.class', $classroom->id) }}"
            class="card h-100 border-0 shadow-sm text-decoration-none results-card"
            style="border-radius: 16px; transition: all 0.3s ease;">
            <div class="card-body p-4 position-relative overflow-hidden">

                {{-- Decorative circle --}}
                <div class="position-absolute" style="top:-20px;right:-20px;width:120px;height:120px;background:radial-gradient(circle,rgba(99,102,241,0.12) 0%,rgba(255,255,255,0) 70%);border-radius:50%;"></div>

                <div class="d-flex align-items-center mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center shadow-sm me-3 flex-shrink-0"
                        style="width:54px;height:54px;border-radius:14px;background:linear-gradient(135deg,#eff6ff,#dbeafe);color:#3b82f6;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $classroom->name }}</h5>
                        <small class="text-muted">{{ $classroom->exam_count }} ujian</small>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-2">
                    <span class="badge rounded-pill px-3 py-2" style="background:#f0fdf4;color:#16a34a;font-size:0.8rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>{{ $classroom->done_count }} Selesai
                    </span>
                    @if($classroom->pending_count > 0)
                    <span class="badge rounded-pill px-3 py-2" style="background:#fefce8;color:#ca8a04;font-size:0.8rem;">
                        <i class="bi bi-hourglass-split me-1"></i>{{ $classroom->pending_count }} Perlu Dikoreksi
                    </span>
                    @endif
                </div>

                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                    <small class="text-muted">Lihat Ujian</small>
                    <i class="bi bi-chevron-right text-primary"></i>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 text-center py-5 shadow-sm" style="border-radius:16px;">
            <div class="card-body">
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                    style="width:72px;height:72px;background:rgba(99,102,241,0.08);">
                    <i class="bi bi-bar-chart-line fs-2 text-primary"></i>
                </div>
                <h5 class="text-muted fw-semibold">Belum ada data</h5>
                <p class="text-muted mb-0">Data hasil ujian akan muncul setelah Anda menerbitkan ujian ke kelas.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<style>
.results-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(15,23,42,0.10) !important;
}
</style>
@endsection

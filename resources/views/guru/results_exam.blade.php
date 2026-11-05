@extends('layouts.dashboard')

@section('title', 'Hasil: {{ $exam->title }}')
@section('page-title', 'Hasil Ujian')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('guru.results') }}" class="text-decoration-none text-primary">
                <i class="bi bi-bar-chart-line me-1"></i>Hasil Ujian
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('guru.results.class', $classroom->id) }}" class="text-decoration-none text-primary">
                {{ $classroom->name }}
            </a>
        </li>
        <li class="breadcrumb-item active fw-semibold">{{ $exam->title }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">{{ $exam->title }}</h1>
        <p class="text-muted mb-0">{{ $exam->subject }} &bull; Kelas {{ $classroom->name }}</p>
    </div>
    <div class="d-flex gap-2">
        @if($results->where('status', 'pending_grading')->count() > 0)
        <a href="{{ route('guru.exams.grade', $exam->id) }}" class="btn btn-warning fw-bold btn-sm text-dark">
            <i class="bi bi-pencil-square me-1"></i>Koreksi Essay
        </a>
        @endif
        <a href="{{ route('guru.results.class', $classroom->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

{{-- Ringkasan Statistik --}}
@php
    $avg = $results->count() > 0 ? round($results->avg('score'), 1) : 0;
    $highest = $results->count() > 0 ? $results->max('score') : 0;
    $lowest = $results->count() > 0 ? $results->min('score') : 0;
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:14px;">
            <div class="fw-bold fs-3 mb-0" style="color:#3b82f6;">{{ $results->count() }}</div>
            <small class="text-muted">Sudah Mengerjakan</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:14px;">
            <div class="fw-bold fs-3 mb-0" style="color:#f59e0b;">{{ $notDoneStudents->count() }}</div>
            <small class="text-muted">Belum Mengerjakan</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:14px;">
            <div class="fw-bold fs-3 mb-0" style="color:#10b981;">{{ $avg }}</div>
            <small class="text-muted">Rata-rata Nilai</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3" style="border-radius:14px;">
            <div class="fw-bold fs-3 mb-0" style="color:#6366f1;">{{ $highest }}</div>
            <small class="text-muted">Nilai Tertinggi</small>
        </div>
    </div>
</div>

{{-- Search Bar Siswa --}}
<div class="mb-4">
    <div class="input-group shadow-sm" style="border-radius:12px;overflow:hidden;">
        <span class="input-group-text bg-white border-end-0" style="border-radius:12px 0 0 12px;">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text"
            id="searchStudent"
            class="form-control border-start-0 ps-0"
            placeholder="Cari nama siswa..."
            autocomplete="off"
            style="border-radius:0 12px 12px 0;">
    </div>
</div>

{{-- Tabel Siswa yang Sudah Mengerjakan --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-header bg-white border-bottom py-3" style="border-radius:16px 16px 0 0;">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            Sudah Mengerjakan ({{ $results->count() }} siswa)
        </h6>
    </div>
    <div class="card-body p-0">
        @forelse($results as $i => $result)
        <div class="d-flex align-items-center px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }} student-row done-student-row"
            data-name="{{ strtolower($result->student->name) }}">
            {{-- Rank --}}
            <div class="fw-bold me-3 text-muted" style="width:28px;font-size:0.9rem;">
                #{{ $i + 1 }}
            </div>

            {{-- Avatar --}}
            <div class="d-inline-flex align-items-center justify-content-center flex-shrink-0 me-3"
                style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#eff6ff,#dbeafe);color:#3b82f6;font-weight:700;font-size:1rem;">
                {{ strtoupper(substr($result->student->name, 0, 1)) }}
            </div>

            <div class="flex-grow-1">
                <div class="fw-semibold text-dark">{{ $result->student->name }}</div>
                <small class="text-muted">{{ $result->student->username }}</small>
            </div>

            <div class="text-end me-3 d-none d-md-block">
                <small class="text-muted">Selesai</small>
                <div class="small text-dark">{{ $result->finished_at->format('d M Y, H:i') }}</div>
            </div>

            <div class="text-center me-3" style="min-width:80px;">
                @if($result->status === 'pending_grading')
                <span class="badge rounded-pill px-3 py-2" style="background:#fefce8;color:#ca8a04;font-size:0.8rem;">
                    <i class="bi bi-hourglass-split me-1"></i>Belum Dikoreksi
                </span>
                @else
                <span class="fw-bold fs-5" style="color:#16a34a;">{{ $result->score }}</span>
                @endif
            </div>

            <a href="{{ route('guru.exams.grade', $exam->id) }}"
                class="btn btn-sm {{ $result->status === 'pending_grading' ? 'btn-warning text-dark' : 'btn-outline-primary' }}">
                <i class="bi bi-{{ $result->status === 'pending_grading' ? 'pencil-square' : 'eye' }} me-1"></i>
                {{ $result->status === 'pending_grading' ? 'Koreksi' : 'Detail' }}
            </a>
        </div>
        @empty
        <div class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
            Belum ada siswa yang mengerjakan ujian ini.
        </div>
        @endforelse
    </div>
</div>

{{-- Tabel Siswa yang Belum Mengerjakan --}}
@if($notDoneStudents->count() > 0)
<div class="card border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-header bg-white border-bottom py-3" style="border-radius:16px 16px 0 0;">
        <h6 class="fw-bold mb-0 text-muted">
            <i class="bi bi-hourglass text-warning me-2"></i>
            Belum Mengerjakan ({{ $notDoneStudents->count() }} siswa)
        </h6>
    </div>
    <div class="card-body p-0">
        @foreach($notDoneStudents as $student)
        <div class="d-flex align-items-center px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }} notdone-student-row"
            data-name="{{ strtolower($student->name) }}">
            <div class="d-inline-flex align-items-center justify-content-center flex-shrink-0 me-3"
                style="width:40px;height:40px;border-radius:10px;background:#f8fafc;color:#94a3b8;font-weight:700;font-size:1rem;">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold text-muted">{{ $student->name }}</div>
                <small class="text-muted">{{ $student->username }}</small>
            </div>
            <span class="badge rounded-pill px-3 py-2" style="background:#f1f5f9;color:#94a3b8;font-size:0.8rem;">
                <i class="bi bi-dash-circle me-1"></i>Belum mengerjakan
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

<style>
.student-row { transition: background 0.2s ease; }
.student-row:hover { background: #f8fafc; }
</style>
@endsection

@section('scripts')
<script>
(function () {
    const input = document.getElementById('searchStudent');
    if (!input) return;

    input.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();

        // Filter tabel "Sudah Mengerjakan"
        const doneRows = document.querySelectorAll('.done-student-row');
        doneRows.forEach(function (row) {
            const name = row.dataset.name || '';
            row.style.display = name.includes(keyword) ? '' : 'none';
        });

        // Filter tabel "Belum Mengerjakan"
        const notDoneRows = document.querySelectorAll('.notdone-student-row');
        notDoneRows.forEach(function (row) {
            const name = row.dataset.name || '';
            row.style.display = name.includes(keyword) ? '' : 'none';
        });
    });
})();
</script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Bank Soal</h1>
        <p class="text-muted mb-0">Arsip ujian yang telah berakhir. Soal-soal ini tersimpan dan dapat dijadikan referensi.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Ujian</th>
                        <th>Mata Pelajaran</th>
                        <th>Deadline Berakhir</th>
                        <th>Jumlah Soal</th>
                        <th>Pengerjaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $index => $exam)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $exam->title }}</div>
                            <small class="text-muted">{{ $exam->duration_minutes }} menit</small>
                        </td>
                        <td>{{ $exam->subject }}</td>
                        <td>
                            <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold" style="font-size: 0.8rem;">
                                <i class="bi bi-calendar-x me-1"></i>{{ $exam->deadline->format('d M Y, H:i') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $exam->questions_count }} Soal
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success">
                                {{ $exam->results_count }} Murid
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('guru.exams.duplicate', $exam->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary shadow-sm">
                                    <i class="bi bi-files me-1"></i>Gunakan Ulang Soal
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-archive d-block fs-1 mb-3 opacity-25"></i>
                                <h6 class="fw-semibold">Belum Ada Ujian di Bank Soal</h6>
                                <p class="mb-0 small">Ujian yang sudah melewati batas deadline akan otomatis muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

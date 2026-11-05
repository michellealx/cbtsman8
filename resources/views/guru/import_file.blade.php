@extends('layouts.dashboard')

@section('title', 'Import Soal Ujian')
@section('page-title', 'Import Soal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Import Soal dari File</h1>
        <p class="text-muted mb-0">Unggah file Word (.docx) atau PDF untuk membuat soal otomatis.</p>
    </div>
    <a href="{{ route('guru.exams') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="card border-0">
    <div class="card-body">
        <form action="{{ route('guru.exams.store_import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h5 class="fw-bold mb-3">Detail Ujian</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Ujian</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Ujian Akhir Semester">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <option value="1">Bahasa Indonesia</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Durasi (Menit)</label>
                    <input type="number" name="duration" class="form-control" required placeholder="Contoh: 120">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Batas Waktu (Deadline) Ujian</label>
                    <input type="datetime-local" name="deadline" class="form-control" value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" required>
                </div>

            </div>

            <hr>

            <h5 class="fw-bold mb-3 mt-4">Upload File Soal</h5>
            <div class="alert alert-info border-0 d-flex align-items-start">
                <i class="bi bi-info-circle-fill me-3 fs-5 mt-1"></i>
                <div>
                    <strong>Format Penulisan Soal:</strong><br>
                    Untuk pilihan ganda, pastikan soal menggunakan penomoran standar (1., 2., dll) dan opsi jawaban menggunakan abjad (A., B., C., D.). <br>
                    <a href="#" class="alert-link text-decoration-underline">Unduh Template Contoh (Word)</a>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Pilih File (.docx atau .pdf)</label>
                <input type="file" name="file" class="form-control" accept=".docx,.pdf" required>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4">Import dan Proses Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection

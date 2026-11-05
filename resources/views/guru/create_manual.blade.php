@extends('layouts.dashboard')

@section('title', 'Buat Ujian')
@section('page-title', 'Buat Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Buat Ujian Baru</h1>
        <p class="text-muted mb-0">Masukkan detail ujian dan tambahkan soal.</p>
    </div>
    <a href="{{ route('guru.exams') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="card border-0">
    <div class="card-body">
        <form action="{{ route('guru.exams.store_manual') }}" method="POST">
            @csrf
            
            <h5 class="fw-bold mb-3">Detail Ujian</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Ujian</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Ujian Tengah Semester">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Pelajaran</label>
                    <input type="text" name="subject_id" class="form-control" required
                        list="subjects-datalist"
                        placeholder="Ketik atau pilih mata pelajaran..."
                        value="{{ old('subject_id') }}">
                    <datalist id="subjects-datalist">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->name }}">
                        @endforeach
                    </datalist>
                    <div class="form-text">Ketik nama mata pelajaran baru atau pilih dari daftar yang sudah ada.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Durasi (Menit)</label>
                    <input type="number" name="duration" class="form-control" required placeholder="Contoh: 90">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Batas Waktu (Deadline) Ujian</label>
                    <input type="datetime-local" name="deadline" class="form-control" value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="randomizeQuestions" name="randomize_questions" value="1" checked>
                        <label class="form-check-label fw-bold" for="randomizeQuestions">Acak Urutan Soal</label>
                        <div class="form-text">Jika diaktifkan, urutan soal akan diacak secara berbeda untuk setiap siswa (tetap memprioritaskan soal Pilihan Ganda lalu Essay).</div>
                    </div>

                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4">Lanjut: Tambah Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection

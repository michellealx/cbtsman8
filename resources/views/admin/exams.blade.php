@extends('layouts.dashboard')

@section('title', 'Kelola Ujian')
@section('page-title', 'Kelola Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Ujian</h1>
        <p class="text-muted mb-0">Atur ujian baru dan jadwalkan pelaksanaan.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addExamModal">
        <i class="bi bi-plus-lg me-1"></i> Buat Ujian Baru
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Judul Ujian</th>
                        <th>Mata Pelajaran</th>
                        <th>Pengampu (Guru)</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exams as $exam)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $exam->title }}</div>
                                @if($exam->start_time)
                                    <div class="small text-muted">Jadwal: {{ $exam->start_time->format('d M Y, H:i') }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark rounded-pill">{{ $exam->subject }}</span>
                            </td>
                            <td>{{ $exam->teacher->name ?? '-' }}</td>
                            <td>{{ $exam->duration_minutes }} Menit</td>
                            <td>
                                @if($exam->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($exam->status === 'published')
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-danger">Closed</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Kelola Soal">
                                        <i class="bi bi-card-list"></i> Soal
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.exams.delete', $exam->id) }}')" title="Hapus Ujian">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="bi bi-folder2-open display-4 text-secondary opacity-50"></i>
                                </div>
                                <h5>Belum Ada Ujian</h5>
                                <p>Silakan klik "Buat Ujian Baru" untuk menambahkan daftar ujian pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Buat Ujian -->
<div class="modal fade" id="addExamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Buat Ujian Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.exams.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Ujian <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="Contoh: Penilaian Tengah Semester Ganjil">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="subject" class="form-select" required>
                            <option value="">Pilih Mata Pelajaran...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Mata pelajaran bisa ditambahkan di menu Data Master -> Mata Pelajaran.</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Durasi (Menit) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control" required min="1" value="60">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Guru Pengampu <span class="text-danger">*</span></label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">Pilih Guru...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jadwal Mulai (Opsional)</label>
                        <input type="datetime-local" name="start_time" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Ujian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus Ujian -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete(url) {
        if (confirm('Apakah Anda yakin ingin menghapus ujian ini? Semua soal yang terkait juga akan terhapus.')) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection

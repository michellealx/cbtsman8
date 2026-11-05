@extends('layouts.dashboard')

@section('title', 'Sesi Ujian')
@section('page-title', 'Manajemen Sesi Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Sesi Ujian</h1>
        <p class="text-muted mb-0">Atur jadwal, kelas, dan token ujian yang akan dikerjakan siswa.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSessionModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Sesi Baru
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Judul Ujian</th>
                        <th>Kelas</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Token</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $index => $session)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">
                                {{ $session->exam->title ?? 'Ujian Dihapus' }}
                                @if($session->exam)
                                    <div class="small fw-normal text-muted">{{ $session->exam->subject }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary rounded-pill">
                                    {{ $session->classroom->name ?? 'Kelas Dihapus' }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <strong>Mulai:</strong> {{ $session->start_time ? $session->start_time->format('d M Y, H:i') : '-' }}<br>
                                    <strong>Selesai:</strong> {{ $session->end_time ? $session->end_time->format('d M Y, H:i') : '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="font-monospace fw-bold fs-5 text-primary bg-primary bg-opacity-10 rounded px-2 py-1 d-inline-block border border-primary border-opacity-25" style="letter-spacing: 2px;">
                                    {{ $session->token }}
                                </div>
                            </td>
                            <td>
                                @if($session->is_active)
                                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('admin.sessions.toggle', $session->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $session->is_active ? 'warning' : 'success' }}" title="{{ $session->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-{{ $session->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.sessions.delete', $session->id) }}')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="bi bi-calendar-x display-4 text-secondary opacity-50"></i>
                                </div>
                                <h5>Belum Ada Sesi Ujian</h5>
                                <p>Siswa tidak dapat mengerjakan ujian sebelum sesi dibuat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Sesi -->
<div class="modal fade" id="addSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Sesi Ujian Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.sessions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Ujian <span class="text-danger">*</span></label>
                        <select name="exam_id" class="form-select" required>
                            <option value="">Pilih Ujian...</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->subject }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Kelas <span class="text-danger">*</span></label>
                        <select name="classroom_id" class="form-select" required>
                            <option value="">Pilih Kelas...</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waktu Selesai</label>
                            <input type="datetime-local" name="end_time" class="form-control">
                        </div>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> Token ujian (5 karakter) akan dibuat secara otomatis saat sesi disimpan.
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Sesi & Generate Token</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete(url) {
        if (confirm('Apakah Anda yakin ingin menghapus sesi ujian ini?')) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection

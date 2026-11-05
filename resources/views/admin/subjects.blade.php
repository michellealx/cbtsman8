@extends('layouts.dashboard')

@section('title', 'Data Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Mata Pelajaran</h1>
        <p class="text-muted mb-0">Kelola master data mata pelajaran untuk ujian.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pelajaran
    </button>
</div>

@if(!empty($currentUser['is_coordinator']) && isset($teachers) && $teachers->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.subjects') }}" method="GET" class="d-flex align-items-center gap-2">
            <label class="fw-semibold text-muted mb-0"><i class="bi bi-filter me-1"></i>Filter Guru:</label>
            <select name="teacher_id" class="form-select form-select-sm" style="max-width: 250px;" onchange="this.form.submit()">
                <option value="">Semua Guru</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ (isset($selectedTeacher) && $selectedTeacher == $t->id) ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            @if(isset($selectedTeacher) && $selectedTeacher)
                <a href="{{ route('admin.subjects') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $index => $subject)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td>
                                @if($subject->code)
                                    <span class="badge bg-secondary rounded-pill">{{ $subject->code }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">{{ $subject->name }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#editSubjectModal{{ $subject->id }}">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" onclick="confirmDelete('{{ route('admin.subjects.delete', $subject->id) }}')">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Subject -->
                        <div class="modal fade" id="editSubjectModal{{ $subject->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Mata Pelajaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Kode Pelajaran (Opsional)</label>
                                                <input type="text" name="code" class="form-control" value="{{ $subject->code }}" placeholder="Misal: MAT">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" required value="{{ $subject->name }}" placeholder="Misal: Matematika Wajib">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="bi bi-book display-4 text-secondary opacity-50"></i>
                                </div>
                                <h5>Data Kosong</h5>
                                <p>Belum ada mata pelajaran yang ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Subject -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Pelajaran (Opsional)</label>
                        <input type="text" name="code" class="form-control" placeholder="Misal: MAT">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Misal: Matematika Wajib">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
        if (confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection

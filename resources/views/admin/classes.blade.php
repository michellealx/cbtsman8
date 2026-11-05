@extends('layouts.dashboard')

@section('title', 'Data Kelas')
@section('page-title', 'Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Kelas</h1>
        <p class="text-muted mb-0">Kelola master data kelas untuk siswa.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addClassModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
    </button>
</div>

@if(!empty($currentUser['is_coordinator']) && isset($teachers) && $teachers->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.classes') }}" method="GET" class="d-flex align-items-center gap-2">
            <label class="fw-semibold text-muted mb-0"><i class="bi bi-filter me-1"></i>Filter Guru:</label>
            <select name="teacher_id" class="form-select form-select-sm" style="max-width: 250px;" onchange="this.form.submit()">
                <option value="">Semua Guru</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ (isset($selectedTeacher) && $selectedTeacher == $t->id) ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            @if(isset($selectedTeacher) && $selectedTeacher)
                <a href="{{ route('admin.classes') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th class="ps-4" style="width: 10%;">No</th>
                        <th>Nama Kelas</th>
                        <th class="pe-4 text-end" style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $index => $classroom)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $classroom->name }}</td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.classes.show', $classroom->id) }}" class="btn btn-sm btn-outline-success" title="Lihat Kelas">
                                        <i class="bi bi-eye"></i> Lihat Kelas
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClassModal{{ $classroom->id }}" title="Edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('admin.classes.delete', $classroom->id) }}')" title="Hapus">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Kelas -->
                        <div class="modal fade" id="editClassModal{{ $classroom->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Kelas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.classes.update', $classroom->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" required value="{{ $classroom->name }}" placeholder="Misal: X MIPA 1">
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
                            <td colspan="3" class="text-center text-muted py-5">
                                <div class="mb-3">
                                    <i class="bi bi-building display-4 text-secondary opacity-50"></i>
                                </div>
                                <h5>Data Kosong</h5>
                                <p>Belum ada data kelas yang ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.classes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Misal: X MIPA 1">
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
        if (confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
            const form = document.getElementById('delete-form');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection

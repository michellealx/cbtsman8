@extends('layouts.dashboard')

@section('title', 'Ujian')
@section('page-title', 'Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Ujian</h1>
        <p class="text-muted mb-0">Pantau dan edit ujian yang Anda buat.</p>
    </div>
    <a href="{{ route('guru.exams.create_manual') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Buat Ujian Baru
    </a>
</div>

<div class="card border-0 mb-4 bg-transparent shadow-none">
    <form action="{{ route('guru.exams') }}" method="GET" class="d-flex max-w-md">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama atau mapel ujian..." value="{{ request('search') }}">
            
            <input type="date" name="date" class="form-control text-muted" style="max-width: 150px;" value="{{ request('date') }}" title="Filter berdasarkan tanggal dibuat">
            
            @if(request('search') || request('date'))
                <a href="{{ route('guru.exams') }}" class="btn btn-outline-secondary" title="Clear Filter">
                    <i class="bi bi-x"></i>
                </a>
            @endif
            <button type="submit" class="btn btn-primary px-4">Cari</button>
        </div>
    </form>
</div>

<div class="card border-0">
    <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Daftar Ujian Anda</h6>
        <button type="submit" form="bulkDeleteForm" class="btn btn-sm btn-danger d-none" id="btnBulkDelete" onclick="return confirm('Apakah Anda yakin ingin menghapus semua ujian yang dipilih? Semua soal dan nilai murid akan ikut terhapus permanen.')">
            <i class="bi bi-trash me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
        </button>
    </div>
    <div class="card-body p-0">
        <form id="bulkDeleteForm" action="{{ route('guru.exams.bulk_destroy') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="ps-4">
                                <input class="form-check-input border-2 border-secondary" type="checkbox" id="selectAll" style="cursor: pointer;">
                            </th>
                            <th>#</th>
                            <th>Nama Ujian</th>
                            <th>Tanggal Dibuat</th>
                            <th>Batas Waktu (Deadline)</th>
                            <th>Jumlah Soal</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $index => $exam)
                        <tr>
                            <td class="ps-4">
                                <input class="form-check-input border-2 border-secondary exam-checkbox" type="checkbox" name="exam_ids[]" value="{{ $exam->id }}" style="cursor: pointer;">
                            </td>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $exam->title }}</div>
                                <small class="text-muted">{{ $exam->subject }}</small>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.85rem;">{{ $exam->created_at->format('d M Y') }}</span>
                            </td>
                            <td>
                                @if($exam->deadline)
                                    <span class="text-danger fw-semibold" style="font-size: 0.85rem;">{{ $exam->deadline->format('d M Y, H:i') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $exam->questions_count }} Soal</td>
                            <td>{{ $exam->duration_minutes }} Menit</td>
                            <td>
                                @if($exam->status == 'published')
                                    <span class="badge bg-success">Published</span>
                                @elseif($exam->status == 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @else
                                    <span class="badge bg-danger">Closed</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('guru.exams.questions', $exam->id) }}" class="btn btn-sm btn-outline-primary">Kelola Soal</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                Anda belum membuat ujian. Klik "Buat Ujian" untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.exam-checkbox');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const selectedCountSpan = document.getElementById('selectedCount');

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.exam-checkbox:checked').length;
        if (checkedCount > 0) {
            btnBulkDelete.classList.remove('d-none');
            selectedCountSpan.textContent = checkedCount;
        } else {
            btnBulkDelete.classList.add('d-none');
        }
        
        if (checkedCount === checkboxes.length && checkboxes.length > 0) {
            selectAll.checked = true;
        } else {
            selectAll.checked = false;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkDeleteButton);
    });
});
</script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Kelola Siswa - ' . $classroom->name)
@section('page-title', 'Kelola Siswa Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Kelola Siswa: {{ $classroom->name }}</h1>
        <p class="text-muted mb-0">Pilih siswa yang akan dimasukkan ke dalam kelas ini.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="input-group" style="width: 250px;">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama atau NIS...">
        </div>
        <a href="{{ route('admin.classes.show', $classroom->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<form action="{{ route('admin.classes.students.update', $classroom->id) }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Nama Siswa</th>
                            <th>NIS / Username</th>
                            <th>Status Kelas Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td class="ps-4">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-checkbox"
                                        {{ $student->kelas == $classroom->name ? 'checked' : '' }}>
                                </td>
                                <td class="fw-bold text-dark">{{ $student->name }}</td>
                                <td class="text-muted">{{ $student->username }}</td>
                                <td>
                                    @if($student->kelas == $classroom->name)
                                        <span class="badge bg-success">Di kelas ini</span>
                                    @elseif($student->kelas)
                                        <span class="badge bg-warning text-dark">{{ $student->kelas }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum ada kelas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    Belum ada data murid terdaftar di sistem.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.student-checkbox');
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');
        
        // Select All Checkbox
        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    // Only check visible rows
                    const row = cb.closest('tr');
                    if (row.style.display !== 'none') {
                        cb.checked = selectAll.checked;
                    }
                });
            });
        }

        // Live Search Filter
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    // Skip the 'Belum ada data' row
                    if (row.querySelector('td').colSpan == 4) return;
                    
                    const text = row.textContent.toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection

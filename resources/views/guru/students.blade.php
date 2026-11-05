@extends('layouts.dashboard')

@section('title', 'Daftar Siswa')
@section('page-title', 'Daftar Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Siswa {{ isset($selectedKelas) && $selectedKelas ? '- Kelas ' . $selectedKelas : '' }}</h1>
        <p class="text-muted mb-0">Lihat data seluruh siswa yang terdaftar{{ isset($selectedKelas) && $selectedKelas ? ' di kelas ini' : '' }}.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="input-group" style="width: 250px;">
            <span class="input-group-text bg-white text-muted border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama atau NIS...">
        </div>
        @if(isset($selectedKelas) && $selectedKelas)
            <a href="{{ route('guru.classes') }}" class="btn btn-outline-secondary" style="border-radius:8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Kembali
            </a>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-4 py-3" style="font-weight:600; font-size:0.85rem; color:#64748b;">Nama Siswa</th>
                        <th class="py-3" style="font-weight:600; font-size:0.85rem; color:#64748b;">NIS / Username</th>
                        <th class="py-3" style="font-weight:600; font-size:0.85rem; color:#64748b;">Kelas</th>
                        <th class="py-3" style="font-weight:600; font-size:0.85rem; color:#64748b;">Jenis Kelamin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#e2e8f0,#cbd5e1);display:flex;align-items:center;justify-content:center;font-weight:700;color:#475569;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="py-3 text-muted" style="font-size:0.9rem;">{{ $user->username }}</td>
                            <td class="py-3 text-muted" style="font-size:0.9rem;">{{ $user->kelas ?? '-' }}</td>
                            <td class="py-3 text-muted" style="font-size:0.9rem;">
                                @if($user->jenis_kelamin === 'L')
                                    Laki-laki
                                @elseif($user->jenis_kelamin === 'P')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('tbody tr');
        
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                
                tableRows.forEach(row => {
                    // Skip empty state row
                    if (row.querySelector('td').colSpan == 4) return;
                    
                    const text = row.textContent.toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

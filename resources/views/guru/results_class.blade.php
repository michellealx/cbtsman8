@extends('layouts.dashboard')

@section('title', 'Ujian di Kelas {{ $classroom->name }}')
@section('page-title', 'Hasil Ujian')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('guru.results') }}" class="text-decoration-none text-primary">
                <i class="bi bi-bar-chart-line me-1"></i>Hasil Ujian
            </a>
        </li>
        <li class="breadcrumb-item active fw-semibold">{{ $classroom->name }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1 fw-bold">Kelas {{ $classroom->name }}</h1>
        <p class="text-muted mb-0">Pilih ujian untuk melihat hasil siswa.</p>
    </div>
    <a href="{{ route('guru.results') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

{{-- Search Bar Ujian --}}
<div class="mb-4">
    <div class="input-group shadow-sm" style="border-radius:12px;overflow:hidden;">
        <span class="input-group-text bg-white border-end-0" style="border-radius:12px 0 0 12px;">
            <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text"
            id="searchExam"
            class="form-control border-start-0 ps-0"
            placeholder="Cari nama ujian..."
            autocomplete="off"
            style="border-radius:0 12px 12px 0;">
    </div>
</div>

<div id="examListWrapper" class="row g-3">
    @forelse($examSessions as $session)
    <div class="col-12 exam-item" data-name="{{ strtolower($session->exam->title) }}">
        <a href="{{ route('guru.results.exam', [$classroom->id, $session->exam_id]) }}"
            class="card border-0 shadow-sm text-decoration-none exam-row-card"
            style="border-radius:14px;transition:all 0.25s ease;display:block;">
            <div class="card-body py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#f5f3ff,#ede9fe);color:#7c3aed;">
                            <i class="bi bi-journal-text fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $session->exam->title }}</h6>
                            <small class="text-muted">{{ $session->exam->subject }}</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Progress bar --}}
                        @php
                            $pct = $session->student_total > 0
                                ? round(($session->done_count / $session->student_total) * 100)
                                : 0;
                        @endphp
                        <div class="text-end me-2 d-none d-md-block">
                            <small class="text-muted d-block mb-1">{{ $session->done_count }}/{{ $session->student_total }} siswa</small>
                            <div class="progress" style="width:120px;height:6px;border-radius:99px;">
                                <div class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-primary' }}"
                                    style="width:{{ $pct }}%;border-radius:99px;"></div>
                            </div>
                        </div>

                        @if($session->pending_count > 0)
                        <span class="badge rounded-pill px-3 py-2" style="background:#fefce8;color:#ca8a04;">
                            <i class="bi bi-pencil-square me-1"></i>{{ $session->pending_count }} Perlu Koreksi
                        </span>
                        @elseif($session->done_count > 0)
                        <span class="badge rounded-pill px-3 py-2" style="background:#f0fdf4;color:#16a34a;">
                            <i class="bi bi-check-circle-fill me-1"></i>Selesai
                        </span>
                        @else
                        <span class="badge rounded-pill px-3 py-2" style="background:#f8fafc;color:#94a3b8;">
                            <i class="bi bi-hourglass me-1"></i>Belum ada
                        </span>
                        @endif

                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 text-center py-5 shadow-sm" style="border-radius:16px;">
            <div class="card-body">
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                    style="width:72px;height:72px;background:rgba(124,58,237,0.08);">
                    <i class="bi bi-journal-x fs-2" style="color:#7c3aed;"></i>
                </div>
                <h5 class="text-muted fw-semibold">Belum ada ujian di kelas ini</h5>
                <p class="text-muted mb-0">Tidak ada ujian yang diterbitkan ke kelas ini.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<style>
.exam-row-card:hover {
    transform: translateX(4px);
    box-shadow: 0 8px 24px rgba(15,23,42,0.10) !important;
    border-left: 3px solid #7c3aed !important;
}
</style>
@endsection

@section('scripts')
<script>
(function() {
    const input = document.getElementById('searchExam');
    if (!input) return;

    input.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();
        const items = document.querySelectorAll('#examListWrapper .exam-item');
        let visibleCount = 0;

        items.forEach(function (item) {
            const name = item.dataset.name || '';
            const match = name.includes(keyword);
            item.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        // Tampilkan / sembunyikan pesan "tidak ditemukan"
        let emptyMsg = document.getElementById('examSearchEmpty');
        if (!emptyMsg) {
            emptyMsg = document.createElement('div');
            emptyMsg.id = 'examSearchEmpty';
            emptyMsg.className = 'col-12';
            emptyMsg.innerHTML = `
                <div class="card border-0 text-center py-5 shadow-sm" style="border-radius:16px;">
                    <div class="card-body">
                        <i class="bi bi-search fs-2 text-muted d-block mb-2 opacity-50"></i>
                        <h6 class="text-muted fw-semibold">Ujian tidak ditemukan</h6>
                        <p class="text-muted mb-0 small">Coba kata kunci lain.</p>
                    </div>
                </div>`;
            document.getElementById('examListWrapper').appendChild(emptyMsg);
        }
        emptyMsg.style.display = (visibleCount === 0 && keyword !== '') ? '' : 'none';
    });
})();
</script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Detail Ujian')
@section('page-title', 'Detail Ujian')

@section('content')
<div class="mb-4">
    <a href="{{ route('murid.exams') }}" class="d-inline-flex align-items-center gap-1 text-muted text-decoration-none mb-3" style="font-size:0.85rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/></svg>
        Kembali ke Daftar Ujian
    </a>
</div>

@if(session('error'))
<div class="alert alert-danger border-0 shadow-sm mb-4 fw-bold d-flex align-items-start gap-2" style="border-radius: 12px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
    </svg>
    <div>
        {{ session('error') }}
    </div>
</div>
@endif

@if($exam->require_seb)
<div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-start gap-2" style="border-radius: 12px; background: #fffbeb; color: #b45309;">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-shield-lock-fill flex-shrink-0 mt-1" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.8 11.8 0 0 0 2.517 2.453c1.02.582 1.954 1.053 2.723 1.341l.154.057v.002h.004l.01-.003.01.003h.004v-.002l.154-.057c.769-.288 1.704-.759 2.723-1.341a11.8 11.8 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 63 63 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5"/>
    </svg>
    <div>
        <strong>Perhatian!</strong> Ujian ini mewajibkan Anda untuk menggunakan aplikasi <strong>Safe Exam Browser (SEB)</strong>. Silakan buka aplikasi SEB di perangkat Anda untuk memulai ujian.
    </div>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-xl-7 col-lg-9">

        {{-- Header Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px; overflow:hidden;">
            <div style="height:6px; background: linear-gradient(90deg, #1152b4, #3b82f6);"></div>
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <span class="badge fw-normal mb-2" style="background:rgba(17,82,180,0.1);color:#1152b4;font-size:0.75rem;padding:4px 10px;border-radius:6px;">{{ $exam->subject }}</span>
                        <h2 class="h5 fw-bold mb-1">{{ $exam->title }}</h2>
                        <p class="text-muted mb-0" style="font-size:0.82rem;">Dibuat oleh {{ $exam->teacher->name }}</p>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold text-primary" style="font-size:1.4rem; line-height:1;">{{ $exam->duration_minutes }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">menit</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                    <div class="fw-bold text-primary" style="font-size:1.5rem;">{{ $exam->questions_count }}</div>
                    <div class="text-muted" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.5px;">Soal</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                    <div class="fw-bold text-primary" style="font-size:1.5rem;">{{ $exam->duration_minutes }}'</div>
                    <div class="text-muted" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.5px;">Durasi</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm text-center py-3" style="border-radius:12px;">
                    <div class="fw-bold {{ $exam->status == 'published' ? 'text-success' : 'text-warning' }}" style="font-size:1rem;">
                        {{ $exam->status == 'published' ? 'Aktif' : ucfirst($exam->status) }}
                    </div>
                    <div class="text-muted" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.5px;">Status</div>
                </div>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:.5px; color:#64748b;">Petunjuk Pengerjaan</h6>
                <ul class="mb-0" style="font-size:0.875rem; color:#374151; padding-left: 1.2rem; line-height: 1.8;">
                    <li>Pastikan koneksi internet Anda stabil sebelum memulai.</li>
                    <li>Waktu ujian dimulai sejak Anda menekan tombol "Mulai Ujian".</li>
                    <li>Waktu terus berjalan meskipun halaman ditutup atau browser di-refresh.</li>
                    <li>Jawaban akan tersimpan secara otomatis saat Anda mengumpulkan ujian.</li>
                    <li>Ujian tidak dapat dikerjakan ulang setelah dikumpulkan.</li>
                </ul>
            </div>
        </div>

        {{-- Action Button --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-4 text-center">
                @if($result && $result->finished_at)
                    <div class="d-inline-flex align-items-center gap-2 mb-3 px-4 py-2 rounded-3" style="background:#f0fdf4; color:#166534; font-size:0.9rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#16a34a" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                        <span class="fw-semibold">Ujian telah diselesaikan</span>
                    </div>
                    <br>
                    <a href="{{ route('murid.scores') }}" class="btn btn-outline-primary px-5" style="border-radius:8px;">Lihat Nilai Saya</a>
                @elseif($result && !$result->finished_at)
                    <p class="text-muted mb-3" style="font-size:0.875rem;">Ujian Anda sedang berlangsung. Waktu terus berjalan.</p>
                    <form action="{{ route('murid.exams.start', $exam->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning fw-bold px-5 text-dark" style="border-radius:8px; font-size:0.9rem;">
                            Lanjutkan Ujian
                        </button>
                    </form>
                @else
                    <p class="text-muted mb-3" style="font-size:0.875rem;">Klik tombol di bawah untuk memulai. Waktu langsung berjalan.</p>
                    <form action="{{ route('murid.exams.start', $exam->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success fw-bold px-5 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius:10px; font-size:1rem; background-color: #16a34a; border-color: #16a34a;">
                            <i class="bi bi-play-circle-fill"></i> Mulai Ujian
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

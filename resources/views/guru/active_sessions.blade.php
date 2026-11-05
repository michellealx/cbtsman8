@extends('layouts.dashboard')

@section('title', 'Sesi Ujian Berlangsung')
@section('page-title', 'Sesi Ujian Berlangsung')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Sesi Ujian Aktif</h1>
        <p class="text-muted mb-0">Pantau murid yang sedang mengerjakan ujian secara langsung.</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    @forelse($sessions as $session)
    <div class="col-md-6 col-lg-4">
        <a href="{{ route('guru.active_sessions.show', $session->id) }}" class="card border-0 h-100 text-decoration-none shadow-sm" style="transition: transform 0.2s;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-primary rounded-pill px-3">{{ $session->classroom->name }}</span>
                    <span class="badge bg-success rounded-pill">Aktif</span>
                </div>
                <h5 class="card-title fw-bold text-dark mb-1">{{ $session->exam->title }}</h5>
                <p class="text-muted small mb-0">{{ $session->exam->subject }}</p>
                
                <hr class="my-3">
                
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="bi bi-clock me-1"></i> {{ $session->exam->duration_minutes }} Menit
                        @if($session->exam->deadline)
                        <br>
                        <span class="text-danger fw-semibold mt-1 d-inline-block" style="font-size: 0.75rem;">
                            <i class="bi bi-stopwatch me-1"></i> Sisa Waktu Ujian: 
                            <span class="session-deadline-timer" data-deadline="{{ $session->exam->deadline->timestamp * 1000 }}">--:--:--</span>
                        </span>
                        @endif
                    </div>
                    <div class="fw-semibold text-primary">
                        Lihat Sesi &rarr;
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <h5 class="text-muted fw-bold mb-0">Tidak ada ujian yang sedang diterbitkan.</h5>
                <p class="text-muted small mt-2 mb-0">Ujian yang diterbitkan akan muncul di sini.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timers = document.querySelectorAll('.session-deadline-timer');
    if (timers.length === 0) return;

    function updateTimers() {
        const now = new Date().getTime();
        
        timers.forEach(timer => {
            const deadlineTime = parseInt(timer.getAttribute('data-deadline'));
            if (!deadlineTime) return;
            
            const distance = deadlineTime - now;
            
            if (distance <= 0) {
                timer.innerHTML = "WAKTU HABIS";
                window.location.href = "{{ route('guru.questions') }}";
                return;
            }
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)) + Math.floor(distance / (1000 * 60 * 60 * 24)) * 24;
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const h = hours < 10 ? '0' + hours : hours;
            const m = minutes < 10 ? '0' + minutes : minutes;
            const s = seconds < 10 ? '0' + seconds : seconds;
            
            timer.innerHTML = h + ":" + m + ":" + s;
        });
    }

    updateTimers();
    setInterval(updateTimers, 1000);
});
</script>
@endsection

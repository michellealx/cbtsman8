@extends('layouts.dashboard')

@section('title', 'Detail Sesi Aktif')
@section('page-title', 'Detail Sesi Aktif')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Live Monitoring</h1>
        <p class="text-muted mb-1">Ujian: <strong>{{ $session->exam->title }}</strong> &bull; Kelas: <strong>{{ $session->classroom->name }}</strong></p>
        @if($session->exam->deadline)
        <div class="d-inline-flex align-items-center bg-danger text-white px-3 py-1 rounded-pill" style="font-size: 0.85rem;">
            <i class="bi bi-stopwatch me-2"></i>Sisa Waktu Ujian: <strong id="global-deadline-timer" data-deadline="{{ $session->exam->deadline->timestamp * 1000 }}" class="ms-1">--:--:--</strong>
        </div>
        @endif
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('guru.active_sessions.end', $session->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENGAKHIRI SESI ujian ini SEKARANG? Ujian akan langsung ditutup untuk kelas ini, dan murid yang sedang mengerjakan akan otomatis dianggap selesai.')">
            @csrf
            <button type="submit" class="btn btn-danger fw-bold shadow-sm">
                <i class="bi bi-x-circle me-1"></i> Selesaikan Ujian
            </button>
        </form>
        <a href="{{ route('guru.active_sessions') }}" class="btn btn-outline-secondary shadow-sm">
            &larr; Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0">Daftar Murid di Kelas Ini</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Sisa Waktu / Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    @php
                        $result = $results->get($student->id);
                        $status = 'Belum Mulai';
                        $badge = 'secondary';
                        $remaining = null;
                        $extra = 0;
                        if ($result) {
                            if ($result->finished_at) {
                                $status = 'Selesai';
                                $badge = 'success';
                            } else {
                                $status = 'Sedang Mengerjakan';
                                $badge = 'primary';
                                $extra = $result->extra_time ?? 0;
                                $endTime = $result->started_at->addMinutes($session->exam->duration_minutes + $extra);
                                $remaining = max(0, now()->diffInMinutes($endTime, false));
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $student->name }}</div>
                            <small class="text-muted">{{ $student->username }}</small>
                        </td>
                        <td>
                            @if($result)
                                {{ $result->started_at->format('H:i:s') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($result && $result->finished_at)
                                {{ $result->finished_at->format('H:i:s') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $badge }}">{{ $status }}</span>
                            @if($status === 'Sedang Mengerjakan')
                                <br><small class="text-muted">{{ $remaining }} Menit Tersisa</small>
                                @if($extra > 0)
                                    <br><small class="text-success"><i class="bi bi-plus-circle"></i> {{ $extra }} mnt ekstra</small>
                                @endif
                            @endif
                        </td>
                        <td class="text-end">
                            @if($status === 'Sedang Mengerjakan')
                            <button class="btn btn-sm btn-outline-warning fw-bold" data-bs-toggle="modal" data-bs-target="#addTimeModal-{{ $result->id }}">
                                + Tambah Waktu
                            </button>
                            @else
                            -
                            @endif
                        </td>
                    </tr>

                    @if($result && $status === 'Sedang Mengerjakan')
                    <!-- Modal Tambah Waktu -->
                    <div class="modal fade" id="addTimeModal-{{ $result->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content text-start">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Tambah Waktu Ujian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('guru.active_sessions.add_time', $result->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p class="text-muted">Berapa menit tambahan waktu yang ingin diberikan kepada <strong>{{ $student->name }}</strong>?</p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tambahan Menit</label>
                                            <input type="number" name="extra_minutes" class="form-control form-control-lg" required min="1" max="120" value="15">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning fw-bold">Simpan Waktu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            Belum ada data murid di kelas ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timerElement = document.getElementById('global-deadline-timer');
    if (!timerElement) return;

    const deadlineTime = parseInt(timerElement.getAttribute('data-deadline'));
    if (!deadlineTime) return;

    function updateTimer() {
        const now = new Date().getTime();
        const distance = deadlineTime - now;

        if (distance <= 0) {
            timerElement.innerHTML = "WAKTU HABIS";
            window.location.href = "{{ route('guru.questions') }}";
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)) + Math.floor(distance / (1000 * 60 * 60 * 24)) * 24;
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const h = hours < 10 ? '0' + hours : hours;
        const m = minutes < 10 ? '0' + minutes : minutes;
        const s = seconds < 10 ? '0' + seconds : seconds;

        timerElement.innerHTML = h + ":" + m + ":" + s;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
});
</script>
@endsection

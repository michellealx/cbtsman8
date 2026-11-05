@extends('layouts.dashboard')

@section('title', 'Ujian Tersedia')
@section('page-title', 'Dashboard')

@section('content')
<div class="mb-5">
    <h1 class="h4 fw-bold mb-1">Ujian Tersedia</h1>
    <p class="text-muted mb-0" style="font-size: 0.9rem;">Daftar ujian yang belum Anda kerjakan.</p>
</div>

<div class="card border-0 mb-4 bg-transparent shadow-none">
    <form action="{{ route('murid.exams') }}" method="GET" class="d-flex max-w-md">
        <div class="input-group" style="box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama atau mapel ujian..." value="{{ request('search') }}" style="border-radius: 0;">
            
            <input type="date" name="date" class="form-control text-muted" style="max-width: 150px; border-radius: 0;" value="{{ request('date') }}" title="Filter berdasarkan tanggal dibuat">
            
            @if(request('search') || request('date'))
                <a href="{{ route('murid.exams') }}" class="btn btn-outline-secondary" title="Clear Filter" style="border-radius: 0;">
                    <i class="bi bi-x"></i>
                </a>
            @endif
            <button type="submit" class="btn btn-primary px-4" style="border-radius: 0 12px 12px 0;">Cari</button>
        </div>
    </form>
</div>

@forelse($exams as $exam)
<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="d-flex align-items-stretch">

            {{-- Left accent bar --}}
            <div style="width: 5px; flex-shrink: 0; background: linear-gradient(180deg, #1152b4, #3b82f6);"></div>

            <div class="p-4 flex-grow-1">
                <div class="row align-items-center">

                    {{-- Info --}}
                    <div class="col-md-6">
                        <span class="badge fw-normal mb-2" style="background: rgba(17,82,180,0.08); color:#1152b4; font-size: 0.75rem; padding: 3px 9px; border-radius: 6px;">{{ $exam->subject }}</span>
                        @if($exam->require_seb)
                            <span class="badge bg-warning text-dark fw-bold mb-2 ms-1" style="font-size: 0.75rem; padding: 3px 9px; border-radius: 6px;"><i class="bi bi-shield-lock-fill"></i> Wajib SEB</span>
                        @endif
                        <h5 class="fw-bold mb-1" style="font-size: 1rem;">{{ $exam->title }}</h5>
                        <p class="text-muted mb-0" style="font-size: 0.82rem;">
                            {{ $exam->teacher->name }} 
                            &bull; <span title="Tanggal Dibuat">{{ $exam->created_at->format('d M Y') }}</span>
                        </p>
                    </div>

                    {{-- Meta --}}
                    <div class="col-md-3 mt-3 mt-md-0">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#94a3b8" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M0 10a8 8 0 1 1 16 0A8 8 0 0 1 0 10m8-7a7 7 0 0 0-7 7 7 7 0 0 0 14 0 7 7 0 0 0-7-7"/></svg>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $exam->duration_minutes }} menit</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#94a3b8" viewBox="0 0 16 16"><path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/></svg>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $exam->questions_count }} soal</span>
                            </div>
                            @if($exam->deadline)
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="#dc3545" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>
                                <span class="text-danger fw-semibold" style="font-size: 0.75rem;">Batas: {{ $exam->deadline->format('d M Y, H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="col-md-3 mt-3 mt-md-0 text-md-end">
                        <a href="{{ route('murid.exam', $exam->id) }}"
                           class="btn btn-success fw-bold px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-size:0.9rem; background-color: #16a34a; border-color: #16a34a;">
                            <i class="bi bi-play-circle-fill"></i> Mulai Ujian
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="card border-0" style="border-radius: 12px;">
    <div class="card-body py-5 text-center">
        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center" style="width:56px;height:56px;background:#f1f5f9;border-radius:50%;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#94a3b8" viewBox="0 0 16 16"><path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/></svg>
        </div>
        <h6 class="fw-semibold text-muted">Tidak ada ujian yang tersedia</h6>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">Semua ujian telah dikerjakan, atau guru belum mempublikasikan ujian baru.</p>
    </div>
</div>
@endforelse
@endsection

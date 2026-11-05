@extends('layouts.dashboard')

@section('title', 'Riwayat Ujian')
@section('page-title', 'Riwayat Ujian')

@section('content')

{{-- Header --}}
<div class="mb-5">
    <h1 class="h4 fw-bold mb-1">Riwayat Ujian</h1>
    <p class="text-muted" style="font-size: 0.875rem;">Rekap semua ujian yang telah Anda ikuti.</p>
</div>

{{-- Stats Strip --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="p-3 rounded-3 text-center d-flex flex-column align-items-center justify-content-center" style="background:#f0fdf4; border:1px solid #bbf7d0;">
            <div class="fw-bold" style="font-size:1.6rem; color:#16a34a;">{{ $results->count() }}</div>
            <div class="text-muted" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:.5px; margin-top:2px;">Total Ujian Selesai</div>
        </div>
    </div>
</div>

{{-- History List --}}
@forelse($results as $index => $result)
<div class="d-flex align-items-center gap-3 mb-2 p-3 rounded-3"
     style="background:#fff; border:1px solid #e2e8f0; transition: border-color .15s;"
     onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">

    {{-- Number --}}
    <div class="text-center flex-shrink-0"
         style="width:36px; height:36px; border-radius:8px; background:#f1f5f9;
                display:flex; align-items:center; justify-content:center;
                font-size:0.78rem; font-weight:700; color:#64748b;">
        {{ $index + 1 }}
    </div>

    {{-- Main info --}}
    <div class="flex-grow-1 min-width-0">
        <div class="fw-semibold text-truncate" style="font-size:0.9rem; color:#1e293b;">{{ $result->exam->title }}</div>
        <div class="text-muted" style="font-size:0.78rem;">{{ $result->exam->subject }} &bull; Selesai {{ $result->finished_at->format('d M Y, H:i') }}</div>
    </div>

    {{-- Status badge --}}
    {{-- Status badge --}}
    <span style="flex-shrink:0; display:inline-flex; align-items:center; gap:5px;
                 background:#f0fdf4; color:#166534; font-size:0.72rem; font-weight:700;
                 padding:4px 10px; border-radius:6px; white-space:nowrap;">
        <span style="width:5px;height:5px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
        Selesai
    </span>
</div>
@empty
<div class="text-center py-5" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px;">
    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
         style="width:48px; height:48px; background:#f1f5f9; border-radius:50%;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#94a3b8" viewBox="0 0 16 16">
            <path d="M4 11H2v3h2zm5-4H7v7h2zm5-5h-2v12h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z"/>
        </svg>
    </div>
    <p class="fw-semibold text-muted mb-1" style="font-size:0.9rem;">Belum ada riwayat</p>
    <p class="text-muted mb-0" style="font-size:0.82rem;">Ikuti ujian untuk melihat riwayat di sini.</p>
</div>
@endforelse

@endsection

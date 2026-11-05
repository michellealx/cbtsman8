@extends('layouts.dashboard')

@section('title', $title ?? 'Sedang Dalam Pengembangan')
@section('page-title', $title ?? 'Sedang Dalam Pengembangan')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center text-center py-5" style="min-height: 60vh;">
    <div class="mb-4">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light shadow-sm" style="width: 100px; height: 100px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#1152b4" viewBox="0 0 16 16">
                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
            </svg>
        </div>
    </div>
    <h1 class="h3 fw-bold text-dark mb-2">Halaman {{ $title ?? 'Ini' }} Belum Tersedia</h1>
    <p class="text-muted mb-4 max-w-md" style="max-width: 500px;">
        Kami sedang bekerja keras membangun fitur ini untuk Anda. Fitur <strong>{{ $title ?? 'ini' }}</strong> akan segera hadir pada pembaruan sistem berikutnya.
    </p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
        Kembali ke Dashboard
    </a>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Laporan')
@section('page-title', 'Laporan Nilai')

@section('content')
<div class="mb-4">
    <h1 class="h4 mb-1 fw-bold">Laporan & Statistik</h1>
    <p class="text-muted mb-0">Lihat rekap nilai murid dan statistik ujian.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 text-center">
            <div class="card-body py-4">
                <h2 class="display-6 fw-bold" style="color:#1152b4;">0</h2>
                <p class="text-muted mb-0">Total Ujian</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 text-center">
            <div class="card-body py-4">
                <h2 class="display-6 fw-bold" style="color:#10b981;">0</h2>
                <p class="text-muted mb-0">Total Peserta</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 text-center">
            <div class="card-body py-4">
                <h2 class="display-6 fw-bold" style="color:#a855f7;">0</h2>
                <p class="text-muted mb-0">Rata-rata Nilai</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Rekap Nilai Terbaru</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama Murid</th>
                        <th>Ujian</th>
                        <th>Nilai</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            Belum ada data laporan. Data akan muncul setelah ujian dilaksanakan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

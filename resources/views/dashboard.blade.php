@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <h1 class="h4 mb-1 fw-bold">Selamat Datang, {{ $currentUser['name'] }}</h1>
                        <p class="text-muted mb-0">Sistem Informasi CBT SMAN 8 Tangerang Selatan</p>
                    </div>
                    <span class="badge bg-primary text-capitalize py-2 px-3 rounded-pill" style="font-size: 0.8rem;">{{ $currentUser['role'] === 'murid' ? 'Siswa' : (!empty($currentUser['is_coordinator']) ? 'Koordinator CBT' : 'Guru / Pengajar') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($currentUser['role'] !== 'murid')
    <div class="row g-4 mb-4">
        <!-- Statistik 1: Total Siswa -->
        <div class="col-md-3">
            <div class="card border-0 text-center shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;background:rgba(17,82,180,0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#1152b4" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $total_siswa ?? 0 }}</h3>
                    <small class="text-muted">Total Siswa</small>
                </div>
            </div>
        </div>
        @if ($currentUser['role'] === 'admin' || !empty($currentUser['is_coordinator']))
        <!-- Statistik: Total Guru -->
        <div class="col-md-3">
            <div class="card border-0 text-center shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;background:rgba(16,185,129,0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#10b981" viewBox="0 0 16 16">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold mb-0" style="color:#10b981;">{{ $total_guru ?? 0 }}</h3>
                    <small class="text-muted">Total Guru</small>
                </div>
            </div>
        </div>
        @endif
        <!-- Statistik 2: Total Kelas -->
        <div class="col-md-3">
            <div class="card border-0 text-center shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;background:rgba(245,158,11,0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#f59e0b" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 0-1 1v4.586l7 7L15.586 6 11 1.414 10.586 1zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                            <path d="M1 7.086V2a2 2 0 0 1 2-2h4.586a2 2 0 0 1 1.414.586L15.586 7.5a2 2 0 0 1 0 2.828l-6.586 6.586A2 2 0 0 1 6.172 17L1 11.828V7.086zM5.5 5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $total_kelas ?? 0 }}</h3>
                    <small class="text-muted">Total Kelas</small>
                </div>
            </div>
        </div>
        <!-- Statistik 3: Total Mapel -->
        <div class="col-md-3">
            <div class="card border-0 text-center shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;background:rgba(168,85,247,0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#a855f7" viewBox="0 0 16 16">
                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold mb-0">{{ $total_mapel ?? 0 }}</h3>
                    <small class="text-muted">Mata Pelajaran</small>
                </div>
            </div>
        </div>
        @if ($currentUser['role'] !== 'admin')
        <!-- Statistik 4: Ujian Dibuat -->
        <div class="col-md-3">
            <div class="card border-0 text-center shadow-sm h-100">
                <div class="card-body py-4">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;background:rgba(16,185,129,0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#10b981" viewBox="0 0 16 16">
                            <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold mb-0" style="color:#10b981;">{{ $total_ujian ?? 0 }}</h3>
                    <small class="text-muted">Ujian Saya</small>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Pintasan Utama -->
    <div class="row g-4 mb-4">
        <!-- Section: Data Master -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3" style="color: #0f3460;">
                        <i class="bi bi-folder2-open me-2 text-primary"></i>Pintasan Data Master
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.students') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#f8f9fa; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#f0f4ff'; this.style.borderColor='#1152b4';" onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#1152b4; font-size: 1.05rem;">Data Siswa</div>
                                <small class="text-muted">Kelola & import murid</small>
                            </a>
                        </div>
                        @if ($currentUser['role'] === 'admin' || !empty($currentUser['is_coordinator']))
                        <div class="col-md-3">
                            <a href="{{ route('admin.teachers') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#f8f9fa; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#f0fdf4'; this.style.borderColor='#10b981';" onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#10b981; font-size: 1.05rem;">Data Guru</div>
                                <small class="text-muted">Kelola akun rekan guru</small>
                            </a>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <a href="{{ route('admin.classes') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#f8f9fa; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#fffbeb'; this.style.borderColor='#f59e0b';" onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#f59e0b; font-size: 1.05rem;">Daftar Kelas</div>
                                <small class="text-muted">Kelola kelas & rombel</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.subjects') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#faf5ff; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#f3e8ff'; this.style.borderColor='#a855f7';" onmouseout="this.style.background='#faf5ff'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#a855f7; font-size: 1.05rem;">Mata Pelajaran</div>
                                <small class="text-muted">Kelola kurikulum mapel</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($currentUser['role'] !== 'admin')
        <!-- Section: Ujian & Evaluasi -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3" style="color: #0f3460;">
                        <i class="bi bi-journal-check me-2 text-primary"></i>Manajemen Ujian & Laporan
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('guru.exams') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#f8f9fa; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#f0f4ff'; this.style.borderColor='#1152b4';" onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#1152b4; font-size: 1.05rem;">Kelola Ujian</div>
                                <small class="text-muted">Buat baru / import file</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('guru.questions') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#f8f9fa; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#f0fdf4'; this.style.borderColor='#10b981';" onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#10b981; font-size: 1.05rem;">Bank Soal</div>
                                <small class="text-muted">Kelola kumpulan soal</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('guru.active_sessions') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#f8f9fa; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#fffbeb'; this.style.borderColor='#f59e0b';" onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#f59e0b; font-size: 1.05rem;">Sesi Aktif</div>
                                <small class="text-muted">Pantau & tambah waktu</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('guru.results') }}" class="d-block p-3 rounded-3 text-decoration-none text-center h-100" style="background:#f8f9fa; border: 1px solid #e9ecef; transition:all 0.2s;" onmouseover="this.style.background='#faf5ff'; this.style.borderColor='#a855f7';" onmouseout="this.style.background='#f8f9fa'; this.style.borderColor='#e9ecef';">
                                <div class="fw-bold mb-1" style="color:#a855f7; font-size: 1.05rem;">Hasil & Nilai</div>
                                <small class="text-muted">Koreksi & rekap nilai</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

@else
    <div class="row g-4">
        <div class="col-md-6">
            <a href="{{ route('murid.exams') }}" class="card border-0 h-100 text-decoration-none shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold" style="color: #0f3460;">Ujian Tersedia</h5>
                    <p class="text-muted mb-0">Lihat semua ujian yang bisa diikuti hari ini.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('murid.scores') }}" class="card border-0 h-100 text-decoration-none shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold" style="color: #0f3460;">Riwayat Ujian</h5>
                    <p class="text-muted mb-0">Pantau hasil dan nilai ujian yang telah Anda kerjakan.</p>
                </div>
            </a>
        </div>
    </div>
@endif
@endsection

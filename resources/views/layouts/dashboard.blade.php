<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - CBT SMAN 8</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard-body">
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" width="32" class="sidebar-logo">
                <span class="sidebar-brand">CBT SMAN 8</span>
                @if (!empty($currentUser['is_coordinator']))
                    <span class="sidebar-badge" style="background: #f59e0b; color: #fff; font-weight: 700;">KOORDINATOR</span>
                @else
                    <span class="sidebar-badge">{{ strtoupper($currentUser['role']) }}</span>
                @endif
            </div>

            <nav class="sidebar-nav">
                @if ($currentUser['role'] !== 'murid')
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.5.5 0 0 0-.708 0l-.914.914a.5.5 0 0 0 .708.708l.914-.914a.5.5 0 0 0 0-.708M0 10a8 8 0 1 1 16 0A8 8 0 0 1 0 10m8-7a7 7 0 0 0-7 7 7 7 0 0 0 14 0 7 7 0 0 0-7-7"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                @endif

                @if (in_array($currentUser['role'], ['admin', 'guru']))
                    <div class="sidebar-section-title">DATA MASTER</div>
                    <a href="{{ route('admin.students') }}" class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                        </svg>
                        <span>Siswa</span>
                    </a>
                    @if ($currentUser['role'] === 'admin' || !empty($currentUser['is_coordinator']))
                    <a href="{{ route('admin.teachers') }}" class="sidebar-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                        </svg>
                        <span>Data Guru</span>
                    </a>
                    @endif
                    <a href="{{ route('admin.subjects') }}" class="sidebar-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                        </svg>
                        <span>Mata Pelajaran</span>
                    </a>
                    <a href="{{ route('admin.classes') }}" class="sidebar-link {{ request()->routeIs('admin.classes*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 0-1 1v4.586l7 7L15.586 6 11 1.414 10.586 1zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                            <path d="M1 7.086V2a2 2 0 0 1 2-2h4.586a2 2 0 0 1 1.414.586L15.586 7.5a2 2 0 0 1 0 2.828l-6.586 6.586A2 2 0 0 1 6.172 17L1 11.828V7.086zM5.5 5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                        </svg>
                        <span>Kelas</span>
                    </a>

                    @if ($currentUser['role'] === 'guru' && empty($currentUser['is_coordinator']))
                    <div class="sidebar-section-title">UJIAN</div>
                    <a href="{{ route('guru.exams') }}" class="sidebar-link {{ request()->routeIs('guru.exams*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                        </svg>
                        <span>Ujian</span>
                    </a>
                    <a href="{{ route('guru.active_sessions') }}" class="sidebar-link {{ request()->routeIs('guru.active_sessions*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                        <span>Sesi Aktif</span>
                    </a>
                    <a href="{{ route('guru.questions') }}" class="sidebar-link {{ request()->routeIs('guru.questions*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"/>
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6 1a6 6 0 1 1 0 12A6 6 0 0 1 8 1"/>
                        </svg>
                        <span>Bank Soal</span>
                    </a>

                    <div class="sidebar-section-title">LAPORAN</div>
                    <a href="{{ route('guru.results') }}" class="sidebar-link {{ request()->routeIs('guru.results*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M4 11H2v3h2zm5-4H7v7h2zm5-5h-2v12h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z"/>
                        </svg>
                        <span>Hasil Ujian</span>
                    </a>
                    @endif

                @else
                    <a href="{{ route('murid.exams') }}" class="sidebar-link {{ request()->routeIs('murid.exams') || request()->routeIs('murid.exam') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.5.5 0 0 0-.708 0l-.914.914a.5.5 0 0 0 .708.708l.914-.914a.5.5 0 0 0 0-.708M0 10a8 8 0 1 1 16 0A8 8 0 0 1 0 10m8-7a7 7 0 0 0-7 7 7 7 0 0 0 14 0 7 7 0 0 0-7-7"/>
                        </svg>
                        <span>Ujian Tersedia</span>
                    </a>

                    <div class="sidebar-section-title">RIWAYAT</div>
                    <a href="{{ route('murid.scores') }}" class="sidebar-link {{ request()->routeIs('murid.scores') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M4 11H2v3h2zm5-4H7v7h2zm5-5h-2v12h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z"/></svg>
                        <span>Riwayat Ujian</span>
                    </a>
                @endif
            </nav>

        </aside>

        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Top Bar -->
            <header class="dashboard-topbar">
                <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                    </svg>
                </button>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar-right d-flex align-items-center gap-2">

                    {{-- Theme Toggle --}}
                    <button id="themeToggle" onclick="toggleTheme()" title="Ganti Tema"
                        style="width:34px;height:34px;border-radius:8px;
                               border:1.5px solid #e2e8f0;background:#f8fafc;color:#64748b;
                               display:flex;align-items:center;justify-content:center;
                               cursor:pointer;transition:all .2s;flex-shrink:0;">
                        <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
                        </svg>
                        <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="display:none;">
                            <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>
                        </svg>
                    </button>

                    {{-- Profile Dropdown --}}
                    <div class="dropdown">
                        <button class="border-0" data-bs-toggle="dropdown" aria-expanded="false"
                            style="display:flex;align-items:center;gap:8px;
                                   background:#f8fafc;border:1.5px solid #e2e8f0 !important;
                                   border-radius:10px;padding:4px 10px 4px 5px;
                                   cursor:pointer;transition:all .15s;">
                            <div style="width:26px;height:26px;border-radius:6px;
                                        background:linear-gradient(135deg,#1152b4,#3b82f6);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:0.7rem;font-weight:700;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr($currentUser['name'], 0, 1)) }}
                            </div>
                            <div style="text-align:left;line-height:1.2;">
                                <div style="font-size:0.78rem;font-weight:600;color:#1e293b;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $currentUser['name'] }}
                                </div>
                                <div style="font-size:0.68rem;color:#64748b;">{{ !empty($currentUser['is_coordinator']) ? 'Koordinator CBT' : ucfirst($currentUser['role']) }}</div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="#94a3b8" viewBox="0 0 16 16">
                                <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                            </svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius:12px;min-width:170px;padding:6px;">
                            <li class="px-3 py-2" style="border-bottom:1px solid #f1f5f9;margin-bottom:4px;">
                                <div style="font-size:0.8rem;font-weight:700;color:#1e293b;">{{ $currentUser['name'] }}</div>
                                <div style="font-size:0.7rem;color:#64748b;">{{ !empty($currentUser['is_coordinator']) ? 'Koordinator CBT' : ucfirst($currentUser['role']) }}</div>
                            </li>
                            <li>
                                <button type="button" onclick="document.getElementById('logoutModal').classList.add('show')" class="dropdown-item d-flex align-items-center gap-2 text-danger" style="border-radius:7px;font-size:0.83rem;padding:8px 12px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/></svg>
                                    Keluar
                                </button>
                            </li>
                        </ul>
                    </div>

                </div>
            </header>

            <!-- Page Content -->
            <main class="dashboard-content">
                <!-- Alerts moved to Toast Container -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9999;
                align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;padding:32px 28px;max-width:380px;width:90%;
                    text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div style="width:52px;height:52px;border-radius:50%;background:#fef2f2;
                        display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#dc2626" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                </svg>
            </div>
            <h5 style="font-weight:700;font-size:1rem;color:#1e293b;margin-bottom:8px;">Keluar dari Akun?</h5>
            <p style="font-size:0.85rem;color:#64748b;margin-bottom:24px;">
                Anda akan keluar dari sesi ini. Pastikan semua pekerjaan Anda telah tersimpan.
            </p>
            <div style="display:flex;gap:10px;justify-content:center;">
                <button onclick="document.getElementById('logoutModal').classList.remove('show')"
                    style="padding:9px 22px;border-radius:9px;border:1.5px solid #e2e8f0;
                           background:#f8fafc;color:#374151;font-size:0.875rem;font-weight:600;cursor:pointer;">
                    Batal
                </button>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit"
                        style="padding:9px 22px;border-radius:9px;border:none;
                               background:#dc2626;color:#fff;font-size:0.875rem;font-weight:600;cursor:pointer;">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        #logoutModal.show { display: flex !important; }
        [data-theme="dark"] #logoutModal > div {
            background: #1e293b !important;
            color: #e2e8f0;
        }
        [data-theme="dark"] #logoutModal h5 { color: #e2e8f0 !important; }
    </style>

    <script>
        // ─── Sidebar toggle ───────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (toggle) toggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
            if (overlay) overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        });

        // ─── Dark Mode ───────────────────────────────────────────
        function applyTheme(dark) {
            document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
            document.getElementById('iconMoon').style.display = dark ? 'none'  : 'block';
            document.getElementById('iconSun').style.display  = dark ? 'block' : 'none';
            const btn = document.getElementById('themeToggle');
            if (btn) {
                btn.style.background = dark ? '#1e293b' : '#f8fafc';
                btn.style.borderColor = dark ? '#334155' : '#e2e8f0';
                btn.style.color = dark ? '#94a3b8' : '#64748b';
            }
        }
        function toggleTheme() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            localStorage.setItem('cbt-theme', isDark ? 'light' : 'dark');
            applyTheme(!isDark);
        }
        // Apply saved theme on load
        (function() {
            const saved = localStorage.getItem('cbt-theme');
            applyTheme(saved === 'dark');
        })();
    </script>

    <style>
        /* ── Dark Mode Variables ── */
        [data-theme="dark"] {
            --bs-body-bg: #0f172a;
            --bs-body-color: #e2e8f0;
        }
        [data-theme="dark"] body,
        [data-theme="dark"] .dashboard-body { background: #0f172a !important; color: #e2e8f0; }
        [data-theme="dark"] .sidebar { background: #1e293b !important; border-color: #334155 !important; }
        [data-theme="dark"] .sidebar-link { color: #94a3b8 !important; }
        [data-theme="dark"] .sidebar-link:hover,
        [data-theme="dark"] .sidebar-link.active { background: rgba(255,255,255,.05) !important; color: #e2e8f0 !important; }
        [data-theme="dark"] .sidebar-brand,
        [data-theme="dark"] .sidebar-user-name { color: #e2e8f0 !important; }
        [data-theme="dark"] .sidebar-section-title { color: #475569 !important; }
        [data-theme="dark"] .dashboard-topbar { background: #1e293b !important; border-color: #334155 !important; }
        [data-theme="dark"] .topbar-title { color: #e2e8f0 !important; }
        [data-theme="dark"] .dashboard-main { background: #0f172a !important; }
        [data-theme="dark"] .card { background: #1e293b !important; border-color: #334155 !important; }
        [data-theme="dark"] .card-header,
        [data-theme="dark"] .card-footer { background: #1e293b !important; border-color: #334155 !important; }
        [data-theme="dark"] .table { color: #e2e8f0 !important; }
        [data-theme="dark"] .table thead tr { background: #0f172a !important; }
        [data-theme="dark"] .text-muted { color: #64748b !important; }
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select { background: #0f172a !important; border-color: #334155 !important; color: #e2e8f0 !important; }
        [data-theme="dark"] .btn-outline-secondary { border-color: #475569 !important; color: #94a3b8 !important; }
        [data-theme="dark"] .dropdown-menu { background: #1e293b !important; border-color: #334155 !important; }
        [data-theme="dark"] .dropdown-item { color: #e2e8f0 !important; }
        [data-theme="dark"] .dropdown-item:hover { background: rgba(255,255,255,.06) !important; }
        [data-theme="dark"] .modal-content { background: #1e293b !important; color: #e2e8f0 !important; }
        [data-theme="dark"] hr { border-color: #334155; }
    </style>

    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Initialize Toasts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            toastElList.forEach(function (toastEl) {
                var toast = new bootstrap.Toast(toastEl)
                toast.show()
            })
        });
    </script>

    <!-- Additional Scripts -->
    @yield('scripts')
</body>
</html>

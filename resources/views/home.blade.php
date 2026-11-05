<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal CBT - SMAN 8 Tangerang Selatan</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { margin: 0; font-family: 'Open Sans', sans-serif; overflow: hidden; background: #fff; }
        
        .split-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Left Side: Branding & Hero */
        .brand-side {
            flex: 1;
            position: relative;
            background-image: url("{{ asset('images/bg.jpeg') }}");
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        /* Dark overlay for better text readability */
        .brand-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(17, 82, 180, 0.7), rgba(15, 52, 96, 0.8));
            z-index: 1;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            animation: fadeIn 1s ease-out;
        }

        .hero-title-main {
            font-size: 4rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
            font-family: 'Arial Black', Impact, sans-serif;
            letter-spacing: -1px;
            text-shadow: 0px 4px 15px rgba(0,0,0,0.5);
        }

        .hero-subtitle-main {
            font-family: 'Great Vibes', cursive;
            font-size: 3rem;
            margin-top: 10px;
            text-shadow: 0px 2px 10px rgba(0,0,0,0.4);
            color: #ffd54f; /* Golden yellow for elegant contrast */
        }

        /* Right Side: Login Form */
        .login-side {
            width: 450px;
            background: linear-gradient(135deg, #f8f9fa, #eef2f7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            position: relative;
            z-index: 5;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
        }

        .login-form-wrapper {
            background: rgba(255, 255, 255, 1);
            padding: 3rem 2.5rem;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 1);
            box-shadow: 0 20px 40px -10px rgba(15, 52, 96, 0.1), 0 10px 20px -5px rgba(15, 52, 96, 0.05);
            /* animation removed as requested */
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: #ffffff;
            color: #1152b4;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 25px -5px rgba(17, 82, 180, 0.2);
            border: 1px solid rgba(226, 232, 240, 1);
        }

        .form-control-cbt {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control-cbt:focus {
            background: #fff;
            border-color: #1152b4;
            box-shadow: 0 0 0 4px rgba(17, 82, 180, 0.1);
        }

        .btn-cbt {
            background: #1152b4;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 1.05rem;
            font-weight: 700;
            width: 100%;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 15px rgba(17, 82, 180, 0.25);
        }

        .btn-cbt:hover {
            background: #0f4699;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(17, 82, 180, 0.35);
            color: white;
        }

        /* Floating effect keyframes (REMOVED) */

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .split-layout {
                flex-direction: column;
                overflow-y: auto;
            }
            .brand-side {
                padding: 4rem 2rem;
                flex: none;
                min-height: 50vh;
            }
            .login-side {
                width: 100%;
                min-height: 50vh;
                padding: 2rem;
                border-radius: 20px 20px 0 0;
                margin-top: -20px;
            }
            .hero-title-main { font-size: 2.5rem; }
            .hero-subtitle-main { font-size: 2rem; }
            body { overflow: auto; }
        }
    </style>
</head>
<body>

    <div class="split-layout">
        <!-- Left Side: Branding -->
        <div class="brand-side">
            <div class="brand-content">
                <h1 class="hero-title-main">CBT SMA Negeri 8 Tangsel</h1>
                <div class="hero-subtitle-main">Disiplin, Kreatif, Berprestasi</div>
            </div>
        </div>

        <!-- Right Side: Login -->
        <div class="login-side">
            <div class="login-form-wrapper">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-1" style="color: #0f3460;" id="form-title">Portal Ujian</h3>
                    <p class="text-muted small" id="form-desc">Silakan masuk untuk memulai ujian.</p>
                </div>

                <!-- Alerts removed from here and moved to Toast -->

                <form method="POST" action="{{ url('/login') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="username" class="form-label fw-semibold" style="color: #0f3460;" id="username-label">Nomor Induk Siswa (NIS)</label>
                        <input id="username" name="username" type="text" class="form-control form-control-cbt w-100" value="{{ old('username') }}" required autofocus placeholder="Masukkan NIS Anda">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold" style="color: #0f3460;">Password</label>
                        <div class="input-group">
                            <input id="password" name="password" type="password" class="form-control form-control-cbt" required placeholder="••••••••" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-outline-secondary bg-light" type="button" id="togglePassword" style="border-color: #e9ecef; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-cbt mt-2" id="btn-login">
                        Masuk
                    </button>
                </form>

                <!-- Toggle for Guru/Admin -->
                <div class="text-center mt-4">
                    <a href="javascript:void(0)" id="toggle-role" onclick="switchLoginMode()" class="text-decoration-none text-muted" style="font-size: 0.85rem; font-weight: 600; transition: color 0.3s;">
                        <i class="bi bi-shield-lock me-1"></i> Akses Guru
                    </a>
                </div>

                <p class="text-center text-muted mt-5 mb-0" style="font-size: 0.75rem;">
                    &copy; {{ date('Y') }} SMAN 8 Tangerang Selatan.<br>Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </div>

    <script>
        let isStudentMode = true;

        function switchLoginMode() {
            isStudentMode = !isStudentMode;
            
            const title = document.getElementById('form-title');
            const desc = document.getElementById('form-desc');
            const userLabel = document.getElementById('username-label');
            const userInput = document.getElementById('username');
            const toggleBtn = document.getElementById('toggle-role');

            if (isStudentMode) {
                // Mode Siswa
                title.innerText = "Portal Ujian";
                desc.innerText = "Silakan masuk untuk memulai sesi Anda.";
                userLabel.innerText = "Nomor Induk Siswa (NIS)";
                userInput.placeholder = "Masukkan NIS Anda";
                toggleBtn.innerHTML = '<i class="bi bi-shield-lock me-1"></i> Akses Guru';
            } else {
                // Mode Guru/Admin
                title.innerText = "Portal Guru";
                desc.innerText = "Masuk untuk mengelola sistem & soal.";
                userLabel.innerText = "NIP / Username";
                userInput.placeholder = "Masukkan Username atau NIP Anda";
                toggleBtn.innerHTML = '<i class="bi bi-arrow-left me-1"></i> Kembali ke Portal Siswa';
            }
        }
        // Show/Hide Password Feature
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    </script>

    <!-- SweetAlert2 Notification Container -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '{{ $error }}<br>';
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    html: errorMessages,
                    confirmButtonColor: '#1152b4',
                    confirmButtonText: 'Coba Lagi'
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif
        });
    </script>
</body>
</html>

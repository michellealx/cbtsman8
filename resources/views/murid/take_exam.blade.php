<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} - CBT SMAN 8</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        /* Top Bar */
        .exam-topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px;
        }
        .exam-topbar .exam-title { font-weight: 700; font-size: 0.95rem; color: #1e293b; }
        .exam-topbar .exam-subject { font-size: 0.78rem; color: #64748b; }

        /* Timer */
        .timer-badge {
            display: flex; align-items: center; gap: 8px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 6px 14px;
        }
        .timer-badge .timer-dot { width: 8px; height: 8px; border-radius: 50%; background: #16a34a; animation: pulse 1.5s infinite; }
        .timer-badge .timer-time { font-size: 1rem; font-weight: 700; font-variant-numeric: tabular-nums; color: #1e293b; letter-spacing: 1px; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .timer-badge.warning .timer-dot { background: #d97706; }
        .timer-badge.warning .timer-time { color: #d97706; }
        .timer-badge.danger .timer-dot { background: #dc2626; }
        .timer-badge.danger .timer-time { color: #dc2626; }

        /* Main layout */
        .exam-body { padding-top: 80px; padding-bottom: 100px; }

        /* Progress bar */
        .progress-track {
            background: #e2e8f0; border-radius: 99px; height: 4px; overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(90deg, #1152b4, #3b82f6);
            height: 100%; border-radius: 99px;
            transition: width 0.4s ease;
        }

        /* Question nav dots */
        .q-nav { display: flex; flex-wrap: wrap; gap: 6px; }
        .q-dot {
            width: 32px; height: 32px; border-radius: 8px;
            font-size: 0.78rem; font-weight: 600;
            border: 1.5px solid #cbd5e1; background: #fff; color: #64748b;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .15s;
        }
        .q-dot.answered { background: #1152b4; border-color: #1152b4; color: #fff; }
        .q-dot.active { border-color: #1152b4; color: #1152b4; background: #eff6ff; }

        /* Question card */
        .question-card {
            display: none;
            animation: fadeIn .25s ease;
        }
        .question-card.active { display: block; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

        .question-number {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; color: #1152b4;
        }
        .question-text { font-size: 1.05rem; font-weight: 600; color: #1e293b; line-height: 1.7; }

        /* Options */
        .option-item { display: none; }
        .option-label {
            display: flex; align-items: center; gap: 14px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            padding: 14px 16px; cursor: pointer;
            transition: all .15s; background: #fff; margin-bottom: 10px;
            font-size: 0.9rem; color: #374151;
        }
        .option-label:hover { border-color: #93c5fd; background: #eff6ff; }
        .option-item:checked + .option-label {
            border-color: #1152b4; background: #eff6ff; color: #1152b4; font-weight: 600;
        }
        .option-letter {
            width: 32px; height: 32px; flex-shrink: 0;
            border-radius: 8px; border: 1.5px solid #cbd5e1;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem; color: #64748b;
            transition: all .15s;
        }
        .option-item:checked + .option-label .option-letter {
            border-color: #1152b4; background: #1152b4; color: #fff;
        }

        /* Essay textarea */
        .essay-textarea {
            width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
            padding: 14px 16px; font-size: 0.9rem; color: #374151; resize: vertical;
            min-height: 160px; outline: none; transition: border-color .15s;
        }
        .essay-textarea:focus { border-color: #1152b4; }

        /* Bottom nav */
        .exam-bottombar {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #fff; border-top: 1px solid #e2e8f0;
            padding: 14px 24px;
            display: flex; align-items: center; justify-content: space-between;
            z-index: 100;
        }
        .btn-nav {
            border: 1.5px solid #e2e8f0; background: #fff; color: #374151;
            border-radius: 10px; padding: 9px 20px; font-size: 0.875rem; font-weight: 600;
            cursor: pointer; transition: all .15s;
        }
        .btn-nav:hover { background: #f8fafc; }
        .btn-nav:disabled { opacity: .4; cursor: default; }
        .btn-next {
            background: #1152b4; color: #fff; border: none;
            border-radius: 10px; padding: 9px 28px; font-size: 0.875rem; font-weight: 600;
            cursor: pointer; transition: all .15s;
        }
        .btn-next:hover { background: #1e40af; }
        .btn-submit {
            background: #16a34a; color: #fff; border: none;
            border-radius: 10px; padding: 9px 28px; font-size: 0.875rem; font-weight: 600;
            cursor: pointer; transition: all .15s;
        }
        .btn-submit:hover { background: #15803d; }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,.35);
            z-index: 999; align-items: center; justify-content: center;
        }
        .modal-overlay.show { display: flex; }
        .modal-box {
            background: #fff; border-radius: 16px; padding: 32px;
            max-width: 420px; width: 90%; text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,.15);
        }
        .modal-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: #fef3c7; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
    </style>
</head>
<body>

    @if(!$exam->require_seb)
    <!-- Anti-Cheat Overlay -->
    <div id="antiCheatOverlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; text-align: center; flex-direction: column; padding: 2rem;">
        <h2 class="text-danger fw-bold mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-exclamation-triangle-fill mb-1 me-2" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
            </svg>
            MODE UJIAN AMAN
        </h2>
        <p class="fs-5 mb-4" style="max-width: 600px;">Anda wajib masuk ke mode <strong>Layar Penuh (Fullscreen)</strong>. <br>Segala bentuk aktivitas keluar dari ujian (pindah tab, split screen, dsb) akan dicatat sebagai pelanggaran dan dapat membuat ujian terkumpul otomatis.</p>
        <div id="violationAlert" class="alert alert-warning fw-bold mb-4" style="display: none; max-width: 500px;">
            <span id="violationMessage"></span><br>
            <span class="text-danger">Pelanggaran ke: <span id="violationCount">0</span> dari 3 maksimal.</span>
        </div>
        <button id="btnStartFullscreen" class="btn btn-primary btn-lg px-5 fw-bold">Mulai / Kembali ke Ujian</button>
    </div>
    @endif

{{-- Top Bar --}}
<div class="exam-topbar">
    <div>
        <div class="exam-title">{{ $exam->title }}</div>
        <div class="exam-subject">{{ $exam->subject }}</div>
    </div>
    <div class="timer-badge" id="timerBadge">
        <div class="timer-dot" id="timerDot"></div>
        <div class="timer-time" id="timerDisplay">--:--</div>
    </div>
</div>

<div class="exam-body">
    <div class="container-fluid px-3 px-md-5">

        {{-- Progress --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="progress-track flex-grow-1">
                <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
            </div>
            <div class="text-muted" style="font-size: 0.8rem; white-space:nowrap;" id="progressText">
                0 / {{ $exam->questions->count() }}
            </div>
        </div>

        <div class="row gap-0" style="gap:0">
            {{-- Question Area --}}
            <div class="col-lg-8 mb-4">
                <form id="examForm" action="{{ route('murid.exams.submit', $exam->id) }}" method="POST">
                    @csrf

                    @foreach($exam->questions as $index => $question)
                    <div class="question-card {{ $index === 0 ? 'active' : '' }}" id="qcard-{{ $index }}" data-index="{{ $index }}" data-qid="{{ $question->id }}">
                        <div class="card border-0 bg-transparent">
                            <div class="card-body p-0 p-md-2">
                                <div class="question-number mb-3">Soal {{ $index + 1 }} dari {{ $exam->questions->count() }}</div>
                                <div class="question-text mb-4" style="overflow-x:auto;">{!! $question->question_text !!}</div>

                                @if($question->type === 'multiple_choice')
                                    <div>
                                        @foreach(['A','B','C','D','E'] as $opt)
                                            @php $optVal = $question->{'option_'.strtolower($opt)}; @endphp
                                            @if($optVal)
                                            <div>
                                                <input class="option-item" type="radio" name="answers[{{ $question->id }}]" id="q{{ $question->id }}_{{ $opt }}" value="{{ $opt }}" {{ (isset($existingAnswers[$question->id]) && $existingAnswers[$question->id] === $opt) ? 'checked' : '' }}>
                                                <label class="option-label" for="q{{ $question->id }}_{{ $opt }}">
                                                    <div class="option-letter">{{ $opt }}</div>
                                                    <span style="overflow-x:auto;">{!! $optVal !!}</span>
                                                </label>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>

                                @else
                                    <textarea
                                        class="essay-textarea"
                                        name="answers[{{ $question->id }}]"
                                        placeholder="Tuliskan jawaban Anda di sini..."
                                        oninput="markAnswered({{ $index }})">{{ $existingAnswers[$question->id] ?? '' }}</textarea>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                </form>
            </div>

            {{-- Sidebar: question nav --}}
            <div class="col-lg-4 ps-lg-3 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius:14px; position:sticky; top:80px;">
                    <div class="card-body p-3">

                        @php
                            $pgQuestions     = $exam->questions->where('type', 'multiple_choice')->values();
                            $essayQuestions  = $exam->questions->where('type', 'essay')->values();
                            // Build a map: question id => overall index (for goToQuestion)
                            $indexMap = [];
                            foreach ($exam->questions as $i => $q) {
                                $indexMap[$q->id] = $i;
                            }
                        @endphp

                        @if($pgQuestions->count() > 0)
                        <div class="mb-1" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#1152b4;">
                            Pilihan Ganda ({{ $pgQuestions->count() }})
                        </div>
                        <div class="q-nav mb-3" id="qNavPG">
                            @foreach($pgQuestions as $question)
                            @php $overallIndex = $indexMap[$question->id]; @endphp
                            <div class="q-dot {{ $overallIndex === 0 ? 'active' : '' }}" id="qdot-{{ $overallIndex }}" onclick="goToQuestion({{ $overallIndex }})">
                                {{ $loop->iteration }}
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if($essayQuestions->count() > 0)
                        <div class="mb-1" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#0891b2;">
                            Essay ({{ $essayQuestions->count() }})
                        </div>
                        <div class="q-nav" id="qNavEssay">
                            @foreach($essayQuestions as $question)
                            @php $overallIndex = $indexMap[$question->id]; @endphp
                            <div class="q-dot {{ $overallIndex === 0 && $pgQuestions->count() === 0 ? 'active' : '' }}" id="qdot-{{ $overallIndex }}" onclick="goToQuestion({{ $overallIndex }})">
                                {{ $loop->iteration }}
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="mt-3 pt-3 border-top d-flex gap-2 flex-wrap" style="font-size:0.75rem; color:#64748b;">
                            <div class="d-flex align-items-center gap-1">
                                <div style="width:10px;height:10px;border-radius:3px;background:#1152b4;"></div> Dijawab
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <div style="width:10px;height:10px;border-radius:3px;background:#fff;border:1.5px solid #cbd5e1;"></div> Belum
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bottom Nav --}}
<div class="exam-bottombar">
    <button class="btn-nav" id="btnPrev" onclick="prevQuestion()" disabled>
        &larr; Sebelumnya
    </button>
    <div id="centerInfo" class="text-muted" style="font-size:0.8rem;">
        Soal <span id="currentNum">1</span> dari {{ $exam->questions->count() }}
    </div>
    <div id="btnNextWrap">
        <button class="btn-next" id="btnNext" onclick="nextQuestion()">
            Selanjutnya &rarr;
        </button>
    </div>
    <div id="btnSubmitWrap" style="display:none;">
        <button class="btn-submit" onclick="openSubmitModal()">
            Kumpulkan Jawaban
        </button>
    </div>
</div>

{{-- Submit Modal --}}
<div class="modal-overlay" id="submitModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#d97706" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg>
        </div>
        <h5 class="fw-bold mb-2">Kumpulkan Jawaban?</h5>
        <p class="text-muted mb-4" style="font-size:0.875rem;">
            Anda telah menjawab <strong id="answeredCount">0</strong> dari <strong>{{ $exam->questions->count() }}</strong> soal.
            Jawaban yang belum diisi akan dianggap kosong.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <button class="btn-nav px-4" onclick="closeSubmitModal()">Batal</button>
            <button class="btn-submit px-4" onclick="submitExam()">Ya, Kumpulkan</button>
        </div>
    </div>
</div>

<script>
    const TOTAL = {{ $exam->questions->count() }};
    const REMAINING_SECONDS = {{ $remainingSeconds }};
    let CLIENT_END_TIME = Date.now() + (REMAINING_SECONDS * 1000);
    let current = 0;
    const answered = new Array(TOTAL).fill(false);
    @foreach($exam->questions as $index => $q)
        @if(isset($existingAnswers[$q->id]))
            answered[{{ $index }}] = true;
        @endif
    @endforeach
    let isSubmitting = false;

    // Call once to fill progress bar on load
    document.addEventListener("DOMContentLoaded", () => updateProgress());

    // ─── Timer ───────────────────────────────────────
    const timerDisplay = document.getElementById('timerDisplay');
    const timerBadge   = document.getElementById('timerBadge');

    function updateTimer() {
        const remaining = Math.max(0, CLIENT_END_TIME - Date.now());
        const h = Math.floor(remaining / 3600000);
        const m = Math.floor((remaining % 3600000) / 60000);
        const s = Math.floor((remaining % 60000) / 1000);

        timerDisplay.textContent =
            (h > 0 ? String(h).padStart(2,'0') + ':' : '') +
            String(m).padStart(2,'0') + ':' +
            String(s).padStart(2,'0');

        timerBadge.className = 'timer-badge';
        if (remaining <= 0) {
            clearInterval(timerInterval);
            alert('Waktu habis! Jawaban Anda otomatis dikumpulkan.');
            isSubmitting = true;
            document.getElementById('examForm').submit();
        } else if (remaining < 5 * 60 * 1000) {
            timerBadge.classList.add('danger');
        } else if (remaining < 10 * 60 * 1000) {
            timerBadge.classList.add('warning');
        }
    }
    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    // ─── Real-time Time Sync ──────────────────────────
    function pollTime() {
        if (isSubmitting) return;
        fetch("{{ route('murid.exams.check_time', $exam->id) }}")
            .then(response => response.json())
            .then(data => {
                if (data.remainingSeconds !== undefined) {
                    // Update CLIENT_END_TIME with server's new remaining time
                    CLIENT_END_TIME = Date.now() + (data.remainingSeconds * 1000);
                }
            })
            .catch(error => console.error('Error fetching time:', error));
    }
    // Cek tambahan waktu ke server setiap 30 detik
    setInterval(pollTime, 30000);

    // ─── Navigation ──────────────────────────────────
    function showQuestion(idx) {
        document.querySelectorAll('.question-card').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.q-dot').forEach(d => d.classList.remove('active'));

        document.getElementById('qcard-' + idx).classList.add('active');
        document.getElementById('qdot-' + idx).classList.add('active');

        document.getElementById('currentNum').textContent = idx + 1;
        document.getElementById('btnPrev').disabled = idx === 0;

        const isLast = idx === TOTAL - 1;
        document.getElementById('btnNextWrap').style.display   = isLast ? 'none'  : 'block';
        document.getElementById('btnSubmitWrap').style.display = isLast ? 'block' : 'none';

        current = idx;
        updateProgress();
    }

    function nextQuestion() {
        if (current < TOTAL - 1) showQuestion(current + 1);
    }

    function prevQuestion() {
        if (current > 0) showQuestion(current - 1);
    }

    function goToQuestion(idx) {
        showQuestion(idx);
    }

    // ─── Answer tracking ─────────────────────────────
    function markAnswered(idx) {
        answered[idx] = true;
        const dot = document.getElementById('qdot-' + idx);
        dot.classList.remove('active');
        dot.classList.add('answered');
        updateProgress();
        // restore active class on current
        document.getElementById('qdot-' + current).classList.add('active');
    }

    // Listen radio changes
    document.querySelectorAll('.option-item').forEach(radio => {
        radio.addEventListener('change', function() {
            // Find index of this question's card
            const card = this.closest('.question-card');
            const idx = parseInt(card.dataset.index);
            markAnswered(idx);
            saveAnswer(card.dataset.qid, this.value);
        });
    });

    // Listen textarea changes
    let debounceTimer;
    document.querySelectorAll('.essay-textarea').forEach(txt => {
        txt.addEventListener('input', function() {
            const card = this.closest('.question-card');
            const idx = parseInt(card.dataset.index);
            if (this.value.trim().length > 0) {
                markAnswered(idx);
            }
            
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                saveAnswer(card.dataset.qid, this.value);
            }, 1000); // debounce 1 second
        });
    });

    function saveAnswer(questionId, val) {
        if (isSubmitting) return;
        fetch("{{ route('murid.exams.autosave', $exam->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ question_id: questionId, answer: val })
        }).catch(err => console.error('Autosave error:', err));
    }

    function updateProgress() {
        const count = answered.filter(Boolean).length;
        const pct   = (count / TOTAL) * 100;
        document.getElementById('progressFill').style.width = pct + '%';
        document.getElementById('progressText').textContent = count + ' / ' + TOTAL;
    }

    // ─── Submit modal ────────────────────────────────
    function openSubmitModal() {
        document.getElementById('answeredCount').textContent = answered.filter(Boolean).length;
        document.getElementById('submitModal').classList.add('show');
    }

    function closeSubmitModal() {
        document.getElementById('submitModal').classList.remove('show');
    }

    function submitExam() {
        isSubmitting = true;
        document.getElementById('examForm').submit();
    }

    // Initialize first question state
    showQuestion(0);

    // ─── Anti-Cheat Mechanics ────────────────────────
    @if(!$exam->require_seb)
    const examIdKey = 'violations_' + {{ $exam->id }};
    let violations = parseInt(localStorage.getItem(examIdKey) || 0);
    const maxViolations = 3;

    if (violations >= maxViolations) {
        isSubmitting = true;
        alert("PELANGGARAN MAKSIMAL TERCAPAI.\n\nUjian Anda dikumpulkan secara otomatis akibat pelanggaran sistem keamanan!");
        document.getElementById('examForm').submit();
    }

    const overlay = document.getElementById('antiCheatOverlay');
    const btnStart = document.getElementById('btnStartFullscreen');
    const violationAlert = document.getElementById('violationAlert');
    const violationMessage = document.getElementById('violationMessage');
    const violationCount = document.getElementById('violationCount');
    let hasStarted = false;

    function enterFullscreen() {
        let elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) { /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { /* IE11 */
            elem.msRequestFullscreen();
        }
    }

    btnStart.addEventListener('click', () => {
        enterFullscreen();
        hasStarted = true;
    });

    document.addEventListener('fullscreenchange', () => {
        if (isSubmitting) return;
        if (!document.fullscreenElement && hasStarted) {
            handleViolation("Anda terdeteksi keluar dari mode Layar Penuh!");
        } else if (document.fullscreenElement) {
            overlay.style.display = 'none';
        }
    });

    document.addEventListener('visibilitychange', () => {
        if (isSubmitting) return;
        if (document.hidden && hasStarted) {
            handleViolation("Anda terdeteksi membuka tab atau aplikasi lain!");
        }
    });

    // Deteksi menarik panel notifikasi (window kehilangan fokus)
    window.addEventListener('blur', () => {
        if (isSubmitting || !hasStarted) return;
        handleViolation("Anda terdeteksi membuka panel notifikasi atau berpindah dari ujian!");
    });

    // Blokir gesture tarik dari atas layar (mencegah panel notifikasi di HP)
    document.addEventListener('touchstart', (e) => {
        if (!hasStarted) return;
        const touch = e.touches[0];
        // Jika sentuhan dimulai dari 30px paling atas layar, batalkan
        if (touch.clientY <= 30) {
            e.preventDefault();
        }
    }, { passive: false });

    function handleViolation(message) {
        hasStarted = false; // Pause monitoring until they click start again to prevent double counting
        violations++;
        localStorage.setItem(examIdKey, violations);
        
        if (violations >= maxViolations) {
            isSubmitting = true;
            alert("PELANGGARAN MAKSIMAL TERCAPAI (" + maxViolations + "x).\n\nUjian Anda dikumpulkan secara otomatis!");
            document.getElementById('examForm').submit();
        } else {
            violationMessage.textContent = message;
            violationCount.textContent = violations;
            violationAlert.style.display = 'block';
            overlay.style.display = 'flex';
            if (document.fullscreenElement) {
                document.exitFullscreen(); // force them to click start again
            }
        }
    }

    // Anti Copy Paste & Right Click
    document.addEventListener('contextmenu', event => event.preventDefault());
    document.addEventListener('copy', event => event.preventDefault());
    document.addEventListener('paste', event => {
        // Biarkan paste berjalan di textarea (karena untuk essay)
        if (event.target.tagName !== 'TEXTAREA') {
            event.preventDefault();
        }
    });
    
    // Anti Keyboard Shortcuts (F12, Ctrl+C, Ctrl+V, etc)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F12' || 
           (e.ctrlKey && (e.key === 'c' || e.key === 'p' || e.key === 'u' || e.key === 's'))) {
            e.preventDefault();
        }
    });
    @endif
</script>
</body>
</html>

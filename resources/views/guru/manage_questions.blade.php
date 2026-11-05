@extends('layouts.dashboard')

@section('title', 'Manajemen Soal Ujian')
@section('page-title', 'Manajemen Soal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="h4 mb-0 fw-bold">Manajemen Soal</h1>
            @if($exam->status === 'draft')
                <span class="badge bg-secondary">Draft</span>
            @else
                <span class="badge bg-success">Published</span>
            @endif
        </div>
        <p class="text-muted mb-0">
            {{ $exam->title }} ({{ $exam->subject }}) - {{ $exam->duration_minutes }} Menit
            @if(!$exam->deadline || $exam->deadline > now())
                <a href="#" class="text-primary text-decoration-none ms-2 small" data-bs-toggle="modal" data-bs-target="#durationModal">
                    <i class="bi bi-pencil-square"></i> Ubah Durasi
                </a>
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('guru.exams') }}" class="btn btn-outline-secondary">
            Kembali
        </a>

        @if($exam->status === 'draft')
        <button type="button" class="btn btn-success fw-bold" id="btn-publish-trigger">
            <i class="bi bi-send me-1"></i> Terbitkan
        </button>
        @endif
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
        <div>
            <h6 class="mb-1 fw-bold">Gagal menyimpan soal!</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary">Tambah Soal Baru</h5>
            </div>
            <div class="card-body p-4">
                <ul class="nav nav-pills mb-4 gap-2" id="questionTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4" id="pg-tab" data-bs-toggle="pill" data-bs-target="#pg-content" type="button" role="tab" aria-controls="pg-content" aria-selected="true">Pilihan Ganda</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4" id="essay-tab" data-bs-toggle="pill" data-bs-target="#essay-content" type="button" role="tab" aria-controls="essay-content" aria-selected="false">Essay</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="questionTabsContent">
                    <!-- Tab Pilihan Ganda -->
                    <div class="tab-pane fade show active" id="pg-content" role="tabpanel" aria-labelledby="pg-tab">
                        <form action="{{ route('guru.exams.questions.store', $exam->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="pg">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-6">Teks Pertanyaan (Bisa Paste Gambar)</label>
                                <textarea name="text" class="form-control summernote" required placeholder="Tuliskan pertanyaan di sini..."></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-6">Pilihan Jawaban</label>
                                <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i> Biarkan kotak teks kosong (tidak perlu diisi) untuk opsi yang tidak digunakan (misalnya, jika soal hanya sampai opsi C atau D).</p>
                                <div class="row g-3">
                                    @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1">
                                            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                                                <span class="fw-bold fs-6 text-dark">Opsi {{ $opt }}</span>
                                                <div>
                                                    <input type="radio" class="btn-check" name="correct_answer" id="radio{{ $opt }}" autocomplete="off" value="{{ $opt }}" required>
                                                    <label class="btn btn-outline-success btn-sm fw-bold rounded-pill px-3" for="radio{{ $opt }}">
                                                        <i class="bi bi-check-circle me-1"></i> Jadikan Kunci Jawaban
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <textarea name="options[{{ $opt }}]" class="form-control summernote" placeholder="Jawaban {{ $opt }}"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Simpan Soal Pilihan Ganda</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Tab Essay -->
                    <div class="tab-pane fade" id="essay-content" role="tabpanel" aria-labelledby="essay-tab">
                        <form action="{{ route('guru.exams.questions.store', $exam->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="essay">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-6">Teks Pertanyaan Essay (Bisa Paste Gambar)</label>
                                <textarea name="text" class="form-control summernote" required placeholder="Tuliskan pertanyaan essay di sini..."></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Simpan Soal Essay</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kolom Pilihan Ganda (Kiri) -->
    <div class="col-md-6 mb-4">
        <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
            Pilihan Ganda ({{ $exam->questions->where('type', 'multiple_choice')->count() }})
        </h6>
        
        @forelse($exam->questions->where('type', 'multiple_choice') as $index => $question)
            <div class="card border border-light shadow-sm mb-2">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-primary small"># {{ $loop->iteration }}</span>
                        <div>
                            <span class="badge bg-light text-dark border" style="font-size:0.75rem;">Poin: {{ $question->points }}</span>
                            <a href="{{ route('guru.exams.questions.edit', $question->id) }}" class="btn btn-sm btn-primary py-0 px-2 ms-2">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form action="{{ route('guru.exams.questions.destroy', $question->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Hapus soal pilihan ganda ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger py-0 px-2">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mb-2" style="font-size: 0.9rem; overflow-x: auto;">
                        {!! $question->question_text !!}
                    </div>
                    
                    <div class="d-flex flex-column gap-1">
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                            @php
                                $optCol = 'option_' . strtolower($opt);
                                $optVal = $question->$optCol;
                                $isCorrect = $question->correct_answer === $opt;
                            @endphp
                            @if($optVal)
                                <div class="d-flex align-items-center rounded p-1 px-2 {{ $isCorrect ? 'bg-success bg-opacity-10 border border-success' : 'bg-light' }}" style="font-size: 0.85rem;">
                                    <strong class="me-2 {{ $isCorrect ? 'text-success' : '' }}">{{ $opt }}.</strong>
                                    <div class="flex-grow-1" style="overflow-x: auto; margin-bottom: 0;">{!! $optVal !!}</div>
                                    @if($isCorrect) 
                                        <i class="bi bi-check-circle-fill text-success ms-2"></i> 
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 bg-light rounded text-muted small">
                Belum ada soal Pilihan Ganda
            </div>
        @endforelse
    </div>

    <!-- Kolom Essay (Kanan) -->
    <div class="col-md-6 mb-4">
        <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
            Essay ({{ $exam->questions->where('type', 'essay')->count() }})
        </h6>
        
        @forelse($exam->questions->where('type', 'essay') as $index => $question)
            <div class="card border border-light shadow-sm mb-2">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-info small"># {{ $loop->iteration }}</span>
                        <div>
                            <a href="{{ route('guru.exams.questions.edit', $question->id) }}" class="btn btn-sm btn-primary py-0 px-2 ms-2">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form action="{{ route('guru.exams.questions.destroy', $question->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Hapus soal essay ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger py-0 px-2">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <div style="font-size: 0.9rem; overflow-x: auto;">
                        {!! $question->question_text !!}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 bg-light rounded text-muted small">
                Belum ada soal Essay
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        let isDirty = false;

        // Detect changes in standard inputs (ignore modals)
        $('form input, form select, form textarea').on('input change', function() {
            if (!$(this).closest('.modal').length) {
                isDirty = true;
            }
        });

        // Disable warning when submitting any form
        $('form').on('submit', function() {
            isDirty = false;
        });

        // Warn before leaving if form is dirty
        window.addEventListener('beforeunload', function(e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = ''; // Required for Chrome
            }
        });

        // Intercept Publish Button
        $('#btn-publish-trigger').on('click', function() {
            if (isDirty) {
                // If sweetalert is available use it, otherwise use standard alert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Soal Belum Disimpan!',
                        text: 'Anda sedang mengetik soal. Silakan klik tombol "Simpan Soal" terlebih dahulu sebelum menerbitkan ujian.',
                        confirmButtonColor: '#1152b4',
                        confirmButtonText: 'Mengerti'
                    });
                } else {
                    alert('Peringatan: Anda sedang mengetik soal. Silakan klik tombol "Simpan Soal" terlebih dahulu sebelum menerbitkan ujian.');
                }
            } else {
                $('#publishModal').modal('show');
            }
        });

        // Bersihkan semua draft lama dari localStorage untuk ujian ini
        var examId = '{{ $exam->id }}';
        Object.keys(localStorage).forEach(function(key) {
            if (key.startsWith('draft_exam_' + examId + '_')) {
                localStorage.removeItem(key);
            }
        });

        $('.summernote').summernote({
            height: 120,
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['insert', ['picture']],
            ],
            placeholder: 'Ketik di sini atau Paste gambar dari Word...',
            callbacks: {
                onChange: function(contents, $editable) {
                    isDirty = true;
                }
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modal Terbitkan Ujian -->
<div class="modal fade" id="publishModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Terbitkan Ujian ke Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.exams.publish', $exam->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Pilih kelas mana saja yang dapat mengakses ujian ini. Ujian yang sudah diterbitkan akan langsung muncul di halaman murid pada kelas yang dipilih.</p>
                    
                    <div class="list-group mb-3">
                        @forelse($classrooms as $classroom)
                        <label class="list-group-item d-flex gap-2">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="classrooms[]" value="{{ $classroom->id }}">
                            <span>
                                Kelas {{ $classroom->name }}
                            </span>
                        </label>
                        @empty
                        <div class="text-muted text-center py-3">Belum ada kelas yang Anda buat. Silakan tambahkan kelas di menu Kelas terlebih dahulu.</div>
                        @endforelse
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Batas Waktu (Deadline) Ujian</label>
                        <input type="datetime-local" name="deadline" class="form-control" value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" required>
                        <small class="text-muted">Secara default disetel 24 jam ke depan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold" {{ count($classrooms) == 0 ? 'disabled' : '' }}>Terbitkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ubah Durasi -->
@if(!$exam->deadline || $exam->deadline > now())
<div class="modal fade" id="durationModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ubah Durasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('guru.exams.update_duration', $exam->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" class="form-control" value="{{ $exam->duration_minutes }}" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

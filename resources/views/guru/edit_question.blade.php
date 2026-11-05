@extends('layouts.dashboard')

@section('title', 'Edit Soal')
@section('page-title', 'Edit Soal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Edit Soal</h1>
        <p class="text-muted mb-0">Ubah pertanyaan atau jawaban soal ini.</p>
    </div>
    <a href="{{ route('guru.exams.questions', $exam->id) }}" class="btn btn-outline-secondary">
        Batal
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('guru.exams.questions.update', $question->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="type" value="{{ $question->type === 'multiple_choice' ? 'pg' : 'essay' }}">
            
            @if($question->type === 'multiple_choice')
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-6">Teks Pertanyaan</label>
                    <textarea name="text" class="form-control summernote" required>{{ $question->question_text }}</textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-6">Pilihan Jawaban</label>
                    <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i> Biarkan kotak teks kosong (tidak perlu diisi) untuk opsi yang tidak digunakan.</p>
                    <div class="row g-3">
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                        @php
                            $optField = 'option_' . strtolower($opt);
                        @endphp
                        <div class="col-md-6">
                            <div class="card h-100 border-1">
                                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-6 text-dark">Opsi {{ $opt }}</span>
                                    <div>
                                        <input type="radio" class="btn-check" name="correct_answer" id="radio{{ $opt }}" autocomplete="off" value="{{ $opt }}" {{ $question->correct_answer === $opt ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-success btn-sm fw-bold rounded-pill px-3" for="radio{{ $opt }}">
                                            <i class="bi bi-check-circle me-1"></i> Jadikan Kunci Jawaban
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <textarea name="options[{{ $opt }}]" class="form-control summernote" placeholder="Jawaban {{ $opt }}">{{ $question->$optField }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-6">Teks Pertanyaan Essay</label>
                    <textarea name="text" class="form-control summernote" required>{{ $question->question_text }}</textarea>
                </div>
            @endif
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ],
            placeholder: 'Tuliskan teks di sini...'
        });
    });
</script>
@endsection

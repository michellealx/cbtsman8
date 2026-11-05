@extends('layouts.dashboard')

@section('title', 'Koreksi Ujian: {{ $exam->title }}')
@section('page-title', 'Koreksi Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Koreksi: {{ $exam->title }}</h1>
        <p class="text-muted mb-0">{{ $exam->subject }} &bull; Klik nama murid untuk memberikan nilai essay.</p>
    </div>
    <a href="{{ route('guru.results') }}" class="btn btn-outline-secondary">← Kembali ke Hasil Ujian</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@forelse($results as $result)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <div>
            <span class="fw-bold fs-6">{{ $result->student->name }}</span>
            <small class="text-muted ms-2">( {{ $result->student->username }} )</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            @if($result->status === 'pending_grading')
                <span class="badge bg-warning text-dark">Belum Dikoreksi</span>
            @else
                <span class="badge bg-success fs-6">Nilai: {{ $result->score }}</span>
            @endif
            <small class="text-muted">Selesai: {{ $result->finished_at->format('d M Y, H:i') }}</small>
        </div>
    </div>
    
    <form action="{{ route('guru.exams.grade.submit', $result->id) }}" method="POST">
        @csrf
        <div class="card-body">
            @foreach($result->answers as $answer)
            <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="d-flex align-items-start">
                    <span class="badge bg-{{ $answer->question->type == 'multiple_choice' ? 'primary' : 'info text-dark' }} me-2 flex-shrink-0 mt-1">
                        {{ $answer->question->type == 'multiple_choice' ? 'PG' : 'Essay' }}
                    </span>
                    <div class="w-100">
                        <div class="fw-semibold mb-2" style="overflow-x:auto;">{!! $answer->question->question_text !!}</div>
                        
                        @if($answer->question->type === 'multiple_choice')
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small">Jawaban murid:</span>
                                <span class="badge {{ $answer->is_correct ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $answer->answer ?? '-' }}
                                </span>
                                <span class="text-muted small">| Kunci:
                                    <span class="fw-bold text-success">{{ $answer->question->correct_answer }}</span>
                                </span>
                                @if($answer->is_correct)
                                    <span class="text-success small"><i class="bi bi-check-circle-fill"></i> Benar (+{{ $answer->question->points }} poin)</span>
                                @else
                                    <span class="text-danger small"><i class="bi bi-x-circle-fill"></i> Salah (0 poin)</span>
                                @endif
                            </div>
                        @else
                            {{-- Essay --}}
                            <div class="bg-light rounded-3 p-3 mb-3">
                                <p class="mb-0 fst-italic">
                                    @if($answer->answer)
                                        {!! nl2br(e($answer->answer)) !!}
                                    @else
                                        <span class="text-muted">Tidak dijawab.</span>
                                    @endif
                                </p>
                            </div>
                            @if($result->status === 'completed')
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Nilai yang diberikan:</span>
                                    <span class="badge bg-success fs-6">{{ $answer->score ?? 0 }} / {{ $answer->question->points }} poin</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <label class="fw-semibold small text-nowrap">Nilai Essay (0 - {{ $answer->question->points }}):</label>
                                    <input type="number"
                                        name="essay_scores[{{ $answer->id }}]"
                                        class="form-control form-control-sm"
                                        style="width: 90px;"
                                        min="0"
                                        max="{{ $answer->question->points }}"
                                        value="{{ $answer->score ?? 0 }}"
                                        required>
                                    <span class="text-muted small">/ {{ $answer->question->points }} poin</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($result->status === 'pending_grading')
        <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">
            <button type="submit" class="btn btn-primary fw-bold px-4">
                <i class="bi bi-check-lg me-1"></i>Simpan Nilai
            </button>
        </div>
        @endif
    </form>
</div>
@empty
<div class="card border-0 text-center py-5">
    <div class="card-body">
        <h5 class="text-muted fw-semibold">Belum ada murid yang mengerjakan ujian ini.</h5>
    </div>
</div>
@endforelse
@endsection

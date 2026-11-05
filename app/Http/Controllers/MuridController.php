<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MuridController extends Controller
{
    public function exams(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        if ($redirect = $this->requireRole($request, ['murid'])) {
            return $redirect;
        }

        $userClassStr = session('user')['kelas'] ?? null;
        if (!$userClassStr) {
            $user = \App\Models\User::find(session('user')['id']);
            $userClassStr = $user->kelas ?? null;
        }
        
        $classroomIds = \App\Models\Classroom::where('name', $userClassStr)->pluck('id');

        $search = $request->query('search');
        $date = $request->query('date');

        $exams = collect();
        if ($classroomIds->isNotEmpty()) {
            $query = \App\Models\Exam::where('status', 'published')
                ->where(function($q) {
                    $q->whereNull('deadline')
                      ->orWhere('deadline', '>=', now());
                })
                ->whereHas('sessions', function($q) use ($classroomIds) {
                    $q->whereIn('classroom_id', $classroomIds)
                      ->where('is_active', true);
                });

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%");
                });
            }

            if ($date) {
                $query->whereDate('created_at', $date);
            }

            $exams = $query->withCount('questions')->with('teacher')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Get exam IDs that this student has already finished
        $completedResults = \App\Models\ExamResult::where('student_id', session('user')['id'])
            ->whereNotNull('finished_at')
            ->get()
            ->keyBy('exam_id');

        // Only show exams not yet completed
        $completedExamIds = $completedResults->keys()->toArray();
        $exams = $exams->reject(fn($exam) => in_array($exam->id, $completedExamIds));

        return $this->view($request, 'murid.exams', [
            'exams'            => $exams,
            'completedResults' => $completedResults,
            'search'           => $search,
            'date'             => $date
        ]);
    }

    public function examDetail(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        if ($redirect = $this->requireRole($request, ['murid'])) {
            return $redirect;
        }

        $exam = \App\Models\Exam::withCount('questions')->findOrFail($id);
        
        $result = \App\Models\ExamResult::where('exam_id', $exam->id)
            ->where('student_id', session('user')['id'])
            ->first();
            
        return $this->view($request, 'murid.exam', [
            'exam' => $exam,
            'result' => $result
        ]);
    }

    public function startExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['murid'])) return $redirect;

        $exam = \App\Models\Exam::findOrFail($id);
        $studentId = session('user')['id'];

        if ($exam->deadline && now()->greaterThan($exam->deadline)) {
            return redirect()->route('murid.exams')->with('error', 'Waktu pengerjaan ujian ini sudah kedaluwarsa.');
        }

        if ($exam->require_seb) {
            $userAgent = $request->header('User-Agent');
            if (stripos($userAgent, 'SEB') === false && stripos($userAgent, 'SafeExamBrowser') === false) {
                return redirect()->route('murid.exam', $exam->id)->with('error', 'Akses ditolak! Anda wajib menggunakan aplikasi Safe Exam Browser (SEB) untuk mengikuti ujian ini.');
            }
        }

        $result = \App\Models\ExamResult::firstOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $studentId],
            [
                'total_questions' => $exam->questions()->count(),
                'started_at' => now(),
            ]
        );

        if ($result->finished_at) {
            return redirect()->route('murid.scores')->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        return redirect()->route('murid.exams.take', $exam->id);
    }

    public function takeExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['murid'])) return $redirect;

        $exam = \App\Models\Exam::with('questions')->findOrFail($id);
        $studentId = session('user')['id'];

        if ($exam->deadline && now()->greaterThan($exam->deadline)) {
            return redirect()->route('murid.exams')->with('error', 'Waktu pengerjaan ujian ini sudah kedaluwarsa.');
        }

        if ($exam->require_seb) {
            $userAgent = $request->header('User-Agent');
            if (stripos($userAgent, 'SEB') === false && stripos($userAgent, 'SafeExamBrowser') === false) {
                return redirect()->route('murid.exam', $exam->id)->with('error', 'Akses ditolak! Anda wajib menggunakan aplikasi Safe Exam Browser (SEB) untuk mengikuti ujian ini.');
            }
        }

        $result = \App\Models\ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$result || $result->finished_at) {
            return redirect()->route('murid.exams');
        }

        $extraTime = $result->extra_time ?? 0;
        $endTime = $result->started_at->copy()->addMinutes($exam->duration_minutes + $extraTime);
        $remainingSeconds = $endTime->getTimestamp() - now()->getTimestamp();

        if ($remainingSeconds <= 0) {
            return redirect()->route('murid.exams.submit', $exam->id); // Auto submit if time passed but not finished
        }

        if ($exam->randomize_questions) {
            $exam->setRelation('questions', $exam->questions->sortBy(function ($q) use ($studentId, $exam) {
                $typeOrder = $q->type === 'essay' ? 1 : 0;
                $hash = md5($q->id . $studentId . $exam->id);
                return $typeOrder . '_' . $hash;
            })->values());
        }

        // Fetch existing answers for auto-save resumption
        $existingAnswers = \App\Models\StudentAnswer::where('exam_result_id', $result->id)
            ->pluck('answer', 'question_id')
            ->toArray();

        return $this->view($request, 'murid.take_exam', [
            'exam' => $exam,
            'result' => $result,
            'remainingSeconds' => $remainingSeconds,
            'existingAnswers' => $existingAnswers,
        ]);
    }

    public function checkTime(Request $request, $id)
    {
        $studentId = session('user')['id'] ?? null;
        if (!$studentId) return response()->json(['error' => 'Unauthorized'], 401);

        $exam = \App\Models\Exam::find($id);
        if (!$exam) return response()->json(['error' => 'Not found'], 404);

        $result = \App\Models\ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$result || $result->finished_at) {
            return response()->json(['remainingSeconds' => 0]);
        }

        $extraTime = $result->extra_time ?? 0;
        $endTime = $result->started_at->copy()->addMinutes($exam->duration_minutes + $extraTime);
        $remainingSeconds = $endTime->getTimestamp() - now()->getTimestamp();

        return response()->json([
            'remainingSeconds' => max(0, $remainingSeconds),
            'extraTime' => $extraTime
        ]);
    }

    public function autoSave(Request $request, $id)
    {
        $studentId = session('user')['id'] ?? null;
        if (!$studentId) return response()->json(['error' => 'Unauthorized'], 401);

        $exam = \App\Models\Exam::find($id);
        if (!$exam) return response()->json(['error' => 'Not found'], 404);

        $result = \App\Models\ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$result || $result->finished_at) {
            return response()->json(['error' => 'Exam finished or not started'], 400);
        }

        $questionId = $request->input('question_id');
        $answerStr = $request->input('answer');
        
        $question = \App\Models\Question::where('exam_id', $exam->id)->find($questionId);
        if (!$question) return response()->json(['error' => 'Invalid question'], 400);

        $isCorrect = null;
        if ($question->type === 'multiple_choice') {
            $isCorrect = $answerStr === $question->correct_answer;
        }

        \App\Models\StudentAnswer::updateOrCreate(
            ['exam_result_id' => $result->id, 'question_id' => $question->id],
            [
                'answer' => $answerStr,
                'is_correct' => $isCorrect
            ]
        );

        return response()->json(['success' => true]);
    }

    public function submitExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['murid'])) return $redirect;

        $exam = \App\Models\Exam::with('questions')->findOrFail($id);
        $studentId = session('user')['id'];

        $result = \App\Models\ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$result || $result->finished_at) {
            return redirect()->route('murid.scores');
        }

        $answers = $request->input('answers', []);
        $correctCount = 0;
        $pgCount = 0;
        $hasEssay = false;

        foreach ($exam->questions as $question) {
            $studentAnswerStr = $answers[$question->id] ?? null;
            $isCorrect = false;
            
            if ($question->type === 'essay') {
                $hasEssay = true;
            }

            if ($question->type === 'multiple_choice') {
                $pgCount++;
                $isCorrect = $studentAnswerStr === $question->correct_answer;
                if ($isCorrect) {
                    $correctCount++;
                }
            }

            \App\Models\StudentAnswer::updateOrCreate(
                ['exam_result_id' => $result->id, 'question_id' => $question->id],
                [
                    'answer' => $studentAnswerStr, 
                    'is_correct' => $question->type === 'multiple_choice' ? $isCorrect : null
                ]
            );
        }

        // Hitung nilai PG proporsional (maksimal 100)
        $pgScore = $pgCount > 0 ? round(($correctCount / $pgCount) * 100) : 0;

        $result->update([
            'score' => $hasEssay ? null : $pgScore,
            'correct_answers' => $correctCount,
            'status' => $hasEssay ? 'pending_grading' : 'completed',
            'finished_at' => now(),
        ]);

        return redirect()->route('murid.scores')->with('success', 'Ujian selesai. Jawaban Anda telah disimpan.');
    }

    public function scores(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        if ($redirect = $this->requireRole($request, ['murid'])) {
            return $redirect;
        }

        $results = \App\Models\ExamResult::with('exam')
            ->where('student_id', session('user')['id'])
            ->whereNotNull('finished_at')
            ->orderByDesc('finished_at')
            ->get();
            
        return $this->view($request, 'murid.scores', ['results' => $results]);
    }
}

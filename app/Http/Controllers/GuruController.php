<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function exams(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) {
            return $redirect;
        }

        $query = \App\Models\Exam::where('teacher_id', session('user')['id']);
        
        $search = $request->query('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $date = $request->query('date');
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $exams = $query->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return $this->view($request, 'guru.exams', [
            'exams' => $exams,
            'search' => $search,
            'date' => $date
        ]);
    }

    public function questions(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) {
            return $redirect;
        }

        $teacherId = session('user')['id'];

        // Ambil ujian milik guru yang deadlinenya sudah habis
        $exams = \App\Models\Exam::where('teacher_id', $teacherId)
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->withCount('questions')
            ->withCount('results')
            ->orderBy('deadline', 'desc')
            ->get();

        return $this->view($request, 'guru.questions', ['exams' => $exams]);
    }


    public function activeSessions(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];

        // Get active exam sessions (the classes assigned) for this teacher
        $sessions = \App\Models\ExamSession::whereHas('exam', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)
              ->where('status', 'published')
              ->where(function($q2) {
                  $q2->whereNull('deadline')
                     ->orWhere('deadline', '>=', now());
              });
        })
        ->where('is_active', true)
        ->with(['exam', 'classroom'])
        ->orderBy('created_at', 'desc')
        ->get();

        return $this->view($request, 'guru.active_sessions', ['sessions' => $sessions]);
    }

    public function showActiveSession(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $session = \App\Models\ExamSession::with(['exam', 'classroom'])->findOrFail($id);
        
        if ($session->exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.active_sessions')->with('error', 'Akses ditolak.');
        }

        $students = \App\Models\User::where('role', 'murid')
            ->where('kelas', $session->classroom->name)
            ->where('teacher_id', session('user')['id'])
            ->orderBy('name')
            ->get();

        $results = \App\Models\ExamResult::where('exam_id', $session->exam_id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return $this->view($request, 'guru.active_session_detail', [
            'session' => $session,
            'students' => $students,
            'results' => $results
        ]);
    }

    public function endSession(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $session = \App\Models\ExamSession::with('exam')->findOrFail($id);
        
        if ($session->exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.active_sessions')->with('error', 'Akses ditolak.');
        }

        // Set session to inactive
        $session->update(['is_active' => false]);

        // Force submit for all students in this class who are currently taking this exam
        $studentIds = \App\Models\User::where('role', 'murid')
            ->where('kelas', $session->classroom->name)
            ->where('teacher_id', session('user')['id'])
            ->pluck('id');

        \App\Models\ExamResult::where('exam_id', $session->exam_id)
            ->whereIn('student_id', $studentIds)
            ->whereNull('finished_at')
            ->update(['finished_at' => now()]);

        // Force the exam deadline to expire so it moves to Bank Soal
        $session->exam->update(['deadline' => now()->subMinute()]);

        return redirect()->route('guru.questions')->with('success', 'Sesi ujian untuk kelas ' . $session->classroom->name . ' telah berhasil diakhiri dan nilai murid disimpan.');
    }

    public function addExtraTime(Request $request, $resultId)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $request->validate([
            'extra_minutes' => 'required|integer|min:1|max:120'
        ]);

        $result = \App\Models\ExamResult::with('exam')->findOrFail($resultId);
        
        if ($result->exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.active_sessions')->with('error', 'Akses ditolak.');
        }

        $result->extra_time += $request->extra_minutes;
        $result->save();

        return redirect()->back()->with('success', "Berhasil menambahkan {$request->extra_minutes} menit untuk siswa {$result->student->name}.");
    }

    public function gradeExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = \App\Models\Exam::findOrFail($id);

        if ($exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        $results = \App\Models\ExamResult::with(['student', 'answers.question'])
            ->where('exam_id', $exam->id)
            ->whereNotNull('finished_at')
            ->get();

        return $this->view($request, 'guru.grade_exam', [
            'exam' => $exam,
            'results' => $results,
        ]);
    }

    public function submitGrade(Request $request, $resultId)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $result = \App\Models\ExamResult::with(['exam', 'answers.question'])->findOrFail($resultId);

        if ($result->exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        $essayScores = $request->input('essay_scores', []);
        
        $pgCount = 0;
        $pgCorrect = 0;
        $essayCount = 0;

        // Pertama hitung PG
        foreach ($result->answers as $answer) {
            if ($answer->question->type === 'multiple_choice') {
                $pgCount++;
                if ($answer->is_correct) {
                    $pgCorrect++;
                }
            } elseif ($answer->question->type === 'essay') {
                $essayCount++;
            }
        }
        
        // Hitung nilai PG murni (maks 100)
        $pgScoreRaw = $pgCount > 0 ? ($pgCorrect / $pgCount) * 100 : 0;

        // Kedua hitung Essay
        $essayPointsObtained = 0;
        $essayPointsMax = 0;

        foreach ($result->answers as $answer) {
            if ($answer->question->type === 'essay') {
                $score = isset($essayScores[$answer->id]) ? (int) $essayScores[$answer->id] : 0;
                $score = min($score, $answer->question->points); 
                $answer->update(['score' => $score, 'is_correct' => $score > 0]);
                
                $essayPointsObtained += $score;
                $essayPointsMax += $answer->question->points;
            }
        }
        
        // Hitung nilai Essay murni (maks 100)
        $essayScoreRaw = $essayPointsMax > 0 ? ($essayPointsObtained / $essayPointsMax) * 100 : 0;
        
        // Hitung Total Nilai Akhir
        $finalScore = 0;
        if ($pgCount > 0 && $essayCount > 0) {
            // Bobot: PG 60%, Essay 40%
            $finalScore = round(($pgScoreRaw * 0.60) + ($essayScoreRaw * 0.40));
        } elseif ($pgCount > 0) {
            $finalScore = round($pgScoreRaw);
        } elseif ($essayCount > 0) {
            $finalScore = round($essayScoreRaw);
        }

        $result->update([
            'score' => $finalScore,
            'status' => 'completed',
        ]);

        return redirect()->route('guru.exams.grade', $result->exam_id)->with('success', 'Penilaian berhasil disimpan.');
    }

    public function results(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];

        // Ambil kelas yang memiliki sesi ujian dari guru ini
        $classrooms = \App\Models\Classroom::where('teacher_id', $teacherId)->whereHas('examSessions', function($q) use ($teacherId) {
            $q->whereHas('exam', function($q2) use ($teacherId) {
                $q2->where('teacher_id', $teacherId);
            });
        })->get();

        // Hitung jumlah ujian dan hasil per kelas
        foreach ($classrooms as $classroom) {
            $classroom->exam_count = \App\Models\ExamSession::where('classroom_id', $classroom->id)
                ->whereHas('exam', function($q) use ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                })->count();

            $studentIds = \App\Models\User::where('role', 'murid')
                ->where('kelas', $classroom->name)
                ->pluck('id');

            $examIds = \App\Models\ExamSession::where('classroom_id', $classroom->id)
                ->whereHas('exam', function($q) use ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                })->pluck('exam_id');

            $classroom->done_count = \App\Models\ExamResult::whereIn('exam_id', $examIds)
                ->whereIn('student_id', $studentIds)
                ->whereNotNull('finished_at')
                ->count();

            $classroom->pending_count = \App\Models\ExamResult::whereIn('exam_id', $examIds)
                ->whereIn('student_id', $studentIds)
                ->where('status', 'pending_grading')
                ->count();
        }

        return $this->view($request, 'guru.results', ['classrooms' => $classrooms]);
    }

    public function resultsByClass(Request $request, $classroomId)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];
        $classroom = \App\Models\Classroom::where('id', $classroomId)->where('teacher_id', $teacherId)->firstOrFail();

        // Ambil ujian yang ditugaskan ke kelas ini oleh guru ini
        $examSessions = \App\Models\ExamSession::where('classroom_id', $classroomId)
            ->whereHas('exam', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->with('exam')
            ->orderBy('created_at', 'desc')
            ->get();

        $studentIds = \App\Models\User::where('role', 'murid')
            ->where('kelas', $classroom->name)
            ->pluck('id');

        foreach ($examSessions as $session) {
            $session->done_count = \App\Models\ExamResult::where('exam_id', $session->exam_id)
                ->whereIn('student_id', $studentIds)
                ->whereNotNull('finished_at')
                ->count();

            $session->pending_count = \App\Models\ExamResult::where('exam_id', $session->exam_id)
                ->whereIn('student_id', $studentIds)
                ->where('status', 'pending_grading')
                ->count();

            $session->student_total = $studentIds->count();
        }

        return $this->view($request, 'guru.results_class', [
            'classroom' => $classroom,
            'examSessions' => $examSessions,
        ]);
    }

    public function resultsByExam(Request $request, $classroomId, $examId)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];
        $classroom = \App\Models\Classroom::where('id', $classroomId)->where('teacher_id', $teacherId)->firstOrFail();
        $exam = \App\Models\Exam::findOrFail($examId);

        if ($exam->teacher_id !== $teacherId) {
            return redirect()->route('guru.results')->with('error', 'Akses ditolak.');
        }

        $studentIds = \App\Models\User::where('role', 'murid')
            ->where('kelas', $classroom->name)
            ->pluck('id');

        $results = \App\Models\ExamResult::with('student')
            ->where('exam_id', $examId)
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('finished_at')
            ->orderBy('score', 'desc')
            ->get();

        // Siswa yang belum mengerjakan
        $doneStudentIds = $results->pluck('student_id');
        $notDoneStudents = \App\Models\User::whereIn('id', $studentIds)
            ->whereNotIn('id', $doneStudentIds)
            ->orderBy('name')
            ->get();

        return $this->view($request, 'guru.results_exam', [
            'classroom' => $classroom,
            'exam' => $exam,
            'results' => $results,
            'notDoneStudents' => $notDoneStudents,
        ]);
    }

    public function createManual(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;
        
        $teacherId = session('user')['id'];
        $subjects = \App\Models\Subject::where('teacher_id', $teacherId)->orderBy('name', 'asc')->get();
        return $this->view($request, 'guru.create_manual', ['subjects' => $subjects]);
    }

    public function storeManual(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'deadline' => 'required|date',
        ]);

        $exam = \App\Models\Exam::create([
            'teacher_id' => session('user')['id'],
            'title' => $request->name,
            'subject' => $request->subject_id,
            'duration_minutes' => $request->duration,
            'require_seb' => $request->has('require_seb') ? true : false,
            'randomize_questions' => $request->has('randomize_questions') ? true : false,
            'deadline' => \Carbon\Carbon::parse($request->deadline),
            'status' => 'draft',
        ]);
        
        return redirect()->route('guru.exams.questions', $exam->id)->with('success', 'Detail ujian berhasil disimpan. Silakan tambahkan soal.');
    }

    public function manageQuestions(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = \App\Models\Exam::with('questions')->findOrFail($id);
        
        // Cek apakah ujian ini milik guru yang sedang login
        if ($exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }
        
        $teacherId = session('user')['id'];
        $classrooms = \App\Models\Classroom::where('teacher_id', $teacherId)->orderBy('name')->get();

        return $this->view($request, 'guru.manage_questions', ['exam' => $exam, 'classrooms' => $classrooms]);
    }

    public function storeQuestion(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = \App\Models\Exam::findOrFail($id);
        
        if ($exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        $type = $request->input('type');
        $order = $exam->questions()->count();

        if ($type === 'pg') {
            $request->validate([
                'text' => 'required|string',
                'options.A' => 'required|string',
                'options.B' => 'required|string',
                'options.C' => 'nullable|string',
                'options.D' => 'nullable|string',
                'options.E' => 'nullable|string',
                'correct_answer' => 'required|in:A,B,C,D,E',
            ]);

            $cleanOption = function($val) {
                if (!$val) return null;
                $stripped = trim(strip_tags($val));
                if (empty($stripped) && strpos($val, '<img') === false) return null;
                return $val;
            };

            \App\Models\Question::create([
                'exam_id' => $exam->id,
                'question_text' => $request->text,
                'type' => 'multiple_choice',
                'option_a' => $cleanOption($request->options['A']),
                'option_b' => $cleanOption($request->options['B']),
                'option_c' => $cleanOption($request->options['C'] ?? null),
                'option_d' => $cleanOption($request->options['D'] ?? null),
                'option_e' => $cleanOption($request->options['E'] ?? null),
                'correct_answer' => $request->correct_answer,
                'points' => 10,
                'order' => $order,
            ]);
        } else {
            $request->validate([
                'text' => 'required|string',
            ]);

            \App\Models\Question::create([
                'exam_id' => $exam->id,
                'question_text' => $request->text,
                'type' => 'essay',
                'points' => 10,
                'order' => $order,
            ]);
        }

        return redirect()->route('guru.exams.questions', $exam->id)->with('success', 'Soal berhasil ditambahkan.');
    }

    public function editQuestion(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $question = \App\Models\Question::with('exam')->findOrFail($id);
        
        if ($question->exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        return $this->view($request, 'guru.edit_question', ['question' => $question, 'exam' => $question->exam]);
    }

    public function updateQuestion(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $question = \App\Models\Question::with('exam')->findOrFail($id);
        
        if ($question->exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        if ($question->type === 'multiple_choice') {
            $request->validate([
                'text' => 'required|string',
                'options.A' => 'required|string',
                'options.B' => 'required|string',
                'options.C' => 'nullable|string',
                'options.D' => 'nullable|string',
                'options.E' => 'nullable|string',
                'correct_answer' => 'required|in:A,B,C,D,E',
            ]);

            $cleanOption = function($val) {
                if (!$val) return null;
                $stripped = trim(strip_tags($val));
                if (empty($stripped) && strpos($val, '<img') === false) return null;
                return $val;
            };

            $question->update([
                'question_text' => $request->text,
                'option_a' => $cleanOption($request->options['A']),
                'option_b' => $cleanOption($request->options['B']),
                'option_c' => $cleanOption($request->options['C'] ?? null),
                'option_d' => $cleanOption($request->options['D'] ?? null),
                'option_e' => $cleanOption($request->options['E'] ?? null),
                'correct_answer' => $request->correct_answer,
            ]);
        } else {
            $request->validate([
                'text' => 'required|string',
            ]);

            $question->update([
                'question_text' => $request->text,
            ]);
        }

        return redirect()->route('guru.exams.questions', $question->exam_id)->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroyQuestion(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $question = \App\Models\Question::with('exam')->findOrFail($id);
        
        if ($question->exam->teacher_id !== session('user')['id']) {
            return redirect()->back()->withErrors(['Akses ditolak.']);
        }

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }

    public function publishExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = \App\Models\Exam::findOrFail($id);
        
        if ($exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        if ($exam->questions()->count() == 0) {
            return redirect()->route('guru.exams.questions', $exam->id)->with('error', 'Tidak bisa mempublikasikan ujian tanpa soal.');
        }

        $request->validate([
            'classrooms' => 'required|array',
            'classrooms.*' => 'exists:classrooms,id',
            'deadline' => 'required|date'
        ]);

        $teacherId = session('user')['id'];
        $validClassrooms = \App\Models\Classroom::where('teacher_id', $teacherId)->whereIn('id', $request->classrooms)->pluck('id');
        foreach ($validClassrooms as $classroomId) {
            \App\Models\ExamSession::create([
                'exam_id' => $exam->id,
                'classroom_id' => $classroomId,
                'is_active' => true,
                'token' => strtoupper(\Illuminate\Support\Str::random(6))
            ]);
        }

        $exam->update([
            'status' => 'published',
            'deadline' => \Carbon\Carbon::parse($request->deadline)
        ]);

        return redirect()->route('guru.exams')->with('success', 'Ujian berhasil diterbitkan ke kelas yang dipilih.');
    }

    public function destroyExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = \App\Models\Exam::findOrFail($id);
        
        if ($exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        $exam->delete();

        return redirect()->route('guru.exams')->with('success', 'Ujian berhasil dihapus.');
    }

    public function duplicateExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = \App\Models\Exam::with('questions')->findOrFail($id);
        
        if ($exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        // Create new exam
        $newExam = $exam->replicate();
        
        // Hapus teks (Salinan) jika ada dari judul sebelumnya
        $cleanTitle = trim(str_replace(['(Salinan)', ' (Salinan) '], '', $exam->title));
        $newExam->title = $cleanTitle; 
        
        $newExam->status = 'draft';
        $newExam->deadline = null;
        $newExam->start_time = null;
        $newExam->end_time = null;
        $newExam->save();

        // Copy questions
        foreach ($exam->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->exam_id = $newExam->id;
            $newQuestion->save();
        }

        return redirect()->route('guru.exams.questions', $newExam->id)
            ->with('success', 'Ujian berhasil diterbitkan ulang. Silakan periksa kembali pengaturan durasi/soal sebelum disebar ke murid.');
    }

    public function updateDuration(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = \App\Models\Exam::findOrFail($id);
        
        if ($exam->teacher_id !== session('user')['id']) {
            return redirect()->route('guru.exams')->with('error', 'Akses ditolak.');
        }

        // Hanya boleh diubah jika belum deadline
        if ($exam->deadline && $exam->deadline < now()) {
            return back()->with('error', 'Tidak bisa mengubah durasi ujian yang sudah berakhir.');
        }

        $request->validate([
            'duration_minutes' => 'required|integer|min:1'
        ]);

        $exam->duration_minutes = $request->duration_minutes;
        $exam->save();

        return back()->with('success', 'Durasi pengerjaan berhasil diubah menjadi ' . $exam->duration_minutes . ' menit.');
    }

    public function bulkDestroyExam(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $request->validate([
            'exam_ids' => 'required|array',
            'exam_ids.*' => 'exists:exams,id'
        ]);

        $exams = \App\Models\Exam::whereIn('id', $request->exam_ids)->get();

        foreach ($exams as $exam) {
            if ($exam->teacher_id === session('user')['id']) {
                $exam->delete();
            }
        }

        return redirect()->route('guru.exams')->with('success', count($request->exam_ids) . ' Ujian berhasil dihapus.');
    }

    public function importFile(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;
        return $this->view($request, 'guru.import_file');
    }

    public function storeImport(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'deadline' => 'required|date',
            'file' => 'required|file|mimes:docx,pdf'
        ]);

        $exam = \App\Models\Exam::create([
            'teacher_id' => session('user')['id'],
            'title' => $request->name,
            'subject' => $request->subject_id,
            'duration_minutes' => $request->duration,
            'require_seb' => $request->has('require_seb') ? true : false,
            'deadline' => \Carbon\Carbon::parse($request->deadline),
            'status' => 'draft',
        ]);
        
        // TODO: Logic for parsing Word/PDF and storing questions
        
        return redirect()->route('guru.exams')->with('success', 'Soal berhasil diimport dari file.');
    }
    public function classes(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];
        $classrooms = \App\Models\Classroom::where('teacher_id', $teacherId)->orderBy('name', 'asc')->get();
        return $this->view($request, 'guru.classes', ['classrooms' => $classrooms]);
    }

    public function students(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];
        $query = \App\Models\User::where('role', 'murid')->where('teacher_id', $teacherId);
        
        $kelas = $request->query('kelas');
        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        $users = $query->orderBy('name', 'asc')->get();
        return $this->view($request, 'guru.students', ['users' => $users, 'selectedKelas' => $kelas]);
    }
}

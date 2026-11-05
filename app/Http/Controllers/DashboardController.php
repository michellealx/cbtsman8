<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        // Murid has no dashboard — redirect to exams
        $user = $this->currentUser($request);
        if ($user && $user['role'] === 'murid') {
            return redirect()->route('murid.exams');
        }

        $data = [];
        if (in_array($user['role'], ['admin', 'guru'])) {
            $teacherId = $user['id'];
            $data['total_siswa'] = \App\Models\User::where('role', 'murid')->where('teacher_id', $teacherId)->count();
            $data['total_guru'] = \App\Models\User::where('role', 'guru')->count();
            $data['total_kelas'] = \App\Models\Classroom::where('teacher_id', $teacherId)->count();
            $data['total_mapel'] = \App\Models\Subject::where('teacher_id', $teacherId)->count();
            $data['total_ujian'] = \App\Models\Exam::where('teacher_id', $teacherId)->count();
            $data['total_soal'] = \App\Models\Question::whereHas('exam', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->count();
            $data['total_peserta'] = \App\Models\ExamResult::whereHas('exam', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->count();
        }

        return $this->view($request, 'dashboard', $data);
    }
}

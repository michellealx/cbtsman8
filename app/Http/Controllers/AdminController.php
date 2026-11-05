<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function students(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        $query = User::where('role', 'murid');
        $classroomQuery = Classroom::query();

        if (!$isCoordinator) {
            $query->where('teacher_id', $teacherId);
            $classroomQuery->where('teacher_id', $teacherId);
        } else {
            $selectedTeacher = $request->query('teacher_id');
            if ($selectedTeacher) {
                $query->where('teacher_id', $selectedTeacher);
                $classroomQuery->where('teacher_id', $selectedTeacher);
            }
        }

        $users = $query->orderBy('name', 'asc')->get();
        $classrooms = $classroomQuery->orderBy('name', 'asc')->get();
        $teachers = $isCoordinator ? User::where('role', 'guru')->orderBy('name', 'asc')->get() : collect();

        return $this->view($request, 'admin.students', [
            'users' => $users,
            'classrooms' => $classrooms,
            'teachers' => $teachers,
            'selectedTeacher' => $request->query('teacher_id'),
        ]);
    }

    public function teachers(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireCoordinator($request)) return $redirect;

        $users = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        return $this->view($request, 'admin.teachers', ['users' => $users]);
    }

    public function resetTeacherPassword(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireCoordinator($request)) return $redirect;

        $teacher = User::where('role', 'guru')->findOrFail($id);
        $defaultPassword = 'password123';
        $teacher->update([
            'password' => Hash::make($defaultPassword),
            'session_token' => null,
        ]);

        return redirect()->route('admin.teachers')->with('success', "Password untuk guru {$teacher->name} berhasil di-reset menjadi: {$defaultPassword}");
    }

    public function toggleCoordinator(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireCoordinator($request)) return $redirect;

        $teacher = User::where('role', 'guru')->findOrFail($id);
        if ($teacher->id === session('user')['id']) {
            return redirect()->route('admin.teachers')->with('error', 'Anda tidak dapat mengubah status Koordinator akun sendiri.');
        }

        $teacher->update([
            'is_coordinator' => !$teacher->is_coordinator
        ]);

        $status = $teacher->is_coordinator ? 'diangkat menjadi Koordinator CBT' : 'dijadikan Guru Biasa';
        return redirect()->route('admin.teachers')->with('success', "Guru {$teacher->name} berhasil {$status}.");
    }

    public function storeBulkStudents(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];
        $selectedKelas = $request->input('kelas');
        $data = [];

        // 1. Ambil data dari JSON parsingan browser jika ada
        if ($request->filled('students_data')) {
            $decoded = json_decode($request->input('students_data'), true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        // 2. Fallback: Parse langsung file CSV / TXT di server jika students_data kosong
        if (empty($data) && $request->hasFile('excel_file')) {
            $file = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['csv', 'txt'])) {
                $rawContent = file_get_contents($file->getRealPath());
                // Hapus UTF-8 BOM jika ada
                $rawContent = preg_replace('/^\xEF\xBB\xBF/', '', $rawContent);
                $lines = preg_split('/\r\n|\n|\r/', trim($rawContent));

                if (count($lines) >= 2) {
                    $firstLine = $lines[0];
                    $delimiter = str_contains($firstLine, ';') ? ';' : (str_contains($firstLine, "\t") ? "\t" : ',');
                    $headers = array_map(fn($h) => strtolower(trim(trim($h, '"'))), str_getcsv($firstLine, $delimiter));

                    $idxNis = -1;
                    $idxNama = -1;
                    $idxJk = -1;
                    $idxPass = -1;
                    $idxKelas = -1;

                    foreach ($headers as $i => $h) {
                        if (str_contains($h, 'nis')) $idxNis = $i;
                        elseif (str_contains($h, 'nama')) $idxNama = $i;
                        elseif (str_contains($h, 'kelamin') || str_contains($h, 'jk') || $h === 'l/p' || $h === 'lp') $idxJk = $i;
                        elseif (str_contains($h, 'pass')) $idxPass = $i;
                        elseif (str_contains($h, 'kelas') || str_contains($h, 'rombel')) $idxKelas = $i;
                    }

                    if ($idxNis !== -1 && $idxNama !== -1) {
                        for ($i = 1; $i < count($lines); $i++) {
                            $line = trim($lines[$i]);
                            if (empty($line)) continue;
                            $cols = str_getcsv($line, $delimiter);
                            if (empty($cols[$idxNis]) && empty($cols[$idxNama])) continue;

                            $data[] = [
                                'nis' => trim($cols[$idxNis] ?? ''),
                                'nama' => trim($cols[$idxNama] ?? ''),
                                'jenis_kelamin' => $idxJk !== -1 ? trim($cols[$idxJk] ?? '') : null,
                                'password' => $idxPass !== -1 ? trim($cols[$idxPass] ?? '') : null,
                                'kelas' => $idxKelas !== -1 ? trim($cols[$idxKelas] ?? '') : null,
                            ];
                        }
                    }
                }
            }
        }

        if (!is_array($data) || count($data) === 0) {
            return redirect()->back()->withErrors(['Data siswa tidak valid atau kosong. Pastikan file memiliki baris NIS dan Nama.']);
        }

        $inserted = 0;
        $updated = 0;

        foreach ($data as $row) {
            if (empty($row['nis']) || empty($row['nama'])) continue;

            $nis = trim((string)$row['nis']);
            $nama = trim((string)$row['nama']);
            $kelas = $selectedKelas ?: (!empty($row['kelas']) ? trim((string)$row['kelas']) : null);
            $jk = !empty($row['jenis_kelamin']) ? strtoupper(substr(trim((string)$row['jenis_kelamin']), 0, 1)) : null;
            if ($jk !== 'L' && $jk !== 'P') $jk = null;
            $pass = !empty($row['password']) ? trim((string)$row['password']) : $nis;

            // Jika kelas belum pernah dibuat oleh guru ini, buat otomatis
            if ($kelas && !Classroom::where('name', $kelas)->where('teacher_id', $teacherId)->exists()) {
                Classroom::create([
                    'name' => $kelas,
                    'teacher_id' => $teacherId,
                ]);
            }

            $user = User::where('username', $nis)->first();
            if ($user) {
                // Update kepemilikan guru, kelas, nama, jk
                $user->teacher_id = $teacherId;
                $user->name = $nama;
                if ($kelas) $user->kelas = $kelas;
                if ($jk) $user->jenis_kelamin = $jk;
                if (!empty($row['password'])) {
                    $user->password = Hash::make($row['password']);
                }
                $user->save();
                $updated++;
                continue;
            }

            User::create([
                'name' => $nama,
                'username' => $nis,
                'password' => Hash::make($pass),
                'role' => 'murid',
                'teacher_id' => $teacherId,
                'jenis_kelamin' => $jk,
                'kelas' => $kelas,
            ]);
            $inserted++;
        }

        $total = $inserted + $updated;
        if ($total === 0) {
            return redirect()->back()->withErrors(['Tidak ada baris siswa yang valid untuk diproses.']);
        }

        $message = "Selesai memproses data ({$total} siswa). ";
        if ($inserted > 0) $message .= "{$inserted} siswa baru ditambahkan. ";
        if ($updated > 0) $message .= "{$updated} siswa berhasil diperbarui ke kelas Anda. ";

        return redirect()->back()->with('success', $message);
    }

    public function storeUser(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) {
            return $redirect;
        }

        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:murid,guru',
            'kelas' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        if ($request->role === 'guru' && !$isCoordinator) {
            return redirect()->back()->withErrors(['Hanya Koordinator CBT yang dapat menambahkan akun Guru.']);
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'teacher_id' => $request->role === 'murid' ? $teacherId : null,
            'kelas' => $request->kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $user = User::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $user->role === 'murid' && $user->teacher_id !== $teacherId) {
            return redirect()->back()->withErrors(['Akses ditolak. Anda tidak berhak mengubah data siswa ini.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'kelas' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->kelas = $request->kelas;
        $user->jenis_kelamin = $request->jenis_kelamin;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function deleteUser(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $user = User::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $user->role === 'murid' && $user->teacher_id !== $teacherId) {
            return redirect()->back()->withErrors(['Akses ditolak. Anda tidak berhak menghapus siswa ini.']);
        }
        
        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
    }

    public function exams(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        $examQuery = Exam::with('teacher')->orderBy('created_at', 'desc');
        $subjectQuery = Subject::orderBy('name', 'asc');

        if (!$isCoordinator) {
            $examQuery->where('teacher_id', $teacherId);
            $subjectQuery->where('teacher_id', $teacherId);
        }

        $exams = $examQuery->get();
        $teachers = User::where('role', 'guru')->get();
        $subjects = $subjectQuery->get();

        return $this->view($request, 'admin.exams', [
            'exams' => $exams, 
            'teachers' => $teachers,
            'subjects' => $subjects
        ]);
    }

    public function storeExam(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'teacher_id' => 'required|exists:users,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        Exam::create([
            'title' => $request->title,
            'subject' => $request->subject,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'teacher_id' => $request->teacher_id,
            'status' => 'draft',
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.exams')->with('success', 'Ujian berhasil dibuat!');
    }

    public function deleteExam(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $exam = Exam::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $exam->teacher_id !== $teacherId) {
            return redirect()->route('admin.exams')->with('error', 'Akses ditolak.');
        }

        $exam->delete();

        return redirect()->route('admin.exams')->with('success', 'Ujian berhasil dihapus!');
    }

    public function subjects(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;
        
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        $query = Subject::query();
        if (!$isCoordinator) {
            $query->where('teacher_id', $teacherId);
        } else {
            $selectedTeacher = $request->query('teacher_id');
            if ($selectedTeacher) {
                $query->where('teacher_id', $selectedTeacher);
            }
        }

        $subjects = $query->orderBy('name', 'asc')->get();
        $teachers = $isCoordinator ? User::where('role', 'guru')->orderBy('name', 'asc')->get() : collect();

        return $this->view($request, 'admin.subjects', [
            'subjects' => $subjects,
            'teachers' => $teachers,
            'selectedTeacher' => $request->query('teacher_id'),
        ]);
    }

    public function storeSubject(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        Subject::create([
            'teacher_id' => session('user')['id'],
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('admin.subjects')->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    public function updateSubject(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $subject = Subject::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $subject->teacher_id !== $teacherId) {
            return redirect()->route('admin.subjects')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        $subject->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return redirect()->route('admin.subjects')->with('success', 'Mata Pelajaran berhasil diperbarui!');
    }

    public function deleteSubject(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $subject = Subject::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $subject->teacher_id !== $teacherId) {
            return redirect()->route('admin.subjects')->with('error', 'Akses ditolak.');
        }

        $subject->delete();

        return redirect()->route('admin.subjects')->with('success', 'Mata Pelajaran berhasil dihapus!');
    }

    public function classes(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;
        
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        $query = Classroom::query();
        if (!$isCoordinator) {
            $query->where('teacher_id', $teacherId);
        } else {
            $selectedTeacher = $request->query('teacher_id');
            if ($selectedTeacher) {
                $query->where('teacher_id', $selectedTeacher);
            }
        }

        $classes = $query->orderBy('name', 'asc')->get();
        $teachers = $isCoordinator ? User::where('role', 'guru')->orderBy('name', 'asc')->get() : collect();

        return $this->view($request, 'admin.classes', [
            'classes' => $classes,
            'teachers' => $teachers,
            'selectedTeacher' => $request->query('teacher_id'),
        ]);
    }

    public function storeClass(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Classroom::create([
            'teacher_id' => session('user')['id'],
            'name' => $request->name,
        ]);

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function updateClass(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $classroom = Classroom::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $classroom->teacher_id !== $teacherId) {
            return redirect()->route('admin.classes')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $classroom->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil diperbarui!');
    }

    public function deleteClass(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $classroom = Classroom::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $classroom->teacher_id !== $teacherId) {
            return redirect()->route('admin.classes')->with('error', 'Akses ditolak.');
        }

        $classroom->delete();

        return redirect()->route('admin.classes')->with('success', 'Kelas berhasil dihapus!');
    }

    public function showClass(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $classroom = Classroom::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $classroom->teacher_id !== $teacherId) {
            return redirect()->route('admin.classes')->with('error', 'Akses ditolak.');
        }

        $students = User::where('role', 'murid')
            ->where('kelas', $classroom->name)
            ->where('teacher_id', $classroom->teacher_id ?? $teacherId)
            ->orderBy('name', 'asc')
            ->get();

        return $this->view($request, 'admin.class_show', [
            'classroom' => $classroom,
            'students' => $students
        ]);
    }

    public function removeStudentFromClass(Request $request, $id, $student_id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $classroom = Classroom::findOrFail($id);
        $student = User::findOrFail($student_id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && ($classroom->teacher_id !== $teacherId || $student->teacher_id !== $teacherId)) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $student->kelas = null;
        $student->save();

        return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    public function manageStudents(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $classroom = Classroom::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $classroom->teacher_id !== $teacherId) {
            return redirect()->route('admin.classes')->with('error', 'Akses ditolak.');
        }
        
        $students = User::where('role', 'murid')
            ->where('teacher_id', $classroom->teacher_id ?? $teacherId)
            ->orderBy('name', 'asc')
            ->get();

        return $this->view($request, 'admin.manage_class_students', [
            'classroom' => $classroom,
            'students' => $students
        ]);
    }

    public function updateStudents(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $classroom = Classroom::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $classroom->teacher_id !== $teacherId) {
            return redirect()->route('admin.classes')->with('error', 'Akses ditolak.');
        }

        $classTeacherId = $classroom->teacher_id ?? $teacherId;
        $studentIds = $request->input('student_ids', []);

        User::where('role', 'murid')
            ->where('teacher_id', $classTeacherId)
            ->where('kelas', $classroom->name)
            ->whereNotIn('id', $studentIds)
            ->update(['kelas' => null]);

        if (!empty($studentIds)) {
            User::whereIn('id', $studentIds)
                ->where('role', 'murid')
                ->where('teacher_id', $classTeacherId)
                ->update(['kelas' => $classroom->name]);
        }

        return redirect()->route('admin.classes.show', $classroom->id)->with('success', 'Daftar siswa di kelas berhasil diperbarui!');
    }

    public function sessions(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;
        
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        $sessionQuery = ExamSession::with(['exam', 'classroom'])->orderBy('created_at', 'desc');
        $examQuery = Exam::orderBy('title', 'asc');
        $classroomQuery = Classroom::orderBy('name', 'asc');

        if (!$isCoordinator) {
            $sessionQuery->whereHas('exam', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
            $examQuery->where('teacher_id', $teacherId);
            $classroomQuery->where('teacher_id', $teacherId);
        }

        $sessions = $sessionQuery->get();
        $exams = $examQuery->get();
        $classrooms = $classroomQuery->get();

        return $this->view($request, 'admin.sessions', [
            'sessions' => $sessions,
            'exams' => $exams,
            'classrooms' => $classrooms
        ]);
    }

    public function storeSession(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        ExamSession::create([
            'exam_id' => $request->exam_id,
            'classroom_id' => $request->classroom_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'token' => strtoupper(Str::random(5)),
            'is_active' => false,
        ]);

        return redirect()->route('admin.sessions')->with('success', 'Sesi Ujian berhasil dibuat dengan Token Baru!');
    }

    public function toggleSession(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $session = ExamSession::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $session->exam && $session->exam->teacher_id !== $teacherId) {
            return redirect()->route('admin.sessions')->with('error', 'Akses ditolak.');
        }

        $session->update([
            'is_active' => !$session->is_active,
        ]);

        $statusMsg = $session->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.sessions')->with('success', "Sesi Ujian berhasil {$statusMsg}!");
    }

    public function deleteSession(Request $request, $id)
    {
        if ($redirect = $this->requireAuth($request)) return $redirect;
        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) return $redirect;

        $session = ExamSession::findOrFail($id);
        $teacherId = session('user')['id'];
        $isCoordinator = !empty(session('user')['is_coordinator']);

        if (!$isCoordinator && $session->exam && $session->exam->teacher_id !== $teacherId) {
            return redirect()->route('admin.sessions')->with('error', 'Akses ditolak.');
        }

        $session->delete();

        return redirect()->route('admin.sessions')->with('success', 'Sesi Ujian berhasil dihapus!');
    }

    public function reports(Request $request)
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        if ($redirect = $this->requireRole($request, ['admin', 'guru'])) {
            return $redirect;
        }

        return $this->view($request, 'admin.reports');
    }
}

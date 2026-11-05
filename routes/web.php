<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\MuridController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $currentUser = session('user');
    return view('home', ['currentUser' => $currentUser]);
});

Route::get('/login', function () {
    return redirect('/');
})->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('admin')->group(function () {
    Route::get('/users', function() { return redirect()->route('admin.students'); });
    Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
    Route::post('/students/bulk', [AdminController::class, 'storeBulkStudents'])->name('admin.students.bulk');
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
    Route::post('/teachers/{id}/reset-password', [AdminController::class, 'resetTeacherPassword'])->name('admin.teachers.reset_password');
    Route::post('/teachers/{id}/toggle-coordinator', [AdminController::class, 'toggleCoordinator'])->name('admin.teachers.toggle_coordinator');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Master Data
    Route::get('/subjects', [AdminController::class, 'subjects'])->name('admin.subjects');
    Route::post('/subjects', [AdminController::class, 'storeSubject'])->name('admin.subjects.store');
    Route::put('/subjects/{id}', [AdminController::class, 'updateSubject'])->name('admin.subjects.update');
    Route::delete('/subjects/{id}', [AdminController::class, 'deleteSubject'])->name('admin.subjects.delete');
    
    Route::get('/classes', [AdminController::class, 'classes'])->name('admin.classes');
    Route::post('/classes', [AdminController::class, 'storeClass'])->name('admin.classes.store');
    Route::put('/classes/{id}', [AdminController::class, 'updateClass'])->name('admin.classes.update');
    Route::delete('/classes/{id}', [AdminController::class, 'deleteClass'])->name('admin.classes.delete');
    Route::get('/classes/{id}', [AdminController::class, 'showClass'])->name('admin.classes.show');
    Route::post('/classes/{id}/students/remove/{student_id}', [AdminController::class, 'removeStudentFromClass'])->name('admin.classes.students.remove');
    Route::get('/classes/{id}/students/add', [AdminController::class, 'manageStudents'])->name('admin.classes.students.add');
    Route::post('/classes/{id}/students/add', [AdminController::class, 'updateStudents'])->name('admin.classes.students.update');
    
    // Exams
    Route::get('/exams', [AdminController::class, 'exams'])->name('admin.exams');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('admin.exams.store');
    Route::delete('/exams/{id}', [AdminController::class, 'deleteExam'])->name('admin.exams.delete');
    // Sessions
    Route::get('/sessions', [AdminController::class, 'sessions'])->name('admin.sessions');
    Route::post('/sessions', [AdminController::class, 'storeSession'])->name('admin.sessions.store');
    Route::put('/sessions/{id}/toggle', [AdminController::class, 'toggleSession'])->name('admin.sessions.toggle');
    Route::delete('/sessions/{id}', [AdminController::class, 'deleteSession'])->name('admin.sessions.delete');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
});

Route::prefix('guru')->group(function () {
    Route::get('/exams', [GuruController::class, 'exams'])->name('guru.exams');
    Route::get('/exams/create-manual', [GuruController::class, 'createManual'])->name('guru.exams.create_manual');
    Route::post('/exams/store-manual', [GuruController::class, 'storeManual'])->name('guru.exams.store_manual');
    Route::get('/exams/import', [GuruController::class, 'importFile'])->name('guru.exams.import');
    Route::post('/exams/import', [GuruController::class, 'storeImport'])->name('guru.exams.store_import');
    
    // Akademik
    Route::get('/classes', [GuruController::class, 'classes'])->name('guru.classes');
    Route::get('/students', [GuruController::class, 'students'])->name('guru.students');
    
    Route::get('/exams/{id}/questions', [GuruController::class, 'manageQuestions'])->name('guru.exams.questions');
    Route::post('/exams/{id}/questions', [GuruController::class, 'storeQuestion'])->name('guru.exams.questions.store');
    Route::post('/exams/{id}/publish', [GuruController::class, 'publishExam'])->name('guru.exams.publish');
    Route::post('/exams/{id}/duplicate', [GuruController::class, 'duplicateExam'])->name('guru.exams.duplicate');
    Route::get('/exams/questions/{id}/edit', [GuruController::class, 'editQuestion'])->name('guru.exams.questions.edit');
    Route::put('/exams/questions/{id}', [GuruController::class, 'updateQuestion'])->name('guru.exams.questions.update');
    Route::delete('/exams/questions/{id}', [GuruController::class, 'destroyQuestion'])->name('guru.exams.questions.destroy');
    Route::post('/exams/{id}/duration', [GuruController::class, 'updateDuration'])->name('guru.exams.update_duration');
    Route::post('/exams/bulk-delete', [GuruController::class, 'bulkDestroyExam'])->name('guru.exams.bulk_destroy');
    Route::delete('/exams/{id}', [GuruController::class, 'destroyExam'])->name('guru.exams.destroy');
    
    // Active Sessions & Extra Time
    Route::get('/active-sessions', [GuruController::class, 'activeSessions'])->name('guru.active_sessions');
    Route::get('/active-sessions/{id}', [GuruController::class, 'showActiveSession'])->name('guru.active_sessions.show');
    Route::post('/active-sessions/{id}/end', [GuruController::class, 'endSession'])->name('guru.active_sessions.end');
    Route::post('/active-sessions/result/{resultId}/add-time', [GuruController::class, 'addExtraTime'])->name('guru.active_sessions.add_time');

    Route::get('/exams/{id}/grade', [GuruController::class, 'gradeExam'])->name('guru.exams.grade');
    Route::post('/grade/{resultId}', [GuruController::class, 'submitGrade'])->name('guru.exams.grade.submit');
    
    Route::get('/questions', [GuruController::class, 'questions'])->name('guru.questions');
    Route::get('/results', [GuruController::class, 'results'])->name('guru.results');
    Route::get('/results/class/{classroomId}', [GuruController::class, 'resultsByClass'])->name('guru.results.class');
    Route::get('/results/class/{classroomId}/exam/{examId}', [GuruController::class, 'resultsByExam'])->name('guru.results.exam');
});

Route::prefix('murid')->group(function () {
    Route::get('/exams', [MuridController::class, 'exams'])->name('murid.exams');
    Route::get('/exams/{id}', [MuridController::class, 'examDetail'])->name('murid.exam');
    Route::post('/exams/{id}/start', [MuridController::class, 'startExam'])->name('murid.exams.start');
    Route::get('/exams/{id}/take', [MuridController::class, 'takeExam'])->name('murid.exams.take');
    Route::get('/exams/{id}/check-time', [MuridController::class, 'checkTime'])->name('murid.exams.check_time');
    Route::post('/exams/{id}/autosave', [MuridController::class, 'autoSave'])->name('murid.exams.autosave');
    Route::post('/exams/{id}/submit', [MuridController::class, 'submitExam'])->name('murid.exams.submit');
    Route::get('/scores', [MuridController::class, 'scores'])->name('murid.scores');
});

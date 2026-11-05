<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$students = App\Models\User::where('role', 'murid')->get(['id', 'username', 'name', 'teacher_id', 'kelas']);
echo json_encode($students, JSON_PRETTY_PRINT);


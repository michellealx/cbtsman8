<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add teacher_id to subjects table
        if (!Schema::hasColumn('subjects', 'teacher_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->foreignId('teacher_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            });
        }

        // 2. Add teacher_id to classrooms table
        if (!Schema::hasColumn('classrooms', 'teacher_id')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->foreignId('teacher_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            });
        }

        // 3. Add teacher_id to users table (for murid)
        if (!Schema::hasColumn('users', 'teacher_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('teacher_id')->nullable()->after('role')->constrained('users')->onDelete('cascade');
            });
        }

        // 4. Assign existing data to teacher ID 2 (Budi Guru) who created the initial test data
        DB::table('subjects')->whereNull('teacher_id')->update(['teacher_id' => 2]);
        DB::table('classrooms')->whereNull('teacher_id')->update(['teacher_id' => 2]);
        DB::table('users')->where('role', 'murid')->whereNull('teacher_id')->update(['teacher_id' => 2]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'teacher_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
                $table->dropColumn('teacher_id');
            });
        }

        if (Schema::hasColumn('classrooms', 'teacher_id')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
                $table->dropColumn('teacher_id');
            });
        }

        if (Schema::hasColumn('subjects', 'teacher_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
                $table->dropColumn('teacher_id');
            });
        }
    }
};


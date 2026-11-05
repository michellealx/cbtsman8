<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->enum('status', ['in_progress', 'completed', 'pending_grading'])->default('in_progress')->after('score');
        });
        
        Schema::table('student_answers', function (Blueprint $table) {
            $table->integer('score')->nullable()->after('is_correct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropColumn('score');
        });
    }
};

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
        Schema::table('questions', function (Blueprint $table) {
            $table->longText('question_text')->change();
            $table->longText('option_a')->nullable()->change();
            $table->longText('option_b')->nullable()->change();
            $table->longText('option_c')->nullable()->change();
            $table->longText('option_d')->nullable()->change();
            $table->longText('option_e')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->text('question_text')->change();
            $table->text('option_a')->nullable()->change();
            $table->text('option_b')->nullable()->change();
            $table->text('option_c')->nullable()->change();
            $table->text('option_d')->nullable()->change();
            $table->text('option_e')->nullable()->change();
        });
    }
};

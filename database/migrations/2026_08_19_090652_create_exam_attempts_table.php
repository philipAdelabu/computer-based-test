// database/migrations/2024_01_01_000008_create_exam_attempts_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->json('answers')->nullable();
            $table->integer('score')->nullable();
            $table->integer('total_questions_answered')->default(0);
            $table->enum('status', ['in_progress', 'submitted', 'timed_out'])->default('in_progress');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_attempts');
    }
};
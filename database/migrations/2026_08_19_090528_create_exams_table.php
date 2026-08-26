<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('duration_minutes');
            $table->integer('total_questions');
            $table->integer('total_score');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('schedule_type', ['no_date', 'single_date', 'date_range'])
                  ->default('single_date');

            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->enum('created_by_role', ['admin', 'teacher'])->default('admin');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('instructions')->nullable();
            $table->boolean('is_published')->default(false);

           // Add new columns
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_to')->nullable();
            $table->integer('max_attempts')->default(1);
            $table->integer('passing_score')->nullable();
            $table->boolean('show_answers_after_completion')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exams');
    }
};
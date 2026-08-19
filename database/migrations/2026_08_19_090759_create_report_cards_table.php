// database/migrations/2024_01_01_000010_create_report_cards_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('term');
            $table->integer('academic_year');
            $table->json('subject_scores');
            $table->integer('total_score');
            $table->integer('average_score');
            $table->string('grade');
            $table->text('remarks');
            $table->integer('position')->nullable();
            $table->integer('total_students')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_cards');
    }
};
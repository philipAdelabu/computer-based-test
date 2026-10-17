<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('report_cards', function (Blueprint $table) {
            // Student info
            $table->string('gender')->nullable()->after('student_id');
            $table->integer('age')->nullable()->after('gender');
            $table->string('house')->nullable()->after('age');
            $table->integer('no_in_class')->nullable()->after('house');
            $table->string('session')->nullable()->after('academic_year'); // e.g., "2025/2026"

            // Result analysis
            $table->decimal('max_class_score', 8, 2)->nullable()->after('grand_max');
            $table->decimal('min_class_score', 8, 2)->nullable()->after('max_class_score');
            $table->integer('marks_obtainable')->nullable()->after('min_class_score');

            // Attendance
            $table->integer('times_school_opened')->nullable()->after('total_students');
            $table->integer('times_present')->nullable()->after('times_school_opened');

            // Affective traits (JSON: trait_name => rating 1-5)
            $table->json('affective_traits')->nullable()->after('times_present');

            // Psychomotor skills (JSON: skill_name => rating 1-5)
            $table->json('psychomotor_skills')->nullable()->after('affective_traits');

            // Comments
            $table->text('class_teacher_comment')->nullable()->after('psychomotor_skills');
            $table->string('class_teacher_name')->nullable()->after('class_teacher_comment');
            $table->text('principal_comment')->nullable()->after('class_teacher_name');
            $table->string('principal_name')->nullable()->after('principal_comment');

            // Vacation & resumption
            $table->date('vacation_date')->nullable()->after('principal_name');
            $table->date('resumption_date')->nullable()->after('vacation_date');
        });
    }

    public function down()
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'age', 'house', 'no_in_class', 'session',
                'max_class_score', 'min_class_score', 'marks_obtainable',
                'times_school_opened', 'times_present',
                'affective_traits', 'psychomotor_skills',
                'class_teacher_comment', 'class_teacher_name',
                'principal_comment', 'principal_name',
                'vacation_date', 'resumption_date',
            ]);
        });
    }
};

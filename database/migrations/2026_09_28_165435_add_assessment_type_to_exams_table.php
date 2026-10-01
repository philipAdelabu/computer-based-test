// database/migrations/YYYY_MM_DD_HHMMSS_add_assessment_type_to_exams_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            // 'test' or 'exam' - defaults to 'exam' for backward compatibility
            $table->enum('assessment_type', ['test', 'exam'])
                  ->default('exam')
                  ->after('title');
            
            // Maximum marks allowed for this assessment (e.g., 30 for test, 70 for exam)
            $table->integer('max_marks')
                  ->default(100)
                  ->after('assessment_type');
            
            // Benchmark (passing threshold in percentage)
            $table->integer('benchmark')
                  ->default(50)
                  ->after('max_marks');
            
            // Term and academic year for grouping into report cards
            $table->string('term')->nullable()->after('benchmark');
            $table->integer('academic_year')->nullable()->after('term');
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_type',
                'max_marks',
                'benchmark',
                'term',
                'academic_year',
            ]);
        });
    }
};
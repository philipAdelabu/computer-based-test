// database/migrations/YYYY_MM_DD_HHMMSS_update_report_cards_for_test_exam.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('report_cards', function (Blueprint $table) {
            // Detailed breakdown per subject
            $table->json('subject_breakdown')->nullable()->after('subject_scores');
            
            // Total test score (out of 30 * subjects)
            $table->integer('total_test_score')->default(0)->after('average_score');
            $table->integer('total_test_max')->default(0)->after('total_test_score');
            
            // Total exam score (out of 70 * subjects)
            $table->integer('total_exam_score')->default(0)->after('total_test_max');
            $table->integer('total_exam_max')->default(0)->after('total_exam_score');
            
            // Grand total
            $table->integer('grand_total')->default(0)->after('total_exam_max');
            $table->integer('grand_max')->default(0)->after('grand_total');
            
            // Metadata
            $table->date('generated_date')->nullable()->after('remarks');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null')->after('generated_date');
        });
    }

    public function down()
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->dropColumn([
                'subject_breakdown',
                'total_test_score',
                'total_test_max',
                'total_exam_score',
                'total_exam_max',
                'grand_total',
                'grand_max',
                'generated_date',
                'generated_by',
            ]);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            // 'objective' or 'theory'
            $table->enum('exam_mode', ['objective', 'theory'])->default('objective')->after('assessment_type');
            $table->boolean('shuffle_questions')->default(false)->after('exam_mode');
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('exam_mode');
            $table->dropColumn('shuffle_questions');
        });
    }
};

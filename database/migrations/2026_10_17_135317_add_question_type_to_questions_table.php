<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // 'objective' (default) or 'theory'
            $table->enum('question_type', ['objective', 'theory'])->default('objective')->after('question_text');
            
            // Theory questions don't need options, so make it nullable
            $table->json('options')->nullable()->change();
            $table->string('correct_answer')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('question_type');
            $table->json('options')->nullable(false)->change();
            $table->string('correct_answer')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('class_teacher_id')
                  ->nullable()
                  ->after('description')
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['class_teacher_id']);
            $table->dropColumn('class_teacher_id');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            // Stores a JSON array mapping option index to image path (e.g., {"0": "uploads/options/img1.jpg", "2": "..."})
            $table->json('option_images')->nullable()->after('options');
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('option_images');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // e.g., "Standard WAEC", "University 5-point"
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('grading_scale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scale_id')->constrained()->onDelete('cascade');
            $table->string('grade');             // e.g., "A1", "B2", "F9"
            $table->decimal('min_score', 5, 2);  // e.g., 90.00
            $table->decimal('max_score', 5, 2);  // e.g., 100.00
            $table->string('remark')->nullable();// e.g., "Excellent", "Fail"
            $table->string('color', 20)->default('secondary'); // badge color
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['grading_scale_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grading_scale_items');
        Schema::dropIfExists('grading_scales');
    }
};
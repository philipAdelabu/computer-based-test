<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Whether the student can take assessments at all
            $table->boolean('can_take_assessments')->default(true)->after('status');

            // Reason for deactivation (shown to admin/teacher)
            $table->text('deactivation_reason')->nullable()->after('can_take_assessments');

            // Who deactivated the student
            $table->foreignId('deactivated_by')->nullable()->constrained('users')->onDelete('set null')->after('deactivation_reason');

            // When they were deactivated
            $table->timestamp('deactivated_at')->nullable()->after('deactivated_by');

            // Optional: auto-reactivate at a specific date
            $table->timestamp('reactivate_at')->nullable()->after('deactivated_at');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['deactivated_by']);
            $table->dropColumn([
                'can_take_assessments',
                'deactivation_reason',
                'deactivated_by',
                'deactivated_at',
                'reactivate_at',
            ]);
        });
    }
};
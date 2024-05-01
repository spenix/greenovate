<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('employee_leaves')) {
            Schema::table('employee_leaves', function (Blueprint $table) {
                if (!Schema::hasColumns('employee_leaves', ['running_balance'])) {
                    $table->decimal('running_balance', 8, 2)->default(0)->after('leave_days');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employee_leaves')) {
            Schema::table('employee_leaves', function (Blueprint $table) {
                if (Schema::hasColumns('employee_leaves', ['running_balance'])) {
                    $table->dropColumn('running_balance');
                }
            });
        }
    }
};

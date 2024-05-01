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
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumns('employees', ['reason', 'termination_date'])) {
                    $table->text('reason')->nullable()->after('terminate');
                    $table->date('termination_date')->nullable()->after('reason');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumns('employees', ['reason', 'termination_date'])) {
                    $table->dropColumn('reason');
                    $table->dropColumn('termination_date');
                }
            });
        }
    }
};

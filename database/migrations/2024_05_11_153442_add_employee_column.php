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
                if (!Schema::hasColumns('employees', ['civil_status'])) {
                    $table->enum('civil_status', ['S', 'M', 'W', 'D'])->default('S')->after('birthdate')->comment("S-Single, M-Married, W-Widowed, D-Divorced");
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
                if (Schema::hasColumns('employees', ['civil_status'])) {
                    $table->dropColumn('civil_status');
                }
            });
        }
    }
};

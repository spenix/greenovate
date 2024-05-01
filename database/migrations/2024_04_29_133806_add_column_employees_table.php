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
                if (!Schema::hasColumns('employees', ['terminate'])) {
                    $table->enum('terminate', ['Y', 'N'])->default('N')->after('rate');
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
                if (Schema::hasColumns('employees', ['terminate'])) {
                    $table->dropColumn('terminate');
                }
            });
        }
    }
};

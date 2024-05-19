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
        if (Schema::hasTable('shift_codes')) {
            Schema::table('shift_codes', function (Blueprint $table) {
                if (!Schema::hasColumns('shift_codes', ['days'])) {
                    $table->string('days')->after('clock_out');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('shift_codes')) {
            Schema::table('shift_codes', function (Blueprint $table) {
                if (Schema::hasColumns('shift_codes', ['days'])) {
                    $table->dropColumn('days');
                }
            });
        }
    }
};

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
        if (Schema::hasTable('compensation')) {
            Schema::table('compensation', function (Blueprint $table) {
                if (!Schema::hasColumns('compensation', ['start_date', 'end_date'])) {
                    $table->timestamp('start_date')->useCurrent()->after('status');
                    $table->timestamp('end_date')->nullable()->after('start_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('compensation')) {
            Schema::table('compensation', function (Blueprint $table) {
                if (Schema::hasColumns('compensation', ['start_date', 'end_date'])) {
                    $table->dropColumn('start_date');
                    $table->dropColumn('end_date');
                }
            });
        }
    }
};

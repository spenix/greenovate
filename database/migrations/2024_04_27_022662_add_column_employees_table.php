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
                if (!Schema::hasColumns('employees', ['contact_no', 'email'])) {
                    $table->string('contact_no', 20)->nullable()->after('employee_no');
                    $table->string('email')->unique()->nullable()->after('contact_no');
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
                if (Schema::hasColumns('employees', ['contact_no', 'email'])) {
                    $table->dropColumn('contact_no');
                    $table->dropColumn('email');
                }
            });
        }
    }
};

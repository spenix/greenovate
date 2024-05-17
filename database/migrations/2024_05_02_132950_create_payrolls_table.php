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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 8, 2)->default(0);
            $table->decimal('working_hours', 8, 2)->default(0);
            $table->decimal('working_days', 8, 2)->default(0);
            $table->decimal('ot_hours', 8, 2)->default(0);
            $table->decimal('ot_compensation', 8, 2)->default(0);
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};

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
            $table->decimal('payment_rate', 8, 2);
            $table->date('payment_start');
            $table->date('payment_period');
            $table->decimal('reg_hours', 8, 2);
            $table->decimal('reg_hour_rate', 8, 2);
            $table->decimal('ot_hours', 8, 2);
            $table->decimal('ot_hour_rate', 8, 2);
            $table->decimal('philhealth', 8, 2);
            $table->decimal('tin', 8, 2);
            $table->decimal('sss', 8, 2);
            $table->decimal('pag_ibig', 8, 2);
            $table->decimal('quarterly', 8, 2);
            $table->decimal('year_end', 8, 2);
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

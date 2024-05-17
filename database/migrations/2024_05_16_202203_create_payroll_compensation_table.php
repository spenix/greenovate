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
        Schema::create('payroll_compensation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_id');
            $table->unsignedBigInteger('compensation_id');
            $table->decimal('amount', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('payroll_id')->references('id')->on('payrolls');
            $table->foreign('compensation_id')->references('id')->on('param_benefits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_compensation');
    }
};

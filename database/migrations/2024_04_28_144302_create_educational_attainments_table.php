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
        Schema::create('educational_attainments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('school_name');
            $table->string('address');
            $table->unsignedBigInteger('educational_level_id');
            $table->year('year_start');
            $table->year('year_end');
            $table->timestamps();
            $table->foreign('educational_level_id')->references('id')->on('educational_levels');
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_attainments');
    }
};

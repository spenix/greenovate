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
        Schema::create('attendance_record_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_record_id');
            $table->unsignedBigInteger('shift_id');
            $table->time('clock_in');
            $table->time('break_out');
            $table->time('break_in');
            $table->time('clock_out');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('attendance_record_id')->references('id')->on('attendance_records');
            $table->foreign('shift_id')->references('id')->on('shifts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_record_details');
    }
};

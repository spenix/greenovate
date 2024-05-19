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
        Schema::create('emp_basic_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designation_id');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->enum('status', ['Y', 'N'])->default('Y');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('designation_id')->references('id')->on('designations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emp_basic_salaries');
    }
};

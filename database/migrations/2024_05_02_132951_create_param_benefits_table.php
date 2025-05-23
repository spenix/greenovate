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
        Schema::create('param_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_code', 100);
            $table->decimal('amount', 8, 2)->nullable();
            $table->enum('classification', ['Allowance', 'Benefit', 'Basic'])->default('Basic');
            $table->enum('status', ['Y', 'N'])->default('Y');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('param_benefits');
    }
};

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
        Schema::create('family_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('name');
            $table->unsignedBigInteger('relationship_type_id');
            $table->string('address');
            $table->string('contact_no', 20);
            $table->date('birthdate');
            $table->string('occupation');
            $table->timestamps();
            $table->foreign('relationship_type_id')->references('id')->on('relationship_types');
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_backgrounds');
    }
};

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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 100);
            $table->string('middlename', 100);
            $table->string('lastname', 100);
            $table->string('suffix', 100)->nullable();
            $table->enum('gender', ['M', 'F'])->default('M');
            $table->unsignedBigInteger('blood_type_id')->unsigned()->nullable();
            $table->date('birthdate')->nullable();
            $table->string('employee_no', 100)->nullable();
            $table->unsignedBigInteger('employee_type_id')->unsigned()->nullable();
            $table->unsignedBigInteger('department_id')->unsigned()->nullable();
            $table->unsignedBigInteger('designation_id')->unsigned()->nullable();
            $table->date('date_hired')->nullable();
            $table->string('sss', 100)->nullable();
            $table->string('tin', 100)->nullable();
            $table->string('pag_ibig', 100)->nullable();
            $table->string('philhealth', 100)->nullable();
            $table->decimal('rate', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('blood_type_id')->references('id')->on('blood_types');
            $table->foreign('employee_type_id')->references('id')->on('employee_types');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('designation_id')->references('id')->on('designations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

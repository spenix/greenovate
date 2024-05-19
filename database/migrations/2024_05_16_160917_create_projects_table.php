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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('client_name');
            $table->text('description')->nullable();
            $table->decimal('project_cost', 12, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Pending', 'Ongoing', 'Done', 'Cancelled']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

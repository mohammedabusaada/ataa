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
        Schema::create('distribution_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aid_program_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('round_number');
            $table->date('scheduled_on');
            $table->string('location');
            $table->unsignedInteger('quantity_per_household');
            $table->string('status', 30)->default('planned')->index();
            $table->timestamps();

            $table->unique(['aid_program_id', 'round_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_rounds');
    }
};

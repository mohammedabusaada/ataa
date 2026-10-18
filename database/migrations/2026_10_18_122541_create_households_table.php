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
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->char('national_id', 9)->unique();
            $table->string('head_name', 255);
            $table->string('head_gender', 30);
            $table->date('head_birth_date');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->foreignId('area_id')->constrained()->restrictOnDelete();
            $table->string('address', 255);
            $table->unsignedTinyInteger('family_size');
            $table->string('status', 30)->default('registered')->index();
            $table->foreignId('registered_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};

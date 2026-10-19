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
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_round_id')->constrained()->restrictOnDelete();
            $table->foreignId('household_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('status', 30)->default('scheduled')->index();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('collected_at')->nullable();
            $table->string('received_by_name')->nullable();
            $table->char('received_by_national_id', 9)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            // A household can be allocated at most once per distribution round.
            $table->unique(['distribution_round_id', 'household_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};

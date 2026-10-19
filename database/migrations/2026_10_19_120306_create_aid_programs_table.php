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
        Schema::create('aid_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('type', 30);
            $table->string('unit', 30);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->unsignedTinyInteger('min_family_size')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aid_programs');
    }
};

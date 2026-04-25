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
        Schema::create('precision_definitions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key', 80)->unique();
            $table->string('label', 150);
            $table->text('description')->nullable();

            $table->string('type', 30);       // PrecisionTypeEnum
            $table->string('target', 20);     // PrecisionTargetEnum

            // Paramètres utilisateur (ex: {"min_count": {"type":"int","default":10,"min":1}})
            $table->json('parameters_schema')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precision_definitions');
    }
};

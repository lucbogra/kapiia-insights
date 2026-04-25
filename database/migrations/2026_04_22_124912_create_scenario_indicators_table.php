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
        Schema::create('scenario_indicators', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('scenario_id');
            $table->foreign('scenario_id')
                ->references('id')
                ->on('scenarios')
                ->cascadeOnDelete();

            $table->ulid('indicator_definition_id');
            $table->foreign('indicator_definition_id')
                ->references('id')
                ->on('indicator_definitions')
                ->cascadeOnDelete();

            $table->json('parameters')->nullable();

            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['scenario_id', 'indicator_definition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_indicators');
    }
};

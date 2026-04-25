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
        Schema::create('scenario_precisions', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('scenario_id');
            $table->foreign('scenario_id')
                ->references('id')
                ->on('scenarios')
                ->cascadeOnDelete();

            $table->ulid('precision_definition_id');
            $table->foreign('precision_definition_id')
                ->references('id')
                ->on('precision_definitions')
                ->cascadeOnDelete();

            // Paramètres saisis par l'utilisateur pour cette précision
            $table->json('parameters');

            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['scenario_id', 'precision_definition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_precisions');
    }
};

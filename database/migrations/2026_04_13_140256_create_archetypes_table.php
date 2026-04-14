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
        Schema::create('archetypes', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Combinaison des critères, ex:
            // {"sex":"M","birth_year":{"from":2010,"to":2013},"parents_status":"SEP"}
            $table->json('criteria_values');

            // SHA-256 de criteria_values normalisé (clés triées, valeurs canoniques)
            // Garantit l'unicité indépendamment de l'ordre des clés JSON
            $table->char('criteria_hash', 64)->unique();

            // Générée automatiquement, ex: "M-10-13-SEP"
            // Stockée pour permettre l'édition manuelle ultérieure
            $table->string('nomenclature', 80)->unique();

            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archetypes');
    }
};

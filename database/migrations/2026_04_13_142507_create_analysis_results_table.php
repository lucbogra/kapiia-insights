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
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('archetype_id');
            $table->foreign('archetype_id')
                  ->references('id')
                  ->on('archetypes')
                  ->cascadeOnDelete();

            // Sources interrogées lors de cette analyse
            $table->json('source_connection_ids');

            // Résultats agrégés complets
            $table->json('payload');

            // Population totale trouvée
            $table->unsignedInteger('population_count')->default(0);

            // Permet d'invalider le cache si les données sources ont changé
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};

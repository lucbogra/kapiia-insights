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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name', 150);
            $table->text('description')->nullable();

            // Propriétaire
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Visibilité
            $table->boolean('is_shared')->default(false);

            // Source de population : soit un archétype, soit des critères custom
            $table->ulid('archetype_id')->nullable();
            $table->foreign('archetype_id')
                ->references('id')
                ->on('archetypes')
                ->nullOnDelete();

            // Critères custom (utilisés si archetype_id est null)
            $table->json('criteria_values')->nullable();

            $table->timestamps();

            $table->index(['owner_id', 'is_shared']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};

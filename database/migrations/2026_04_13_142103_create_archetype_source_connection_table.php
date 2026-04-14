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
        Schema::create('archetype_source_connection', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('archetype_id');
            $table->ulid('source_connection_id');

            $table->foreign('archetype_id')
                ->references('id')
                ->on('archetypes')
                ->cascadeOnDelete();

            $table->foreign('source_connection_id')
                ->references('id')
                ->on('source_connections')
                ->cascadeOnDelete();

            // Unicité de la paire pour éviter les doublons
            $table->unique(['archetype_id', 'source_connection_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archetype_source_connection');
    }
};

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
        Schema::create('indicator_definitions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key', 80)->unique();                       // lien vers le code
            $table->string('label', 150);
            $table->text('description')->nullable();

            // Sur quel jeu de données l'indicateur opère
            $table->string('target', 20);

            // Comment présenter le résultat dans l'UI
            $table->string('output_type', 20);

            // Paramètres utilisateur (ex: {"limit": {"type": "int", "default": 10}})
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
        Schema::dropIfExists('indicator_definitions');
    }
};

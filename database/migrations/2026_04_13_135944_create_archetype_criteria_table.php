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
        Schema::create('archetype_criteria', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Clé machine unique, ex: 'birth_year', 'sex', 'sibling_count'
            $table->string('key', 50)->unique();

            // Libellé lisible FR, ex: "Année de naissance"
            $table->string('label', 100);

            // 'discrete' = valeur fixe (ex: sexe, orphelin)
            // 'range'    = plage numérique (ex: année de naissance, nb de frères)
            $table->enum('type', ['discrete', 'range']);

            // discrete : {"values": ["M", "F"]}
            // range    : {"min": 0, "max": 25, "step": 1}
            $table->json('options')->nullable();

            // Préfixe utilisé dans la nomenclature, ex: "M", "SEP", "ORP-P"
            $table->string('nomenclature_prefix', 10)->nullable();

            // Colonne correspondante dans les BDs Kapiia, ex: "birth_year"
            $table->string('source_column', 80);

            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archetype_criterias');
    }
};

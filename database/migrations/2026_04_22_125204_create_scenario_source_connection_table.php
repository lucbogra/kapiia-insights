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
        Schema::create('scenario_source_connection', function (Blueprint $table) {
            $table->ulid('scenario_id');
            $table->foreign('scenario_id')
                ->references('id')
                ->on('scenarios')
                ->cascadeOnDelete();

            $table->ulid('source_connection_id');
            $table->foreign('source_connection_id')
                ->references('id')
                ->on('source_connections')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_source_connection');
    }
};

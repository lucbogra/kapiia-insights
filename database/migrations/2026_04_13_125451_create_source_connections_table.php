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
        Schema::create('source_connections', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('label', 100);                        // "Kapiia — Site Paris"
            $table->string('host', 255);
            $table->unsignedSmallInteger('port')->default(3306);
            $table->string('database_name', 100);
            $table->string('username', 100);
            $table->text('password');                            // stocké chiffré via encrypt()
            $table->string('driver', 20)->default('mysql');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_connections');
    }
};

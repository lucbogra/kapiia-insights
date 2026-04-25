<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Tables sans FK sortantes
        DB::statement('UPDATE archetype_criteria SET id = LOWER(id)');
        DB::statement('UPDATE indicator_definitions SET id = LOWER(id)');
        DB::statement('UPDATE precision_definitions SET id = LOWER(id)');
        DB::statement('UPDATE source_connections SET id = LOWER(id)');

        // archetypes : référencé par archetypes_source_connection, scenarios, analysis_results
        DB::statement('UPDATE archetypes SET id = LOWER(id)');

        // scenarios : référencé par scenario_indicators, scenario_precisions, scenario_source_connection
        DB::statement('UPDATE scenarios SET id = LOWER(id), archetype_id = LOWER(archetype_id)');

        // Pivots et tables enfants
        DB::statement('UPDATE archetype_source_connection SET archetype_id = LOWER(archetype_id), source_connection_id = LOWER(source_connection_id)');
        DB::statement('UPDATE scenario_source_connection SET scenario_id = LOWER(scenario_id), source_connection_id = LOWER(source_connection_id)');
        DB::statement('UPDATE scenario_indicators SET id = LOWER(id), scenario_id = LOWER(scenario_id), indicator_definition_id = LOWER(indicator_definition_id)');
        DB::statement('UPDATE scenario_precisions SET id = LOWER(id), scenario_id = LOWER(scenario_id), precision_definition_id = LOWER(precision_definition_id)');
        DB::statement('UPDATE analysis_results SET id = LOWER(id), archetype_id = LOWER(archetype_id)');

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        DB::statement('UPDATE archetype_criteria SET id = UPPER(id)');
        DB::statement('UPDATE indicator_definitions SET id = UPPER(id)');
        DB::statement('UPDATE precision_definitions SET id = UPPER(id)');
        DB::statement('UPDATE source_connections SET id = UPPER(id)');
        DB::statement('UPDATE archetypes SET id = UPPER(id)');
        DB::statement('UPDATE scenarios SET id = UPPER(id), archetype_id = UPPER(archetype_id)');
        DB::statement('UPDATE archetype_source_connection SET archetype_id = UPPER(archetype_id), source_connection_id = UPPER(source_connection_id)');
        DB::statement('UPDATE scenario_source_connection SET scenario_id = UPPER(scenario_id), source_connection_id = UPPER(source_connection_id)');
        DB::statement('UPDATE scenario_indicators SET id = UPPER(id), scenario_id = UPPER(scenario_id), indicator_definition_id = UPPER(indicator_definition_id)');
        DB::statement('UPDATE scenario_precisions SET id = UPPER(id), scenario_id = UPPER(scenario_id), precision_definition_id = UPPER(precision_definition_id)');
        DB::statement('UPDATE analysis_results SET id = UPPER(id), archetype_id = UPPER(archetype_id)');

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};

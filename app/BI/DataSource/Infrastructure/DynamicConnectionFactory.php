<?php

namespace App\BI\DataSource\Infrastructure;

use App\BI\DataSource\Domain\SourceConnection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Log;

final class DynamicConnectionFactory
{
    /**
     * Crée (ou récupère en cache) une connexion Laravel nommée
     * pour la source donnée.
     */
    public function make(SourceConnection $source): ConnectionInterface
    {
        $name = 'kapiia_' . $source->id;

        // On n'écrase pas une connexion déjà résolue dans cette requête
        // if (! Config::has("database.connections.{$name}")) {
            Config::set("database.connections.{$name}", [
                'driver'    => $source->driver,
                'host'      => $source->host,
                'port'      => $source->port,
                'database'  => $source->databaseName,
                'username'  => $source->username,
                'password'  => $source->password,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'strict'    => true,
            ]);
        // }

        return DB::connection($name);
    }

    /**
     * Teste la connexion sans déclencher d'exception silencieuse.
     */
    public function test(SourceConnection $source): bool
    {
        try {
            $this->make($source)->select('SELECT 1');
            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}

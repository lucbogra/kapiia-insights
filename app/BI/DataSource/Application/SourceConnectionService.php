<?php

namespace App\BI\DataSource\Application;

use App\BI\DataSource\Domain\Repository\SourceConnectionRepositoryInterface;
use App\BI\DataSource\Domain\SourceConnection;
use App\BI\DataSource\Infrastructure\DynamicConnectionFactory;
use Illuminate\Support\Str;

class SourceConnectionService
{
    public function __construct(
        private readonly SourceConnectionRepositoryInterface $repository,
        private readonly DynamicConnectionFactory $connectionFactory,
    ) {}

    public function create(
        string $label,
        string $host,
        int $port,
        string $databaseName,
        string $username,
        string $password,
        string $driver = 'mysql',
    ): SourceConnection {
        $connection = new SourceConnection(
            id: strtolower((string) Str::ulid()),
            label: $label,
            host: $host,
            port: $port,
            databaseName: $databaseName,
            username: $username,
            password: $password,
            driver: $driver,
            isActive: true,
        );

        $this->repository->save($connection);

        return $connection;
    }

    public function test(string $id): bool
    {
        $connection = $this->repository->findById($id);

        if (! $connection) {
            return false;
        }

        $success = $this->connectionFactory->test($connection);

        // On met à jour last_tested_at que la connexion soit réussie ou non
        $this->repository->save(
            $connection->withLastTestedAt(now()->toISOString())
        );

        return $success;
    }

    public function disable(string $id): void
    {
        $connection = $this->repository->findById($id);

        if (! $connection) {
            return;
        }

        $this->repository->save(new SourceConnection(
            id: $connection->id,
            label: $connection->label,
            host: $connection->host,
            port: $connection->port,
            databaseName: $connection->databaseName,
            username: $connection->username,
            password: $connection->password,
            driver: $connection->driver,
            isActive: false,
            lastTestedAt: $connection->lastTestedAt,
        ));
    }
}

<?php

namespace App\BI\DataSource\Infrastructure\Persistence;

use App\BI\DataSource\Domain\SourceConnection;
use App\BI\DataSource\Domain\Repository\SourceConnectionRepositoryInterface;

class SourceConnectionRepository implements SourceConnectionRepositoryInterface
{
    public function findById(string $id): ?SourceConnection
    {
        $model = SourceConnectionModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findAllActive(): array
    {
        return SourceConnectionModel::query()
            ->where('is_active', true)
            ->orderBy('label')
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->all();
    }

    public function findAll(): array
    {
        return SourceConnectionModel::query()
            ->orderBy('label')
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->all();
    }

    public function save(SourceConnection $sourceConnection): void
    {
        SourceConnectionModel::updateOrCreate(
            ['id' => $sourceConnection->id],
            [
                'label'         => $sourceConnection->label,
                'host'          => $sourceConnection->host,
                'port'          => $sourceConnection->port,
                'database_name' => $sourceConnection->databaseName,
                'username'      => $sourceConnection->username,
                'password'      => $sourceConnection->password,
                'driver'        => $sourceConnection->driver,
                'is_active'     => $sourceConnection->isActive,
                'last_tested_at'=> $sourceConnection->lastTestedAt,
            ],
        );
    }

    public function delete(string $id): void
    {
        SourceConnectionModel::destroy($id);
    }

    private function toDomain(SourceConnectionModel $model): SourceConnection
    {
        return new SourceConnection(
            id:           $model->id,
            label:        $model->label,
            host:         $model->host,
            port:         $model->port,
            databaseName: $model->database_name,
            username:     $model->username,
            password:     $model->password,
            driver:       $model->driver,
            isActive:     $model->is_active,
            lastTestedAt: $model->last_tested_at?->toISOString(),
        );
    }
}

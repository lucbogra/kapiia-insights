<?php

namespace App\BI\Profiling\Infrastructure\Persistence;

use App\BI\Profiling\Domain\Archetype;
use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;

class ArchetypeRepository implements ArchetypeRepositoryInterface
{
    public function findById(string $id): ?Archetype
    {
        $model = ArchetypeModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByHash(string $hash): ?Archetype
    {
        $model = ArchetypeModel::where('criteria_hash', $hash)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findAllActive(): array
    {
        return ArchetypeModel::query()
            ->where('is_active', true)
            ->orderBy('nomenclature')
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->all();
    }

    public function save(Archetype $archetype): void
    {
        ArchetypeModel::updateOrCreate(
            ['id' => $archetype->id],
            [
                'criteria_values' => $archetype->criteriaValues,
                'criteria_hash'   => $archetype->criteriaHash,
                'nomenclature'    => $archetype->nomenclature,
                'description'     => $archetype->description,
                'is_active'       => $archetype->isActive,
            ],
        );
    }

    public function delete(string $id): void
    {
        ArchetypeModel::destroy($id);
    }

    private function toDomain(ArchetypeModel $model): Archetype
    {
        return new Archetype(
            id:             $model->id,
            criteriaValues: $model->criteria_values ?? [],
            criteriaHash:   $model->criteria_hash,
            nomenclature:   $model->nomenclature,
            description:    $model->description,
            isActive:       $model->is_active,
        );
    }
}

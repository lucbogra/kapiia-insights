<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Domain\PrecisionDefinition;
use App\BI\Analysis\Domain\Repository\PrecisionDefinitionRepositoryInterface;

class PrecisionDefinitionRepository implements PrecisionDefinitionRepositoryInterface
{
    public function findById(string $id): ?PrecisionDefinition
    {
        $model = PrecisionDefinitionModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByKey(string $key): ?PrecisionDefinition
    {
        $model = PrecisionDefinitionModel::where('key', $key)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findAllActive(): array
    {
        return PrecisionDefinitionModel::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function findAll(): array
    {
        return PrecisionDefinitionModel::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function save(PrecisionDefinition $def): void
    {
        PrecisionDefinitionModel::updateOrCreate(
            ['id' => $def->id],
            [
                'key' => $def->key,
                'label' => $def->label,
                'description' => $def->description,
                'type' => $def->type,
                'target' => $def->target,
                'parameters_schema' => $def->parametersSchema,
                'is_active' => $def->isActive,
                'sort_order' => $def->sortOrder,
            ],
        );
    }

    public function delete(string $id): void
    {
        PrecisionDefinitionModel::destroy($id);
    }

    private function toDomain(PrecisionDefinitionModel $m): PrecisionDefinition
    {
        return new PrecisionDefinition(
            id: $m->id,
            key: $m->key,
            label: $m->label,
            description: $m->description,
            type: $m->type,
            target: $m->target,
            parametersSchema: $m->parameters_schema,
            isActive: $m->is_active,
            sortOrder: $m->sort_order,
        );
    }
}

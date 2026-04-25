<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Domain\IndicatorDefinition;
use App\BI\Analysis\Domain\Repository\IndicatorDefinitionRepositoryInterface;

class IndicatorDefinitionRepository implements IndicatorDefinitionRepositoryInterface
{
    public function findById(string $id): ?IndicatorDefinition
    {
        $model = IndicatorDefinitionModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByKey(string $key): ?IndicatorDefinition
    {
        $model = IndicatorDefinitionModel::where('key', $key)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findAllActive(): array
    {
        return IndicatorDefinitionModel::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function findAll(): array
    {
        return IndicatorDefinitionModel::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function save(IndicatorDefinition $def): void
    {
        IndicatorDefinitionModel::updateOrCreate(
            ['id' => $def->id],
            [
                'key' => $def->key,
                'label' => $def->label,
                'description' => $def->description,
                'target' => $def->target,
                'output_type' => $def->outputType,
                'parameters_schema' => $def->parametersSchema,
                'is_active' => $def->isActive,
                'sort_order' => $def->sortOrder,
            ],
        );
    }

    public function delete(string $id): void
    {
        IndicatorDefinitionModel::destroy($id);
    }

    private function toDomain(IndicatorDefinitionModel $m): IndicatorDefinition
    {
        return new IndicatorDefinition(
            id: $m->id,
            key: $m->key,
            label: $m->label,
            description: $m->description,
            target: $m->target,
            outputType: $m->output_type,
            parametersSchema: $m->parameters_schema,
            isActive: $m->is_active,
            sortOrder: $m->sort_order,
        );
    }
}

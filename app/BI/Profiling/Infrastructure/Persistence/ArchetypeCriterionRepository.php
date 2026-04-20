<?php

namespace App\BI\Profiling\Infrastructure\Persistence;

use App\BI\Profiling\Domain\ArchetypeCriterion;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;

class ArchetypeCriterionRepository implements ArchetypeCriterionRepositoryInterface
{
    public function findById(string $id): ?ArchetypeCriterion
    {
        $model = ArchetypeCriterionModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findAll(): array
    {
        return ArchetypeCriterionModel::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($m) => $this->toDomain($m))
            ->all();
    }

    public function save(ArchetypeCriterion $criterion): void
    {
        ArchetypeCriterionModel::updateOrCreate(
            ['id' => $criterion->id],
            [
                'key'                  => $criterion->key,
                'label'                => $criterion->label,
                'type'                 => $criterion->type,
                'options'              => $criterion->options,
                'nomenclature_prefix'  => $criterion->nomenclaturePrefix,
                'source_column'        => $criterion->sourceColumn,
                'sort_order'           => $criterion->sortOrder,
            ],
        );
    }

    public function delete(string $id): void
    {
        ArchetypeCriterionModel::destroy($id);
    }

    private function toDomain(ArchetypeCriterionModel $model): ArchetypeCriterion
    {
        return new ArchetypeCriterion(
            id:                 $model->id,
            key:                $model->key,
            label:              $model->label,
            type:               $model->type,
            options:            $model->options ?? [],
            nomenclaturePrefix: $model->nomenclature_prefix,
            sourceColumn:       $model->source_column,
            sortOrder:          $model->sort_order,
        );
    }
}

<?php

// app/BI/Analysis/Infrastructure/Persistence/AnalysisResultRepository.php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Domain\AnalysisResult;
use App\BI\Analysis\Domain\Repository\AnalysisResultRepositoryInterface;

class AnalysisResultRepository implements AnalysisResultRepositoryInterface
{
    public function findFreshByArchetype(string $archetypeId): ?AnalysisResult
    {
        $model = AnalysisResultModel::query()
            ->where('archetype_id', $archetypeId)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function save(AnalysisResult $result): void
    {
        AnalysisResultModel::updateOrCreate(
            ['id' => $result->id],
            [
                'archetype_id'         => $result->archetypeId,
                'source_connection_ids'=> $result->sourceConnectionIds,
                'payload'              => $result->payload,
                'population_count'     => $result->populationCount,
                'expires_at'           => $result->expiresAt,
            ],
        );
    }

    public function deleteExpired(): void
    {
        AnalysisResultModel::query()
            ->where('expires_at', '<=', now())
            ->delete();
    }

    public function delete(string $id): void
    {
        AnalysisResultModel::find($id)->delete();
    }

    private function toDomain(AnalysisResultModel $model): AnalysisResult
    {
        return new AnalysisResult(
            id:                  $model->id,
            archetypeId:         $model->archetype_id,
            sourceConnectionIds: $model->source_connection_ids ?? [],
            payload:             $model->payload ?? [],
            populationCount:     $model->population_count,
            expiresAt:           $model->expires_at?->toISOString(),
        );
    }
}

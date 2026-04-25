<?php

namespace App\BI\Profiling\Application\UseCase\CreateArchetypeCriterion;

use App\BI\Profiling\Domain\ArchetypeCriterion;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;
use Illuminate\Support\Str;

final class CreateArchetypeCriterionUseCase
{
    public function __construct(
        private readonly ArchetypeCriterionRepositoryInterface $repository,
    ) {}

    public function execute(CreateArchetypeCriterionCommand $command): ArchetypeCriterion
    {
        $criterion = new ArchetypeCriterion(
            id: strtolower((string) Str::ulid()),
            key: $command->key,
            label: $command->label,
            type: $command->type,
            options: $command->options,
            nomenclaturePrefix: $command->nomenclaturePrefix,
            sourceColumn: $command->sourceColumn,
            sortOrder: $command->sortOrder,
        );

        $this->repository->save($criterion);

        return $criterion;
    }
}

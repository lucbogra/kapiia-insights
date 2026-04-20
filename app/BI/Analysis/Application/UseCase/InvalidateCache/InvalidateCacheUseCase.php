<?php

namespace App\BI\Analysis\Application\UseCase\InvalidateCache;

use App\BI\Analysis\Domain\Repository\AnalysisResultRepositoryInterface;

final class InvalidateCacheUseCase
{
    public function __construct(
        private readonly AnalysisResultRepositoryInterface $repository,
    ) {}

    public function execute(string $id): void
    {
        $this->repository->delete($id);
    }
}

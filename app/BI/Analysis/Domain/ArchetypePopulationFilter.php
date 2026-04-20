<?php

namespace App\BI\Analysis\Domain;

use App\BI\Profiling\Domain\Criteria\CriterionInterface;

final class ArchetypePopulationFilter
{
    /**
     * @param CriterionInterface[] $criteria
     */
    public function __construct(
        private readonly array $criteria,
    ) {}

    /**
     * @return CriterionInterface[]
     */
    public function criteria(): array
    {
        return $this->criteria;
    }

    public function isEmpty(): bool
    {
        return empty($this->criteria);
    }
}

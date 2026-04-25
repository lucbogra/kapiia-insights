<?php

namespace App\BI\Profiling\Domain\Criteria;

final class RangeCriterion implements CriterionInterface
{
    public function __construct(
        private readonly string $column,
        private readonly int|float|null $min,
        private readonly int|float|null $max,
    ) {}

    public function column(): string
    {
        return $this->column;
    }

    public function min(): int|float|null
    {
        return $this->min;
    }

    public function max(): int|float|null
    {
        return $this->max;
    }

    public function isRange(): bool
    {
        return true;
    }
}

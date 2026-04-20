<?php

namespace App\BI\Profiling\Domain\Criteria;

final class DiscreteCriterion implements CriterionInterface
{
    public function __construct(
        private readonly string $column,
        private readonly mixed  $value,
    ) {}

    public function column(): string
    {
        return $this->column;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function isRange(): bool
    {
        return false;
    }
}

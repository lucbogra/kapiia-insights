<?php

namespace App\BI\Analysis\Domain\Precision;

use Illuminate\Database\Query\Builder;

interface PrecisionInterface
{
    public function key(): string;

    public function label(): string;

    public function type(): PrecisionTypeEnum;

    public function target(): PrecisionTargetEnum;

    public function parametersSchema(): ?array;

    /**
     * Applique la précision à une query builder.
     * Le builder passé doit cibler la bonne table selon le target.
     */
    public function apply(Builder $query, array $parameters): void;
}

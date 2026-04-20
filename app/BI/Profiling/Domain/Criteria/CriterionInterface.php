<?php

namespace App\BI\Profiling\Domain\Criteria;

interface CriterionInterface
{
    public function column(): string;
    public function isRange(): bool;
}

<?php

namespace App\BI\Profiling\Application\UseCase\CreateArchetypeCriterion;

final class CreateArchetypeCriterionCommand
{
    public function __construct(
        public readonly string  $key,
        public readonly string  $label,
        public readonly string  $type,
        public readonly array   $options,
        public readonly string  $sourceColumn,
        public readonly ?string $nomenclaturePrefix = null,
        public readonly int     $sortOrder = 0,
    ) {}
}

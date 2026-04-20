<?php

namespace App\BI\Profiling\Application\UseCase\CreateArchetype;

final class CreateArchetypeCommand
{
    public function __construct(
        public readonly array   $criteriaValues,
        public readonly ?string $description = null,
    ) {}
}

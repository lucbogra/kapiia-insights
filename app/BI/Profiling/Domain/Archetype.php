<?php

namespace App\BI\Profiling\Domain;

final class Archetype
{
    public function __construct(
        public readonly string  $id,
        public readonly array   $criteriaValues,
        public readonly string  $criteriaHash,
        public readonly string  $nomenclature,
        public readonly ?string $description,
        public readonly bool    $isActive,
    ) {}

    public static function hashCriteria(array $criteriaValues): string
    {
        ksort($criteriaValues);
        return hash('sha256', json_encode($criteriaValues));
    }
}

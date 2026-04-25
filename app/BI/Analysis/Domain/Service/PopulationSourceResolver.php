<?php

namespace App\BI\Analysis\Domain\Service;

use App\BI\Profiling\Domain\Service\CriteriaValuesSanitizer;

final class PopulationSourceResolver
{
    public function __construct(
        private readonly CriteriaValuesSanitizer $sanitizer,
    ) {}

    /**
     * @return array{0: ?string, 1: ?array}
     */
    public function resolve(?string $archetypeId, ?array $criteriaValues): array
    {
        if ($archetypeId !== null && $archetypeId !== '') {
            return [$archetypeId, null];
        }

        $sanitized = $criteriaValues !== null
            ? $this->sanitizer->sanitize($criteriaValues)
            : [];

        if (empty($sanitized)) {
            throw new \DomainException(
                'Le scénario doit référencer un archétype ou contenir au moins un critère valorisé.'
            );
        }

        return [null, $sanitized];
    }
}

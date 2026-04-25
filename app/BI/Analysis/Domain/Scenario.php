<?php

namespace App\BI\Analysis\Domain;

final class Scenario
{
    /**
     * @param  ScenarioPrecisionConfig[]  $precisions
     * @param  ScenarioIndicatorConfig[]  $indicators
     * @param  string[]  $sourceConnectionIds
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $ownerId,
        public readonly bool $isShared,
        public readonly ?string $archetypeId,
        public readonly ?array $criteriaValues,
        public readonly array $precisions,
        public readonly array $indicators,
        public readonly array $sourceConnectionIds,
    ) {
        if ($this->archetypeId === null && empty($this->criteriaValues)) {
            throw new \DomainException(
                'Un scénario doit soit référencer un archétype, soit définir ses propres critères.'
            );
        }

        if ($this->archetypeId !== null && ! empty($this->criteriaValues)) {
            throw new \DomainException(
                'Un scénario ne peut pas avoir à la fois un archétype et des critères custom.'
            );
        }
    }

    public function usesArchetype(): bool
    {
        return $this->archetypeId !== null;
    }

    public function usesCustomCriteria(): bool
    {
        return $this->criteriaValues !== null && ! empty($this->criteriaValues);
    }

    public function isOwnedBy(string $userId): bool
    {
        return $this->ownerId === $userId;
    }

    public function isVisibleTo(string $userId): bool
    {
        return $this->isOwnedBy($userId) || $this->isShared;
    }
}

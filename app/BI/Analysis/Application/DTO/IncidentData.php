<?php

namespace App\BI\Analysis\Application\DTO;

final class IncidentData
{
    public function __construct(
        public readonly string $intitule,
        public readonly int    $gravite,
        public readonly string $dateIncident,
        public readonly string $lieu,
        public readonly string $attitude,
        public readonly string $sourceLabel
    ) {}
}

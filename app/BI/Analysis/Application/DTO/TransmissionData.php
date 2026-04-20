<?php

namespace App\BI\Analysis\Application\DTO;

final class TransmissionData
{
    public function __construct(
        public readonly string $activitySlug,
        public readonly int    $concentration,
        public readonly int    $behavior,
        public readonly int    $social,
        public readonly string $transmittedAt,
        public readonly string $sourceLabel,
    ) {}
}
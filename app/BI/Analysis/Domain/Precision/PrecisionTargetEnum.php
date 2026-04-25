<?php

namespace App\BI\Analysis\Domain\Precision;

enum PrecisionTargetEnum: string
{
    case Incidents = 'incidents';
    case Transmissions = 'transmissions';

    public function label(): string
    {
        return match ($this) {
            self::Incidents => 'Incidents',
            self::Transmissions => 'Transmissions',
        };
    }
}

<?php

namespace App\BI\Analysis\Domain\Indicator;

enum IndicatorTargetEnum: string
{
    case Incidents = 'incidents';
    case Transmissions = 'transmissions';
    case Global = 'global';

    public function label(): string
    {
        return match ($this) {
            self::Incidents => 'Incidents',
            self::Transmissions => 'Transmissions',
            self::Global => 'Global',
        };
    }
}

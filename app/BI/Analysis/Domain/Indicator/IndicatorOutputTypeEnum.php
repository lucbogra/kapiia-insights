<?php

namespace App\BI\Analysis\Domain\Indicator;

enum IndicatorOutputTypeEnum: string
{
    case Scalar = 'scalar';
    case ListItems = 'list';
    case Distribution = 'distribution';
    case GroupedAverage = 'grouped_average';

    public function label(): string
    {
        return match ($this) {
            self::Scalar => 'Valeur unique',
            self::ListItems => 'Liste',
            self::Distribution => 'Distribution',
            self::GroupedAverage => 'Moyenne groupée',
        };
    }
}

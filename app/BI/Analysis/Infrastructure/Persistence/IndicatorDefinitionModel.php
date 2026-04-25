<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Domain\Indicator\IndicatorOutputTypeEnum;
use App\BI\Analysis\Domain\Indicator\IndicatorTargetEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class IndicatorDefinitionModel extends Model
{
    use HasUlids;

    protected $table = 'indicator_definitions';

    protected $fillable = [
        'key', 'label', 'description', 'target',
        'output_type', 'parameters_schema', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'target' => IndicatorTargetEnum::class,
        'output_type' => IndicatorOutputTypeEnum::class,
        'parameters_schema' => 'array',
        'is_active' => 'boolean',
    ];
}

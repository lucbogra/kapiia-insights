<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Domain\Precision\PrecisionTargetEnum;
use App\BI\Analysis\Domain\Precision\PrecisionTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PrecisionDefinitionModel extends Model
{
    use HasUlids;

    protected $table = 'precision_definitions';

    protected $fillable = [
        'key', 'label', 'description', 'type', 'target',
        'parameters_schema', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'type' => PrecisionTypeEnum::class,
        'target' => PrecisionTargetEnum::class,
        'parameters_schema' => 'array',
        'is_active' => 'boolean',
    ];
}

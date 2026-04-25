<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioIndicatorModel extends Model
{
    use HasUlids;

    protected $table = 'scenario_indicators';

    protected $fillable = [
        'scenario_id',
        'indicator_definition_id',
        'parameters',
        'sort_order',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(ScenarioModel::class);
    }

    public function indicatorDefinition(): BelongsTo
    {
        return $this->belongsTo(IndicatorDefinitionModel::class, 'indicator_definition_id');
    }
}

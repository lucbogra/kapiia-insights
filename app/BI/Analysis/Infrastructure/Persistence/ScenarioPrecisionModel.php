<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioPrecisionModel extends Model
{
    use HasUlids;

    protected $table = 'scenario_precisions';

    protected $fillable = [
        'scenario_id',
        'precision_definition_id',
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

    public function precisionDefinition(): BelongsTo
    {
        return $this->belongsTo(PrecisionDefinitionModel::class, 'precision_definition_id');
    }
}

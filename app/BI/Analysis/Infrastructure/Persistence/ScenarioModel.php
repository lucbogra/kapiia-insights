<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionModel;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScenarioModel extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'scenarios';

    protected $fillable = [
        'id',
        'name',
        'description',
        'owner_id',
        'is_shared',
        'archetype_id',
        'criteria_values',
    ];

    protected $casts = [
        'is_shared' => 'boolean',
        'criteria_values' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function archetype(): BelongsTo
    {
        return $this->belongsTo(ArchetypeModel::class, 'archetype_id');
    }

    public function precisions(): HasMany
    {
        return $this->hasMany(ScenarioPrecisionModel::class, 'scenario_id')
            ->orderBy('sort_order');
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(ScenarioIndicatorModel::class, 'scenario_id')
            ->orderBy('sort_order');
    }

    public function sourceConnections(): BelongsToMany
    {
        return $this->belongsToMany(
            SourceConnectionModel::class,
            'scenario_source_connection',
            'scenario_id',
            'source_connection_id',
        );
    }
}

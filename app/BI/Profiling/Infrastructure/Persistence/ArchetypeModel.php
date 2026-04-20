<?php

namespace App\BI\Profiling\Infrastructure\Persistence;

use App\BI\Analysis\Infrastructure\Persistence\AnalysisResultModel;
use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArchetypeModel extends Model
{
    use HasFactory;

    protected $table = 'archetypes';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'criteria_values',
        'criteria_hash',
        'nomenclature',
        'description',
        'is_active',
    ];

    protected $casts = [
        'criteria_values' => 'array',
        'is_active'       => 'boolean',
    ];

    public function sourceConnections(): BelongsToMany
    {
        return $this->belongsToMany(
            SourceConnectionModel::class,
            'archetype_source_connection',
            'archetype_id',
            'source_connection_id',
            'id',
            'id'
        );
    }

    public function analysisResults(): HasMany
    {
        return $this->hasMany(AnalysisResultModel::class);
    }

    /**
     * Résultat en cache encore valide pour cet archétype.
     */
    public function freshAnalysisResult(): ?AnalysisResultModel
    {
        return $this->analysisResults()
                    ->where('expires_at', '>', now())
                    ->latest()
                    ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Génère le hash de déduplication à partir des critères normalisés.
     * Les clés sont triées pour garantir un hash stable peu importe l'ordre.
     */
    public static function hashCriteria(array $criteriaValues): string
    {
        ksort($criteriaValues);
        return hash('sha256', json_encode($criteriaValues));
    }
}

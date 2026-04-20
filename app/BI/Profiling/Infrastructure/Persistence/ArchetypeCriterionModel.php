<?php

namespace App\BI\Profiling\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArchetypeCriterionModel extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'archetype_criteria';

    protected $fillable = [
        'id',
        'key',
        'label',
        'type',
        'options',
        'nomenclature_prefix',
        'source_column',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    // --- Helpers métier ---

    public function isRange(): bool
    {
        return $this->type === 'range';
    }

    public function isDiscrete(): bool
    {
        return $this->type === 'discrete';
    }

    /**
     * Valeurs possibles pour un critère discret.
     * Ex: ["M", "F"] ou ["aucun", "père", "mère", "les deux"]
     */
    public function discreteValues(): array
    {
        return $this->options['values'] ?? [];
    }

    /**
     * Bornes pour un critère de type plage.
     * Ex: ['min' => 0, 'max' => 25, 'step' => 1]
     */
    public function rangeBounds(): array
    {
        return [
            'min'  => $this->options['min']  ?? 0,
            'max'  => $this->options['max']  ?? 100,
            'step' => $this->options['step'] ?? 1,
        ];
    }
}

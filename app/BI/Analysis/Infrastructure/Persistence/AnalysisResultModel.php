<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisResultModel extends Model
{
    use HasUlids, HasFactory;

    protected $table = 'analysis_results';

    protected $fillable = [
        'archetype_id',
        'source_connection_ids',
        'payload',
        'population_count',
        'expires_at',
    ];

    protected $casts = [
        'source_connection_ids' => 'array',
        'payload'               => 'array',
        'expires_at'            => 'datetime',
    ];

    public function archetype(): BelongsTo
    {
        return $this->belongsTo(ArchetypeModel::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isFresh(): bool
    {
        return ! $this->isExpired();
    }
}

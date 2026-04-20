<?php

namespace App\BI\Profiling\Infrastructure\Persistence;

use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchetypeSourceConnectionModel extends Model
{
    protected $table = 'archetype_source_connection';

    protected $fillable = [
        'archetype_id',
        'source_connection_id',
    ];

    public function archetype(): BelongsTo
    {
        return $this->belongsTo(ArchetypeModel::class);
    }

    public function sourceConnection(): BelongsTo
    {
        return $this->belongsTo(SourceConnectionModel::class);
    }
}

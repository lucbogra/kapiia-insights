<?php

namespace App\BI\DataSource\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;

class SourceConnectionModel extends Model
{
    // use HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'source_connections';

    protected $fillable = [
        'id',
        'label',
        'host',
        'port',
        'database_name',
        'username',
        'password',
        'driver',
        'is_active',
        'last_tested_at',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'last_tested_at' => 'datetime',
        'password'       => 'encrypted', // chiffrement/déchiffrement automatique
    ];

    protected $hidden = ['password'];

    public function archetypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ArchetypeModel::class,
            'archetype_source_connection',
            'source_connection_id',
            'archetype_id',
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

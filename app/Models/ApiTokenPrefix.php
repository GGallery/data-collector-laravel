<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiTokenPrefix extends Model
{
    protected $table = 'api_tokens_prefixes';
    protected $fillable = [
        'platform_name',
        'prefix_token',
    ];

    /**
     * Relazione con i puntatori di sincronizzazione
     */
    public function syncPointers()
    {
        return $this->hasMany(SyncPointer::class, 'platform_prefix', 'prefix_token');
    }

    /**
     * Ottiene l'ultimo puntatore di sincronizzazione
     */
    public function getLastSyncPointer()
    {
        return $this->syncPointers()->latest('updated_at')->first();
    }
}
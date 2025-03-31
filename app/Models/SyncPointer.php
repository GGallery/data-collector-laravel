<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncPointer extends Model
{
    // use HasFactory;

    /**
     * I campi assegnabili in massa
     *
     * @var array
     */
    protected $fillable = [
        'platform_prefix',
        'last_id_processed',
        'last_sync_date',
        'processed_records',
        'success_count',
        'error_count'        
    ];

    /**
     * Gli attributi che devono essere convertiti in tipi nativi.
     *
     * @var array
     */
    protected $casts = [
        'last_sync_date' => 'datetime',
    ];

    /**
     * Relazione con la ApiTokenPrefix
     */
    public function platform()
    {
        return $this->belongsTo(ApiTokenPrefix::class, 'platform_prefix', 'prefix_token');
    }

    /**
     * Metodo di utilità per aggiornare il puntatore di sincronizzazione
     */
    public static function updatePointer(string $platformPrefix, int $lastId): self
    {
        $pointer = self::firstOrNew(['platform_prefix' => $platformPrefix]);
        
        // Aggiorna solo se il nuovo ID è maggiore dell'ultimo registrato
        if ($lastId > $pointer->last_id_processed) {
            $pointer->last_id_processed = $lastId;
            $pointer->last_sync_date = now();
            $pointer->save();
        }
        
        return $pointer;
    }
}
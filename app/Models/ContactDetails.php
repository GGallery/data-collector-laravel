<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactDetails extends Model
{

    protected $table = 'contacts_details';
    
    protected $fillable = [
        'contact_id',
        'cb_cognome',
        'cb_codicefiscale',
        'cb_datadinascita',
        'cb_luogodinascita',
        'cb_provinciadinascita', 
        'cb_indirizzodiresidenza',
        'cb_provdiresidenza',
        'cb_cap',
        'cb_telefono',
        'cb_nome',
        'cb_citta',
        'cb_professionedisciplina', 
        'cb_ordine',
        'cb_numeroiscrizione',
        'cb_reclutamento',
        'cb_codicereclutamento', 
        'cb_professione',
        'cb_profiloprofessionale',
        'cb_settore',
        'cb_societa'
    ];



    public function contact(): BelongsTo {
        return $this->belongsTo(Contact::class);
    }

}
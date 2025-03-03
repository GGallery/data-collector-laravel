<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    /**
     * I tipi degli attributi.
     *
     * @var array
     */
    protected $casts = [
        'cb_datadinascita' => 'date',
    ];

    /**
     * Il contatto associato a questi dettagli.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }    
}

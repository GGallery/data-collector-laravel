<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactDetails extends Model
{

    protected $table = 'contacts_details';
    
    protected $fillable = [
        'cb_cognome',
        'cb_codicefiscale',
        'cb_datadinascita',
        'cb_luogodinascita',
    ];
}

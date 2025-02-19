<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactExtra extends Model
{
    protected $fillable = [
        'cb_cognome',
        'cb_codicefiscale',
        'cb_datadinascita',
        'cb_luogodinascita',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Contact extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'platform_prefix',
        'password',
    ];

    // protected $attributes = [
    //     'name' => '',
    //     'username' => '',
    //     'password' => '',
    // ];

    public function contactDetails(): HasOne {
        return $this->hasOne(ContactDetails::class);
    }
}

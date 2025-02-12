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
}

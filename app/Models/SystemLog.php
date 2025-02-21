<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_log';
    protected $fillable = [
        'file',
        'platform_name',
        'function_name',
        'message',
    ];
}

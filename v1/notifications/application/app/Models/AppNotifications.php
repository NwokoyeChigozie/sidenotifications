<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotifications extends Model
{
    protected $connection = 'app';
    protected $casts = [
        'payload' => 'object'
    ];
}

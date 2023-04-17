<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    function user() {
        return $this->hasOne(User::class, 'account_id', 'account_id');
    }
}

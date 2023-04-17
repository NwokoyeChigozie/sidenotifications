<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    protected $table = 'user_profiles';
    // use SoftDeletes;

    function user()
    {
        return $this->hasOne(User::class, 'account_id', 'account_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessTokens extends Model
{
    public function getBusiness($private_key) {
        $data = $this->where('private_key', $private_key)->orWhere('public_key', $private_key)->first();
        return $data;
    }
}

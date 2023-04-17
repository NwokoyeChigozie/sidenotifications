<?php

namespace App\Models;

use App\Models\Banks;
use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    public function bankName()
    {
        return Banks::where('id', $this->bank_id)->first()->name ?? '';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PluginEvent extends Model
{
    protected $connection = 'app';
    // use SoftDeletes;
}

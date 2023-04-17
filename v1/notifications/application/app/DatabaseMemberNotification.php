<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class DatabaseMemberNotification extends DatabaseNotification
{
    protected $connection = 'app';
    protected $table = 'member_notifications';
}

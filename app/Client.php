<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Client extends Authenticatable
{
    protected $table = 'Clients';
    protected $primaryKey = 'id';
    protected $guarded = [];
}

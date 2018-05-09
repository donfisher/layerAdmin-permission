<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;


class Permissions extends Authenticatable
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $guarded = [];
}

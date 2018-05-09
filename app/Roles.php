<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;


class Roles extends Authenticatable
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $guarded = [];
}

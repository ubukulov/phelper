<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'role';
}

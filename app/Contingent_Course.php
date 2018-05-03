<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contingent_Course extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'contingent_course';

    protected $fillable = [
        'id_contingent','id_course'
    ];
}

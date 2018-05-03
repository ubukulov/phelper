<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contingent_Studyforms extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'contingent_studyforms';

    protected $fillable = [
        'id_contingent','id_study_form'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'student_results';

    protected $fillable = [
        'id', 'id_student', 'id_tutor', 'winner_olimp', 'winner_olimp_txt', 'res_program', 'res_program_txt', 'sport_achievement',
        'type_sport', 'sport_section', 'univer_clubs', 'creative_clubs', 'created_at', 'updated_at'
    ];
}

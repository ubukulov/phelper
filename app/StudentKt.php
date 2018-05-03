<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentKt extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'student_kt';

    protected $fillable = [
        'id', 'id_student', 'id_tutor', 'is_result', 'seria_certificate', 'number_certificate', 'number_ikt', 'count_ball', 'ball_math', 'ball_history',
        'ball_read', 'kt_profile_subject1', 'one_ball', 'kt_profile_subject2', 'two_ball', 'kt_creative_exam1', 'oneExamBall', 'kt_creative_exam2',
        'twoExamBall', 'special_subject', 'special_ball', 'created_at', 'updated_at'
    ];
}

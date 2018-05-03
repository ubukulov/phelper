<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TutorStream extends Model
{
    protected $table = 'tutor_stream';
    protected $connection = 'mysql2';

    protected $fillable = [
        'id', 'tutor_id', 'title', 'created_at', 'updated_at'
    ];
}

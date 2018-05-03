<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'SubjectID', 'SubjectNameRU', 'SubjectCodeRu'
    ];
}

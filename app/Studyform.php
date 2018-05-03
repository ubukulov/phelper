<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Studyform extends Model
{
    protected $connection = 'mysql';
    protected $table = 'studyforms';

    public static function getStudyform($id_study_form){
        $result = DB::select(DB::raw("SELECT Id, NameRu FROM studyforms WHERE Id IN($id_study_form)"));
        return $result;
    }

    public static function getAllStudyform(){
        $result = DB::select(DB::raw("SELECT Id, NameRu FROM studyforms WHERE Id != 2 AND Id != 5 AND Id != 8 AND Id != 14 AND Id != 15"));
        return $result;
    }
}

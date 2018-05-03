<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Studylanguage extends Model
{
    protected $connection = 'mysql';
    protected $table = 'studylanguages';

    public static function getStudylang(){
        $result = DB::select(DB::raw("SELECT Id, NameRU FROM studylanguages"));
        return $result;
    }
}

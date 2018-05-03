<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Profession extends Model
{
    protected $connection = 'mysql';
    protected $table = 'professions';

    public static function getProfession($id_profession){
        $result = DB::select(DB::raw("SELECT professionID, CONCAT_WS('-',professionCode,professionNameRU) AS name FROM professions WHERE professionID IN($id_profession) AND deleted=0"));
        return $result;
    }

    public static function getAllProfession(){
        $result = DB::select(DB::raw("SELECT professionID, CONCAT_WS('-',professionCode,professionNameRU) AS name FROM professions WHERE deleted=0"));
        return $result;
    }
}

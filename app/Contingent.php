<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contingent extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'contingent';

    protected $fillable = [
        'id','id_tutor','id_profession','id_study_lang','id_study_form','id_course','role_id','created_at','updated_at'
    ];
    public static function getContingentProfession($id_tutor){
        $result = DB::select("SELECT CONCAT_WS('-',NP.professionCode,NP.professionNameRU) AS name, PC.id_profession AS professionID FROM phelper.contingent PC
                              LEFT JOIN nitro.professions NP ON NP.professionID=PC.id_profession
                              WHERE PC.id_tutor=$id_tutor GROUP BY PC.id_profession");
        return $result;
    }

    public function role(){
        return $this->hasOne('App\Role', 'fk_role_id','role_id');
    }

    public static function check_contingent($id_tutor){
        $result = Contingent::where(['id_tutor' => $id_tutor])->get();
        if(count($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    public static function check_user_role($tutor_id){
        $result = Contingent::where(['id_tutor' => $tutor_id])->first();
        return $result->role_id;
    }
}

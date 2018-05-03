<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonRole extends Model
{
    protected $connection = 'mysql';
    protected $table = 'person_roles';

    protected $fillable = [
        'personID','roleID','person_type'
    ];

    public static function check_tutor_role($id_tutor){
        $result = PersonRole::where(['personID' => $id_tutor, 'roleID' => 60])->get();
        if(count($result) > 0){
            return true;
        }else{
            return false;
        }
    }
}

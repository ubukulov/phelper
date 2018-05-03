<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{

    protected $connection = 'mysql';
    protected $table = 'tutors';
    public static $tutor;

    protected $fillable = [
        'Login', 'email', 'Password','lastname','firstname','patronymic','TutorID'
    ];

    protected $hidden = [
        'Password'
    ];


    public static function attempt($login,$password){
        $password = md5($password);
        $result = DB::select(DB::raw("SELECT * FROM nitro.tutors WHERE Login='$login' AND Password='$password' AND deleted='0'"));
        if(count($result) > 0){
            User::$tutor = $result;
            return true;
        }
        return false;
    }

    public static function getFullName($id_tutor){
        $result = DB::select("SELECT lastname,firstname,patronymic FROM tutors WHERE TutorID='$id_tutor'");
        return $result[0]->lastname." ".$result[0]->firstname." ".$result[0]->patronymic;
    }
}

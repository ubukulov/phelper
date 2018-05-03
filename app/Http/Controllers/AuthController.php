<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\User;
use Illuminate\Support\Facades\Session;
use App\Contingent;

class AuthController extends Controller
{
    public function login(){
        return view('registration/login');
    }

    public function authenticateUser(){
        $username = Input::get('login');
        $password = Input::get('password');
        if(User::attempt($username, $password)){
            $_SESSION['id_tutor'] = User::$tutor[0]->TutorID;
            $_SESSION['role'] = $this->getUserRoleName(User::$tutor[0]->TutorID);
            $_SESSION['human'] = User::$tutor[0]->lastname." ".User::$tutor[0]->firstname." ".User::$tutor[0]->patronymic;
            $initial = User::$tutor[0]->lastname." ".substr(User::$tutor[0]->firstname,0,2).".";
            setcookie('initial', $initial,0);
            if(Contingent::check_contingent(User::$tutor[0]->TutorID)){
                $_SESSION['last_first'] = User::$tutor[0]->lastname." ".substr(User::$tutor[0]->firstname,0,2).".".substr(User::$tutor[0]->patronymic,0,2).".";
            }else{
                $_SESSION['last_first'] = "";
            }
			if($_SESSION['id_tutor'] == 2245){
				return redirect('user/transcript');
			}else{
				return redirect('user');
			}
        }else{
            Session::flash('message', "Логин или пароль неправильно");
            return redirect()->back();
        }
    }

    # определить роль пользователя
    public function getUserRoleName($id_tutor){
        $result = DB::select("SELECT PR.title FROM phelper.contingent PC
                                INNER JOIN phelper.role PR ON PR.id=PC.role_id
                                WHERE PC.id_tutor='$id_tutor' LIMIT 1");
        return $result[0]->title;
    }
}

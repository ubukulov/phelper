<?php
use App\Contingent;

if(!isset($_SESSION)){
    session_start();
}

Route::group(['middleware' => 'app'], function(){
    Route::get('/', 'UserController@index');
    Route::get('user/methodists', 'UserController@methodists');
    Route::get('user/settings', 'UserController@settings_show_form');
    Route::post('user/settings', 'UserController@settings');
    Route::get('user', ['as' => 'user', 'uses' => 'UserController@index']);
    # итоговая ведомость
    Route::get('/user/summary_statement', 'UserController@summary_statement');
    Route::get('/user/summary/statement/{pid}/{sid}/{ssi}/{course}/{term}/{lang}', 'UserController@sum_statement');
    Route::post('/user/summary_statement', 'IndexController@summary_statement');
    # итоговая ведомость

    # сводная ведомость
    Route::get('/user/statement', 'StatementController@statement_create');
    Route::post('/user/statement', 'StatementController@statement_store');
    # сводная ведомость

    Route::get('index', 'IndexController@welcome');
    Route::get('/institute/{id}', 'IndexController@institute');
    Route::get('/chair/{id}', 'IndexController@chair');
    Route::get('/profession/{pid}/studyform/{sid}/course/{cid}/term/{tid}/year/{year}', 'IndexController@profession');
    Route::get('/profession/{pid}', 'IndexController@group');
    Route::get('/teacher/{sid}', 'UserController@teacher');
    Route::post('/statement', 'IndexController@statement');
    Route::get('/study/{id}', 'IndexController@study');
    Route::get('/contingent/{id}', 'IndexController@tutor_contingent');
    Route::get('/contingent/delete/{id}', function($id){
        Contingent::destroy($id);
        return redirect()->back();
    });
    Route::get('/contingent/profession/{id}', 'UserController@study_forms');
    Route::get('/specialization/profession/{id}', 'UserController@specialization');
    Route::get('/group/specialization/{id}', 'UserController@group');
    Route::get('/contingent/profession/{pid}/studyform/{sid}', 'UserController@course');

    Route::get('user/contingent', 'UserController@contingent');
    Route::post('user/change', 'UserController@change');

    Route::get('/vedomost/{pid}/{sid}/{course}/{term}/{lang}', 'UserController@vedomost');
    Route::get('/individual/{pid}/{sid}/{course}/{lang}/{status}/{from_tran}', 'UserController@individual');

    Route::post('/test', 'UserController@test');

    # Транскрипт
    Route::get("/user/transcript", 'UserController@transcript');
    Route::post('/transcript', 'IndexController@transcript');

    # ректорат
    Route::get('rectorate', ['as' => 'rector', 'uses' => 'RectorateController@index']);
    Route::get('/rectorate/report/{id}','RectorateController@show');

    # Отдел кадр
    Route::get('kadr', 'KadrController@index');
    Route::get('/kadr/report/{id}','KadrController@show');

    #porilojenie
    Route::get('user/prilojenie','IndexController@prilojenie');
    Route::get('/list_year/{pid}/{sid}','UserController@list_year');
    Route::get('/students/{pid}/{sid}/{pyear}','UserController@students');
    Route::get('/student_transcript/{studentid}/{isstudent}','UserController@student_transcript');
    Route::post('/prilojenie_excel','UserController@prilojenie_excel');

    # Студенческий отдел
    Route::get('/student/list/{pid}/{sid}/{course}/{lang}/{spec}', 'UserController@list_students');

    # ИУП
    Route::get('/user/iup', 'IndexController@iup');

    # Приемняя комиссия
    Route::post('/user/entrant', 'ReceptionController@entrant');
    Route::get('/user/kt/{id}', 'ReceptionController@kt');
});

Route::get('/', 'AuthController@login');
Route::post('auth', 'AuthController@authenticateUser');
Route::get('login', 'UserController@login');

Route::get('user/logout', function(){
    session_unset();
    return redirect('/');
});



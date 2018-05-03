<?php

use App\Contingent;
use App\Role;
use App\StudentResult;
use App\StudentKt;

function getUserRoleTitle($id_tutor){
    $result = Contingent::where(['id_tutor' => $id_tutor])->get();
    if($result){
        $role = Role::find($result[0]->role_id);
        return $role->title;
    }
    return false;
}

function getStudentKtInfo($id_student){
    $student_result = StudentResult::where(['id_student' => $id_student])->first();
    $percent = 0;
    if($student_result){
        // 30 %
        $percent += ($student_result->winner_olimp == 1) ? 4 : 0;
        $percent += (!empty($student_result->winner_olimp_txt)) ? 3 : 0;
        $percent += ($student_result->res_program == 1) ? 4 : 0;
        $percent += (!empty($student_result->res_program_txt)) ? 3 : 0;
        $percent += (!empty($student_result->sport_achievement)) ? 1.5 : 0;
        $percent += (!empty($student_result->type_sport)) ? 2.5 : 0;
        $percent += (!empty($student_result->sport_section)) ? 4 : 0;
        $percent += (!empty($student_result->univer_clubs)) ? 4 : 0;
        $percent += (!empty($student_result->creative_clubs)) ? 4 : 0;
    }

    $student_kt = StudentKt::where(['id_student' => $id_student, 'is_result' => '0'])->first();
    if($student_kt){
        // 17.5 %
        $percent += (!empty($student_kt->seria_certificate)) ? 1:0;
        $percent += (!empty($student_kt->number_certificate)) ? 1:0;
        $percent += (!empty($student_kt->number_ikt)) ? 0.5:0;
        $percent += (!empty($student_kt->count_ball)) ? 0.5:0;
        $percent += (!empty($student_kt->ball_math)) ? 1:0;
        $percent += (!empty($student_kt->ball_history)) ? 1:0;
        $percent += (!empty($student_kt->ball_read)) ? 1:0;
        $percent += (!empty($student_kt->kt_profile_subject1)) ? 1:0;
        $percent += (!empty($student_kt->one_ball)) ? 1:0;
        $percent += (!empty($student_kt->kt_profile_subject2)) ? 1:0;
        $percent += (!empty($student_kt->two_ball)) ? 1:0;
        $percent += (!empty($student_kt->kt_creative_exam1)) ? 1:0;
        $percent += (!empty($student_kt->oneExamBall)) ? 2:0;
        $percent += (!empty($student_kt->kt_creative_exam2)) ? 1:0;
        $percent += (!empty($student_kt->twoExamBall)) ? 1:0;
        $percent += (!empty($student_kt->special_subject)) ? 1:0;
        $percent += (!empty($student_kt->special_ball)) ? 1.5:0;
    }

    $student_kt1 = StudentKt::where(['id_student' => $id_student, 'is_result' => '1'])->first();
    if($student_kt1){
        // 17.5 %
        $percent += (!empty($student_kt1->seria_certificate)) ? 1:0;
        $percent += (!empty($student_kt1->number_certificate)) ? 1:0;
        $percent += (!empty($student_kt1->number_ikt)) ? 0.5:0;
        $percent += (!empty($student_kt1->count_ball)) ? 0.5:0;
        $percent += (!empty($student_kt1->ball_math)) ? 1:0;
        $percent += (!empty($student_kt1->ball_history)) ? 1:0;
        $percent += (!empty($student_kt1->ball_read)) ? 1:0;
        $percent += (!empty($student_kt1->kt_profile_subject1)) ? 1:0;
        $percent += (!empty($student_kt1->one_ball)) ? 1:0;
        $percent += (!empty($student_kt1->kt_profile_subject2)) ? 1:0;
        $percent += (!empty($student_kt1->two_ball)) ? 1:0;
        $percent += (!empty($student_kt1->kt_creative_exam1)) ? 1:0;
        $percent += (!empty($student_kt1->oneExamBall)) ? 2:0;
        $percent += (!empty($student_kt1->kt_creative_exam2)) ? 1:0;
        $percent += (!empty($student_kt1->twoExamBall)) ? 1:0;
        $percent += (!empty($student_kt1->special_subject)) ? 1:0;
        $percent += (!empty($student_kt1->special_ball)) ? 1.5:0;
    }

    $student_kt2 = StudentKt::where(['id_student' => $id_student, 'is_result' => '2'])->first();
    if($student_kt2){
        // 17.5 %
        $percent += (!empty($student_kt2->seria_certificate)) ? 1:0;
        $percent += (!empty($student_kt2->number_certificate)) ? 1:0;
        $percent += (!empty($student_kt2->number_ikt)) ? 0.5:0;
        $percent += (!empty($student_kt2->count_ball)) ? 0.5:0;
        $percent += (!empty($student_kt2->ball_math)) ? 1:0;
        $percent += (!empty($student_kt2->ball_history)) ? 1:0;
        $percent += (!empty($student_kt2->ball_read)) ? 1:0;
        $percent += (!empty($student_kt2->kt_profile_subject1)) ? 1:0;
        $percent += (!empty($student_kt2->one_ball)) ? 1:0;
        $percent += (!empty($student_kt2->kt_profile_subject2)) ? 1:0;
        $percent += (!empty($student_kt2->two_ball)) ? 1:0;
        $percent += (!empty($student_kt2->kt_creative_exam1)) ? 1:0;
        $percent += (!empty($student_kt2->oneExamBall)) ? 2:0;
        $percent += (!empty($student_kt2->kt_creative_exam2)) ? 1:0;
        $percent += (!empty($student_kt2->twoExamBall)) ? 1:0;
        $percent += (!empty($student_kt2->special_subject)) ? 1:0;
        $percent += (!empty($student_kt2->special_ball)) ? 1.5:0;
    }

    $student_ent = StudentKt::where(['id_student' => $id_student, 'is_result' => '3'])->first();
    if($student_ent){
        // 17.5 %
        $percent += (!empty($student_ent->seria_certificate)) ? 1:0;
        $percent += (!empty($student_ent->number_certificate)) ? 1:0;
        $percent += (!empty($student_ent->number_ikt)) ? 0.5:0;
        $percent += (!empty($student_ent->count_ball)) ? 0.5:0;
        $percent += (!empty($student_ent->ball_math)) ? 1:0;
        $percent += (!empty($student_ent->ball_history)) ? 1:0;
        $percent += (!empty($student_ent->ball_read)) ? 1:0;
        $percent += (!empty($student_ent->kt_profile_subject1)) ? 1:0;
        $percent += (!empty($student_ent->one_ball)) ? 1:0;
        $percent += (!empty($student_ent->kt_profile_subject2)) ? 1:0;
        $percent += (!empty($student_ent->two_ball)) ? 1:0;
        $percent += (!empty($student_ent->kt_creative_exam1)) ? 1:0;
        $percent += (!empty($student_ent->oneExamBall)) ? 2:0;
        $percent += (!empty($student_ent->kt_creative_exam2)) ? 1:0;
        $percent += (!empty($student_ent->twoExamBall)) ? 1:0;
        $percent += (!empty($student_ent->special_subject)) ? 1:0;
        $percent += (!empty($student_ent->special_ball)) ? 1.5:0;
    }

    $str = '';
    switch (true){
        case ($percent < 30):
            $str = '<span style="color: red;">Заполнено: '.$percent." %</span>";
            break;
        case ($percent >= 30) AND ($percent < 60):
            $str = '<span style="color: orange;">Заполнено: '.$percent." %</span>";
            break;
        case ($percent >= 60):
            $str = '<span style="color: green;">Заполнено: '.$percent." %</span>";
            break;
    }

    return $str;
}
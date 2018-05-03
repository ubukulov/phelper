<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\StudentResult;
use App\StudentKt;

class ReceptionController extends Controller
{
    public function entrant(Request $request){
        $data = $request->all();
        $sport_section = '';
        $univer_clubs  = '';
        $creative_clubs  = '';
        $id_tutor = $_SESSION['id_tutor'];
        $id_student = $data['id_student'];
        if(!$id_student){
            return redirect()->back();
        }
        $winner_olimp_txt = $data['winner_olimp_txt'];
        $res_program_txt = $data['res_program_txt'];
        $sport_achievement = $data['sport_achievement'];
        $type_sport = $data['type_sport'];
        if(isset($data['sport_section'])){
            for($i=0; $i<count($data['sport_section']); $i++){
                $sport_section .= $data['sport_section'][$i].',';
            }
            $sport_section = trim($sport_section,',');
        }
        if(isset($data['univer_clubs'])){
            for($i=0; $i<count($data['univer_clubs']); $i++){
                $univer_clubs .= $data['univer_clubs'][$i].',';
            }
            $univer_clubs = trim($univer_clubs,',');
        }
        if(isset($data['creative_clubs'])){
            for($i=0; $i<count($data['creative_clubs']); $i++){
                $creative_clubs .= $data['creative_clubs'][$i].',';
            }
            $creative_clubs = trim($creative_clubs,',');
        }
        $winner_olimp = (isset($data['winner_olimp'])) ? 1 : 0;
        $res_program = (isset($data['res_program'])) ? 1 : 0;

        $student_result = StudentResult::where(['id_student' => $id_student])->first();
        if($student_result){
            $student_result->update([
                'id_student' => $id_student, 'id_tutor' => $id_tutor, 'winner_olimp' => $winner_olimp, 'winner_olimp_txt' => $winner_olimp_txt, 'res_program' => $res_program,
                'res_program_txt' => $res_program_txt, 'sport_achievement' => $sport_achievement, 'type_sport' => $type_sport, 'sport_section' => $sport_section,
                'univer_clubs' => $univer_clubs, 'creative_clubs' => $creative_clubs, 'updated_at' => time()
            ]);
        }else{
            StudentResult::create([
                'id_student' => $id_student, 'id_tutor' => $id_tutor, 'winner_olimp' => $winner_olimp, 'winner_olimp_txt' => $winner_olimp_txt, 'res_program' => $res_program,
                'res_program_txt' => $res_program_txt, 'sport_achievement' => $sport_achievement, 'type_sport' => $type_sport, 'sport_section' => $sport_section,
                'univer_clubs' => $univer_clubs, 'creative_clubs' => $creative_clubs, 'created_at' => time()
            ]);
        }

        if(isset($data['is_result_kt'])){
            $student_kt = StudentKt::where(['id_student' => $id_student, 'is_result' => '0'])->first();
            if($student_kt){
                $student_kt->update([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '0', 'seria_certificate' => $data['seria_certificate'],
                    'number_certificate' => $data['number_certificate'], 'number_ikt' => $data['number_ikt'], 'count_ball' => $data['count_ball'],
                    'ball_math' => $data['ball_math'], 'ball_history' => $data['ball_history'], 'ball_read' => $data['ball_read'],
                    'kt_profile_subject1' => $data['kt_profile_subject1'], 'one_ball' => $data['one_ball'], 'kt_profile_subject2' => $data['kt_profile_subject2'], 'two_ball' => $data['two_ball'],
                    'kt_creative_exam1' => $data['kt_creative_exam1'], 'oneExamBall' => $data['oneExamBall'], 'kt_creative_exam2' => $data['kt_creative_exam2'], 'twoExamBall' => $data['twoExamBall'],
                    'special_subject' => $data['special_subject'], 'special_ball' => $data['special_ball'], 'updated_at' => time()
                ]);
            }else{
                StudentKt::create([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '0', 'seria_certificate' => $data['seria_certificate'],
                    'number_certificate' => $data['number_certificate'], 'number_ikt' => $data['number_ikt'], 'count_ball' => $data['count_ball'],
                    'ball_math' => $data['ball_math'], 'ball_history' => $data['ball_history'], 'ball_read' => $data['ball_read'],
                    'kt_profile_subject1' => $data['kt_profile_subject1'], 'one_ball' => $data['one_ball'], 'kt_profile_subject2' => $data['kt_profile_subject2'], 'two_ball' => $data['two_ball'],
                    'kt_creative_exam1' => $data['kt_creative_exam1'], 'oneExamBall' => $data['oneExamBall'], 'kt_creative_exam2' => $data['kt_creative_exam2'], 'twoExamBall' => $data['twoExamBall'],
                    'special_subject' => $data['special_subject'], 'special_ball' => $data['special_ball'], 'created_at' => time()
                ]);
            }
        }

        if(isset($data['is_result_kt1'])){
            $student_kt1 = StudentKt::where(['id_student' => $id_student, 'is_result' => '1'])->first();
            if($student_kt1){
                $student_kt1->update([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '1', 'seria_certificate' => $data['seria_certificate1'],
                    'number_certificate' => $data['number_certificate1'], 'number_ikt' => $data['number_ikt1'], 'count_ball' => $data['count_ball1'],
                    'ball_math' => $data['ball_math1'], 'ball_history' => $data['ball_history1'], 'ball_read' => $data['ball_read1'],
                    'kt_profile_subject1' => $data['kt1_profile_subject1'], 'one_ball' => $data['one_ball1'], 'kt_profile_subject2' => $data['kt1_profile_subject2'], 'two_ball' => $data['two_ball1'],
                    'kt_creative_exam1' => $data['kt1_creative_exam1'], 'oneExamBall' => $data['oneExamBall1'], 'kt_creative_exam2' => $data['kt1_creative_exam2'], 'twoExamBall' => $data['twoExamBall1'],
                    'special_subject' => $data['special_subject1'], 'special_ball' => $data['special_ball1'], 'updated_at' => time()
                ]);
            }else{
                StudentKt::create([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '1', 'seria_certificate' => $data['seria_certificate1'],
                    'number_certificate' => $data['number_certificate1'], 'number_ikt' => $data['number_ikt1'], 'count_ball' => $data['count_ball1'],
                    'ball_math' => $data['ball_math1'], 'ball_history' => $data['ball_history1'], 'ball_read' => $data['ball_read1'],
                    'kt_profile_subject1' => $data['kt1_profile_subject1'], 'one_ball' => $data['one_ball1'], 'kt_profile_subject2' => $data['kt1_profile_subject2'], 'two_ball' => $data['two_ball1'],
                    'kt_creative_exam1' => $data['kt1_creative_exam1'], 'oneExamBall' => $data['oneExamBall1'], 'kt_creative_exam2' => $data['kt1_creative_exam2'], 'twoExamBall' => $data['twoExamBall1'],
                    'special_subject' => $data['special_subject1'], 'special_ball' => $data['special_ball1'], 'created_at' => time()
                ]);
            }
        }

        if(isset($data['is_result_kt2'])){
            $student_kt2 = StudentKt::where(['id_student' => $id_student, 'is_result' => '2'])->first();
            if($student_kt2){
                $student_kt2->update([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '2', 'seria_certificate' => $data['seria_certificate2'],
                    'number_certificate' => $data['number_certificate2'], 'number_ikt' => $data['number_ikt2'], 'count_ball' => $data['count_ball2'],
                    'ball_math' => $data['ball_math2'], 'ball_history' => $data['ball_history2'], 'ball_read' => $data['ball_read2'],
                    'kt_profile_subject1' => $data['kt2_profile_subject1'], 'one_ball' => $data['one_ball2'], 'kt_profile_subject2' => $data['kt2_profile_subject2'], 'two_ball' => $data['two_ball2'],
                    'kt_creative_exam1' => $data['kt2_creative_exam1'], 'oneExamBall' => $data['oneExamBall2'], 'kt_creative_exam2' => $data['kt2_creative_exam2'], 'twoExamBall' => $data['twoExamBall2'],
                    'special_subject' => $data['special_subject2'], 'special_ball' => $data['special_ball2'], 'updated_at' => time()
                ]);
            }else{
                StudentKt::create([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '2', 'seria_certificate' => $data['seria_certificate2'],
                    'number_certificate' => $data['number_certificate2'], 'number_ikt' => $data['number_ikt2'], 'count_ball' => $data['count_ball2'],
                    'ball_math' => $data['ball_math2'], 'ball_history' => $data['ball_history2'], 'ball_read' => $data['ball_read2'],
                    'kt_profile_subject1' => $data['kt2_profile_subject1'], 'one_ball' => $data['one_ball2'], 'kt_profile_subject2' => $data['kt2_profile_subject2'], 'two_ball' => $data['two_ball2'],
                    'kt_creative_exam1' => $data['kt2_creative_exam1'], 'oneExamBall' => $data['oneExamBall2'], 'kt_creative_exam2' => $data['kt2_creative_exam2'], 'twoExamBall' => $data['twoExamBall2'],
                    'special_subject' => $data['special_subject2'], 'special_ball' => $data['special_ball2'], 'created_at' => time()
                ]);
            }
        }

        if(isset($data['is_result_ent'])){
            $student_ent = StudentKt::where(['id_student' => $id_student, 'is_result' => '3'])->first();
            if($student_ent){
                $student_ent->update([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '3', 'seria_certificate' => $data['seria_certificate3'],
                    'number_certificate' => $data['number_certificate3'], 'number_ikt' => $data['number_ikt3'], 'count_ball' => $data['count_ball3'],
                    'ball_math' => $data['ball_math3'], 'ball_history' => $data['ball_history3'], 'ball_read' => $data['ball_read3'],
                    'kt_profile_subject1' => $data['ent_profile_subject1'], 'one_ball' => $data['one_ball3'], 'kt_profile_subject2' => $data['ent_profile_subject2'], 'two_ball' => $data['two_ball3'],
                    'kt_creative_exam1' => $data['ent_creative_exam1'], 'oneExamBall' => $data['oneExamBall3'], 'kt_creative_exam2' => $data['ent_creative_exam2'], 'twoExamBall' => $data['twoExamBall3'],
                    'special_subject' => $data['special_subject3'], 'special_ball' => $data['special_ball3'], 'updated_at' => time()
                ]);
            }else{
                StudentKt::create([
                    'id_student' => $id_student, 'id_tutor' => $id_tutor, 'is_result' => '3', 'seria_certificate' => $data['seria_certificate3'],
                    'number_certificate' => $data['number_certificate3'], 'number_ikt' => $data['number_ikt3'], 'count_ball' => $data['count_ball3'],
                    'ball_math' => $data['ball_math3'], 'ball_history' => $data['ball_history3'], 'ball_read' => $data['ball_read3'],
                    'kt_profile_subject1' => $data['ent_profile_subject1'], 'one_ball' => $data['one_ball3'], 'kt_profile_subject2' => $data['ent_profile_subject2'], 'two_ball' => $data['two_ball3'],
                    'kt_creative_exam1' => $data['ent_creative_exam1'], 'oneExamBall' => $data['oneExamBall3'], 'kt_creative_exam2' => $data['ent_creative_exam2'], 'twoExamBall' => $data['twoExamBall3'],
                    'special_subject' => $data['special_subject3'], 'special_ball' => $data['special_ball3'], 'created_at' => time()
                ]);
            }
        }

        if(isset($_SESSION['student_results'])){
            unset($_SESSION['student_results']);
        }
        return redirect()->back()->with('message', 'Успешно сохранен');
    }

    public function kt($id_student){
        $student_results = StudentResult::where(['id_student' => $id_student])->first();
        $student_kt = StudentKt::where(['id_student' => $id_student, 'is_result' => '0'])->first();
        $student_kt1 = StudentKt::where(['id_student' => $id_student, 'is_result' => '1'])->first();
        $student_kt2 = StudentKt::where(['id_student' => $id_student, 'is_result' => '2'])->first();
        $student_ent = StudentKt::where(['id_student' => $id_student, 'is_result' => '3'])->first();

        return response()->json([
            'student_results' => $student_results, 'student_kt' => $student_kt, 'student_kt1' => $student_kt1, 'student_kt2' => $student_kt2, 'student_ent' => $student_ent
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contingent;
use App\Http\Requests;
use App\Profession;
use App\Studyform;
use App\Studylanguage;
use Illuminate\Support\Facades\DB;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class StatementController extends Controller
{

    public function statement_create(){
        switch (Contingent::check_user_role($_SESSION['id_tutor'])) {
            case 1:
                // Методист-регистратор
                $professions = Contingent::getContingentProfession($_SESSION['id_tutor']);
                $study_lang = Studylanguage::getStudylang();
                return view('registration/statement', compact('professions', 'study_lang'));
                break;
            case 2:
                // Начальник управление регистрации
                $professions = Profession::getAllProfession();
                $study_form = Studyform::getAllStudyform();
                $study_lang = Studylanguage::getStudylang();
                $course = ['1', '2', '3', '4', '5'];
                return view('registration/statement', compact('professions', 'study_form', 'study_lang', 'course'));
                break;
        }
    }

    public function statement_store(Request $request){
        $id_profession      = (int) $request->input('id_profession');
        $id_specialization  = (int) $request->input('id_specialization');
        $id_course          = (int) $request->input('id_course');
        $id_term            = (int) $request->input('id_term');
        $id_study_lang      = (int) $request->input('id_study_lang');
        $id_study_form      = (int) $request->input('id_study_form');
        $start_date         = $request->input('start_date');
        $course = 0;
        $study_year = '';
        $year = 0;
        switch ($id_term){
            case 1:
                $course = 1;
                $year = $start_date + 1;
                $study_year = $start_date.'/'.$year;
                break;
            case 2:
                $course = 1;
                $year = $start_date + 1;
                $study_year = $start_date.'/'.$year;
                break;
            case 3:
                $course = 2;
                $year1 = $start_date + 1;
                $year2 = $start_date + 2;
                $study_year = $year1.'/'.$year2;
                break;
            case 4:
                $course = 2;
                $year1 = $start_date + 1;
                $year2 = $start_date + 2;
                $study_year = $year1.'/'.$year2;
                break;
            case 5:
                $course = 3;
                break;
            case 6:
                $course = 3;
                break;
        }

        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$id_profession' AND PC.deleted IS NULL)");

        $university = $result1[0]->facultyNameKZ." / ".$result1[0]->facultyNameRU;
        $id_director = $result1[0]->facultyDean;
        $result2 = DB::select("SELECT * FROM tutors WHERE TutorID='$id_director'");
        if($id_specialization == 215){
            $director = 'Жанбеков Х.Н.';
        }else{
            $director = $result2[0]->lastname." ".substr($result2[0]->firstname,0,2).".".substr($result2[0]->patronymic,0,2).".";
        }
        $id_faculty = $result1[0]->FacultyID;
        $result3 = DB::select("SELECT 
            CONCAT(CASE NT.lastname WHEN '' THEN '' ELSE NT.lastname END,
                  CASE NT.firstname WHEN '' THEN '' ELSE CONCAT(' ',LEFT(NT.firstname,1)) END,
                  CASE NT.patronymic WHEN '' THEN '' ELSE CONCAT('.',LEFT(NT.patronymic,1),'.') END) AS tutor 
            FROM phelper.testing_department TD
            LEFT JOIN nitro.tutors NT ON NT.TutorID=TD.tutor_id
            LEFT JOIN nitro.faculties NF ON NF.FacultyID=TD.faculty_id
            WHERE TD.faculty_id='$id_faculty'");
        $tutor_ct = $result3[0]->tutor;

        date_default_timezone_set('Asia/Almaty');
        $filename = base_path() . "/public/reports/summary.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $styleArray = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                )
            ),
            'font'  => array(
                'size'  => 8,
                'name'  => 'Times New Roman'
            )
        );
        $res_profession = DB::select("SELECT * FROM nitro.professions WHERE professionID='$id_profession'");
        $res_lang = DB::select("SELECT * FROM nitro.studylanguages WHERE Id='$id_study_lang'");
        $res_spec = DB::select("SELECT * FROM nitro.specializations WHERE id='$id_specialization'");
        $profession = $res_profession[0]->professionCode."-".$res_profession[0]->professionNameRU;
        $study_form = $id_study_form;
        $course_number = $course;
        $semester = $id_term;
        $lang = $res_lang[0]->NameRU;
        $lang = mb_substr($lang,0,3);
        $spec_name = $res_spec[0]->nameru;
        if(date("m") < '02'){
            $active_sheet->setCellValue('B5', 'ҚЫСҚЫ СЕССИЯНЫҢ ЖИНАҚ ВЕДОМОСЫ /  СВОДНАЯ ВЕДОМОСТЬ  ЗИМНЕЙ СЕССИИ');
        }else{
            $active_sheet->setCellValue('B5', 'ЖАЗҒЫ СЕССИЯНЫҢ ЖИНАҚ ВЕДОМОСЫ /  СВОДНАЯ ВЕДОМОСТЬ  ЛЕТНЕЙ СЕССИИ');
        }

        $str = "Мамандығы/Специальность, шифр: ";
        $active_sheet->setCellValue('C4',$university);
        $active_sheet->setCellValue('B6',$str.$profession.",        оқу тілі/язык обучения: ".$lang.",        курс: ".$course_number.",        семестр: ".$semester);
        $active_sheet->setCellValue('B7',"Оқу түрі/Форма обучения: ".$this->get_study_form($study_form,1).",        оқу жылы/учебный год: ".$study_year.",        Түскен жылы/Год поступления: ".$start_date.",        оқу мерзімі/срок обучения: ".$this->get_study_form($study_form,2));
        $active_sheet->setCellValue('B8',"мамандануы/cпециализации: ".$spec_name.",        күні/дата: ".date("d.m.Y H:i:s"));

        if($id_term == 2){
            $result2 = DB::select("
SELECT PIS.* FROM ((SELECT
                    SB.SubjectCode,SB.SubjectNameRU,SB.SubjectNameKZ,SB.SubjectNameENG,
                    NT.totalmark,NT.credits,A.gnt,A.lastname,A.firstname,A.patronymic,SB.SubjectID,A.studentid
                    FROM
                    (SELECT Q.queryID,Q.subjectid as tupSubjectID,Q.studentid,Q.year,Q.term,
                    ST.studyformid,ST.StudyLanguageID,ST.ProfessionID,ST.CourseNumber,ST.specializationID,
                    ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND
                    ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND ST.specializationID='$id_specialization' AND
                    Q.studentID =ST.studentID) AS A
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    LEFT JOIN nitro.totalmarks NT ON NT.studentID=A.studentid AND NT.queryID=A.queryID
                    )
UNION
(SELECT PID.subjectcode,PID.subjectnameRU,PID.subjectnameKZ,PID.subjectnameEN,PID.Totalmark,PID.credits,PID.gnt,PID.lastname, PID.firstname,PID.patronymic,OOO.subjectid,PID.studentid FROM
(SELECT * FROM nitro.transcript NT

INNER JOIN (SELECT DISTINCT ST.studentid AS student_id, ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM
                    nitro.students AS ST,nitro.queries QQ

                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND
                          ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND ST.specializationID='$id_specialization' AND QQ.studentID =ST.studentID AND QQ.year='$course' ) POD ON POD.student_id=NT.StudentID AND NT.coursenumber='$id_course') PID
LEFT JOIN (SELECT SB.SubjectCode,A.studentid,SB.SubjectID
                    FROM
                    (SELECT Q.subjectid as tupSubjectID,Q.studentid

                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND
                    ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND ST.specializationID='$id_specialization' AND
                    Q.studentID =ST.studentID) AS A
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    ) OOO ON OOO.studentid=PID.StudentID AND OOO.SubjectCode=PID.subjectcode
WHERE OOO.SubjectCode IS NULL)) PIS ORDER BY SubjectNameRU,lastname,firstname,patronymic");

            $result = DB::select("
SELECT PIS.* FROM ((SELECT 
                    SB.SubjectCode,SB.SubjectNameRU,SB.SubjectNameKZ,SB.SubjectNameENG,
                    NT.totalmark,NT.credits,A.gnt,A.lastname,A.firstname,A.patronymic,SB.SubjectID,A.studentid
                    FROM
                    (SELECT Q.queryID,Q.subjectid as tupSubjectID,Q.studentid,Q.year,Q.term,
                    ST.studyformid,ST.StudyLanguageID,ST.ProfessionID,ST.CourseNumber,ST.specializationID,
                    ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND ST.specializationID='$id_specialization' AND Q.term='$id_term' AND 
                    Q.studentID =ST.studentID) AS A 
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    LEFT JOIN nitro.totalmarks NT ON NT.studentID=A.studentid AND NT.queryID=A.queryID
                    )
UNION
(SELECT PID.subjectcode,PID.subjectnameRU,PID.subjectnameKZ,PID.subjectnameEN,PID.Totalmark,PID.credits,PID.gnt,PID.lastname, PID.firstname,PID.patronymic,OOO.subjectid,PID.studentid FROM
(SELECT * FROM nitro.transcript NT 

INNER JOIN (SELECT DISTINCT ST.studentid AS student_id, ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM 
                    nitro.students AS ST,nitro.queries QQ

                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                          ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND ST.specializationID='$id_specialization' AND
                          QQ.term='$id_term' AND QQ.studentID =ST.studentID AND QQ.year='$course' ) POD ON POD.student_id=NT.StudentID AND NT.term='$id_term' AND NT.coursenumber='$id_course') PID
LEFT JOIN (SELECT SB.SubjectCode,A.studentid,SB.SubjectID
                    FROM
                    (SELECT Q.subjectid as tupSubjectID,Q.studentid
                    
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND Q.term='$id_term' AND ST.specializationID='$id_specialization' AND
                    Q.studentID =ST.studentID) AS A 
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    ) OOO ON OOO.studentid=PID.StudentID AND OOO.SubjectCode=PID.subjectcode
WHERE OOO.SubjectCode IS NULL)) PIS ORDER BY SubjectNameRU,lastname,firstname,patronymic");
        }else{
            $result = DB::select("
SELECT PIS.* FROM ((SELECT 
                    SB.SubjectCode,SB.SubjectNameRU,SB.SubjectNameKZ,SB.SubjectNameENG,
                    NT.totalmark,NT.credits,A.gnt,A.lastname,A.firstname,A.patronymic,SB.SubjectID,A.studentid
                    FROM
                    (SELECT Q.queryID,Q.subjectid as tupSubjectID,Q.studentid,Q.year,Q.term,
                    ST.studyformid,ST.StudyLanguageID,ST.ProfessionID,ST.CourseNumber,ST.specializationID,
                    ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND ST.specializationID='$id_specialization' AND Q.term='$id_term' AND 
                    Q.studentID =ST.studentID AND Q.year='$course') AS A 
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    LEFT JOIN nitro.totalmarks NT ON NT.studentID=A.studentid AND NT.queryID=A.queryID
                    )
UNION
(SELECT PID.subjectcode,PID.subjectnameRU,PID.subjectnameKZ,PID.subjectnameEN,PID.Totalmark,PID.credits,PID.gnt,PID.lastname, PID.firstname,PID.patronymic,OOO.subjectid,PID.studentid FROM
(SELECT * FROM nitro.transcript NT 

INNER JOIN (SELECT DISTINCT ST.studentid AS student_id, ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM 
                    nitro.students AS ST,nitro.queries QQ

                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                          ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND ST.specializationID='$id_specialization' AND
                          QQ.term='$id_term' AND QQ.studentID =ST.studentID AND QQ.year='$course' ) POD ON POD.student_id=NT.StudentID AND NT.term='$id_term' AND NT.coursenumber='$id_course') PID
LEFT JOIN (SELECT SB.SubjectCode,A.studentid,SB.SubjectID
                    FROM
                    (SELECT Q.subjectid as tupSubjectID,Q.studentid
                    
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$id_profession' AND ST.studyformid='$id_study_form' AND ST.studylanguageid='$id_study_lang' AND ST.CourseNumber='$id_course' AND Q.term='$id_term' AND ST.specializationID='$id_specialization' AND
                    Q.studentID =ST.studentID AND Q.year='$course') AS A 
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    ) OOO ON OOO.studentid=PID.StudentID AND OOO.SubjectCode=PID.subjectcode
WHERE OOO.SubjectCode IS NULL)) PIS ORDER BY SubjectNameRU,lastname,firstname,patronymic");
        }


        if(!$result){
            dd("По выбранным данным не найдено студентов");
        }

        $arr_student    = [];
        $arr_discipline = [];
        $arr_gpa    = [];
        $arr_credit = [];

        $col_start = 4;
        $row_start = 16;



        $count_credit = 0;
        for($i=0; $i< count($result); $i++) {
            $subjectid = $result[$i]->SubjectCode;
            $studentid = $result[$i]->studentid;
            $bool = array_search($subjectid,$arr_discipline);
            if($bool === FALSE){
                $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '11';
                $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '11';
                $range = $letter1 . ":" . $letter2;
                $active_sheet->mergeCells($range);
                $active_sheet->getStyle($range)->applyFromArray($styleArray);
                $active_sheet->setCellValue($letter1, count($arr_discipline) + 1);

                $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '12';
                $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '12';
                $range = $letter1 . ":" . $letter2;
                $active_sheet->mergeCells($range);
                $active_sheet->getStyle($range)->applyFromArray($styleArray);
                $active_sheet->setCellValue($letter1, $result[$i]->SubjectCode . " " . $result[$i]->SubjectNameRU);
                $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '14';
                $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '14';
                $range = $letter1 . ":" . $letter2;
                $active_sheet->mergeCells($range);
                $active_sheet->getStyle($range)->applyFromArray($styleArray);
                $active_sheet->setCellValue($letter1, $result[$i]->credits);
                $count_credit = $count_credit + $result[$i]->credits;

                $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '16';
                $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
                $active_sheet->setCellValue($letter1, '%');

                $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3 + 1) . '16';
                $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
                $active_sheet->setCellValue($letter1, 'ә/б');

                $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '16';
                $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
                $active_sheet->setCellValue($letter1, 'Б');

                $arr_discipline[] = $subjectid;
            }

            $item_student = array_search($studentid,$arr_student);
            if($item_student === FALSE){
                $arr_student[] = $studentid;
                $arr_gpa[] = 0;
                $arr_credit[] = 0;
                $var = $row_start+count($arr_student);
                $letter1 = "B" . $var;
                $active_sheet->insertNewRowBefore($var, 1);
                $active_sheet->getStyle($letter1)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                );
                $fio = $result[$i]->lastname." ".$result[$i]->firstname." ".$result[$i]->patronymic;
                $active_sheet->setCellValue($letter1, $fio);
                $active_sheet->setCellValue('C'.$var, $result[$i]->gnt);
                $active_sheet->setCellValue('A'.$var, count($arr_student));

            }
            $item_student = array_search($studentid,$arr_student);
            $item_discipline = array_search($subjectid,$arr_discipline);
            $arr_gpa[$item_student] = $arr_gpa[$item_student]+(str_replace(",",".",$this->total_mark($result[$i]->totalmark,2))*$result[$i]->credits);
            $arr_credit[$item_student] = $arr_credit[$item_student]+$result[$i]->credits;

            $v = $row_start+$item_student+1;
            $percent = $this->getLetter($col_start + $item_discipline*3).$v;
            $active_sheet->getStyle($percent)->applyFromArray($styleArray);
            $active_sheet->setCellValue($percent, round($result[$i]->totalmark));

            $alpha = $this->getLetter($col_start + $item_discipline*3+1).$v;
            $active_sheet->getStyle($alpha)->applyFromArray($styleArray);
            $active_sheet->setCellValue($alpha, $this->total_mark($result[$i]->totalmark,1));

            $ball = $this->getLetter($col_start + $item_discipline*3+2).$v;
            $active_sheet->getStyle($ball)->applyFromArray($styleArray);
            $active_sheet->setCellValue($ball, $this->total_mark($result[$i]->totalmark,2));
        }
        ///
        if($id_term == 2){
            $arr = [];
            for($k=0; $k<count($result2); $k++){
                if(array_key_exists($result2[$k]->studentid, $arr)){
                    $arr[$result2[$k]->studentid]['all_credit'] += (int) $result2[$k]->credits;
                    $arr[$result2[$k]->studentid]['all_gpa']    += (str_replace(",",".",$this->total_mark($result2[$k]->totalmark,2))*$result2[$k]->credits);
                }else{
                    $arr[$result2[$k]->studentid]['all_credit'] = (int) $result2[$k]->credits;
                    $arr[$result2[$k]->studentid]['all_gpa']    = (str_replace(",",".",$this->total_mark($result2[$k]->totalmark,2))*$result2[$k]->credits);
                }
            }
        }


        ///
        $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3-1) . '10';
        $active_sheet->mergeCells('D10:'.$letter1);
        $active_sheet->getStyle('D10')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $active_sheet->getStyle('D10:'.$letter1)->applyFromArray($styleArray);
        $active_sheet->setCellValue('D10', 'Пәндер/Дисциплины');

        $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3-1) . '13';
        $active_sheet->mergeCells('D13:'.$letter1);
        $active_sheet->getStyle('D13')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $active_sheet->getStyle('D13:'.$letter1)->applyFromArray($styleArray);
        $active_sheet->setCellValue('D13', 'Кредиттер саны/Количество кредитов');

        $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3-1) . '15';
        $active_sheet->mergeCells('D15:'.$letter1);
        $active_sheet->getStyle('D15')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $active_sheet->getStyle('D15:'.$letter1)->applyFromArray($styleArray);
        $active_sheet->setCellValue('D15', 'Баға/Оценка');

        $methodist = 'P' . ($row_start+count($arr_student)+4);
        $active_sheet->setCellValue($methodist, $_SESSION['last_first']);

        // Всего кредитов

        $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '10';
        $letter3 = $this->getLetter($col_start + count($arr_discipline) * 3);
        $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3) . '16';
        for($j=10; $j<16; $j++){
            $active_sheet->getStyle($letter3.$j)->applyFromArray($styleArray);
        }
        $active_sheet->mergeCells($letter1.':'.$letter2);
        $active_sheet->getStyle($letter1)->getAlignment()->setVertical(
            PHPExcel_Style_Alignment::VERTICAL_CENTER
        );
        $active_sheet->getStyle($letter1)->getAlignment()->setTextRotation(90);
        $active_sheet->getStyle($letter1)->getAlignment()->setWrapText(true);
        $active_sheet->getColumnDimension($letter3)->setWidth(6);

        $active_sheet->setCellValue($letter1, 'Барлық кредиттер саны / Всего кредитов');
        for($i=0; $i<count($arr_student); $i++){
            $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3).($row_start + $i+1);
            $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
            $active_sheet->getStyle($letter1)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            if($id_term == 1){
                $active_sheet->setCellValue($letter1, $arr_credit[$i]);
            }else{
                $active_sheet->setCellValue($letter1, $arr[$arr_student[$i]]['all_credit']);
            }
        }

        //

        // GPA
        $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3+1) . '10';
        $letter3 = $this->getLetter($col_start + count($arr_discipline) * 3+1);
        $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3+1) . '16';
        for($j=10; $j<17; $j++){
            $active_sheet->getStyle($letter3.$j)->applyFromArray($styleArray);
        }
        $active_sheet->mergeCells($letter1 . ':' . $letter2);
        $active_sheet->getStyle($letter1)->getAlignment()->setVertical(
            PHPExcel_Style_Alignment::VERTICAL_CENTER
        );
        $active_sheet->getStyle($letter1)->getAlignment()->setTextRotation(90);
        $active_sheet->getStyle($letter1)->getAlignment()->setWrapText(true);
        $active_sheet->getColumnDimension($letter3)->setWidth(6);

        $active_sheet->getStyle($letter1)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );

        $active_sheet->setCellValue($letter1, 'GPA');
        $active_sheet->getStyle($letter1)->applyFromArray($styleArray);

        for($i=0; $i<count($arr_student); $i++){
            $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3+1).($row_start + $i+1);
            $active_sheet->getStyle($letter1)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->getStyle($letter1)->applyFromArray($styleArray);

            if($id_term == 1){
                $active_sheet->setCellValue($letter1,round($arr_gpa[$i]/$arr_credit[$i],2));
            }else{
                $active_sheet->setCellValue($letter1,round($arr[$arr_student[$i]]['all_gpa']/$arr[$arr_student[$i]]['all_credit'],2));
            }
        }

        // Нарисовать бардеры
        for($i=0; $i<count($arr_student); $i++){
            for($j=0; $j<count($arr_discipline); $j++){
                $end = $row_start+$i+1;
                $letter1 = $this->getLetter($col_start + $j * 3) . $end;
                $letter2 = $this->getLetter($col_start + $j * 3+1) . $end;
                $letter3 = $this->getLetter($col_start + $j * 3+2) .  $end;
                $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
                $active_sheet->getStyle($letter2)->applyFromArray($styleArray);
                $active_sheet->getStyle($letter3)->applyFromArray($styleArray);
            }
        }

        $filename = 'сводная_'.date("d.m.Y H:i:s");
        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=$filename.xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    public function total_mark($total, $param = 1){
        $total = (int) round($total);
        $mark = [
            0 => [ 'letter' => 'F', 'ball' => '0,00', 'trade' => 2 ],
            50 => [ 'letter' => 'D', 'ball' => '1,00', 'trade' => 3 ],
            51 => [ 'letter' => 'D', 'ball' => '1,00', 'trade' => 3 ],
            52 => [ 'letter' => 'D', 'ball' => '1,00', 'trade' => 3 ],
            53 => [ 'letter' => 'D', 'ball' => '1,00', 'trade' => 3 ],
            54 => [ 'letter' => 'D', 'ball' => '1,00', 'trade' => 3 ],
            55 => [ 'letter' => 'D+', 'ball' => '1,33', 'trade' => 3 ],
            56 => [ 'letter' => 'D+', 'ball' => '1,33', 'trade' => 3 ],
            57 => [ 'letter' => 'D+', 'ball' => '1,33', 'trade' => 3 ],
            58 => [ 'letter' => 'D+', 'ball' => '1,33', 'trade' => 3 ],
            59 => [ 'letter' => 'D+', 'ball' => '1,33', 'trade' => 3 ],
            60 => [ 'letter' => 'C-', 'ball' => '1,67', 'trade' => 3 ],
            61 => [ 'letter' => 'C-', 'ball' => '1,67', 'trade' => 3 ],
            62 => [ 'letter' => 'C-', 'ball' => '1,67', 'trade' => 3 ],
            63 => [ 'letter' => 'C-', 'ball' => '1,67', 'trade' => 3 ],
            64 => [ 'letter' => 'C-', 'ball' => '1,67', 'trade' => 3 ],
            65 => [ 'letter' => 'C',  'ball' => '2,00', 'trade' => 3 ],
            66 => [ 'letter' => 'C',  'ball' => '2,00', 'trade' => 3 ],
            67 => [ 'letter' => 'C',  'ball' => '2,00', 'trade' => 3 ],
            68 => [ 'letter' => 'C',  'ball' => '2,00', 'trade' => 3 ],
            69 => [ 'letter' => 'C',  'ball' => '2,00', 'trade' => 3 ],
            70 => [ 'letter' => 'C+', 'ball' => '2,33', 'trade' => 3 ],
            71 => [ 'letter' => 'C+', 'ball' => '2,33', 'trade' => 3 ],
            72 => [ 'letter' => 'C+', 'ball' => '2,33', 'trade' => 3 ],
            73 => [ 'letter' => 'C+', 'ball' => '2,33', 'trade' => 3 ],
            74 => [ 'letter' => 'C+', 'ball' => '2,33', 'trade' => 3 ],
            75 => [ 'letter' => 'B-', 'ball' => '2,67', 'trade' => 4 ],
            76 => [ 'letter' => 'B-', 'ball' => '2,67', 'trade' => 4 ],
            77 => [ 'letter' => 'B-', 'ball' => '2,67', 'trade' => 4 ],
            78 => [ 'letter' => 'B-', 'ball' => '2,67', 'trade' => 4 ],
            79 => [ 'letter' => 'B-', 'ball' => '2,67', 'trade' => 4 ],
            80 => [ 'letter' => 'B', 'ball' => '3,00', 'trade' => 4 ],
            81 => [ 'letter' => 'B', 'ball' => '3,00', 'trade' => 4 ],
            82 => [ 'letter' => 'B', 'ball' => '3,00', 'trade' => 4 ],
            83 => [ 'letter' => 'B', 'ball' => '3,00', 'trade' => 4 ],
            84 => [ 'letter' => 'B', 'ball' => '3,00', 'trade' => 4 ],
            85 => [ 'letter' => 'B+', 'ball' => '3,33', 'trade' => 4 ],
            86 => [ 'letter' => 'B+', 'ball' => '3,33', 'trade' => 4 ],
            87 => [ 'letter' => 'B+', 'ball' => '3,33', 'trade' => 4 ],
            88 => [ 'letter' => 'B+', 'ball' => '3,33', 'trade' => 4 ],
            89 => [ 'letter' => 'B+', 'ball' => '3,33', 'trade' => 4 ],
            90 => [ 'letter' => 'A-', 'ball' => '3,67', 'trade' => 5 ],
            91 => [ 'letter' => 'A-', 'ball' => '3,67', 'trade' => 5 ],
            92 => [ 'letter' => 'A-', 'ball' => '3,67', 'trade' => 5 ],
            93 => [ 'letter' => 'A-', 'ball' => '3,67', 'trade' => 5 ],
            94 => [ 'letter' => 'A-', 'ball' => '3,67', 'trade' => 5 ],
            95 => [ 'letter' => 'A', 'ball' => '4,00', 'trade' => 5 ],
            96 => [ 'letter' => 'A', 'ball' => '4,00', 'trade' => 5 ],
            97 => [ 'letter' => 'A', 'ball' => '4,00', 'trade' => 5 ],
            98 => [ 'letter' => 'A', 'ball' => '4,00', 'trade' => 5 ],
            99 => [ 'letter' => 'A', 'ball' => '4,00', 'trade' => 5 ],
            100 => [ 'letter' => 'A', 'ball' => '4,00', 'trade' => 5 ]
        ];
        if(array_key_exists($total,$mark)){
            switch ($param){
                case 1:
                    return $mark[$total]['letter'];
                    break;

                case 2:
                    return $mark[$total]['ball'];
                    break;

                case 3:
                    return $mark[$total]['trade'];
                    break;
            }
        }else{
            switch ($param){
                case 1:
                    return $mark[0]['letter'];
                    break;

                case 2:
                    return $mark[0]['ball'];
                    break;

                case 3:
                    return $mark[0]['trade'];
                    break;
            }
        }
    }

    // Определение форму обучение
    public function get_study_form($id,$param){
        $arr = [
            1 => [
                'form' => 'очная', 'period' => 4
            ],
            3 => [
                'form' => 'очная', 'period' => 3
            ],
            4 => [
                'form' => 'очная', 'period' => 5
            ],
            6 => [
                'form' => 'заочная', 'period' => 3
            ],
            7 => [
                'form' => 'заочная', 'period' => 2
            ],
            9 => [
                'form' => 'очная', 'period' => 1
            ],
            10 => [
                'form' => 'очная', 'period' => 2
            ],
            12 => [
                'form' => 'очная', 'period' => '1,5'
            ],
            13 => [
                'form' => 'очная', 'period' => 3
            ],
        ];
        if(array_key_exists($id, $arr)){
            switch ($param){
                case 1:
                    return $arr[$id]['form'];
                    break;
                case 2:
                    return $arr[$id]['period'];
                    break;
            }
        }
    }

    // Определение год поступление по курсам
    public function get_year($course){
        $course = (int) $course;
        $current_month = date("m");
        $current_year  = date("Y");
        switch ($course){
            case 1:
                if($current_month != '09'){
                    $god = (int)$current_year - 1;
                    return $god;
                }else{
                    dd("Еще не время для сводного ведомоста");
                }
                break;
            case 2:
                if($current_month != '09'){
                    $god = (int) $current_year - 1;
                    return $god;
                }else{
                    dd("Еще не время для сводного ведомоста");
                }
                break;
            case 3:
                if($current_month != '09'){
                    $god = (int)$current_year - 2;
                    return $god;
                }else{
                    dd("Еще не время для сводного ведомоста");
                }
                break;
            case 4:
                if($current_month != '09'){
                    $god = (int)$current_year - 4;
                    return $god;
                }else{
                    dd("Еще не время для сводного ведомоста");
                }
                break;
            case 5:
                if($current_month != '09'){
                    $god = (int)$current_year - 5;
                    return $god;
                }else{
                    dd("Еще не время для сводного ведомоста");
                }
                break;
        }
    }

    public function getLetter($number){
        $arr = [
            1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',
            11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',
            20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z',27 => 'AA',28 => 'AB',
            29 => 'AC',30 => 'AD',31 => 'AE',32 => 'AF',33 => 'AG',34 => 'AH',35 => 'AI',36 => 'AJ',
            37 => 'AK',38 => 'AL',39 => 'AM',40 => 'AN',41 => 'AO',42 => 'AP',43 => 'AQ',44 => 'AR',
            45 => 'AS',46 => 'AT',47 => 'AU',48 => 'AV',49 => 'AW',50 => 'AX',51 => 'AY',52 => 'AZ',
            53 => 'BA',54 => 'BB',55 => 'BC',56 => 'BD',57 => 'BE',58 => 'BF',59 => 'BG',60 => 'BH',
            61 => 'BI',62 => 'BJ',63 => 'BK',64 => 'BL',65 => 'BM',66 => 'BN',67 => 'BO',68 => 'BP',
            69 => 'BQ',70 => 'BR',71 => 'BS',72 => 'BT',73 => 'BU',74 => 'BV',75 => 'BW',76 => 'BX',
            77 => 'BY',78 => 'BZ',79 => 'CA',80 => 'CB',81 => 'CC',82 => 'CD',83 => 'CE',84 => 'CF',
            85 => 'CG',86 => 'CH',87 => 'CI',88 => 'CJ',89 => 'CK',90 => 'CL',91 => 'CM',92 => 'CN',
            93 => 'CO',94 => 'CP',95 => 'CQ',96 => 'CR',97 => 'CS',98 => 'CT',99 => 'CU',100 => 'CV',
            101 => 'CW',102 => 'CY',103 => 'CZ'
        ];
        if(array_key_exists($number,$arr)){
            return $arr[$number];
        }
    }
}

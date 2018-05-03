<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Subject;
use PHPExcel;
use PHPExcel_IOFactory;
use App\Group;
use App\Http\Requests;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Style_Fill;
use App\TutorStream;
use App\Profession;
use App\Studyform;
use App\Studylanguage;
use Prophecy\Call\Call;

class IndexController extends Controller
{
    protected $user_id; // Ид пользователя который вошел в систему
    protected $id_subject; // Ид предмета
    protected $id_profession; // Ид специальноста
    protected $id_specialization; // Ид специализации
    protected $id_group;
    protected $id_stream; // поток
    protected $id_report; // отчет
    protected $id_course; // курс
    protected $id_term; // семестр
    protected $date_time; // время
    protected $students; // массив где храниться информации о студента
    protected $profession; // наименование специальноста
    protected $language; // язык обучение
    protected $study_form; // форма обучение
    protected $subject; // наименование предмета
    protected $teacher; // ФИО преподавателя
    protected $credit; // кредит
    protected $university; // Название института
    protected $director; // ФИО институт директора
    protected $id_study_form; // Ид форма обучения
    protected $date_time1; // время
    protected $date_time2; // время
    protected $tutor_ct; // ФИО сотрудника Центр тестирование
    protected $id_study_lang; // Язык обучение
    protected $id_student; // Ид студента
    protected $id_status; // Статус студента
    protected $middle_ball; // Средний балл успеваемости
    protected $type; // Тип ведомость
    protected $trans_type; // Тип транскрипта
    protected $all_credit; // зарегистрированные кредиты
    protected $all_credit_ects; // зарегистрированные ects кредиты
    protected $all_gpa;
    protected $all_zach_credit; // зачтенные кредиты
    protected $all_zach_credit_ects; // зачтенные кредиты ects
    protected $credit_nir;
    protected $credit_nir_ects;
    protected $id_lang; // язык транскрипт
    protected $type_summary;
    protected $fio_teacher;
	protected $group_summary;


    public function welcome(){
        $ins = DB::table('faculties')->get();
        $study_forms = DB::table('studyforms')->get();
        $study_lang = DB::table('studylanguages')->get();
        return view('welcome', compact('ins','study_forms','study_lang'));
    }

    public function institute($id){
        $users = DB::select(DB::raw("SELECT cafedraID,cafedraNameRU FROM cafedras WHERE FacultyID=$id"));
        $users = json_encode($users);
        return $users;
    }

    public function chair($id){
        $users = DB::table('professions')
                    ->select(DB::raw('professionID, CONCAT_WS("-",professionCode,professionNameRU) AS professionName'))
                    ->whereIn('professionID', function($query) use ($id){
                        $query->select(DB::raw('professionID'))
                              ->from('profession_cafedra')
                              ->where(['cafedraID' => $id])
                              ->whereNull('deleted');
                    })
                    ->where(['deleted' => 0])
                    ->get();
        $users = json_encode($users);
        return $users;
    }

    public function profession($pid,$sid,$cid,$tid,$year){
        $tup = DB::select(DB::raw("SELECT CurriculumID FROM typcurriculums WHERE ProfessionID=$pid AND studyformID=$sid AND YEAR(StartDate)=$year"));
        $id_tup = $tup[0]->CurriculumID;
        $sub = DB::select(DB::raw("SELECT SubjectID FROM tupsubjects WHERE CurriculumID=$id_tup AND Term=$tid AND Year=$cid"));
        $list_subject = array();
        for($i=0; $i<count($sub); $i++){
            $subject = Subject::where(['SubjectID' => $sub[$i]->SubjectID])->get();
            $list_subject[$i]['id'] = $subject[0]->SubjectID;
            $list_subject[$i]['name'] = $subject[0]->SubjectNameRU;
            $list_subject[$i]['code'] = $subject[0]->SubjectCodeRu;
        }
        return json_encode($list_subject);
    }

    public function group($pid){
        $sid = DB::select(DB::raw("SELECT id FROM profession_cafedra WHERE professionID=$pid AND deleted IS NULL"));
        $sid = $sid[0]->id;
        $groups = DB::select(DB::raw("SELECT groupID,name FROM groups WHERE specializationID=$sid AND deleted IS NULL"));

        $list_groups = array();
        for($i=0; $i<count($groups); $i++){
            $group = Group::where(['groupID' => $groups[$i]->groupID])->get();
            $list_groups[$i]['id'] = $group[0]->groupID;
            $list_groups[$i]['name'] = $group[0]->name;
        }
        return json_encode($list_groups);
    }

    
    public function study($id){
        $result = DB::select("SELECT StudyFormID AS s,CourseNumber AS c,F.nameru AS n 
             FROM (SELECT * FROM (SELECT DISTINCTROW ProfessionID,StudyFormID,CourseNumber,
                          CONCAT(CAST(ProfessionID AS CHAR(10)),'_',CAST(StudyFormID AS char(10)),'_',CAST(CourseNumber AS char(10))) AS D 
                      FROM nitro.students WHERE isStudent=1) vyd
                           WHERE D NOT IN (SELECT CONCAT(CAST(id_profession AS CHAR(10)),'_',CAST(id_study_form AS char(10)),'_',CAST(id_course AS char(10))) AS D
                                                                         FROM phelper.contingent)) DISTINCT_VYD
                      LEFT JOIN studyforms F ON F.id=DISTINCT_VYD.StudyFormID                                                 
            WHERE DISTINCT_VYD.ProfessionID='$id' ORDER BY StudyFormID,CourseNumber");
        $cont = json_encode($result);
        return $cont;
    }

    public function tutor_contingent($id){
        $result = DB::select("SELECT 
                                PC.id as id,PC.id_profession as pid,PC.id_study_form as psid,CONCAT_WS('-',NP.professionCode,NP.professionNameRU) AS pname,NS.NameRu AS sname,PC.id_course AS course,
                                (SELECT COUNT(*) FROM nitro.students WHERE isStudent=1 AND ProfessionID=pid AND StudyFormID=psid AND CourseNumber=course) as count
                                FROM phelper.contingent PC
                                LEFT JOIN nitro.professions NP ON NP.professionID=PC.id_profession
                                LEFT JOIN nitro.studyforms NS ON NS.Id=PC.id_study_form
                                WHERE PC.id_tutor=$id ORDER BY NP.professionNameRU");
        return json_encode($result);
    }

    public function statement(Request $request){
        $this->user_id = $_SESSION['id_tutor'];
        $this->id_subject = (int) $request->input('id_subject');
        $this->id_profession = (int) $request->input('id_profession');
        $this->id_specialization = (int) $request->input('id_specialization');
        $this->id_stream = (int) $request->input('id_stream');
        $this->id_report = (int) $request->input('id_report');
        $this->id_course = (int) $request->input('id_course');
        $this->id_term = (int) $request->input('id_term');
        
        $this->type = (int) $request->input('type');
        $this->type_summary = (int) $request->input('type_summary');
        $this->fio_teacher = $request->input('fio_teacher');
        $this->id_study_lang = (int) $request->input('id_study_lang');
        $this->id_study_form = (int) $request->input('id_study_form');
        $this->date_time = ($request->input('date_time')) ? $request->input('date_time') : date("d.m.Y");
        $this->date_time1 = ($request->input('date_time1')) ? $request->input('date_time1') : date("d.m.Y");
        $this->date_time2 = ($request->input('date_time2')) ? $request->input('date_time2') : date("d.m.Y");
		
		if($request->input('with_group') == 'on'){
			$gr = '';
			$this->group_summary = '';
		}else{
			$this->id_group = (int) $request->input('id_group');
			$gr = "AND PT.id_group=".$this->id_group;
			$this->group_summary = "AND ST.groupID=".$this->id_group;;
		}
		
        if($this->id_stream){
            if($this->id_profession == 14 AND $this->id_study_form == 7 OR $this->id_study_form == 6){
                $this->students = DB::select("SELECT PT.studentid,PT.fio_student,PT.studygroupid,PT.studygroupid_p, 
                                (CASE WHEN NJ1.Mark IS NULL THEN NNJ1.Mark ELSE NJ1.Mark END) AS rk1,
                                (CASE WHEN NJ2.Mark IS NULL THEN NNJ2.Mark ELSE NJ2.Mark END) AS rk2,
                                 PT.id_calendar, PT.id_group,PT.queryid,PT.group_title
                                FROM phelper.temp PT
                    LEFT JOIN nitro.journal NJ1 ON  NJ1.markTypeID=2 AND NJ1.StudentID=PT.studentid AND  NJ1.StudyGroupID=PT.studygroupid AND NJ1.number=1 AND NJ1.isCurrent=1
                    LEFT JOIN nitro.journal NNJ1 ON  NNJ1.markTypeID=2 AND NNJ1.StudentID=PT.studentid AND NNJ1.StudyGroupID=PT.studygroupid_p AND NNJ1.number=1 AND NNJ1.isCurrent=1
                    LEFT JOIN nitro.journal NJ2 ON  NJ2.markTypeID=2 AND NJ2.StudentID=PT.studentid AND NJ2.StudyGroupID=PT.studygroupid AND NJ2.number=2 AND NJ2.isCurrent=1
                    LEFT JOIN nitro.journal NNJ2 ON  NNJ2.markTypeID=2 AND NNJ2.StudentID=PT.studentid AND NNJ2.StudyGroupID=PT.studygroupid_p AND NNJ2.number=2 AND NNJ2.isCurrent=1
                    WHERE PT.id='$this->user_id'  AND PT.studygroupid='$this->id_stream' $gr ORDER BY StudyGroupID,PT.fio_student");
            }else{
                $this->students = DB::select("SELECT PT.studentid,PT.fio_student,PT.studygroupid,PT.studygroupid_p, 
                                (CASE WHEN NJ1.Mark IS NULL THEN NNJ1.Mark ELSE NJ1.Mark END) AS rk1,
                                (CASE WHEN NJ2.Mark IS NULL THEN NNJ2.Mark ELSE NJ2.Mark END) AS rk2,
                                 PT.id_calendar, PT.id_group,PT.queryid,PT.group_title
                                FROM phelper.temp PT
                    LEFT JOIN nitro.journal NJ1 ON  NJ1.markTypeID=2 AND NJ1.StudentID=PT.studentid AND  NJ1.StudyGroupID=PT.studygroupid AND NJ1.number=1 AND NJ1.isCurrent=1
                    LEFT JOIN nitro.journal NNJ1 ON  NNJ1.markTypeID=2 AND NNJ1.StudentID=PT.studentid AND NNJ1.StudyGroupID=PT.studygroupid_p AND NNJ1.number=1 AND NNJ1.isCurrent=1
                    LEFT JOIN nitro.journal NJ2 ON  NJ2.markTypeID=2 AND NJ2.StudentID=PT.studentid AND NJ2.StudyGroupID=PT.studygroupid AND NJ2.number=2 AND NJ2.isCurrent=1
                    LEFT JOIN nitro.journal NNJ2 ON  NNJ2.markTypeID=2 AND NNJ2.StudentID=PT.studentid AND NNJ2.StudyGroupID=PT.studygroupid_p AND NNJ2.number=2 AND NNJ2.isCurrent=1
                    WHERE PT.id='$this->user_id'  AND PT.studygroupid='$this->id_stream' AND PT.id_spec='$this->id_specialization' $gr ORDER BY StudyGroupID,PT.fio_student");
            }


            $result = DB::select("SELECT pname,lang,form,subjectnameru,year,term,fio_tutor,creditcount FROM phelper.temp PT WHERE PT.studygroupid='$this->id_stream' AND PT.id='$this->user_id' LIMIT 1");
            $this->profession = $result[0]->pname;
            $this->language = $result[0]->lang;
            $this->study_form = $result[0]->form;
            $this->subject = $result[0]->subjectnameru;
            $this->teacher = $result[0]->fio_tutor;
            $this->credit = $result[0]->creditcount;
        }

        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$this->id_profession' AND PC.deleted IS NULL)");

        $this->university = $result1[0]->facultyNameKZ." / ".$result1[0]->facultyNameRU;
        $id_director = $result1[0]->facultyDean;
        $result2 = DB::select("SELECT * FROM tutors WHERE TutorID='$id_director'");
        if($this->id_specialization == 215){
            $this->director = 'Таубаева Г.З.';
        }else{
            $this->director = $result2[0]->lastname." ".substr($result2[0]->firstname,0,2).".".substr($result2[0]->patronymic,0,2).".";
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
        $this->tutor_ct = $result3[0]->tutor;

        switch ($this->id_report){
            case 1:
                $this->report1();
                break;
            case 2:
                $this->report2();
                break;
            case 3:
                $this->report3();
                break;
            case 4:
                $this->report4();
                break;
            case 5:
                $branch = (int) $request->input('branch');
                $this->report5($branch);
                break;
            case 6:
                $id_student = (int) $request->input('id_student');
                $this->report6($id_student);
                break;
            case 7:
                $id_student = (int) $request->input('id_student');
                $this->report7($id_student);
                break;
            case 8:
                $id_summary = $request->input('id_summary');
                $start_date = $request->input('start_date');
                $this->report8($id_summary,$start_date);

                break;
            case 9:
                $this->report9();
                break;
            case 10:
                $this->report10();
                break;
            case 11:
                $id_student = (int) $request->input('id_student');
                $this->report11($id_student);
                break;
            case 12:
                $this->report12();
                break;
        }
    }
    
    public function report1(){
        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            // заочная
            $statement = "РЕЙТИНГ ЖӘНЕ АРАЛЫҚ АТТЕСТАЦИЯ ВЕДОМОСЫ /                                                                                 ВЕДОМОСТЬ РЕЙТИНГА И ПРОМЕЖУТОЧНОЙ АТТЕСТАЦИИ № ".$this->id_stream;
            $filename = base_path() . "/public/reports/1_z.xls";
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objReader->setReadDataOnly(false);
            $objPHPExcel = $objReader->load($filename);
            $objPHPExcel->setActiveSheetIndex(0);
            $active_sheet = $objPHPExcel->getActiveSheet();

            $active_sheet->setCellValue('B3',$this->university);
            $active_sheet->setCellValue('B5',$statement);
            if(mb_strlen($this->profession) > 41){
                $active_sheet->getRowDimension('8')->setRowHeight(35);
                $active_sheet->setCellValue('D8',$this->profession);
            }else{
                $active_sheet->setCellValue('D8',$this->profession);
            }
            $active_sheet->setCellValue('D9',$this->language);
            $active_sheet->setCellValue('D10',$this->study_form);
            $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
            $active_sheet->setCellValue('D11',$this->subject);

            $active_sheet->setCellValue('H8',"Курс: ".$this->id_course);
            $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
            $active_sheet->setCellValue('D12',$this->teacher);
            $active_sheet->setCellValue('D13',$this->credit);

            $row_start = 16;
            $current_row = $row_start;

            for($i=0; $i<count($this->students); $i++){
                $j = $i+1;
                $current_row++;
                //$fio = $this->students[$i]->fio_student;
                $id_student = $this->students[$i]->studentid;
                $query_id = $this->students[$i]->queryid;
                //$group_name = $this->students[$i]->group_title;
                $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
                $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
                $active_sheet->setCellValue('C'.$current_row,$this->students[$i]->fio_student);
                $active_sheet->setCellValue('B'.$current_row,$j);
                $active_sheet->setCellValue('E'.$current_row,$this->students[$i]->rk1);
                $exammark = DB::select("SELECT NT.ap_exammark AS mark, NT.ap_totalmark as total FROM nitro.totalmarks NT WHERE NT.queryID='$query_id' AND NT.studentID='$id_student'");
                if($exammark){
                    $active_sheet->setCellValue('G'.$current_row,$exammark[0]->mark);
                }
                $date = "Р-1: ".$this->date_time1.",       ПА: ".$this->date_time2;
                $active_sheet->setCellValue('D14',$date);
                $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->group_title);
                $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                );
                $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
            }
            $methodist = $current_row+5;
            $d = $current_row+7;
			if(!empty($this->fio_teacher)){
				$active_sheet->setCellValue('G'.$methodist,$this->fio_teacher);
			}else{
				$active_sheet->setCellValue('G'.$methodist,$_SESSION['last_first']);
			}
            $active_sheet->setCellValue('G'.$d,$this->director);

            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=Ведомость рейтинга №" .$this->id_stream . ".xls");
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save("php://output");
            exit();
        }else{
            // очная
            // Ведомость Р1 и Р2
            $statement = "РЕЙТИНГ ВЕДОМОСЫ / ВЕДОМОСТЬ РЕЙТИНГА № ".$this->id_stream;
            if($this->type == 3){
                $filename = base_path() . "/public/reports/1_let.xls";
            }else{
                $filename = base_path() . "/public/reports/1.xls";
            }

            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $objReader->setReadDataOnly(false);
            $objPHPExcel = $objReader->load($filename);
            $objPHPExcel->setActiveSheetIndex(0);
            $active_sheet = $objPHPExcel->getActiveSheet();

            $active_sheet->setCellValue('B3',$this->university);
            $active_sheet->setCellValue('B5',$statement);
            if(mb_strlen($this->profession) > 41){
                $active_sheet->getRowDimension('8')->setRowHeight(35);
                $active_sheet->setCellValue('D8',$this->profession);
            }else{
                $active_sheet->setCellValue('D8',$this->profession);
            }
            $active_sheet->setCellValue('D9',$this->language);
            $active_sheet->setCellValue('D10',$this->study_form);
            $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
            $active_sheet->setCellValue('D11',$this->subject);

            $active_sheet->setCellValue('H8',"Курс: ".$this->id_course);
            $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
            $active_sheet->setCellValue('D12',$this->teacher);
            $active_sheet->setCellValue('D13',$this->credit);

            $row_start = 16;
            $current_row = $row_start;

            for($i=0; $i<count($this->students); $i++){
                $j = $i+1;
                $current_row++;
                $fio = $this->students[$i]->fio_student;
                $id_student = $this->students[$i]->studentid;
                $id_calendar = $this->students[$i]->id_calendar;

                $sql_rk1_start_date = DB::select("SELECT SP.StartDate AS d FROM studyperiods SP
                               WHERE SP.TermID =(SELECT T.TermID FROM terms T WHERE T.StudyCalendarID='$id_calendar' AND T.TermTypeID=1 AND T.TermNumber='$this->id_term') 
                               AND SP.number=1");
                $sql_rk2_start_date = DB::select("SELECT SP.StartDate AS d FROM studyperiods SP
                               WHERE SP.TermID =(SELECT T.TermID FROM terms T WHERE T.StudyCalendarID='$id_calendar' AND T.TermTypeID=1 AND T.TermNumber='$this->id_term') 
                               AND SP.number=2");

                $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
                $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
                $active_sheet->setCellValue('C'.$current_row,$fio);
                $active_sheet->setCellValue('B'.$current_row,$j);

                $active_sheet->setCellValue('E'.$current_row,$this->students[$i]->rk1);
                $active_sheet->setCellValue('G'.$current_row,$this->students[$i]->rk2);

                if($sql_rk1_start_date AND $sql_rk2_start_date){
                    $rk1_start_date = date("d.m.Y",strtotime($sql_rk1_start_date[0]->d));
                    $rk2_start_date = date("d.m.Y",strtotime($sql_rk2_start_date[0]->d));
                    $date = "Р-1: ".$rk1_start_date.",       Р-2: ".$rk2_start_date;
                    $active_sheet->setCellValue('D14',$date);
                }

                $id_group = $this->students[$i]->id_group;
                $group = DB::select(DB::raw("SELECT name FROM groups WHERE groupID='$id_group'"));
                $active_sheet->setCellValue('D'.$current_row,$group[0]->name);
                $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                );
                $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
            }
            $methodist = $current_row+5;
            $d = $current_row+7;
            $active_sheet->setCellValue('G'.$methodist,$_SESSION['last_first']);
            $active_sheet->setCellValue('G'.$d,$this->director);

            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=Ведомость рейтинга №" .$this->id_stream . ".xls");
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save("php://output");
            exit();
        }
    }

    public function report2(){
        # Явочный лист
        $filename = base_path() . "/public/reports/2.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $title = "КЕЛУ ПАРАҒЫ / ЯВОЧНЫЙ ЛИСТ № ".$this->id_stream;
        $active_sheet->setCellValue('B3',$this->university);
        $active_sheet->setCellValue('B5',$title);
        if(mb_strlen($this->profession) > 41){
            $active_sheet->getRowDimension('8')->setRowHeight(35);
            $active_sheet->setCellValue('D8',$this->profession);
        }else{
            $active_sheet->setCellValue('D8',$this->profession);
        }
        $active_sheet->setCellValue('D9',$this->language);
        $active_sheet->setCellValue('D10',$this->study_form);
        $active_sheet->setCellValue('D14',$this->date_time);
        $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
        $active_sheet->setCellValue('D11',$this->subject);

        $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
        $active_sheet->setCellValue('H8',"Курс: ".$this->id_course);
        if(!empty($this->fio_teacher)){
            $active_sheet->setCellValue('D12',$this->fio_teacher);
        }else{
            $active_sheet->setCellValue('D12',$this->teacher);
        }
        $active_sheet->setCellValue('D13',$this->credit);

        $row_start = 16;
        $current_row = $row_start;
        for($i=0; $i<count($this->students); $i++){
            $j = $i+1;
            $current_row++;
            //$fio = $this->students[$i]->fio_student;
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
            $active_sheet->setCellValue('C'.$current_row,$this->students[$i]->fio_student);
            $active_sheet->setCellValue('B'.$current_row,$j);

            $middle_rk = ($this->students[$i]->rk1+$this->students[$i]->rk2) / 2;

            if($middle_rk < 50){
                $middle_rk = $middle_rk." (не доп)";
            }
            $active_sheet->mergeCells('E'.$current_row.':F'.$current_row);
            $active_sheet->mergeCells('G'.$current_row.':H'.$current_row);
            $active_sheet->setCellValue('E'.$current_row,$middle_rk);
            //$id_group = $this->students[$i]->id_group;
            //$group = DB::select(DB::raw("SELECT name FROM groups WHERE groupID='$id_group'"));
            $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->group_title);
            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
        }
        $methodist = $current_row+5;
        $active_sheet->setCellValue('G'.$methodist,$_SESSION['last_first']);

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Явочный лист №" .$this->id_stream . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }
    # Явочный лист-тест
    public function report3(){
        $filename = base_path() . "/public/reports/3.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $title = "КЕЛУ ПАРАҒЫ / ЯВОЧНЫЙ ЛИСТ № ".$this->id_stream;
        $active_sheet->setCellValue('B3',$this->university);
        $active_sheet->setCellValue('B5',$title);
        if(mb_strlen($this->profession) > 41){
            $active_sheet->getRowDimension('8')->setRowHeight(35);
            $active_sheet->setCellValue('D8',$this->profession);
        }else{
            $active_sheet->setCellValue('D8',$this->profession);
        }
        $active_sheet->setCellValue('D9',$this->language);
        $active_sheet->setCellValue('D10',$this->study_form);
        $active_sheet->setCellValue('D14',$this->date_time);
        $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
        $active_sheet->setCellValue('D11',$this->subject);

        $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
        $active_sheet->setCellValue('H8',"Курс: ".$this->id_course);
        $active_sheet->setCellValue('D12',$this->teacher);
        $active_sheet->setCellValue('D13',$this->credit);

        $row_start = 16;
        $current_row = $row_start;
        for($i=0; $i<count($this->students); $i++){
            $j = $i+1;
            $current_row++;
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
            $active_sheet->setCellValue('C'.$current_row,$this->students[$i]->fio_student);
            $active_sheet->setCellValue('B'.$current_row,$j);
            $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->studentid);
            $middle_rk = ($this->students[$i]->rk1+$this->students[$i]->rk2) / 2;

            if($middle_rk < 50){
                $middle_rk = $middle_rk." (не доп)";
            }
            $active_sheet->mergeCells('G'.$current_row.':H'.$current_row);
            $active_sheet->setCellValue('F'.$current_row,$middle_rk);
            $active_sheet->setCellValue('E'.$current_row,$this->students[$i]->group_title);
            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
        }
        $methodist = $current_row+5;
        $test = $current_row+7;
        $active_sheet->setCellValue('G'.$methodist,$_SESSION['last_first']);
        $active_sheet->setCellValue('G'.$test,$this->tutor_ct);

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Явочный лист-тест №" .$this->id_stream . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    # Ведомость ПА
    public function report4(){
        $filename = base_path() . "/public/reports/4.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $title = "АРАЛЫҚ АТТЕСТАЦИЯ ВЕДОМОСЫ /                                                                                                                                            ВЕДОМОСТЬ ПРОМЕЖУТОЧНОЙ АТТЕСТАЦИИ № ".$this->id_stream;
        $active_sheet->setCellValue('B3',$this->university);
        $active_sheet->setCellValue('B5',$title);
        if(mb_strlen($this->profession) > 41){
            $active_sheet->getRowDimension('8')->setRowHeight(35);
            $active_sheet->setCellValue('D8',$this->profession);
        }else{
            $active_sheet->setCellValue('D8',$this->profession);
        }
        $active_sheet->setCellValue('D9',$this->language);
        $active_sheet->setCellValue('D10',$this->study_form);
        $active_sheet->setCellValue('D14',$this->date_time);
        $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
        $active_sheet->setCellValue('D11',$this->subject);

        $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
        $active_sheet->setCellValue('H8',"Курс: ".$this->id_course);
        if(!empty($this->fio_teacher)){
            $active_sheet->setCellValue('D12',$this->fio_teacher);
        }else{
            $active_sheet->setCellValue('D12',$this->teacher);
        }
        
        $active_sheet->setCellValue('D13',$this->credit);

        $row_start = 16;
        $current_row = $row_start;
        for($i=0; $i<count($this->students); $i++){
            $j = $i+1;
            $current_row++;
            $id_student = $this->students[$i]->studentid;
            $query_id = $this->students[$i]->queryid;
            $exammark = DB::select("SELECT NT.exammark AS mark FROM nitro.totalmarks NT WHERE NT.queryID='$query_id' AND NT.studentID='$id_student'");
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
            $active_sheet->setCellValue('C'.$current_row,$this->students[$i]->fio_student);
            $active_sheet->setCellValue('B'.$current_row,$j);

            $middle_rk = ($this->students[$i]->rk1+$this->students[$i]->rk2) / 2;
            if($middle_rk < 50){
                $middle_rk = $middle_rk." (не доп)";
                $active_sheet->setCellValue('G'.$current_row,'не доп.');
            }else{
                if($exammark){
                    $active_sheet->setCellValue('G'.$current_row,$exammark[0]->mark);
                }
            }
            $active_sheet->mergeCells('E'.$current_row.':F'.$current_row);
            $active_sheet->setCellValue('E'.$current_row,$middle_rk);
            $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->group_title);
            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
        }
        $methodist = $current_row+5;
        $d = $current_row+7;
        if($this->id_specialization == 256){
            $active_sheet->setCellValue('G'.$d,"Таубаева Г.З.");
        }else{
            $active_sheet->setCellValue('G'.$d,$this->director);
        }
        $active_sheet->setCellValue('G'.$methodist,$_SESSION['last_first']);

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Ведомость ПА №" .$this->id_stream . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    # Итоговая ведомость
    public function report5($branch){
        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $filename = base_path() . "/public/reports/5_z.xls";
        }else{
            $filename = ($branch == 1) ? base_path() . "/public/reports/5_1.xls" : base_path() . "/public/reports/5.xls";
        }

        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $title = "ПӘН БОЙЫНША ОҚУ ЖЕТІСТІГІНІҢ ҚОРЫТЫНДЫ ВЕДОМОСЫ /                                              ИТОГОВАЯ ВЕДОМОСТЬ УЧЕБНЫХ ДОСТИЖЕНИЙ ПО ДИСЦИПЛИНЕ № ".$this->id_stream;
        $active_sheet->setCellValue('B3',$this->university);
        $active_sheet->setCellValue('B5',$title);
        if(mb_strlen($this->profession) > 41){
            $active_sheet->getRowDimension('8')->setRowHeight(35);
            $active_sheet->setCellValue('D8',$this->profession);
        }else{
            $active_sheet->setCellValue('D8',$this->profession);
        }
        $active_sheet->setCellValue('D9',$this->language);
        $active_sheet->setCellValue('D10',$this->study_form);
        $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
        $active_sheet->setCellValue('D11',$this->subject);
        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
            $active_sheet->setCellValue('H8',"Курс: ".$this->id_course);
        }else{
            $active_sheet->setCellValue('I9',"Семестр: ".$this->id_term);
            $active_sheet->setCellValue('I8',"Курс: ".$this->id_course);
        }
		if(!empty($this->fio_teacher)){
			$active_sheet->setCellValue('D12',$this->fio_teacher);
		}else{
			$active_sheet->setCellValue('D12',$this->teacher);
		}
        
        $active_sheet->setCellValue('D13',$this->credit);

        $row_start = 16;
        $current_row = $row_start;
        for($i=0; $i<count($this->students); $i++){
            $j = $i+1;
            $current_row++;
            //$fio = $this->students[$i]->fio_student;
            $id_student = $this->students[$i]->studentid;
            //$id_group = $this->students[$i]->id_group;
            $query_id = $this->students[$i]->queryid;

            $exammark = DB::select("SELECT NT.ap_exammark AS mark, NT.ap_totalmark as total FROM nitro.totalmarks NT WHERE NT.queryID='$query_id' AND NT.studentID='$id_student'");
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
            $active_sheet->setCellValue('C'.$current_row,$this->students[$i]->fio_student);
            $active_sheet->setCellValue('B'.$current_row,$j);
			$active_sheet->setCellValue('E'.$current_row,$this->students[$i]->rk1);
            if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
                if(count($exammark) > 0){
                    $active_sheet->setCellValue('F'.$current_row,$exammark[0]->mark);
                    $total = (int) round($exammark[0]->total);
                    $active_sheet->setCellValue('G'.$current_row,$this->total_mark($total,1));
                    $active_sheet->setCellValue('H'.$current_row,$this->total_mark($total,2));
                    $this->middle_ball = $this->middle_ball + str_replace(",",'.',$this->total_mark($total,2));
                    $active_sheet->setCellValue('I'.$current_row,$total);
					if($this->students[$i]->rk1 < 50){
						$active_sheet->setCellValue('F'.$current_row,"не доп.");
					}
                }else{
                    $active_sheet->setCellValue('F'.$current_row,0);
                }
            }else{
                $active_sheet->setCellValue('F'.$current_row,$this->students[$i]->rk2);
				$two = (int) round(($this->students[$i]->rk1 + $this->students[$i]->rk2)/2);
                if(count($exammark) > 0){
                    $total = (int) round($exammark[0]->total);
                    $active_sheet->setCellValue('H'.$current_row,$this->total_mark($total,1));

                    $active_sheet->setCellValue('I'.$current_row,$this->total_mark($total,2));
                    $this->middle_ball = $this->middle_ball + str_replace(",",'.',$this->total_mark($total,2));
                    $active_sheet->setCellValue('J'.$current_row,$total);
                    if($two < 50){
                        $active_sheet->setCellValue('G'.$current_row,"не доп.");
                    }else{
                        if($exammark[0]->mark){
                            $active_sheet->setCellValue('G'.$current_row,$exammark[0]->mark);
                        }else{
                            $active_sheet->setCellValue('G'.$current_row,0);
                        }

                    }
                }else{
                    $active_sheet->setCellValue('G'.$current_row,0);
                }
            }

            //$group = DB::select(DB::raw("SELECT name FROM groups WHERE groupID=$id_group"));
            $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->group_title);
            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
        }

        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $methodist = $current_row+7;
            $active_sheet->setCellValue('H'.$methodist,$_SESSION['last_first']);
            $middle = $current_row+2;
            $middle_ball = ($this->middle_ball == 0) ? 0 : round($this->middle_ball/count($this->students),2);

            $active_sheet->setCellValue('G'.$middle,$middle_ball);
            $middle = $current_row+3;
            $active_sheet->setCellValue('G'.$middle,$middle_ball);
        }else{
            if($branch == 1){
                $methodist = $current_row+7;
                $active_sheet->setCellValue('I'.$methodist,$_SESSION['last_first']);
                $middle = $current_row+2;
                $middle_ball = ($this->middle_ball == 0) ? 0 : round($this->middle_ball/count($this->students),2);

                $active_sheet->setCellValue('G'.$middle,$middle_ball);
                $middle = $current_row+3;
                $active_sheet->setCellValue('G'.$middle,$middle_ball);
            }
            if($branch == 2){
                $methodist = $current_row+7;
                $d = $methodist+4;
                $active_sheet->setCellValue('I'.$d,$this->tutor_ct);
                $active_sheet->setCellValue('I'.$methodist,$_SESSION['last_first']);
                $middle = $current_row+2;
                $middle_ball = ($this->middle_ball == 0) ? 0 : round($this->middle_ball/count($this->students),2);

                $active_sheet->setCellValue('G'.$middle,$middle_ball);
                $middle = $current_row+3;
                $active_sheet->setCellValue('G'.$middle,$middle_ball);
            }
        }


        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Итоговая ведомость №" .$this->id_stream . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    # Итоговая ведомость
    public function summary_statement(Request $request){
        $branch = $request->input('branch');
        $this->user_id = $_SESSION['id_tutor'];
        $this->id_subject = (int) $request->input('id_subject');
        $this->id_profession = (int) $request->input('id_profession');
        $this->id_specialization = (int) $request->input('id_specialization');
        $this->id_stream = (int) $request->input('id_stream');
        $this->id_report = (int) $request->input('id_report');
        $this->id_course = (int) $request->input('id_course');
        $this->id_term = (int) $request->input('id_term');
        $this->type = (int) $request->input('type');
        $this->type_summary = (int) $request->input('type_summary');
        $this->id_study_lang = (int) $request->input('id_study_lang');
        $this->id_study_form = (int) $request->input('id_study_form');
        $this->date_time = ($request->input('date_time')) ? $request->input('date_time') : date("d.m.Y");
        $this->date_time1 = ($request->input('date_time1')) ? $request->input('date_time1') : date("d.m.Y");
        $this->date_time2 = ($request->input('date_time2')) ? $request->input('date_time2') : date("d.m.Y");
        $course = 0;
        switch ($this->id_term){
            case 1:
                $course = 1;
                break;
            case 2:
                $course = 1;
                break;
            case 3:
                $course = 2;
                break;
            case 4:
                $course = 2;
                break;
            case 5:
                $course = 3;
                break;
            case 6:
                $course = 3;
                break;
        }

        if($this->id_stream){
            if($this->id_profession == 14 AND $this->id_study_form == 7 OR $this->id_study_form == 6){
                $this->students = DB::select("SELECT PT.studentid,PT.fio_student,PT.studygroupid,PT.studygroupid_p, 
                                (CASE WHEN NJ1.Mark IS NULL THEN NNJ1.Mark ELSE NJ1.Mark END) AS rk1,
                                (CASE WHEN NJ2.Mark IS NULL THEN NNJ2.Mark ELSE NJ2.Mark END) AS rk2,
                                 PT.id_calendar, PT.id_group,PT.queryid,PT.group_title
                                FROM phelper.temp PT
                    LEFT JOIN nitro.journal NJ1 ON  NJ1.markTypeID=2 AND NJ1.StudentID=PT.studentid AND  NJ1.StudyGroupID=PT.studygroupid AND NJ1.number=1 AND NJ1.isCurrent=1
                    LEFT JOIN nitro.journal NNJ1 ON  NNJ1.markTypeID=2 AND NNJ1.StudentID=PT.studentid AND NNJ1.StudyGroupID=PT.studygroupid_p AND NNJ1.number=1 AND NNJ1.isCurrent=1
                    LEFT JOIN nitro.journal NJ2 ON  NJ2.markTypeID=2 AND NJ2.StudentID=PT.studentid AND NJ2.StudyGroupID=PT.studygroupid AND NJ2.number=2 AND NJ2.isCurrent=1
                    LEFT JOIN nitro.journal NNJ2 ON  NNJ2.markTypeID=2 AND NNJ2.StudentID=PT.studentid AND NNJ2.StudyGroupID=PT.studygroupid_p AND NNJ2.number=2 AND NNJ2.isCurrent=1
                    WHERE PT.id='$this->user_id'  AND PT.studygroupid='$this->id_stream' ORDER BY StudyGroupID,PT.fio_student");
            }else{
                $this->students = DB::select("SELECT PT.studentid,PT.fio_student,PT.studygroupid,PT.studygroupid_p, 
                                (CASE WHEN NJ1.Mark IS NULL THEN NNJ1.Mark ELSE NJ1.Mark END) AS rk1,
                                (CASE WHEN NJ2.Mark IS NULL THEN NNJ2.Mark ELSE NJ2.Mark END) AS rk2,
                                 PT.id_calendar, PT.id_group,PT.queryid,PT.group_title
                                FROM phelper.temp PT
                    LEFT JOIN nitro.journal NJ1 ON  NJ1.markTypeID=2 AND NJ1.StudentID=PT.studentid AND  NJ1.StudyGroupID=PT.studygroupid AND NJ1.number=1 AND NJ1.isCurrent=1
                    LEFT JOIN nitro.journal NNJ1 ON  NNJ1.markTypeID=2 AND NNJ1.StudentID=PT.studentid AND NNJ1.StudyGroupID=PT.studygroupid_p AND NNJ1.number=1 AND NNJ1.isCurrent=1
                    LEFT JOIN nitro.journal NJ2 ON  NJ2.markTypeID=2 AND NJ2.StudentID=PT.studentid AND NJ2.StudyGroupID=PT.studygroupid AND NJ2.number=2 AND NJ2.isCurrent=1
                    LEFT JOIN nitro.journal NNJ2 ON  NNJ2.markTypeID=2 AND NNJ2.StudentID=PT.studentid AND NNJ2.StudyGroupID=PT.studygroupid_p AND NNJ2.number=2 AND NNJ2.isCurrent=1
                    WHERE PT.id='$this->user_id'  AND PT.studygroupid='$this->id_stream' AND PT.id_spec='$this->id_specialization' ORDER BY StudyGroupID,PT.fio_student");
            }


            $result = DB::select("SELECT pname,lang,form,subjectnameru,year,term,fio_tutor,creditcount FROM phelper.temp PT WHERE PT.subjectid='$this->id_subject' AND PT.studygroupid='$this->id_stream' AND PT.id='$this->user_id' LIMIT 1");
            $this->profession = $result[0]->pname;
            $this->language = $result[0]->lang;
            $this->study_form = $result[0]->form;
            $this->subject = $result[0]->subjectnameru;
            $this->teacher = $result[0]->fio_tutor;
            $this->credit = $result[0]->creditcount;
        }

        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$this->id_profession' AND PC.deleted IS NULL)");

        $this->university = $result1[0]->facultyNameKZ." / ".$result1[0]->facultyNameRU;
        $id_director = $result1[0]->facultyDean;
        $result2 = DB::select("SELECT * FROM tutors WHERE TutorID='$id_director'");
        if($this->id_specialization == 215){
            $this->director = 'Жанбеков Х.Н.';
        }else{
            $this->director = $result2[0]->lastname." ".substr($result2[0]->firstname,0,2).".".substr($result2[0]->patronymic,0,2).".";
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
        $this->tutor_ct = $result3[0]->tutor;

        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $filename = base_path() . "/public/reports/5_z.xls";
        }else{
            $filename = ($branch == 1) ? base_path() . "/public/reports/5_1.xls" : base_path() . "/public/reports/5.xls";
        }

        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $title = "ПӘН БОЙЫНША ОҚУ ЖЕТІСТІГІНІҢ ҚОРЫТЫНДЫ ВЕДОМОСЫ /                                              ИТОГОВАЯ ВЕДОМОСТЬ УЧЕБНЫХ ДОСТИЖЕНИЙ ПО ДИСЦИПЛИНЕ № ".$this->id_stream;
        $active_sheet->setCellValue('B3',$this->university);
        $active_sheet->setCellValue('B5',$title);
        if(mb_strlen($this->profession) > 41){
            $active_sheet->getRowDimension('8')->setRowHeight(35);
            $active_sheet->setCellValue('D8',$this->profession);
        }else{
            $active_sheet->setCellValue('D8',$this->profession);
        }
        $active_sheet->setCellValue('D9',$this->language);
        $active_sheet->setCellValue('D10',$this->study_form);
        $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
        $active_sheet->setCellValue('D11',$this->subject);
        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
            $active_sheet->setCellValue('H8',"Курс: ".$course);
        }else{
            $active_sheet->setCellValue('I9',"Семестр: ".$this->id_term);
            $active_sheet->setCellValue('I8',"Курс: ".$course);
        }

        $active_sheet->setCellValue('D12',$this->teacher);
        $active_sheet->setCellValue('D13',$this->credit);

        $row_start = 16;
        $current_row = $row_start;
        for($i=0; $i<count($this->students); $i++){
            $j = $i+1;
            $current_row++;
            $id_student = $this->students[$i]->studentid;
            $query_id = $this->students[$i]->queryid;

            $exammark = DB::select("SELECT NT.ap_exammark AS mark, NT.ap_totalmark as total FROM nitro.totalmarks NT WHERE NT.queryID='$query_id' AND NT.studentID='$id_student'");
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
            $active_sheet->setCellValue('C'.$current_row,$this->students[$i]->fio_student);
            $active_sheet->setCellValue('B'.$current_row,$j);
            $active_sheet->setCellValue('E'.$current_row,$this->students[$i]->rk1);
            if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
                if(count($exammark) > 0){
                    $active_sheet->setCellValue('F'.$current_row,$exammark[0]->mark);
                    $total = (int) round($exammark[0]->total);
                    $active_sheet->setCellValue('G'.$current_row,$this->total_mark($total,1));
                    $active_sheet->setCellValue('H'.$current_row,$this->total_mark($total,2));
                    $this->middle_ball = $this->middle_ball + str_replace(",",'.',$this->total_mark($total,2));
                    $active_sheet->setCellValue('I'.$current_row,$total);
                    if($this->students[$i]->rk1 < 50){
                        $active_sheet->setCellValue('F'.$current_row,"не доп.");
                    }
                }else{
                    $active_sheet->setCellValue('F'.$current_row,0);
                }
            }else{
                $active_sheet->setCellValue('F'.$current_row,$this->students[$i]->rk2);
                $two = (int) round(($this->students[$i]->rk1 + $this->students[$i]->rk2)/2);
                if(count($exammark) > 0){
                    $total = (int) round($exammark[0]->total);
                    $active_sheet->setCellValue('H'.$current_row,$this->total_mark($total,1));

                    $active_sheet->setCellValue('I'.$current_row,$this->total_mark($total,2));
                    $this->middle_ball = $this->middle_ball + str_replace(",",'.',$this->total_mark($total,2));
                    $active_sheet->setCellValue('J'.$current_row,$total);
                    if($two < 50){
                        $active_sheet->setCellValue('G'.$current_row,"не доп.");
                    }else{
                        if($exammark[0]->mark){
                            $active_sheet->setCellValue('G'.$current_row,$exammark[0]->mark);
                        }else{
                            $active_sheet->setCellValue('G'.$current_row,0);
                        }

                    }
                }else{
                    $active_sheet->setCellValue('G'.$current_row,0);
                }
            }

            //$group = DB::select(DB::raw("SELECT name FROM groups WHERE groupID=$id_group"));
            $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->group_title);
            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
        }

        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $methodist = $current_row+7;
            $active_sheet->setCellValue('H'.$methodist,$_SESSION['last_first']);
            $middle = $current_row+2;
            $middle_ball = ($this->middle_ball == 0) ? 0 : round($this->middle_ball/count($this->students),2);

            $active_sheet->setCellValue('G'.$middle,$middle_ball);
            $middle = $current_row+3;
            $active_sheet->setCellValue('G'.$middle,$middle_ball);
        }else{
            if($branch == 1){
                $methodist = $current_row+7;
                $active_sheet->setCellValue('I'.$methodist,$_SESSION['last_first']);
                $middle = $current_row+2;
                $middle_ball = ($this->middle_ball == 0) ? 0 : round($this->middle_ball/count($this->students),2);

                $active_sheet->setCellValue('G'.$middle,$middle_ball);
                $middle = $current_row+3;
                $active_sheet->setCellValue('G'.$middle,$middle_ball);
            }
            if($branch == 2){
                $methodist = $current_row+7;
                $d = $methodist+4;
                $active_sheet->setCellValue('I'.$d,$this->tutor_ct);
                $active_sheet->setCellValue('I'.$methodist,$_SESSION['last_first']);
                $middle = $current_row+2;
                $middle_ball = ($this->middle_ball == 0) ? 0 : round($this->middle_ball/count($this->students),2);

                $active_sheet->setCellValue('G'.$middle,$middle_ball);
                $middle = $current_row+3;
                $active_sheet->setCellValue('G'.$middle,$middle_ball);
            }
        }


        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Итоговая ведомость №" .$this->id_stream . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    # Индивидуальная ведомость рейтинга
    public function report6($id_student){
        $result = DB::select("SELECT PT.studentid,PT.fio_student,PT.studygroupid,PT.studygroupid_p, 
                                (CASE WHEN NJ1.Mark IS NULL THEN NNJ1.Mark ELSE NJ1.Mark END) AS rk1,
                                (CASE WHEN NJ2.Mark IS NULL THEN NNJ2.Mark ELSE NJ2.Mark END) AS rk2,
                                 PT.id_calendar, PT.id_group,PT.tutor_inicial, PT.term, PT.creditcount,PT.subjectnameru,PT.pname,PT.form,PT.lang,
                                 PT.year
                                FROM phelper.temp PT
                    LEFT JOIN nitro.journal NJ1 ON  NJ1.markTypeID=2 AND NJ1.StudentID=PT.studentid AND  NJ1.StudyGroupID=PT.studygroupid AND NJ1.number=1
                    LEFT JOIN nitro.journal NNJ1 ON  NNJ1.markTypeID=2 AND NNJ1.StudentID=PT.studentid AND NNJ1.StudyGroupID=PT.studygroupid_p AND NNJ1.number=1
                    LEFT JOIN nitro.journal NJ2 ON  NJ2.markTypeID=2 AND NJ2.StudentID=PT.studentid AND NJ2.StudyGroupID=PT.studygroupid AND NJ2.number=2
                    LEFT JOIN nitro.journal NNJ2 ON  NNJ2.markTypeID=2 AND NNJ2.StudentID=PT.studentid AND NNJ2.StudyGroupID=PT.studygroupid_p AND NNJ2.number=2
                    WHERE PT.id='$this->user_id'  AND PT.studentid='$id_student' AND PT.year='$this->id_course' AND PT.term='$this->id_term' ORDER BY StudyGroupID,PT.fio_student");

        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $filename = base_path() . "/public/reports/6_z.xls";
        }else{
            $filename = base_path() . "/public/reports/6.xls";
        }


        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objReader->setReadDataOnly(false);

        $objPHPExcel = $objReader->load($filename);

        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $number = $this->tutorStream($this->user_id, "Индивидуальная ведомость рейтинга");
        $title = "РЕЙТИНГ ЖЕКЕ ВЕДОМОСЫ / ИНДИВИДУАЛЬНАЯ ВЕДОМОСТЬ РЕЙТИНГА № ".$number;
        $active_sheet->setCellValue('B3',$this->university);
        $active_sheet->setCellValue('B5',$title);
        $active_sheet->setCellValue('D12',$this->date_time);

        $row_start = 14;
        $current_row = $row_start;
        for($i=0; $i<count($result); $i++){
            $j = $i+1;
            $current_row++;
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->setCellValue('B'.$current_row,$j);
            $active_sheet->setCellValue('D11',$result[$i]->fio_student);
            if(mb_strlen($result[$i]->pname) > 41){
                $active_sheet->getRowDimension('8')->setRowHeight(35);
                $active_sheet->setCellValue('D8',$result[$i]->pname);
            }else{
                $active_sheet->setCellValue('D8',$result[$i]->pname);
            }
            $active_sheet->setCellValue('D9',$result[$i]->lang);
            $active_sheet->setCellValue('D10',$result[$i]->form);
            $active_sheet->setCellValue('H8',"Курс: ".$result[$i]->year);
            $active_sheet->setCellValue('C'.$current_row,$result[$i]->subjectnameru);
            $active_sheet->setCellValue('D'.$current_row,$result[$i]->tutor_inicial);
            $active_sheet->setCellValue('E'.$current_row,$result[$i]->term);
            $active_sheet->setCellValue('F'.$current_row,$result[$i]->creditcount);
            $active_sheet->setCellValue('G'.$current_row,$result[$i]->rk1);
            $active_sheet->setCellValue('I'.$current_row,$result[$i]->rk2);

            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getStyle('D'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
        }
        $methodist = $current_row+6;

        $active_sheet->setCellValue('G'.$methodist,$_SESSION['last_first']);

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Индивидуальная ведомость рейтинга №" .$number . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    # Индивидуальная ведомость ПА
    public function report7($id_student){
        $result = DB::select("SELECT PT.studentid,PT.fio_student,PT.studygroupid,PT.studygroupid_p, 
                                (CASE WHEN NJ1.Mark IS NULL THEN NNJ1.Mark ELSE NJ1.Mark END) AS rk1,
                                (CASE WHEN NJ2.Mark IS NULL THEN NNJ2.Mark ELSE NJ2.Mark END) AS rk2,
                                 PT.id_calendar, PT.id_group,PT.tutor_inicial, PT.term, PT.creditcount,PT.subjectnameru,PT.pname,PT.form,PT.lang,
                                 PT.year,PT.queryid
                                FROM phelper.temp PT
                    LEFT JOIN nitro.journal NJ1 ON  NJ1.markTypeID=2 AND NJ1.StudentID=PT.studentid AND  NJ1.StudyGroupID=PT.studygroupid AND NJ1.number=1
                    LEFT JOIN nitro.journal NNJ1 ON  NNJ1.markTypeID=2 AND NNJ1.StudentID=PT.studentid AND NNJ1.StudyGroupID=PT.studygroupid_p AND NNJ1.number=1
                    LEFT JOIN nitro.journal NJ2 ON  NJ2.markTypeID=2 AND NJ2.StudentID=PT.studentid AND NJ2.StudyGroupID=PT.studygroupid AND NJ2.number=2
                    LEFT JOIN nitro.journal NNJ2 ON  NNJ2.markTypeID=2 AND NNJ2.StudentID=PT.studentid AND NNJ2.StudyGroupID=PT.studygroupid_p AND NNJ2.number=2
                    WHERE PT.id='$this->user_id'  AND PT.studentid='$id_student' AND PT.year='$this->id_course' AND PT.term='$this->id_term' ORDER BY StudyGroupID,PT.fio_student");


        if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
            $filename = base_path() . "/public/reports/7_z.xls";
        }else{
            $filename = base_path() . "/public/reports/7.xls";
            switch ($this->type){
                case 1:
                    // управление регистрации
                    $filename = base_path() . "/public/reports/7_app_ur.xls";
                    break;
                case 2:
                    // Отдел тестирование
                    $filename = base_path() . "/public/reports/7_app_ot.xls";
                    break;
            }
        }

        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objReader->setReadDataOnly(false);

        $objPHPExcel = $objReader->load($filename);

        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $number = $this->tutorStream($this->user_id, "Индивидуальная ведомость ПА");
        $title = "АРАЛЫҚ АТТЕСТАЦИЯ ЖЕКЕ ВЕДОМОСЫ /                                                                                                                                                            ИНДИВИДУАЛЬНАЯ ВЕДОМОСТЬ ПРОМЕЖУТОЧНЫЙ АТТЕСТАЦИИ № ".$number;
        $active_sheet->setCellValue('B3',$this->university);
        $active_sheet->setCellValue('D12',$this->date_time);
        if($this->type != 0){
            $title = "АРАЛЫҚ АТТЕСТАЦИЯ ЖЕКЕ ВЕДОМОСЫ (апелляция есебімен)/                                                                                                                                                            ИНДИВИДУАЛЬНАЯ ВЕДОМОСТЬ ПРОМЕЖУТОЧНЫЙ АТТЕСТАЦИИ № ".$number." (с учетом апелляции)";
        }
        $active_sheet->setCellValue('B5',$title);

        $row_start = 14;
        $current_row = $row_start;
        for($i=0; $i<count($result); $i++){
            $j = $i+1;
            //$study_group_id = $result[$i]->studygroupid;
            $query_id = $result[$i]->queryid;
            $exammark = DB::select("SELECT NT.exammark AS mark, NT.ap_exammark as ap_mark FROM nitro.totalmarks NT WHERE NT.queryID='$query_id' AND NT.studentID='$id_student' AND NT.isCurrent=1");
            $current_row++;
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->setCellValue('B'.$current_row,$j);
            $active_sheet->setCellValue('D11',$result[$i]->fio_student);
            if(mb_strlen($result[$i]->pname) > 41){
                $active_sheet->getRowDimension('8')->setRowHeight(35);
                $active_sheet->setCellValue('D8',$result[$i]->pname);
            }else{
                $active_sheet->setCellValue('D8',$result[$i]->pname);
            }
            $active_sheet->setCellValue('D9',$result[$i]->lang);
            $active_sheet->setCellValue('D10',$result[$i]->form);
            $active_sheet->setCellValue('I8',"Курс: ".$result[$i]->year);
            $active_sheet->setCellValue('C'.$current_row,$result[$i]->subjectnameru);
            $active_sheet->setCellValue('D'.$current_row,$result[$i]->tutor_inicial);
            $active_sheet->setCellValue('E'.$current_row,$result[$i]->term);
            $active_sheet->setCellValue('F'.$current_row,$result[$i]->creditcount);
            if(($this->id_study_form == 6) OR ($this->id_study_form == 7)){
                $secondary = $result[$i]->rk1;
                $active_sheet->setCellValue('G'.$current_row,$secondary);
                if($exammark){
                    if($secondary < 50){
                        $active_sheet->setCellValue('H'.$current_row,"не доп.");
                    }else{
                        $active_sheet->setCellValue('H'.$current_row,$exammark[0]->mark);
                    }
                }
            }else{
                $secondary = ($result[$i]->rk1 + $result[$i]->rk2) / 2;
                $active_sheet->setCellValue('G'.$current_row,$secondary);
                if($exammark){
                    if($secondary < 50){
                        $active_sheet->setCellValue('H'.$current_row,"не доп.");
                        if($this->type != 0){
                            $active_sheet->setCellValue('I'.$current_row,$exammark[0]->ap_mark);
                        }
                    }else{
                        $active_sheet->setCellValue('H'.$current_row,$exammark[0]->mark);
                        if($this->type != 0){
                            $active_sheet->setCellValue('I'.$current_row,$exammark[0]->ap_mark);
                        }
                    }
                }
            }


            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getStyle('D'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
        }
        switch ($this->type){
            case 0:
                $current_row += 6;
                $active_sheet->setCellValue('G'.$current_row,$_SESSION['last_first']);
                break;
            case 1:
                // управление регистрации
				$current_dt = $current_row + 3;
				$active_sheet->setCellValue('G'.$current_dt,$this->director);
                $current_row += 6;
                $active_sheet->setCellValue('G'.$current_row,$_SESSION['last_first']);
                break;
            case 2:
                // Отдел тестирование
				$current_dt = $current_row + 3;
				$active_sheet->setCellValue('G'.$current_dt,$this->director);
                $current_row += 6;
                $active_sheet->setCellValue('G'.$current_row,$_SESSION['last_first']);
				$current_ct = $current_row+4;
				$active_sheet->setCellValue('G'.$current_ct,$this->tutor_ct);
                break;
        }


        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Индивидуальная ведомость ПА №" .$number . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    # Сводная ведомость
    public function report8($id_summary,$start_date){
        date_default_timezone_set('Asia/Almaty');
        $filename = base_path() . "/public/reports/summary.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$this->id_profession' AND PC.deleted IS NULL)");

        $university = $result1[0]->facultyNameKZ." / ".$result1[0]->facultyNameRU;
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
        $res_profession = DB::select("SELECT * FROM nitro.professions WHERE professionID='$this->id_profession'");
        $res_lang = DB::select("SELECT * FROM nitro.studylanguages WHERE Id='$this->id_study_lang'");
        $res_spec = DB::select("SELECT * FROM nitro.specializations WHERE id='$this->id_specialization'");
        $profession = $res_profession[0]->professionCode."-".$res_profession[0]->professionNameRU;
        $study_form = $this->id_study_form;
        $course_number = $this->id_course;
        $semester = $this->id_term;
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
        $active_sheet->setCellValue('B7',"Оқу түрі/Форма обучения: ".$this->get_study_form($study_form,1).",        оқу жылы/учебный год: 2017/2018,        Түскен жылы/Год поступления: ".$start_date.",        оқу мерзімі/срок обучения: ".$this->get_study_form($study_form,2));
        $active_sheet->setCellValue('B8',"мамандануы/cпециализации: ".$spec_name.",        күні/дата: ".date("d.m.Y H:i:s"));
		$summary_group = $this->group_summary;
        if($id_summary == 0){
            if($this->id_term == 2){
				
                $result2 = DB::select("
SELECT PIS.* FROM ((SELECT
                    SB.SubjectCode,SB.SubjectNameRU,SB.SubjectNameKZ,SB.SubjectNameENG,
                    NT.ap_totalmark,NT.credits,A.gnt,A.lastname,A.firstname,A.patronymic,SB.SubjectID,A.studentid
                    FROM
                    (SELECT Q.queryID,Q.subjectid as tupSubjectID,Q.studentid,Q.year,Q.term,
                    ST.studyformid,ST.StudyLanguageID,ST.ProfessionID,ST.CourseNumber,ST.specializationID,
                    ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND
                    Q.studentID =ST.studentID $summary_group) AS A
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
                          ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND QQ.studentID =ST.studentID AND QQ.year=ST.coursenumber $summary_group) POD ON POD.student_id=NT.StudentID AND NT.coursenumber='$this->id_course') PID
LEFT JOIN (SELECT SB.SubjectCode,A.studentid,SB.SubjectID
                    FROM
                    (SELECT Q.subjectid as tupSubjectID,Q.studentid

                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND
                    Q.studentID =ST.studentID $summary_group) AS A
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    ) OOO ON OOO.studentid=PID.StudentID AND OOO.SubjectCode=PID.subjectcode
WHERE OOO.SubjectCode IS NULL)) PIS ORDER BY SubjectNameRU,lastname,firstname,patronymic");

                $result = DB::select("
SELECT PIS.* FROM ((SELECT 
                    SB.SubjectCode,SB.SubjectNameRU,SB.SubjectNameKZ,SB.SubjectNameENG,
                    NT.ap_totalmark,NT.credits,A.gnt,A.lastname,A.firstname,A.patronymic,SB.SubjectID,A.studentid
                    FROM
                    (SELECT Q.queryID,Q.subjectid as tupSubjectID,Q.studentid,Q.year,Q.term,
                    ST.studyformid,ST.StudyLanguageID,ST.ProfessionID,ST.CourseNumber,ST.specializationID,
                    ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND Q.term='$this->id_term' AND 
                    Q.studentID =ST.studentID $summary_group) AS A 
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
                          ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND
                          QQ.term='$this->id_term' AND QQ.studentID =ST.studentID AND QQ.year=ST.coursenumber $summary_group) POD ON POD.student_id=NT.StudentID AND NT.term='$this->id_term' AND NT.coursenumber='$this->id_course') PID
LEFT JOIN (SELECT SB.SubjectCode,A.studentid,SB.SubjectID
                    FROM
                    (SELECT Q.subjectid as tupSubjectID,Q.studentid
                    
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND Q.term='$this->id_term' AND ST.specializationID='$this->id_specialization' AND
                    Q.studentID =ST.studentID $summary_group) AS A 
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    ) OOO ON OOO.studentid=PID.StudentID AND OOO.SubjectCode=PID.subjectcode
WHERE OOO.SubjectCode IS NULL)) PIS ORDER BY SubjectNameRU,lastname,firstname,patronymic");
            }else{
                $result = DB::select("
SELECT PIS.* FROM ((SELECT 
                    SB.SubjectCode,SB.SubjectNameRU,SB.SubjectNameKZ,SB.SubjectNameENG,
                    NT.ap_totalmark,NT.credits,A.gnt,A.lastname,A.firstname,A.patronymic,SB.SubjectID,A.studentid
                    FROM
                    (SELECT Q.queryID,Q.subjectid as tupSubjectID,Q.studentid,Q.year,Q.term,
                    ST.studyformid,ST.StudyLanguageID,ST.ProfessionID,ST.CourseNumber,ST.specializationID,
                    ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND Q.term='$this->id_term' AND 
                    Q.studentID =ST.studentID AND Q.year=ST.coursenumber $summary_group) AS A 
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
                          ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND
                          QQ.term='$this->id_term' AND QQ.studentID =ST.studentID AND QQ.year=ST.coursenumber $summary_group) POD ON POD.student_id=NT.StudentID AND NT.term='$this->id_term' AND NT.coursenumber='$this->id_course') PID
LEFT JOIN (SELECT SB.SubjectCode,A.studentid,SB.SubjectID
                    FROM
                    (SELECT Q.subjectid as tupSubjectID,Q.studentid
                    
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE YEAR(ST.StartDate)=$start_date AND ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND Q.term='$this->id_term' AND ST.specializationID='$this->id_specialization' AND
                    Q.studentID =ST.studentID AND Q.year=ST.coursenumber $summary_group) AS A 
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
                $arr_gpa[$item_student] = $arr_gpa[$item_student]+(str_replace(",",".",$this->total_mark($result[$i]->ap_totalmark,2))*$result[$i]->credits);
                $arr_credit[$item_student] = $arr_credit[$item_student]+$result[$i]->credits;

                $v = $row_start+$item_student+1;
                $percent = $this->getLetter($col_start + $item_discipline*3).$v;
                $active_sheet->getStyle($percent)->applyFromArray($styleArray);
                $active_sheet->setCellValue($percent, round($result[$i]->ap_totalmark));

                $alpha = $this->getLetter($col_start + $item_discipline*3+1).$v;
                $active_sheet->getStyle($alpha)->applyFromArray($styleArray);
                $active_sheet->setCellValue($alpha, $this->total_mark($result[$i]->ap_totalmark,1));

                $ball = $this->getLetter($col_start + $item_discipline*3+2).$v;
                $active_sheet->getStyle($ball)->applyFromArray($styleArray);
                $active_sheet->setCellValue($ball, $this->total_mark($result[$i]->ap_totalmark,2));
            }
            ///
            if($this->id_term == 2){
                $arr = [];
                for($k=0; $k<count($result2); $k++){
                    if(array_key_exists($result2[$k]->studentid, $arr)){
                        $arr[$result2[$k]->studentid]['all_credit'] += (int) $result2[$k]->credits;
                        $arr[$result2[$k]->studentid]['all_gpa']    += (str_replace(",",".",$this->total_mark($result2[$k]->ap_totalmark,2))*$result2[$k]->credits);
                    }else{
                        $arr[$result2[$k]->studentid]['all_credit'] = (int) $result2[$k]->credits;
                        $arr[$result2[$k]->studentid]['all_gpa']    = (str_replace(",",".",$this->total_mark($result2[$k]->ap_totalmark,2))*$result2[$k]->credits);
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
                if($this->id_term == 1){
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

                if($this->id_term == 1){
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
        }else{
            // конкурс
            $konkurs = DB::select("CALL phelper.svodniy_s_term($start_date,$this->id_profession,$this->id_study_form,$this->id_study_lang,$this->id_course,$this->id_term,$this->id_specialization)");
            $all_semesters = DB::select("CALL phelper.konkurs($start_date,$this->id_profession,$this->id_study_form,$this->id_study_lang,$this->id_course,$this->id_specialization)");
            $arr_student    = [];
            $arr_discipline = [];
            $arr_gpa    = [];
            $arr_credit = [];

            $col_start = 4;
            $row_start = 16;
            $count_credit = 0;
            for($i=0; $i< count($konkurs); $i++) {
                $subjectid = $konkurs[$i]->SubjectCode;
                $studentid = $konkurs[$i]->studentid;
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
                    $active_sheet->setCellValue($letter1, $konkurs[$i]->SubjectCode . " " . $konkurs[$i]->SubjectNameRU);
                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '14';
                    $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '14';
                    $range = $letter1 . ":" . $letter2;
                    $active_sheet->mergeCells($range);
                    $active_sheet->getStyle($range)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, $konkurs[$i]->credits);
                    $count_credit = $count_credit + $konkurs[$i]->credits;
                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '16';
                    $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '16';
                    $range = $letter1 . ":" . $letter2;
                    $active_sheet->mergeCells($range);
                    $active_sheet->getStyle($range)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, 'ә/б');

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
                    $fio = $konkurs[$i]->lastname." ".$konkurs[$i]->firstname." ".$konkurs[$i]->patronymic;
                    $active_sheet->setCellValue($letter1, $fio);
                    $active_sheet->setCellValue('C'.$var, $konkurs[$i]->gnt);
                    $active_sheet->setCellValue('A'.$var, count($arr_student));

                }
                $item_student = array_search($studentid,$arr_student);
                $item_discipline = array_search($subjectid,$arr_discipline);
                $v = $row_start+$item_student+1;
                $letter1 = $this->getLetter($col_start + $item_discipline * 3) . $v;
                $letter2 = $this->getLetter($col_start + $item_discipline * 3 + 2) . $v;
                $range = $letter1 . ":" . $letter2;
                $active_sheet->mergeCells($range);
                $active_sheet->getStyle($range)->applyFromArray($styleArray);
                $active_sheet->setCellValue($letter1, $this->total_mark($konkurs[$i]->ap_totalmark,1));
            }
            ///
            $arr = [];
            for($k=0; $k<count($all_semesters); $k++){
                if(array_key_exists($all_semesters[$k]->studentid, $arr)){
                    $arr[$all_semesters[$k]->studentid]['credit'] += (int) $all_semesters[$k]->credits;
                    $arr[$all_semesters[$k]->studentid]['gpa']    += (str_replace(",",".",$this->total_mark($all_semesters[$k]->ap_totalmark,2))*$all_semesters[$k]->credits);
                }else{
                    $arr[$all_semesters[$k]->studentid]['credit'] = (int) $all_semesters[$k]->credits;
                    $arr[$all_semesters[$k]->studentid]['gpa']    = (str_replace(",",".",$this->total_mark($all_semesters[$k]->ap_totalmark,2))*$all_semesters[$k]->credits);
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
                if(array_key_exists($arr_student[$i], $arr)){
                    $active_sheet->setCellValue($letter1, $arr[$arr_student[$i]]['credit']);
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
                if(array_key_exists($arr_student[$i], $arr)){
                    $gpa = str_replace(",",".",round($arr[$arr_student[$i]]['gpa']/$arr[$arr_student[$i]]['credit'],2));
                    $active_sheet->setCellValue($letter1, $gpa);
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
        }


		$filename = 'сводная_'.date("d.m.Y H:i:s");	
        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=$filename.xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }
    
    # Сводная ведомость стипендиятов
    public function report9(){
        date_default_timezone_set('Asia/Almaty');
        $filename = base_path() . "/public/reports/summary7.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();

        $result = DB::select("SELECT A.*,
                    TS.subjectID,SB.SubjectCode,SB.SubjectNameRU,SB.SubjectNameKZ,SB.SubjectNameENG,
                    PF.professionNameRU,PF.professionNameKZ,PF.professionNameEN,PF.professioncode,
                    SF.NameRu as formobuch_ru,SF.NameKz as formobuch_kz,SF.NameEn as formobuch_eng,
                    SL.NameRU as otdelenie_ru,SL.NameKZ as otdelenie_kz,SL.NameEN as otdelenie_eng,
                    NT.totalmark,NT.ap_totalmark,NT.credits
                    FROM
                    (SELECT Q.queryID,Q.subjectid as tupSubjectID,Q.studentid,Q.year,Q.term,
                    ST.studyformid,ST.StudyLanguageID,ST.ProfessionID,ST.CourseNumber,
                    ST.lastname,ST.firstname,ST.patronymic,CASE WHEN ST.PaymentFormID=1 THEN 'п' WHEN ST.PaymentFormID=2 THEN 'г' END AS gnt
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND Q.term='$this->id_term' AND 
                    Q.studentID =ST.studentID AND Q.year=ST.coursenumber) AS A 
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    LEFT JOIN nitro.professions PF ON PF.professionID=A.ProfessionID
                    LEFT JOIN nitro.studyforms SF ON SF.Id=A.studyformid
                    LEFT JOIN nitro.studylanguages SL ON SL.Id=A.StudyLanguageID
                    LEFT JOIN nitro.totalmarks NT ON NT.studentID=A.studentid AND NT.queryID=A.queryID
                    ORDER BY SubjectNameRU,lastname,firstname,patronymic");

        if(!$result){
            dd("По выбранным данным не найдено студентов");
        }
        $rrr = [];
        $vbv = [];

        for($i=0; $i<count($result); $i++){
            if(($result[$i]->ap_totalmark>=74.5 AND $result[$i]->ap_totalmark < 75) OR ($result[$i]->ap_totalmark>=89.5 AND $result[$i]->ap_totalmark < 90)){
                $bool = array_search($result[$i]->studentid,$rrr);
                if($bool === FALSE){
                    $rrr[] = $result[$i]->studentid;
                }
            }

            if($result[$i]->ap_totalmark<74.5){
                $bool = array_search($result[$i]->studentid,$vbv);
                if($bool === FALSE){
                    $vbv[] = $result[$i]->studentid;
                }
            }

        }
        //dd($rrr);
        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$this->id_profession' AND PC.deleted IS NULL)");

        $university = $result1[0]->facultyNameKZ." / ".$result1[0]->facultyNameRU;
        $arr_student    = [];
        $arr_discipline = [];
        $arr_gpa    = [];
        $arr_credit = [];

        $col_start = 4;
        $row_start = 15;

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
        $profession = $result[0]->professioncode."-".$result[0]->professionNameRU;
        $study_form = $result[0]->studyformid;
        $course_number = $result[0]->CourseNumber;
        $semester = $result[0]->term;
        $lang = $result[0]->otdelenie_ru;
        $lang = mb_substr($lang,0,3);

        $active_sheet->setCellValue('C3',$university);
        $active_sheet->setCellValue('E5',$profession);
        $active_sheet->setCellValue('C6',$this->get_study_form($study_form,1));
        $active_sheet->setCellValue('X5',$course_number);
        $active_sheet->setCellValue('AA5',$semester);
        $active_sheet->setCellValue('AE6',$this->get_study_form($study_form,2));
        $active_sheet->setCellValue('I7',date("d.m.Y H:i:s"));
        $active_sheet->setCellValue('T5',$lang);
        $active_sheet->setCellValue('V6',$this->get_year($course_number));

        $count_credit = 0;
        if(!empty($rrr)){
            for($i=0; $i< count($result); $i++) {
                $subjectid = (int) $result[$i]->subjectID;
                $studentid = $result[$i]->studentid;
                $bool = array_search($subjectid,$arr_discipline);
                if($bool === FALSE){
                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '10';
                    $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '10';
                    $range = $letter1 . ":" . $letter2;
                    $active_sheet->mergeCells($range);
                    $active_sheet->getStyle($range)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, count($arr_discipline) + 1);

                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '11';
                    $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '11';
                    $range = $letter1 . ":" . $letter2;
                    $active_sheet->mergeCells($range);
                    $active_sheet->getStyle($range)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, $result[$i]->SubjectCode . " " . $result[$i]->SubjectNameRU);

                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '13';
                    $letter2 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '13';
                    $range = $letter1 . ":" . $letter2;
                    $active_sheet->mergeCells($range);
                    $active_sheet->getStyle($range)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, $result[$i]->credits);
                    $count_credit = $count_credit + $result[$i]->credits;

                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3) . '15';
                    $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, '%');

                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3 + 1) . '15';
                    $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, 'ә/б');

                    $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3 + 2) . '15';
                    $active_sheet->getStyle($letter1)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($letter1, 'Б');

                    $arr_discipline[] = $subjectid;
                }
                if(in_array($studentid,$rrr) AND !in_array($studentid,$vbv)){
                    // est
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
                    $arr_gpa[$item_student] = $arr_gpa[$item_student]+(str_replace(",",".",$this->total_mark($result[$i]->ap_totalmark,2))*$result[$i]->credits);
                    $arr_credit[$item_student] = $arr_credit[$item_student]+$result[$i]->credits;
                    $v = $row_start+$item_student+1;
                    $percent = $this->getLetter($col_start + $item_discipline*3).$v;
                    $active_sheet->getStyle($percent)->applyFromArray($styleArray);
                    $active_sheet->getStyle($percent)->getNumberFormat()->applyFromArray(
                        array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL
                        )
                    );
                    $tot = ' '.str_replace(".",",",substr($result[$i]->ap_totalmark,0,5));
                    //echo substr($result[$i]->ap_totalmark,0,5)."<br>";
                    if(($result[$i]->ap_totalmark >= 74.5 AND $result[$i]->ap_totalmark < 75) OR ($result[$i]->ap_totalmark>=89.5 AND $result[$i]->ap_totalmark<90)){
                        $active_sheet->getStyle($percent)->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'FFFF66')
                                ),
                                'font'  => array(
                                    'bold'  => true,
                                    'color' => array('rgb' => '000000'),
                                    'size'  => 14,
                                    'name'  => 'Verdana'
                                )
                            )
                        );
                    }
                    $active_sheet->setCellValue($percent, $tot);
                    //echo $result[$i]->ap_totalmark."<br>";
                    $alpha = $this->getLetter($col_start + $item_discipline*3+1).$v;
                    $active_sheet->getStyle($alpha)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($alpha, $this->total_mark($result[$i]->ap_totalmark,1));

                    $ball = $this->getLetter($col_start + $item_discipline*3+2).$v;
                    $active_sheet->getStyle($ball)->applyFromArray($styleArray);
                    $active_sheet->setCellValue($ball, $this->total_mark($result[$i]->ap_totalmark,2));
                }
            }
            $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3-1) . '9';
            $active_sheet->mergeCells('D9:'.$letter1);
            $active_sheet->getStyle('D9')->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->getStyle('D9:'.$letter1)->applyFromArray($styleArray);
            $active_sheet->setCellValue('D9', 'Пәндер/Дисциплины');

            $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3-1) . '12';
            $active_sheet->mergeCells('D12:'.$letter1);
            $active_sheet->getStyle('D12')->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->getStyle('D12:'.$letter1)->applyFromArray($styleArray);
            $active_sheet->setCellValue('D12', 'Кредиттер саны/Количество кредитов');

            $letter1 = $this->getLetter($col_start + count($arr_discipline) * 3-1) . '14';
            $active_sheet->mergeCells('D14:'.$letter1);
            $active_sheet->getStyle('D14')->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->getStyle('D14:'.$letter1)->applyFromArray($styleArray);
            $active_sheet->setCellValue('D14', 'Баға/Оценка');

            $methodist = 'P' . ($row_start+count($arr_student)+4);
            $active_sheet->setCellValue($methodist, $_SESSION['last_first']);

            // Нарисовать бардеры
            for($i=0; $i<count($arr_student); $i++){
                $found = 0;
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


            header("Content-Type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=сводная.xls");
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save("php://output");
            exit();
        }else{
            dd("Не найдено студентов по условие");
        }
    }

    public function report10(){
        $filename = base_path() . "/public/reports/10.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        //$title = "ПӘН БОЙЫНША ОҚУ ЖЕТІСТІГІНІҢ ҚОРЫТЫНДЫ ВЕДОМОСЫ /                                              ИТОГОВАЯ ВЕДОМОСТЬ УЧЕБНЫХ ДОСТИЖЕНИЙ ПО ДИСЦИПЛИНЕ № ".$this->id_stream;
        $active_sheet->setCellValue('B3',$this->university);
        //$active_sheet->setCellValue('B5',$title);
        if(mb_strlen($this->profession) > 41){
            $active_sheet->getRowDimension('8')->setRowHeight(35);
            $active_sheet->setCellValue('D8',$this->profession);
        }else{
            $active_sheet->setCellValue('D8',$this->profession);
        }
        $active_sheet->setCellValue('D9',$this->language);
        $active_sheet->setCellValue('D10',$this->study_form);
        $active_sheet->getRowDimension('11')->setRowHeight(ceil(mb_strlen($this->subject)/41)*17.5);
        $active_sheet->setCellValue('D11',$this->subject);
        $active_sheet->setCellValue('H8',"Курс: ".$this->id_course);
        $active_sheet->setCellValue('H9',"Семестр: ".$this->id_term);
        $active_sheet->setCellValue('D12',$this->teacher);
        $active_sheet->setCellValue('D13',$this->credit);
        $active_sheet->setCellValue('D13',$this->credit);
        $active_sheet->setCellValue('D14',$this->date_time);

        $row_start = 17;
        $current_row = $row_start;
        for($i=0; $i<count($this->students); $i++){
            $j = $i+1;
            $current_row++;
            $id_student = $this->students[$i]->studentid;
            $query_id = $this->students[$i]->queryid;

            $exammark = DB::select("SELECT NT.ap_exammark AS mark, NT.ap_totalmark as total FROM nitro.totalmarks NT WHERE NT.queryID='$query_id' AND NT.studentID='$id_student'");
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(9);
            $active_sheet->setCellValue('C'.$current_row,$this->students[$i]->fio_student);
            $active_sheet->setCellValue('B'.$current_row,$j);

            $total = (int) round($exammark[0]->total);
            if($total == null){
                $this->middle_ball = $this->middle_ball + str_replace(",",'.',$this->total_mark($total,2));

                $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->group_title);
                $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                );
                $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
            }else{
                $active_sheet->setCellValue('E'.$current_row,$this->total_mark($total,1));
                $active_sheet->setCellValue('F'.$current_row,$this->total_mark($total,2));
                $active_sheet->setCellValue('G'.$current_row,$total);
                $this->middle_ball = $this->middle_ball + str_replace(",",'.',$this->total_mark($total,2));

                $active_sheet->setCellValue('D'.$current_row,$this->students[$i]->group_title);
                $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                );
                $active_sheet->getRowDimension($current_row)->setRowHeight(-1);
            }

        }

        $methodist = $current_row+7;
        $active_sheet->setCellValue('H'.$methodist,$_SESSION['last_first']);
        $methodist += 2;
        $active_sheet->setCellValue('H'.$methodist,$this->director);
		$middle_ball = ($this->middle_ball == 0) ? 0 : round(($this->middle_ball/count($this->students)),2);
		$ball = $current_row+2;
		$active_sheet->setCellValue('G'.$ball,$middle_ball);
		$ball = $current_row+3;
		$active_sheet->setCellValue('G'.$ball,$middle_ball);

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Всех видов практик №" .$this->id_stream . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }
    # Направление на летный семестр
    public function report11($student_id){
        $result = DB::select("SELECT OOO.* FROM ((SELECT TR.subjectcode AS kod,TR.Credits AS credit,TR.TotalMark AS total,TR.subjectnameRU AS name_ru,
        TR.subjectnameKZ AS name_kz, TR.subjectnameEN AS name_en,TR.coursenumber AS course,TR.term AS semester,
        TR.R1,TR.R2,TR.exammark AS exam
        FROM nitro.transcript TR WHERE TR.StudentID='$student_id')
        UNION
        /* 2 запрос*/
        (SELECT NQ.code AS kod,NQ.credits AS credit,NT.ap_totalmark AS total,NE.subjectRU AS name_ru,NE.subjectKZ AS name_kz,NE.subjectEN AS name_en,
        NQ.year AS course,NQ.term AS semester,NJ1.Mark AS R1,NJ2.Mark AS R2,NJ3.Mark AS exam
        FROM nitro.queries NQ 
        LEFT JOIN nitro.totalmarks NT ON NT.queryID=NQ.QueryID
        LEFT JOIN nitro.erwithappealreports NE ON NE.groupID=NT.studygroupID
        LEFT JOIN nitro.journal NJ1 ON NJ1.StudyGroupID=NT.studygroupID AND NJ1.StudentID=NT.studentID AND NJ1.number=1 AND NJ1.isCurrent=1
        LEFT JOIN nitro.journal NJ2 ON NJ2.StudyGroupID=NT.studygroupID AND NJ2.StudentID=NT.studentID AND NJ2.number=2 AND NJ2.isCurrent=1
        LEFT JOIN nitro.journal NJ3 ON NJ3.StudyGroupID=NT.studygroupID AND NJ3.StudentID=NT.studentID AND NJ3.markTypeID=3 AND NJ3.isCurrent=1
        WHERE NQ.StudentID='$student_id')) OOO WHERE OOO.total=0  GROUP BY OOO.name_ru ORDER BY OOO.course DESC,OOO.semester,OOO.name_ru");
        $result2 = DB::select("SELECT * FROM phelper.temp PT WHERE PT.id='$this->user_id' AND PT.studentid='$student_id' LIMIT 1");
        if(count($result) == 0){
            dd("У студента отсутствует задолженности");
        }
        $filename = base_path() . "/public/reports/12.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $active_sheet->setCellValue('E6',$result2[0]->pname);
        $active_sheet->setCellValue('E7',$result2[0]->lang);
        $active_sheet->setCellValue('E8',$result2[0]->form);
        $active_sheet->setCellValue('E9',$result2[0]->fio_student);

        $row_start = 11;
        $current_row = $row_start;
        $all_credit = 0;
        $all_sum = 0;
        for($i=0; $i<count($result); $i++){
            $j = $i+1;
            $current_row++;
            $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
            $active_sheet->getRowDimension($current_row)->setRowHeight(ceil(mb_strlen($result[$i]->name_ru)/41)*17.5);
            $active_sheet->getStyle("B".$current_row.":H".$current_row)->getFont()->setSize(12);
            $active_sheet->setCellValue('B'.$current_row,$j);
            $active_sheet->getStyle('C'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->setCellValue('C'.$current_row,$result[$i]->kod);
            $active_sheet->getStyle('D'.$current_row)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $start_date = $this->get_year($result[$i]->course);
            $next_year = $start_date+1;
            $study_year = $start_date.'-'.$next_year.'/'.$result[$i]->semester;
            $active_sheet->setCellValue('D'.$current_row,$result[$i]->name_ru);
            $active_sheet->setCellValue('E'.$current_row,$study_year);
            $active_sheet->setCellValue('F'.$current_row,$result[$i]->credit);
            $all_credit += $result[$i]->credit;
            if($this->id_study_form < 5){
                $all_sum += $result[$i]->credit * env('FULL_TIME');
                $active_sheet->setCellValue('G'.$current_row,$result[$i]->credit * env('FULL_TIME'));

            }
            if($this->id_study_form == 6 OR $this->id_study_form == 7){
                $all_sum += $result[$i]->credit * env('PART_TIME');
                $active_sheet->setCellValue('G'.$current_row,$result[$i]->credit * env('PART_TIME'));
            }
            if($this->id_study_form == 9 OR $this->id_study_form == 10 OR $this->id_study_form == 12){
                $all_sum += $result[$i]->credit * env('MASTER');
                $active_sheet->setCellValue('G'.$current_row,$result[$i]->credit * env('MASTER'));
            }
        }
        $active_sheet->insertNewRowBefore($current_row +1, 1);
        $current_row++;
        $active_sheet->setCellValue('E'.$current_row,'Итого');
        $active_sheet->setCellValue('F'.$current_row,$all_credit);
        $active_sheet->setCellValue('G'.$current_row,$all_sum);

        $methodist = $current_row+6;
        $active_sheet->setCellValue('G'.$methodist,$_SESSION['last_first']);
        $methodist += 8;
        $active_sheet->setCellValue('G'.$methodist,'Дата: '.date("d.m.Y"));

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Направление на летный семестр.xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    # Рейтинг обучающихся
    public function report12(){
        date_default_timezone_set('Asia/Almaty');
        $filename = base_path() . "/public/reports/rating_students.xls";
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
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
                    WHERE ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.PaymentFormID=1 AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND 
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

                    WHERE ST.coursenumber<>0 AND ST.isstudent=1 AND 
                          ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND
                          QQ.studentID =ST.studentID) POD ON POD.student_id=NT.StudentID AND NT.coursenumber='$this->id_course') PID
LEFT JOIN (SELECT SB.SubjectCode,A.studentid,SB.SubjectID
                    FROM
                    (SELECT Q.subjectid as tupSubjectID,Q.studentid
                    
                    FROM nitro.queries AS Q ,
                    nitro.students AS ST
                    WHERE ST.coursenumber<>0 AND ST.isstudent=1 AND 
                    ST.professionid='$this->id_profession' AND ST.studyformid='$this->id_study_form' AND ST.PaymentFormID=1 AND ST.studylanguageid='$this->id_study_lang' AND ST.CourseNumber='$this->id_course' AND ST.specializationID='$this->id_specialization' AND
                    Q.studentID =ST.studentID AND Q.year=ST.coursenumber) AS A 
                    LEFT JOIN nitro.tupsubjects TS ON TS.tupsubjectid=A.tupSubjectID
                    LEFT JOIN nitro.subjects SB ON SB.subjectid=TS.subjectid
                    ) OOO ON OOO.studentid=PID.StudentID AND OOO.SubjectCode=PID.subjectcode
WHERE OOO.SubjectCode IS NULL)) PIS ORDER BY SubjectNameRU,lastname,firstname,patronymic");


        if(!$result){
            dd("По выбранным данным не найдено студентов");
        }
        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$this->id_profession' AND PC.deleted IS NULL)");

        $university = $result1[0]->facultyNameKZ." / ".$result1[0]->facultyNameRU;
        $arr_student    = [];
        $arr_gpa    = [];
        $arr_credit = [];
        $row_start = 16;
        $res_profession = DB::select("SELECT * FROM nitro.professions WHERE professionID='$this->id_profession'");
        $res_lang = DB::select("SELECT * FROM nitro.studylanguages WHERE Id='$this->id_study_lang'");
        $res_spec = DB::select("SELECT * FROM nitro.specializations WHERE id='$this->id_specialization'");
        $profession = $res_profession[0]->professionCode."-".$res_profession[0]->professionNameRU;
        $study_form = $this->id_study_form;
        $course_number = $this->id_course;
        $lang = $res_lang[0]->NameRU;
        $lang = mb_substr($lang,0,3);
        $spec_name = $res_spec[0]->nameru;
        $active_sheet->setCellValue('B5', 'ОҚУШЫЛАР РЕЙТІҢІ /  РЕЙТИНГ ОБУЧАЮЩИХСЯ');

        $str = "Мамандығы/Специальность, шифр: ";
        $active_sheet->setCellValue('C4',$university);
        $active_sheet->setCellValue('B6',$str.$profession.",        оқу тілі/язык обучения: ".$lang.",        курс: ".$course_number);
        $active_sheet->setCellValue('B7',"Оқу түрі/Форма обучения: ".$this->get_study_form($study_form,1).",        оқу жылы/учебный год: 2016/2017,        Түскен жылы/Год поступления: ".$this->get_year($course_number).",        оқу мерзімі/срок обучения: ".$this->get_study_form($study_form,2));
        $active_sheet->setCellValue('B8',"мамандануы/cпециализации: ".$spec_name.",        күні/дата: ".date("d.m.Y H:i:s"));

        $current_row = $row_start;
        for($i=0; $i< count($result); $i++) {
            $studentid = $result[$i]->studentid;
            $item_student = array_search($studentid,$arr_student);
            if($item_student === FALSE){
                $arr_student[] = $studentid;
                $arr_gpa[] = 0;
                $arr_credit[] = 0;
                $j = $i + 1;
                $current_row++;
                $active_sheet->insertNewRowBefore($row_start + $i+1, 1);
                $active_sheet->setCellValue('A'.$current_row,$j);
                $fio = $result[$i]->lastname." ".$result[$i]->firstname." ".$result[$i]->patronymic;
                $active_sheet->getStyle('B'.$current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                );
                $active_sheet->setCellValue('B'.$current_row,$fio);
                $active_sheet->setCellValue('C'.$current_row,$result[$i]->gnt);
            }

            $arr_gpa[$item_student] += str_replace(",",".",$this->total_mark($result[$i]->totalmark,2))* (int) $result[$i]->credits;
            $arr_credit[$item_student] += (int) $result[$i]->credits;
        }
        $current = $row_start;
        for($i=0; $i<count($arr_student); $i++){
            $current++;
            if($arr_credit[$i] == 0){
                $active_sheet->setCellValue('D'.$current,0);
            }else{
                $gpa = round($arr_gpa[$i]/$arr_credit[$i],2);
                $gpa = str_replace(".",",",$gpa);
                $active_sheet->setCellValue('D'.$current,$gpa);
            }
        }
        $filename = 'сводная_'.date("d.m.Y H:i:s");
        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=$filename.xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
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
                    $god = (int) $current_year - 2;
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
    
    public function tutorStream($id_tutor,$title){
        $result = TutorStream::create([
            'tutor_id' => $id_tutor, 'title' => $title
        ]);
        return $result->id;
    }

    public function transcript(Request $request)
    {
//        $this->id_student = 13252;
        $this->id_profession = (int) $request->input('id_profession');
        $this->id_study_form = (int) $request->input('id_study_form');
        //$this->id_term = $request->input('id_term');
        $this->id_study_lang = (int) $request->input('id_study_lang');
        $this->id_student = (int)$request->input('id_student');
        $this->id_status = (int) $request->input('id_status');
        $this->trans_type = (int) $request->input('trans_type');
        $this->id_lang = (int) $request->input('id_lang');
        $this->id_course = (int) $request->input('id_course');
        $this->date_time = ($request->input('date_time')) ? $request->input('date_time') : date("d.m.Y");

        switch (true){
            case ($this->id_study_form == 9) or ($this->id_study_form == 10) or ($this->id_study_form == 12) or ($this->id_study_form == 13):
                $this->transcript_magistr_and_doctor();
                break;
        }

        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$this->id_profession' AND PC.deleted IS NULL)");

        $university = $result1[0]->facultyNameRU;

        $result = DB::select("SELECT CONCAT(NS.lastname,' ',NS.firstname,' ',NS.patronymic) AS fio, CONCAT(NP.professionCode,'-',NP.professionNameRU) AS pname, NSL.NameRU AS lang,NS.StartDate FROM nitro.students NS
                                LEFT JOIN nitro.professions NP ON NP.professionID=NS.ProfessionID
                                LEFT JOIN nitro.studylanguages NSL ON NSL.Id=NS.StudyLanguageID
                                WHERE NS.StudentID='$this->id_student'");
        $start_date = substr($result[0]->StartDate, 0, 4);
        $student_inicial = $result[0]->fio;

        switch (true){
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $filename = base_path() . "/public/reports/top.xls";
                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                switch (true){
                    case ($this->id_study_form == 6) OR ($this->id_study_form == 7):
                        $filename = base_path() . "/public/reports/top_ects2.xls";
                        break;
                    default:
                        $filename = base_path() . "/public/reports/top_ects.xls";
                        break;
                }
                break;
        }

        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objReader->setReadDataOnly(false);

        $objPHPExcel = $objReader->load($filename);

        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $active_sheet->setCellValue('M8', $university);
        $active_sheet->setCellValue('M10', $start_date);
        $active_sheet->setCellValue('M12', $this->get_study_form($this->id_study_form,2));
        if($this->id_status == 1){
            $student = DB::select("SELECT * FROM phelper.temp WHERE studentid='$this->id_student' LIMIT 1");
            $student1 = DB::select("SELECT CONCAT(ST.lastname,' ',ST.firstname,' ', ST.patronymic) AS fio_student, CONCAT(PS.professionCode,'-',PS.professionNameRU) AS pname, NS.NameRU AS lang FROM nitro.students ST
                                        INNER JOIN nitro.professions PS ON PS.professionID=ST.ProfessionID
                                        INNER JOIN nitro.studylanguages NS ON NS.Id=ST.StudyLanguageID
                                        WHERE ST.StudentID='$this->id_student'");
            if($student){
                $active_sheet->setCellValue('M7', $student[0]->fio_student);
                $active_sheet->setCellValue('M9', $student[0]->pname);
                $active_sheet->setCellValue('M11', $student[0]->lang);
            }elseif($student1){
                $active_sheet->setCellValue('M7', $student1[0]->fio_student);
                $active_sheet->setCellValue('M9', $student1[0]->pname);
                $active_sheet->setCellValue('M11', $student1[0]->lang);
            }
            else{
                return redirect('user/transcript');
            }
        }else{
            $active_sheet->setCellValue('M7', $result[0]->fio);
            $active_sheet->setCellValue('M9', $result[0]->pname);
            $active_sheet->setCellValue('M11', $result[0]->lang);
        }

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
            'font' => array(
                'size' => 9,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $data = $request->input('id_term');
        $term = '';
        for($i=0; $i<count($data); $i++){
            $term .= $data[$i].',';
        }
        $term = rtrim($term,',');
        switch ($term) {
            // Все
            case 00:
                $semester1 = $this->sql(1,1);
                $semester2 = $this->sql(1,2);
                $dop_sem1  = $this->sql(1,0);

                $semester3 = $this->sql(2,1);
                $semester4 = $this->sql(2,2);
                $dop_sem2  = $this->sql(2,0);

                $semester5 = $this->sql(3,1);
                $semester6 = $this->sql(3,2);
                $dop_sem3  = $this->sql(3,0);

                $semester7 = $this->sql(4,1);
                $semester8 = $this->sql(4,2);
                $dop_sem4  = $this->sql(4,0);
                if (!empty($semester1)) {
                    // если есть предметы на 1 семестре
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,1);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 1 семестр");
                }
                // 2 семестр
                if (!empty($semester2)) {
                    // если есть предметы на 2 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester2,$active_sheet,$styleArray,$current_row1,2);
                }
                # Дополнительный семестр на 1 курсе
                if(!empty($dop_sem1)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem1,$active_sheet,$styleArray,$current_row1);
                }
                // 3 семестр
                if (!empty($semester3)) {
                    // если есть предметы на 3 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester3,$active_sheet,$styleArray,$current_row1,3);
                }
                // 4 семестр
                if (!empty($semester4)) {
                    // если есть предметы на 4 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester4,$active_sheet,$styleArray,$current_row1,4);
                }
                # Дополнительный семестр на 2 курсе
                if(!empty($dop_sem2)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem2,$active_sheet,$styleArray,$current_row1);
                }
                // 5 семестр
                if (!empty($semester5)) {
                    // если есть предметы на 5 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester5,$active_sheet,$styleArray,$current_row1,5);
                }
                // 6 семестр
                if (!empty($semester6)) {
                    // если есть предметы на 6 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester6,$active_sheet,$styleArray,$current_row1,6);
                }
                # Дополнительный семестр на 3 курсе
                if(!empty($dop_sem3)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem3,$active_sheet,$styleArray,$current_row1);
                }
                // 7 семестр
                if (!empty($semester7)) {
                    // если есть предметы на 7 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester7,$active_sheet,$styleArray,$current_row1,7);
                }
                // 8 семестр
                if (!empty($semester8)) {
                    // если есть предметы на 8 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester8,$active_sheet,$styleArray,$current_row1,8);
                }
                # Дополнительный семестр на 4 курсе
                if(!empty($dop_sem4)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem4,$active_sheet,$styleArray,$current_row1);
                }
                break;
            default:
                switch(true){
                    case ($term == '1,2'):
                        $course = 1;
                        $id_term = '1,2';
                        break;
                    case ($term == '3,4'):
                        $course = 2;
                        $id_term = '1,2';
                        break;
                    case ($term == '5,6'):
                        $course = 3;
                        $id_term = '1,2';
                        break;
                    case ($term == '7,8'):
                        $course = 4;
                        $id_term = '1,2';
                        break;
                    case ($term == '1'):
                        $course = 1;
                        $id_term = '1';
                        break;
                    case ($term == '2'):
                        $course = 1;
                        $id_term = '2';
                        break;
                    case ($term == '3'):
                        $course = 2;
                        $id_term = '1';
                        break;
                    case ($term == '4'):
                        $course = 2;
                        $id_term = '2';
                        break;
                    default:
                        $course = 1;
                        $id_term = '1,2';
                        break;
                }
                $dop_sem1  = $this->sql($course,0);
                $arr = $this->sql($course,$id_term);
                if(!empty($data[0])){
                    $n1 = $data[0];
                }else{
                    $n1 = 1;
                }
                if(!empty($data[1])){
                    $n2 = $data[1];
                }else{
                    $n2 = 2;
                }
                $semester1 = [];
                $semester2 = [];
                for($j=0; $j<count($arr); $j++){
                    if($arr[$j]->semester == 1){
                        $semester1[] = $arr[$j];
                    }else{
                        $semester2[] = $arr[$j];
                    }
                }
                if (!empty($semester1)) {
                    // если есть предметы на 1 семестре
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,$n1);
                }
                if (!empty($semester2)) {
                    // если есть предметы на 1 семестре
                    if(isset($zar_credit)){
                        $current_row1 = $zar_credit;
                    }else{
                        $current_row1 = 15;
                    }

                    $zar_credit = $this->bac_line1($semester2,$active_sheet,$styleArray,$current_row1,$n2);
                }
                # Дополнительный семестр на 1 курсе
                if(!empty($dop_sem1)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem1,$active_sheet,$styleArray,$current_row1);
                }
                break;
        }
        $style_array = array(
            'font' => array(
                'size' => 7,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        # если есть практики
        $practice = $this->practice();
        if (!empty($practice)) {
            $zar_credit++;
            $title = 'Кәсіптік практиканы өтті / Has passed professional practice / Прошел профессиональные практики';
            $header1 = 'Кәсіптік практикалардың түрлері/The form of professional practice/Виды профессиональных практик';
            $header2 = 'Өту кезеңі/Term/Период прохождения';
            $zar_credit = $this->setHeader($active_sheet,$zar_credit,$styleArray,$style_array,$title,$header1,$header2);

            $current_row = $zar_credit;
            for ($i = 0; $i < count($practice); $i++) {
                $period = $this->semester($practice[$i]->course,$practice[$i]->semester)  . " семестр";
                $active_sheet->insertNewRowBefore($current_row + 1, 1);
                $current_row++;
                $active_sheet->mergeCells('A' . $current_row . ':I' . $current_row);
                switch($this->id_lang){
                    case 1:
                        $active_sheet->getRowDimension($current_row)->setRowHeight(ceil(mb_strlen($practice[$i]->name_kz) / 35) * 17.5);
                        $active_sheet->getStyle('A' . $current_row)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $current_row . ":AA" . $current_row)->getFont()->setSize(9);
                        $active_sheet->setCellValue('A' . $current_row, $practice[$i]->name_kz);
                        break;

                    case 2:
                        $active_sheet->getRowDimension($current_row)->setRowHeight(ceil(mb_strlen($practice[$i]->name_ru) / 35) * 17.5);
                        $active_sheet->getStyle('A' . $current_row)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $current_row . ":AA" . $current_row)->getFont()->setSize(9);
                        $active_sheet->setCellValue('A' . $current_row, $practice[$i]->name_ru);
                        break;

                    case 3:
                        $active_sheet->getRowDimension($current_row)->setRowHeight(ceil(mb_strlen($practice[$i]->name_en) / 35) * 17.5);
                        $active_sheet->getStyle('A' . $current_row)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $current_row . ":AA" . $current_row)->getFont()->setSize(9);
                        $active_sheet->setCellValue('A' . $current_row, $practice[$i]->name_en);
                        break;
                }


                $active_sheet->mergeCells('J' . $current_row . ':N' . $current_row);
                $active_sheet->getStyle('J' . $current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('J' . $current_row, $period);
                $active_sheet->mergeCells('O' . $current_row . ':Q' . $current_row);
                $active_sheet->getStyle('O' . $current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('O' . $current_row, $practice[$i]->credit);

                $practice_total_mark = (int)round($practice[$i]->total);

                $active_sheet->mergeCells('R' . $current_row . ':T' . $current_row);
                $active_sheet->getStyle('R' . $current_row)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->mergeCells('U' . $current_row . ':W' . $current_row);
                $active_sheet->mergeCells('X' . $current_row . ':Y' . $current_row);
                $active_sheet->mergeCells('Z' . $current_row . ':AA' . $current_row);

                switch(true){
                    case ($this->trans_type < 2):
                        $active_sheet->setCellValue('R' . $current_row, $this->total_mark($practice_total_mark));
                        break;
                    case ($this->trans_type > 1):
                        switch ($this->is_type_pratice($practice[$i]->name_ru)){
                            case 100:
                                // произ
                                $ects_credit = $this->ects($this->id_study_form,'practice', $practice[$i]->credit, 'pro');
                                $this->all_credit_ects += $ects_credit;
                                $active_sheet->setCellValue('R' . $current_row, $ects_credit);
                                break;
                            case 101:
                                // пед
                                $ects_credit = $this->ects($this->id_study_form,'practice', $practice[$i]->credit, 'ped');
                                $this->all_credit_ects += $ects_credit;
                                $active_sheet->setCellValue('R' . $current_row, $ects_credit);
                                break;
                            case 102:
                                // учеб
                                $ects_credit = $this->ects($this->id_study_form,'practice', $practice[$i]->credit, 'edu');
                                $this->all_credit_ects += $ects_credit;
                                $active_sheet->setCellValue('R' . $current_row, $ects_credit);
                                break;
                            case 0:
                                // учеб
                                $ects_credit = $this->ects($this->id_study_form,'theory', $practice[$i]->credit);
                                $this->all_credit_ects += $ects_credit;
                                $active_sheet->setCellValue('R' . $current_row, $ects_credit);
                                break;
                        }
                        break;
                }

                $active_sheet->setCellValue('U' . $current_row, $this->total_mark($practice_total_mark, 2));
                $active_sheet->setCellValue('X' . $current_row, $practice_total_mark);
                $active_sheet->setCellValue('Z' . $current_row, $this->total_mark($practice_total_mark, 3));
            }
            $zar_credit = $current_row;
        }

        # если есть государственные аттестации
        $general = DB::select("SELECT S.subjectru,S.subjectkz,S.subjecten,S.date,S.number,G.mark,G.ap_mark,G.credits,G.course,G.term,NG.tupsubjectID
            FROM sacreports S
            LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
            LEFT JOIN nitro.generalexams NG ON NG.examID=S.examID
            WHERE G.studentID='$this->id_student' AND G.typeID < 4 AND G.iscurrent=1");
        if (!empty($general)) {
            # есть
            $zar_credit++;
            $title = 'Мемлекеттік қорытынды аттестация / Final state attestation / Итоговая аттестация обучающихся';
            $header1 = 'Мемлекеттік емтиханды тапсырды/Has passed the state examinations/Сдал государственные экзамены';
            $header2 = 'МАК-тың хаттамасының күні және нөмірі/Date and number of the report  SAC/Дата и номер протокола ГАК';
            $zar_credit = $this->setHeader($active_sheet,$zar_credit,$styleArray,$style_array,$title,$header1,$header2);
            # государственный экзамен
            $zar_credit++;
            $active_sheet->getRowDimension($zar_credit)->setRowHeight(25);
            $active_sheet->getStyle('A' . $zar_credit . ':I' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
            $active_sheet->setCellValue('A' . $zar_credit, "Пәндер бойынша/ At courses/ По  дисциплинам");

            $active_sheet->getStyle('J' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('J' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->getStyle('J' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            $active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            $active_sheet->getStyle('R' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                PHPExcel_Style_Border::BORDER_THIN
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);


            $all_general_credit = 0; $all_general_zach_credit = 0; $all_general_gpa = 0;
            for($i=0; $i<count($general); $i++){
                $zar_credit++;
                $protocol_date_number = substr($general[$i]->date, 8, 2) . "." . substr($general[$i]->date, 5, 2) . "." . substr($general[$i]->date, 0, 4) . " / " . $general[$i]->number;
                $active_sheet->insertNewRowBefore($zar_credit, 1);
                $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
                $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                )->setWrapText(true);
                switch($this->id_lang){
                    case 1:
                        $active_sheet->setCellValue('A' . $zar_credit, $general[$i]->subjectkz);
                        break;
                    case 2:
                        $active_sheet->setCellValue('A' . $zar_credit, $general[$i]->subjectru);
                        break;
                    case 3:
                        $active_sheet->setCellValue('A' . $zar_credit, $general[$i]->subjecten);
                        break;
                }
                $active_sheet->mergeCells('J' . $zar_credit . ':N' . $zar_credit);
                $active_sheet->setCellValue('J' . $zar_credit, $protocol_date_number);
                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->setCellValue('O' . $zar_credit, $general[$i]->credits);

                $general_total_mark = (int)round($general[$i]->ap_mark);
                if(empty($general[$i]->tupsubjectID)){
                    $all_general_credit += $general[$i]->credits;
                    $all_general_gpa += (int) $general[$i]->credits * str_replace(',','.',$this->total_mark($general_total_mark, 2));
                }
                if($general_total_mark > 49){
                    $all_general_zach_credit += $general[$i]->credits;
                }

                $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                switch(true){
                    case ($this->trans_type < 2):
                        $active_sheet->setCellValue('R' . $zar_credit, $this->total_mark($general_total_mark));
                        break;
                    case ($this->trans_type > 1):
                        $ects_credit = $this->ects($this->id_study_form,'exam',$general[$i]->credits,'gos');
                        $this->all_credit_ects += $ects_credit;
                        $active_sheet->setCellValue('R' . $zar_credit, $ects_credit);
                        break;
                }

                $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($general_total_mark, 2));
                $active_sheet->setCellValue('X' . $zar_credit, $general_total_mark);
                $active_sheet->setCellValue('Z' . $zar_credit, $this->total_mark($general_total_mark, 3));
            }
            $this->all_credit += $all_general_credit;
            $this->all_gpa += $all_general_gpa;

            // Первая строка
            $zar_credit++;
            $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);

            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            switch ($this->id_profession){
                case 51:
                    $active_sheet->setCellValue('O' . $zar_credit, "Тіркелген кредиттер");
                    break;

                default:
                    $active_sheet->setCellValue('O' . $zar_credit, "Зарегистрировано кредитов");
                    break;
            }

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            switch ($this->id_profession){
                case 51:
                    $active_sheet->setCellValue('U' . $zar_credit, "Сыналған кредиттер");
                    break;

                default:
                    $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");
                    break;
            }

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $active_sheet->setCellValue('Z' . $zar_credit, "GPA");

            // Вторая строка
            $zar_credit++;
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
            );

            switch ($this->id_profession){
                case 51:
                    $active_sheet->setCellValue('A' . $zar_credit, "Барлығы:");
                    break;

                default:
                    $active_sheet->setCellValue('A' . $zar_credit, "Всего:");
                    break;
            }

            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('O' . $zar_credit, $all_general_credit);

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('U' . $zar_credit, $all_general_zach_credit);

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            if($all_general_credit == 0){
                $active_sheet->setCellValue('Z' . $zar_credit, 0);
            }else{
                $active_sheet->setCellValue('Z' . $zar_credit, round(($all_general_gpa) / ($all_general_credit), 2));
            }
        }

        # Дипломная работа
        $diplom = DB::select("SELECT G.nameworken,G.nameworkkz,G.nameworkru,S.number,S.date,G.credits,G.mark FROM sacreports S
                                LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
                                WHERE G.studentID='$this->id_student' AND S.typeID=7");
        if (!empty($diplom)) {
            # есть
            $zar_credit++;
            $title = 'Дипломдық жұмысты орындады және қорғады/ Has executed and has protected degree work/ Выполнил (а) и защитил (а) дипломную работу';
            $header1 = 'Дипломдық жұмыстың тақырыбы/ Theme of degree work/ Тема дипломной работы';
            $header2 = 'МАК-тың хаттамасының күні және нөмірі/Date and number of the report  SAC/Дата и номер протокола ГАК';
            $zar_credit = $this->setHeader($active_sheet,$zar_credit,$styleArray,$style_array,$title,$header1,$header2);

            # дипломная работа
            $protocol_date_number = substr($diplom[0]->date, 8, 2) . "." . substr($diplom[0]->date, 5, 2) . "." . substr($diplom[0]->date, 0, 4) . " / " . $diplom[0]->number;
            $zar_credit++;
            $active_sheet->insertNewRowBefore($zar_credit, 1);
            $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
            switch($this->id_lang){
                case 1:
                    $active_sheet->setCellValue('A' . $zar_credit, $diplom[0]->nameworkkz);
                    break;
                case 2:
                    $active_sheet->setCellValue('A' . $zar_credit, $diplom[0]->nameworkru);
                    break;
                case 3:
                    $active_sheet->setCellValue('A' . $zar_credit, $diplom[0]->nameworken);
                    break;
            }


            $active_sheet->mergeCells('J' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->setCellValue('J' . $zar_credit, $protocol_date_number);
            $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
            $active_sheet->setCellValue('O' . $zar_credit, $diplom[0]->credits);

            $general_total_mark = (int)round($diplom[0]->mark);
            $this->all_credit += $diplom[0]->credits;
            $this->all_gpa += (int) $diplom[0]->credits * str_replace(',','.',$this->total_mark($general_total_mark, 2));


            $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
            $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            switch(true){
                case ($this->trans_type < 2):
                    $active_sheet->setCellValue('R' . $zar_credit, $this->total_mark($general_total_mark));
                    break;
                case ($this->trans_type > 1):
                    $ects_credit = $this->ects($this->id_study_form,'exam', $diplom[0]->credits,'protection');
                    $this->all_credit_ects += $ects_credit;
                    $active_sheet->setCellValue('R' . $zar_credit, $ects_credit);
                    $this->all_credit_ects += $ects_credit;
                    break;
            }

            $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($general_total_mark, 2));
            $active_sheet->setCellValue('X' . $zar_credit, $general_total_mark);
            $active_sheet->setCellValue('Z' . $zar_credit, $this->total_mark($general_total_mark, 3));

            // Первая строка
            $zar_credit++;
            $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);

            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            switch ($this->id_profession){
                case 51:
                    $active_sheet->setCellValue('O' . $zar_credit, "Тіркелген кредиттер");
                    break;

                default:
                    $active_sheet->setCellValue('O' . $zar_credit, "Зарегистрировано кредитов");
                    break;
            }

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            switch ($this->id_profession){
                case 51:
                    $active_sheet->setCellValue('U' . $zar_credit, "Сыналған кредиттер");
                    break;

                default:
                    $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");
                    break;
            }

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $active_sheet->setCellValue('Z' . $zar_credit, "GPA");

            // Вторая строка
            $zar_credit++;
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
            );

            switch ($this->id_profession){
                case 51:
                    $active_sheet->setCellValue('A' . $zar_credit, "Барлығы:");
                    break;

                default:
                    $active_sheet->setCellValue('A' . $zar_credit, "Всего:");
                    break;
            }

            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('O' . $zar_credit, $diplom[0]->credits);

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('U' . $zar_credit, $diplom[0]->credits);

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $bal = str_replace(',','.',$this->total_mark($general_total_mark, 2));
            $active_sheet->setCellValue('Z' . $zar_credit, round(($bal * $diplom[0]->credits) / ($diplom[0]->credits), 2));
        }

        $zar_credit = $zar_credit + 2;
        //$active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->getFont()->setBold(true);
        $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
        $active_sheet->setCellValue('A' . $zar_credit, "Жалпы кредит саны/ Total Hours Passed/ Общее число кредитов: ");

        $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
        //$active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit);
        $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
        //$active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->getFont()->setBold(true);
        $active_sheet->setCellValue('X' . $zar_credit, "GPA=");

        $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
        //$active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('Z' . $zar_credit, round(($this->all_gpa / $this->all_credit), 2));
        switch (true){
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $zar_credit += 1;
                $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
                $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
                $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->getFont()->setBold(true);
                $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
                $active_sheet->setCellValue('A' . $zar_credit, "ECTS кредиттер саны/Credit hours ECTS/Количество кредитов ECTS: ");

                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit_ects);
                break;
        }
        $zar_credit = $zar_credit + 2;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch($this->id_profession){
            case 51:
                $active_sheet->setCellValue('B' . $zar_credit, "* - Ағылшын тілінде оқылған пәндер");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "* - Дисциплины, изученные на английском языке");
                break;
        }


        switch (true){
            case ($this->trans_type == 1) OR ($this->trans_type == 3):
                $zar_credit += 2;
                $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->setCellValue('B' . $zar_credit, "Ректор КазНПУ имени Абая");
                $active_sheet->mergeCells('V' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->setCellValue('V' . $zar_credit, "Балыкбаев Т.О.");
        }
        $zar_credit = $zar_credit + 2;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch($this->id_profession){
            case 51:
                $active_sheet->setCellValue('B' . $zar_credit, "Тіркеу басқармасының бастығы");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "Начальник управления регистрации");
                break;
        }

        $active_sheet->mergeCells('V' . $zar_credit . ':AA' . $zar_credit);
        $active_sheet->setCellValue('V' . $zar_credit, "Кантарбаева Р.М.");

        $zar_credit = $zar_credit + 2;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch($this->id_profession){
            case 51:
                $active_sheet->setCellValue('B' . $zar_credit, "Көшірме дұрыс: әдіскер-тіркеуші");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "Выписка верна: методист-регистратор");
                break;
        }

        $active_sheet->mergeCells('V' . $zar_credit . ':AA' . $zar_credit);
        $active_sheet->setCellValue('V' . $zar_credit, $_SESSION['last_first']);

        $zar_credit++;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch($this->id_profession){
            case 51:
                $active_sheet->setCellValue('B' . $zar_credit, "Тіркеу № _______");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "Регистрационный № _______");
                break;
        }


        $zar_credit++;
        $active_sheet->mergeCells('B' . $zar_credit . ':I' . $zar_credit);
        $active_sheet->setCellValue('B' . $zar_credit, $this->date_time);

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Транскрипт " . $student_inicial . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }

    //
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

    ##### Магистратура #####
    public function transcript_magistr_and_doctor()
    {
        $result1 = DB::select("SELECT * FROM faculties F WHERE FacultyID IN(SELECT 
                               C.FacultyID 
                               FROM profession_cafedra PC
                               LEFT JOIN cafedras C ON C.cafedraID=PC.cafedraID
                               WHERE PC.professionID='$this->id_profession' AND PC.deleted IS NULL)");

        $university = $result1[0]->facultyNameRU;

        $result = DB::select("SELECT CONCAT(NS.lastname,' ',NS.firstname,' ',NS.patronymic) AS fio, CONCAT(NP.professionCode,'-',NP.professionNameRU) AS pname, NSL.NameRU AS lang,NS.StartDate FROM nitro.students NS
                                LEFT JOIN nitro.professions NP ON NP.professionID=NS.ProfessionID
                                LEFT JOIN nitro.studylanguages NSL ON NSL.Id=NS.StudyLanguageID
                                WHERE NS.StudentID='$this->id_student'");
        $start_date = substr($result[0]->StartDate, 0, 4);
        $student_inicial = $result[0]->fio;

        switch (true){
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $filename = base_path() . "/public/reports/top.xls";
                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $filename = base_path() . "/public/reports/top_ects.xls";
                break;
        }
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($filename);
        $objPHPExcel->setActiveSheetIndex(0);
        $active_sheet = $objPHPExcel->getActiveSheet();
        $active_sheet->setCellValue('M8', $university);
        $active_sheet->setCellValue('M10', $start_date);
        $active_sheet->setCellValue('M12', $this->get_study_form($this->id_study_form,2));
        if($this->id_status == 1){
            $student = DB::select("SELECT * FROM phelper.temp WHERE studentid='$this->id_student' LIMIT 1");
            if($student){
                $active_sheet->setCellValue('M7', $student[0]->fio_student);
                $active_sheet->setCellValue('M9', $student[0]->pname);
                $active_sheet->setCellValue('M11', $student[0]->lang);
            }else{
                return redirect('user/transcript');
            }
        }else{
            $active_sheet->setCellValue('M7', $result[0]->fio);
            $active_sheet->setCellValue('M9', $result[0]->pname);
            $active_sheet->setCellValue('M11', $result[0]->lang);
        }
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
            'font' => array(
                'size' => 9,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        switch ($this->id_term) {
            // Все
            case 0:
                $semester1 = $this->sql(1,1);
                $semester2 = $this->sql(1,2);
                $dop_sem1  = $this->sql(1,0);
                $semester3 = $this->sql(2,1);
                $semester4 = $this->sql(2,2);
                $dop_sem2  = $this->sql(2,0);
                $semester5 = $this->sql(3,1);
                $semester6 = $this->sql(3,2);
                $dop_sem3  = $this->sql(3,0);
                $semester7 = $this->sql(4,1);
                $semester8 = $this->sql(4,2);
                $dop_sem4  = $this->sql(4,0);
                if (!empty($semester1)) {
                    // если есть предметы на 1 семестре
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,1);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 1 семестр");
                }
                // 2 семестр
                if (!empty($semester2)) {
                    // если есть предметы на 2 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester2,$active_sheet,$styleArray,$current_row1,2);
                }
                # Дополнительный семестр на 1 курсе
                if(!empty($dop_sem1)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem1,$active_sheet,$styleArray,$current_row1);
                }
                // 3 семестр
                if (!empty($semester3)) {
                    // если есть предметы на 3 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester3,$active_sheet,$styleArray,$current_row1,3);
                }
                // 4 семестр
                if (!empty($semester4)) {
                    // если есть предметы на 4 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester4,$active_sheet,$styleArray,$current_row1,4);
                }
                # Дополнительный семестр на 2 курсе
                if(!empty($dop_sem2)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem2,$active_sheet,$styleArray,$current_row1);
                }
                // 5 семестр
                if (!empty($semester5)) {
                    // если есть предметы на 5 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester5,$active_sheet,$styleArray,$current_row1,5);
                }
                // 6 семестр
                if (!empty($semester6)) {
                    // если есть предметы на 6 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester6,$active_sheet,$styleArray,$current_row1,6);
                }
                # Дополнительный семестр на 3 курсе
                if(!empty($dop_sem3)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem3,$active_sheet,$styleArray,$current_row1);
                }
                // 7 семестр
                if (!empty($semester7)) {
                    // если есть предметы на 7 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester7,$active_sheet,$styleArray,$current_row1,7);
                }
                // 8 семестр
                if (!empty($semester8)) {
                    // если есть предметы на 8 семестре
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->bac_line1($semester8,$active_sheet,$styleArray,$current_row1,8);
                }
                # Дополнительный семестр на 4 курсе
                if(!empty($dop_sem4)){
                    $current_row1 = $zar_credit;
                    $zar_credit = $this->dop_semester($dop_sem4,$active_sheet,$styleArray,$current_row1);
                }
                break;
            // 1 семестр
            case 1:
                $semester1 = $this->sql(1,1);
                if (!empty($semester1)) {
                    // если есть предметы на 1 семестре
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,1);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 1 семестр");
                }
                break;
            // 2 семестр
            case 2:
                $semester1 = $this->sql(1,2);
                if (!empty($semester1)) {
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,2);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 2 семестр");
                }
                break;
            // 3 семестр
            case 3:
                $semester1 = $this->sql(2,1);
                if (!empty($semester1)) {
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,3);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 3 семестр");
                }
                break;
            // 4 семестр
            case 4:
                $semester1 = $this->sql(2,2);
                if (!empty($semester1)) {
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,4);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 4 семестр");
                }
                break;
            // 5 семестр
            case 5:
                $semester1 = $this->sql(3,1);
                if (!empty($semester1)) {
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,5);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 5 семестр");
                }
                break;
            // 6 семестр
            case 6:
                $semester1 = $this->sql(3,2);
                if (!empty($semester1)) {
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,6);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 6 семестр");
                }
                break;
            // 7 семестр
            case 7:
                $semester1 = $this->sql(4,1);
                if (!empty($semester1)) {
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,7);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 7 семестр");
                }
                break;
            // 8 семестр
            case 8:
                $semester1 = $this->sql(4,2);
                if (!empty($semester1)) {
                    $current_row1 = 15;
                    $zar_credit = $this->bac_line1($semester1,$active_sheet,$styleArray,$current_row1,8);
                } else {
                    dd("Сессия не закрыто или у студента отсутсвует предметы на 8 семестр");
                }
                break;
            default:
                dd("Не правильно указан семестр");
                break;
        }
        $style_array = array(
            'font' => array(
                'size' => 7,
                'name' => 'Times New Roman'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        # если есть практики
        $practice = $this->practice();
        if (!empty($practice)) {
            # Кәсіптік практиканы өтті
            $zar_credit++;
            $p_title = 'Кәсіптік практиканы өтті / Has passed professional practice / Прошел профессиональные практики';
            $p_header1 = 'Кәсіптік практикалардың түрлері/The form of professional practice/Виды профессиональных практик';
            $p_header2 = 'Өту кезеңі/Term/Период прохождения';
            $zar_credit = $this->setHeader($active_sheet,$zar_credit,$styleArray,$style_array,$p_title,$p_header1,$p_header2);

            for ($i = 0; $i < count($practice); $i++) {
                $practice_name = trim($practice[$i]->name_ru);
                $practice_name = mb_strtolower($practice_name);
                if(preg_match("/практика/", $practice_name)){
                    $period = $this->semester($practice[$i]->course,$practice[$i]->semester) . " семестр";
                    $active_sheet->insertNewRowBefore($zar_credit + 1, 1);
                    $zar_credit++;
                    $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
                    switch ($this->id_lang){
                        case 1:
                            $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($practice[$i]->name_kz) / 35) * 17.5);
                            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                            $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                            $active_sheet->setCellValue('A' . $zar_credit, $practice[$i]->name_kz);
                            break;

                        case 2:
                            $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($practice[$i]->name_ru) / 35) * 17.5);
                            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                            $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                            $active_sheet->setCellValue('A' . $zar_credit, $practice[$i]->name_ru);
                            break;

                        case 3:
                            $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($practice[$i]->name_en) / 35) * 17.5);
                            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                            $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                            $active_sheet->setCellValue('A' . $zar_credit, $practice[$i]->name_en);
                            break;
                    }


                    $active_sheet->mergeCells('J' . $zar_credit . ':N' . $zar_credit);
                    $active_sheet->getStyle('J' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                    $active_sheet->setCellValue('J' . $zar_credit, $period);
                    $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                    $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                    $active_sheet->setCellValue('O' . $zar_credit, $practice[$i]->credit);

                    $practice_total_mark = (int)round($practice[0]->total);

                    $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
                    $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                    $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                    $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                    if(preg_match("/педагогическая/", $practice_name)){
                        $ects_credit = $this->ects($this->id_study_form,'practice',$practice[$i]->credit,'ped');
                        $this->all_credit_ects += $ects_credit;
                    }elseif(preg_match("/научно-иссле/", $practice_name)){
                        $ects_credit = $this->ects($this->id_study_form,'practice',$practice[$i]->credit,'iss');
                        $this->all_credit_ects += $ects_credit;
                    }elseif(preg_match("/производственная/", $practice_name)){
                        $ects_credit = $this->ects($this->id_study_form,'practice',$practice[$i]->credit,'pro');
                        $this->all_credit_ects += $ects_credit;
                    }
                    switch (true){
                        case ($this->trans_type < 2):
                            $active_sheet->setCellValue('R' . $zar_credit, $this->total_mark($practice_total_mark));
                            $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($practice_total_mark, 2));
                            $active_sheet->setCellValue('X' . $zar_credit, $practice_total_mark);
                            $active_sheet->setCellValue('Z' . $zar_credit, $this->total_mark($practice_total_mark, 3));
                            break;
                        case ($this->trans_type > 1):
                            $active_sheet->setCellValue('R' . $zar_credit, $ects_credit);
                            $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($practice_total_mark));
                            $active_sheet->setCellValue('X' . $zar_credit, $this->total_mark($practice_total_mark, 2));
                            $active_sheet->setCellValue('Z' . $zar_credit, $this->total_mark($practice_total_mark, 3));
                            break;
                    }
                }
            }
        }
        # Ғылыми-зерттеу (эксперименттік-зерттеу) жұмысы
        $research = DB::select("CALL phelper.nir($this->id_student);");
        if($research){
            $zar_credit++;
            $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setWrapText(true);
            $active_sheet->getRowDimension($zar_credit)->setRowHeight(25);
            $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->getFont()->setBold(true);
            $active_sheet->setCellValue('A' . $zar_credit, "Ғылыми-зерттеу жұмысы / Scientific Research / Научно-исследовательская работа");

            $zar_credit++;
            switch (true){
                case ($this->trans_type < 2):
                    $active_sheet->getStyle('A' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($style_array);
                    $active_sheet->mergeCells('A' . $zar_credit . ':Q' . $zar_credit);
                    $active_sheet->getStyle('A' . $zar_credit . ':Q' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                    $active_sheet->getRowDimension($zar_credit)->setRowHeight(42);
                    $active_sheet->setCellValue('A' . $zar_credit, "Ғылыми-зерттеу жұмыс түрлері / Types of Scientific Research work / Виды научно-исследовательской работы");

                    $active_sheet->getStyle('R' . $zar_credit . ':W' . $zar_credit)->applyFromArray($style_array);
                    $active_sheet->mergeCells('R' . $zar_credit . ':W' . $zar_credit);
                    $active_sheet->getStyle('R' . $zar_credit . ':W' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

                    $active_sheet->setCellValue('R' . $zar_credit, "Өту кезеңі / Term / Период прохождения");

                    $active_sheet->getStyle('X' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($style_array);
                    $active_sheet->mergeCells('X' . $zar_credit . ':AA' . $zar_credit);
                    $active_sheet->getStyle('X' . $zar_credit . ':AA' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                    $active_sheet->setCellValue('X' . $zar_credit, "Кредиттер саны/ Credit hours/ Количество кредитов");
                    break;
                case ($this->trans_type > 1):
                    $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($style_array);
                    $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
                    $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                    $active_sheet->getRowDimension($zar_credit)->setRowHeight(42);
                    $active_sheet->setCellValue('A' . $zar_credit, "Ғылыми-зерттеу жұмыс түрлері / Types of Scientific Research work / Виды научно-исследовательской работы");

                    $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($style_array);
                    $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
                    $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

                    $active_sheet->setCellValue('O' . $zar_credit, "Өту кезеңі / Term / Период прохождения");

                    $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($style_array);
                    $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                    $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                    $active_sheet->setCellValue('U' . $zar_credit, "Кредиттер саны/ Credit hours/ Количество кредитов");

                    $active_sheet->getStyle('X' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($style_array);
                    $active_sheet->mergeCells('X' . $zar_credit . ':AA' . $zar_credit);
                    $active_sheet->getStyle('X' . $zar_credit . ':AA' . $zar_credit)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                    $active_sheet->setCellValue('X' . $zar_credit, "ECTS кредиттер саны/ Credit hours ECTS/ Количество кредитов ECTS");
                    break;
            }

            for ($i = 0; $i < count($research); $i++) {
                $period = $this->semester($research[$i]->course,$research[$i]->Term) . " семестр";
                $active_sheet->insertNewRowBefore($zar_credit + 1, 1);
                $zar_credit++;

                switch (true){
                    case ($this->trans_type < 2):
                        $active_sheet->mergeCells('A' . $zar_credit . ':Q' . $zar_credit);
                        $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($research[$i]->SubjectNameRU) / 70) * 17.5);
                        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                        switch ($this->id_lang){
                            case 1:
                                $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameKZ);
                                break;
                            case 2:
                                $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameRU);
                                break;
                            case 3:
                                if(empty($research[$i]->SubjectNameENG)){
                                    $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameRU);
                                }else{
                                    $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameENG);
                                }
                                break;
                        }


                        $active_sheet->mergeCells('R' . $zar_credit . ':W' . $zar_credit);
                        $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $active_sheet->setCellValue('R' . $zar_credit, $period);

                        $active_sheet->mergeCells('X' . $zar_credit . ':AA' . $zar_credit);
                        $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->setCellValue('X' . $zar_credit, $research[$i]->creditscount);
                        $this->credit_nir += $research[$i]->creditscount;
                        break;
                    case ($this->trans_type > 1):
                        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
                        $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($research[$i]->SubjectNameRU) / 70) * 17.5);
                        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                        switch ($this->id_lang){
                            case 1:
                                $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameKZ);
                                break;
                            case 2:
                                $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameRU);
                                break;
                            case 3:
                                if(empty($research[$i]->SubjectNameENG)){
                                    $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameRU);
                                }else{
                                    $active_sheet->setCellValue('A' . $zar_credit, $research[$i]->SubjectNameENG);
                                }
                                break;
                        }

                        $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
                        $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $active_sheet->setCellValue('O' . $zar_credit, $period);

                        $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                        $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->setCellValue('U' . $zar_credit, $research[$i]->creditscount);

                        $ects_credit = $this->ects($this->id_study_form,'nir',$research[$i]->creditscount);
                        $this->all_credit_ects += $ects_credit;
                        $active_sheet->mergeCells('X' . $zar_credit . ':AA' . $zar_credit);
                        $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->setCellValue('X' . $zar_credit, $ects_credit);
                        $this->credit_nir += $research[$i]->creditscount;
                        $this->credit_nir_ects += $ects_credit;
                        break;
                }
            }
        }

        # Қорытынды аттестация

        # если есть государственные аттестации

        switch (true){
            case($this->id_study_form == 10):
                $mag_general = DB::select("SELECT * FROM sacreports S
                                LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
                                WHERE G.studentID='$this->id_student' AND G.typeID=5");
                break;

            case($this->id_study_form == 9) OR ($this->id_study_form == 12):
                $mag_general = DB::select("SELECT * FROM sacreports S
                                LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
                                WHERE G.studentID='$this->id_student' AND G.typeID=4");
                break;
            case($this->id_study_form == 13):
                $mag_general = DB::select("SELECT * FROM sacreports S
                                LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
                                WHERE G.studentID='$this->id_student' AND G.typeID=10");
                break;
        }
        if (!empty($mag_general)) {
            $zar_credit++;
            $g_title = 'Қорытынды аттестация / Final attestation / Итоговая аттестация';
            $g_header1 = 'Емтиханды тапсырды/Has passed the examinations/Сдал экзамены';
            $g_header2 = 'Хаттамасының күні және нөмірі/Date and number of the report/Дата и номер протокола';
            $zar_credit = $this->setHeader($active_sheet,$zar_credit,$styleArray,$style_array,$g_title,$g_header1,$g_header2);

            $all_general_credit = 0; $all_general_gpa = 0; $all_general_zach_credit = 0;
            for($i=0; $i<count($mag_general); $i++){
                $protocol_date_number = substr($mag_general[$i]->date, 8, 2) . "." . substr($mag_general[$i]->date, 5, 2) . "." . substr($mag_general[$i]->date, 0, 4) . " / " . $mag_general[$i]->number;
                $active_sheet->insertNewRowBefore($zar_credit+1, 1);
                $zar_credit++;

                switch (true){
                    case ($this->id_lang == 1):
                        $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($mag_general[$i]->subjectkz) / 35) * 17.5);
                        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                        $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
                        $active_sheet->setCellValue('A' . $zar_credit, $mag_general[$i]->subjectkz);
                        break;
                    case ($this->id_lang == 2):
                        $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($mag_general[$i]->subjectru) / 35) * 17.5);
                        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                        $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
                        $active_sheet->setCellValue('A' . $zar_credit, $mag_general[$i]->subjectru);
                        break;
                    case ($this->id_lang == 3):
                        $active_sheet->getRowDimension($zar_credit)->setRowHeight(ceil(mb_strlen($mag_general[$i]->subjecten) / 35) * 17.5);
                        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                            PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                        $active_sheet->getStyle("A" . $zar_credit . ":AA" . $zar_credit)->getFont()->setSize(9);
                        $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
                        $active_sheet->setCellValue('A' . $zar_credit, $mag_general[$i]->subjecten);
                        break;
                }
                $general_total_mark = (int) round($mag_general[$i]->ap_mark);
                $active_sheet->getStyle('J' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->mergeCells('J' . $zar_credit . ':N' . $zar_credit);
                $active_sheet->setCellValue('J' . $zar_credit, $protocol_date_number);

                $active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->setCellValue('O' . $zar_credit, $mag_general[$i]->credits);

                $active_sheet->getStyle('R' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);

                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);

                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                switch(true) {
                    case ($this->trans_type < 2):
                        // обычный
                        $active_sheet->setCellValue('R' . $zar_credit, $this->total_mark($general_total_mark));
                        $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($general_total_mark, 2));
                        $active_sheet->setCellValue('X' . $zar_credit, $general_total_mark);
                        break;
                    case ($this->trans_type > 1):
                        // ECTs
                        $ects_credit = $this->ects($this->id_study_form,'exam', $mag_general[$i]->credits,'comp');
                        $this->all_credit_ects += $ects_credit;
                        $active_sheet->setCellValue('R' . $zar_credit, $ects_credit);
                        $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($general_total_mark));
                        $active_sheet->setCellValue('X' . $zar_credit, $this->total_mark($general_total_mark,2));
                        break;
                }

                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->setCellValue('Z' . $zar_credit, $this->total_mark($general_total_mark, 3));

                $all_general_credit += $mag_general[$i]->credits;
                $all_general_gpa += (int) $mag_general[$i]->credits * str_replace(',','.',$this->total_mark($general_total_mark, 2));
                if($general_total_mark > 49){
                    $all_general_zach_credit += $mag_general[$i]->credits;
                }
            }
            $this->all_credit += $all_general_credit;
            $this->all_gpa += $all_general_gpa;

            // Первая строка
            $zar_credit++;
            $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->setCellValue('A' . $zar_credit, '');

            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            switch(true){
                case ($this->id_profession == 54) OR ($this->id_profession == 65):
                    $active_sheet->setCellValue('O' . $zar_credit, "Тіркелген кредиттер");
                    break;
                default:
                    $active_sheet->setCellValue('O' . $zar_credit, "Зарегистрировано кредитов");
                    break;
            }

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            switch(true){
                case ($this->id_profession == 54) OR ($this->id_profession == 65):
                    $active_sheet->setCellValue('U' . $zar_credit, "Сыналған кредиттер");
                    break;
                default:
                    $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");
                    break;
            }

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $active_sheet->setCellValue('Z' . $zar_credit, "GPA");

            // Вторая строка
            $zar_credit++;
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
            );

            switch(true){
                case ($this->id_profession == 54) OR ($this->id_profession == 65):
                    $active_sheet->setCellValue('A' . $zar_credit, "Барлығы:");
                    break;
                default:
                    $active_sheet->setCellValue('A' . $zar_credit, "Всего:");
                    break;
            }

            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('O' . $zar_credit, $all_general_credit);

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('U' . $zar_credit, $all_general_zach_credit);

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            if($all_general_credit == 0){
                $active_sheet->setCellValue('Z' . $zar_credit, 0);
            }else{
                $active_sheet->setCellValue('Z' . $zar_credit, str_replace('.',',',($all_general_gpa) / ($all_general_credit)));
            }
        }

        # Дипломная работа
		switch(true){
            case ($this->id_study_form == 10):
                $diplom = DB::select("SELECT G.nameworken,G.nameworkkz,G.nameworkru,S.number,S.date,G.credits,G.mark FROM sacreports S
                                LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
                                WHERE G.studentID='$this->id_student' AND G.typeID=8");
                break;
            case ($this->id_study_form == 9) OR ($this->id_study_form == 12):
                $diplom = DB::select("SELECT G.nameworken,G.nameworkkz,G.nameworkru,S.number,S.date,G.credits,G.mark FROM sacreports S
                                LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
                                WHERE G.studentID='$this->id_student' AND G.typeID=9");
                break;
            case ($this->id_study_form == 13):
                $diplom = DB::select("SELECT G.nameworken,G.nameworkkz,G.nameworkru,S.number,S.date,G.credits,G.mark FROM sacreports S
                                LEFT JOIN generalexamsmarks G ON G.reportID=S.reportID
                                WHERE G.studentID='$this->id_student' AND G.typeID=11");
                break;
		}

        if (!empty($diplom)) {
            # есть
            $zar_credit++;
            $d_title = 'Диссертацияны орындады және қорғады/ Completed and Defended the Dissertation/ Выполнил (а) и защитил (а) диссертацию';
            $d_header1 = 'Диссертация тақырыбы/ Theme of dissertation/ Тема диссертации';
            $d_header2 = 'Хаттама күні және нөмірі/Date and number of the report/Дата и номер протокола';
            $zar_credit = $this->setHeader($active_sheet,$zar_credit,$styleArray,$style_array,$d_title,$d_header1,$d_header2);

            # дипломная работа
            $protocol_date_number = substr($diplom[0]->date, 8, 2) . "." . substr($diplom[0]->date, 5, 2) . "." . substr($diplom[0]->date, 0, 4) . " / " . $diplom[0]->number;
            $zar_credit++;
            $active_sheet->insertNewRowBefore($zar_credit, 1);
            $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':I' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
            switch (true){
                case ($this->id_lang == 1):
                    $active_sheet->setCellValue('A' . $zar_credit, $diplom[0]->nameworkkz);
                    break;
                case ($this->id_lang == 2):
                    $active_sheet->setCellValue('A' . $zar_credit, $diplom[0]->nameworkru);
                    break;
                case ($this->id_lang == 3):
                    $active_sheet->setCellValue('A' . $zar_credit, $diplom[0]->nameworken);
                    break;
            }


            $active_sheet->mergeCells('J' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->setCellValue('J' . $zar_credit, $protocol_date_number);
            $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
            $active_sheet->setCellValue('O' . $zar_credit, $diplom[0]->credits);

            $general_total_mark = (int)round($diplom[0]->mark);
            $this->all_credit += $diplom[0]->credits;
            $this->all_gpa += (int) $diplom[0]->credits * str_replace(',','.',$this->total_mark($general_total_mark, 2));


            $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
            $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            switch(true) {
                case ($this->trans_type < 2):
                    // обычный
                    $active_sheet->setCellValue('R' . $zar_credit, $this->total_mark($general_total_mark));
                    $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($general_total_mark, 2));
                    $active_sheet->setCellValue('X' . $zar_credit, $general_total_mark);
                    break;
                case ($this->trans_type > 1):
                    // ECTs
                    $ects_credit = $this->ects($this->id_study_form,'exam', $diplom[0]->credits,'protection');
                    $this->all_credit_ects += $ects_credit;
                    $active_sheet->setCellValue('R' . $zar_credit, $ects_credit);
                    $active_sheet->setCellValue('U' . $zar_credit, $this->total_mark($general_total_mark));
                    $active_sheet->setCellValue('X' . $zar_credit, $this->total_mark($general_total_mark,2));
                    break;
            }
            $active_sheet->setCellValue('Z' . $zar_credit, $this->total_mark($general_total_mark, 3));

            // Первая строка
            $zar_credit++;
            $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);

            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);

            switch(true){
                case ($this->id_profession == 54) OR ($this->id_profession == 65):
                    $active_sheet->setCellValue('O' . $zar_credit, "Тіркелген кредиттер");
                    break;
                default:
                    $active_sheet->setCellValue('O' . $zar_credit, "Зарегистрировано кредитов");
                    break;
            }

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            switch(true){
                case ($this->id_profession == 54) OR ($this->id_profession == 65):
                    $active_sheet->setCellValue('U' . $zar_credit, "Сыналған кредиттер");
                    break;
                default:
                    $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");
                    break;
            }

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $active_sheet->setCellValue('Z' . $zar_credit, "GPA");

            // Вторая строка
            $zar_credit++;
            $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
            $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
            );

            switch(true){
                case ($this->id_profession == 54) OR ($this->id_profession == 65):
                    $active_sheet->setCellValue('A' . $zar_credit, "Барлығы:");
                    break;
                default:
                    $active_sheet->setCellValue('A' . $zar_credit, "Всего:");
                    break;
            }

            $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
            $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('O' . $zar_credit, $diplom[0]->credits);

            $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
            $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('U' . $zar_credit, $diplom[0]->credits);

            $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
            $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
            $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $bal = str_replace(',','.',$this->total_mark($general_total_mark, 2));
            $active_sheet->setCellValue('Z' . $zar_credit, round(($bal * $diplom[0]->credits) / ($diplom[0]->credits), 2));
        }

        $zar_credit = $zar_credit + 2;
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->getFont()->setBold(true);
        $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
        $active_sheet->setCellValue('A' . $zar_credit, "Жалпы кредит саны/ Total Hours Passed/ Общее число кредитов: ");

        $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
        //$active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit + $this->credit_nir);
        $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
        //$active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->getFont()->setBold(true);
        $active_sheet->setCellValue('X' . $zar_credit, "GPA=");

        $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
        //$active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('Z' . $zar_credit, round(($this->all_gpa / $this->all_credit), 2));
        switch (true){
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $zar_credit += 1;
                $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
                $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setWrapText(true);
                $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->getFont()->setBold(true);
                $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
                $active_sheet->setCellValue('A' . $zar_credit, "ECTS кредиттер саны/Credit hours ECTS/Количество кредитов ECTS: ");

                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit_ects);
                break;
        }
        $zar_credit = $zar_credit + 2;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch(true){
            case ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('B' . $zar_credit, "* - Ағылшын тілінде оқылған пәндер");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "* - Дисциплины, изученные на английском языке");
                break;
        }


        switch (true){
            case ($this->trans_type == 1) OR ($this->trans_type == 3):
                $zar_credit += 2;
                $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->setCellValue('B' . $zar_credit, "Ректор КазНПУ имени Абая");
                $active_sheet->mergeCells('V' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->setCellValue('V' . $zar_credit, "Балыкбаев Т.О.");
        }
        $zar_credit = $zar_credit + 2;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch(true){
            case ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('B' . $zar_credit, "Тіркеу басқармасының бастығы");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "Начальник управления регистрации");
                break;
        }

        $active_sheet->mergeCells('V' . $zar_credit . ':AA' . $zar_credit);
        $active_sheet->setCellValue('V' . $zar_credit, "Кантарбаева Р.М.");

        $zar_credit = $zar_credit + 2;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch(true){
            case ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('B' . $zar_credit, "Көшірме дұрыс: әдіскер-тіркеуші");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "Выписка верна: методист-регистратор");
                break;
        }

        $active_sheet->mergeCells('V' . $zar_credit . ':AA' . $zar_credit);
        $active_sheet->setCellValue('V' . $zar_credit, $_SESSION['last_first']);

        $zar_credit++;
        $active_sheet->mergeCells('B' . $zar_credit . ':Q' . $zar_credit);
        switch(true){
            case ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('B' . $zar_credit, "Тіркеу № _______");
                break;
            default:
                $active_sheet->setCellValue('B' . $zar_credit, "Регистрационный № _______");
                break;
        }


        $zar_credit++;
        $active_sheet->mergeCells('B' . $zar_credit . ':I' . $zar_credit);
        $active_sheet->setCellValue('B' . $zar_credit, $this->date_time);

        header("Content-Type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=Транскрипт " . $student_inicial . ".xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
        exit();
    }
    ##### Магистратура #####

    ### ECTS ###
    public function ects($id_study_form, $type, $credit,$pod_type = null){
        $arr = [
            '5B' => [
                'theory' => [
                    0 => 0, 1 => 2, 2 => 4, 3 => 5, 4 => 6, 5 => 8
                ],
                'practice' => [
                    'edu' => [
                        0 => 0, 1 => 2, 2 => 4
                    ],
                    'ped' => [
                        0 => 0, 2 => 2, 4 => 4
                    ],
                    'pro' => [
                        0 => 0, 2 => 5, 4 => 12, 6 => 18
                    ]
                ],
                'exam' => [
                    'gos' => [
                        0 => 0, 2 => 8,1 => 4
                    ],
                    'protection' => [
                        0 => 0, 2 => 8
                    ]
                ]
            ],
            '6M' => [
                'theory' => [
                    0 => 0, 1 => 2, 2 => 4, 3 => 5, 4 => 6, 6 => 10
                ],
                'practice' => [
                    'iss' => [
                        0 => 0, 3 => 12
                    ],
                    'ped' => [
                        0 => 0, 2=>2, 3 => 3
                    ],
                    'pro' => [
                        0 => 0, 2 => 5
                    ]
                ],
                'nir' => [
                    0 => 0, 1 => 4, 2 => 8, 3 => 12
                ],
                'exam' => [
                    'comp' => [
                        0 => 0, 1 => 3
                    ],
                    'protection' => [
                        0 => 0, 3 => 10
                    ]
                ]
            ],
            '6D' => [
                'theory' => [
                    0 => 0, 1 => 2, 2 => 4, 3 => 5, 4 => 6
                ],
                'practice' => [
                    'iss' => [
                        0 => 0, 3 => 12
                    ],
                    'ped' => [
                        0 => 0, 3 => 3
                    ]
                ],
                'nir' => [
                    0 => 0, 1 => 4, 2 => 8, 3 => 12, 4 => 16, 5 => 20, 6 => 24, 7 => 28, 8 => 32
                ],
                'exam' => [
                    'comp' => [
                        0 => 0, 1 => 3
                    ],
                    'protection' => [
                        0 => 0, 4 => 13
                    ]
                ]
            ]
        ];
        switch(true){
            case ($id_study_form == 1) OR ($id_study_form == 3) OR ($id_study_form == 4) OR ($id_study_form == 6) OR ($id_study_form == 7):
                switch (true){
                    case ($type == 'theory'):
                        return $arr['5B'][$type][$credit];
                        break;
                    case ($type == 'practice') OR ($type == 'exam'):
                        return $arr['5B'][$type][$pod_type][$credit];
                        break;
                }
                break;
            case ($id_study_form == 9) OR ($id_study_form == 10) OR ($id_study_form == 12):
                switch (true){
                    case ($type == 'theory') OR ($type == 'nir'):
                        return $arr['6M'][$type][$credit];
                        break;
                    case ($type == 'practice') OR ($type == 'exam'):
                        return $arr['6M'][$type][$pod_type][$credit];
                        break;
                }
                break;
            case ($id_study_form == 13):
                switch (true){
                    case ($type == 'theory') OR ($type == 'nir'):
                        return $arr['6D'][$type][$credit];
                        break;
                    case ($type == 'practice') OR ($type == 'exam'):
                        return $arr['6D'][$type][$pod_type][$credit];
                        break;
                }
                break;
        }
    }
    ### ECTS ###

    # Первая строка
    public function bac_line1($semester1,$active_sheet,$styleArray, $current,$number){
        $credit = 0; $p = 0; $zach_credit = 0; $credit_ects = 0; $zach_credit_ects = 0;
        $current_row1 = $current;
        $active_sheet->insertNewRowBefore($current_row1 + 1, 1);
        $current_row1++;
        $active_sheet->mergeCells('A' . $current_row1 . ':AA' . $current_row1);
        $active_sheet->getStyle('A' . $current_row1)->applyFromArray($styleArray);
        switch (true){
            case ($this->id_profession == 51) OR ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('A' . $current_row1, $number." семестр");
                break;

            default:
                $active_sheet->setCellValue('A' . $current_row1, $number." семестр /semester /семестр");
                break;
        }
        for ($i = 0; $i < count($semester1); $i++) {
            $j = $i + 1;
            $active_sheet->insertNewRowBefore($current_row1 + 1, 1);
            $current_row1++;
            $total1 = round($semester1[$i]->total);
            $active_sheet->setCellValue('A' . $current_row1, $j);
            $active_sheet->mergeCells('B' . $current_row1 . ':E' . $current_row1);
            $active_sheet->getStyle('B' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->getStyle('B' . $current_row1)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            );
            $active_sheet->setCellValue('B' . $current_row1, $semester1[$i]->kod);
            $active_sheet->mergeCells('F' . $current_row1 . ':N' . $current_row1);
            switch ($this->id_lang){
                case 1:
                    // казахский
                    $active_sheet->getRowDimension($current_row1)->setRowHeight(ceil(mb_strlen($semester1[$i]->name_kz) / 35) * 17.5);
                    $active_sheet->getStyle('F' . $current_row1)->applyFromArray($styleArray);
                    $active_sheet->getStyle('F' . $current_row1)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )->setWrapText(true);
                    $active_sheet->setCellValue('F' . $current_row1, $semester1[$i]->name_kz);
                    break;
                case 2:
                    // русский
                    $active_sheet->getRowDimension($current_row1)->setRowHeight(ceil(mb_strlen($semester1[$i]->name_ru) / 35) * 17.5);
                    $active_sheet->getStyle('F' . $current_row1)->applyFromArray($styleArray);
                    $active_sheet->getStyle('F' . $current_row1)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )->setWrapText(true);
                    $active_sheet->setCellValue('F' . $current_row1, $semester1[$i]->name_ru);
                    break;
                case 3:
                    // английский
                    $active_sheet->getRowDimension($current_row1)->setRowHeight(ceil(mb_strlen($semester1[$i]->name_en) / 35) * 17.5);
                    $active_sheet->getStyle('F' . $current_row1)->applyFromArray($styleArray);
                    $active_sheet->getStyle('F' . $current_row1)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )->setWrapText(true);
                    $active_sheet->setCellValue('F' . $current_row1, $semester1[$i]->name_en);
                    break;
            }


            $active_sheet->mergeCells('O' . $current_row1 . ':Q' . $current_row1);
            $active_sheet->getStyle('O' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->setCellValue('O' . $current_row1, $semester1[$i]->credit);
            $active_sheet->mergeCells('R' . $current_row1 . ':T' . $current_row1);
            $active_sheet->getStyle('R' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->mergeCells('U' . $current_row1 . ':W' . $current_row1);
            $active_sheet->getStyle('U' . $current_row1)->applyFromArray($styleArray);

            $active_sheet->mergeCells('X' . $current_row1 . ':Y' . $current_row1);
            $active_sheet->getStyle('X' . $current_row1)->applyFromArray($styleArray);

            $active_sheet->mergeCells('Z' . $current_row1 . ':AA' . $current_row1);
            $active_sheet->getStyle('Z' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->setCellValue('Z' . $current_row1, $this->total_mark($total1, 3));
            switch (true){
                case ($this->trans_type < 2):
                    $active_sheet->setCellValue('R' . $current_row1, $this->total_mark($total1, 1));
                    $active_sheet->setCellValue('U' . $current_row1, $this->total_mark($total1, 2));
                    $active_sheet->setCellValue('X' . $current_row1, $total1);
                    break;
                case ($this->trans_type > 1):
                    $practice_name = trim($semester1[$i]->name_ru);
                    $practice_name = mb_strtolower($practice_name);
                    if(preg_match("/производственная/", $practice_name)){
                        $ects_credit = $this->ects($this->id_study_form,'practice', $semester1[$i]->credit, 'pro');
                        $active_sheet->setCellValue('R' . $current_row1, $ects_credit);
                        $credit_ects += $ects_credit;
                        if($this->total_mark($total1, 1) != 'F'){
                            $zach_credit_ects += (int) $ects_credit;
                        }
                    }elseif(preg_match("/педагогическая/", $practice_name)){
                        $ects_credit = $this->ects($this->id_study_form,'practice', $semester1[$i]->credit, 'ped');
                        $active_sheet->setCellValue('R' . $current_row1, $ects_credit);
                        $credit_ects += $ects_credit;
                        if($this->total_mark($total1, 1) != 'F'){
                            $zach_credit_ects += (int) $ects_credit;
                        }
                    }elseif(preg_match("/учебная/", $practice_name)){
                        $ects_credit = $this->ects($this->id_study_form,'practice', $semester1[$i]->credit, 'edu');
                        $active_sheet->setCellValue('R' . $current_row1, $ects_credit);
                        $credit_ects += $ects_credit;
                        if($this->total_mark($total1, 1) != 'F'){
                            $zach_credit_ects += (int) $ects_credit;
                        }
                    }elseif(preg_match("/научно-исследовательская/", $practice_name)){
                        $ects_credit = $this->ects($this->id_study_form,'practice', $semester1[$i]->credit, 'iss');
                        $active_sheet->setCellValue('R' . $current_row1, $ects_credit);
                        $credit_ects += $ects_credit;
                        if($this->total_mark($total1, 1) != 'F'){
                            $zach_credit_ects += (int) $ects_credit;
                        }
                    }else{
                        $ects_credit = $this->ects($this->id_study_form,'theory', $semester1[$i]->credit);
                        $active_sheet->setCellValue('R' . $current_row1, $ects_credit);
                        $credit_ects += $ects_credit;
                        if($this->total_mark($total1, 1) != 'F'){
                            $zach_credit_ects += (int) $ects_credit;
                        }
                    }

                    $active_sheet->setCellValue('U' . $current_row1, $this->total_mark($total1, 1));
                    $active_sheet->setCellValue('X' . $current_row1, $this->total_mark($total1, 2));
                    break;
            }



            $credit += (int) $semester1[$i]->credit;
            if($this->total_mark($total1, 1) != 'F'){
                $zach_credit += (int)$semester1[$i]->credit;
                //$zach_credit_ects += (int) $ects_credit;
            }

            $p += ($semester1[$i]->credit * str_replace(',','.',$this->total_mark($total1, 2)));
        }
        $this->all_credit += $credit;
        $this->all_credit_ects += $credit_ects;
        $this->all_gpa += $p;
        $this->all_zach_credit += $zach_credit;
        $this->all_zach_credit_ects += $zach_credit_ects;

        // Первая строка
        $zar_credit = $current_row1 + 1;
        $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);

        $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
        $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        switch (true){
            case ($this->id_profession == 51) OR ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('O' . $zar_credit, "Тіркелген кредиттер");
                break;
            default:
                $active_sheet->setCellValue('O' . $zar_credit, "Зарегистрировано кредитов");
                break;
        }
        switch (true){
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                switch (true){
                    case ($this->id_profession == 51) OR ($this->id_profession == 54) OR ($this->id_profession == 65):
                        $active_sheet->setCellValue('U' . $zar_credit, "Сыналған кредиттер");
                        break;
                    default:
                        $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");
                        break;
                }

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('Z' . $zar_credit, "GPA");
                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                switch (true){
                    case ($this->id_profession == 51) OR ($this->id_profession == 54) OR ($this->id_profession == 65):
                        $active_sheet->setCellValue('U' . $zar_credit, "Сыналған кредиттер");
                        break;
                    default:
                        $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");
                        break;
                }

                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('X' . $zar_credit, "Зачтено кредитов ECTS");

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('Z' . $zar_credit, "GPA");
                break;
        }

        // Вторая строка
        $zar_credit++;
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        );
        switch (true){
            case ($this->id_profession == 51) OR ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('A' . $zar_credit, "Семестр үшін:");
                break;
            default:
                $active_sheet->setCellValue('A' . $zar_credit, "За семестр:");
                break;
        }


        switch (true) {
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $credit);

                $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $zach_credit);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                if($credit == 0){
                    $active_sheet->setCellValue('Z' . $zar_credit, 0);
                }else{
                    $active_sheet->setCellValue('Z' . $zar_credit, round($p / $credit, 2));
                }

                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $credit);

                $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('R' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('R' . $zar_credit, $credit_ects);

                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $zach_credit);

                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('X' . $zar_credit, $zach_credit_ects);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('Z' . $zar_credit, round($p / $credit, 2));
                break;
        }

        // Третья строка
        $zar_credit++;
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        );
        switch (true){
            case ($this->id_profession == 51) OR ($this->id_profession == 54) OR ($this->id_profession == 65):
                $active_sheet->setCellValue('A' . $zar_credit, "Барлығы:");
                break;
            default:
                $active_sheet->setCellValue('A' . $zar_credit, "Всего:");
                break;
        }

        switch (true) {
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit);

                $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $this->all_zach_credit);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('Z' . $zar_credit, round($this->all_gpa / $this->all_credit, 2));
                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit);

                $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('R' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('R' . $zar_credit, $this->all_credit_ects);

                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $this->all_zach_credit);

                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('X' . $zar_credit, $this->all_zach_credit_ects);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('Z' . $zar_credit, round($this->all_gpa / $this->all_credit, 2));
                break;
        }
        return $zar_credit;
    }

    # dop_semester
    public function dop_semester($dop_sem1,$active_sheet,$styleArray,$current){
        $dop_cre1 = 0; $dop_p1 = 0; $dop_zach_credit1 = 0; $dop_zach_credit_ects = 0; $dop_credit_ects = 0;
        $current_row1 = $current;
        $active_sheet->insertNewRowBefore($current_row1 + 1, 1);
        $current_row1++;
        $active_sheet->mergeCells('A' . $current_row1 . ':AA' . $current_row1);
        $active_sheet->getStyle('A' . $current_row1)->applyFromArray($styleArray);
        $active_sheet->getStyle('A' . $current_row1)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        );
        $active_sheet->setCellValue('A' . $current_row1, "дополнительный семестр");
        for ($i = 0; $i < count($dop_sem1); $i++) {
            $j = $i + 1;
            $active_sheet->insertNewRowBefore($current_row1 + 1, 1);
            $current_row1++;
            $active_sheet->setCellValue('A' . $current_row1, $j);
            $active_sheet->mergeCells('B' . $current_row1 . ':E' . $current_row1);
            $active_sheet->getStyle('B' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->getStyle('B' . $current_row1)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            )->setWrapText(true);
            $active_sheet->setCellValue('B' . $current_row1, $dop_sem1[$i]->kod);
            $active_sheet->mergeCells('F' . $current_row1 . ':N' . $current_row1);
            $total1 = round($dop_sem1[$i]->total);
            switch($this->id_lang){
                case 1:
                    // казахский
                    $active_sheet->getRowDimension($current_row1)->setRowHeight(ceil(mb_strlen($dop_sem1[$i]->name_kz) / 35) * 17.5);
                    $active_sheet->getStyle('F' . $current_row1)->applyFromArray($styleArray);
                    $active_sheet->getStyle('F' . $current_row1)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )->setWrapText(true);
                    $active_sheet->setCellValue('F' . $current_row1, $dop_sem1[$i]->name_kz);
                    break;
                case 2:
                    // русский
                    $active_sheet->getRowDimension($current_row1)->setRowHeight(ceil(mb_strlen($dop_sem1[$i]->name_ru) / 35) * 17.5);
                    $active_sheet->getStyle('F' . $current_row1)->applyFromArray($styleArray);
                    $active_sheet->getStyle('F' . $current_row1)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )->setWrapText(true);
                    $active_sheet->setCellValue('F' . $current_row1, $dop_sem1[$i]->name_ru);
                    break;
                case 3:
                    // английский
                    $active_sheet->getRowDimension($current_row1)->setRowHeight(ceil(mb_strlen($dop_sem1[$i]->name_en) / 35) * 17.5);
                    $active_sheet->getStyle('F' . $current_row1)->applyFromArray($styleArray);
                    $active_sheet->getStyle('F' . $current_row1)->getAlignment()->setHorizontal(
                        PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )->setWrapText(true);
                    $active_sheet->setCellValue('F' . $current_row1, $dop_sem1[$i]->name_en);
                    break;
            }


            $active_sheet->mergeCells('O' . $current_row1 . ':Q' . $current_row1);
            $active_sheet->getStyle('O' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->setCellValue('O' . $current_row1, $dop_sem1[$i]->credit);
            $active_sheet->mergeCells('R' . $current_row1 . ':T' . $current_row1);
            $active_sheet->getStyle('R' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->getStyle('R' . $current_row1)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->mergeCells('U' . $current_row1 . ':W' . $current_row1);
            $active_sheet->getStyle('U' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->mergeCells('X' . $current_row1 . ':Y' . $current_row1);
            $active_sheet->getStyle('X' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->getStyle('X' . $current_row1)->getAlignment()->setHorizontal(
                PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            );
            $active_sheet->setCellValue('X' . $current_row1, $total1);
            $active_sheet->mergeCells('Z' . $current_row1 . ':AA' . $current_row1);
            $active_sheet->getStyle('Z' . $current_row1)->applyFromArray($styleArray);
            $active_sheet->setCellValue('Z' . $current_row1, $this->total_mark($total1, 3));
            $ects_credit = $this->ects($this->id_study_form,'theory',$dop_sem1[$i]->credit);
            switch (true){
                case ($this->trans_type == 0) OR ($this->trans_type == 1):
                    $active_sheet->setCellValue('R' . $current_row1, $this->total_mark($total1, 1));
                    $active_sheet->setCellValue('U' . $current_row1, $this->total_mark($total1, 2));
                    $active_sheet->setCellValue('X' . $current_row1, $total1);
                    break;
                case ($this->trans_type == 2) OR ($this->trans_type == 3):
                    $active_sheet->setCellValue('R' . $current_row1, $ects_credit);
                    $active_sheet->setCellValue('U' . $current_row1, $this->total_mark($total1));
                    $active_sheet->setCellValue('X' . $current_row1, $this->total_mark($total1,2));
                    break;
            }
            $dop_cre1 += (int)$dop_sem1[$i]->credit;
            $dop_credit_ects += $ects_credit;
            $dop_p1 += ($dop_sem1[$i]->credit * str_replace(',','.',$this->total_mark($total1,2)));
            if($this->total_mark($total1) != 'F'){
                $dop_zach_credit1 += (int)$dop_sem1[$i]->credit;
                $dop_zach_credit_ects += (int) $ects_credit;
            }
        }
        $this->all_credit += $dop_cre1;
        $this->all_credit_ects += $dop_credit_ects;
        $this->all_gpa += $dop_p1;
        $this->all_zach_credit += $dop_zach_credit1;
        $this->all_zach_credit_ects += $dop_zach_credit_ects;

        // Первая строка
        $zar_credit = $current_row1 + 1;
        $active_sheet->getRowDimension($zar_credit)->setRowHeight(30);
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);

        $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
        $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $active_sheet->setCellValue('O' . $zar_credit, "Зарегистрировано кредитов");

        switch (true){
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('Z' . $zar_credit, "GPA");
                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('U' . $zar_credit, "Зачтено кредитов");

                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('X' . $zar_credit, "Зачтено кредитов ECTS");

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $active_sheet->setCellValue('Z' . $zar_credit, "GPA");
                break;
        }

        // Вторая строка
        $zar_credit++;
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        );
        $active_sheet->setCellValue('A' . $zar_credit, "За семестр:");
        switch (true) {
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $dop_cre1);

                $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $dop_zach_credit1);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('Z' . $zar_credit, round($dop_p1 / $dop_cre1, 2));
                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $dop_cre1);

                $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('R' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('R' . $zar_credit, $dop_credit_ects);

                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $dop_zach_credit1);

                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('X' . $zar_credit, $dop_zach_credit_ects);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('Z' . $zar_credit, round($dop_p1 / $dop_cre1, 2));
                break;
        }

        // Третья строка
        $zar_credit++;
        $active_sheet->getStyle('A' . $zar_credit . ':N' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('A' . $zar_credit . ':N' . $zar_credit);
        $active_sheet->getStyle('A' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
        );
        $active_sheet->setCellValue('A' . $zar_credit, "Всего:");

        switch (true) {
            case ($this->trans_type == 0) OR ($this->trans_type == 1):
                $active_sheet->mergeCells('O' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit);

                $active_sheet->mergeCells('U' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $this->all_zach_credit);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('Z' . $zar_credit, round($this->all_gpa / $this->all_credit, 2));
                break;
            case ($this->trans_type == 2) OR ($this->trans_type == 3):
                $active_sheet->mergeCells('O' . $zar_credit . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $zar_credit . ':Q' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('O' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('O' . $zar_credit, $this->all_credit);

                $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('R' . $zar_credit . ':T' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('R' . $zar_credit, $this->all_credit_ects);

                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $zar_credit, $this->all_zach_credit);

                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('X' . $zar_credit, $this->all_zach_credit_ects);

                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('Z' . $zar_credit, round($this->all_gpa / $this->all_credit, 2));
                break;
        }
        return $zar_credit;
    }

    # По курсу определить семетр
    public function semester($course,$term){
        switch (true){
            case ($course == 1) AND ($term == 1):
                return 1;
                break;
            case ($course == 1) AND ($term == 2):
                return 2;
                break;
            case ($course == 2) AND ($term == 1):
                return 3;
                break;
            case ($course == 2) AND ($term == 2):
                return 4;
                break;
            case ($course == 3) AND ($term == 1):
                return 5;
                break;
            case ($course == 3) AND ($term == 2):
                return 6;
                break;
            case ($course == 4) AND ($term == 1):
                return 7;
                break;
            case ($course == 4) AND ($term == 2):
                return 8;
                break;
        }
    }

    public function sql($course,$term){
        if($this->id_status == 1){
            $table = 'transcript';
        }
        if($this->id_status == 2 OR $this->id_status == 3){
            $table = 'deletedtranscript';
        }
        if($term == 0){
            $group_by = 'GROUP BY OOO.kod';
        }else{
            $group_by = 'GROUP BY OOO.kod';
//            $group_by = '';
        }
        $result = DB::select("SELECT OOO.* FROM ((SELECT TR.subjectcode AS kod,TR.Credits AS credit,TR.TotalMark AS total,RTRIM(LTRIM(TR.subjectnameRU)) AS name_ru,
TR.subjectnameKZ AS name_kz, TR.subjectnameEN AS name_en,TR.coursenumber AS course,TR.term AS semester,
TR.R1,TR.R2
FROM nitro.$table TR WHERE TR.StudentID='$this->id_student' AND TR.coursenumber='$course' AND TR.term IN($term))
UNION
/* 2 запрос*/
(SELECT NQ.code AS kod,NQ.credits AS credit,ROUND(NT.ap_totalmark) AS total,RTRIM(LTRIM(NE.subjectRU)) AS name_ru,NE.subjectKZ AS name_kz,NE.subjectEN AS name_en,
NQ.year AS course,NQ.term AS semester,NJ1.Mark AS R1,NJ2.Mark AS R2
FROM nitro.queries NQ 
LEFT JOIN nitro.totalmarks NT ON NT.queryID=NQ.QueryID
LEFT JOIN nitro.erwithappealreports NE ON NE.groupID=NT.studygroupID
LEFT JOIN nitro.journal NJ1 ON NJ1.StudyGroupID=NT.studygroupID AND NJ1.StudentID=NT.studentID AND NJ1.number=1 AND NJ1.isCurrent=1
LEFT JOIN nitro.journal NJ2 ON NJ2.StudyGroupID=NT.studygroupID AND NJ2.StudentID=NT.studentID AND NJ2.number=2 AND NJ2.isCurrent=1
LEFT JOIN nitro.journal NJ3 ON NJ3.StudyGroupID=NT.studygroupID AND NJ3.StudentID=NT.studentID AND NJ3.markTypeID=3 AND NJ3.isCurrent=1
WHERE NQ.StudentID='$this->id_student' AND NQ.year='$course' AND NQ.term IN($term))) OOO WHERE OOO.name_ru IS NOT NULL $group_by ORDER BY OOO.course,OOO.semester,OOO.name_ru");
        return $result;
    }

    # практики
    public function practice(){
        $result = DB::select("SELECT OOO.* FROM ((SELECT TR.subjectcode AS kod,TR.Credits AS credit,TR.TotalMark AS total,TR.subjectnameRU AS name_ru,
TR.subjectnameKZ AS name_kz, TR.subjectnameEN AS name_en,TR.coursenumber AS course,TR.term AS semester,
TR.R1,TR.R2,TR.exammark AS exam
FROM nitro.transcript TR WHERE TR.StudentID='$this->id_student')
UNION
/* 2 запрос*/
(SELECT NQ.code AS kod,NQ.credits AS credit,NT.ap_totalmark AS total,NE.subjectRU AS name_ru,NE.subjectKZ AS name_kz,NE.subjectEN AS name_en,
NQ.year AS course,NQ.term AS semester,NJ1.Mark AS R1,NJ2.Mark AS R2,NJ3.Mark AS exam
FROM nitro.queries NQ 
LEFT JOIN nitro.totalmarks NT ON NT.queryID=NQ.QueryID
LEFT JOIN nitro.erwithappealreports NE ON NE.groupID=NT.studygroupID
INNER JOIN nitro.journal NJ1 ON NJ1.StudyGroupID=NT.studygroupID AND NJ1.StudentID=NT.studentID AND NJ1.number=1 AND NJ1.isCurrent=1
LEFT JOIN nitro.journal NJ2 ON NJ2.StudyGroupID=NT.studygroupID AND NJ2.StudentID=NT.studentID AND NJ2.number=2 AND NJ2.isCurrent=1
LEFT JOIN nitro.journal NJ3 ON NJ3.StudyGroupID=NT.studygroupID AND NJ3.StudentID=NT.studentID AND NJ3.markTypeID=3 AND NJ3.isCurrent=1
WHERE NQ.StudentID='$this->id_student')) OOO WHERE OOO.R1 IS NULL AND OOO.R2 IS NULL AND OOO.total IS NOT NULL AND OOO.credit>0 GROUP BY OOO.name_ru ORDER BY OOO.course,OOO.semester,OOO.name_ru");
        return $result;
    }

    public function prilojenie(){
        //$result = DB::select('select * from nitro.students where nitro.students.studentID=14547');
        $professions = Profession::getAllProfession();
        $study_form = Studyform::getAllStudyform();
        $course = ['1','2','3','4','5'];
        $js = 'prilojenie';
        return view('registration/prilojenie', compact('professions','study_form','course', 'js'));
    }

    public function is_type_pratice($practice_name){
        $practice_name = trim($practice_name);
        $practice_name = mb_strtolower($practice_name);
        if(preg_match("/производственная/", $practice_name)){
            return 100;
        }
        if(preg_match("/педогогичес/", $practice_name)){
            return 101;
        }
        if(preg_match("/учебная/", $practice_name)){
            return 102;
        }

        return 0;
    }

    # нарисовать шапки
    public function setHeader($active_sheet,$zar_credit,$styleArray,$style_array,$title,$header1,$header2){
        $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($styleArray);
        $active_sheet->mergeCells('A' . $zar_credit . ':AA' . $zar_credit);
        $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $active_sheet->getStyle('A' . $zar_credit . ':AA' . $zar_credit)->getFont()->setBold(true);
        $active_sheet->getRowDimension($zar_credit)->setRowHeight(25);
        $active_sheet->setCellValue('A' . $zar_credit, $title);
        $top = $zar_credit + 1;
        switch(true){
            case ($this->trans_type < 2):
                $active_sheet->getStyle('R' . $top . ':AA' . $top)->applyFromArray($style_array);
                $active_sheet->mergeCells('R' . $top . ':AA' . $top);
                $active_sheet->getStyle('R' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('R' . $top, "Баға/Grade/Оценка");

                $zar_credit = $top + 1;
                $active_sheet->getRowDimension($zar_credit)->setRowHeight(60);
                $active_sheet->getStyle('A' . $top . ':I' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('A' . $top . ':I' . $zar_credit);
                $active_sheet->getStyle('A' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('A' . $top, $header1);

                $active_sheet->getStyle('J' . $top . ':N' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('J' . $top . ':N' . $zar_credit);
                $active_sheet->getStyle('J' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('J' . $top, $header2);

                $active_sheet->getStyle('O' . $top . ':Q' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('O' . $top . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('O' . $top, "Кредиттер саны/Credit hours/Количество кредитов");

                $active_sheet->getStyle('R' . $zar_credit . ':T' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('R' . $zar_credit . ':T' . $zar_credit);
                $active_sheet->getStyle('R' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    PHPExcel_Style_Border::BORDER_THIN
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('R' . $zar_credit, "Әріптік / Alphabetic / Буквенная");

                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('U' . $zar_credit, "Балмен /In points / В баллах");

                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('X' . $zar_credit, "Пайызбен /In percent / В процентах");

                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('Z' . $zar_credit, "Дәстүрлі жүйемен / Traditional / Традиционная");
                break;
            case ($this->trans_type > 1):
                $active_sheet->getStyle('U' . $top . ':AA' . $top)->applyFromArray($style_array);
                $active_sheet->mergeCells('U' . $top . ':AA' . $top);
                $active_sheet->getStyle('U' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                );
                $active_sheet->setCellValue('U' . $top, "Баға/Grade/Оценка");

                $zar_credit = $top + 1;
                $active_sheet->getRowDimension($zar_credit)->setRowHeight(60);
                $active_sheet->getStyle('A' . $top . ':I' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('A' . $top . ':I' . $zar_credit);
                $active_sheet->getStyle('A' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('A' . $top, $header1);

                $active_sheet->getStyle('J' . $top . ':N' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('J' . $top . ':N' . $zar_credit);
                $active_sheet->getStyle('J' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('J' . $top, $header2);

                $active_sheet->getStyle('O' . $top . ':Q' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('O' . $top . ':Q' . $zar_credit);
                $active_sheet->getStyle('O' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('O' . $top, "Кредиттер саны/Credit hours/Количество кредитов");

                $active_sheet->getStyle('R' . $top . ':T' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('R' . $top . ':T' . $zar_credit);
                $active_sheet->getStyle('R' . $top)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('R' . $top, "ECTS кредиттер саны/ Credit hours ECTS/ Количество кредитов ECTS");

                $active_sheet->getStyle('U' . $zar_credit . ':W' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('U' . $zar_credit . ':W' . $zar_credit);
                $active_sheet->getStyle('U' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    PHPExcel_Style_Border::BORDER_THIN
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('U' . $zar_credit, "Әріптік / Alphabetic / Буквенная");

                $active_sheet->getStyle('X' . $zar_credit . ':Y' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('X' . $zar_credit . ':Y' . $zar_credit);
                $active_sheet->getStyle('X' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('X' . $zar_credit, "Балмен /In points / В баллах");

                $active_sheet->getStyle('Z' . $zar_credit . ':AA' . $zar_credit)->applyFromArray($style_array);
                $active_sheet->mergeCells('Z' . $zar_credit . ':AA' . $zar_credit);
                $active_sheet->getStyle('Z' . $zar_credit)->getAlignment()->setHorizontal(
                    PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                )->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
                $active_sheet->setCellValue('Z' . $zar_credit, "Дәстүрлі жүйемен / Traditional / Традиционная");
                break;
        }
        return $zar_credit;
    }

    # ИУП
    public function iup(){
        $professions = Profession::getAllProfession();
        $study_lang = Studylanguage::getStudylang();
        return view('registration/iup', compact('professions', 'study_lang'));
    }

    # По коду дисциплину определяет
    public function getSubjectFromQuery($id_student, $subject_code){
        $result = DB::select("SELECT * FROM nitro.queries NQ WHERE NQ.StudentID='$id_student' AND NQ.term=0 AND NQ.`code`='$subject_code'");
        if($result){
            return true;
        }
    }
}
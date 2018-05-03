<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use App\Contingent;
use App\Profession;
use App\Studyform;
use App\Studylanguage;
use PHPExcel_IOFactory;
use Illuminate\Support\Facades\Session;
use PHPExcel_RichText;
use PHPExcel;
use Cache;

class UserController extends Controller
{
    public function login(){
        return view('registration/login');
    }
    
    public function index(){
        switch (Contingent::check_user_role($_SESSION['id_tutor'])){
            case 1:
                // Методист-регистратор
                $professions = Contingent::getContingentProfession($_SESSION['id_tutor']);
                $study_lang = Studylanguage::getStudylang();
                return view('registration/index', compact('professions','study_lang'));
                break;
            case 2:
                // Начальник управление регистрации
                $professions = Profession::getAllProfession();
                $study_form = Studyform::getAllStudyform();
                $study_lang = Studylanguage::getStudylang();
                $course = ['1','2','3','4','5'];
                return view('registration/index', compact('professions','study_form','study_lang','course')); 
                break;
            case 3:
                // Отдел тестирование
                return view('TestDepartment/index');
                break;
            case 4:
                // Проректор по воспитательной работе
                //return view('rectorate/index');
                return redirect('rectorate');
                break;
            case 5:
                // Отдел кадр
                return redirect('kadr');
                break;
            case 6:
                // деканат
                $professions = Profession::getAllProfession();
                return view('deanery/prilojenie', compact('professions'));
                break;
            case 7:
                // студенческий отдел
                //$professions = Profession::getAllProfession();

                if(Cache::has('sport_sections')){
                    $sport_sections = Cache::get('sport_sections');
                    $univer_clubs = Cache::get('univer_clubs');
                    $creative_clubs = Cache::get('creative_clubs');
                    $subjects = Cache::get('subjects');
                    $students = Cache::get('students');
                }else{
                    $sport_sections = DB::select("SELECT * FROM phelper.sport_sections");
                    $univer_clubs = DB::select("SELECT * FROM phelper.univer_clubs");
                    $creative_clubs = DB::select("SELECT * FROM phelper.creative_clubs");
                    $subjects = DB::select("SELECT * FROM phelper.subjects");

                    $students = DB::select("SELECT ST.StudentID AS id, CONCAT(ST.lastname,' ',ST.firstname,' ',ST.patronymic) AS fio FROM nitro.students ST
											WHERE ST.isStudent=1 AND YEAR(ST.StartDate)=2017 ORDER BY ST.lastname, ST.firstname, ST.patronymic LIMIT 20");

                    Cache::put('sport_sections', $sport_sections, 30);
                    Cache::put('univer_clubs', $univer_clubs, 30);
                    Cache::put('creative_clubs', $creative_clubs, 30);
                    Cache::put('subjects', $subjects, 30);
                    Cache::put('students', $students, 30);
                }

                return view('student/index', compact('sport_sections', 'univer_clubs', 'creative_clubs', 'students', 'subjects'));
                break;
            case 8:
                // приемная комиссия
                $professions = Profession::getAllProfession();
                $sport_sections = DB::select("SELECT * FROM phelper.sport_sections");
                $univer_clubs = DB::select("SELECT * FROM phelper.univer_clubs");
                $creative_clubs = DB::select("SELECT * FROM phelper.creative_clubs");
                $subjects = DB::select("SELECT * FROM phelper.subjects");
                $id_tutor = $_SESSION['id_tutor'];
                //$id_univer = DB::select("SELECT PT.id_univer FROM phelper.tutor_univer PT WHERE PT.id_tutor=$id_tutor LIMIT 1");
                //$id_univer = $id_univer[0]->id_univer;
                $students = DB::select("SELECT ST.StudentID AS id, CONCAT(ST.lastname,' ',ST.firstname,' ',ST.patronymic) AS fio FROM nitro.cafedras NC 
                INNER JOIN nitro.profession_cafedra PC ON PC.cafedraID=NC.cafedraID
                INNER JOIN nitro.students ST ON ST.ProfessionID=PC.professionID
                WHERE ST.isStudent=1 AND PC.deleted IS NULL AND YEAR(ST.StartDate)=2017 ORDER BY ST.lastname, ST.firstname, ST.patronymic");
                return view('reception/index', compact('professions', 'sport_sections', 'univer_clubs', 'creative_clubs', 'students', 'subjects'));
                break;
        }
    }

    # Transcript
    public function transcript(){
        if($_SESSION['id_tutor'] == 2245){
            $professions = Profession::getAllProfession();
            $study_form = Studyform::getAllStudyform();
            $study_lang = Studylanguage::getStudylang();
            $course = ['1','2','3','4','5'];
            return view('registration/transcript', compact('professions','study_form','study_lang','course'));
        }else{
            switch (Contingent::check_user_role($_SESSION['id_tutor'])){
                case 1:
                    // Методист-регистратор
                    $professions = Contingent::getContingentProfession($_SESSION['id_tutor']);
                    $study_lang = Studylanguage::getStudylang();
                    return view('registration/transcript', compact('professions','study_lang'));
                    break;
                case 2:
                    // Начальник управление регистрации
                    $professions = Profession::getAllProfession();
                    $study_form = Studyform::getAllStudyform();
                    $study_lang = Studylanguage::getStudylang();
                    $course = ['1','2','3','4','5'];
                    return view('registration/transcript', compact('professions','study_form','study_lang','course'));
                    break;
            }
        }

    }

    # Список методистов
    public function methodists(){
        $methodists = DB::select(DB::raw("SELECT 
                                            T.TutorID,T.lastname,T.firstname,T.patronymic,VBN.sum,T.Login 
                                            FROM nitro.tutors T 
                                            LEFT JOIN nitro.person_roles P ON P.personID=T.TutorID 
                                            LEFT JOIN (SELECT 
                                                                    PC.id_tutor, SUM(PVP.count) AS sum
                                                                    FROM phelper.contingent PC
                                                                    LEFT JOIN (SELECT COUNT(*) as count,ProfessionID,StudyFormID,CourseNumber 
                                                                               FROM nitro.students WHERE isStudent=1 GROUP BY ProfessionID,StudyFormID,CourseNumber) AS PVP 
                                                                             ON PVP.ProfessionID=PC.id_profession AND PVP.StudyFormID=PC.id_study_form AND PVP.CourseNumber=PC.id_course
                                                                GROUP BY PC.id_tutor) AS VBN 
                                                                ON VBN.id_tutor=T.TutorID
                                            WHERE deleted='0' AND P.roleID='62'"));
        return view('registration/methodists', compact('methodists'));
    }

    public function contingent(){
        $methodists = DB::select(DB::raw("SELECT * FROM tutors T LEFT JOIN person_roles P ON P.personID=T.TutorID WHERE deleted='0' AND P.roleID='62' ORDER BY T.lastname"));
        $profession = DB::select(DB::raw("SELECT DISTINCT_VYD.ProfessionID AS id,CONCAT_WS(\"-\",P.ProfessionCode,P.ProfessionNameRU) AS name
 FROM (SELECT DISTINCT ProfessionID FROM (SELECT DISTINCTROW ProfessionID,StudyFormID,CourseNumber,
              CONCAT(CAST(ProfessionID AS CHAR(10)),'_',CAST(StudyFormID AS char(10)),'_',CAST(CourseNumber AS char(10))) AS D 
          FROM nitro.students WHERE isStudent=1) vyd
               WHERE D NOT IN (SELECT CONCAT(CAST(id_profession AS CHAR(10)),'_',CAST(id_study_form AS char(10)),'_',CAST(id_course AS char(10))) AS D
															 FROM phelper.contingent)) DISTINCT_VYD
LEFT JOIN nitro.professions P ON P.professionid=DISTINCT_VYD.ProfessionID ORDER BY P.ProfessionNameRU"));
        $study_forms = DB::select(DB::raw("SELECT * FROM studyforms WHERE Id IN(1,3,4,6,7,9,10,12,13)"));
        return view('registration/contingent',compact('methodists','profession','study_forms'));
    }

    public function change(Request $request){
        $id_tutor = $request->id_tutor;
        $profession = $request->id_profession;
        $full4 = $request->full4;
        $full1 = $request->full1;
        $full3 = $request->full3;
        $full6 = $request->full6;
        $full7 = $request->full7;
        $full10 = $request->full10;
        $full12 = $request->full12;
        $full9 = $request->full9;
        $full13 = $request->full13;
        # Очная 5
        if($full4){
            for($i=0; $i<count($full4); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 4,
                    'id_course' => $full4[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Очная 4
        if($full1){
            for($i=0; $i<count($full1); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 1,
                    'id_course' => $full1[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Очная 3
        if($full3){
            for($i=0; $i<count($full3); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 3,
                    'id_course' => $full3[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Заочная 3
        if($full6){
            for($i=0; $i<count($full6); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 6,
                    'id_course' => $full6[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Заочная 2
        if($full7){
            for($i=0; $i<count($full7); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 7,
                    'id_course' => $full7[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Научно-педагогическая
        if($full10){
            for($i=0; $i<count($full10); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 10,
                    'id_course' => $full10[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Научно-профильная
        if($full12){
            for($i=0; $i<count($full12); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 12,
                    'id_course' => $full12[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Профильная
        if($full9){
            for($i=0; $i<count($full9); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 9,
                    'id_course' => $full9[$i],
                    'role_id' => 1
                ]);
            }
        }
        # Докторантура
        if($full13){
            for($i=0; $i<count($full13); $i++){
                Contingent::create([
                    'id_tutor' => $id_tutor,
                    'id_profession' => $profession,
                    'id_study_form' => 13,
                    'id_course' => $full13[$i],
                    'role_id' => 1
                ]);
            }
        }

        return redirect('user/contingent');
    }

    public function study_forms($id){
        $id_tutor = $_SESSION['id_tutor'];
        if($id_tutor == 2245){
            $result = DB::select("SELECT NS.Id AS id, NS.NameRu AS name FROM phelper.contingent PC 
                                LEFT JOIN nitro.studyforms NS ON NS.Id=PC.id_study_form
                              WHERE PC.id_profession='$id' GROUP BY PC.id_study_form");
        }else{
            if(Contingent::check_user_role($id_tutor) == 2){
                $result = DB::select("SELECT NS.Id AS id, NS.NameRu AS name FROM phelper.contingent PC 
                                LEFT JOIN nitro.studyforms NS ON NS.Id=PC.id_study_form
                              WHERE PC.id_profession='$id' GROUP BY PC.id_study_form");
            }else if(Contingent::check_user_role($id_tutor) == 6){
                $result = DB::select("SELECT NS.Id AS id, NS.NameRu AS name FROM phelper.contingent PC 
                                LEFT JOIN nitro.studyforms NS ON NS.Id=PC.id_study_form
                              WHERE PC.id_profession='$id' GROUP BY PC.id_study_form");
            }else{
                $result = DB::select("SELECT NS.Id AS id, NS.NameRu AS name FROM phelper.contingent PC 
                                LEFT JOIN nitro.studyforms NS ON NS.Id=PC.id_study_form
                              WHERE PC.id_profession='$id' AND PC.id_tutor='$id_tutor' GROUP BY PC.id_study_form");
            }
        }
        return json_encode($result);
    }

    public function specialization($id){
        $result = DB::select("SELECT SC.id,SC.nameru,SC.namekz,SC.nameen FROM specializations SC
                                LEFT JOIN profession_cafedra PC ON PC.id=SC.prof_caf_id
                                WHERE PC.professionID='$id' AND SC.deleted IS NULL");
        return json_encode($result);
    }

    public function group($id){
        $result = DB::select("SELECT NG.groupID AS id, NG.name AS nameru FROM nitro.groups NG
                              WHERE NG.specializationID='$id' AND NG.deleted IS NULL");
        return json_encode($result);
    }

    public function course($pid,$sid){
        $id_tutor = $_SESSION['id_tutor'];
        if($id_tutor == 2245){
            $result = DB::select("SELECT PC.id_course AS course FROM phelper.contingent PC
                                WHERE PC.id_profession='$pid' AND PC.id_study_form='$sid'");
        }elseif($id_tutor == 151){
            $result = DB::select("SELECT PC.id_course AS course FROM phelper.contingent PC
                                WHERE PC.id_profession='$pid' AND PC.id_study_form='$sid'");
        }else{
            $result = DB::select("SELECT PC.id_course AS course FROM phelper.contingent PC
                                WHERE PC.id_profession='$pid' AND PC.id_tutor='$id_tutor' AND PC.id_study_form='$sid'");
        }

        return json_encode($result);
    }

    public function vedomost($pid,$sid,$course,$term,$lang){
        $id = $_SESSION['id_tutor'];
        DB::select("DELETE FROM phelper.temp WHERE id='$id'");

        DB::select("INSERT INTO phelper.temp (id,queryid,studentid,year,term,tupsid,professionid,studyformid,studylangid,fio_student,id_group,id_calendar,group_title,id_spec,spec_name)
                        SELECT $id,IUP.QueryID, IUP.StudentID,IUP.`year`,IUP.term,IUP.tupsid,IUP.ProfessionID,IUP.StudyFormID,IUP.StudyLanguageID,
                                     CONCAT(CASE IUP.lastname WHEN '' THEN '' ELSE IUP.lastname END,
                                            CASE IUP.firstname WHEN '' THEN '' ELSE CONCAT(' ',IUP.firstname) END,
                                            CASE IUP.patronymic WHEN '' THEN '' ELSE CONCAT(' ',IUP.patronymic) END) AS fio_student,
                                             IUP.groupID,IUP.studyCalendarID,IUP.group_name,IUP.specializationID,IUP.spec_name
                        FROM
                        (SELECT q1.QueryID,q1.StudentID,q1.year,q1.term,q1.SubjectID AS tupsid,S.ProfessionID,S.StudyFormID,S.StudyLanguageID,S.lastname,S.firstname,S.patronymic,S.groupID,S.studyCalendarID,S.group_name,S.specializationID,S.spec_name
                          FROM nitro.queries q1,
                          (SELECT ST.StudentID,ST.CourseNumber,ST.ProfessionID,ST.StudyFormID,ST.StudyLanguageID,ST.lastname,ST.firstname,ST.patronymic,ST.groupID,ST.studyCalendarID,NG.name AS group_name,ST.specializationID,NS.nameru AS spec_name
                          FROM nitro.students ST
                          LEFT JOIN nitro.groups NG ON NG.groupID=ST.groupID
                          LEFT JOIN nitro.specializations NS ON NS.id=ST.specializationID
                          WHERE ST.ProfessionID='$pid' AND ST.StudyFormID='$sid' AND ST.StudyLanguageID='$lang' AND ST.CourseNumber='$course' AND ST.isStudent=1) S
                          WHERE q1.StudentID=S.StudentID AND q1.term='$term' AND q1.year=S.CourseNumber) IUP");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'L') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.id=$id");


        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PS') AS OOP
                        SET PT.studygroupid_p=OOP.studygroupid, PT.grouptypeid_p=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid ");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PS') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'Lab') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'SP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'WP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'SRW') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS OOT,nitro.subjects NS
                        SET OOT.subjectnameru=NS.SubjectNameRU, OOT.subjectcode=NS.SubjectCodeRu
                        WHERE OOT.subjectid=NS.SubjectID  AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.tutors NT
                        SET OOT.fio_tutor=CONCAT(CASE NT.lastname WHEN '' THEN '' ELSE NT.lastname END,
                                            CASE NT.firstname WHEN '' THEN '' ELSE CONCAT(' ',NT.firstname) END,
                                            CASE NT.patronymic WHEN '' THEN '' ELSE CONCAT(' ',NT.patronymic) END),
                            OOT.tutor_inicial=CONCAT(CASE NT.lastname WHEN '' THEN '' ELSE NT.lastname END,
                                              CASE NT.firstname WHEN '' THEN '' ELSE CONCAT(' ',LEFT(NT.firstname,1)) END,
                                              CASE NT.patronymic WHEN '' THEN '' ELSE CONCAT('.',LEFT(NT.patronymic,1),'.') END)                
                        WHERE OOT.tutorid=NT.TutorID AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.tupsubjects NTUP
                        SET OOT.creditcount=NTUP.creditscount
                        WHERE OOT.tupsid=NTUP.tupSubjectID AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.professions NPRO
                        SET OOT.pname=CONCAT_WS('-',NPRO.professionCode,NPRO.professionNameRU)
                        WHERE NPRO.professionID=OOT.professionid AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.studyforms NSTU
                        SET OOT.form=NSTU.NameRu
                        WHERE NSTU.Id=OOT.studyformid AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.studylanguages NSL
                        SET OOT.lang=NSL.NameRu
                        WHERE NSL.Id=OOT.studylangid AND OOT.id=$id");

        $subject = DB::select("SELECT PT.subjectid AS id,PT.subjectnameru AS name,PT.subjectcode AS code,NG.nameru AS type FROM phelper.temp PT
                               LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=PT.grouptypeid
                               WHERE PT.id=$id GROUP BY PT.subjectid");

        return json_encode($subject);
    }

    public function teacher($sid){
        $id = $_SESSION['id_tutor'];
        $result = DB::select("SELECT PT.studygroupid AS stream, PT.fio_tutor AS tutor FROM phelper.temp PT WHERE PT.subjectid=$sid AND id=$id GROUP BY PT.studygroupid");
        return json_encode($result);
    }

    public function individual($pid,$sid,$course,$lang,$status,$from_tran){
        $id = $_SESSION['id_tutor'];
        DB::select("DELETE FROM phelper.temp WHERE id='$id'");
		if($status == 0 OR $status == null){
		    $status = 1;
        }
        if($from_tran == 1){
		    $status = 4;
        }
        switch ($status){
            case 1:
                DB::select("INSERT INTO phelper.temp (id,queryid,studentid,year,term,tupsid,professionid,studyformid,studylangid,fio_student,id_group,id_calendar,group_title,id_spec,spec_name,id_curriculum)
                        SELECT $id,IUP.QueryID, IUP.StudentID,IUP.`year`,IUP.term,IUP.tupsid,IUP.ProfessionID,IUP.StudyFormID,IUP.StudyLanguageID,
                                     CONCAT(CASE IUP.lastname WHEN '' THEN '' ELSE IUP.lastname END,
                                            CASE IUP.firstname WHEN '' THEN '' ELSE CONCAT(' ',IUP.firstname) END,
                                            CASE IUP.patronymic WHEN '' THEN '' ELSE CONCAT(' ',IUP.patronymic) END) AS fio_student,
                                             IUP.groupID,IUP.studyCalendarID,IUP.group_name,IUP.specializationID,IUP.spec_name,IUP.TypCurriculumID
                        FROM
                        (SELECT q1.QueryID,q1.StudentID,q1.year,q1.term,q1.SubjectID AS tupsid,S.ProfessionID,S.StudyFormID,S.StudyLanguageID,S.lastname,S.firstname,S.patronymic,S.groupID,S.studyCalendarID,S.group_name,S.specializationID,S.spec_name,S.TypCurriculumID
                          FROM nitro.queries q1,
                          (SELECT ST.StudentID,ST.CourseNumber,ST.ProfessionID,ST.StudyFormID,ST.StudyLanguageID,ST.lastname,ST.firstname,ST.patronymic,ST.groupID,ST.studyCalendarID,NG.name AS group_name,ST.specializationID,NS.nameru AS spec_name,ST.TypCurriculumID
                          FROM nitro.students ST
                          LEFT JOIN nitro.groups NG ON NG.groupID=ST.groupID
                          LEFT JOIN nitro.specializations NS ON NS.id=ST.specializationID
                          WHERE ST.ProfessionID='$pid' AND ST.StudyFormID='$sid' AND ST.StudyLanguageID='$lang' AND ST.CourseNumber='$course' AND ST.isStudent='$status') S
                          WHERE q1.StudentID=S.StudentID) IUP");

                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'L') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.id='$id'");


                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PS') AS OOP
                        SET PT.studygroupid_p=OOP.studygroupid, PT.grouptypeid_p=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid ");

                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PS') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'Lab') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'SP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'WP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

                DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent='$status') NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'SRW') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

                DB::select("UPDATE phelper.temp AS OOT,nitro.subjects NS
                        SET OOT.subjectnameru=NS.SubjectNameRU, OOT.subjectcode=NS.SubjectCodeRu
                        WHERE OOT.subjectid=NS.SubjectID  AND OOT.id=$id");

                DB::select("UPDATE phelper.temp AS OOT,nitro.tutors NT
                        SET OOT.fio_tutor=CONCAT(CASE NT.lastname WHEN '' THEN '' ELSE NT.lastname END,
                                            CASE NT.firstname WHEN '' THEN '' ELSE CONCAT(' ',NT.firstname) END,
                                            CASE NT.patronymic WHEN '' THEN '' ELSE CONCAT(' ',NT.patronymic) END),
                            OOT.tutor_inicial=CONCAT(CASE NT.lastname WHEN '' THEN '' ELSE NT.lastname END,
                                              CASE NT.firstname WHEN '' THEN '' ELSE CONCAT(' ',LEFT(NT.firstname,1)) END,
                                              CASE NT.patronymic WHEN '' THEN '' ELSE CONCAT('.',LEFT(NT.patronymic,1),'.') END)              
                        WHERE OOT.tutorid=NT.TutorID AND OOT.id='$id'");

                DB::select("UPDATE phelper.temp AS OOT,nitro.tupsubjects NTUP
                        SET OOT.creditcount=NTUP.creditscount
                        WHERE OOT.tupsid=NTUP.tupSubjectID AND OOT.id='$id'");

                DB::select("UPDATE phelper.temp AS OOT,nitro.professions NPRO
                        SET OOT.pname=CONCAT_WS('-',NPRO.professionCode,NPRO.professionNameRU)
                        WHERE NPRO.professionID=OOT.professionid AND OOT.id='$id'");

                DB::select("UPDATE phelper.temp AS OOT,nitro.studyforms NSTU
                        SET OOT.form=NSTU.NameRu
                        WHERE NSTU.Id=OOT.studyformid AND OOT.id='$id'");

                DB::select("UPDATE phelper.temp AS OOT,nitro.studylanguages NSL
                        SET OOT.lang=NSL.NameRu
                        WHERE NSL.Id=OOT.studylangid AND OOT.id='$id'");

                $student = DB::select("SELECT DISTINCT PT.studentid AS id,PT.fio_student AS fio FROM phelper.temp PT WHERE PT.id='$id' ORDER BY PT.fio_student");
                break;

            case 2:
                // выпуск
                $student = DB::select("SELECT ST.StudentID AS id, CONCAT(ST.lastname,' ',ST.firstname,' ',ST.patronymic) AS fio FROM nitro.students ST
                INNER JOIN nitro.totalmarks TR ON TR.StudentID=ST.StudentID
                INNER JOIN nitro.orderstudentinfo ND ON ND.studentID=ST.StudentID
                INNER JOIN nitro.orders NR ON NR.orderID=ND.orderID
                WHERE ST.ProfessionID='$pid' AND ST.CourseNumber=0 AND ST.StudyFormID='$sid' AND ST.StudyLanguageID='$lang' AND ST.isStudent=3 
                AND YEAR(NR.orderdate)=YEAR(CURRENT_TIMESTAMP()) GROUP BY ST.lastname");
                break;

            case 3:
                // отчислен
                $student = DB::select("SELECT ST.StudentID AS id, CONCAT(ST.lastname,' ',ST.firstname,' ',ST.patronymic) AS fio FROM nitro.students ST
                LEFT JOIN nitro.transcript TR ON TR.StudentID=ST.StudentID
                WHERE ST.ProfessionID='$pid' AND ST.CourseNumber='$course' AND ST.StudyFormID='$sid' AND ST.StudyLanguageID='$lang' AND ST.isStudent=3
                GROUP BY ST.lastname");
                break;
            case 4:
                // обучающий данные брать из таблицы транскрипт
                $student = DB::select("SELECT ST.StudentID AS id, CONCAT(ST.lastname,' ',ST.firstname,' ',ST.patronymic) AS fio FROM nitro.students ST
                LEFT JOIN nitro.transcript TR ON TR.StudentID=ST.StudentID
                WHERE ST.ProfessionID='$pid' AND ST.CourseNumber='$course' AND ST.StudyFormID='$sid' AND ST.StudyLanguageID='$lang' AND ST.isStudent=1
                GROUP BY ST.lastname,ST.firstname, ST.patronymic");
                break;
        }

        return json_encode($student);
    }
    
    public function test(){
        $filename = $_FILES['file']['tmp_name'];
        $objPHPExcel = PHPExcel_IOFactory::load($filename);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        for($i = 50; $i<count($sheetData); $i++){
            $val_studentid = (int) $sheetData[$i]['H'];
            $val_queryid   = (int) $sheetData[$i]['I'];
            $val_procent   = (int) $sheetData[$i]['E'];
            if (($val_queryid <> 0) AND ($val_studentid <> 0)) {
                DB::transaction(function() use ($val_procent,$val_studentid,$val_queryid){
                    // totalmarks
                    if($val_procent < 50){
                        DB::statement("UPDATE nitro.totalmarks SET exammark=$val_procent, ap_exammark=$val_procent, totalmark=0,ap_totalmark=0  WHERE studentID=$val_studentid AND queryID=$val_queryid AND exammark IS NULL AND ap_exammark IS NULL");
                    }else{
                        DB::statement("UPDATE nitro.totalmarks SET exammark=$val_procent, ap_exammark=$val_procent, totalmark=(ap_ratings*0.6 + $val_procent*0.4),ap_totalmark=(ap_ratings*0.6 + $val_procent*0.4)  WHERE studentID=$val_studentid AND queryID=$val_queryid AND exammark IS NULL AND ap_exammark IS NULL");
                    }

                    $result1 = DB::select("SELECT NT.studygroupID FROM nitro.totalmarks NT WHERE  NT.studentID='$val_studentid' AND NT.queryID='$val_queryid'");
                    if(count($result1) > 0){
                        $study_group_id = $result1[0]->studygroupID;
                        $journal = DB::select("SELECT * FROM nitro.journal NJ WHERE NJ.StudentID='$val_studentid' AND NJ.StudyGroupID='$study_group_id' AND NJ.markTypeID=3 AND NJ.isCurrent=1");
                        if(count($journal) > 0){
                            // делаем обновление
                            DB::update("UPDATE nitro.journal SET Mark='$val_procent',MarkDate=CURDATE(),created=CURRENT_TIMESTAMP() WHERE studentID='$val_studentid' AND studygroupid='$study_group_id' AND markTypeID=3 AND isCurrent=1 AND Mark IS NULL");
                        }else{
                            // записываем в журнал
                            DB::insert("INSERT INTO nitro.journal(StudentID,Mark,MarkDate,StudyGroupID,markTypeID,created,number,isCurrent) 
                            SELECT studentid, ap_exammark, CURDATE(),studygroupid,3,CURRENT_TIMESTAMP(),0,1 FROM nitro.totalmarks WHERE studentID='$val_studentid' AND queryID='$val_queryid' AND isCurrent=1");
                        }

                        // таблица er_marks
                        $report = DB::select("SELECT * FROM nitro.erwithappealreports WHERE groupID='$study_group_id'");
                        $id_report = $report[0]->reportID;
                        $er_marks = DB::select("SELECT * FROM nitro.er_marks NE WHERE NE.reportID='$id_report' AND NE.studentID='$val_studentid' AND NE.markTypeID=3");
                        if(count($er_marks) > 0){
                            // обновляем
                            DB::update("UPDATE nitro.er_marks SET mark='$val_procent',ap_mark='$val_procent' WHERE studentID='$val_studentid' AND reportID='$id_report' AND markTypeID=3 AND mark IS NULL AND ap_mark IS NULL");
                        }else{
                            DB::insert("INSERT INTO nitro.er_marks(reportID,studentID,markTypeID,number,mark,ap_mark) VALUES($id_report, $val_studentid,3,0,$val_procent, $val_procent)");
                        }
                    }
                });
            } else {
                continue;
            }
        }
        Session::flash('message', "Успешно обновлено.");
        return redirect()->back();
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
                    $god = (int)$current_year - 2;
                    return $god;
                }else{
                    dd("Еще не время для сводного ведомоста");
                }
                break;
            case 3:
                if($current_month != '09'){
                    $god = (int)$current_year - 3;
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

    # настройки
    public function settings_show_form(){
        $user = User::where(['TutorID' => $_SESSION['id_tutor']])->first();
        return view('registration/settings', compact('user'));
    }

    public function settings(Request $request){
        $tutor_id = $_SESSION['id_tutor'];
        $password = md5($request->get('password'));
        DB::update("UPDATE nitro.tutors NT SET NT.Password='$password' WHERE NT.TutorID='$tutor_id'");
        return redirect()->back()->with('message', 'Пароль успешно обновлен');
    }

    #### ДИПЛОМ ####
    public function prilojenie_excel (Request $request){
        $studentid=$request->studentid;
        //$array_praktika=json_decode($request->array_praktika);
        $is_student=$request->isstudent;
        $table_transcript="nitro.transcript";
        if(($is_student==4) or ($is_student==3)) {
            $table_transcript="nitro.deletedtranscript";
        };

        $array_result=DB::select("SELECT students . firstname,students . lastname,students . patronymic ,
                                  ifnull(if( students . seriyaAttestata  <> '',  students . seriyaAttestata , ' - '), ' - ') AS  seriyaAttestata ,
                                  ifnull(if( students . nomerAttestata  <> '',  students . nomerAttestata , ' - '), ' - ') AS  nomerAttestata ,
                                  ifnull(if( students . dataVydachiAttestata  <> '', DATE_FORMAT( students . dataVydachiAttestata ,'%d.%m.%Y'), ' - '), ' - ') AS  dataVydachiAttestata ,
                                  concat( professions . professionCode , ' - ',  professions . professionNameRU ) AS  profession ,
                                  concat( professions . professionCode , ' - ',  professions . professionNameKZ ) AS  professionkz ,
                                  concat( professions . professionCode , ' - ',  professions . professionNameEN ) AS  professionen ,
                                   specializations . nameru  AS  specializatiya ,
                                   specializations . namekz  AS  specializatiyakz ,
                                   specializations . nameen  AS  specializatiyaen ,
                                  DATE_FORMAT( students . BirthDate , '%d.%m.%Y') AS  BirthDate ,
                                  DATE_FORMAT( o . orderdate , '%d.%m.%Y') AS  StartDate ,
                                  DATE_FORMAT( O2 . orderdate , '%d.%m.%Y') AS  FinishDate ,
                                   studyforms . NameRu  AS  forma_obuch ,studyforms . NameKZ  AS  forma_obuchkz ,studyforms . NameEN  AS  forma_obuchen ,studyforms . degreeID ,
                                   students . enterexams ,students . enterexamskz ,students . enterexamsen ,
                                   students . pasteduinfo ,students . pasteduinfokz ,students . pasteduinfoen ,
                                   students . enteredinru ,students . enteredinkz ,students . enteredinen ,                                   
                                   students . firstname_en ,students . lastname_en ,students . patronymic_en ,
                                   students . end_school ,students . end_college ,students . end_high_school ,students . end_other ,
                                   students . series_number_doc_education ,students . date_doc_education ,students . seriyaDiploma ,
                                   DATE_FORMAT( students . dataVydachiDiploma ,'%d.%m.%Y') as dataVydachiDiploma,students . diploma_number,
                                   students.registerNumberDiplom                                

                                    FROM
                                       nitro.professions
                                      INNER JOIN  nitro.profession_cafedra  ON ( professions . professionID  =  profession_cafedra . professionID )
                                      INNER JOIN  nitro.specializations  ON ( profession_cafedra . id  =  specializations . prof_caf_id )
                                      INNER JOIN  nitro.students  ON ( specializations . id  =  students . specializationID )
                                      INNER JOIN  nitro.studyforms  ON ( students . StudyFormID  =  studyforms . Id )
                                      INNER JOIN nitro.orderstudentinfo OSI ON (OSI.studentID=students.StudentID AND OSI.oldcourse=0 AND OSI.oldprofessionid=0 AND OSI.enter_from=1 )
                                      INNER JOIN nitro.orders O ON (OSI.orderID=O.orderID)
                                      INNER JOIN nitro.orderstudentinfo OSI2 ON (OSI2.studentID=students.StudentID AND OSI2.course=0 AND OSI2.professionid=0 AND OSI2.deduct_to=1 )
                                      INNER JOIN nitro.orders O2 ON (OSI2.orderID=O2.orderID)
                                    WHERE
                                       students . StudentID  = $studentid AND
                                       professions . deleted  = 0");


        $array_result2=DB::select("SELECT
                                      transcript.id,
                                      transcript.StudentID,
                                      transcript.subjectcode,
                                      transcript.Credits,
                                      transcript.AlphaMark,
                                      transcript.NumeralMark,
                                      transcript.TotalMark,
                                      transcript.subjectnameRU,
                                      transcript.subjectnameKZ,
                                      transcript.subjectnameEN,
                                      transcript.type
                                    FROM
                                      $table_transcript as transcript
                                    WHERE                                    
                                      transcript.StudentID = $studentid 
                                      ORDER BY subjectnameRU");


        for ($j=0;$j<count($array_result2); $j++){
            if(trim(mb_strtolower($array_result2[$j]->subjectnameRU))== 'производственная практика'){
                $array_result2[$j]->type=1;
            }
            if(trim(mb_strtolower($array_result2[$j]->subjectnameRU))== 'учебная практика'){
                $array_result2[$j]->type=2;
            }
            if(trim(mb_strtolower($array_result2[$j]->subjectnameRU))== 'педагогическая практика'){
                $array_result2[$j]->type=3;
            }
            if(trim(mb_strtolower($array_result2[$j]->subjectnameRU))== 'технологическая практика'){
                $array_result2[$j]->type=4;
            }
            if(trim(mb_strtolower($array_result2[$j]->subjectnameRU))== 'профессиональная практика'){
                $array_result2[$j]->type=5;
            }
        }



        $array_result3=DB::select("SELECT gem.nameworkru as diplom_name,gem.nameworkkz as diplom_namekz,gem.nameworkkz as diplom_nameen,
                                          gem.mark,gem.credits,qe.needName
                                      FROM
                                          generalexamsmarks gem
                                          LEFT JOIN qexaminations qe ON (gem.examID = qe.examID)
                                      WHERE
                                          gem.studentID = $studentid AND 
                                          gem.typeID in (2,7)");
        $array_result4=DB::select("SELECT 
                                    ifnull(if(typcurriculums.creditsOODElect <> '', typcurriculums.creditsOODElect,  0 ), ' - ') AS creditsOODElect,
                                    ifnull(if(typcurriculums.creditsBDElect <> '', typcurriculums.creditsBDElect,  0 ), ' - ') AS creditsBDElect,
                                    ifnull(if(typcurriculums.creditsPDElect <> '', typcurriculums.creditsPDElect,  0 ), ' - ') AS creditsPDElect,
                                    ifnull(if(students.GPA <> '', students.GPA, ' 0  '), ' - ') AS GPA
                                   FROM
                                      students
                                      INNER JOIN typcurriculums ON (students.TypCurriculumID = typcurriculums.CurriculumID)
                                      AND (students.ProfessionID = typcurriculums.ProfessionID)
                                   WHERE
                                    students.StudentID = $studentid");

        $sum=0;
        if(!empty($array_result4)){
            $sum = $array_result4[0]->creditsOODElect+$array_result4[0]->creditsBDElect+$array_result4[0]->creditsPDElect;
        }

        $array_result6=DB::select("SELECT generalexamsmarks.mark,generalexamsmarks.credits,generalexamsmarks.typeID,
                                    generalexams.SubjectRU,generalexams.SubjectKZ,generalexams.SubjectEN
                                    FROM
                                    generalexamsmarks
                                    INNER JOIN generalexams ON generalexamsmarks.examID = generalexams.examID
                                    WHERE
                                    generalexamsmarks.studentID=$studentid AND 
                                    generalexamsmarks.typeID IN (1,3,4,5) AND
                                    generalexams.deleted = 0");

        $sortedru_array_result7=DB::select("SELECT
                                    transcript.StudentID,ltrim(transcript.code_ru)as code_ru,
                                    ltrim(transcript.subjectnameRU) as subjectnameRU,
                                    ltrim(transcript.subjectnameKZ) as subjectnameKZ,
                                    ltrim(transcript.subjectnameEN) as subjectnameEN,
                                    transcript.Credits,
                                    round(transcript.TotalMark) as TotalMark,
                                    round(transcript.NumeralMark) as NumeralMark,
                                    round(transcript.AlphaMark) as AlphaMark
                                    FROM
                                    $table_transcript as transcript
                                    INNER JOIN students ON students.StudentID = transcript.StudentID
                                    WHERE
                                    transcript.StudentID = $studentid AND 
                                    transcript.type=0 AND 
                                    LOWER(ltrim(transcript.subjectnameRU)) NOT IN ('производственная практика','учебная практика','педагогическая практика','технологическая практика','профессиональная практика')
                                   ORDER BY subjectnameRU");
        $sortedkz_array_result7=DB::select("SELECT
                                    transcript.StudentID,ltrim(transcript.subjectcode)as subjectcode,
                                    ltrim(transcript.subjectnameRU) as subjectnameRU,
                                    ltrim(transcript.subjectnameKZ) as subjectnameKZ,
                                    ltrim(transcript.subjectnameEN) as subjectnameEN,
                                    transcript.Credits,
                                    round(transcript.TotalMark) as TotalMark,
                                    round(transcript.NumeralMark) as NumeralMark,
                                    round(transcript.AlphaMark) as AlphaMark
                                    FROM
                                    $table_transcript as transcript
                                    INNER JOIN students ON students.StudentID = transcript.StudentID
                                    WHERE
                                    transcript.StudentID = $studentid AND 
                                    transcript.type=0 AND 
                                    LOWER(ltrim(transcript.subjectnameRU)) NOT IN ('производственная практика','учебная практика','педагогическая практика','технологическая практика','профессиональная практика')
                                   ORDER BY subjectnameKZ COLLATE utf8_unicode_ci");

        $sorteden_array_result7=DB::select("SELECT
                                    transcript.StudentID,ltrim(transcript.code_en)as code_en,
                                    ltrim(transcript.subjectnameRU) as subjectnameRU,
                                    ltrim(transcript.subjectnameKZ) as subjectnameKZ,
                                    ltrim(transcript.subjectnameEN) as subjectnameEN,
                                    transcript.Credits,
                                    round(transcript.TotalMark) as TotalMark,
                                    round(transcript.NumeralMark) as NumeralMark,
                                    round(transcript.AlphaMark) as AlphaMark
                                    FROM
                                    $table_transcript as transcript
                                    INNER JOIN students ON students.StudentID = transcript.StudentID
                                    WHERE
                                    transcript.StudentID = $studentid AND 
                                    transcript.type=0 AND 
                                    LOWER(ltrim(transcript.subjectnameRU)) NOT IN ('производственная практика','учебная практика','педагогическая практика','технологическая практика','профессиональная практика')
                                   ORDER BY subjectnameEN");

        $array_result9=DB::select("SELECT students.firstname,students.lastname,students.patronymic,graduates.firstname,graduates.lastname,
                                    graduates.patronymic,graduates.finishDiplomSeries,
                                    DATE_FORMAT(graduates.finishDocDate,'%d.%m.%y') as finishDocDate,                                  
                                    DATE_FORMAT(graduates.finishOrderDate,'%d.%m.%y') as finishOrderDate,                                  
                                    DATE_FORMAT(graduates.protocolDate,'%d.%m.%y') as protocolDate,
                                    DATE_FORMAT(graduates.startDate,'%d.%m.%y') as startDate,
                                    graduates.regDiplomNumber,graduates.finishOrderNumber,graduates.protocolNumber
                                   FROM
                                      students
                                      INNER JOIN graduates ON (students.iinplt = graduates.iinplt)
                                   where
                                     students.StudentID=$studentid");
        if(!empty($array_result) && !empty($array_result2) && !empty($array_result3) && !empty($array_result4)
            && !empty($array_result6) && !empty($sortedru_array_result7)  && !empty($array_result9))
        {

            $objPHPExcel = new PHPExcel();
            $objRichText = new PHPExcel_RichText();
            //Открываем файл-шаблон
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $file_name = base_path();

            $objPHPExcel = $objReader->load($file_name.'/public/reports/appendix.xls');
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $objPHPExcel->setActiveSheetIndex(0);
            $active_sheet = $objPHPExcel->getActiveSheet();
            //Заносим данные
            $active_sheet->setCellValue('F2', $array_result[0]->lastname);
            $active_sheet->setCellValue('F3', $array_result[0]->firstname . " " . $array_result[0]->patronymic);
            $active_sheet->setCellValue('D5', $array_result[0]->BirthDate);
            //заносим вступительные испытания
            $active_sheet->setCellValue('F9', $array_result[0]->enterexams);
            //$active_sheet->setCellValue('F10', "протокол № " . $array_result9[0]->protocolNumber . " от " . $array_result9[0]->protocolDate . "г.");
            //заносим поступил\окончил
            $active_sheet->setCellValue('A10', "Казахский Национальный Педагогический университет им.Абая, " . $array_result[0]->StartDate . "г.");
            $active_sheet->setCellValue('A11', "Казахский Национальный Педагогический университет им.Абая, " . $array_result[0]->FinishDate . "г.");
            //zanosim predyduwii dokument ob obu4enii
            if ($array_result[0]->end_school == '1') {
                //$active_sheet->setCellValue('G5',$row['pasteduinfo);
                $active_sheet->setCellValue('G6', "аттестат");
                $active_sheet->setCellValue('A7', $array_result[0]->seriyaAttestata . '№ ' . $array_result[0]->nomerAttestata. " от " . $array_result[0]->dataVydachiAttestata . " года");
                //$active_sheet->setCellValue('B6','№ '.$row['nomerAttestata);
                //$active_sheet->setCellValue('A8', "от " . $array_result[0]->dataVydachiAttestata . " года");
            };
            if ($array_result[0]->end_college == '1' or $array_result[0]->end_high_school == '1' or $array_result[0]->end_other == '1') {
                //$active_sheet->setCellValue('G5',$row['pasteduinfo);
                //$active_sheet->setCellValue('A6',$row['seriyaAttestata);
                $active_sheet->setCellValue('G6', "диплом");
                $active_sheet->setCellValue('A7', 'Протокол № ' . $array_result9[0]->finishDiplomSeries ." от " . $array_result9[0]->finishDocDate . " года");
                //$active_sheet->setCellValue('A7', 'Протокол № ' . $array_result[0]->seriyaDiploma ." от " . $array_result[0]->dataVydachiDiploma . " года");
                //$active_sheet->setCellValue('A8', "от " . $array_result[0]->dataVydachiDiploma . " года");
            };
            //zanosim dannye o diplomke
            $active_sheet->setCellValue('L19', $array_result[0]->seriyaDiploma);
            $active_sheet->setCellValue('L22', $array_result[0]->dataVydachiDiploma);
            $active_sheet->setCellValue('L24', $array_result[0]->registerNumberDiplom);
            //$active_sheet->GetStyle('L10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
            //zanosim kaz variant
            $objPHPExcel->setActiveSheetIndex(3);
            $active_sheet = $objPHPExcel->getActiveSheet(3);
            $active_sheet->setCellValue('L19', $array_result[0]->seriyaDiploma);
            $active_sheet->setCellValue('L22', $array_result[0]->dataVydachiDiploma);
            $active_sheet->setCellValue('L24', $array_result[0]->registerNumberDiplom);
            //$active_sheet->GetStyle('L10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
            //zanosim eng variant
            $objPHPExcel->setActiveSheetIndex(5);
            $active_sheet = $objPHPExcel->getActiveSheet(5);
            $active_sheet->setCellValue('K18', $array_result[0]->seriyaDiploma);
            $active_sheet->setCellValue('K21', $array_result[0]->dataVydachiDiploma);
            $active_sheet->setCellValue('K23', $array_result[0]->registerNumberDiplom);
            //$active_sheet->GetStyle('L10')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
            $objPHPExcel->setActiveSheetIndex(2);
            $active_sheet = $objPHPExcel->getActiveSheet(2);

            //zanosim akadem. stepen'
            if ($array_result[0]->degreeID == '1') {
                $active_sheet->setCellValue('C59', "академическая степень бакалавра");
            };
            if ($array_result[0]->degreeID == '2') {
                $active_sheet->setCellValue('C59', "академическая степень магистранта");
            };
            if ($array_result[0]->degreeID == '6') {
                $active_sheet->setCellValue('C59', "академическая степень докторанта");
            };
            //zanosim special'nost'
            $active_sheet->setCellValue('C60', $array_result[0]->profession . " - " . $array_result[0]->forma_obuch);
            //zanosim specializaciu'
            $active_sheet->setCellValue('D63', $array_result[0]->specializatiya);
            $objPHPExcel->setActiveSheetIndex(0);
            $active_sheet = $objPHPExcel->getActiveSheet(0);
            //zanosim diplom
            $row_start = 37;
            //return count($array_result3);
            for ($i = 0; $i < count($array_result3); $i++) {
                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $array_result3[$i]->diplom_name);
                $active_sheet->setCellValue('F' . $row_next, $array_result3[$i]->credits);
                if ($array_result3[$i]->credits == '2') {
                    $active_sheet->setCellValue('G' . $row_next, '8');
                };
                if ($array_result3[$i]->credits == '3') {
                    $active_sheet->setCellValue('G' . $row_next, '10');
                };
                if ($array_result3[$i]->credits == '4') {
                    $active_sheet->setCellValue('G' . $row_next, '13');
                };

                //$active_sheet->setCellValue('I37',$row3['mark);
                if ($array_result3[$i]->mark == '100') {
                    $active_sheet->setCellValue('H' . $row_next, 'A');
                    $active_sheet->setCellValue('J' . $row_next, 'отлично');
                    $active_sheet->setCellValue('I' . $row_next, '4');
                } else {
                    if ($array_result3[$i]->mark >= '95' && $array_result3[$i]->mark < '100') {
                        $active_sheet->setCellValue('H' . $row_next, 'A');
                        $active_sheet->setCellValue('J' . $row_next, 'отлично');
                        $active_sheet->setCellValue('I' . $row_next, '4');
                    };
                    if ($array_result3[$i]->mark >= '90' && $array_result3[$i]->mark < '95') {
                        $active_sheet->setCellValue('H' . $row_next, 'A-');
                        $active_sheet->setCellValue('J' . $row_next, 'отлично');
                        $active_sheet->setCellValue('I' . $row_next, '3,67');
                    };
                    if ($array_result3[$i]->mark >= '85' && $array_result3[$i]->mark < '90') {
                        $active_sheet->setCellValue('H' . $row_next, 'B+');
                        $active_sheet->setCellValue('J' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('I' . $row_next, '3,33');
                    };
                    if ($array_result3[$i]->mark >= '80' && $array_result3[$i]->mark < '85') {
                        $active_sheet->setCellValue('H' . $row_next, 'B');
                        $active_sheet->setCellValue('J' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('I' . $row_next, '3');
                    };
                    if ($array_result3[$i]->mark >= '75' && $array_result3[$i]->mark < '80') {
                        $active_sheet->setCellValue('H' . $row_next, 'B-');
                        $active_sheet->setCellValue('J' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('I' . $row_next, '2,67');
                    };
                    if ($array_result3[$i]->mark >= '70' && $array_result3[$i]->mark < '75') {
                        $active_sheet->setCellValue('H' . $row_next, 'C+');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '2,33');
                    };
                    if ($array_result3[$i]->mark >= '65' && $array_result3[$i]->mark < '70') {
                        $active_sheet->setCellValue('H' . $row_next, 'C');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '2');
                    };
                    if ($array_result3[$i]->mark >= '60' && $array_result3[$i]->mark < '65') {
                        $active_sheet->setCellValue('H' . $row_next, 'C-');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '1,67');
                    };
                    if ($array_result3[$i]->mark >= '55' && $array_result3[$i]->mark < '60') {
                        $active_sheet->setCellValue('H' . $row_next, 'D+');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '1,33');
                    };
                    if ($array_result3[$i]->mark >= '50' && $array_result3[$i]->mark < '55') {
                        $active_sheet->setCellValue('H' . $row_next, 'D');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '1');

                    };
                    if ($array_result3[$i]->mark >= '0' && $array_result3[$i]->mark < '50') {
                        $active_sheet->setCellValue('H' . $row_next, 'F');
                        $active_sheet->setCellValue('J' . $row_next, 'плохо');
                        $active_sheet->setCellValue('I' . $row_next, '0');
                    };
                };
            };
            //заносим общее число кредитов и GPA
            //  $active_sheet->setCellValue('J42', $sum . '/' . $array_result8[0]['avg_mark);
            $active_sheet->setCellValue('H16', $array_result4[0]->GPA);
            //заносим оценки за Гос.экзамен
            $row_start = 28;
            for ($i = 0; $i < count($array_result6); $i++) {
                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $array_result6[$i]->SubjectRU);
                $active_sheet->setCellValue('F' . $row_next, $array_result6[$i]->credits);
                //esli bakalavr
                if ($array_result6[$i]->typeID == 3) {
                    if ($array_result6[$i]->credits == '1') {
                        $active_sheet->setCellValue('G' . $row_next, '4');
                    };
                }
                //esli magistr ili doktor
                if (($array_result6[$i]->typeID == 4) or ($array_result6[$i]->typeID == 5)) {
                    if ($array_result6[$i]->credits == '1') {
                        $active_sheet->setCellValue('G' . $row_next, '3');
                    };
                }



                $active_sheet->setCellValue('I' . $row_next, $array_result6[$i]->mark);
                if ($array_result6[$i]->mark == '100') {
                    $active_sheet->setCellValue('H' . $row_next, 'A');
                    $active_sheet->setCellValue('J' . $row_next, 'отлично');
                    $active_sheet->setCellValue('I' . $row_next, '4');
                } else {
                    if ($array_result6[$i]->mark >= '95' && $array_result6[$i]->mark < '100') {
                        $active_sheet->setCellValue('H' . $row_next, 'A');
                        $active_sheet->setCellValue('J' . $row_next, 'отлично');
                        $active_sheet->setCellValue('I' . $row_next, '4');
                    };
                    if ($array_result6[$i]->mark >= '90' && $array_result6[$i]->mark < '95') {
                        $active_sheet->setCellValue('H' . $row_next, 'A-');
                        $active_sheet->setCellValue('J' . $row_next, 'отлично');
                        $active_sheet->setCellValue('I' . $row_next, '3,67');
                    };
                    if ($array_result6[$i]->mark >= '85' && $array_result6[$i]->mark < '90') {
                        $active_sheet->setCellValue('H' . $row_next, 'B+');
                        $active_sheet->setCellValue('J' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('I' . $row_next, '3,33');
                    };
                    if ($array_result6[$i]->mark >= '80' && $array_result6[$i]->mark < '85') {
                        $active_sheet->setCellValue('H' . $row_next, 'B');
                        $active_sheet->setCellValue('J' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('I' . $row_next, '3');
                    };
                    if ($array_result6[$i]->mark >= '75' && $array_result6[$i]->mark < '80') {
                        $active_sheet->setCellValue('H' . $row_next, 'B-');
                        $active_sheet->setCellValue('J' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('I' . $row_next, '2,67');
                    };
                    if ($array_result6[$i]->mark >= '70' && $array_result6[$i]->mark < '75') {
                        $active_sheet->setCellValue('H' . $row_next, 'C+');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '2,33');
                    };
                    if ($array_result6[$i]->mark >= '65' && $array_result6[$i]->mark < '70') {
                        $active_sheet->setCellValue('H' . $row_next, 'C');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '2');
                    };
                    if ($array_result6[$i]->mark >= '60' && $array_result6[$i]->mark < '65') {
                        $active_sheet->setCellValue('H' . $row_next, 'C-');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '1,67');
                    };
                    if ($array_result6[$i]->mark >= '55' && $array_result6[$i]->mark < '60') {
                        $active_sheet->setCellValue('H' . $row_next, 'D+');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '1,33');
                    };
                    if ($array_result6[$i]->mark >= '50' && $array_result6[$i]->mark < '55') {
                        $active_sheet->setCellValue('H' . $row_next, 'D');
                        $active_sheet->setCellValue('J' . $row_next, 'удовл.');
                        $active_sheet->setCellValue('I' . $row_next, '1');
                    };
                    if ($array_result6[$i]->mark >= '0' && $array_result6[$i]->mark < '50') {
                        $active_sheet->setCellValue('H' . $row_next, 'F');
                        $active_sheet->setCellValue('J' . $row_next, 'плохо');
                        $active_sheet->setCellValue('I' . $row_next, '0');
                    };
                };

            };
//заносим дисциплины по практике циклом
            $row_start = 20;
            $row_next=$row_start;
            //$i = 0;
            for ($i = 0; $i < count($array_result2); $i++) {
                $objPHPExcel->setActiveSheetIndex(0);
                $active_sheet = $objPHPExcel->getActiveSheet(0);

                if ($array_result2[$i]->type>0){
                    $active_sheet->setCellValue('A' . $row_next, $array_result2[$i]->subjectnameRU);
                    $active_sheet->setCellValue('F' . $row_next, $array_result2[$i]->Credits);
                    //esli proizvodstvennaya praktika
                    if ($array_result2[$i]->type == 1) {
                        if ($array_result2[$i]->Credits == '2') {
                            $active_sheet->setCellValue('G' . $row_next, "5");
                        }
                        if ($array_result2[$i]->Credits == '4') {
                            $active_sheet->setCellValue('G' . $row_next, "12");
                        }
                    };
                    //esli u4ebnaya praktika
                    if ($array_result2[$i]->type == 2) {
                        if ($array_result2[$i]->Credits == '1' or '2') {
                            $active_sheet->setCellValue('G' . $row_next, "1");
                        }
                        if ($array_result2[$i]->Credits == '3' or '4') {
                            $active_sheet->setCellValue('G' . $row_next, "2");
                        }
                    };
                    //pedagogi4eskaya praktika
                    if ($array_result2[$i]->type == 3) {
                        if ($array_result2[$i]->Credits == '2') {
                            $active_sheet->setCellValue('G' . $row_next, "2");
                        }
                        if ($array_result2[$i]->Credits == '4') {
                            $active_sheet->setCellValue('G' . $row_next, "4");
                        }
                    };
                    $active_sheet->setCellValue('H' . $row_next, $array_result2[$i]->AlphaMark);
                    $active_sheet->setCellValue('I' . $row_next, $array_result2[$i]->NumeralMark);
                    //$active_sheet->setCellValue('I'.$row_next,$row2->TotalMark);
                    if ($array_result2[$i]->AlphaMark == 'A' or $array_result2[$i]->AlphaMark == 'A-' or $array_result2[$i]->AlphaMark == 'A+') {
                        $active_sheet->setCellValue('J' . $row_next, "отлично");
                    };
                    if ($array_result2[$i]->AlphaMark == 'B' or $array_result2[$i]->AlphaMark == 'B-' or $array_result2[$i]->AlphaMark == 'B+') {
                        $active_sheet->setCellValue('J' . $row_next, "хорошо");
                    };
                    if ($array_result2[$i]->AlphaMark == 'C' or $array_result2[$i]->AlphaMark == 'C-' or $array_result2[$i]->AlphaMark == 'C+') {
                        $active_sheet->setCellValue('J' . $row_next, "удовл.");
                    };
                    if ($array_result2[$i]->AlphaMark == 'D' or $array_result2[$i]->AlphaMark == 'D-' or $array_result2[$i]->AlphaMark == 'D+') {
                        $active_sheet->setCellValue('J' . $row_next, "удовл.");
                    };
                    ////////////заносим каз вариант
                    $objPHPExcel->setActiveSheetIndex(3);
                    $active_sheet = $objPHPExcel->getActiveSheet(3);
                    $active_sheet->setCellValue('A' . ($row_next+4), $array_result2[$i]->subjectnameKZ);
                    $active_sheet->setCellValue('F' . ($row_next+4), $array_result2[$i]->Credits);
                    //esli proizvodstvennaya praktika
                    if ($array_result2[$i]->type == '1') {
                        if ($array_result2[$i]->Credits == '2') {
                            $active_sheet->setCellValue('G' . ($row_next+4), "5");
                        }
                        if ($array_result2[$i]->Credits == '4') {
                            $active_sheet->setCellValue('G' . ($row_next+4), "12");
                        }
                    };
                    //esli u4ebnaya praktika
                    if ($array_result2[$i]->type == '2') {
                        if ($array_result2[$i]->Credits == '1' or '2') {
                            $active_sheet->setCellValue('G' . ($row_next+4), "1");
                        }
                        if ($array_result2[$i]->Credits == '3' or '4') {
                            $active_sheet->setCellValue('G' . ($row_next+4), "2");
                        }
                    };
                    //pedagogi4eskaya praktika
                    if ($array_result2[$i]->type == '3') {
                        if ($array_result2[$i]->Credits == '2') {
                            $active_sheet->setCellValue('G' . ($row_next+4), "2");
                        }
                        if ($array_result2[$i]->Credits == '3') {
                            $active_sheet->setCellValue('G' . ($row_next+4), "3");
                        }
                        if ($array_result2[$i]->Credits == '4') {
                            $active_sheet->setCellValue('G' . ($row_next+4), "4");
                        }

                    };
                    $active_sheet->setCellValue('H' . ($row_next+4), $array_result2[$i]->AlphaMark);
                    $active_sheet->setCellValue('I' . ($row_next+4), $array_result2[$i]->NumeralMark);
                    //$active_sheet->setCellValue('I'.$row_next,$row2->TotalMark);
                    if ($array_result2[$i]->AlphaMark == 'A' or $array_result2[$i]->AlphaMark == 'A-' or $array_result2[$i]->AlphaMark == 'A+') {
                        $active_sheet->setCellValue('J' . ($row_next+4), "өте жақсы");
                    };
                    if ($array_result2[$i]->AlphaMark == 'B' or $array_result2[$i]->AlphaMark == 'B-' or $array_result2[$i]->AlphaMark == 'B+') {
                        $active_sheet->setCellValue('J' . ($row_next+4), "жақсы");
                    };
                    if ($array_result2[$i]->AlphaMark == 'C' or $array_result2[$i]->AlphaMark == 'C-' or $array_result2[$i]->AlphaMark == 'C+') {
                        $active_sheet->setCellValue('J' . ($row_next+4), "қанағат.");
                    };
                    if ($array_result2[$i]->AlphaMark == 'D' or $array_result2[$i]->AlphaMark == 'D-' or $array_result2[$i]->AlphaMark == 'D+') {
                        $active_sheet->setCellValue('J' . ($row_next+4), "қанағат.");
                    };
                    ////////////заносим англ вариант
                    $objPHPExcel->setActiveSheetIndex(5);
                    $active_sheet = $objPHPExcel->getActiveSheet(5);
                    $active_sheet->setCellValue('A' . $row_next, $array_result2[$i]->subjectnameEN);
                    $active_sheet->setCellValue('D' . $row_next, $array_result2[$i]->Credits);
                    //esli proizvodstvennaya praktika
                    if ($array_result2[$i]->type == '1') {
                        if ($array_result2[$i]->Credits == '2') {
                            $active_sheet->setCellValue('F' . $row_next, "5");
                        }
                        if ($array_result2[$i]->Credits == '4') {
                            $active_sheet->setCellValue('F' . $row_next, "12");
                        }
                    };
                    //esli u4ebnaya praktika
                    if ($array_result2[$i]->type == '2') {
                        if ($array_result2[$i]->Credits == '1' or '2') {
                            $active_sheet->setCellValue('F' . $row_next, "1");
                        }
                        if ($array_result2[$i]->Credits == '3' or '4') {
                            $active_sheet->setCellValue('F' . $row_next, "2");
                        }
                    };
                    //pedagogi4eskaya praktika
                    if ($array_result2[$i]->type == '3') {
                        if ($array_result2[$i]->Credits == '2') {
                            $active_sheet->setCellValue('F' . $row_next, "2");
                        }
                        if ($array_result2[$i]->Credits == '4') {
                            $active_sheet->setCellValue('F' . $row_next, "4");
                        }
                    };
                    $active_sheet->setCellValue('G' . $row_next, $array_result2[$i]->AlphaMark);
                    $active_sheet->setCellValue('H' . $row_next, $array_result2[$i]->NumeralMark);
                    //$active_sheet->setCellValue('I'.$row_next,$row2->TotalMark);
                    if ($array_result2[$i]->AlphaMark == 'A' or $array_result2[$i]->AlphaMark == 'A-' or $array_result2[$i]->AlphaMark == 'A+') {
                        $active_sheet->setCellValue('I' . $row_next, "excellent");
                    };
                    if ($array_result2[$i]->AlphaMark == 'B' or $array_result2[$i]->AlphaMark == 'B-' or $array_result2[$i]->AlphaMark == 'B+') {
                        $active_sheet->setCellValue('I' . $row_next, "good");
                    };
                    if ($array_result2[$i]->AlphaMark == 'C' or $array_result2[$i]->AlphaMark == 'C-' or $array_result2[$i]->AlphaMark == 'C+') {
                        $active_sheet->setCellValue('I' . $row_next, "sat.");
                    };
                    if ($array_result2[$i]->AlphaMark == 'D' or $array_result2[$i]->AlphaMark == 'D-' or $array_result2[$i]->AlphaMark == 'D+') {
                        $active_sheet->setCellValue('I' . $row_next, "sat.");
                    };
                    $row_next = $row_next + 1;}
            };

//ОТкрываем 2 лист EXcel

            //заносим ИО дисциплин циклом
            $c = 1;
            //zanosim disciplin na russkom
            for ($i = 0; $i < count($sortedru_array_result7); $i++) {
                if($i<=7) {
                    $objPHPExcel->setActiveSheetIndex(1);
                    $active_sheet = $objPHPExcel->getActiveSheet(1);
                    $row_start = 57;
                }else
                {
                    $objPHPExcel->setActiveSheetIndex(2);
                    $active_sheet = $objPHPExcel->getActiveSheet(2);
                    $row_start = -7;
                }
                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $c);
                $active_sheet->setCellValue('C' . $row_next, $sortedru_array_result7[$i]->subjectnameRU);
                $active_sheet->setCellValue('B' . $row_next, $sortedru_array_result7[$i]->code_ru);
                //$active_sheet->setCellValue('G' . $row_next, $array_result7[$i]->TotalMark);
                $active_sheet->setCellValue('D' . $row_next, $sortedru_array_result7[$i]->Credits);

                if ($sortedru_array_result7[$i]->Credits == 1) {
                    $active_sheet->setCellValue('E' . $row_next, '2');
                }
                if ($sortedru_array_result7[$i]->Credits == 2) {
                    $active_sheet->setCellValue('E' . $row_next, '4');
                }
                if ($sortedru_array_result7[$i]->Credits == 3) {
                    $active_sheet->setCellValue('E' . $row_next, '5');
                }
                if ($sortedru_array_result7[$i]->Credits == 4) {
                    $active_sheet->setCellValue('E' . $row_next, '6');
                }
                if ($sortedru_array_result7[$i]->Credits == 5) {
                    $active_sheet->setCellValue('E' . $row_next, '8');
                }
                if ($sortedru_array_result7[$i]->Credits == 6) {
                    $active_sheet->setCellValue('E' . $row_next, '9');
                }
                if ($sortedru_array_result7[$i]->TotalMark == '100') {
                    $active_sheet->setCellValue('F' . $row_next, 'A');
                    $active_sheet->setCellValue('H' . $row_next, 'отлично');
                    $active_sheet->setCellValue('G' . $row_next, '4');
                } else {
                    if ($sortedru_array_result7[$i]->TotalMark >= '95' && $sortedru_array_result7[$i]->TotalMark < '100') {
                        $active_sheet->setCellValue('F' . $row_next, 'A');
                        $active_sheet->setCellValue('H' . $row_next, 'отлично');
                        $active_sheet->setCellValue('G' . $row_next, '4');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '90' && $sortedru_array_result7[$i]->TotalMark < '95') {
                        $active_sheet->setCellValue('F' . $row_next, 'A-');
                        $active_sheet->setCellValue('H' . $row_next, 'отлично');
                        $active_sheet->setCellValue('G' . $row_next, '3,67');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '85' && $sortedru_array_result7[$i]->TotalMark < '90') {
                        $active_sheet->setCellValue('F' . $row_next, 'B+');
                        $active_sheet->setCellValue('H' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('G' . $row_next, '3,33');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '80' && $sortedru_array_result7[$i]->TotalMark < '85') {
                        $active_sheet->setCellValue('F' . $row_next, 'B');
                        $active_sheet->setCellValue('H' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('G' . $row_next, '3');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '75' && $sortedru_array_result7[$i]->TotalMark < '80') {
                        $active_sheet->setCellValue('F' . $row_next, 'B-');
                        $active_sheet->setCellValue('H' . $row_next, 'хорошо');
                        $active_sheet->setCellValue('G' . $row_next, '2,67');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '70' && $sortedru_array_result7[$i]->TotalMark < '75') {
                        $active_sheet->setCellValue('F' . $row_next, 'C+');
                        $active_sheet->setCellValue('H' . $row_next, 'плохо');
                        $active_sheet->setCellValue('G' . $row_next, '2,33');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '65' && $sortedru_array_result7[$i]->TotalMark < '70') {
                        $active_sheet->setCellValue('F' . $row_next, 'C');
                        $active_sheet->setCellValue('H' . $row_next, 'плохо');
                        $active_sheet->setCellValue('G' . $row_next, '2');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '60' && $sortedru_array_result7[$i]->TotalMark < '65') {
                        $active_sheet->setCellValue('F' . $row_next, 'C-');
                        $active_sheet->setCellValue('H' . $row_next, 'плохо');
                        $active_sheet->setCellValue('G' . $row_next, '1,67');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '55' && $sortedru_array_result7[$i]->TotalMark < '60') {
                        $active_sheet->setCellValue('F' . $row_next, 'D+');
                        $active_sheet->setCellValue('H' . $row_next, 'плохо');
                        $active_sheet->setCellValue('G' . $row_next, '1,33');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '50' && $sortedru_array_result7[$i]->TotalMark < '55') {
                        $active_sheet->setCellValue('F' . $row_next, 'D');
                        $active_sheet->setCellValue('H' . $row_next, 'плохо');
                        $active_sheet->setCellValue('G' . $row_next, '1');
                    };
                    if ($sortedru_array_result7[$i]->TotalMark >= '0' && $sortedru_array_result7[$i]->TotalMark < '50') {
                        $active_sheet->setCellValue('F' . $row_next, 'F');
                        $active_sheet->setCellValue('H' . $row_next, 'провалено');
                        $active_sheet->setCellValue('G' . $row_next, '0');
                    };
                };
                $c++;
            }
            //zanosim disciplin na kaz
            $c = 1;
            for ($i = 0; $i < count($sortedkz_array_result7); $i++) {
                $objPHPExcel->setActiveSheetIndex(4);
                $active_sheet = $objPHPExcel->getActiveSheet(4);

                $row_start=1;

                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $c);
                $active_sheet->setCellValue('B' . $row_next, $sortedkz_array_result7[$i]->subjectcode);
                $active_sheet->setCellValue('C' . $row_next, $sortedkz_array_result7[$i]->subjectnameKZ);
                //$active_sheet->setCellValue('G' . $row_next, $sortedkz_array_result7[$i]->TotalMark);
                $active_sheet->setCellValue('D' . $row_next, $sortedkz_array_result7[$i]->Credits);

                if ($sortedkz_array_result7[$i]->Credits == 1) {
                    $active_sheet->setCellValue('E' . $row_next, '2');
                }
                if ($sortedkz_array_result7[$i]->Credits == 2) {
                    $active_sheet->setCellValue('E' . $row_next, '4');
                }
                if ($sortedkz_array_result7[$i]->Credits == 3) {
                    $active_sheet->setCellValue('E' . $row_next, '5');
                }
                if ($sortedkz_array_result7[$i]->Credits == 4) {
                    $active_sheet->setCellValue('E' . $row_next, '6');
                }
                if ($sortedkz_array_result7[$i]->Credits == 5) {
                    $active_sheet->setCellValue('E' . $row_next, '8');
                }
                if ($sortedkz_array_result7[$i]->Credits == 6) {
                    $active_sheet->setCellValue('E' . $row_next, '9');
                }

                if ($sortedkz_array_result7[$i]->TotalMark == '100') {
                    $active_sheet->setCellValue('F' . $row_next, 'A');
                    $active_sheet->setCellValue('H' . $row_next, 'өте жақсы');
                    $active_sheet->setCellValue('G' . $row_next, '4');
                } else {
                    if ($sortedkz_array_result7[$i]->TotalMark >= '95' && $sortedkz_array_result7[$i]->TotalMark < '100') {
                        $active_sheet->setCellValue('F' . $row_next, 'A');
                        $active_sheet->setCellValue('H' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('G' . $row_next, '4');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '90' && $sortedkz_array_result7[$i]->TotalMark < '95') {
                        $active_sheet->setCellValue('F' . $row_next, 'A-');
                        $active_sheet->setCellValue('H' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('G' . $row_next, '3,67');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '85' && $sortedkz_array_result7[$i]->TotalMark < '90') {
                        $active_sheet->setCellValue('F' . $row_next, 'B+');
                        $active_sheet->setCellValue('H' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('G' . $row_next, '3,33');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '80' && $sortedkz_array_result7[$i]->TotalMark < '85') {
                        $active_sheet->setCellValue('F' . $row_next, 'B');
                        $active_sheet->setCellValue('H' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('G' . $row_next, '3');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '75' && $sortedkz_array_result7[$i]->TotalMark < '80') {
                        $active_sheet->setCellValue('F' . $row_next, 'B-');
                        $active_sheet->setCellValue('H' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('G' . $row_next, '2,67');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '70' && $sortedkz_array_result7[$i]->TotalMark < '75') {
                        $active_sheet->setCellValue('F' . $row_next, 'C+');
                        $active_sheet->setCellValue('H' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('G' . $row_next, '2,33');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '65' && $sortedkz_array_result7[$i]->TotalMark < '70') {
                        $active_sheet->setCellValue('F' . $row_next, 'C');
                        $active_sheet->setCellValue('H' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('G' . $row_next, '2');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '60' && $sortedkz_array_result7[$i]->TotalMark < '65') {
                        $active_sheet->setCellValue('F' . $row_next, 'C-');
                        $active_sheet->setCellValue('H' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('G' . $row_next, '1,67');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '55' && $sortedkz_array_result7[$i]->TotalMark < '60') {
                        $active_sheet->setCellValue('F' . $row_next, 'D+');
                        $active_sheet->setCellValue('H' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('G' . $row_next, '1,33');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '50' && $sortedkz_array_result7[$i]->TotalMark < '55') {
                        $active_sheet->setCellValue('F' . $row_next, 'D');
                        $active_sheet->setCellValue('H' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('G' . $row_next, '1');
                    };
                    if ($sortedkz_array_result7[$i]->TotalMark >= '0' && $sortedkz_array_result7[$i]->TotalMark < '50') {
                        $active_sheet->setCellValue('F' . $row_next, 'F');
                        $active_sheet->setCellValue('H' . $row_next, 'жаман');
                        $active_sheet->setCellValue('G' . $row_next, '0');
                    };
                };
                $c++;
            }
//zanosim disciplin na eng
            $c = 1;
            for ($i = 0; $i < count($sorteden_array_result7); $i++) {
                if($i<=7) {
                    $objPHPExcel->setActiveSheetIndex(6);
                    $active_sheet = $objPHPExcel->getActiveSheet(6);
                    $row_start = 57;
                }else
                {
                    $objPHPExcel->setActiveSheetIndex(7);
                    $active_sheet = $objPHPExcel->getActiveSheet(7);
                    $row_start = -7;
                }
                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $c);
                $active_sheet->setCellValue('C' . $row_next, $sorteden_array_result7[$i]->subjectnameEN);
                $active_sheet->setCellValue('B' . $row_next, $sorteden_array_result7[$i]->code_en);
                // $active_sheet->setCellValue('G' . $row_next, $sorteden_array_result7[$i]->TotalMark);
                $active_sheet->setCellValue('D' . $row_next, $sorteden_array_result7[$i]->Credits);

                if ($sorteden_array_result7[$i]->Credits == 1) {
                    $active_sheet->setCellValue('E' . $row_next, '2');
                }
                if ($sorteden_array_result7[$i]->Credits == 2) {
                    $active_sheet->setCellValue('E' . $row_next, '4');
                }
                if ($sorteden_array_result7[$i]->Credits == 3) {
                    $active_sheet->setCellValue('E' . $row_next, '5');
                }
                if ($sorteden_array_result7[$i]->Credits == 4) {
                    $active_sheet->setCellValue('E' . $row_next, '6');
                }
                if ($sorteden_array_result7[$i]->Credits == 5) {
                    $active_sheet->setCellValue('E' . $row_next, '8');
                }
                if ($sorteden_array_result7[$i]->Credits == 6) {
                    $active_sheet->setCellValue('E' . $row_next, '9');
                }

                if ($sorteden_array_result7[$i]->TotalMark == '100') {
                    $active_sheet->setCellValue('F' . $row_next, 'A');
                    $active_sheet->setCellValue('H' . $row_next, 'excellent');
                    $active_sheet->setCellValue('G' . $row_next, '4');
                } else {
                    if ($sorteden_array_result7[$i]->TotalMark >= '95' && $sorteden_array_result7[$i]->TotalMark < '100') {
                        $active_sheet->setCellValue('F' . $row_next, 'A');
                        $active_sheet->setCellValue('H' . $row_next, 'excellent');
                        $active_sheet->setCellValue('G' . $row_next, '4');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '90' && $sorteden_array_result7[$i]->TotalMark < '95') {
                        $active_sheet->setCellValue('F' . $row_next, 'A-');
                        $active_sheet->setCellValue('H' . $row_next, 'excellent');
                        $active_sheet->setCellValue('G' . $row_next, '3,67');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '85' && $sorteden_array_result7[$i]->TotalMark < '90') {
                        $active_sheet->setCellValue('F' . $row_next, 'B+');
                        $active_sheet->setCellValue('H' . $row_next, 'good');
                        $active_sheet->setCellValue('G' . $row_next, '3,33');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '80' && $sorteden_array_result7[$i]->TotalMark < '85') {
                        $active_sheet->setCellValue('F' . $row_next, 'B');
                        $active_sheet->setCellValue('H' . $row_next, 'good');
                        $active_sheet->setCellValue('G' . $row_next, '3');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '75' && $sorteden_array_result7[$i]->TotalMark < '80') {
                        $active_sheet->setCellValue('F' . $row_next, 'B-');
                        $active_sheet->setCellValue('H' . $row_next, 'good');
                        $active_sheet->setCellValue('G' . $row_next, '2,67');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '70' && $sorteden_array_result7[$i]->TotalMark < '75') {
                        $active_sheet->setCellValue('F' . $row_next, 'C+');
                        $active_sheet->setCellValue('H' . $row_next, 'sat.');
                        $active_sheet->setCellValue('G' . $row_next, '2,33');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '65' && $sorteden_array_result7[$i]->TotalMark < '70') {
                        $active_sheet->setCellValue('F' . $row_next, 'C');
                        $active_sheet->setCellValue('H' . $row_next, 'sat.');
                        $active_sheet->setCellValue('G' . $row_next, '2');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '60' && $sorteden_array_result7[$i]->TotalMark < '65') {
                        $active_sheet->setCellValue('F' . $row_next, 'C-');
                        $active_sheet->setCellValue('H' . $row_next, 'sat.');
                        $active_sheet->setCellValue('G' . $row_next, '1,67');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '55' && $sorteden_array_result7[$i]->TotalMark < '60') {
                        $active_sheet->setCellValue('F' . $row_next, 'D+');
                        $active_sheet->setCellValue('H' . $row_next, 'sat.');
                        $active_sheet->setCellValue('G' . $row_next, '1,33');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '50' && $sorteden_array_result7[$i]->TotalMark < '55') {
                        $active_sheet->setCellValue('F' . $row_next, 'D');
                        $active_sheet->setCellValue('H' . $row_next, 'sat.');
                        $active_sheet->setCellValue('G' . $row_next, '1');
                    };
                    if ($sorteden_array_result7[$i]->TotalMark >= '0' && $sorteden_array_result7[$i]->TotalMark < '50') {
                        $active_sheet->setCellValue('F' . $row_next, 'F');
                        $active_sheet->setCellValue('H' . $row_next, 'fail');
                        $active_sheet->setCellValue('G' . $row_next, '0');
                    };
                };
                $c++;
            }



            //////////////////////////////////////////////////////KZ Variant/////////////////////////////////////////////////////////////////////////////////
//
//делаем активным 4 лист
            $objPHPExcel->setActiveSheetIndex(3);
            $active_sheet = $objPHPExcel->getActiveSheet(3);
            $active_sheet->setCellValue('F2', $array_result[0]->lastname);
            $active_sheet->setCellValue('F3', $array_result[0]->firstname . " " . $array_result[0]->patronymic);
            $active_sheet->setCellValue('D4', $array_result[0]->BirthDate);
            //заносим вступительные испытания
            $active_sheet->setCellValue('F9', $array_result[0]->enterexamskz);
            // $active_sheet->setCellValue('F10', "Хаттама № " . $array_result9[0]->protocolNumber . "  " . $array_result9[0]->protocolDate . " ж.");
            //заносим поступил\окончил
            $active_sheet->setCellValue('A11', "Абай атындағы Қазақ Ұлттық Педагогикалық университеті, " . $array_result[0]->StartDate . " ж.");
            $active_sheet->setCellValue('A12', "Абай атындағы Қазақ Ұлттық Педагогикалық университеті, " . $array_result[0]->FinishDate . " ж.");
            //zanosim predyduwii dokument ob obu4enii
            if ($array_result[0]->end_school == '1') {
                //  $active_sheet->setCellValue('G5',$array_result[0]->pasteduinfokz);

                $active_sheet->setCellValue('G5', "аттестат");
                $active_sheet->setCellValue('A7', $array_result[0]->seriyaAttestata . '№ ' . $array_result[0]->nomerAttestata);
                //$active_sheet->setCellValue('B6','№ '.$row->nomerAttestata);
                $active_sheet->setCellValue('A8', $array_result[0]->dataVydachiAttestata . " жылы");

            };
            if ($array_result[0]->end_college == '1' or $array_result[0]->end_high_school == '1' or $array_result[0]->end_other == '1') {
                $active_sheet->setCellValue('G5', "диплом");
//                $active_sheet->setCellValue('G6', 'Хаттама № ' . $array_result[0]->seriyaDiploma);
//                $active_sheet->setCellValue('G7', $array_result[0]->dataVydachiDiploma . " жылы");
                $active_sheet->setCellValue('A6', 'Хаттама № ' . $array_result9[0]->finishDiplomSeries .'  '. $array_result9[0]->finishDocDate);
            };

            /*//zanosim dannye o diplomke
            $active_sheet->setCellValue('L25',$row->seriyaDiploma);
            $active_sheet->setCellValue('L26',$row->dataVydachiDiploma);*/

            $objPHPExcel->setActiveSheetIndex(4);
            $active_sheet = $objPHPExcel->getActiveSheet(4);

            //zanosim akadem. stepen'
            if ($array_result[0]->degreeID == '1') {
                $active_sheet->setCellValue('C59', "бакалавр академиялық дәрежесі");
            };
            if ($array_result[0]->degreeID == '2') {
                $active_sheet->setCellValue('C59', "магистрант академиялық дәрежесі");
            };
            if ($array_result[0]->degreeID == '6') {
                $active_sheet->setCellValue('C59', "доктор академиялық дәрежесі");
            };

            //zanosim special'nost'
            $active_sheet->setCellValue('C58', $array_result[0]->professionkz . " - " . $array_result[0]->forma_obuchkz);
            //zanosim specializaciu'
            $active_sheet->setCellValue('C61', $array_result[0]->specializatiyakz);
            $objPHPExcel->setActiveSheetIndex(3);
            $active_sheet = $objPHPExcel->getActiveSheet(3);
            //zanosim diplom
            $row_start = 45;
            for ($i = 0; $i < count($array_result3); $i++) {
                $row_next = $row_start + $i;

                $active_sheet->setCellValue('A' . $row_next, $array_result3[$i]->diplom_namekz);
                $active_sheet->setCellValue('F' . $row_next, $array_result3[$i]->credits);
                // $active_sheet->setCellValue('I38',$row3->mark);
                if ($array_result3[$i]->credits == '2') {
                    $active_sheet->setCellValue('G' . $row_next, '8');
                };

                //$active_sheet->setCellValue('I37',$row3->mark);
                if ($array_result3[$i]->mark == '100') {
                    $active_sheet->setCellValue('H' . $row_next, 'A');
                    $active_sheet->setCellValue('J' . $row_next, 'өте жақсы');
                    $active_sheet->setCellValue('I' . $row_next, '4');
                } else {
                    if ($array_result3[$i]->mark >= '95' && $array_result3[$i]->mark < '100') {
                        $active_sheet->setCellValue('H' . $row_next, 'A');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '4');
                    };
                    if ($array_result3[$i]->mark >= '90' && $array_result3[$i]->mark < '95') {
                        $active_sheet->setCellValue('H' . $row_next, 'A-');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '3,67');
                    };
                    if ($array_result3[$i]->mark >= '85' && $array_result3[$i]->mark < '90') {
                        $active_sheet->setCellValue('H' . $row_next, 'B+');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '3,33');
                    };
                    if ($array_result3[$i]->mark >= '80' && $array_result3[$i]->mark < '85') {
                        $active_sheet->setCellValue('H' . $row_next, 'B');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '3');
                    };
                    if ($array_result3[$i]->mark >= '75' && $array_result3[$i]->mark < '80') {
                        $active_sheet->setCellValue('H' . $row_next, 'B-');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '2,67');
                    };
                    if ($array_result3[$i]->mark >= '70' && $array_result3[$i]->mark < '75') {
                        $active_sheet->setCellValue('H' . $row_next, 'C+');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '2,33');
                    };
                    if ($array_result3[$i]->mark >= '65' && $array_result3[$i]->mark < '70') {
                        $active_sheet->setCellValue('H' . $row_next, 'C');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '2');
                    };
                    if ($array_result3[$i]->mark >= '60' && $array_result3[$i]->mark < '65') {
                        $active_sheet->setCellValue('H' . $row_next, 'C-');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '1,67');
                    };
                    if ($array_result3[$i]->mark >= '55' && $array_result3[$i]->mark < '60') {
                        $active_sheet->setCellValue('H' . $row_next, 'D+');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '1,33');
                    };
                    if ($array_result3[$i]->mark >= '50' && $array_result3[$i]->mark < '55') {
                        $active_sheet->setCellValue('H' . $row_next, 'D');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '1');
                    };
                    if ($array_result3[$i]->mark >= '0' && $array_result3[$i]->mark < '50') {
                        $active_sheet->setCellValue('H' . $row_next, 'F');
                        $active_sheet->setCellValue('J' . $row_next, 'жаман');
                        $active_sheet->setCellValue('I' . $row_next, '0');
                    };
                };
            }
            //заносим оценки за Гос.экзамен
            $row_start = 34;
            for ($i = 0; $i < count($array_result6); $i++) {
                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $array_result6[$i]->SubjectKZ);
                $active_sheet->setCellValue('F' . $row_next, $array_result6[$i]->credits);
                if ($array_result6[$i]->credits == '1') {
                    $active_sheet->setCellValue('G' . $row_next, '4');
                };

                $active_sheet->setCellValue('I' . $row_next, $array_result6[$i]->mark);
                if ($array_result6[$i]->mark == '100') {
                    $active_sheet->setCellValue('H' . $row_next, 'A');
                    $active_sheet->setCellValue('J' . $row_next, 'өте жақсы');
                    $active_sheet->setCellValue('I' . $row_next, '4');
                } else {
                    if ($array_result6[$i]->mark >= '95' && $array_result6[$i]->mark < '100') {
                        $active_sheet->setCellValue('H' . $row_next, 'A');
                        $active_sheet->setCellValue('J' . $row_next, 'өте жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '4');
                    };
                    if ($array_result6[$i]->mark >= '90' && $array_result6[$i]->mark < '95') {
                        $active_sheet->setCellValue('H' . $row_next, 'A-');
                        $active_sheet->setCellValue('J' . $row_next, 'өте жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '3,67');
                    };
                    if ($array_result6[$i]->mark >= '85' && $array_result6[$i]->mark < '90') {
                        $active_sheet->setCellValue('H' . $row_next, 'B+');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '3,33');
                    };
                    if ($array_result6[$i]->mark >= '80' && $array_result6[$i]->mark < '85') {
                        $active_sheet->setCellValue('H' . $row_next, 'B');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '3');
                    };
                    if ($array_result6[$i]->mark >= '75' && $array_result6[$i]->mark < '80') {
                        $active_sheet->setCellValue('H' . $row_next, 'B-');
                        $active_sheet->setCellValue('J' . $row_next, 'жақсы');
                        $active_sheet->setCellValue('I' . $row_next, '2,67');
                    };
                    if ($array_result6[$i]->mark >= '70' && $array_result6[$i]->mark < '75') {
                        $active_sheet->setCellValue('H' . $row_next, 'C+');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '2,33');
                    };
                    if ($array_result6[$i]->mark >= '65' && $array_result6[$i]->mark < '70') {
                        $active_sheet->setCellValue('H' . $row_next, 'C');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '2');
                    };
                    if ($array_result6[$i]->mark >= '60' && $array_result6[$i]->mark < '65') {
                        $active_sheet->setCellValue('H' . $row_next, 'C-');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '1,67');
                    };
                    if ($array_result6[$i]->mark >= '55' && $array_result6[$i]->mark < '60') {
                        $active_sheet->setCellValue('H' . $row_next, 'D+');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '1,33');
                    };
                    if ($array_result6[$i]->mark >= '50' && $array_result6[$i]->mark < '55') {
                        $active_sheet->setCellValue('H' . $row_next, 'D');
                        $active_sheet->setCellValue('J' . $row_next, 'қанағат.');
                        $active_sheet->setCellValue('I' . $row_next, '1');
                    };
                    if ($array_result6[$i]->mark >= '0' && $array_result6[$i]->mark < '50') {
                        $active_sheet->setCellValue('H' . $row_next, 'F');
                        $active_sheet->setCellValue('J' . $row_next, 'жаман');
                        $active_sheet->setCellValue('I' . $row_next, '0');
                    };
                };
            };
///////////////////////////////////////////англ вариант////////////////////////////////////////////////////////////
            $objPHPExcel->setActiveSheetIndex(5);
            $active_sheet = $objPHPExcel->getActiveSheet(5);
            $active_sheet->setCellValue('F2', $array_result[0]->lastname_en);
            $active_sheet->setCellValue('F3', $array_result[0]->firstname_en . " " . $array_result[0]->patronymic_en);
            $active_sheet->setCellValue('D4', $array_result[0]->BirthDate);
            //заносим вступительные испытания
            $active_sheet->setCellValue('F9', $array_result[0]->enterexamsen);
            //$active_sheet->setCellValue('F10', "Protocol № " . $array_result9[0]->protocolNumber . " " . $array_result9[0]->protocolDate);
            //заносим поступил\окончил
            $active_sheet->setCellValue('A9', "The Kazakh National Pedagogical University named after Abai, " . $array_result[0]->StartDate . ' y.');
            $active_sheet->setCellValue('A10', "The Kazakh National Pedagogical University named after Abai, " . $array_result[0]->FinishDate . ' y.');
            //zanosim predyduwii dokument ob obu4enii
            if ($array_result[0]->end_school == '1') {
                // $active_sheet->setCellValue('G5', $array_result[0]->pasteduinfoen);
                $active_sheet->setCellValue('A5', "attestat");
                $active_sheet->setCellValue('A6', $array_result[0]->seriyaAttestata. '№ ' . $array_result[0]->nomerAttestata.' '.$array_result[0]->dataVydachiAttestata . ' y.');
                //$active_sheet->setCellValue('B6', '№ ' . $array_result[0]->nomerAttestata);
                //$active_sheet->setCellValue('A8', $array_result[0]->dataVydachiAttestata . ' y.');
            };
            if ($array_result[0]->end_college == '1' or $array_result[0]->end_high_school == '1' or $array_result[0]->end_other == '1') {
                // $active_sheet->setCellValue('G5', $array_result[0]->pasteduinfoen);
                //$active_sheet->setCellValue('A6', $array_result[0]->seriyaAttestata);
                $active_sheet->setCellValue('A5', "diplom");
                //$active_sheet->setCellValue('A6', 'Protocol № ' . $array_result[0]->seriyaDiploma.' '.$array_result[0]->dataVydachiDiploma . ' y.');
                //$active_sheet->setCellValue('A8', $array_result[0]->dataVydachiDiploma . ' y.');
                $active_sheet->setCellValue('A6', 'Protocol № ' . $array_result9[0]->finishDiplomSeries .' ' . $array_result9[0]->finishDocDate . ' y.');
            };
            //zanosim akadem. stepen'
            $objPHPExcel->setActiveSheetIndex(7);
            $active_sheet = $objPHPExcel->getActiveSheet(7);
            if ($array_result[0]->degreeID == '1') {
                $active_sheet->setCellValue('A60', "academic bachelor's degree");
            };
            if ($array_result[0]->degreeID == '2') {
                $active_sheet->setCellValue('A60', "the undergraduate academic degree");
            };
            if ($array_result[0]->degreeID == '6') {
                $active_sheet->setCellValue('A60', "Academic doctoral degree");
            };

            //zanosim special'nost'
            $active_sheet->setCellValue('C61', $array_result[0]->professionen . " - " . $array_result[0]->forma_obuchen);
            //zanosim specializaciu'
            $active_sheet->setCellValue('C63', $array_result[0]->specializatiyaen);

            $objPHPExcel->setActiveSheetIndex(5);
            $active_sheet = $objPHPExcel->getActiveSheet(5);
            //zanosim diplom
            $row_start=37;
            for ($i = 0; $i < count($array_result3); $i++) {
                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $array_result3[$i]->diplom_nameen);
                $active_sheet->setCellValue('D' . $row_next, $array_result3[$i]->credits);
                if ($array_result3[$i]->credits == '2') {
                    $active_sheet->setCellValue('F' . $row_next, '8');
                };

                //$active_sheet->setCellValue('I37',$row3->mark);
                if ($array_result3[$i]->mark == '100') {
                    $active_sheet->setCellValue('G' . $row_next, 'A');
                    $active_sheet->setCellValue('I' . $row_next, 'excellent');
                    $active_sheet->setCellValue('H' . $row_next, '4');
                } else {
                    if ($array_result3[$i]->mark >= '95' && $array_result3[$i]->mark < '100') {
                        $active_sheet->setCellValue('G' . $row_next, 'A');
                        $active_sheet->setCellValue('I' . $row_next, 'excellent');
                        $active_sheet->setCellValue('H' . $row_next, '4');
                    };
                    if ($array_result3[$i]->mark >= '90' && $array_result3[$i]->mark < '95') {
                        $active_sheet->setCellValue('G' . $row_next, 'A-');
                        $active_sheet->setCellValue('I' . $row_next, 'excellent');
                        $active_sheet->setCellValue('H' . $row_next, '3,67');
                    };
                    if ($array_result3[$i]->mark >= '85' && $array_result3[$i]->mark < '90') {
                        $active_sheet->setCellValue('G' . $row_next, 'B+');
                        $active_sheet->setCellValue('I' . $row_next, 'good');
                        $active_sheet->setCellValue('H' . $row_next, '3,33');
                    };
                    if ($array_result3[$i]->mark >= '80' && $array_result3[$i]->mark < '85') {
                        $active_sheet->setCellValue('G' . $row_next, 'B');
                        $active_sheet->setCellValue('I' . $row_next, 'good');
                        $active_sheet->setCellValue('H' . $row_next, '3');
                    };
                    if ($array_result3[$i]->mark >= '75' && $array_result3[$i]->mark < '80') {
                        $active_sheet->setCellValue('G' . $row_next, 'B-');
                        $active_sheet->setCellValue('I' . $row_next, 'good');
                        $active_sheet->setCellValue('H' . $row_next, '2,67');
                    };
                    if ($array_result3[$i]->mark >= '70' && $array_result3[$i]->mark < '75') {
                        $active_sheet->setCellValue('G' . $row_next, 'C+');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '2,33');
                    };
                    if ($array_result3[$i]->mark >= '65' && $array_result3[$i]->mark < '70') {
                        $active_sheet->setCellValue('G' . $row_next, 'C');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '2');
                    };
                    if ($array_result3[$i]->mark >= '60' && $array_result3[$i]->mark < '65') {
                        $active_sheet->setCellValue('G' . $row_next, 'C-');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '1,67');
                    };
                    if ($array_result3[$i]->mark >= '55' && $array_result3[$i]->mark < '60') {
                        $active_sheet->setCellValue('G' . $row_next, 'D+');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '1,33');
                    };
                    if ($array_result3[$i]->mark >= '50' && $array_result3[$i]->mark < '55') {
                        $active_sheet->setCellValue('G' . $row_next, 'D');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '1');
                    };
                    if ($array_result3[$i]->mark >= '0' && $array_result3[$i]->mark < '50') {
                        $active_sheet->setCellValue('G' . $row_next, 'F');
                        $active_sheet->setCellValue('I' . $row_next, 'fail');
                        $active_sheet->setCellValue('H' . $row_next, '0');
                    };
                };
            };

            //заносим оценки за Гос.экзамен
            $row_start = 28;
            for ($i = 0; $i < count($array_result6); $i++) {
                $row_next = $row_start + $i;
                $active_sheet->setCellValue('A' . $row_next, $array_result6[$i]->SubjectEN);
                $active_sheet->setCellValue('D' . $row_next, $array_result6[$i]->credits);
                if ($array_result6[$i]->credits == '1') {
                    $active_sheet->setCellValue('F' . $row_next, '4');
                };

                //$active_sheet->setCellValue('I' . $row_next, $array_result6[$i]->mark);
                if ($array_result6[$i]->mark == '100') {
                    $active_sheet->setCellValue('G' . $row_next, 'A');
                    $active_sheet->setCellValue('I' . $row_next, 'excellent');
                    $active_sheet->setCellValue('H' . $row_next, '4');
                } else {
                    if ($array_result6[$i]->mark >= '95' && $array_result6[$i]->mark < '100') {
                        $active_sheet->setCellValue('G' . $row_next, 'A');
                        $active_sheet->setCellValue('I' . $row_next, 'excellent');
                        $active_sheet->setCellValue('H' . $row_next, '4');
                    };
                    if ($array_result6[$i]->mark >= '90' && $array_result6[$i]->mark < '95') {
                        $active_sheet->setCellValue('G' . $row_next, 'A-');
                        $active_sheet->setCellValue('I' . $row_next, 'excellent');
                        $active_sheet->setCellValue('H' . $row_next, '3,67');
                    };
                    if ($array_result6[$i]->mark >= '85' && $array_result6[$i]->mark < '90') {
                        $active_sheet->setCellValue('G' . $row_next, 'B+');
                        $active_sheet->setCellValue('I' . $row_next, 'good');
                        $active_sheet->setCellValue('H' . $row_next, '3,33');
                    };
                    if ($array_result6[$i]->mark >= '80' && $array_result6[$i]->mark < '85') {
                        $active_sheet->setCellValue('G' . $row_next, 'B');
                        $active_sheet->setCellValue('I' . $row_next, 'good');
                        $active_sheet->setCellValue('H' . $row_next, '3');
                    };
                    if ($array_result6[$i]->mark >= '75' && $array_result6[$i]->mark < '80') {
                        $active_sheet->setCellValue('H' . $row_next, 'B-');
                        $active_sheet->setCellValue('I' . $row_next, 'good');
                        $active_sheet->setCellValue('H' . $row_next, '2,67');
                    };
                    if ($array_result6[$i]->mark >= '70' && $array_result6[$i]->mark < '75') {
                        $active_sheet->setCellValue('G' . $row_next, 'C+');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '2,33');
                    };
                    if ($array_result6[$i]->mark >= '65' && $array_result6[$i]->mark < '70') {
                        $active_sheet->setCellValue('G' . $row_next, 'C');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '2');
                    };
                    if ($array_result6[$i]->mark >= '60' && $array_result6[$i]->mark < '65') {
                        $active_sheet->setCellValue('H' . $row_next, 'C-');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '1,67');
                    };
                    if ($array_result6[$i]->mark >= '55' && $array_result6[$i]->mark < '60') {
                        $active_sheet->setCellValue('G' . $row_next, 'D+');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '1,33');
                    };
                    if ($array_result6[$i]->mark >= '50' && $array_result6[$i]->mark < '55') {
                        $active_sheet->setCellValue('G' . $row_next, 'D');
                        $active_sheet->setCellValue('I' . $row_next, 'sat.');
                        $active_sheet->setCellValue('H' . $row_next, '1');
                    };
                    if ($array_result6[$i]->mark >= '0' && $array_result6[$i]->mark < '50') {
                        $active_sheet->setCellValue('G' . $row_next, 'F');
                        $active_sheet->setCellValue('I' . $row_next, 'fail');
                        $active_sheet->setCellValue('H' . $row_next, '0');
                    };
                };
            };

//задаем тип и название исходного файла
//            header("Content-Type:application/vnd.ms-excel");
//            header("Content-Disposition:attachment;filename='simple.xls'");
//            //отправляем в браузер
//            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//            $objWriter->save('php://output');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save($file_name.'/public/prilojenie/'.$studentid.'.xls');
            $message=array('message'=>'uspewno','file_name'=>'/prilojenie/'.$studentid.'.xls');
            return json_encode($message);
        }else{
            $message=array('message'=>'');
            if(empty($array_result)){
                $message=array('message'=>'Отсутствуют данные по студенту SQL-1');
            }
            if(empty($array_result2)){
                $message=array('message'=>'Отсутствуют оценки за практику SQL-2');
            }
            if(empty($array_result3)){
                $message=array('message'=>'Отсутствуют оценка за диплом SQL-3');
            }
            if(empty($array_result4)){
                $message=array('message'=>'Отсутствуют количество кредитов по дисциплинам и сред.оценка gpa SQL-4');
            }
            if(empty($array_result6)){
                $message=array('message'=>'Отсутствуют оценки за Гос.экзамены SQL-6');
            }
            if(empty($sortedru_array_result7)){
                $message=array('message'=>'Отсутствуют итоговые оценки по дисциплинам SQL-7');
            }
            if(empty($array_result9)){
                $message=array('message'=>'Отсутствуют серии и номера диплома и т.д. SQL-9');
            }
            return json_encode($message);
        };
    }

    public function student_transcript($studentid,$isstudent){

//        $table_transcript="";
        if (intval($isstudent)==1){
            $table_transcript=' nitro.transcript ';
        };
        if (intval($isstudent)==3){
            $table_transcript=' nitro.deletedtranscript ';
        };
        if (empty($table_transcript)==false){
            $transcript=DB::select("SELECT
                                  transcript.id,
                                  transcript.StudentID,
                                  transcript.subjectcode,
                                  transcript.Credits,
                                  transcript.AlphaMark,
                                  transcript.NumeralMark,
                                  transcript.TotalMark,
                                  transcript.subjectnameRU,
                                  transcript.subjectnameKZ,
                                  transcript.subjectnameEN,
                                  transcript.type
                                FROM
                                  $table_transcript as transcript
                                WHERE                                    
                                  transcript.StudentID = $studentid 
                                ORDER BY subjectnameRU,subjectnameKZ,subjectnameEN");
        };

        if(empty($transcript)==true){

            $transcript="Транскрипт не найден";
            return $transcript;
        }else{
            return json_encode($transcript);
        }

    }

    public function list_year($pid,$sid){
        $list_year=DB::select("SELECT 
                                DISTINCT (CASE  WHEN O.orderdate IS NULL THEN 'не указан год' ELSE CONVERT(DATE_FORMAT( O.orderdate, '%Y'),CHAR)
                                     END) AS FinishDate 
                                FROM 
                                nitro.students ST
                                LEFT JOIN nitro.orderstudentinfo OSI ON OSI.studentID=ST.StudentID AND OSI.course=0 AND OSI.professionid=0 
                                LEFT JOIN nitro.orders O ON O.orderID=OSI.orderID
                                WHERE ST.isStudent=3 AND
                                ST.ProfessionID=$pid AND
                                ST.CourseNumber=0 AND
                                ST.StudyFormID=$sid                              
                                ORDER BY FinishDate;");
        return json_encode($list_year);
    }

    public function students($pid,$sid,$pyear)
    {
        $student=DB::select("SELECT ST.StudentID as id,
                            CONCAT(CASE ST.lastname WHEN '' THEN '' ELSE ST.lastname END,
                            CASE ST.firstname WHEN '' THEN '' ELSE CONCAT(' ',ST.firstname) END,
                            CASE ST.patronymic WHEN '' THEN '' ELSE CONCAT(' ',ST.patronymic) END) AS fio,
                            ST.isStudent	
FROM 
nitro.students ST
LEFT JOIN nitro.orderstudentinfo OSI ON OSI.studentID=ST.StudentID AND OSI.course=0 AND OSI.professionid=0 
INNER JOIN nitro.orders O ON O.orderID=OSI.orderID AND DATE_FORMAT(O.orderdate,'%Y')=$pyear
WHERE ST.isStudent=3 AND
ST.ProfessionID=$pid AND
ST.CourseNumber=0 AND
ST.StudyFormID=$sid
ORDER BY fio;");
        return json_encode($student);
    }
    #### ДИПЛОМ ####

    public function list_students($pid,$sid,$course,$lang,$spec){
        $student = DB::select("SELECT NS.StudentID AS id,CONCAT(CASE NS.lastname WHEN '' THEN '' ELSE NS.lastname END,
                            CASE NS.firstname WHEN '' THEN '' ELSE CONCAT(' ',NS.firstname) END,
                            CASE NS.patronymic WHEN '' THEN '' ELSE CONCAT(' ',NS.patronymic) END) AS fio FROM nitro.students NS WHERE NS.ProfessionID='$pid' AND NS.StudyFormID='$sid' AND NS.CourseNumber='$course' AND NS.StudyLanguageID='$lang' AND NS.specializationID='$spec' AND NS.isStudent=1");
        return json_encode($student);
    }


    public function summary_statement(){
        $professions = Contingent::getContingentProfession($_SESSION['id_tutor']);
        $study_lang = Studylanguage::getStudylang();
        return view('registration/summary_statement', compact('professions', 'study_lang'));
    }

    public function sum_statement($pid, $sid, $ssi, $course, $term, $lang){
        $id = $_SESSION['id_tutor'];
        DB::select("DELETE FROM phelper.temp WHERE id='$id'");

        $c = 0;
        $t = 0;
        if($course == 2 AND $term == 1){
            $c = 1; $t = 1;
        }
        if($course == 2 AND $term == 2){
            $c = 1; $t = 2;
        }
        if($course == 2 AND $term == 3){
            $c = 2; $t = 1;
        }
        if($course == 2 AND $term == 4){
            $c = 2; $t = 2;
        }
        if($course == 3 AND $term == 1){
            $c = 1; $t = 1;
        }
        if($course == 3 AND $term == 2){
            $c = 1; $t = 2;
        }
        if($course == 3 AND $term == 3){
            $c = 2; $t = 1;
        }
        if($course == 3 AND $term == 4){
            $c = 2; $t = 2;
        }


        DB::select("INSERT INTO phelper.temp (id,queryid,studentid,year,term,tupsid,professionid,studyformid,studylangid,fio_student,id_group,id_calendar,group_title,id_spec,spec_name)
                        SELECT $id,IUP.QueryID, IUP.StudentID,IUP.`year`,IUP.term,IUP.tupsid,IUP.ProfessionID,IUP.StudyFormID,IUP.StudyLanguageID,
                                     CONCAT(CASE IUP.lastname WHEN '' THEN '' ELSE IUP.lastname END,
                                            CASE IUP.firstname WHEN '' THEN '' ELSE CONCAT(' ',IUP.firstname) END,
                                            CASE IUP.patronymic WHEN '' THEN '' ELSE CONCAT(' ',IUP.patronymic) END) AS fio_student,
                                             IUP.groupID,IUP.studyCalendarID,IUP.group_name,IUP.specializationID,IUP.spec_name
                        FROM
                        (SELECT q1.QueryID,q1.StudentID,q1.year,q1.term,q1.SubjectID AS tupsid,S.ProfessionID,S.StudyFormID,S.StudyLanguageID,S.lastname,S.firstname,S.patronymic,S.groupID,S.studyCalendarID,S.group_name,S.specializationID,S.spec_name
                          FROM nitro.queries q1,
                          (SELECT ST.StudentID,ST.CourseNumber,ST.ProfessionID,ST.StudyFormID,ST.StudyLanguageID,ST.lastname,ST.firstname,ST.patronymic,ST.groupID,ST.studyCalendarID,NG.name AS group_name,ST.specializationID,NS.nameru AS spec_name
                          FROM nitro.students ST
                          LEFT JOIN nitro.groups NG ON NG.groupID=ST.groupID
                          LEFT JOIN nitro.specializations NS ON NS.id=ST.specializationID
                          WHERE ST.ProfessionID='$pid' AND ST.StudyFormID='$sid' AND ST.StudyLanguageID='$lang' AND ST.CourseNumber='$course' AND ST.isStudent=1) S
                          WHERE q1.StudentID=S.StudentID AND q1.term='$t' AND q1.year='$c') IUP");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'L') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.id=$id");


        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PS') AS OOP
                        SET PT.studygroupid_p=OOP.studygroupid, PT.grouptypeid_p=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid ");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PS') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'PP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'Lab') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'SP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'WP') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS PT,(SELECT GRUPPA.*,ts.TutorSubjectID,ts.TutorID,ts.grouptypeid,ts.SubjectID  FROM
                            (SELECT ssg.StudyGroupID,ssg.StudentID,ssg.QueryID
                            FROM nitro.studentstudygroup ssg,
                                     (SELECT StudentID FROM nitro.students WHERE StudyFormID=$sid AND ProfessionID=$pid AND StudyLanguageID=$lang AND CourseNumber=$course AND isStudent=1) NN
                            WHERE ssg.StudentID=NN.StudentID) GRUPPA
                            LEFT JOIN nitro.studygroups sg ON sg.StudyGroupID=GRUPPA.studyGroupID
                            LEFT JOIN nitro.tutorsubject ts ON ts.TutorSubjectID=sg.tutorSubjectID
                        	LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=ts.grouptypeid
	                        WHERE NG.shortnameen = 'SRW') AS OOP
                        SET PT.studygroupid=OOP.studygroupid, PT.tutorid=OOP.tutorid, PT.subjectid=OOP.subjectid, PT.grouptypeid=OOP.grouptypeid
                        WHERE  PT.id=$id AND PT.queryid=OOP.queryid AND PT.studentid=OOP.studentid AND PT.studygroupid IS NULL");

        DB::select("UPDATE phelper.temp AS OOT,nitro.subjects NS
                        SET OOT.subjectnameru=NS.SubjectNameRU, OOT.subjectcode=NS.SubjectCodeRu
                        WHERE OOT.subjectid=NS.SubjectID  AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.tutors NT
                        SET OOT.fio_tutor=CONCAT(CASE NT.lastname WHEN '' THEN '' ELSE NT.lastname END,
                                            CASE NT.firstname WHEN '' THEN '' ELSE CONCAT(' ',NT.firstname) END,
                                            CASE NT.patronymic WHEN '' THEN '' ELSE CONCAT(' ',NT.patronymic) END),
                            OOT.tutor_inicial=CONCAT(CASE NT.lastname WHEN '' THEN '' ELSE NT.lastname END,
                                              CASE NT.firstname WHEN '' THEN '' ELSE CONCAT(' ',LEFT(NT.firstname,1)) END,
                                              CASE NT.patronymic WHEN '' THEN '' ELSE CONCAT('.',LEFT(NT.patronymic,1),'.') END)                
                        WHERE OOT.tutorid=NT.TutorID AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.tupsubjects NTUP
                        SET OOT.creditcount=NTUP.creditscount
                        WHERE OOT.tupsid=NTUP.tupSubjectID AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.professions NPRO
                        SET OOT.pname=CONCAT_WS('-',NPRO.professionCode,NPRO.professionNameRU)
                        WHERE NPRO.professionID=OOT.professionid AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.studyforms NSTU
                        SET OOT.form=NSTU.NameRu
                        WHERE NSTU.Id=OOT.studyformid AND OOT.id=$id");

        DB::select("UPDATE phelper.temp AS OOT,nitro.studylanguages NSL
                        SET OOT.lang=NSL.NameRu
                        WHERE NSL.Id=OOT.studylangid AND OOT.id=$id");

        $subject = DB::select("SELECT PT.subjectid AS id,PT.subjectnameru AS name,PT.subjectcode AS code,NG.nameru AS type FROM phelper.temp PT
                               LEFT JOIN nitro.grouptypes NG ON NG.grouptypeID=PT.grouptypeid
                               WHERE PT.id=$id GROUP BY PT.subjectid");

        return json_encode($subject);
    }
}

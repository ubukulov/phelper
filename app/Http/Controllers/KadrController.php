<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use PHPExcel;
use PHPExcel_Writer_Excel5;

class KadrController extends Controller
{
    public function index(){
        return view('kadr/index');
    }

    public function show($id){
        $id = (int) $id;
        $xls = new PHPExcel();
        // Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
        // Получаем активный лист
        $sheet = $xls->getActiveSheet();
        switch ($id){
            // Доктор наук
            case 1:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,C.cafedraNameRU,T.BirthDate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where T.deleted=0 AND T.Scientificdegreeid=3 AND TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'докторов наук по кафедрам КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
			    $sheet->setCellValue("K4",' Ставка');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet->setCellValue("K".$j, $result[$i]->rate);
                }
					
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;

            // доктор phd
            case 2:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                               TC.cafedraid,C.cafedraNameRU,T.BirthDate,TC.rate,T.job_title as positio_ru,
                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                        
                        FROM tutors T
                       LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
					   LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                       LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
					   LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                       LEFT JOIN tutor_positions TP ON TC.position=TP.id
					   LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
					   LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                        where T.deleted=0 AND T.Scientificdegreeid=5 AND TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'PhD докторов по кафедрам КазНПУ имени Абая на '.date("d.m.Y"));
               

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
                $sheet->setCellValue("K4",' Ставка');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                    $sheet->setCellValue("K".$j, $result[$i]->rate);
                }

                $j += 3;
                $sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_phd.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				 // штатных пенсионеров по кафедрам
				 case 3:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,T.SexID,TC.type,T.deleted,C.cafedraNameRU,T.BirthDate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where T.deleted=0 AND T.work_status=1 AND
										((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))AND TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'штатных пенсионеров по кафедрам КазНПУ имени Абая на '.date("d.m.Y"));
               

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения  ');
				$sheet->setCellValue("K4",' Пенсионер');
                $sheet->setCellValue("L4",' Ставка');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet->setCellValue("L".$j, $result[$i]->rate);
                }
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				 // Кандидатов наук
				 case 4:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,C.cafedraNameRU,T.BirthDate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where T.deleted=0 AND T.Scientificdegreeid=2 AND TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'кондидатов наук по кафедрам КазНПУ имени Абая на '.date("d.m.Y"));
                
				
                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
			    $sheet->setCellValue("K4",' Ставка');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet->setCellValue("K".$j, $result[$i]->rate);
                }
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				// магистров по кафедрам
				case 5:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,C.cafedraNameRU,T.BirthDate,T.rang,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where T.deleted=0 AND T.Scientificdegreeid=4 AND TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Магистрантов по кафедрам КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
			    $sheet->setCellValue("K4",' Ставка');
				$sheet->setCellValue("L4",' наградо (допольнителное информация)');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) { 
					$j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet->setCellValue("K".$j, $result[$i]->rate);
					$sheet->setCellValue("L".$j, $result[$i]->rang);
					}
                                   
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				// уволенных по университету
				case 6:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,T.SexID,TC.type,T.rang,T.deleted,T.work_status,C.cafedraNameRU,T.BirthDate,T.finishdate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where T.deleted=1 AND  TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Уволенных по университету КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
			    $sheet->setCellValue("K4",' Ставка');
				$sheet->setCellValue("L4",'Дата завершение работа');
				$sheet->setCellValue("M4",'Причина за уволенных');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet->setCellValue("K".$j, $result[$i]->rate);
					$sheet->setCellValue("L".$j, $result[$i]->finishdate);
					}
					
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 7:
                //совместител по университету
				$result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,T.SexID,TC.type,T.deleted,T.work_status,C.cafedraNameRU,T.BirthDate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
										LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where T.deleted=0 AND T.work_status=2 AND TC.type=2 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Совместителей по университету КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
			    $sheet->setCellValue("K4",' Ставка');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet->setCellValue("K".$j, $result[$i]->rate);
                }
					
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				// принятых по университету
				case 8:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,T.SexID,TC.type,T.deleted,T.work_status,C.cafedraNameRU,T.StartDate,T.birthdate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where  YEAR(T.StartDate)=2016 AND T.deleted=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'принятых по университету КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата начало работа ');
			    $sheet->setCellValue("K4",' Ставка');
				$sheet->setCellValue("L4",'Дата рождения');
                $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->StartDate);
                  	$sheet->setCellValue("K".$j, $result[$i]->rate);
					$sheet->setCellValue("L".$j, $result[$i]->birthdate);
                }
					
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );

                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				// юбиляров
				case 9:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,T.SexID,TC.type,T.deleted,T.work_status,T.rang as RA,C.cafedraNameRU,T.BirthDate,T.StartDate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
										LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where  T.deleted=0 AND (YEAR(T.BirthDate)=1962 OR YEAR(T.BirthDate)=1957 OR YEAR(T.BirthDate)=1952 
OR YEAR(T.BirthDate)=1947 OR YEAR(T.BirthDate)=1942 OR YEAR(T.BirthDate)=1937)ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'юбиляров 55,60,65,70,75,80 лет по университету КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
			    $sheet->setCellValue("K4",' ставка'); 
				$sheet->setCellValue("L4",' наградо (допольнителное информация)');
				
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet->setCellValue("K".$j, $result[$i]->rate); 
					$sheet->setCellValue("L".$j, $result[$i]->RA);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
	break;
	// ШТАТНЫХ ЖЕНЩИН 
	case 10:
                $result = DB::select("SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,T.SexID,TC.type,T.deleted,T.work_status,T.rang as RA,C.cafedraNameRU,T.BirthDate,T.StartDate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where  T.deleted=0 AND T.work_status=1 AND T.SexID=1 AND TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'штатных женщин по университету КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' Фамилия ');
                $sheet->setCellValue("B4"   , ' Имя ');
                $sheet->setCellValue("C4"    ,' Отчество ');
                $sheet->setCellValue("D4"   ,'Ученая степень');
                $sheet->setCellValue("E4"   ,'Институт ');
                $sheet->setCellValue("F4"  ,    ' Кафедра ');
                $sheet->setCellValue("G4"  , ' Должность  ');
                $sheet->setCellValue("H4"  , ' Академический статус  ');
                $sheet->setCellValue("I4",  ' Ученая степень по спец. ');
                $sheet->setCellValue("J4",' Дата рождения   ');
			    $sheet->setCellValue("K4",' ставка');
				$sheet->setCellValue("L4",' наградо (допольнителное информация)');
			 	
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->lastname);
                    $sheet->setCellValue("B".$j, $result[$i]->firstname);
                    $sheet->setCellValue("C".$j, $result[$i]->patronymic);
                    $sheet->setCellValue("D".$j, $result[$i]->Scientificdegree_ru);
                    $sheet->setCellValue("E".$j, $result[$i]->faculti_ru);
                    $sheet->setCellValue("F".$j, $result[$i]->cafedraNameRU);
                    $sheet->setCellValue("G".$j, $result[$i]->positio_ru);
                    $sheet->setCellValue("H".$j, $result[$i]->academicstatus_ru);
                    $sheet->setCellValue("I".$j, $result[$i]->sciencefields_ru);
                    $sheet->setCellValue("J".$j, $result[$i]->BirthDate);
                  	$sheet-> setCellValue("K".$j, $result[$i]->rate);
					$sheet->setCellValue("L".$j,  $result[$i]->RA);
					
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
					case 11:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=22");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 12:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru,US.nameru as Usi_Ru      
                                        
                                        FROM tutors T
                    LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
										LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND FC.FacultyID=7");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
				$sheet->setCellValue("N4",'Университет');
				
			 	
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j,  $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
					
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 13:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru , US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=50");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j,  $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 14:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru , US.nameru as Usi_Ru
                                        FROM tutors T
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON US.universitytypeid=UN.startdate
                                       
																				
where T.deleted=0 AND T.funiversityID=0");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j,  $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 15:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=56");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 16:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=55");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 17:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=33");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 18:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=37");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 19:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=53");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 20:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=62");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 21:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=38");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 22:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru ,US.nameru as Usi_Ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
                                        LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN academicstatus AC ON T.AcademicStatusID=AC.id
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND C.cafedraID=35");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
			 	$sheet->setCellValue("N4",'Университет');
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j, $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
				case 23:
                $result = DB::select("SELECT SUM(TC.rate)as Stedints ,SUM(T.ScientificDegreeID=3) as doktorN, SUM(T.ScientificDegreeID=5) as PhdN, SUM(T.ScientificDegreeID=4) as mag,
 SUM(T.ScientificDegreeID=2) as KN, COUNT(T.deleted) as ChPPs, SUM(T.work_status=1) as ShPPS, SUM(T.work_status=2 AND TC.type=2) as VSo, SUM(TC.type=0 and T.work_status=2) VNS,SUM(((YEAR(T.BirthDate)=1962 AND T.SexID=1 OR YEAR(T.BirthDate)<1962 AND T.SexID=1)
							OR (YEAR(T.BirthDate)=1954 AND T.SexID=2 OR YEAR(T.BirthDate)<1954 AND T.SexID=2))) as Pens, ROUND(SUM(T.ScientificDegreeID=3)+SUM(T.ScientificDegreeID=5)+SUM(T.ScientificDegreeID=2)*100/SUM(T.work_status=1),0) as OSt,
                             C.cafedraNameRU as cafedra_Ru,FC.facultyNameRU as institute_Ru,US.nameru as Usi_Ru      
                                        
                                        FROM tutors T
                    LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
										LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
										LEFT JOIN cafedras C ON TC.cafedraid= C.CafedraID
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN universities US ON US.id_university=T.Password
										LEFT JOIN university UN ON UN.startdate=US.universitytypeid
										where T.deleted=0 AND FC.FacultyID=13");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'Количественный и качественный состав ППС'.date("d.m.Y"));
                

                $sheet->setCellValue("A4", ' ШТАТНЫХ единицы');
                $sheet->setCellValue("B4"   , ' Числен.ППС всего');
                $sheet->setCellValue("C4"    ,' Штатн.ППС');
                $sheet->setCellValue("D4"   ,'д/н');
                $sheet->setCellValue("E4"   ,'к/н');
                $sheet->setCellValue("F4"  ,    ' PhD');
                $sheet->setCellValue("G4"  , ' ОСТ.');
                $sheet->setCellValue("H4"  , ' Маг.');
                $sheet->setCellValue("I4",  ' Пенс');
                $sheet->setCellValue("J4",' Внеш.совм');
			    $sheet->setCellValue("K4",' Внут.совм');
				$sheet->setCellValue("L4",'Кафедра');
				$sheet->setCellValue("M4",'Институт');
				$sheet->setCellValue("N4",'Университет');
				
			 	
				
                
				  $current_row = 5;
                for ($i = 0;$i < count($result); $i++) {
                    $j = $current_row+$i;
                    $sheet->setCellValue("A".$j, $result[$i]->Stedints);
                    $sheet->setCellValue("B".$j, $result[$i]->ChPPs);
                    $sheet->setCellValue("C".$j, $result[$i]->ShPPS);
                    $sheet->setCellValue("D".$j, $result[$i]->doktorN);
                    $sheet->setCellValue("E".$j, $result[$i]->KN);
                    $sheet->setCellValue("F".$j, $result[$i]->PhdN);
                    $sheet->setCellValue("G".$j, $result[$i]->OSt);
                    $sheet->setCellValue("H".$j, $result[$i]->mag);
                    $sheet->setCellValue("I".$j, $result[$i]->Pens);
                    $sheet->setCellValue("J".$j, $result[$i]->VSo);
                  	$sheet-> setCellValue("K".$j, $result[$i]->VNS);
					$sheet->setCellValue("L".$j,  $result[$i]->cafedra_Ru);
					$sheet->setCellValue("M".$j,  $result[$i]->institute_Ru);
					$sheet->setCellValue("N".$j, $result[$i]->Usi_Ru);
					
					
					
				}
				
				$j+=3;
				$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
                header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                header ( "Cache-Control: no-cache, must-revalidate" );
                header ( "Pragma: no-cache" );
                header ( "Content-type: application/vnd.ms-excel" );
                header ( "Content-Disposition: attachment; filename=доктор_наук.xls" );
				
                // Выводим содержимое файла
                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                return redirect()->back();
                break;
		}
	}
}

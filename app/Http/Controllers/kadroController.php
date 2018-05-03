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
                $result = DB::select("SELECT T.lastname,T.firstname,T.patronymic,
                                               SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               T.rang as RA,C.cafedraNameRU,T.BirthDate,T.StartDate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.Id
										LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN tutor_positions TP ON TC.position=TP.id
										LEFT JOIN faculties FC ON FC.FacultyID=C.FacultyID
										LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where  T.deleted=0 AND T.work_status=1 AND TC.type=0 ORDER BY lastname");
                // Подписываем лист
                $sheet->setCellValue("D1", 'СПИСОК ');
                $sheet->mergeCells('C2:I2');
                $sheet->setCellValue("C2", 'штатных сотрудник университету КазНПУ имени Абая на '.date("d.m.Y"));
                

                $sheet->SetCellValue("A4", ' Фамилия ');
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
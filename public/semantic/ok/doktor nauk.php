<?php
include 'config.php';
require_once("PHPExcel.php");
require_once('PHPExcel/Writer/Excel5.php');
$cn = mysql_connect(HOST,USER,PASS);
mysql_select_db(DB,$cn);
mysql_query("SET NAMES utf8");

$sql = "SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
                                               T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
                                               TC.cafedraid,C.cafedraNameRU,T.BirthDate,TC.rate,T.job_title as positio_ru,
                                               AC.nameru as academicstatus_ru,FC.facultyNameRU as faculti_ru
                                        
                                        FROM tutors T
                                        LEFT JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
                                        LEFT JOIN sciencefields SC ON T.ScientificFieldID=SC.id
                                        LEFT JOIN tutor_cafedra TC ON T.TutorID=TC.tutorID
                                        LEFT JOIN cafedras C ON TC.cafedraid=C.cafedraid
                                        LEFT JOIN faculties FC ON FC.facultyDean=T.TutorID
                                        LEFT JOIN tutorqual TQ ON FC.facultyDean=TQ.ResourceID
                                        LEFT JOIN  academicstatus AC ON AC.id=T.AcademicStatusID
                                        where T.deleted=0 AND T.Scientificdegreeid=3 AND TC.type=0 ORDER BY lastname";
	
	
	
$res = mysql_query($sql);

if(!$res) {
	return FALSE;
}
for ($i = 0;$i < mysql_num_rows($res); $i++) {
	$row[] = mysql_fetch_array($res,MYSQL_ASSOC);
}
// Создаем объект класса PHPExcel
$xls = new PHPExcel();
// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
// Подписываем лист
$sheet->setCellValue("D1", 'СПИСОК ');
$sheet->mergeCells('C2:I2');
$sheet->setCellValue("C2", 'докторов наук по кафедрам КазНПУ имени Абая на '.date("d.m.Y"));

$sheet->setCellValue("A4", ' 
Фамилия ');
$sheet->setCellValue("B4"   , ' 
Имя ');
$sheet->setCellValue("C4"    ,' 
Отчество ');
$sheet->setCellValue("D4"   ,'
Ученая степень');
$sheet->setCellValue("E4"   ,'
Институт ');
$sheet->setCellValue("F4"  ,    ' 
Кафедра ');
$sheet->setCellValue("G4"  , ' 
Должность  ');
$sheet->setCellValue("H4"  , ' 
Академический статус  ');
$sheet->setCellValue("I4",  ' 
Ученая степень по спец. ');
$sheet->setCellValue("J4",' 
Дата рождения   ');
$sheet->setCellValue("K4",' 
Ставка');

	for ($i = 5;$i < count($row); $i++) {
	$j = $current_row+$i;
	$sheet->setCellValue("A".$j, $row[$i]['lastname']);
	$sheet->setCellValue("B".$j, $row[$i]['firstname']);
	$sheet->setCellValue("C".$j, $row[$i]['patronymic']);
	$sheet->setCellValue("D".$j, $row[$i]['Scientificdegree_ru']);
	$sheet->setCellValue("E".$j, $row[$i]['faculti_ru']);
	$sheet->setCellValue("F".$j, $row[$i]['cafedraNameRU']);
	$sheet->setCellValue("G".$j, $row[$i]['positio_ru']);
	$sheet->setCellValue("H".$j, $row[$i]['academicstatus_ru']);
	$sheet->setCellValue("I".$j, $row[$i]['sciencefields_ru']);
	$sheet->setCellValue("J".$j, $row[$i]['BirthDate']);
	$sheet->setCellValue("K".$j, $row[$i]['rate']);

	
}
$j += 3;
$sheet->setCellValue("C".$j, 'Ответственное лицо ________________________подпись ____________дата________');
header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=matrix.xls" );

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
?>
<html>
<style type="text/css">
table {border:none; border-collapse:collapse; margin:auto; margin-right:10px}
table td {border:solid blue 1px; padding:5px;}
</style>
<head>
<title>Отдел кадров</title>
<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>



<body>


 <script type="text/javascript"> 
  setTimeout('location.replace("doktor nauk.php")', 1000); 
</script> 

    


     
	</body>
	</head> 
</html>
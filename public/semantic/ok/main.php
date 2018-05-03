<?php
include 'config.php';
require_once("PHPExcel.php");
require_once('PHPExcel/Writer/Excel5.php');
$cn = mysql_connect(HOST,USER,PASS);
mysql_select_db(DB,$cn);
mysql_query("SET NAMES utf8");
$sql = "SELECT T.tutorid,T.lastname,T.firstname,T.patronymic,
       T.ScientificDegreeID,SD.NAMERU as Scientificdegree_ru,SC.NameRU as sciencefields_ru,
       TC.cafedraid,C.cafedraNameRU,TC.rate,T.BirthDate,
       TC.position,TP.NameRU as position_RU

FROM tutors T
INNER JOIN Scientificdegree SD ON T.Scientificdegreeid=SD.id
INNER JOIN sciencefields SC ON ScientificDegreeID=SC.id
INNER JOIN tutor_cafedra TC ON T.tutorID=TC.tutorid
INNER JOIN cafedras C ON TC.cafedraid=nitro.C.cafedraid
INNER JOIN tutor_positions TP ON TP.id=TC.position
where T.deleted=0 ORDER BY T.Lastname";
		
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
$sheet->setTitle("Список доктор наук");
// Вставляем текст в ячейку A1
$sheet->setCellValue("A1", 'Фамилия');
$sheet->setCellValue("B1",'Имя');
$sheet->setCellValue("C1",'Отчество');
$sheet->setCellValue("D1",'Ученая степень');
$sheet->setCellValue("E1",'Ученая степень по специальности');
$sheet->setCellValue("F1",'Кафедра');
$sheet->setCellValue("G1",'Позиция');
$sheet->setCellValue("H1",'Ставка');
$sheet->setCellValue("I1",'Датарождение');
	for ($i = 1;$i < count($row); $i++) {
	$j = $i+1;
	$sheet->setCellValue("A".$j, $row[$i]['lastname']);
	$sheet->setCellValue("B".$j, $row[$i]['firstname']);
	$sheet->setCellValue("C".$j, $row[$i]['patronymic']);
	$sheet->setCellValue("D".$j, $row[$i]['Scientificdegree_ru']);
	$sheet->setCellValue("E".$j, $row[$i]['sciencefields_ru']);
	$sheet->setCellValue("F".$j, $row[$i]['cafedraNameRU']);
	$sheet->setCellValue("G".$j, $row[$i]['position_RU']);
	$sheet->setCellValue("H".$j, $row[$i]['rate']);
	$sheet->setCellValue("I".$j, $row[$i]['BirthDate']);

	
}

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
table {border:none; border-collapse:collapse; margin:auto; margin-left:10px}
table td {border:solid blue 1px; padding:5px;}
</style>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1000">
<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<title>чет</title>
<meta name="" content="">

<body>

   <h2>Список докторов наук</h2>
    <p></p>
    <form class="ui-filterable">
      <input id="myFilter" data-type="search">
    </form>
	<script type="text/javascript"> 
  setTimeout('location.replace("main.php")', 1000); 
</script> 

    <ul data-role="listview" data-filter="true" data-input="#myFilter" data-autodividers="true" data-inset="true">  
 <<li data-filtertext="fav"><a href="main.php">Все списки доктор наук</a></li>
	  
	
	</body>
	</head> 
</html>

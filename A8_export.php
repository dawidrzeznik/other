<?php 

require("../PHPExcel/PHPExcel/IOFactory.php");
require_once ("../PHPExcel/PHPExcel.php");



//GENEROWANIE EXCELA

// WYSZUKIWANIE REKORDÓW
//$sql_a8 = "SELECT * FROM UR_ZwrotyCzesci WHERE SentToJDE !=9" ; //zm
$sql_a8 = "SELECT a.* , b.id_czesci_jde  FROM UR_ZwrotyCzesci a 
			left join UR_CzesciHistoria b  on a.Id_Pobranie = b.ID 
			WHERE Status !=9 AND Status !=0 AND Status!=10";

	if($_POST['short_id'] > 0)
		$sql_a8 .= " AND Id_Czesci = ".$_POST['short_id'];

	if(!empty($_POST['item_id']))
		$sql_a8 .= " AND id_czesci_jde = '".$_POST['item_id']."'";

	if($_POST['PrzeznaczenieFiltr'] != "*" && !empty($_POST['PrzeznaczenieFiltr']))
		$sql_a8 .= " AND Stan = '".$_POST['PrzeznaczenieFiltr']."'";

//echo($_POST['Weryfikacja']);
  	if(!empty($_POST['Weryfikacja']) && $_POST['Weryfikacja'] != '*' )
		if($_POST['Weryfikacja'] == 1) //zweryfikowane
			//$sql_a8 .= " AND  Weryfikowal is not null";
			$sql_a8 .= " AND Status=2";
		elseif($_POST['Weryfikacja'] == 2) //niezweryfikowane
			//$sql_a8 .= " AND  Weryfikowal is null";
			$sql_a8 .= " AND Status>=0 AND Status<2";
   if(!empty($_POST['data_od']))
		$sql_a8 .= " AND Data_Rozliczenia >= '".$_POST['data_od']."'";
	elseif(empty($_POST['Odswiezono']))
	  $sql_a8 .= " AND Data_Rozliczenia >= '".date("Y-m-d", time() - (7*24*3600))."'";


	if(!empty($_POST['data_do']))
		$sql_a8 .= " AND Data_Rozliczenia <= '".$_POST['data_do']."'";
	elseif(empty($_POST['Odswiezono']))
	  $sql_a8 .= " AND Data_Rozliczenia <= '".date("Y-m-d")."'";
	  
	
	
	if(!empty($_POST['weryf_od']))
		$sql_a8 .= " AND Data_Weryfikacji >= '".$_POST['weryf_od']."'";
	
	if(!empty($_POST['weryf_do']))
		$sql_a8 .= " AND Data_Weryfikacji <= '".$_POST['weryf_do']."'";
	



	if($_POST['PrzeznaczenieWeryfikacja'] != '*' && !empty($_POST['Weryfikacja']))
		$sql_a8 .= " AND Stan_Weryfikacja = '".$_POST['PrzeznaczenieWeryfikacja']."'";


$inputFileType = 'Excel5';
   if(!empty($_POST['ExcelPO']))
      $inputFileName = 'WeryfikacjaA8SzablonPO.xls';
   else
      $inputFileName = 'WeryfikacjaA8Szablon.xls';

$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);


	$objPHPExcel->getActiveSheet()->setCellValue("D1",date('d-m-Y h:m'));

	$rs_A8 = mssql_query($sql_a8) OR die($lang['error1']."<br>".$sql_a8);
 	$wiersz = 3;
	while($row_A8 = mssql_fetch_assoc($rs_A8))
  	{

		$sql="select IMITM as ID, IMDSC1 as opis1, IMDSC2 as opis2 from F4101 where IMITM= ".$row_A8['Id_Czesci'];
			        //echo($sql);
		$rsjde=mssql_query($sql) OR die($lang['error1']);
	   if(mssql_num_rows($rsjde)==0) die($lang['error6']);
		$rowjde=mssql_fetch_assoc($rsjde);

	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$wiersz,$row_A8['Id_Czesci']);
	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$wiersz,$row_A8['id_czesci_jde']);
	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$wiersz,$row_A8['Ilosc']);
	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$wiersz,mb_convert_encoding($rowjde['opis1'],'UTF-8', 'ISO-8859-2'));
	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$wiersz,mb_convert_encoding($row_A8['Uwagi'],'UTF-8', 'ISO-8859-2'));
	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$wiersz,mb_convert_encoding(PelnyPracownik($row_A8['Id_Prac_Oddal'], null),'UTF-8', 'ISO-8859-2'));
	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$wiersz,$row_A8['Stan']);

   if(!empty($_POST['ExcelPO']) && $row_A8['Stan_Weryfikacja']!=$row_A8['Stan']) //excel po weryfikacji, dodatkowa kolumna
	  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$wiersz,$row_A8['Stan_Weryfikacja']);


	$wiersz ++;
  	}


$styleArray = array(
          'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
          'font'  => array('size'  => 10) 
      );

      if(!empty($_POST['ExcelPO']))
      $objPHPExcel->getActiveSheet()->getStyle("A1:H".($wiersz-1))->applyFromArray($styleArray);
      else
      $objPHPExcel->getActiveSheet()->getStyle("A1:G".($wiersz-1))->applyFromArray($styleArray);
      
      unset($styleArray);


	// Write out as the new file
	// Write out as the new file
	ob_clean();
	//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Type: application/vnd.ms-excel; charset=ISO-8859-2');
	
	header('Cache-Control: max-age=0');
	//header("Content-Transfer-Encoding: BINARY");
if(!empty($_POST['ExcelPO']))
      header("Content-Disposition: attachment;filename=\"Po Weryfikacji A8.xls");
   else
      header("Content-Disposition: attachment;filename=\"Przed Weryfikacj¹ A8.xls");



	$outputFileType = 'Excel5';
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $outputFileType);
	//$objWriter->setVersion(8);//to wywo³anie jest wbrew pozorom bardzo wa¿ne ;)

	$objWriter->save('php://output');


?>
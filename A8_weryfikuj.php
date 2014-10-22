<?php
//echo("W pliku");
require("../PHPExcel/PHPExcel/IOFactory.php");
require_once ("../PHPExcel/PHPExcel.php");

echo("<script>history.forward();</script>");

//ZMIANY W PRZEZNACZENIU
if(!empty($_POST['ID_tran']) && $_POST['Zapisz'])
{

	$size = count($_POST['ID_tran']);
	$i = 0;
	while ($i < $size) {
	$sql_select = "SELECT Stan_Weryfikacja FROM UR_ZwrotyCzesci WHERE Id_Zwrotu= ".$_POST['ID_tran'][$i];

	$rs_select = mssql_query($sql_select) OR die("Select problem");
 	$row_select = mssql_fetch_assoc($rs_select);
	if(substr($_POST['Przeznaczenie'][$i],0,3) != 'BEZ')
	{
		$sql_update = "Update UR_ZwrotyCzesci
							set Stan_Weryfikacja = '".$_POST['Przeznaczenie'][$i]."',
							Id_Prac_Weryfikowal = '".$_SESSION['userID']."',
							ModifiedWhen = '".date("Y-m-d H:i:s")."',
							Data_Weryfikacji = '".date("Y-m-d H:i:s")."',
							ModifiedBy = '".$_SESSION['userID']."',
							Status=2
							WHERE Id_Zwrotu= ".$_POST['ID_tran'][$i];

		$rs_update = mssql_query($sql_update) OR die("Problem z update");
	}
	$i++;
	 }


}

// WYSZUKIWANIE REKORDÓW
$sql_a8 = "SELECT a.* , b.id_czesci_jde  FROM UR_ZwrotyCzesci a 
			left join UR_CzesciHistoria b  on a.Id_Pobranie = b.ID 
			WHERE Status !=9 AND Status !=0 AND Status !=10";

	if($_POST['short_id'] > 0)
		$sql_a8 .= " AND Id_Czesci = ".$_POST['short_id'];

	if(!empty($_POST['item_id']))
		$sql_a8 .= " AND id_czesci_jde = '".$_POST['item_id']."'";

	if($_POST['PrzeznaczenieFiltr'] != "*" && !empty($_POST['PrzeznaczenieFiltr']))
		$sql_a8 .= " AND Stan = '".$_POST['PrzeznaczenieFiltr']."'";

  	if(!empty($_POST['Weryfikacja']) && $_POST['Weryfikacja'] != '*' )
		if($_POST['Weryfikacja'] == 1) //zweryfikowane
			$sql_a8 .= " AND Status=2";
		elseif($_POST['Weryfikacja'] == 2) //niezweryfikowane
			$sql_a8 .= " AND Status=1";
   if(!empty($_POST['data_od']))
		$sql_a8 .= " AND Data_Rozliczenia >= '".$_POST['data_od']."'";
	elseif(empty($_POST['Odswiezono']))
	  $sql_a8 .= " AND Data_Rozliczenia >= '".date("Y-m-d", time() - (7*24*3600))."'";


	if(!empty($_POST['data_do']))
		$sql_a8 .= " AND Data_Rozliczenia <= dateadd(day,1,'".$_POST['data_do']."')";
	elseif(empty($_POST['Odswiezono']))
	  $sql_a8 .= " AND Data_Rozliczenia <= '".date("Y-m-d 23:59:59")."'";
	  
	
	
	if(!empty($_POST['weryf_od']))
		$sql_a8 .= " AND Data_Weryfikacji >= '".$_POST['weryf_od']."'";
	
	if(!empty($_POST['weryf_do']))
		$sql_a8 .= " AND Data_Weryfikacji <= dateadd(day,1,'".$_POST['weryf_do']."')";
	



	if($_POST['PrzeznaczenieWeryfikacja'] != '*' && !empty($_POST['Weryfikacja']))
		$sql_a8 .= " AND Stan_Weryfikacja = '".$_POST['PrzeznaczenieWeryfikacja']."'";


$rs_A8 = mssql_query($sql_a8) OR die($lang['error1']."<br>".$sql_a8);


echo("<table>")    ;
echo("\n<tr><td colspan=6><b>Wnioski A8:</b></td></tr>");
                echo("\n<tr>\n\t<td><b>ID czêœci (short):</b></td>");
                echo("<td><b>Numer seryjny (ID):</b></td>");
                echo("\n\t<td><b>Nr Zlecenia:</b></td>");
					 echo("\n\t<td><b>Iloœæ:</b></td>");
					 echo("\n\t<td><b>Opis 1:</b></td>");
					 echo("\n\t<td><b>Uwagi:</b></td>");
					 echo("\n\t<td><b>Zwróci³:</b></td>");
					 echo("\n\t<td><b>Przeznaczenie oryg.</b></td>");
					 echo("\n\t<td><b>Weryfikowa³:</b></td>");
					 echo("\n\t<td><b>Przeznaczenie weryf.</b></td>");
					 echo("\n\t<td><b>Zmieñ</b></td></tr>");


					 while($row_A8 = mssql_fetch_assoc($rs_A8))
					 {
                        if($color == "#9090FF") {
                                $color = "#C0C0FF";
                        } else {
                                $color = "#9090FF";
                        }

						$sql="select IMITM as ID, IMDSC1 as opis1, IMDSC2 as opis2 from F4101 where IMITM= ".$row_A8['Id_Czesci'];
						$rsjde=mssql_query($sql) OR die($lang['error1']);
				      if(mssql_num_rows($rsjde)==0) die($lang['error6']);
			   	   $rowjde=mssql_fetch_assoc($rsjde);


                        echo("\n<tr bgcolor=\"$color\">\n\t<td>".$row_A8['Id_Czesci']."</td>");
                        echo("\n\t<td>".$row_A8['id_czesci_jde']."&nbsp;</td>");
                        echo("\n\t<td><a href=\"../Zlecenia/?op=4&dat=szur&id_zlec=".$row_A8['Id_Zlec']."\" target='_blanket'>".$row_A8['Id_Zlec']."</a> </td>");
								echo("\n\t<td>".$row_A8['Ilosc']."</td>");
								echo("\n\t<td>".$rowjde['opis1']."</td>");
								echo("\n\t<td>".$row_A8['Uwagi']."</td>");
								echo("\n\t<td>".PelnyPracownik($row_A8['Id_Prac_Oddal'], null)."</td>");
								echo("\n\t<td><b>".$row_A8['Stan']."</b></td>");
								echo("\n\t<td>".PelnyPracownik($row_A8['Id_Prac_Weryfikowal'], null)."</td>");
                        echo("\n\t<td><b>");
							  	if(!empty($row_A8['Id_Prac_Weryfikowal']))
									echo($row_A8['Stan_Weryfikacja']);
								else
									echo("&nbsp;");
								echo("</b></td>");
								echo("\n\t<td><select name=\"Przeznaczenie[]\">");
								echo("\n\t<option value='BEZ' >Bez weryfikacji</option>");
                 	  		echo("\n\t<option value='MCN' >MCN</option>");
	 							echo("\n\t<option value='REGENERACJA' >REGENERACJA</option>");
								echo("\n\t<option value='UTYLIZACJA'  >UTYLIZACJA</option>");
								echo("\n\t</select>");
								echo("\n\t<input type=\"hidden\" name=\"ID_tran[]\" value=\"".$row_A8['Id_Zwrotu']."\"><br></td></tr>");
						  }
//
					 echo("<tr><td colspan=10 align='right'><input type=\"submit\" name=\"Zapisz\" value=\"Zapisz\"></td></tr>");
					echo("</table>");

					 echo("\n</form>");

?>
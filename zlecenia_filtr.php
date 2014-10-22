<?php 
        echo("\n<table align=\"center\" bgcolor=\"#E0C040\" cellpadding=\"2\" cellspacing=\"0\" border=1>");
        echo("\n<form action=\"\" method='POST'>");

        echo("\n<tr align=\"center\">");
        echo("\n\t<td colspan=\"8\" align=\"center\"><b>".$lang['typical11']."</b></td></tr>");
        echo("\n<tr align=\"left\">");
        
        if($_POST['wyczysc']) {
                $fizl = null;
        } elseif($_POST['wyswietl']==$langbtn['typical4'] || $_POST['CzasWystAuto']) {
                 UserPrefsWrite($_POST,"FIZL");
                 $fizl=$_POST;
        } elseif(!$_POST['wyczysc']) {
                $fizl = UserPrefsRead("FIZL");
        }
        
        //Objaw text
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical113']."</b></td>");
        echo("\n\t<td><input name=\"Objaw_Zlecenia\" type=\"text\" value=\"".htmls(magicslashes($fizl['Objaw_Zlecenia']))."\"></td>");
        
        //Lokalizacja
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical7']."</b></td>");
        echo("\n\t<td><select name=\"Sciezka\">");
        echo("\n\t<option value=\"#\">#</option>");
        $sql="SELECT Sciezka FROM UR_Drzewo_DefPoziomy INNER JOIN UR_PelnaSciezkaT ON UR_Drzewo_DefPoziomy.ID_lok=UR_PelnaSciezkaT.ID_lok WHERE OznPoziomu In ('M_HM', 'M_IN', 'M_II') ORDER BY Sciezka";
        $rsKombi=mssql_query($sql) OR die($lang['error1']);
        while ($rowKombi=mssql_fetch_assoc($rsKombi)) 
                echo("\n\t<option value=\"".$rowKombi['Sciezka'].(($rowKombi['Sciezka']==magicslashes($fizl['Sciezka']))?"\" selected=\"selected\">":"\">").$rowKombi['Sciezka']."</option>");
        echo("\n\t</select></td>");
        
        //Lokalizacja text
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical7a']."</b></td>");
        echo("\n\t<td><input name=\"MetaSciezka\" type=\"text\" value=\"".htmls(magicslashes($fizl['MetaSciezka']))."\"></td>");

        //Nowy wiersz 
        echo("</tr>\n\t<tr>"); 
        
        //Status
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical14']."</b></td>");
        echo("\n\t<td><select name=\"StatusZlec\">");
        //echo("\n\t<option value=\"-1\">*</option>");
        //echo("\n\t<option value=\"-2\">* [Otwarte]</option>");
        $stSymb = array("-1","-2","1","2","3","4","5","6","7");
        $stNaz = $langarr['ZlecStNaz'];
        for($i=0;$i<9;$i++) {
                echo("\n\t<option value=\"".$stSymb[$i].(($stSymb[$i]==$fizl['StatusZlec'])?"\" selected=\"selected\">":"\">").$stNaz[$i]."</option>");
        }
        echo("\n\t</select></td>");
        
        //Grupa
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical13']."</b></td>");
        echo("\n\t<td><select name=\"Rodzaj_Pracy\">");
        echo("\n\t<option value=\"*\">*</option>");
        echo("\n\t<option value=\"UR%\"".($fizl['Rodzaj_Pracy'] == "UR%" ? " selected" : "").">UR</option>");
        echo("\n\t<option value=\"UR[EM]\"".($fizl['Rodzaj_Pracy'] == "UR[EM]" ? " selected" : "").">UR (E+M)</option>");
        echo("\n\t<option value=\"IN%\"".($fizl['Rodzaj_Pracy'] == "IN%" ? " selected" : "").">IN</option>");
        echo("\n\t<option value=\"KL%\"".($fizl['Rodzaj_Pracy'] == "KL%" ? " selected" : "").">KL</option>");
        $sql="SELECT Grupa, Nazwa, NazwaEN FROM UR_Grupy WHERE Grupa LIKE 'UR[^K]%' OR Grupa LIKE 'IN[^K]%' ORDER BY Nazwa";
        $rsKombi=mssql_query($sql) OR die($lang['error1']);
        while ($rowKombi=mssql_fetch_assoc($rsKombi)) 
                echo("\n\t<option value=\"".$rowKombi['Grupa'].(($rowKombi['Grupa']==$fizl['Rodzaj_Pracy'])?"\" selected>":"\">") .(($_SESSION['user_lang']=="english")?$rowKombi['NazwaEN']:$rowKombi['Nazwa'])."</option>");
        echo("\n\t</select></td>");
        
        //Rodzaj
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical66a']."</b></td>");
        echo("\n\t<td><select name=\"Rodzaj_Zlecenia\">");
        echo("\n\t<option value=\"*\">*</option>");
        $sql="SELECT Skrot, RodzajEN, Rodzaj FROM UR_RodzajeZlecen;";
        $rsKombi=mssql_query($sql) or die($lang['error1']);
        while ($rowKombi=mssql_fetch_assoc($rsKombi)) 
                echo("\n\t<option value=\"".$rowKombi['Skrot'].(($rowKombi['Skrot']==$fizl['Rodzaj_Zlecenia'])?"\" selected=\"selected\">":"\">").(($_SESSION['user_lang']=="english")?$rowKombi['RodzajEN']:$rowKombi['Rodzaj'])."</option>");
        echo("\n\t</select></td>");
        
        //Nowy wiersz 
        echo("</tr>\n\t<tr>"); 

        //Zlecaj¹cy
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical68']."</b></td>");
        echo("\n\t<td><select name=\"Zlecajacy_ID\" type=\"text\">");
        echo("\n\t<option value=\"-1\">*</option>");
        $sql="SELECT PRC_ID, PRC_NAZWISKO, PRC_IMIE FROM UR_Uzytkownicy WHERE Aktywny = 1 ORDER BY PRC_NAZWISKO, PRC_IMIE";
        $rsKombi=mssql_query($sql) or die($lang['error1']);
        while($rowKombi=mssql_fetch_assoc($rsKombi)) {
                echo("\n\t<option value=\"".$rowKombi['PRC_ID'].($rowKombi['PRC_ID']==$fizl['Zlecajacy_ID']?"\" selected>":"\">").RTrim($rowKombi['PRC_NAZWISKO'])." ".RTrim($rowKombi['PRC_IMIE'])." [".$rowKombi['PRC_ID']."]"."</option>");
        }
        echo("</select></td>");

        //Przyjmuj¹cy
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical112']."</b></td>");
        echo("\n\t<td><select name=\"KtoPrzyjal\" type=\"text\">");
        echo("\n\t<option value=\"-1\">*</option>");
        $sql="SELECT PRC_ID, PRC_NAZWISKO, PRC_IMIE FROM UR_Uzytkownicy WHERE (Grupa Like 'UR%' OR Grupa Like 'IN%') AND Aktywny = 1 ORDER BY PRC_NAZWISKO, PRC_IMIE";        
        $rsKombi=mssql_query($sql) or die($lang['error1']);
        while($rowKombi=mssql_fetch_assoc($rsKombi)) {
                echo("\n\t<option value=\"".$rowKombi['PRC_ID'].($rowKombi['PRC_ID']==$fizl['KtoPrzyjal']?"\" selected=\"selected\">":"\">").RTrim($rowKombi['PRC_NAZWISKO'])." ".RTrim($rowKombi['PRC_IMIE'])." [".$rowKombi['PRC_ID']."]"."</option>");
        }
        echo("</select></td>");

        //Prowadz¹cy
        echo("\n\t<td align=\"right\" valign=\"top\"><b>Prowadz¹cy</b></td>");
        echo("\n\t<td><select name=\"Prowadzacy\" type=\"text\">");
        echo("\n\t<option value=\"-1\">*</option>");
        $sql="SELECT PRC_ID, PRC_NAZWISKO, PRC_IMIE FROM UR_Uzytkownicy WHERE (Grupa Like 'UR%' OR Grupa Like 'IN%') AND Aktywny = 1 ORDER BY PRC_NAZWISKO, PRC_IMIE";        
        $rsKombi=mssql_query($sql) or die($lang['error1']);
        while($rowKombi=mssql_fetch_assoc($rsKombi)) {
                echo("\n\t<option value=\"".$rowKombi['PRC_ID'].($rowKombi['PRC_ID']==$fizl['Prowadzacy']?"\" selected=\"selected\">":"\">").RTrim($rowKombi['PRC_NAZWISKO'])." ".RTrim($rowKombi['PRC_IMIE'])." [".$rowKombi['PRC_ID']."]"."</option>");
        }
        echo("</select></td>");

        //Nowy wiersz 
        echo("</tr>\n\t<tr>"); 

        //Data auto
        echo("\n\t<td align=\"right\" valign=\"top\" bgcolor=\"#B0A070\"><b>".$lang['typical114']."</b></td>");
        echo("\n\t<td bgcolor=\"#B0A070\"><select onchange='this.form.submit()' name=\"CzasWystAuto\">");
        echo("\n\t<option value=\"0A\">*</option>");
        $stSymb = array("1D","4D","1T","1M");
        $stNaz = $langarr['CzasyStNaz'];
        for($i=0;$i<4;$i++) {
                echo("\n\t<option value=\"".$stSymb[$i]."\">".$stNaz[$i]."</option>");
        }
        echo("\n\t</select></td>");
        //echo("\n\t<input name=\"wyst_auto\" type=\"submit\" value=\"&gt;&gt;\"></td>");
        //echo("</td>");
        if($_POST['CzasWystAuto'] && $_POST['CzasWystAuto']!="0A" && !$_POST['wyczysc']) {
                $data_auto=true;
                $data_do=date("Y-m-d H:i");
                switch ($_POST['CzasWystAuto']) {
                        case "1D":
                        $data_od = date("Y-m-d H:i",mktime(date("H"),date("i"),0,date("m"),date("d")-1,date("Y")));
                        break;
                        case "4D":
                        $data_od = date("Y-m-d H:i",mktime(date("H"),date("i"),0,date("m"),date("d")-4,date("Y")));
                        break;
                        case "1T":
                        $data_od = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
                        break;
                        case "1M":
                        $data_od = date("Y-m-d",mktime(0,0,0,date("m"),1,date("Y")));
                        break;
                }
        } else {
                $data_auto=false;
        }

        //Data OD
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical115']."</b></td>");
        echo("\n\t<td><input name=\"CzasWystOD\" type=\"text\" size=\"16\" value=\"".($data_auto?$data_od:$fizl['CzasWystOD'])."\"></td>");
        
        //Data DO
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical116']."</b></td>");
        echo("\n\t<td><input name=\"CzasWystDO\" type=\"text\" size=\"16\" value=\"".($data_auto?$data_do:$fizl['CzasWystDO'])."\"></td>");

        //Nowy wiersz 
        echo("</tr>\n\t<tr>"); 

        //ID
        echo("\n\t<td align=\"right\" valign=\"top\"><b>".$lang['typical5a'].":</b></td>");
        echo("\n\t<td><input name=\"ID_zlec\" type=\"text\" size=\"12\" value=\"".($_POST['wyczysc'] ? "" : $_POST['ID_zlec'])."\"></td>");
        
        //ID JDE
        echo("\n\t<td align=\"right\" valign=\"top\"><b>JDE WO:</b></td>");
        echo("\n\t<td><input name=\"JDE_ID\" type=\"text\" size=\"16\" value=\"".($_POST['wyczysc'] ? "" : $_POST['JDE_ID'])."\"></td>");
        
        //Data source
        echo("\n\t<td align='right'><b>Dane z:</b></td>");
        echo("\n\t<td align='left'><input type='radio' name='data_source' value='szur'".(($_POST['data_source']=="szur")?" checked":(($_POST['data_source']=="jde")?"":" checked"))."> SZUR &nbsp;&nbsp;");
        echo("\n\t<input type='radio' name='data_source' value='jde'".(($_POST['data_source']=="jde")?" checked":"")."> JDE</td>");
         
        //Nowy wiersz 
        echo("</tr>\n\t<tr>"); 

        //Submit
        echo("\n\t<td align='right' colspan='6'>");        
        echo("<input name=\"wyswietl\" type=\"submit\" value=\"".$langbtn['typical4']."\">&nbsp;&nbsp;");
        echo("<input name=\"wyczysc\" type=\"submit\" value=\"".$langbtn['typical5']."\">");
        echo("</td></tr>");
        echo("\n</form></table>");
?>
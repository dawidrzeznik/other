<?php
require("../db_connect.php");
include("../constans.php");
include("../".$langfile);
include("../funkcje.inc.php");
include("../funkcje_sql.php");
include("top_druk.html");

        if(!$_GET['id_zlec']) die($lang['error10']);
        
        //nag³ówek zlecenia
        $sql="SELECT * FROM UR_Zlecenia WHERE ID_zlec = ".$_GET['id_zlec'];
        $rsZlec=mssql_query($sql) OR die($lang['error1']);
        if(mssql_num_rows($rsZlec)==0) die($lang['error6']);
        $rowZlec=mssql_fetch_assoc($rsZlec);
        echo("\n<table align='center' cellpadding='2' cellspacing='0' border='3' frame='below' rules='none'>");
        echo("\n\t<tr><td align=\"right\" colspan=4><a href='javascript:print_and_close()'><b>Drukuj</b></a> / ");
        echo("<a href='javascript:location.reload()'><b>Odswiez</b></a></td></tr>");
        echo("\n\t<tr><td align=\"center\" colspan=4><b><h2>LISTA CZÊŒCI DO ZLECENIA SZUR</h2></b></td></tr>");
        
        echo("\n\t<tr><td align=\"right\"><b>ID zlecenia SZUR:</b></td><td>".$rowZlec['ID_zlec']."</td>");
        echo("\n\t<td align=\"right\"><b>ID zlecenia JDE:</b></td><td><b><a href=\"?op=4&dat=jde&id_zlec=".$rowZlec['JDE_ID']."\">".$rowZlec['JDE_ID']."</a></b></td></tr>");
        
        // lokalizacja
        echo("\n\t<tr><td align=\"right\"><b>".$lang['typical7'].":</b></td><td>".PelnaSciezka($rowZlec['ID_lok'])."</td>");

        //bu jde
        echo("\n\t<td align=\"right\"><b>Business unit JDE:</b></td>");
        if($rowZlec['JDE_ID']) {
                $sql_bu = "SELECT WAMCU FROM F4801 WHERE WADOCO = ".$rowZlec['JDE_ID'];
                $rowBU = mssql_fetch_assoc(mssql_query($sql_bu));
                echo("<td>".$rowBU['WAMCU']."</td>");
        } else {
                echo("<td>brak</td>");        
        }
        echo("</tr>");        

        echo("\n\t<tr><td colspan=4>&nbsp</td></tr>");
        
        // zleci³
        echo("\n\t<tr><td align=\"right\"><b>".$lang['typical68'].":</b></td><td>".PelnyPracownik($rowZlec['Zlecajacy_ID'],null)."</td>");
        
        // przyj¹³
        echo("\n\t<td align=\"right\"><b>".$lang['typical112'].":</b></td><td>".PelnyPracownik($rowZlec['KtoPrzyjal'],null)."</td></tr>");
        
        // objaw
        echo("\n\t<tr><td align=\"right\"><b>".$lang['typical31'].":</b></td><td colspan=3>".htmls($rowZlec['Objaw_Zlecenia'])."</td></tr>");        
        echo("\n\t<tr><td colspan=4>&nbsp</td></tr>\n</table>");

   // przypisane czêœci
        echo("\n<table align=\"center\" cellpadding=2 cellspacing=0 border=1 frame=\"below\" rules=\"rows\">");
        echo("\n<tr><td colspan=6><b>".$lang['typical151'].":</b></td></tr>");
        echo("\n<tr>\n\t<td><b>ID krótkie:</b></td><td><b>ID d³ugie:</b></td>");
        echo("\n\t<td><b>".$lang['typical8'].":</b></td>");
        echo("\n\t<td><b>Lokalizacja:</b></td>\n\t<td><b>".$lang['typical152'].":</b></td>");
        echo("\n\t<td><b>".$lang['typical9a'].":</b></td>\n\t<td>&nbsp;</td>");
        $sql="SELECT * FROM UR_CzesciHistoria WHERE Id_Zlec = ".$_GET['id_zlec']." ORDER BY Kolejnosc";
        //echo($sql);
        if($rsPom = mssql_query($sql)) {//OR die($lang['error1']."<br>".$sql);
                $excl_cpnb = "0";
                while ($rowPom=mssql_fetch_assoc($rsPom)) {
                        if(!$rowPom['id_czesci']) continue;
                        //wyœwietla szczegó³y czêœci
                        $sqlit = "SELECT IMDSC1, IMDSC2, IMLITM FROM F4101 WHERE IMITM = ".$rowPom['id_czesci'];
                        if($rsItm = mssql_query($sqlit, $conn_p)) $rowItm = mssql_fetch_assoc($rsItm);
                        echo("\n<tr bgcolor=\"$color\">\n\t<td>".$rowPom['id_czesci']."</td>");          
                        echo("\n\t<td>".($rowItm['IMLITM'] ? $rowItm['IMLITM'] : $rowPom['id_czesci_jde'])."&nbsp;</td>");                
                        echo("\n\t<td>".$rowItm['IMDSC1']." (".$rowItm['IMDSC2'].")</td>");
                        //lokalizacja / dostêpne
                        echo("\n\t<td>");
                        if($rowPom['id_czesci'] && $rowPom['branch_jde']) {
                                $sqld = "SELECT LILOCN FROM F41021 WHERE LIITM = ".$rowPom['id_czesci']." AND (LIMCU = '".$rowPom['branch_jde']."') AND LIPQOH > 0";
                                $rsDost = mssql_query($sqld);
                                while ($rowDost = mssql_fetch_assoc($rsDost)) {
                                        if(trim($rowDost['LILOCN'])) echo($rowDost['LILOCN']."<br>");
                                }                                
                        }
                        echo("</td>");
                        echo("\n\t<td>".$rowPom['branch_jde']."</td>");
                        //iloœæ zamówiona / pobrana
                        $iloscReal = "empty";
                        if($rowZlec['JDE_ID'] && $rowPom['id_czesci']) {
                                $sqlp = "SELECT TOP 1 WMCPNB, WMCPIT, WMTRQT, WMUORG FROM F3111 WHERE WMDOCO = ".$rowZlec['JDE_ID']." AND NOT WMCPNB IN($excl_cpnb) AND WMCPIT = ".$rowPom['id_czesci']." AND WMUORG = ".$rowPom['ilosc']." * 1000 ORDER BY WMCPNB";
                                //echo($sqlp);
                                if($rsPobr = mssql_query($sqlp) AND $rowPobr = mssql_fetch_assoc($rsPobr)) {
                                        $iloscReal = $rowPobr['WMTRQT'] / 1000;
                                        $excl_cpnb .= ", ".$rowPobr['WMCPNB'];
                                }
                        }
                        echo("\n\t<td>".$rowPom['ilosc']." / $iloscReal</td>");
                        echo("\n</tr>");
                }        
        } else echo("<tr><td colspan=6>UWAGA: B³¹d po³aczenia z systemem JDE, spróbuj póŸniej...<br>".$sql."</td></tr>");
        echo("</table>");

?>
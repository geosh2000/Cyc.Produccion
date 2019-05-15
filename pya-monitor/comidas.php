<?
function addTime($timeB, $timeA) {
    
    $timeAinSeconds = intval(date('H', strtotime($timeA)))*60*60 + intval(date('i', strtotime($timeA)))*60 + intval(date('s', strtotime($timeA)));
    $timeBinSeconds = intval(date('H', strtotime($timeB)))*60*60 + intval(date('i', strtotime($timeB)))*60 + intval(date('s', strtotime($timeB)));

    $timeABinSeconds = $timeAinSeconds - $timeBinSeconds;

    $timeABsec = $timeABinSeconds % 60;
    $timeABmin = (($timeABinSeconds - $timeABsec) / 60) % 60;
    $timeABh = ($timeABinSeconds - $timeABsec - $timeABmin*60) / 60 / 60;

    return str_pad((int) $timeABh,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABmin,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABsec,2,"0",STR_PAD_LEFT);
}
?>

<table class='t2' width='100%'>
	<tr class='title'>
		<th width='10%'>Comidas</th>
		<th width='15%' colspan=2><? echo $hora[0]; ?></th>
		<th width='15%' colspan=2><? echo $hora[1]; ?></th>
		<th width='15%' colspan=2><? echo $hora[2]; ?></th>
		<th width='15%' colspan=2><? echo $hora[3]; ?></th>
		<th width='15%' colspan=2><? echo $hora[4]; ?></th>
	</tr>
	<?
			foreach($pcrcs_departamento as $key5 => $depto5){

				$depid=$pcrcs_id[$key5];
				foreach($hora as $hk5 => $htime5){
					$query="SELECT Asesores.`N Corto`, `Historial Programacion`.id as 'hid', Asesores.id FROM `Historial Programacion` LEFT JOIN `Asesores` ON `Historial Programacion`.asesor=`Asesores`.id WHERE `Historial Programacion`.Fecha='$dia[$hk5]' AND `Asesores`.`id Departamento`='$depid' AND `Asesores`.`Activo`=1 AND `Historial Programacion`.`comida start`='$htime5' AND (`Historial Programacion`.`comida start`!='00:00:00' OR `Historial Programacion`.`comida end`!='00:00:00') ORDER BY `Asesores`.`N Corto`";
					$result=mysql_query($query);
					$num[$hk5]=mysql_numrows($result);
					$i=0;
					while($i<$num[$hk5]){
						$flag_aus=0;
						$a_comida[$key5][$hk5][$i]=mysql_result($result,$i,'N Corto');
						$hid_comida[$key5][$hk5][$i]=mysql_result($result,$i,'hid');
						$a_id=mysql_result($result,$i,'id');
						$id_comida[$key5][$hk5][$i]=$a_id;
						
						
						
						$query="SELECT * FROM Comidas WHERE asesor='$a_id' AND Fecha='$dia2[$hk5]' AND tipo=3";
						
						$result2=mysql_query($query);
						$num_c[$key5][$hk5][$i]=mysql_numrows($result2)-1;
						$h_comida[$key5][$hk5][$i]=mysql_result($result2,$num_c[$key5][$hk5][$i],'Inicio');
						$h_comida_f[$key5][$hk5][$i]=mysql_result($result2,$num_c[$key5][$hk5][$i],'Fin');
                        $comida_mx_i[$key5][$hk5][$i]=new DateTime($dia2[$hk5].' '.$h_comida[$key5][$hk5][$i].' America/Mexico_city');
                        $comida_mx_i[$key5][$hk5][$i]->setTimezone($cun_time);
                        $comida_mx_i_ok[$key5][$hk5][$i]=$comida_mx_i[$key5][$hk5][$i]->format('H:i:s');
                        $comida_mx_f[$key5][$hk5][$i]=new DateTime($dia2[$hk5].' '.$h_comida_f[$key5][$hk5][$i].' America/Mexico_city');
                        $comida_mx_f[$key5][$hk5][$i]->setTimezone($cun_time);
                        $comida_mx_f_ok[$key5][$hk5][$i]=$comida_mx_f[$key5][$hk5][$i]->format('H:i:s');
						$query="SELECT * FROM Ausentismos a, `Tipos Ausentismos` b WHERE a.tipo_ausentismo=b.id AND a.asesor='$a_id' AND a.Inicio<='$dia2[$hk5]' AND a.Fin>='$dia2[$hk5]'";
						
						$result3=mysql_query($query);
						$num3=mysql_numrows($result3);
						if($num3>0){ $h_comida[$key5][$hk5][$i]=mysql_result($result3,0,'Ausentismo'); $flag_aus=1;}
						$query3="SELECT * FROM PyA_Exceptions a, `Tipos Excepciones` b WHERE a.tipo=b.exc_type_id AND a.horario_id='".$hid_comida[$key5][$hk5][$i]."'";
						$result3=mysql_query($query3);
						$num3=mysql_numrows($result3);
						if($num3>0){$exc_comida[$key5][$hk5][$i]="<br>".mysql_result($result3,0,'Excepcion');}
						
						if($h_comida[$key5][$hk5][$i]!=NULL && $flag_aus==0){
                            $h_comida[$key5][$hk5][$i]=$comida_mx_i_ok[$key5][$hk5][$i];
                            $h_comida_f[$key5][$hk5][$i]=$comida_mx_f_ok[$key5][$hk5][$i];
                        }
						if($h_comida[$key5][$hk5][$i]==NULL && strtotime($htime5)<strtotime('now')){
							$style5[$key5][$hk5][$i]="class='flashred'";
						}
						if(strtotime($h_comida[$key5][$hk5][$i])>strtotime($htime5.'+16 minutes'))
						
						{
							$style5[$key5][$hk5][$i]="class='flashred'";
						}elseif(strtotime($h_comida[$key5][$hk5][$i])>=strtotime($htime5.'+11 minutes') )
						
						{	
							$style5[$key5][$hk5][$i]="class='orange'";
						}elseif($h_comida[$key5][$hk5][$i]!=NULL){
							$style5[$key5][$hk5][$i]="class='green'";
						}
						
						if(intval(date('G',strtotime($h_comida[$key5][$hk5][$i])))>=23 || date('H:i:s',strtotime($htime5))=='00:00:00'){
							$style5[$key5][$hk5][$i]="class='green'";
						}
						
						if($exc_comida[$key5][$hk5][$i]!=""){$style5[$key5][$hk5][$i]="class='orange'";}
						if($flag_aus==1){$style5[$key5][$hk5][$i]="class='yellow'";}
					$i++;
					}
					$i=0;
					
				}
				while($i<max($num)){
						if($i % 2 == 0){$class="pair";}else{$class="odd";}
							echo "\t<tr class=$class>\n";
						if($i==0){ echo "\t\t<th valign='middle' class='title' width='10%' rowspan='".max($num)."'>$depto5</th>"; }
						$iddiv++;
							$x=0;
							while($x<5){
								if($h_comida[$key5][$x][$i]!=NULL){
									if(strtotime($h_comida_f[$key5][$x][$i])>strtotime($h_comida[$key5][$x][$i].'+30 minutes'))
										{$c_class="class='flashred'";}else{$c_class="";}
									$n_comidas="(".($num_c[$key5][$x][$i]+1).")";
								$t="<br>$n_comidas ".addTime($h_comida[$key5][$x][$i],$h_comida_f[$key5][$x][$i]);}else{ $t="";}
								echo "\t\t<td>".$a_comida[$key5][$x][$i]."$t</td>\n
								\t\t<td ".$style5[$key5][$x][$i].">";
								if($h_comida[$key5][$x][$i]!=NULL){ echo "i ".$h_comida[$key5][$x][$i]."<br>f ".$h_comida_f[$key5][$x][$i];}
								echo "</td>\n";
							$x++;
							}
					$i++;
				}
			}
		?>
</table>
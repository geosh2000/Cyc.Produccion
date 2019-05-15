<?
include("../connectDB.php");
include("../DBPcrcs.php");
include("../common/scripts.php");
include("../common/erlangC.php");

$factor_forecast=$_POST['fcast'];
$aht=$_POST['aht'];
$tat=$_POST['tat'];
$slr=$_POST['slr'];
$_callsok=$_POST['calls'];
$reductores=$_POST['red'];
if($factor_forecast==NULL){$factor_forecast=0.75;}
if($aht==NULL){$aht=500;}
if($tat==NULL){$tat=30;}
if($slr==NULL){$slr=0.70;}
if($reductores==NULL){$reductores=0.125;}
$inicio=$_POST['inicio'];
$fin=$_POST['fin'];
if($inicio==NULL){$inicio=date('Y-m-d');}
if($fin==NULL){$fin=date('Y-m-d', strtotime('+30 days'));}
$start=intval(date('Y',strtotime($inicio))-1)."-".date('m-d',strtotime($inicio));
$end=intval(date('Y',strtotime($fin))-1)."-".date('m-d',strtotime($fin.'+ 1 days'));




$query="SELECT DayWeek, AVG(b.`1`/Total) as '1', AVG(b.`2`/Total) as '2', AVG(b.`3`/Total) as '3', AVG(b.`4`/Total) as '4', AVG(b.`5`/Total) as '5', AVG(b.`6`/Total) as '6', AVG(b.`7`/Total) as '7', AVG(b.`8`/Total) as '8', AVG(b.`9`/Total) as '9', AVG(b.`10`/Total) as '10', AVG(b.`11`/Total) as '11', AVG(b.`12`/Total) as '12', AVG(b.`13`/Total) as '13', AVG(b.`14`/Total) as '14', AVG(b.`15`/Total) as '15', AVG(b.`16`/Total) as '16', AVG(b.`17`/Total) as '17', AVG(b.`18`/Total) as '18', AVG(b.`19`/Total) as '19', AVG(b.`20`/Total) as '20', AVG(b.`21`/Total) as '21', AVG(b.`22`/Total) as '22', AVG(b.`23`/Total) as '23', AVG(b.`24`/Total) as '24', AVG(b.`25`/Total) as '25', AVG(b.`26`/Total) as '26', AVG(b.`27`/Total) as '27', AVG(b.`28`/Total) as '28', AVG(b.`29`/Total) as '29', AVG(b.`30`/Total) as '30', AVG(b.`31`/Total) as '31', AVG(b.`32`/Total) as '32', AVG(b.`33`/Total) as '33', AVG(b.`34`/Total) as '34', AVG(b.`35`/Total) as '35', AVG(b.`36`/Total) as '36', AVG(b.`37`/Total) as '37', AVG(b.`38`/Total) as '38', AVG(b.`39`/Total) as '39', AVG(b.`40`/Total) as '40', AVG(b.`41`/Total) as '41', AVG(b.`42`/Total) as '42', AVG(b.`43`/Total) as '43', AVG(b.`44`/Total) as '44', AVG(b.`45`/Total) as '45', AVG(b.`46`/Total) as '46', AVG(b.`47`/Total) as '47', AVG(b.`48`/Total) as '48' FROM (SELECT id as id1,(`1`+`2`+`3`+`4`+`5`+`6`+`7`+`8`+`9`+`10`+`11`+`12`+`13`+`14`+`15`+`16`+`17`+`18`+`19`+`20`+`21`+`22`+`23`+`24`+`25`+`26`+`27`+`28`+`29`+`30`+`31`+`32`+`33`+`34`+`35`+`36`+`37`+`38`+`39`+`40`+`41`+`42`+`43`+`44`+`45`+`46`+`47`+`48`) as 'Total', CAST(CONCAT(Anio,'/',IF(Mes<10,CONCAT('0',Mes),Mes),'/',IF(Dia<10,CONCAT('0',Dia),Dia)) as date) as Fecha, DAYOFWEEK(CAST(CONCAT(Anio,'/',IF(Mes<10,CONCAT('0',Mes),Mes),'/',IF(Dia<10,CONCAT('0',Dia),Dia)) as date)) as DayWeek FROM `Historial Llamadas` WHERE Skill='Servicio a Cliente' AND Mes='1' ORDER BY Anio, Mes, Dia) a, `Historial Llamadas` b WHERE a.id1=b.id AND Fecha >= '$start' AND Fecha <= '$end' Group By Dayweek";
$result=mysql_query($query);
$num=mysql_numrows($result);

$i=0;
while($i<$num){
	$x=1;
	while($x<=48){
		$calls[$i][$x]=mysql_result($result,$i,$x);
		
	$x++;
	}
	
	$dw[$i]=mysql_result($result,$i,'DayWeek');
	
$i++;
}

$query="SELECT * FROM (SELECT id as id1,CAST(CONCAT(Anio,'/',IF(Mes<10,CONCAT('0',Mes),Mes),'/',IF(Dia<10,CONCAT('0',Dia),Dia)) as date) as Fecha, $_callsok as 'Total', WEEKOFYEAR(CAST(CONCAT(Anio,'/',IF(Mes<10,CONCAT('0',Mes),Mes),'/',IF(Dia<10,CONCAT('0',Dia),Dia)) as date)) as W, DAYOFYEAR(CAST(CONCAT(Anio,'/',IF(Mes<10,CONCAT('0',Mes),Mes),'/',IF(Dia<10,CONCAT('0',Dia),Dia)) as date)) as DY,DAYOFWEEK(CAST(CONCAT(Anio,'/',IF(Mes<10,CONCAT('0',Mes),Mes),'/',IF(Dia<10,CONCAT('0',Dia),Dia)) as date)) as DW FROM `Historial Llamadas` WHERE Skill='Servicio a Cliente') a  WHERE Fecha >= '$start' AND Fecha <= '$end'";
$result=mysql_query($query);
$num2=mysql_numrows($result);

$i=0;
while($i<$num2){
	
	$total[$i]=mysql_result($result,$i,'Total');
	$fecha[$i]=mysql_result($result,$i,'Fecha');
	$fechaok[$i]=date('d-M', strtotime($fecha[$i]));
	$week[$i]=mysql_result($result,$i,'W');
	$dwt[$i]=mysql_result($result,$i,'DW');
	$dy[$i]=mysql_result($result,$i,'DY');
	
$i++;
}

//List Depts
	$i=0;
	$departs="<option value'' selected>Selecciona...</option>";
	while($i<$pcrcs_num){
		if($pcrcs_id_Sorted[$i]==$dep){$sel="selected";}else{$sel="";}
		$departs="$departs\t\t<option value='$pcrcs_id_Sorted[$i]' $sel>$pcrcs_departamento_Sorted[$i]</option>\n";
	$i++;
	}
?>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="../js/tableExport.js"></script>
<script type="text/javascript" src="../js/jquery.base64.js"></script>

<? include("../common/menu.php"); ?>


<table width='100%' class='t2'><form method='post' action='<? $_SERVER['PHP_SELF']; ?>'>
	<tr class='title'>
		<th colspan=100>Dimensionamiento por fecha (nivel intraday)</th>
	</tr>
	<tr class='title'>
		<td>Departamento: </td>
		<td class='pair'><select name='dept' required><? echo $departs; ?></select></td>
		<td>Date Start: </td>
		<td class='pair'><input type='date' name='inicio' value='<? echo $inicio; ?>' required></td>
		<td>Date End: </td>
		<td class='pair'><input type='date' name='fin' value='<? echo $fin; ?>' required></td>
		<td>Forecast Fact(%): </td>
		<td class='pair'><input type='text' name='fcast' value='<? echo $factor_forecast; ?>' size=3 required></td>
        <td rowspan=2>Calls: <br><input type='text' name='calls' value='' size=3 required></td>
	</tr>
	<tr class='title'>
		<td>AHT (seg): </td>
		<td class='odd'><input type='text' name='aht' value='<? echo $aht; ?>' size=3 required></td>
		<td>SL Required (/100): </td>
		<td class='odd'><input type='number' name='slr' value='<? echo $slr; ?>' min=0 max=1 step=0.05 required></td>
		<td>Target Time (seg): </td>
		<td class='odd'><input type='number' name='tat' value='<? echo $tat; ?>' min=0 max=100 step=5 required></td>
		<td>Reductores(%): </td>
		<td class='pair'><input type='text' name='red' value='<? echo $reductores; ?>' size=3 required></td>
		
	</tr>
	<tr>
		<td colspan=100 class='total'><input type='submit' name='consulta' value='Consultar'></td>
	</tr>
</form></table>
<br>
<a href="#" onClick ="$('#forecast').tableExport({type:'excel',escape:'false'});">Export to Excel</a> 

<div id='data'>
<table id='forecast' width='100%' class='t2'>

	<tr class='title'>
		
		<th rowspan=2>Hora</th>
		<? $i=0;
		while($i<$num2-1){
			echo "\t\t<th>".$dwt[$i]."</th>\n";
		$i++;
		}
		?>
		
		
		
	</tr>
	<tr class='title'>
		
		
		<? $i=0;
		while($i<$num2-1){
			echo "\t\t<th>$fechaok[$i]</th>\n";
			
		$i++;
		}
		?>
		
		
		
	</tr>
<? $i=1;
while($i<=48){
		if($i % 2 == 0){$class="pair";}{$class="odd";}
		echo "\t<tr class='$class'>\n";
		echo "\t\t<td class='subtitle'>$i</td>\n";
		$x=0;
		while($x<$num2-1){
			$dia=$dwt[$x+1]-2;
			if($dia<0){$dia=6;}
				
			echo "\t\t<td>".intval(agentno(intval($calls[$dia][$i]*($total[$x+1])*$factor_forecast)/1800*$aht,$tat,$aht,$slr))."</td>\n";
            echo "\t\t<td>".intval($calls[$dia][$i]*($total[$x+1])*$factor_forecast)."</td>\n";
		$x++;
		}
		
		
		echo "\t<tr>\n";
	
$i++;
} ?>
</table></div>
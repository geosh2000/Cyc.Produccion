<?
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="schedules_query";


include("../DBPcrcs.php");
include("../connectDB.php");

//Declare Variables
	$dep=$_POST['depto'];
	$fstart=$_POST['fstart'];
	$fend=$_POST['fend'];
	$datestart=strtotime($fstart);
	$dateend=strtotime($fend);
	
if($fstart==NULL || $fend==NULL){
 $fstart=date('Y-m-d');
 $fend=date('Y-m-d');
 $datestart=strtotime($fstart);
$dateend=strtotime($fend);
}


//queries
if(isset($_POST['consulta'])){
	//Validate Dates
	if(intval(date('U',$datestart)>intval(date('U',$dateend)))){
		$error=1;
		
		
	}
	$query="SELECT DISTINCT id, `N Corto`, `Esquema` FROM `Asesores`
    WHERE `id Departamento`=$dep AND `Activo`=1 ORDER BY `N Corto`";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$i=0;
	while($i<$num){
		$asesor[$i]=mysql_result($result,$i,'id');
		$Nasesor[$i]=mysql_result($result,$i,'N Corto');
		$esquema[$i]=mysql_result($result,$i,'Esquema');
	$i++;
	}

	foreach($asesor as $key => $asesorOK){
		$query="SELECT * FROM `Historial Programacion`
LEFT JOIN `Asesores` ON `Historial Programacion`.asesor =`Asesores`.id
WHERE `Asesores`.`id Departamento`=$dep AND `Historial Programacion`.asesor='$asesorOK' AND `Historial Programacion`.Fecha >= '".date('Y-m-d',$datestart)."' AND `Historial Programacion`.Fecha <= '".date('Y-m-d',$dateend)."' ORDER BY `Historial Programacion`.`Fecha`";
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		$i=0;
		while($i<$num){
			$date[$i]=mysql_result($result,$i,'Fecha');
			$jornada[$key][$i]=substr(mysql_result($result,$i,'jornada start'),0,5)." - ".substr(mysql_result($result,$i,'jornada end'),0,5);
			$comida[$key][$i]=substr(mysql_result($result,$i,'comida start'),0,5)." - ".substr(mysql_result($result,$i,'comida end'),0,5);
			if($jornada[$key][$i]=='00:00 - 00:00'){$jornada[$key][$i]='Descanso';}
			if($comida[$key][$i]=='00:00 - 00:00'){$comida[$key][$i]='NA';}
		$i++;
		}
	}
	
$days=count($date);
}

//List para departamentos
	$i=0;
	$departs="<option value'' selected>Selecciona...</option>";
	while($i<$pcrcs_num){
		if($pcrcs_id_Sorted[$i]==$dep){$sel="selected";}else{$sel="";}
		$departs="$departs<option value='$pcrcs_id_Sorted[$i]' $sel>$pcrcs_departamento_Sorted[$i]</option>\n";
	$i++;
	}

?>
<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.0/js/dataTables.fixedColumns.min.js"></script>
<script>
	$(document).ready(function() {
	    $('#horarios').DataTable( {
	        scrollY:        800,
	        scrollX:        true,
	        scrollCollapse: true,
	        paging:         false,
	        fixedColumns:   true,
	        fixedHeader: true
	    } );
	} );
</script>

<style>
th, td { white-space: nowrap; }
</style>

<? include("../common/menu.php"); 
?>
<table style='width:100%' class='t2'>
	<tr class='title'><form name='depselect' method='post' action='<? $_SERVER['PHP_SELF']; ?>'>
		
		<th colspan=4>Consulta de Horarios</th>
	</tr>
	<tr class='subtitle'>
		<td style='width:30%'>Departamento:     <select name='depto' required><? echo $departs; ?></select></td>
		<td style='width:30%'>Inicio:     <input type='date' name='fstart' value='<? echo date('Y-m-d', $datestart); ?>'required></td>
		<td style='width:30%'>Fin:     <input type='date' name='fend' value='<? echo date('Y-m-d', $dateend); ?>' required></td>
		<td style='width:10%' class='total'><input type='submit' value='Consultar' name='consulta'></td>
	</tr></form>
	
</table>
<br><br>
<? if($error==1){ echo"Invalid Dates Selected";  goto EndPage;} ?>
<table id='horarios' class='t2' cellspacing="0" width='100%'>
	<thead>
	<tr  class='title'>
		<td>Asesor</td>
		<td>Esquema</td>
	<? 
		$i=0;
		while($i<$days){
			echo "<td>".date('l',strtotime($date[$i]))."<br>".date('d/F/Y',strtotime($date[$i]))."</td>";
		$i++;
		}
	?>
	</tr>
	</thead>
	<tbody>
	<? foreach($Nasesor as $keyid => $nameid){
		if($keyid % 2 == 0){$class="pair";}else{$class="odd";}
		echo "\t\t<tr class='$class'>\n
			\t\t\t<td class='title'>$nameid</td>\n
			\t\t\t<td class='pair'>$esquema[$keyid]</td>\n";
		
			$i=0;
			while($i<$days){
				echo "\t\t\t\t<td>".$jornada[$keyid][$i]."</td>\n";
				
			$i++;
			}
		
		
		
		echo "\t\t</tr>\n";
	}?>
	</tbody>
</table>


<? EndPage: ?>
</div>
</div></div>
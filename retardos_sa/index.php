<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="retardos";
$menu_programaciones="class='active'";
include("../common/list_asesores.php");
include("../common/scripts.php");

//Get Variables
$dep=$_POST['dep'];
$date=$_POST['date'];
$fecha_ok=date('Y-m-d',strtotime($date));


//Query
if(isset($_POST['consulta'])){
	//Validate Horario de Verano
	if(date('I',strtotime($date))==1){$jdif="'-00:00:00'"; $_jdif="'00:00:00'";}else{$jdif="'-01:00:00'";$_jdif="'23:00:00'";}
	
	$query="SELECT
	Asesores.id, `N Corto`, Fechas.Fecha,
	if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,if(`jornada start`<'01:00:00',ADDTIME(`jornada end`,$_jdif),ADDTIME(`jornada start`,$jdif))) as jornada_start,
	if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,if(`jornada end`<'01:00:00',ADDTIME(`jornada end`,$_jdif),ADDTIME(`jornada end`,$jdif))) as jornada_end,
	LogAsesor('$fecha_ok',t_Sesiones.asesor,'in') as Login,
	LogAsesor('$fecha_ok',t_Sesiones.asesor,'out') as Logout,
	TIMEDIFF(LogAsesor('$fecha_ok',t_Sesiones.asesor,'in'),if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,if(`jornada start`<'01:00:00',ADDTIME(`jornada end`,$_jdif),ADDTIME(`jornada start`,$jdif)))) as 'Dif',
	TIMEDIFF(if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,if(`jornada end`<'01:00:00',ADDTIME(`jornada end`,$_jdif),ADDTIME(`jornada end`,$jdif))),LogAsesor('$fecha_ok',t_Sesiones.asesor,'out')) as 'DifOut',
	`Historial Programacion`.id as hid
	FROM
		Asesores
	JOIN
		Fechas
	LEFT JOIN
		`Historial Programacion`
	ON
		Asesores.id=`Historial Programacion`.asesor AND
		Fechas.Fecha=`Historial Programacion`.Fecha
	LEFT JOIN
		t_Sesiones
	ON
		`Historial Programacion`.asesor=t_Sesiones.asesor AND
		`Historial Programacion`.Fecha=t_Sesiones.Fecha_in
	WHERE
		Fechas.Fecha='$fecha_ok' AND
		Activo=1 AND
        `id Departamento`='$dep'
	GROUP BY
		Asesores.id";

	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$i=0;
	while($i<$num){
		$q_id[$i]=mysql_result($result,$i,'id');
		$q_name[$i]=mysql_result($result,$i,'N Corto');
		$q_date[$i]=mysql_result($result,$i,'Fecha');
		$q_jstart[$i]=mysql_result($result,$i,'jornada_start');
		$q_login[$i]=mysql_result($result,$i,'Login');
		$q_dif_li[$i]=mysql_result($result,$i,'Dif');
		$q_jend[$i]=mysql_result($result,$i,'jornada_end');
		$q_logout[$i]=mysql_result($result,$i,'Logout');
		$q_dif_lo[$i]=mysql_result($result,$i,'DifOut');
		$q_hid[$i]=mysql_result($result,$i,'hid');
		
	$i++;
	}

}


//options

function options_retardo(){
	echo "\t<option value=''>Selecciona...</option>\n";
	echo "\t<option value='3'>Justificado</option>\n";
	echo "\t<option value='4'>Regresado</option>\n";
	echo "\t<option value='5'>Error de Captura</option>\n";
	echo "\t<option value='8'>Especial</option>\n";
	echo "\t<option value='6'>Eliminar Excepcion</option>\n";
	
}

function options_sa(){
	echo "\t<option value=''>Selecciona...</option>\n";
	echo "\t<option value='3'>Justificado</option>\n";
	
	echo "\t<option value='5'>Error de Captura</option>\n";
	echo "\t<option value='8'>Especial</option>\n";
	echo "\t<option value='6'>Eliminar Excepcion</option>\n";
	
}
?>
<?php if($_SESSION['monitor_pya_exceptions']!=1){ goto NoScript; } ?>
<script>
function updateStatus(a,b,c,d,e) {
var str=a;
var id=b;
var div=c;
var hid=d;
var sa=e;

if(str==3 || str==8){
	var caso = prompt("Please enter the case number", "");
    if (caso == null){return;}
    if (caso == "") {
        
        alert("You must enter a valid case number for this exception. No changes applied");
        return;
    }else{
    caso=caso;
    
    }
}

	
    if (str == "") {
        document.getElementById(div).innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById(div).innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","../pya-monitor/exceptions.php?excep="+str+"&asesor="+id+"&h="+hid+"&caso1="+caso+"&sa="+sa,true);
        xmlhttp.send();
       
        
        
    }
}
</script>
<?php NoScript: ?>
<?php include("../common/menu.php"); ?>
<table width='100%' class='t2'><form name='parametros' action='<?php $_SERVER['PHP SELF']; ?>' method='post'>
	<tr class='title'>
		<th colspan=100>Reporte de Retardos y Salidas Anticipadas</th>
		
	</tr>
	<tr class='title'>
		<td>Departamento:</td>
		<td class='pair'><select name='dep' required><?php list_departamentos($dep); ?></selected></td>
		<td>Fecha:</td>
		<td class='pair'><input type='date' name='date' value='<?php echo $date; ?>' required></td>
		<td class='total'><input type='submit' name='consulta' value='consulta'></td>
	</tr>
</form></table>
<?php if(!isset($_POST['consulta'])){ exit; } ?>
<br><br>
<table width='100%' class='t2'>
	<tr class='title'>
		<th width='4%'>id</th>
		<th width='12%'>Asesor</th>
		<th width='12%'>Fecha</th>
		<th width='12%'>Hora de Entrada</th>
		<th width='12%'>Login</th>
		<th width='12%'>Anotacion</th>
		<th width='12%'>Hora de Salida</th>
		<th width='12%'>Logout</th>
		<th width='12%'>Anotacion</th>
	</tr>
	<?php $i=0; $x=0; $y=1;
	while($i<$num){
		$class_login="class='rojo'";
		$class_logout="class='rojo'";
		if($x % 2 == 0){$class='pair';}else{$class='odd';}
		
        if(date('H:i:s',strtotime($q_dif_li[$i]))>date('H:i:s',strtotime('00:00:59'))){$anot_login= "Retardo<br>".date('H:i:s',strtotime($q_dif_li[$i]));}else{$anot_login="ok"; $class_login="class='green'";}
		if(date('H:i:s',strtotime($q_dif_lo[$i]))>date('H:i:s',strtotime('00:00:00'))){$anot_logout= "S. Anticipada<br>".date('H:i:s',strtotime($q_dif_lo[$i]));}else{$anot_logout="ok"; $class_logout="class='green'";}
		
		//chec Exceptions
		$query="SELECT b.Excepcion as tipo FROM PyA_Exceptions a, `Tipos Excepciones` b WHERE a.tipo=exc_type_id AND a.asesor='$q_id[$i]' AND a.horario_id='$q_hid[$i]' AND a.SalidaAnticipada='0'";
		$result2=mysql_query($query);
		$num2=mysql_numrows($result2);
		if($num2>0){$exc_li=mysql_result($result2,0,'tipo'); $class_login="class='orange'";}else{$exc_li="";}
		$query="SELECT b.Excepcion as tipo FROM PyA_Exceptions a, `Tipos Excepciones` b WHERE a.tipo=exc_type_id AND a.asesor='$q_id[$i]' AND a.horario_id='$q_hid[$i]' AND a.SalidaAnticipada='1'";
		$result2=mysql_query($query);
		$num2=mysql_numrows($result2);
		if($num2>0){$exc_lo=mysql_result($result2,0,'tipo'); $class_logout="class='orange'";}else{$exc_lo="";}
		
		if($anot_login!="ok" || $anot_logout!="ok"){
			echo "\t<tr class='$class'>\n";
				echo "\t\t<td>$q_id[$i]</td>\n";
				echo "\t\t<td>$q_name[$i]</td>\n";
				echo "\t\t<td>$q_date[$i]</td>\n";
				echo "\t\t<td>$q_jstart[$i]</td>\n";
				echo "\t\t<td>$q_login[$i]</td>\n";
				echo "\t\t<td $class_login>$anot_login";
					echo "<br>$exc_li<x id='$y'></x><z class='disp2'><select name='retardo' onchange='updateStatus(this.value,$q_id[$i],$y,$q_hid[$i],0);'>";
					if($_SESSION['monitor_pya_exceptions']==1){options_retardo();}
					echo "</select></z>";
					$y++;
				echo "</td>\n";
				echo "\t\t<td>$q_jend[$i]</td>\n";
				echo "\t\t<td>$q_logout[$i]</td>\n";
				echo "\t\t<td $class_logout>$anot_logout";
					echo "<br>$exc_lo<x id='$y'></x><z class='disp2'><select name='sa' onchange='updateStatus(this.value,$q_id[$i],$y,$q_hid[$i],1);'>";
					if($_SESSION['monitor_pya_exceptions']==1){options_sa();}
					echo "</select></z>";
					$y++;
				echo "</td>\n";
				
			echo "\t</tr>";
			$x++;
		}
	$i++;
	}
	?>
</table>
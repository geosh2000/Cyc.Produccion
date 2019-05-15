<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$cun_time = new DateTimeZone('America/Bogota');

$asesor1=$_POST['asesor1'];
$asesor2=$_POST['asesor2'];
$Fecha1=date('Y-m-d',strtotime($_POST['fecha1']));
$Fecha2=date('Y-m-d',strtotime($_POST['fecha2']));

if(isset($_GET['asesor1'])){
	$asesor1=$_GET['asesor1'];
	$asesor2=$_GET['asesor2'];
	$Fecha1=date('Y-m-d',strtotime($_GET['fecha1']));
	$Fecha2=date('Y-m-d',strtotime($_GET['fecha2']));
}

$query="SELECT 
			a.id, a.asesor, `N Corto` as Nombre, Esquema, Fecha, 
			`jornada start` as js, `jornada end` as je,  
			`comida start` as cs, `comida end` as ce,  
			`extra1 start` as x1s, `extra1 end` as x1e,  
			`extra2 start` as x2s, `extra2 end` as x2e,
			getAusentismo(a.asesor,Fecha,1) as Ausentismo,
			CONCAT('Folio: ',c.id,' // ',motivo) as restriccion,
			IF(motivo IS NOT NULL,'Si','No') as restringido,
			cambios
		FROM
			`Historial Programacion` a
		LEFT JOIN 
			Asesores b ON a.asesor=b.id
		LEFT JOIN
			Sanciones c ON a.asesor=c.asesor AND 
			(('$Fecha1' BETWEEN fecha_afectacion_inicio AND fecha_afectacion_fin) OR
			('$Fecha2' BETWEEN fecha_afectacion_inicio AND fecha_afectacion_fin))
		LEFT JOIN
			(
				SELECT id_asesor, COUNT(DISTINCT caso) as cambios FROM `Cambios de Turno` WHERE id_asesor IN ($asesor1, $asesor2) AND tipo IN (1,2) AND MONTH(Fecha) = MONTH('$Fecha1') GROUP BY id_asesor
			) d ON a.asesor=id_asesor
		WHERE
			(Fecha IN ('$Fecha1', '$Fecha2') AND a.asesor=$asesor1) OR
			(Fecha IN ('$Fecha1', '$Fecha2') AND a.asesor=$asesor2) 
			ORDER BY Fecha";
			//echo "$query";
if($result=$connectdb->query($query)){
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_row()){
		for($i=0;$i<$result->field_count;$i++){
			switch($fields[$i]->name){
				case 'js':
				case 'je':
				case 'cs':
				case 'ce':
				case 'x1s':
				case 'x1e':
				case 'x2s':
				case 'x2e':
					$time = new DateTime(date('Y-m-d', strtotime($fila[4])).' '.$fila[$i].' America/Mexico_City');
					$time -> setTimezone($cun_time);
					$datos = $time->format('H:i');
					unset($time);
					break;
				default:
					$datos=utf8_encode($fila[$i]);
					break;
			}
			$data[$fila[1]][$fila[4]][$fields[$i]->name]=$datos;
			//$data[$fila[1]][$fila[4]][$fields[$i]->name]=$datos;
		}
	}
}else{
	$data['error']=$connectdb->error;
}

print json_encode($data,JSON_PRETTY_PRINT);

$connectdb->close();
<?php
include_once("../modules/modules.php");

timeAndRegion::setRegion('Mex');

$connectdbcc=Connection::mysqliDB('WFM');
$connectdb=Connection::mysqliDB('CC');

$fecha=date('Y-m-d', strtotime($_POST['fecha']));

if(isset($_GET['fecha'])){
	$fecha=date('Y-m-d', strtotime($_GET['fecha']));
}

if(date('I',strtotime($fecha))==0){
	$ajuste_cuntz=2;
}else{
	$ajuste_cuntz=0;
}

//Error Handler

function divError(){
 echo "";
}
set_error_handler("divError");

if(date('Y-m-d')==date('Y-m-d', strtotime($fecha))){
	$query="SELECT
				Hora_int, d.Skill, sla20, sla30, Calls, AHT
			FROM
				ccexporter.HoraGroup_Table a
			JOIN
				(SELECT DISTINCT Skill FROM ccexporter.Cola_Skill) d
			LEFT JOIN
				(
					SELECT
						b.Skill,
						HOUR(Hora)*2 + IF(MINUTE(Hora)>=30,1,0) as HoraG,
						COUNT(IF(Answered=1 AND TIME_TO_SEC(Wait<=20),a.id,NULL)) as sla20,
						COUNT(IF(Answered=1 AND TIME_TO_SEC(Wait<=30),a.id,NULL)) as sla30,
						COUNT(*) as Calls,
						AVG(IF(Answered=1,Duracion,NULL)) as AHT
					FROM
						ccexporter.mon_calls_details a
					LEFT JOIN
						ccexporter.Cola_Skill b ON a.Cola=b.Cola
					WHERE
						Fecha='$fecha'
					GROUP BY
						HoraG, Skill
				) b
			ON a.Hora_int=b.HoraG AND d.Skill=b.Skill
            ORDER BY
                d.Skill, a.Hora_int";
	if($result=$connectdbcc->query($query)){
		while($fila=$result->fetch_assoc()){

				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['sla20']=intval($fila['sla20']);
				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['sla30']=intval($fila['sla30']);
				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['calls']=intval($fila['Calls']);
				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['aht']=intval($fila['AHT']);

		}
	}
	unset($result);
}else{
	$query="SELECT
				Hora_int, Skill, sla20, sla30, Calls, AHT
			FROM
				HoraGroup_Table a
			LEFT JOIN
				(
					SELECT
						b.Skill,
						HOUR(Hora)*2 + IF(MINUTE(Hora)>=30,1,0) as HoraG,
						COUNT(IF(Answered=1 AND TIME_TO_SEC(Espera<=20),ac_id,NULL)) as sla20,
						COUNT(IF(Answered=1 AND TIME_TO_SEC(Espera<=30),ac_id,NULL)) as sla30,
						COUNT(*) as Calls,
						AVG(IF(Answered=1,TIME_TO_SEC(Duracion_Real),NULL)) as AHT
					FROM
						t_Answered_Calls a
					LEFT JOIN
						Cola_Skill b ON a.Cola=b.Cola
					WHERE
						Fecha='$fecha'
					GROUP BY
						HoraG, Skill
				) b
			ON a.Hora_int=b.HoraG";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){

				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['sla20']=intval($fila['sla20']);
				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['sla30']=intval($fila['sla30']);
				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['calls']=intval($fila['Calls']);
				$td[$fila['Skill']][($fila['Hora_int']+$ajuste_cuntz)]['aht']=intval($fila['AHT']);

		}
	}
	unset($result);
}

$query="SELECT * FROM bitacora_base a LEFT JOIN bitacora_acciones b ON a.accion=b.id WHERE Fecha='$fecha'";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$td[$fila['skill']][($fila['intervalo']+$ajuste_cuntz)]['accion'.$fila['level']]=intval($fila['accion']);
		$td[$fila['skill']][($fila['intervalo']+$ajuste_cuntz)]['accion_name'.$fila['level']]=utf8_encode($fila['Actividad']);
		$td[$fila['skill']][($fila['intervalo']+$ajuste_cuntz)]['accion_comment'.$fila['level']]=utf8_encode($fila['comments']);
		$td[$fila['skill']][($fila['intervalo']+$ajuste_cuntz)]['indice'.$fila['level']]=utf8_encode($fila['indice']);
	}
}
unset($result);

$query="SELECT a.skill, a.hora, FLOOR(participacion*volumen) as pronostico FROM
			(SELECT * FROM forecast_participacion WHERE Fecha='$fecha') a
		LEFT JOIN
			(SELECT * FROM forecast_volume WHERE Fecha='$fecha') b
		ON a.skill=b.skill";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$td[$fila['skill']][($fila['hora']+$ajuste_cuntz)]['pronostico']=intval($fila['pronostico']);
	}
}
unset($result,$fila);

$query="SELECT `id Departamento` as skill, Hora_int as hora, COUNT(*) as asesores
		FROM (SELECT `id Departamento`, b.asesor, `jornada start` as inicio, `jornada end` as fin, Ausentismo
				FROM Asesores a LEFT JOIN `Historial Programacion` b ON a.id=b.asesor LEFT JOIN (SELECT Ausentismo, Inicio, Fin, asesor FROM Ausentismos a LEFT JOIN `Tipos Ausentismos` b ON a.tipo_ausentismo=b.id WHERE '$fecha' BETWEEN Inicio AND Fin) c ON b.asesor=c.asesor
				WHERE Fecha='$fecha' AND Activo=1 AND `jornada start`!=`jornada end` HAVING Ausentismo IS NULL) a
		LEFT JOIN
			HoraGroup_Table b ON ADDTIME(b.Hora_time,'00:15:00') BETWEEN inicio AND IF(fin<'07:00:00',ADDTIME(fin,'24:00:00'),fin)
		GROUP BY
			skill, b.Hora_int";
if($result=$connectdb->query($query)){
	$td['OK']='OK';
	$td['rows']=$result->num_rows;
	while($fila=$result->fetch_assoc()){
		$td[$fila['skill']][($fila['hora']+$ajuste_cuntz)]['programados']=intval($fila['asesores']);

	}
}else{
    $td['error']=utf8_encode($connectdb->error);
}
unset($result);

unset($td['']);


foreach($td as $skill => $info){
	for($i=0;$i<48;$i++){
		if(count($info[$i])==0){
			$td[$skill][$i]['sla20']=0;
			$td[$skill][$i]['sla30']=0;
			$td[$skill][$i]['calls']=0;
			$td[$skill][$i]['aht']="";
		}
	}
}

$connectdb->close();
$connectdbcc->close();

print json_encode($td,JSON_PRETTY_PRINT);



?>

<?php
include_once('../modules/modules.php');
include_once("../common/erlangC.php");

timeAndRegion::setRegion('Cun');


if(isset($_POST['start'])){
	$inicio=date('Y-m-d', strtotime($_POST['start']));
}else{
	$inicio=date('Y-m-d', strtotime('-7 days'));
}

if(isset($_POST['end'])){
	$fin=date('Y-m-d', strtotime($_POST['end']));
}else{
	$fin=date('Y-m-d', strtotime('-1 days'));
}

$skill=$_POST['skill'];

//is inbound?
$query="SELECT * FROM PCRCs WHERE id=$skill";
if($result=Queries::query($query)){
	$fila=$result->fetch_assoc();
	$inbound=$fila['inbound_calls'];
}

//SLAS
function getTarget($tipo,$skill,$date){

	//Set Data
	IF($skill==3 || $skill==35){
		if(date('Y-m-d',strtotime($date))>=date('Y-m-d',strtotime('2016-08-01'))){
			$slr=0.80;
			$tat=20;
		}else{
			$slr=0.80;
			$tat=20;
		}
	}else{
		$slr=0.70;
		$tat=30;
	}

	//Return Info
	switch($tipo){
		case 'slr':
			return $slr;
			break;
		case 'tat':
			return $tat;
			break;
	}
}



//QUERY
if(isset($_POST['submit'])){

	//Forecast
	$query="SELECT
					a.Fecha, hora, ROUND(volumen*participacion) as forecast, volumen, AHT, Reductores
				FROM
					forecast_volume a
				RIGHT JOIN
					forecast_participacion b ON a.Fecha=b.Fecha AND a.skill=b.skill
				WHERE
					a.skill=$skill AND
					a.Fecha BETWEEN '$inicio' AND '$fin'";
	IF($result=Queries::query($query)){
			while($fila=$result->fetch_assoc()){
				$data[$fila['Fecha']]['forecast'][$fila['hora']]=$fila['forecast'];
				$data[$fila['Fecha']]['fc_volumen']=$fila['volumen'];
				$data[$fila['Fecha']]['fc_AHT']=$fila['AHT'];
				if($fila['forecast']/1800*$fila['AHT']==0){
					$data[$fila['Fecha']]['erlang'][$fila['hora']]=0;
				}else{
					$data[$fila['Fecha']]['erlang'][$fila['hora']]=intval(agentno($fila['forecast']/1800*$fila['AHT'], getTarget('tat', $skill, $fila['Fecha']),$fila['AHT'],getTarget('slr', $skill, $fila['Fecha'])));
				}
				$data[$fila['Fecha']]['necesarios'][$fila['hora']]=intval($data[$fila['Fecha']]['erlang'][$fila['hora']]/(1-$fila['Reductores']));
				if($data[$fila['Fecha']]['necesarios'][$fila['hora']]=="" || $data[$fila['Fecha']]['necesarios'][$fila['hora']]==NULL){
					$data[$fila['Fecha']]['necesarios'][$fila['hora']]=0;
				}
			}
	}

//INBOUND vs BO
if($inbound==1){

	//Reales
	$query="SELECT
					Fecha, CONCAT(HOUR(Hora),'.',IF(MINUTE(Hora)>=30,'5','0'))*2 as horaGroup, COUNT(ac_id) as llamadas
				FROM
					t_Answered_Calls a
				LEFT JOIN
					Cola_Skill b ON a.Cola=b.Cola
				WHERE
					Fecha BETWEEN '$inicio' AND '$fin' AND
					Skill=$skill
				GROUP BY
					Fecha, horaGroup";
	IF($result=Queries::query($query)){
		while($fila=$result->fetch_assoc()){
			$data[$fila['Fecha']]['real'][$fila['horaGroup']]=$fila['llamadas'];
		}
	}

}else{

	if(date('Y-m-d', strtotime($inicio))<'2016-08-22' && date('Y-m-d', strtotime($fin))<'2016-08-22'){
		//Antes del 22 de Agosto 2016
		//Reales
		$query="SELECT
						fecha, CONCAT(HOUR(hora),'.',IF(MINUTE(hora)>=30,'5','0'))*2 as horaGroup, COUNT(bo_casos_id) as llamadas
					FROM
						bo_casos a
					LEFT JOIN
						bo_areas b ON a.area=bo_area_id
					WHERE
						fecha BETWEEN '$inicio' AND '$fin' AND
						bo_skill=$skill  AND
						status!=8
					GROUP BY
						Fecha, horaGroup";
		if($result=Queries::query($query)){
			while($fila=$result->fetch_assoc()){
				$data[$fila['Fecha']]['real'][$fila['horaGroup']]=$fila['llamadas'];
			}
		}

	}else{
		if(date('Y-m-d', strtotime($inicio))<'2016-08-22'){
			//Antes del 22 de Agosto 2016
			//Reales
			$query="SELECT
							fecha, CONCAT(HOUR(hora),'.',IF(MINUTE(hora)>=30,'5','0'))*2 as horaGroup, COUNT(bo_casos_id) as llamadas
						FROM
							bo_casos a
						LEFT JOIN
							bo_areas b ON a.area=bo_area_id
						WHERE
							fecha BETWEEN '$inicio' AND '2016-08-21' AND
							bo_skill=$skill AND
							status!=8
						GROUP BY
							Fecha, horaGroup";
			if($result=Queries::query($query)){
				while($fila=$result->fetch_assoc()){
					$data[$fila['Fecha']]['real'][$fila['horaGroup']]=$fila['llamadas'];
				}
			}

			//A partir del 22 de Agosto 2016
			//Reales
			$query="SELECT
							CAST(fecha_recepcion as DATE) as Fecha, CONCAT(HOUR(fecha_recepcion),'.',IF(MINUTE(fecha_recepcion)>=30,'5','0'))*2 as horaGroup, COUNT(*) as llamadas
						FROM
							bo_tipificacion a
						LEFT JOIN
							bo_areas b ON a.area=bo_area_id
						WHERE
							CAST(fecha_recepcion as DATE) BETWEEN '2016-08-22' AND '$fin' AND
							status!=8 AND
							bo_skill=$skill
						GROUP BY
							Fecha, horaGroup";
		if($result=Queries::query($query)){
			while($fila=$result->fetch_assoc()){
				$data[$fila['Fecha']]['real'][$fila['horaGroup']]=$fila['llamadas'];
			}
		}

		}else{
			//A partir del 22 de Agosto 2016
			//Reales
			$query="SELECT
							CAST(fecha_recepcion as DATE) as Fecha, CONCAT(HOUR(fecha_recepcion),'.',IF(MINUTE(fecha_recepcion)>=30,'5','0'))*2 as horaGroup, COUNT(*) as llamadas
						FROM
							bo_tipificacion a
						LEFT JOIN
							bo_areas b ON a.area=bo_area_id
						WHERE
							CAST(fecha_recepcion as DATE) BETWEEN '$inicio' AND '$fin' AND
							status!=8 AND
							bo_skill=$skill
						GROUP BY
							Fecha, horaGroup";
			if($result=Queries::query($query)){
				while($fila=$result->fetch_assoc()){
					$data[$fila['Fecha']]['real'][$fila['horaGroup']]=$fila['llamadas'];
				}
			}
		}
	}
}
	$query="SELECT Departamento FROM PCRCs WHERE id=$skill";
	if($result=Queries::query($query)){
		$fila=$result->fetch_assoc();
		$depart=$fila['Departamento'];
	}

	//Programaciones
		//Normal
			$query="SELECT
						Fecha, c.Hora_group*2 as horaGroup, COUNT(IF(Egreso>=Fecha,id,NULL)) as asesores
					FROM
						HoraGroup_Table c
					LEFT JOIN
						(
							SELECT
								a.id, a.asesor, Fecha, `jornada start`, IF(`jornada end` < '05:00:00' , ADDTIME(`jornada end`,'24:00:00'), `jornada end`) as `jornada end`, Egreso
							FROM
								`Historial Programacion` a
							LEFT JOIN
								Asesores b ON a.asesor=b.id
							LEFT JOIN
								Ausentismos c ON a.asesor=c.asesor AND
								a.Fecha BETWEEN c.Inicio AND c.Fin
							WHERE
								Fecha BETWEEN '$inicio' AND '$fin' AND
								`id Departamento`=$skill AND
								`jornada start`!= `jornada end` AND
								(c.tipo_ausentismo IN (30,12) OR c.tipo_ausentismo IS NULL)
							ORDER BY
								Fecha, `jornada end`
						) a ON c.Hora_time BETWEEN a.`jornada start` AND ADDTIME(`jornada end`,'-00:00:01')
					WHERE
						Fecha BETWEEN '$inicio' AND '$fin'
					GROUP BY
						Fecha, c.Hora_group";
			if($result=Queries::query($query)){
				while($fila=$result->fetch_assoc()){
					$data[$fila['Fecha']]['programados'][$fila['horaGroup']]=$fila['asesores'];
				}
			}

		//Extra 1
			$query="SELECT
						Fecha, c.Hora_group*2 as horaGroup, COUNT(*) as asesores
					FROM
						HoraGroup_Table c
					LEFT JOIN
						(
							SELECT
								a.id, a.asesor, Fecha, `extra1 start`, IF(`extra1 end` < '05:00:00' , ADDTIME(`extra1 end`,'24:00:00'), `extra1 end`) as `extra1 end`
							FROM
								`Historial Programacion` a
							LEFT JOIN
								Asesores b ON a.asesor=b.id
							LEFT JOIN
								Ausentismos c ON a.asesor=c.asesor AND
								a.Fecha BETWEEN c.Inicio AND c.Fin
							WHERE
								Fecha BETWEEN '$inicio' AND '$fin' AND
								`id Departamento`=$skill AND
								`extra1 start`!= `extra1 end` AND
								(c.tipo_ausentismo IN (30,12) OR c.tipo_ausentismo IS NULL)
							ORDER BY
								Fecha, `jornada end`
						) a ON c.Hora_time BETWEEN a.`extra1 start` AND ADDTIME(`extra1 end`,'-00:00:01')
					WHERE
						Fecha BETWEEN '$inicio' AND '$fin'
					GROUP BY
						Fecha, c.Hora_group";
		if($result=Queries::query($query)){
			while($fila=$result->fetch_assoc()){
				$data[$fila['Fecha']]['programados'][$fila['horaGroup']]=$fila['asesores'];
			}
		}

		//Extra 2
			$query="SELECT
						Fecha, c.Hora_group*2 as horaGroup, COUNT(*) as asesores
					FROM
						HoraGroup_Table c
					LEFT JOIN
						(
							SELECT
								a.id, a.asesor, Fecha, `extra2 start`, IF(`extra2 end` < '05:00:00' , ADDTIME(`extra2 end`,'24:00:00'), `extra2 end`) as `extra2 end`
							FROM
								`Historial Programacion` a
							LEFT JOIN
								Asesores b ON a.asesor=b.id
							LEFT JOIN
								Ausentismos c ON a.asesor=c.asesor AND
								a.Fecha BETWEEN c.Inicio AND c.Fin
							WHERE
								Fecha BETWEEN '$inicio' AND '$fin' AND
								`id Departamento`=$skill AND
								`extra2 start`!= `extra2 end` AND
								(c.tipo_ausentismo IN (30,12) OR c.tipo_ausentismo IS NULL)
							ORDER BY
								Fecha, `jornada end`
						) a ON c.Hora_time BETWEEN a.`extra2 start` AND ADDTIME(`extra2 end`,'-00:00:01')
					WHERE
						Fecha BETWEEN '$inicio' AND '$fin'
					GROUP BY
						Fecha, c.Hora_group";
			if($result=Queries::query($query)){
				while($fila=$result->fetch_assoc()){
					$data[$fila['Fecha']]['programados'][$fila['horaGroup']]=$fila['asesores'];
				}
			}

		//Cumplimiento Programacion
			$query="SELECT
						Fecha, c.Hora_group*2 as horaGroup, COUNT(*) as asesores
					FROM
						HoraGroup_Table c
					LEFT JOIN
						(
							SELECT
								a.sesiones_id, a.asesor, a.Fecha_in as Fecha, LogAsesor(a.Fecha_in,a.asesor,'in') as 'inicio', IF(LogAsesor(a.Fecha_in,a.asesor,'out') < '05:00:00' , ADDTIME(LogAsesor(a.Fecha_in,a.asesor,'out'),'24:00:00'), LogAsesor(a.Fecha_in,a.asesor,'out')) as `fin`
							FROM
								t_Sesiones a
							LEFT JOIN
								Asesores b ON a.asesor=b.id
							WHERE
								a.Fecha_in BETWEEN '$inicio' AND '$fin' AND
								`id Departamento`=$skill
							GROUP BY a.Fecha_in, a.asesor
							ORDER BY
								Fecha, `fin`
						) a ON c.Hora_time BETWEEN ADDTIME(a.`inicio`,'00:11:00') AND ADDTIME(`fin`,'-00:13:00')
					WHERE
						Fecha BETWEEN '$inicio' AND '$fin'
					GROUP BY
						Fecha, c.Hora_group";
			if($result=Queries::query($query)){
				while($fila=$result->fetch_assoc()){
					$data[$fila['Fecha']]['sentados'][$fila['horaGroup']]=$fila['asesores'];
				}
			}

}
?>

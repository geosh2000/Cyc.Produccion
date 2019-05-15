<?php
include("../connectDB.php");
header("Content-Type:  application/json;charset=utf-8");

//Build Info

$from=date('Y-m-d',strtotime($_GET['from']));
$to=date('Y-m-d',strtotime($_GET['to']));
$skill=$_GET['skill'];

switch($skill){
	case '37':
	case '38':
	case '39':
	case '40':
		$dep=6;
		break;
	default:
		$dep=$skill;
		break;
}

$query="SELECT
        id, Asesor, FindSuperDay(DAY(MAX(Fecha)),MONTH(MAX(Fecha)),YEAR(MAX(Fecha)),id) as Supervisor, Esquema, Departamento,
        MIN(Fecha) as Fecha_inicio, MAX(Fecha) as Fecha_fin,
        SUM(Duracion_Sesion)/60 as Duracion_Sesion,
        SUM(PNP)/60 as Pausas_No_Productivas, SUM(PP)/60 as Pausas_Productivas,
        (1-((SUM(PNP)/60)/(SUM(Duracion_Sesion)/60)))*100 as Utilizacion,
        AVG(Adherence)*100 as Adherencia, CAST(AVG(Retardos) as DECIMAL) as Retardos, CAST(AVG(Faltas) as DECIMAL) as Faltas
	FROM
		(
			SELECT
				id, `Nombre` as Asesor, Esquema, `id Departamento` as Departamento
			FROM
				Asesores
			WHERE
				Activo=1 AND
				`id Departamento`=$dep
		) Asesores
	JOIN
        (
			SELECT
				Fecha, Dolar
			FROM
				Fechas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
		) Fechas
	LEFT JOIN
		(
			SELECT
				Fecha_in as Sesiones_Fecha, asesor as Sesiones_asesor, sum(TIME_TO_SEC(Duracion)) as Duracion_Sesion, Skill as Sesiones_Skill
			FROM
				t_Sesiones
			WHERE
				Fecha_in BETWEEN '$from' AND '$to'
			GROUP BY
			Fecha_in, asesor
		)	Sesiones
	ON
		Fechas.Fecha=Sesiones.Sesiones_Fecha AND
		Asesores.id=Sesiones.Sesiones_asesor
	LEFT JOIN
		(
			SELECT
				asesor as Pausas_asesor, Fecha as Pausas_Fecha,
                sum(TIME_TO_SEC(if(codigo!=10 AND codigo!=0,Duracion,NULL))) as PNP,
                sum(TIME_TO_SEC(if(codigo=10 OR codigo=0,Duracion,NULL))) as PP,  Skill as Pausas_Skill
			FROM
				t_pausas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha, asesor
		) Pausas
	ON
		Fechas.Fecha=Pausas_Fecha AND
		Asesores.id=Pausas_asesor
	LEFT JOIN
		(
			SELECT
				id as Adherencia_id, Fecha as Adherencia_Fecha, SUM(time_adh)/SUM(Duracion_jornada) as Adherence, SUM(Retardo)-IF(RJ IS NULL,0,RJ) as Retardos, SUM(if(Duracion_jornada=0,0,if(time_adh=0,1,0))) as Faltas
			FROM
			(
				SELECT
					a1.id, Fecha, if(adherencia=1,0,time_adh) as time_adh, if(adherencia=1,0,Duracion_jornada) as Duracion_jornada, tipo_ausentismo, adherencia, Retardo
				FROM
					(
						SELECT
							id, Fecha,
							TIME_TO_SEC(
								TIMEDIFF(
									if(Logout IS NULL,
										0,
										if(Logout<=
											if(jornada_start='00:00:00' AND jornada_end='00:00:00',jornada_end,
												if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',
												ADDTIME(jornada_end,'24:00:00'),jornada_end)
											)
											,if(jornada_start='00:00:00' AND jornada_end='00:00:00',Logout,
												if(Logout>='00:00:00' AND Logout<='05:00:00',
												ADDTIME(Logout,'24:00:00'),
												Logout)
											)
										,if(jornada_start='00:00:00' AND jornada_end='00:00:00',
											jornada_end,
											if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',
												ADDTIME(jornada_end,'24:00:00'),
												jornada_end)
										)
										)
									),
								if(Login IS NULL,
									0,
									if(Login>=ADDTIME(jornada_start,'00:01:00'),
										Login,
										jornada_start)
								)
								)
							) as time_adh,
							if(jornada_start='00:00:00' AND jornada_end='00:00:00',0,if(Login IS NULL,
									0,
									if(Login>=jornada_start,
										1,
										0)
								)) as Retardo,
							TIME_TO_SEC(TIMEDIFF(if(jornada_start='00:00:00' AND jornada_end='00:00:00',jornada_end,if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',ADDTIME(jornada_end,'24:00:00'),jornada_end)),jornada_start)) as Duracion_jornada
							FROM
								(
									SELECT
										`Historial Programacion`.asesor as id, Fechas.Fecha,
										CASE
											WHEN (`jornada start`='00:00:00' AND `jornada end`='00:00:00') THEN `jornada start`
											WHEN (`jornada start`<'01:00:00') THEN ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00'))
											ELSE ADDTIME(`jornada start`,if(Fechas.Verano=0,'-01:00:00','00:00:00'))
										END as jornada_start,
										CASE
											WHEN (`jornada start`='00:00:00' AND `jornada end`='00:00:00') THEN `jornada end`
											WHEN (`jornada end`<'01:00:00') THEN ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00'))
											ELSE ADDTIME(`jornada end`,if(Fechas.Verano=0,'-01:00:00','00:00:00'))
										END as jornada_end,
										CASE
											WHEN (`jornada start`='00:00:00' AND `jornada end`='00:00:00') THEN `jornada start`
											ELSE LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'in')
										END as Login,
										CASE
											WHEN (`jornada start`='00:00:00' AND `jornada end`='00:00:00') THEN `jornada end`
											ELSE LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'out')
										END as Logout
									FROM
										Fechas
									LEFT JOIN
										`Historial Programacion`
									ON
										Fechas.Fecha=`Historial Programacion`.Fecha
									LEFT JOIN
										t_Sesiones
									ON
										`Historial Programacion`.asesor=t_Sesiones.asesor AND
										Fechas.Fecha=t_Sesiones.Fecha_in
									WHERE
										Fechas.Fecha BETWEEN '$from' AND '$to'
									GROUP BY
										`Historial Programacion`.asesor, Fechas.Fecha
								) as Jornadas
						GROUP BY
								id, Fecha
					) as a1
			LEFT JOIN

			(
				SELECT
					asesor,tipo_ausentismo, Inicio, Fin, adherencia
				FROM
					Ausentismos a,
					`Tipos Ausentismos` c
				WHERE
					a.tipo_ausentismo=c.id AND
					(
						Inicio BETWEEN '$from' AND '$to' OR
						Fin BETWEEN '$from' AND '$to'
					)
			) as b1
			ON
				a1.id=b1.asesor AND
				a1.Fecha BETWEEN Inicio AND Fin
		) as Adherencia
			LEFT JOIN
				(
					SELECT
						Fecha as RJS_Fecha, a.asesor, COUNT(if(tipo=3 OR tipo=8,tipo,NULL)) as RJ
					FROM
						PyA_Exceptions a,
						`Historial Programacion` c
					WHERE
						a.horario_id=c.id AND
						Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						a.asesor,Fecha
				) RJS
			ON
				Adherencia.Fecha=RJS_Fecha AND
				Adherencia.id=RJS.asesor
			GROUP BY
		   	id
		) Adherencia
	ON
		Fechas.Fecha=Adherencia_Fecha AND
		Asesores.id=Adherencia_id
	
	GROUP BY
		Asesor
	ORDER BY
		Asesores.Asesor";

$x=0;
if ($result=$connectdb->query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
			switch($info_field[$i]->type){
				case 246:
					$data[$fila[0]][]=number_format($fila[$i],2);
					break;
				default:
					$data[$fila[0]][]=utf8_encode($fila[$i]);
					break;
			}
		}
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

for($i=0;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$info_field[$i]->name));
}

unset($result);

//Query por Area
	
	//BO
	$query="SELECT
		asesor_id, status, COUNT(*) as Registros
	FROM
		(
			SELECT 
				c.id as asesor_id, a.id, b.area, Nombre, fecha_recepcion, em, localizador, d.`status`, date_created, internal_id 
			FROM 
				bo_tipificacion a 
			LEFT JOIN 
				bo_areas b ON a.area=b.bo_area_id
			LEFT JOIN
				Asesores c ON a.asesor=c.id
			LEFT JOIN
				bo_status d ON a.status=d.id
			WHERE 
				CAST(a.fecha_recepcion as DATE) BETWEEN '$from' AND '$to' AND 
				bo_skill='$skill'
		) base
	GROUP BY
		Nombre, status";
		
	if ($result=$connectdb->query($query)) {
		$info_field_act=$result->fetch_fields();
	   while ($fila = $result->fetch_row()) {
			for($i=2;$i<$result->field_count;$i++){
				switch($info_field_act[$i]->type){
					default:
						$act[$fila[0]][$fila[1]]=utf8_encode($fila[$i]);
						$status[$fila[1]]=ucwords(str_replace("_"," ",$fila[1]));
						break;
				}
			}
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);
	
	
	//Add titles to tableheaders
	foreach($status as $title => $name){
		$dataheaders[]=$name;
	}
	
	//re-organize data for table
	foreach($act as $id => $info){
		foreach($status as $title => $name){
			$data[$id][]=$info[$title];
		}	
	}
	
	//Puntualidad
	$query="SELECT
			asesor_id, COUNT(*) as casos_totales, COUNT(IF(Dias=0,asesor_id,NULL)) as Casos_puntuales,
			FORMAT(COUNT(IF(Dias=0,asesor_id,NULL))/COUNT(*)*100,2) as Puntualidad,
			FORMAT((COUNT(IF(Dias=1,asesor_id,NULL))+COUNT(IF(Dias=2,asesor_id,NULL))*2+COUNT(IF(Dias=3,asesor_id,NULL))*3+COUNT(IF(Dias=4,asesor_id,NULL))*4+COUNT(IF(Dias=5,asesor_id,NULL))*5)/COUNT(IF(Dias>0,asesor_id,NULL)),2) as Pendientes
		FROM
			(
				SELECT 
					c.id as asesor_id, a.id, b.area, Nombre, fecha_recepcion, em, localizador, d.`status`, date_created, internal_id, TIMEDIFF(date_created,fecha_recepcion)/60 as Duracion,
					CASE
						WHEN FLOOR(TIMEDIFF(date_created,fecha_recepcion)/60/1440)<4 THEN FLOOR(TIMEDIFF(date_created,fecha_recepcion)/60/1440)
						ELSE 5
					END as Dias
				FROM 
					bo_tipificacion a 
				LEFT JOIN 
					bo_areas b ON a.area=b.bo_area_id
				LEFT JOIN
					Asesores c ON a.asesor=c.id
				LEFT JOIN
					bo_status d ON a.status=d.id
				WHERE 
					CAST(a.fecha_recepcion as DATE) BETWEEN '$from' AND '$to' AND 
					a.status!=8 AND
					bo_skill=$skill
				ORDER BY
					Duracion
			) regs
		GROUP BY asesor_id";
	
	if ($result=$connectdb->query($query)) {
		$info_field_act=$result->fetch_fields();
	   while ($fila = $result->fetch_row()) {
			for($i=1;$i<$result->field_count;$i++){
				$data[$fila[0]][]=utf8_encode($fila[$i]);
			}
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
		
	//Add titles to tableheaders
	for($i=1;$i<$result->field_count;$i++){
		$dataheaders[]=$info_field_act[$i]->name;
	}
	
	unset($result);
	
	//<---------------------------------- FIN BO ---------------------------------->

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($data as $id =>$info){
	switch($skill){
		case '37':
		case '38':
		case '39':
		case '40':
			if(isset($act[$id])){
				$row[]=$info;
			}
			break;
		default:
			$row[]=$info;
			break;
	}
}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>



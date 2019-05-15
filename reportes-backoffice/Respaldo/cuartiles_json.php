<?php
include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

//Build Info

$from=date('Y-m-d',strtotime($_GET['from']));
$to=date('Y-m-d',strtotime($_GET['to']));
$skill=$_GET['skill'];


switch($skill){
	case '37':
    $puesto=28;
		break;
	case '38':
    $puesto=29;
		break;
	case '39':
    $puesto=30;
    break;
	case '40':
		$puesto=31;
		break;
}

$query="DROP TEMPORARY TABLE IF EXISTS bo_asesores;
        DROP TEMPORARY TABLE IF EXISTS bo_sesiones;
        DROP TEMPORARY TABLE IF EXISTS bo_pausas;
        DROP TEMPORARY TABLE IF EXISTS bo_pya;

        CREATE TEMPORARY TABLE bo_asesores (SELECT
          id as asesor, c.Fecha, Nombre, Esquema, dep as Departamento, b.puesto
        FROM
          (SELECT Fecha FROM Fechas c WHERE Fecha BETWEEN '$from' AND '$to' ) c
        JOIN
          Asesores a
        LEFT JOIN
          dep_asesores b ON a.id=b.asesor AND c.Fecha=b.Fecha
        WHERE
          (Egreso>='$from')
        HAVING dep=6 AND puesto=$puesto);


        CREATE TEMPORARY TABLE bo_sesiones (SELECT
          Fecha_in as Fecha, asesor, sum(TIME_TO_SEC(Duracion)) as Sesion, Skill
        FROM
          t_Sesiones
        WHERE
          Fecha_in BETWEEN '$from' AND '$to'  AND
          Skill IN ($skill)
        GROUP BY
          Fecha_in, asesor);
          
        CREATE TEMPORARY TABLE bo_pausas (SELECT
          asesor, Fecha,
          sum(TIME_TO_SEC(if(codigo!=10 AND codigo!=0,Duracion,NULL))) as PNP,
          sum(TIME_TO_SEC(if(codigo=10 OR codigo=0,Duracion,NULL))) as PP,  Skill
        FROM
          t_pausas
        WHERE
          Fecha BETWEEN '$from' AND '$to' AND
          Skill IN ($skill)
        GROUP BY
          Fecha, asesor);
          
          
        CREATE TEMPORARY TABLE bo_pya (SELECT
            Fecha, asesor, 
            CASE
            WHEN jornada_start!=jornada_end THEN 
              CASE
                WHEN Login IS NULL AND tipo_ausentismo IS NULL THEN 'FA'
                WHEN Login IS NULL AND tipo_ausentismo IS NOT NULL AND tipo_ausentismo=15 THEN 'FA'
                WHEN ADDTIME(Login,-jornada_start) > '00:13:00' THEN 'RTB'
                WHEN ADDTIME(Login,-jornada_start) >= '00:01:00' THEN 'RTA'
                ELSE	NULL
              END
          END as Retardo
        FROM
          (
          SELECT
            b.asesor, b.Fecha,
            `jornada start` as jornada_start,
            `jornada end` as jornada_end,
            LogAsesor(b.Fecha,c.asesor,'in') as Login,
            LogAsesor(b.Fecha,c.asesor,'out') as Logout,
            tipo_ausentismo
          FROM
            bo_asesores a
          LEFT JOIN
            `Historial Programacion` b ON a.asesor=b.asesor AND a.Fecha=b.Fecha
          LEFT JOIN
            t_Sesiones c
          ON
            a.asesor=c.asesor AND
            b.Fecha=c.Fecha_in
          LEFT JOIN 
            Ausentismos d ON a.asesor=d.asesor AND b.Fecha BETWEEN Inicio AND Fin
          WHERE
            b.Fecha BETWEEN '$from' AND '$to' 
          GROUP BY
            b.asesor, b.Fecha
          ) a);";
          
        
          
$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}

$query="SELECT 
          a.asesor, a.Nombre, SUM(Sesion)/60/60 as Sesion, SUM(PNP)/60/60 as PNP, SUM(PP)/60/60 as PP, SUM(Sesion-PNP)/60/60 as Utilizacion, 
          COUNT(IF(Retardo='RTA',Retardo,NULL)) as RTA,
          COUNT(IF(Retardo='RTB',Retardo,NULL)) as RTB,
          COUNT(IF(Retardo='FA',Retardo,NULL)) as FA
        FROM 
          bo_asesores a 
        LEFT JOIN 
          bo_sesiones b ON a.Fecha=b.Fecha AND a.asesor=b.asesor
        LEFT JOIN 
          bo_pausas c ON a.Fecha=c.Fecha AND a.asesor=c.asesor
        LEFT JOIN 
          bo_pya d ON a.Fecha=d.Fecha AND a.asesor=d.asesor
        GROUP BY
          a.asesor";

$x=0;
if ($result=$connectdb->query($query)) {
  $info_field=$result->fetch_fields();
  while ($fila = $result->fetch_array()) {
    for($i=0;$i<$result->field_count;$i++){
			if($info_field[$i]->name=='Sesion'){
				$duracion[$fila[0]]=$fila[$i];
			}
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
				c.id as asesor_id, a.id, b.area, Nombre, fecha_recepcion, em, localizador, d.`status`, CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ), internal_id
			FROM
				bo_tipificacion a
			LEFT JOIN
				bo_areas b ON a.area=b.bo_area_id
			LEFT JOIN
				Asesores c ON a.asesor=c.id
			LEFT JOIN
				bo_status d ON a.status=d.id
			WHERE
				CAST(CONVERT_TZ( a.date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ) as DATE) BETWEEN '$from' AND '$to' AND
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

	//Add title Eficiencia
	$dataheaders[]='Eficiencia';

	//re-organize data for table
	foreach($act as $id => $info){
    if(isset($data[$id])){
      foreach($status as $title => $name){
        if($title == '* Fin (BO)' || $title == '* Fin (IN)' || $title == 'Escalado' || $title == 'Resuelto (BO)' || $title == 'Resuelto (IN)' || $title == 'Seguimiento Proveedor' || $title == 'Seguimiento Interno' || $title == 'Seguimiento Cliente'){
          $tmp[$id]+=$info[$title];
        }
        
        $data[$id][]=$info[$title];
      }
      //Add Value Eficiencia
      @$data[$id][]=number_format($tmp[$id]/($duracion[$id]),2);
    }
	}

	//Get Meta
	$query="SELECT meta FROM bo_puntualidad WHERE modo='tx' AND skill=$skill AND month=".date('m',strtotime($from))." AND YEAR=".date('Y',strtotime($from));
	if($resultado=$connectdb->query($query)){
		while ($fila = $resultado->fetch_assoc()) {
			$meta=$fila['meta'];
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}

	//Puntualidad
	$query="SELECT
			asesor_id, COUNT(*) as casos_totales, COUNT(IF(Dias<=$meta,asesor_id,NULL)) as Casos_puntuales,
			FORMAT(COUNT(IF(Dias<=$meta,asesor_id,NULL))/COUNT(*)*100,2) as Puntualidad, "
			."FORMAT((COUNT(IF(Dias BETWEEN 1 AND 1.99,asesor_id,NULL))+"
					."COUNT(IF(Dias BETWEEN 2 AND 2.99,asesor_id,NULL))*2+"
					."COUNT(IF(Dias BETWEEN 3 AND 3.99,asesor_id,NULL))*3+"
					."COUNT(IF(Dias BETWEEN 4 AND 4.99,asesor_id,NULL))*4+"
					."COUNT(IF(Dias BETWEEN 5 AND 5.99,asesor_id,NULL))*5+"
					."COUNT(IF(Dias BETWEEN 6 AND 6.99,asesor_id,NULL))*6+"
					."COUNT(IF(Dias BETWEEN 7 AND 7.99,asesor_id,NULL))*7+"
					."COUNT(IF(Dias BETWEEN 8 AND 8.99,asesor_id,NULL))*8+"
					."COUNT(IF(Dias BETWEEN 9 AND 9.99,asesor_id,NULL))*9+"
					."COUNT(IF(Dias BETWEEN 10 AND 10.99,asesor_id,NULL))*10+"
					."COUNT(IF(Dias BETWEEN 11 AND 11.99,asesor_id,NULL))*11+"
					."COUNT(IF(Dias>=12,asesor_id,NULL))*12)/COUNT(IF(Dias>0,asesor_id,NULL)),2) as Pendientes "
		."FROM
			(
				SELECT
					c.id as asesor_id, a.id, b.area, Nombre, fecha_recepcion, em, localizador, d.`status`, CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ), internal_id, (DATEDIFF(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ),fecha_recepcion)*24*60*60 + (TIME_TO_SEC(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ))-TIME_TO_SEC(fecha_recepcion)))/60 as Duracion,
					CASE
						WHEN (DATEDIFF(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ),fecha_recepcion)*24*60*60 + (TIME_TO_SEC(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ))-TIME_TO_SEC(fecha_recepcion)))/60/1440<=11 THEN (DATEDIFF(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ),fecha_recepcion)*24*60*60 + (TIME_TO_SEC(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ))-TIME_TO_SEC(fecha_recepcion)))/60/1440
						ELSE 12
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
					CAST(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ) as DATE) BETWEEN '$from' AND '$to' AND
					a.status NOT IN (8,26,27,28,29) AND
					bo_skill=$skill
				ORDER BY
					Duracion
			) regs
		GROUP BY asesor_id";

	if ($result=$connectdb->query($query)) {
		$info_field_act=$result->fetch_fields();
	   while ($fila = $result->fetch_row()) {
      if(isset($data[$fila[0]])){
        for($i=1;$i<$result->field_count;$i++){
          $data[$fila[0]][]=utf8_encode($fila[$i]);
        }
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
	$row[]=$info;
}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);


?>

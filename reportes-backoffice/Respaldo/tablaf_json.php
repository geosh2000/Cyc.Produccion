<?php
include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

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

//Get Meta
$query="SELECT skill, meta, modo FROM bo_puntualidad WHERE month=".date('m',strtotime($from))." AND YEAR=".date('Y',strtotime($to));
if($resultado=$connectdb->query($query)){
	while ($fila = $resultado->fetch_assoc()) {
		$meta[$fila['modo']][$fila['skill']]=$fila['meta'];
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

/*$query="SELECT "
			."area, COUNT(*) as Registros, COUNT(IF(Dias<=IF(areaid=1,".$meta['38'].",IF(areaid=2,".$meta['37'].",IF(areaid=3,".$meta['40'].",".$meta['39']."))),asesor_id,NULL)) as Casos_puntuales, "
			."FORMAT(COUNT(IF(Dias<=IF(areaid=1,".$meta['38'].",IF(areaid=2,".$meta['37'].",IF(areaid=3,".$meta['40'].",".$meta['39']."))),asesor_id,NULL))/COUNT(*)*100,2) as Puntualidad, "
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
					."COUNT(IF(Dias>=12,asesor_id,NULL))*12)/COUNT(IF(Dias>1,asesor_id,NULL)),2) as Pendientes "
		."FROM
			(
				SELECT 
					c.id as asesor_id, a.id, b.area, a.area as areaid, Nombre, fecha_recepcion, em, localizador, d.`status`, date_created, internal_id, SUM((DATEDIFF(date_created,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_created)-TIME_TO_SEC(fecha_recepcion)))/60) as Duracion,
					CASE
						WHEN SUM((DATEDIFF(date_created,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_created)-TIME_TO_SEC(fecha_recepcion)))/60/1440)<=11 THEN SUM((DATEDIFF(date_created,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_created)-TIME_TO_SEC(fecha_recepcion)))/60/1440)
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
					CAST(a.fecha_recepcion as DATE) BETWEEN '$from' AND '$to' AND 
					a.status!=8
				GROUP BY "
					//."em " //Punta a Punta
					."internal_id " //Duracion PT
				."ORDER BY
					Duracion
				
			) regs
		GROUP BY area";*/
		
$query="SELECT "
			."area, COUNT(*) as Registros_Tx, COUNT(IF(Dias<=IF(areaid=1,".$meta['tx']['38'].",IF(areaid=2,".$meta['tx']['37'].",IF(areaid=3,".$meta['tx']['40'].",".$meta['tx']['39']."))),asesor_id,NULL)) as Casos_puntuales_Tx, "
			."FORMAT(COUNT(IF(Dias<=IF(areaid=1,".$meta['tx']['38'].",IF(areaid=2,".$meta['tx']['37'].",IF(areaid=3,".$meta['tx']['40'].",".$meta['tx']['39']."))),asesor_id,NULL))/COUNT(*)*100,2) as Puntualidad_Tx, "
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
					."COUNT(IF(Dias>=12,asesor_id,NULL))*12)/COUNT(IF(Dias>1,asesor_id,NULL)),2) as Pendientes_Tx "
		."FROM
			(
				SELECT 
					c.id as asesor_id, a.id, b.area, a.area as areaid, Nombre, fecha_recepcion, em, localizador, d.`status`, date_createdOK, internal_id, (DATEDIFF(date_createdOK,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_createdOK)-TIME_TO_SEC(fecha_recepcion)))/60 as Duracion,
					CASE
						WHEN (DATEDIFF(date_createdOK,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_createdOK)-TIME_TO_SEC(fecha_recepcion)))/60/1440<=11 THEN (DATEDIFF(date_createdOK,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_createdOK)-TIME_TO_SEC(fecha_recepcion)))/60/1440
						ELSE 12
					END as Dias
				FROM 
					(SELECT id, area, asesor, fecha_recepcion as fecha_recepcion, em, localizador, status, CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ) as date_createdOK, internal_id FROM bo_tipificacion WHERE status!=8 HAVING CAST(fecha_recepcion as DATE) BETWEEN '$from' AND '$to') a 
				LEFT JOIN 
					bo_areas b ON a.area=b.bo_area_id
				LEFT JOIN
					Asesores c ON a.asesor=c.id
				LEFT JOIN
					bo_status d ON a.status=d.id
				ORDER BY
					Duracion
				
				
			) regs
		GROUP BY area";
			
if ($result=$connectdb->query($query)) {
	//echo "$query<br>";
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


$query="SELECT "
			."area, COUNT(*) as Registros_PaP, COUNT(IF(Dias<=IF(areaid=1,".$meta['pap']['38'].",IF(areaid=2,".$meta['pap']['37'].",IF(areaid=3,".$meta['pap']['40'].",".$meta['pap']['39']."))),asesor_id,NULL)) as Casos_puntuales_PaP, "
			."FORMAT(COUNT(IF(Dias<=IF(areaid=1,".$meta['pap']['38'].",IF(areaid=2,".$meta['pap']['37'].",IF(areaid=3,".$meta['pap']['40'].",".$meta['pap']['39']."))),asesor_id,NULL))/COUNT(*)*100,2) as Puntualidad_PaP, "
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
					."COUNT(IF(Dias>=12,asesor_id,NULL))*12)/COUNT(IF(Dias>1,asesor_id,NULL)),2) as Pendientes_PaP "
		."FROM
			(
				SELECT 
					c.id as asesor_id, a.id, b.area, a.area as areaid, Nombre, fecha_recepcion, em, localizador, d.`status`, date_createdOK, internal_id, SUM((DATEDIFF(date_createdOK,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_createdOK)-TIME_TO_SEC(fecha_recepcion)))/60) as Duracion,
					CASE
						WHEN SUM((DATEDIFF(date_createdOK,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_createdOK)-TIME_TO_SEC(fecha_recepcion)))/60/1440)<=11 THEN SUM((DATEDIFF(date_createdOK,fecha_recepcion)*24*60*60 + (TIME_TO_SEC(date_createdOK)-TIME_TO_SEC(fecha_recepcion)))/60/1440)
						ELSE 12
					END as Dias
				FROM 
					(SELECT id, area, asesor, fecha_recepcion as fecha_recepcion, em, localizador, status, CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ) as date_createdOK, internal_id FROM bo_tipificacion WHERE status!=8 AND CAST(fecha_recepcion as DATE) BETWEEN '$from' AND '$to' GROUP BY area, em) a 
				LEFT JOIN 
					bo_areas b ON a.area=b.bo_area_id
				LEFT JOIN
					Asesores c ON a.asesor=c.id
				LEFT JOIN
					bo_status d ON a.status=d.id
				GROUP BY em
				ORDER BY
					Duracion
				
				
			) regs
		GROUP BY area";
		//echo $query;
			
if ($result=$connectdb->query($query)) {
	//echo "$query<br>";
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=1;$i<$result->field_count;$i++){
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

for($i=1;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$info_field[$i]->name));
}

unset($result);

//Tiempo Sesiones y Eficiencia
$query="SELECT
          a.area, Sesion-Pausa as Sesion
        FROM
        (SELECT area, SUM(TIME_TO_SEC(Duracion))/60/60 as Sesion FROM t_Sesiones a LEFT JOIN bo_areas ON Skill=bo_skill WHERE a.Fecha_in BETWEEN '$from' AND '$to' AND Skill IN (37,38,39,40) GROUP BY area) a
        LEFT JOIN
        (SELECT area, SUM(TIME_TO_SEC(Duracion))/60/60 as Pausa FROM t_pausas a LEFT JOIN bo_areas ON Skill=bo_skill WHERE a.Fecha BETWEEN '$from' AND '$to' AND Skill IN (37,38,39,40) GROUP BY area) b
        ON a.area=b.area";
if($result=$connectdb->query($query)){
  if($result->num_rows>0){
  
    $dataheaders[]="Sesiones";
    $dataheaders[]="Eficiencia";
    
    while($fila=$result->fetch_assoc()){
      $data[$fila['area']][]=number_format($fila['Sesion'],2)." hrs.";
      $data[$fila['area']][]=number_format($data[$fila['area']][1]/$fila['Sesion'],2);
    }
  }
}



//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
if(count($data)>0){
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
}

$connectdb->close();

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);


?>



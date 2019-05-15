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

$query="DROP TEMPORARY TABLE IF EXISTS Q_casos;
        DROP TEMPORARY TABLE IF EXISTS Q_casosProcessed;

        CREATE TEMPORARY TABLE Q_casos SELECT
            id,
            area,
            asesor,
            fecha_recepcion AS fecha_recepcion,
            em,
            localizador,
            status,
            CONVERT_TZ(date_created,
                    '-05:00',
                    IF(GETHV(date_created) = 1,
                        '-05:00',
                        '-06:00')) AS date_createdOK,
            internal_id
        FROM
            bo_tipificacion
        WHERE
            status != 8
        HAVING CAST(fecha_recepcion AS DATE) BETWEEN '$from' AND '$to';

        DROP TEMPORARY TABLE IF EXISTS Q_casosProcessed;

        CREATE TEMPORARY TABLE Q_casosProcessed SELECT
            asesor AS asesor_id,
            a.id AS tipificacion_id,
            a.area AS area_id,
            NombreAsesor(asesor,2) as Nombre,
            fecha_recepcion,
            em,
            localizador,
            a.status as status_id,
            date_createdOK,
            internal_id,
            (DATEDIFF(date_createdOK, fecha_recepcion) * 24 * 60 * 60 + (TIME_TO_SEC(date_createdOK) - TIME_TO_SEC(fecha_recepcion))) / 60 AS Duracion,
            (DATEDIFF(date_createdOK, fecha_recepcion) * 24 * 60 * 60 + (TIME_TO_SEC(date_createdOK) - TIME_TO_SEC(fecha_recepcion))) / 60 / 1440 AS Dias,
            IF((DATEDIFF(date_createdOK, fecha_recepcion) * 24 * 60 * 60 + (TIME_TO_SEC(date_createdOK) - TIME_TO_SEC(fecha_recepcion))) / 60 / 1440 <= ROUND(b.meta), 1, 0) as Puntual,
            ROUND(b.meta) as meta_tx,
            ROUND(d.meta) as meta_pap
        FROM
            Q_casos a LEFT JOIN bo_areas c ON a.area=c.bo_area_id LEFT JOIN
            (SELECT
            skill, meta, modo
        FROM
            bo_puntualidad
        WHERE
            month = MONTH('$from')
                AND YEAR = YEAR('$from')
                AND modo = 'tx') b
            ON c.bo_skill = b.skill LEFT JOIN
            (SELECT
            skill, meta, modo
        FROM
            bo_puntualidad
        WHERE
            month = MONTH('$from')
                AND YEAR = YEAR('$from')
                AND modo = 'pap') d
            ON c.bo_skill = d.skill;

        ALTER TABLE Q_casosProcessed ADD PRIMARY KEY (asesor_id, tipificacion_id, area_id);

        DROP TEMPORARY TABLE IF EXISTS Q_tx;

        DROP TEMPORARY TABLE IF EXISTS Q_casosProcessedPap;

        CREATE TEMPORARY TABLE Q_casosProcessedPap SELECT
            asesor AS asesor_id,
            a.id AS tipificacion_id,
            a.area AS area_id,
            NombreAsesor(asesor,2) as Nombre,
            fecha_recepcion,
            IF(em IS NULL, Localizador, em) as emOK,
            localizador,
            a.status as status_id,
            date_createdOK,
            internal_id,
            SUM((DATEDIFF(date_createdOK, fecha_recepcion) * 24 * 60 * 60 + (TIME_TO_SEC(date_createdOK) - TIME_TO_SEC(fecha_recepcion))) / 60) AS Duracion,
            SUM((DATEDIFF(date_createdOK, fecha_recepcion) * 24 * 60 * 60 + (TIME_TO_SEC(date_createdOK) - TIME_TO_SEC(fecha_recepcion))) / 60 / 1440) AS Dias,
            IF(SUM((DATEDIFF(date_createdOK, fecha_recepcion) * 24 * 60 * 60 + (TIME_TO_SEC(date_createdOK) - TIME_TO_SEC(fecha_recepcion))) / 60 / 1440) <= ROUND(d.meta), 1, 0) as Puntual_pap,
            ROUND(b.meta) as meta_tx,
            ROUND(d.meta) as meta_pap
        FROM
            Q_casos a LEFT JOIN bo_areas c ON a.area=c.bo_area_id LEFT JOIN
            (SELECT
            skill, meta, modo
        FROM
            bo_puntualidad
        WHERE
            month = MONTH('$from')
                AND YEAR = YEAR('$from')
                AND modo = 'tx') b
            ON c.bo_skill = b.skill LEFT JOIN
            (SELECT
            skill, meta, modo
        FROM
            bo_puntualidad
        WHERE
            month = MONTH('$from')
                AND YEAR = YEAR('$from')
                AND modo = 'pap') d
            ON c.bo_skill = d.skill GROUP BY area_id, emOK;

        ALTER TABLE Q_casosProcessedPap ADD PRIMARY KEY (area_id, emOK);

        CREATE TEMPORARY TABLE Q_tx SELECT
            area_id,
            COUNT(*) AS Registros_Tx,
            COUNT(IF(Dias <= meta_tx,
                asesor_id,
                NULL)) AS Casos_puntuales_Tx,
            FORMAT(COUNT(IF(Dias <= meta_tx,
                    asesor_id,
                    NULL)) / COUNT(*) * 100,
                2) AS Puntualidad_Tx,
            FORMAT((COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 1 AND (IF(meta_tx IS NULL,0,meta_tx)) + 1.99,
                    asesor_id,
                    NULL)) + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 2 AND (IF(meta_tx IS NULL,0,meta_tx)) + 2.99,
                    asesor_id,
                    NULL)) * 2 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 3 AND (IF(meta_tx IS NULL,0,meta_tx)) + 3.99,
                    asesor_id,
                    NULL)) * 3 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 4 AND (IF(meta_tx IS NULL,0,meta_tx)) + 4.99,
                    asesor_id,
                    NULL)) * 4 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 5 AND (IF(meta_tx IS NULL,0,meta_tx)) + 5.99,
                    asesor_id,
                    NULL)) * 5 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 6 AND (IF(meta_tx IS NULL,0,meta_tx)) + 6.99,
                    asesor_id,
                    NULL)) * 6 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 7 AND (IF(meta_tx IS NULL,0,meta_tx)) + 7.99,
                    asesor_id,
                    NULL)) * 7 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 8 AND (IF(meta_tx IS NULL,0,meta_tx)) + 8.99,
                    asesor_id,
                    NULL)) * 8 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 9 AND (IF(meta_tx IS NULL,0,meta_tx)) + 9.99,
                    asesor_id,
                    NULL)) * 9 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 10 AND (IF(meta_tx IS NULL,0,meta_tx)) + 10.99,
                    asesor_id,
                    NULL)) * 10 + COUNT(IF(Dias BETWEEN (IF(meta_tx IS NULL,0,meta_tx)) + 11 AND (IF(meta_tx IS NULL,0,meta_tx)) + 11.99,
                    asesor_id,
                    NULL)) * 11 + COUNT(IF(Dias >= (IF(meta_tx IS NULL,0,meta_tx)) + 12, asesor_id, NULL)) * 12) / COUNT(IF(Dias > (IF(meta_tx IS NULL,0,meta_tx)) + 1, asesor_id, NULL)),
                2) AS Pendientes_Tx
        FROM
            Q_casosProcessed
        GROUP BY area_id;

        DROP TEMPORARY TABLE IF EXISTS Q_pap;

        CREATE TEMPORARY TABLE Q_pap SELECT
            area_id,
            COUNT(*) AS Registros_pap,
            SUM(Puntual_pap) AS Casos_puntuales_pap,
            FORMAT(SUM(Puntual_pap) / COUNT(*) * 100,
                2) AS Puntualidad_pap,
            FORMAT((COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 1 AND (IF(meta_pap IS NULL,0,meta_pap)) + 1.99,
                    tipificacion_id,
                    NULL)) + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 2 AND (IF(meta_pap IS NULL,0,meta_pap)) + 2.99,
                    tipificacion_id,
                    NULL)) * 2 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 3 AND (IF(meta_pap IS NULL,0,meta_pap)) + 3.99,
                    tipificacion_id,
                    NULL)) * 3 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 4 AND (IF(meta_pap IS NULL,0,meta_pap)) + 4.99,
                    tipificacion_id,
                    NULL)) * 4 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 5 AND (IF(meta_pap IS NULL,0,meta_pap)) + 5.99,
                    tipificacion_id,
                    NULL)) * 5 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 6 AND (IF(meta_pap IS NULL,0,meta_pap)) + 6.99,
                    tipificacion_id,
                    NULL)) * 6 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 7 AND (IF(meta_pap IS NULL,0,meta_pap)) + 7.99,
                    tipificacion_id,
                    NULL)) * 7 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 8 AND (IF(meta_pap IS NULL,0,meta_pap)) + 8.99,
                    tipificacion_id,
                    NULL)) * 8 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 9 AND (IF(meta_pap IS NULL,0,meta_pap)) + 9.99,
                    tipificacion_id,
                    NULL)) * 9 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 10 AND (IF(meta_pap IS NULL,0,meta_pap)) + 10.99,
                    tipificacion_id,
                    NULL)) * 10 + COUNT(IF(Dias BETWEEN (IF(meta_pap IS NULL,0,meta_pap)) + 11 AND (IF(meta_pap IS NULL,0,meta_pap)) + 11.99,
                    tipificacion_id,
                    NULL)) * 11 + COUNT(IF(Dias >= (IF(meta_pap IS NULL,0,meta_pap)) + 12, tipificacion_id, NULL)) * 12) / COUNT(IF(Dias > (IF(meta_pap IS NULL,0,meta_pap)) + 1, tipificacion_id, NULL)),
                2) AS Pendientes_pap
        FROM
            Q_casosProcessedPap
        GROUP BY area_id;

        DROP TEMPORARY TABLE IF EXISTS Q_Sumary;

        CREATE TEMPORARY TABLE Q_Sumary SELECT
            c.area,
            a.*,
            Registros_pap,
            Casos_puntuales_pap,
            Puntualidad_pap,
            Pendientes_pap,
            Sesion,
            Registros_Tx/Sesion as Eficiencia
        FROM
            Q_tx a
                LEFT JOIN
            Q_pap b ON a.area_id = b.area_id
                LEFT JOIN
            bo_areas c ON a.area_id=bo_area_id
                LEFT JOIN
            (SELECT
                a.area, Sesion - Pausa AS Sesion
            FROM
                (SELECT
                    area, skill, SUM(TIME_TO_SEC(Duracion)) / 60 / 60 AS Sesion
                FROM
                    t_Sesiones a
                LEFT JOIN bo_areas ON Skill = bo_skill
                WHERE
                    a.Fecha_in BETWEEN '$from' AND '$to'
                        AND Skill IN (37 , 38, 39, 40, 45, 48, 49)
                GROUP BY area , skill) a
                    LEFT JOIN
                (SELECT
                    area, skill, SUM(TIME_TO_SEC(Duracion)) / 60 / 60 AS Pausa
                FROM
                    t_pausas a
                LEFT JOIN bo_areas ON Skill = bo_skill
                WHERE
                    a.Fecha BETWEEN '$from' AND '$to'
                        AND Skill IN (37 , 38, 39, 40, 45, 48, 49)
                GROUP BY area , skill) b ON a.skill = b.skill
            GROUP BY area) d ON c.area=d.area;";

$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}

$query="SELECT * FROM Q_Sumary HAVING area IS NOT NULL ORDER BY area ";

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
      case '45':
      case '48':
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

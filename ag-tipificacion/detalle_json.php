<?php
include("../modules/modules.php");
header("Content-Type:  application/json;charset=utf-8");

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$connectdb->query("SET timezone = '-10:00'");

//Build Info

$from=date('Y-m-d',strtotime($_POST['from']));
$to=date('Y-m-d',strtotime($_POST['to']));
$skill=$_POST['skill'];

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
			a.id, Nombre,fecha_recepcion as Fecha_recibido,CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ) as fecha_procesado, em, localizador, d.`status`, internal_id,
			(DATEDIFF(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ),fecha_recepcion)*24*60*60 + (TIME_TO_SEC(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ))-TIME_TO_SEC(fecha_recepcion)))/60 as Duracion,
					CASE
						WHEN FLOOR((DATEDIFF(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ),fecha_recepcion)*24*60*60 + (TIME_TO_SEC(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ))-TIME_TO_SEC(fecha_recepcion)))/60/1440)<4 THEN FLOOR((DATEDIFF(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ),fecha_recepcion)*24*60*60 + (TIME_TO_SEC(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ))-TIME_TO_SEC(fecha_recepcion)))/60/1440)
						ELSE 5
					END as Dias_de_atraso
				FROM
					ag_bo_tipificacion a
				LEFT JOIN
					Asesores c ON a.asesor=c.id
				LEFT JOIN
					ag_bo_status d ON a.status=d.id
				WHERE
					CAST(CONVERT_TZ( date_created, '-05:00',IF(getHV(date_created)=1,'-05:00','-06:00') ) as DATE) BETWEEN '$from' AND '$to' AND
					a.status!=8
				ORDER BY
					Duracion
			";
if ($result=$connectdb->query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
			switch($info_field[$i]->type){
				case 246:
					$data[$fila[0]][]=number_format($fila[$i],2);
					break;
				default:
					if($info_field[$i]->name=='Fecha_recibido'){
						if($_POST['editable']==1){
							$data[$fila[0]][]=utf8_encode("<input type='text' class='f_recep' value='$fila[$i]' reg='".$fila[0]."'>");
						}else{
							$data[$fila[0]][]=utf8_encode($fila[$i]);
						}
					}else{
						$data[$fila[0]][]=utf8_encode($fila[$i]);
					}
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

$connectdb->close();

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>

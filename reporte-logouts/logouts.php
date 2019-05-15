<?php
include("../connectDB.php");
header("Content-Type:  application/json;charset=utf-8");

//Build Info

$from=date('Y-m-d',strtotime($_GET['inicio']));
$to=date('Y-m-d',strtotime($_GET['fin']));

$query="SELECT id, Fecha, horario_aceptado as horario, Hora as hora, NombreAsesor(asesor,1) as Asesor, NombreAsesor(asesor,3) as Departamento FROM horarios_check WHERE Fecha BETWEEN '$from' AND '$to'";

$x=0;
if ($result=$connectdb->query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
			switch($info_field[$i]->name){
					case 'horario':
						$data[$fila[0]][]=utf8_encode("<table class='t2' style='text-align: center; margin: auto; width: 335;'>".str_replace('<tbody>', '', str_replace('</tbody>', '', str_replace('<th', "<th style='width: 50%'", $fila[$i])))."</table>");
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



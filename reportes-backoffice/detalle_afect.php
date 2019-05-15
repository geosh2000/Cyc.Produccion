<?php
include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$connectdb->query("SET timezone = '-10:00'");

//Build Info

$from=date('Y-m-d',strtotime($_GET['from']));
$to=date('Y-m-d',strtotime($_GET['to']));

switch($_GET['tipo']){
  case 1:
    $query="SELECT 
              a.id, NombreAsesor(asesor,1) as Asesor, date_created as Fecha, Localizador, Caso, b.text as Aerolinea, c.text as Rango, duracion, d.text as Canal, Last_Update
            FROM 
              bo_afectaciones_funciones a 
            LEFT JOIN
              formulario_BO_Afectaciones b ON a.al=b.id
            LEFT JOIN
              formulario_BO_Afectaciones c ON a.rango=c.id
            LEFT JOIN
              formulario_BO_Afectaciones d ON a.canal=d.id
            WHERE CAST(date_created as DATE) BETWEEN '$from' AND '$to'";
    break;
  case 2:
    $query="SELECT 
              a.id, NombreAsesor(asesor,1) as Asesor, date_created as Fecha, Localizador, Caso, nombre as Cliente, tel as Telefono, b.text as Aerolinea, c.text as Rango, duracion, d.text as Canal, Last_Update
            FROM 
              bo_afectaciones_llamadas a 
            LEFT JOIN
              formulario_BO_Afectaciones_calls b ON a.al=b.id
            LEFT JOIN
              formulario_BO_Afectaciones_calls c ON a.rango=c.id
            LEFT JOIN
              formulario_BO_Afectaciones_calls d ON a.canal=d.id
            WHERE CAST(date_created as DATE) BETWEEN '$from' AND '$to'";
    break;
}
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
						if($_GET['editable']==1){
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



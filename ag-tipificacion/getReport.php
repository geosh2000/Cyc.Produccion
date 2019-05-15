<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$from=$_POST['from'];
$to=$_POST['to'];

$query="SELECT a.id, b.Canal, c.motivo, e.tipo, d.tipo_soporte, nombre_agencia, nombre_cliente, localidad_agencia, localizador, NombreAsesor(asesor,2) as Asesor, date_created
        FROM
        	ag_tipificacion a
        	LEFT JOIN ag_canal b ON a.canal=b.id
        	LEFT JOIN ag_motivos c ON a.motivo=c.id
        	LEFT JOIN ag_soporte d ON a.soporte=d.id
        	LEFT JOIN ag_tipo e ON a.tipo=e.id
        WHERE
        	CAST(a.date_created as DATE) BETWEEN '$from' AND '$to'";

if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  $fcount=$result->field_count;
  $rows=$result->num_rows;
  while($fila=$result->fetch_array()){
    for($i=0;$i<$result->field_count;$i++){
      $td[$fila[0]][]=utf8_encode($fila[$i]);
    }
  }
}

for($i=0;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$fields[$i]->name));
}

unset($result);

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
if(isset($td)){
  foreach($td as $id =>$info){
    $row[]=$info;
  }
}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);

$connectdb->close();
?>

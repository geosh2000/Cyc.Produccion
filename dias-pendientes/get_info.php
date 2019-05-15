<?php

include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$asesor=$_POST['asesor'];

$query="SELECT
        	a.indice, a.asesor, a.motivo, asignados, redimidos
        FROM
        	(SELECT indice, id as asesor, SUM(`dias asignados`) as asignados, motivo FROM `Dias Pendientes` WHERE id=$asesor GROUP BY id, motivo) a
        LEFT JOIN
        	(SELECT id as asesor, SUM(dias) as redimidos, motivo FROM `Dias Pendientes Redimidos` WHERE id=$asesor GROUP BY id, motivo) b ON a.asesor=b.asesor AND a.motivo=b.motivo";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $data[$fila['indice']][]=utf8_encode($fila['motivo']);
    $data[$fila['indice']][]=$fila['asignados'];
    IF($fila['redimidos'] == NULL){
      $redimidos=0;
    }else{
      $redimidos=$fila['redimidos'];
    }
    $data[$fila['indice']][]=$redimidos;
    $data[$fila['indice']][]=$fila['asignados']-$redimidos;
  }
}

$dataheaders[]=ucwords('Motivo');
$dataheaders[]=ucwords('asignados');
$dataheaders[]=ucwords('redimidos');
$dataheaders[]=ucwords('Total');

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($data as $id =>$info){
  $row[]=$info;
}

//Add Edit Button
//if($_SESSION['config']==1){$headers[]=array("text"=>"Editar");}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);

$connectdb->close();

?>

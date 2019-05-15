<?php
include_once("../modules/modules.php");

$asesor=$_POST['asesor'];

$query="SELECT ausent_id as ID, Ausentismo, Inicio, Fin, caso as Caso, Moper, Comments, `Last Update` as 'Fecha Captura', username as 'Capturado por'  FROM Ausentismos a LEFT JOIN `Tipos Ausentismos` b ON a.tipo_ausentismo=b.id LEFT JOIN userDB c ON a.User=c.userid WHERE asesor=$asesor";
if($result=Queries::query($query)){
	$columns=$result->fetch_fields();
	while($fila=$result->fetch_array(MYSQLI_BOTH)){
		for($i=0;$i<$result->field_count;$i++){
			$data[$fila[0]][]=utf8_encode($fila[$i]);
		}
	}
}

for($i=0;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$columns[$i]->name));
}

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Add Moper and Eliminar
$headers[]=array("text"=>"Editar Moper");
$headers[]=array("text"=>"Eliminar");

//Create Rows
foreach($data as $id =>$info){
	$tmp=$info;
	array_push($tmp,"<button class='button button_green_w editMoper' ausid='$id'>Moper</button>","<button class='button button_red_w removeAus' ausid='$id'>Eliminar</button>");
	$row[]=$tmp;
}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);



?>



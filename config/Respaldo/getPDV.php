<?php
include("../modules/modules.php");

session_start();

$id=$_GET['id'];

$query="SELECT a.pdv as pdvid, a.id, NombreAsesor(asesor,2) as Nombre, fecha as Fecha, b.PDV FROM asesores_pdv a LEFT JOIN PDVs b ON a.pdv=b.id WHERE asesor=$id ORDER BY fecha";
if ($result=Queries::query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=1;$i<$result->field_count;$i++){
			$data[$fila[1]][]=utf8_encode($fila[$i]);
		}
    if($_SESSION['config']==1){
			$hidden="<input type='hidden' id='dep_".$fila[1]."' value='".$fila[0]."'>";
      $data[$fila[1]][]=utf8_encode("$hidden<button class='button button_blue_w editPuesto' puestoId='".$fila[1]."'>Edit</button><button class='button button_red_w deletePuesto' puestoId='".$fila[1]."'>Delete</button>");
    }
	}
}


for($i=1;$i<$result->field_count;$i++){
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

//Add Edit Button
if($_SESSION['config']==1){$headers[]=array("text"=>"Editar");}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>

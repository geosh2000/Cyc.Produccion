<?php
include("../modules/modules.php");

session_start();

$id=$_POST['id'];

$query="SELECT IF(Egreso<'2029-01-01',1,0) as EgresoCheck, Egreso FROM Asesores WHERE id=$id";
if($result=Queries::query($query)){
	$fila=$result->fetch_assoc();
	$egresoCheck=$fila['EgresoCheck'];
	$egreso=$fila['Egreso'];
}

$query="SELECT c.id as depid, d.id as puestoId, a.id, NombreAsesor(a.asesor_in,2) as Nombre, a.fecha_in as Fecha, b.id as Vacante, c.Departamento, d.Puesto, e.PDV,
	outP.id as Liberacion_id, outP.fecha_out Liberacion_Vacante, IF(outP.fecha_out IS NOT NULL,IF(Egreso>'2029-01-01',NULL,Egreso),NULL) as Fecha_Baja
FROM asesores_movimiento_vacantes a
LEFT JOIN asesores_plazas b ON a.vacante=b.id
LEFT JOIN PCRCs c ON b.departamento=c.id
LEFT JOIN PCRCs_puestos d ON b.puesto=d.id
LEFT JOIN PDVs e ON b.oficina=e.id
LEFT JOIN asesores_movimiento_vacantes outP ON getVacanteOut(a.asesor_in,a.fecha_in)=outP.id
LEFT JOIN Asesores g ON $id=g.id
WHERE a.asesor_in=$id";
if ($result=Queries::query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=2;$i<$result->field_count;$i++){
			$data[$fila[2]][]=utf8_encode($fila[$i]);
		}
    if($_SESSION['config']==10){
			$data[$fila[2]][]=utf8_encode("$hidden<button class='button button_red_w deletePuesto' puestoId='".$fila[2]."'>Delete</button>");
    }


	}
}

for($i=2;$i<$result->field_count;$i++){
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
//if($_SESSION['config']==1){$headers[]=array("text"=>"Editar");}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

$td['table']=$table;
$td['egreso']=$egreso;
$td['egresoCheck']=$egresoCheck;

//Print JSON
print json_encode($td,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>

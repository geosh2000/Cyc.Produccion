<?php
include("../connectDB.php");
header("Content-Type:  application/json;charset=utf-8");

//Build Info

$from=date('Y-m-d',strtotime($_GET['inicio']));
$to=date('Y-m-d',strtotime($_GET['fin']));

$query="SELECT 
			a.ComidaId as id, asesor, NombreAsesor(asesor,1) as Asesor, NombreAsesor(asesor,3) as Departamento, b.Pausa, a.Fecha, a.Inicio, a.Fin, a.Duracion, c.comentarios as Comentarios, NombreAsesor(creado,1) as aplicado_por, Last_Update as Last_Comment
		FROM 
			Comidas a LEFT JOIN Tipos_pausas b ON a.tipo=b.pausa_id
		LEFT JOIN pausas_incidencias c ON a.ComidaId=c.id_pausa 
		WHERE a.Fecha BETWEEN '$from' AND '$to' AND ((
		  (tipo=3 AND Duracion>'00:37:00' AND NombreAsesor(asesor,3) NOT IN ('Trafico MT','Calidad', 'Mesa de Expertos')) OR 
		  (tipo=3 AND Duracion>'01:10:00' AND NombreAsesor(asesor,3) IN ('Trafico MT','Calidad', 'Mesa de Expertos'))
		 ) OR (tipo=11 AND Duracion>'00:07:00') OR (tipo=10 AND Duracion>'00:04:00')) 
		 HAVING Departamento NOT LIKE '%PDV%'
			
		ORDER BY Fecha, Inicio";

$x=0;
if ($result=$connectdb->query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
			switch($info_field[$i]->type){
				case 246:
					$data[$fila[0]][]=number_format($fila[$i],2);
					break;
				default:
					switch($info_field[$i]->name){
						case 'Comentarios':
							if($fila[$i] == NULL){
								$data[$fila[0]][]=utf8_encode(" ");
							}else{
								$data[$fila[0]][]=utf8_encode($fila[$i]);
							}
							break;
						case 'id':
							$data[$fila[0]][]=utf8_encode("<id class='comment' pausa_id='".$fila[0]."'>".$fila[$i]."</id>");
							break;
						default:
							$data[$fila[0]][]=utf8_encode($fila[$i]);
							break;
					}
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



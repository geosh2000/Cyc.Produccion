<?php

include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

$from=date('Y-m-d',strtotime($_GET['from']));
$to=date('Y-m-d',strtotime($_GET['to']));
$detalle=$_GET['detalle'];


	$query="SELECT 
		CAST(Last_Update as DATE) as Fecha, NombreAsesor(asesor,2) as Nombre, COUNT(*) as Registros 
	FROM 
		sac_tipificacion
	WHERE CAST(Last_Update as DATE) BETWEEN '$from' AND '$to'
	GROUP BY asesor, Fecha
	ORDER BY Nombre, Fecha";
	if($result=Queries::query($query)){
		$fields=$result->fetch_fields();
		$numcols=$result->field_count;
		$i=0;
		while($fila=$result->fetch_array(MYSQLI_BOTH)){
			for($x=0;$x<$numcols;$x++){
				$datatable[$i][$fields[$x]->name]=utf8_encode($fila[$x]);
			}
		
		$i++;
		}
		
		//Add titles to tableheaders
		for($i=1;$i<$result->field_count;$i++){
			$dataheaders[]=utf8_encode(str_replace("_", " ", $info_field_act[$i]->name));
		}
	}
	
	$query="SELECT 
		CAST(Last_Update as DATE) as Fecha, Nombre, f.Canal, g.Producto, b.Motivo_General, c.Motivo_Especifico, d.Detalle, Localizador 
	FROM 
		sac_tipificacion a 
	LEFT JOIN
		sac_motivos_generales b ON a.motivo_general=b.id
	LEFT JOIN
		sac_motivos_especificos c ON a.motivo_especifico = c.id
	LEFT JOIN
		sac_detalle d ON a.detalle=d.id
	LEFT JOIN
		sac_canal f ON a.canal=f.id
	LEFT JOIN
		sac_productos g ON a.producto=g.id
	LEFT JOIN
		Asesores e ON a.asesor=e.id
	WHERE CAST(a.Last_Update as DATE) BETWEEN '$from' AND '$to'
	ORDER BY Last_Update";
	if($result=Queries::query($query)){
		$fields=$result->fetch_fields();
		$numcols=$result->field_count;
		$i=0;
		while($fila=$result->fetch_array(MYSQLI_BOTH)){
			$data[utf8_encode($fila['Motivo_General'])][utf8_encode($fila['Detalle'])]++;
			$total++;
			for($x=0;$x<$numcols;$x++){
				$datatable_detail[$i][$fields[$x]->name]=utf8_encode($fila[$x]);
			}
		
		$i++;
		}
		
		//Add titles to tableheaders
		for($i=1;$i<$result->field_count;$i++){
			$dataheaders_detail[]=utf8_encode(str_replace("_", " ", $info_field_act[$i]->name));
		}
	}
	
	
	foreach($data as $index => $info){
		asort($data[$index]);
	}
	unset($index,$info);
	
	$colors=array('#00358c', '#54008c', '#8c0058', '#008c23', '#878c00', '#ffb200', '#750700');
	
	
	$i=0;
	foreach($data as $cat => $info){
		$categories[]=$cat;
		
		foreach($info as $subcat => $info3){
			$subcategorie[]=$subcat;
			$subdata[]=round($info3/$total*100,2,PHP_ROUND_HALF_UP);
			//$subdata[]=$info3;
			@$subtotal+=$info3;
		}
		
		$data_in[]=array("y" => round($subtotal/$total*100,2,PHP_ROUND_HALF_UP), "color" => $colors[$i], "drilldown" => array("name" => $cat, "categories" => $subcategorie, "data" => $subdata, "color" => $colors[$i]));
		
		unset($sucategorie, $subdata, $subtotal);
		//$data_in[]=array("y" => array_sum($info), "color" => $colors[$i], "drilldown" => array("name" => $cat, "categories" => $subcategorie, "data" => $subdata, "color" => $colors[$i]));
	$i++;
	}
	

if($detalle!=1){
	$headers=$dataheaders;
	$table=$datatable;
}else{
	$headers=$dataheaders_detail;
	$table=$datatable_datail;
}

//Create Headers
foreach($headers as $index => $info){
	$headersOK[]=array("text"=>$info);
}

//Create Rows
foreach($table as $id =>$info){
	$row[]=$info;
}

//Build JSON
$tableOK = array();
$tableOK = array("rows" => $row,"headers"=>array($headersOK));


$td=array('total' => $total, 'graph'=> array("categories" => $categories, "data" => $data_in), 'table' => $tableOK);

print json_encode($td, JSON_PRETTY_PRINT);

?>


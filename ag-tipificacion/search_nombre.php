<?php
   include_once("../modules/modules.php");
	
	$connectdb=Connection::mysqliDB('CC');
	
	//get search term
    $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

    //get matched data from skills table
    $query = "SELECT DISTINCT nombre_cliente FROM ag_tipificacion WHERE nombre_cliente LIKE '%$searchTerm%' ORDER BY nombre_cliente ASC";
    if($result=$connectdb->query($query)){
    	while ($fila=$result->fetch_Assoc()) {
        	$data[] = utf8_encode($fila['nombre_cliente']);
		}
    }
	
	$connectdb->close();

    //return json data
    echo json_encode($data);
?>
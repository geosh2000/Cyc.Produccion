<?php
    include_once("../modules/modules.php");
	
	$connectdb=Connection::mysqliDB('CC');
	
	//get search term
    $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

    //get matched data from skills table
    $query = "SELECT DISTINCT localidad_agencia FROM ag_tipificacion WHERE localidad_agencia LIKE '%$searchTerm%' ORDER BY localidad_agencia ASC";
    if($result=$connectdb->query($query)){
    	while ($fila=$result->fetch_Assoc()) {
        	$data[] = utf8_encode($fila['localidad_agencia']);
		}
    }
	
	$connectdb->close();

    //return json data
    echo json_encode($data);
?>
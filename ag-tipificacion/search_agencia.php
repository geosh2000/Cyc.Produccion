<?php
    include_once("../modules/modules.php");
	
	$connectdb=Connection::mysqliDB('CC');
	
	//get search term
    $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

    //get matched data from skills table
    $query = "SELECT DISTINCT nombre_agencia FROM ag_tipificacion WHERE nombre_agencia LIKE '$searchTerm' ORDER BY nombre_agencia ASC";
    if($result=$connectdb->query($query)){
    	while ($fila=$result->fetch_Assoc()) {
        	$data[] = utf8_encode($fila['nombre_agencia']);
		}
    }else{
    	echo $connectdb->error;
    }
	
	
	
	//return json data
    echo json_encode($data);

	
	$connectdb->close();
?>
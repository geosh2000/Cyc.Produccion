<?php
    include('../connectMYSQLI.php');

    //get search term
    $searchTerm = $_GET['term'];

    //get matched data from skills table
    $query = "SELECT DISTINCT nombre_agencia FROM ag_tipificacion WHERE nombre_agencia LIKE '%$searchTerm%' ORDER BY nombre_agencia ASC";
    if($result=$connectdb->query($query)){
    	while ($fila=$result->fetch_Assoc()) {
        	$data[] = $fila['nombre_agencia'];
		}
    }
	
	$connectdb->close();
	
    //return json data
    echo json_encode($data);
?>
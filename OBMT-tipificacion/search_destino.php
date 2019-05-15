<?php

include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');
    //get search term
    $searchTerm = utf8_decode($_GET['term']);

	
    //get matched data from skills table
    $query = "SELECT DISTINCT level8 FROM OBMT_tipificacion WHERE level8 LIKE '%$searchTerm%' ORDER BY level8 ASC";
    if($result=$connectdb->query($query)){
    	while ($fila=$result->fetch_Assoc()) {
        	$data[] = utf8_encode($fila['level8']);
		}
    }

    //return json data
    echo json_encode($data);

    $connectdb->close();
?>

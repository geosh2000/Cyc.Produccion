<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

//get search term
  $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

  //get matched data from skills table
  $query = "SELECT id, proveedor FROM pantallas_display WHERE proveedor LIKE '$searchTerm' GROUP BY proveedor ORDER BY proveedor";
  if($result=$connectdb->query($query)){
  	while ($fila=$result->fetch_Assoc()) {
      	$data[$fila['id']]['proveedor'] = utf8_encode($fila['proveedor']);
	}
  }else{
  	echo $connectdb->error;
  }

foreach($data as $id => $info){
  $td[]=array('label' => $info['proveedor']);
}

//return json data
echo json_encode($td);


$connectdb->close();
?>

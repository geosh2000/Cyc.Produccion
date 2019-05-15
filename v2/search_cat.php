<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

//get search term
  $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

  //get matched data from skills table
  $query = "SELECT id, categoria FROM pantallas_display WHERE categoria LIKE '$searchTerm' GROUP BY categoria ORDER BY categoria";
  if($result=$connectdb->query($query)){
  	while ($fila=$result->fetch_Assoc()) {
      	$data[$fila['id']]['categoria'] = utf8_encode($fila['categoria']);
	}
  }else{
  	echo $connectdb->error;
  }

foreach($data as $id => $info){
  $td[]=array('label' => $info['categoria']);
}

//return json data
echo json_encode($td);


$connectdb->close();
?>

<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

//get search term
  $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

  //get matched data from skills table
  $query = "SELECT indice, motivo FROM `Dias Pendientes` WHERE motivo LIKE '$searchTerm' GROUP BY motivo ORDER BY motivo ASC";
  if($result=$connectdb->query($query)){
  	while ($fila=$result->fetch_Assoc()) {
      	$data[$fila['indice']]['motivo'] = utf8_encode($fila['motivo']);
    }
  }else{
  	echo $connectdb->error;
  }

foreach($data as $id => $info){
  $td[]=array('label' => $info['motivo']);
}

//return json data
echo json_encode($td);


$connectdb->close();
?>

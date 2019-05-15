<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

//get search term
  $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

  //get matched data from skills table
  $query = "SELECT id, Departamento FROM PCRCs WHERE Departamento LIKE '$searchTerm' AND parent=1 ORDER BY Departamento ASC";
  if($result=$connectdb->query($query)){
  	while ($fila=$result->fetch_Assoc()) {
      	$data[$fila['id']]['dep'] = utf8_encode($fila['Departamento']);
	}
  }else{
  	echo $connectdb->error;
  }

foreach($data as $id => $info){
  $td[]=array('label' => $info['dep'], 'id' => $id );
}

//return json data
echo json_encode($td);


$connectdb->close();
?>

<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

//get search term
  $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

  //get matched data from skills table
  $query = "SELECT a.id, a.Ciudad as name, b.Estado as Departamento FROM db_municipios a LEFT JOIN db_estados b ON a.estado=b.id WHERE Ciudad LIKE '$searchTerm' OR b.Estado LIKE '$searchTerm' ORDER BY b.Estado, Ciudad ASC";
  if($result=$connectdb->query($query)){
  	while ($fila=$result->fetch_Assoc()) {
      	$data[$fila['id']]['name'] = utf8_encode($fila['name']);
        $data[$fila['id']]['dep'] = utf8_encode($fila['Departamento']);
	}
  }else{
  	echo $connectdb->error;
  }

foreach($data as $id => $info){
  $td[]=array('label' => $info['name'], 'category' => $info['dep'], 'id' => $id );
}

//return json data
echo json_encode($td);


$connectdb->close();
?>

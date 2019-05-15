<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

//get search term
  $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

  //get matched data from skills table
  $query = "SELECT a.id, PDV as name, c.Estado as Departamento FROM PDVs a LEFT JOIN db_municipios b ON a.ciudad = b.id LEFT JOIN db_estados c ON b.estado=c.id WHERE PDV LIKE '$searchTerm' OR `PDV_rrhh` LIKE '$searchTerm' OR b.Ciudad LIKE '$searchTerm' OR c.Estado LIKE '$searchTerm' ORDER BY c.Estado, PDV ASC";
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

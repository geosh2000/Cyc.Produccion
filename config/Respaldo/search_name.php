<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

timeAndRegion::setRegion('Cun');

if(isset($_GET['date'])){
  $fecha="'".date('Y-m-d', strtotime($_GET['date']))."'";
}else{
  $fecha='CURDATE()';
}

//get search term
  $searchTerm = $connectdb->real_escape_string("%".$_GET['term']."%");

  //get matched data from skills table
  $query = "SELECT a.id, Nombre as name, Departamento, a.dep as dep, puestoOK
            FROM 
            (SELECT a.*, dep, b.puesto as puestoOK FROM Asesores a LEFT JOIN daily_dep b ON a.id=b.asesor) a
            LEFT JOIN PCRCs b ON a.dep=b.id WHERE Nombre LIKE '$searchTerm' ORDER BY Departamento, Nombre ASC";
  if($result=$connectdb->query($query)){
  	while ($fila=$result->fetch_Assoc()) {
      	$data[$fila['id']]['name'] = utf8_encode($fila['name']);
        $data[$fila['id']]['dep'] = utf8_encode($fila['Departamento']);
        $data[$fila['id']]['depID'] = utf8_encode($fila['dep']);
        $data[$fila['id']]['puestoID'] = utf8_encode($fila['puestoOK']);
	  }
  }else{
  	echo $connectdb->error;
  }

foreach($data as $id => $info){
  $td[]=array('label' => $info['name'], 'category' => $info['dep'], 'id' => $id, 'depid' => $info['depID'], 'puestoid' => $info['puestoID'] );
}

//return json data
echo json_encode($td);


$connectdb->close();
?>

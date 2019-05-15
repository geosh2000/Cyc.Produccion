<?php

include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

$fecha=date('Y-m-d', strtotime($_POST['fecha']));
$dep=$_POST['dep'];
$puesto=$_POST['puesto'];

$connectdb=Connection::mysqliDB('CC');

$query="SELECT a.id, Nombre as name, IF(getDepartamento(a.id,'$fecha') IS NULL,IF(Egreso>'$fecha',getLastDep(a.id),NULL),getDepartamento(a.id,'$fecha')) as dep, IF(getPuesto(a.id,'$fecha') IS NULL,IF(Egreso>'$fecha',getLastPuesto(a.id),NULL),getPuesto(a.id,'$fecha')) as puestoOK
        FROM Asesores a
        HAVING dep=$dep AND puestoOK=$puesto ORDER BY Nombre ASC";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $data['asesor'][]=array(
        'id' => $fila['id'],
        'desc' => utf8_encode($fila['name'])
      );
  }
  $data['error']=0;
}else{
  $data['error']=1;
  $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

if(!isset($data['vac']) && !isset($data['error'])){
  $data['error']=1;
  $data['msg']=utf8_encode("No hay vacantes para la fecha elegida");
}

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);

 ?>

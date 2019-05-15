<?php

include('../modules/modules.php');

session_start();

timeAndRegion::setRegion('Cun');

$date=$_POST['date'];
$asesor=$_POST['asesor'];
$dias=$_POST['dias'];
$motivo=utf8_encode($_POST['motivo']);

$connectdb=Connection::mysqliDB('CC');

$query="INSERT INTO `Dias Pendientes` (id, `dias asignados`, day, month, year, motivo, User) VALUES ($asesor, $dias, ".date('d',strtotime($date)).", ".date('m',strtotime($date)).", ".date('Y',strtotime($date)).", '$motivo', ".$_SESSION['asesor_id'].")";
if($result=$connectdb->query($query)){
  $data['status']=1;
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

print json_encode($data, JSON_PRETTY_PRINT);

 ?>

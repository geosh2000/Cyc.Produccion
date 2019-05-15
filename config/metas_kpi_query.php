<?php

include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$skill=$_POST['skill'];
$month=$_POST['month'];
$year=$_POST['year'];
$tipo=$_POST['tipo'];
$col=$_POST['col'];
$meta=$_POST['newVal'];

$query="INSERT INTO metas_kpi (skill, tipo, mes, anio, $col) VALUES ($skill, '$tipo', $month, $year, $meta) ON DUPLICATE KEY UPDATE $col=$meta";
if($result=$connectdb->query($query)){
  $data['status']=1;
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($data);
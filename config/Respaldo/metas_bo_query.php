<?php

include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$skill=$_POST['skill'];
$month=$_POST['month'];
$year=$_POST['year'];
$modo=$_POST['modo'];
$meta=$_POST['newVal'];

$query="INSERT INTO bo_puntualidad VALUES ($skill, $month, $year, '$modo', $meta) ON DUPLICATE KEY UPDATE meta=$meta";
if($result=$connectdb->query($query)){
  $data['status']=1;
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($data);
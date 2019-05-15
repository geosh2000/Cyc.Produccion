<?php
include_once('../modules/modules.php');

session_start();

$connectdb=Connection::mysqliDB('CC');

$asesor=$_POST['asesor'];
$status=$_POST['status'];

if($status==0){$oldStatus=1;}else{$oldStatus=0;}

$query="INSERT INTO horarios_position_select VALUES ($asesor, WEEK(ADDDATE(CURDATE(),-1))+1, YEAR(CURDATE()),NULL,$status) ON DUPLICATE KEY UPDATE comida=$status";

if($result=$connectdb->query($query)){
  $data['status']=1;
  $data['query']=utf8_encode($query);
  
  $query="INSERT INTO horarios_position_select VALUES ($asesor, WEEK(ADDDATE(CURDATE(),-1))+2, YEAR(CURDATE()),NULL,$status) ON DUPLICATE KEY UPDATE comida=$status";
  $connectdb->query($query);
  
  $query="INSERT INTO historial_asesores (asesor, campo, old_val, new_val, changed_by) VALUES ($asesor,CONCAT('Comida Semana ',WEEK(ADDDATE(CURDATE(),-1))+1, ' || ', YEAR(CURDATE())),$oldStatus,$status,".$_SESSION['asesor_id'].")";
  if($resultado=$connectdb->query($query)){
    $data['historial']=1;
  }else{
    $data['historial']=utf8_encode("ERROR -> ".$connectdb->error." ON $query");
  }
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("ERROR -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($data,JSON_PRETTY_PRINT);

?>

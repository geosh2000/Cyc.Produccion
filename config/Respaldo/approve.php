<?php

include_once('../modules/modules.php');

session_start();

if($_SESSION['vacant_approve']!=1){
  $data['status']=0;
  $data['msg']=utf8_encode('La sesión a expirado o no cuenta con los permisos para aprobar vacantes');
  print json_encode($data);
}

$connectdb=Connection::mysqliDB('CC');

$id=$_POST['id'];
$action=$_POST['action'];
$user=$_SESSION['asesor_id'];

$query="UPDATE asesores_plazas SET Status=$action, approbed_by=$user, date_approbed=CURTIME() WHERE id=$id";
if($result=$connectdb->query($query)){
  $data['status']=1;
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

print json_encode($data, JSON_PRETTY_PRINT);

if($data['status']==1 && $action!=0){
  include_once("../mailing/approve.php");
}

$connectdb->close();

?>

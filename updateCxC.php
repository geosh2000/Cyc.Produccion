<?php
include("../modules/modules.php");

session_start();

$connectdb=Connection::mysqliDB('CC');


$id=$_POST['id'];
$newVal=utf8_decode($_POST['val']);
$field=$_POST['field'];

if($field=='fecha_aplicacion'){
  timeAndRegion::setRegion('Cun');
  $newVal=date('Y-m-d',strtotime($_POST['val']));
}

if($field=='delete'){
  $query="DELETE FROM asesores_cxc WHERE id=$id";
  if ($result=$connectdb->query($query)) {
    $data['status']=1;
  }else{
    $data['status']=0;
    $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
  }
}else{
  $query="UPDATE asesores_cxc SET $field='$newVal', updated_by=".$_SESSION['asesor_id']." WHERE id=$id";
  if ($result=$connectdb->query($query)) {
    $data['status']=1;
  }else{
    $data['status']=0;
    $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
  }

  $query="SELECT NombreAsesor(updated_by,1) as updated, last_update FROM asesores_cxc WHERE id=$id";
  $result=$connectdb->query($query);
  $fila=$result->fetch_assoc();
  $data['updater']=$fila['updated'];
  $data['l_updater']=$fila['last_update'];
}

$connectdb->close();

//Print JSON
print json_encode($data,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>

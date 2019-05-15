<?php

include_once('../modules/modules.php');

session_start();

$connectdb=Connection::mysqliDB('CC');

$id=$_POST['id'];
$field=$_POST['field'];
$newVal="'".utf8_decode($_POST['newVal'])."'";
if($_POST['newVal']==""){$newVal="NULL";}

$query="UPDATE asesores_movimiento_vacantes SET `$field`=$newVal, userupdate=".$_SESSION['asesor_id']."  WHERE id='$id'";

if($result=$connectdb->query($query)){
	$data['status']=1;

}else{
	$data['status']=0;
	$data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);





?>

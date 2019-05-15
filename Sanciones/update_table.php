<?php

include("../connectDB.php");
header('Content-Type: text/html; charset=utf-8');


$id=$_POST['id'];

$field=$_POST['field'];
$asesor=$_POST['user'];

$newVal="'".utf8_decode($_POST['newVal'])."'";
if($_POST['newVal']==""){$newVal="NULL";}



$query="UPDATE Sanciones SET `$field`=$newVal, last_user_update=$asesor WHERE id='$id'";

if($connectdb->query($query)){
	echo "status- OK -status msg- Validacion Exitosa de registro $id -msg";
}else{
	echo "status- ERROR -status msg- Error al actualizar id $id ".$connectdb->error." -msg";
}





?>
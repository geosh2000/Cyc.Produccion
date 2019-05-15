<?php

include_once("../../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$mx_time = new DateTimeZone('America/Mexico_City');

$id=$_POST['id'];
$field=$_POST['field'];
$newVal="'".$_POST['newVal']."'";
$fecha=date('Y-m-d',strtotime($_POST['fecha']));

$tmp = new DateTime($fecha." ".$_POST['newVal']." America/Bogota");
$tmp -> setTimezone($mx_time);
$newVal = "'".$tmp->format('H:i:s')."'";


if($_POST['newVal']==""){$newVal="NULL";}

$query="UPDATE `Historial Programacion` SET `$field`=$newVal WHERE id='$id'";

//echo "$query<br>";

if($result=$connectdb->query($query)){
	$data['status']='OK';
	$data['msg']="Validacion Exitosa de registro $id";
	$data['query']=utf8_encode($query);
}else{
	$data['status']='ERROR';
	$data['msg']=utf8_encode("ERROR ".$connectdb->error." ON $query");
}


print json_encode($data,JSON_PRETTY_PRINT);



?>

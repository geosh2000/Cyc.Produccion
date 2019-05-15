<?php

include_once('../modules/modules.php');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$l=$_POST['l'];
$h=$_POST['h'];
$fecha=$_POST['fecha'];
$skill=$_POST['skill'];
$accion=$_POST['accion'];
$comments=utf8_decode($_POST['comments']);

session_start();

$query="INSERT INTO 
            bitacora_base (intervalo, skill, Fecha, level, accion, user, comments) VALUES ($h, $skill, '$fecha', $l, $accion, ".$_SESSION['asesor_id'].", '$comments') ON DUPLICATE KEY UPDATE accion=$accion, user=".$_SESSION['asesor_id'].", comments='$comments'";
$data['query']=utf8_encode($query);
if($result=$connectdb->query($query)){
    $data['status']=1;
}

echo json_encode($data);

$connectdb->close();
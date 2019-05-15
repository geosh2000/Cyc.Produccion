<?php
include_once('../modules/modules.php');

session_start();

$connectdb=Connection::mysqliDB('CC');

$id=substr($_POST['ids'],1,100);

$query="UPDATE rrhh_pagoCxC SET cobrado=1, saldado_por=".$_SESSION['asesor_id']." WHERE id IN($id)";
if($connectdb->query($query)){
    $td['status']=1;
}else{
    $td['status']=0;
    $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

//Print JSON
print json_encode($td,JSON_UNESCAPED_UNICODE);
 ?>

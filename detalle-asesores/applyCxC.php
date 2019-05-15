<?php 

include_once("../modules/modules.php");

$id=$_POST['id'];

$connectdb=Connection::mysqliDB('CC');

$query="UPDATE asesores_cxc SET status=1 WHERE id=$id";
if($result=$connectdb->query($query)){
    $td['status']=1;
}else{
    $td['status']=0;
    $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($td, JSON_PRETTY_PRINT);
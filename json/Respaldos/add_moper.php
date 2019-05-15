<?php

include_once("../modules/modules.php");

$moper=$_POST['moper'];
$id=$_POST['id'];

$connectdb=Connection::mysqliDB('CC');

$query="UPDATE Ausentismos SET ISI=1, Moper='$moper' WHERE ausent_id='$id'";
if($result=$connectdb->query($query)){
	$data['status']=1;
}else{
	$data['status']=0;
	$data['msg']=utf8_encode("Error! -> ".$connectdb->error." en query $query");
}

echo json_encode($data, JSON_PRETTY_PRINT);
?>

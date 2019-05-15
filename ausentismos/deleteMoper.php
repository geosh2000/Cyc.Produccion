<?php
include_once("../modules/modules.php");

$id=$_POST['id'];

$query="UPDATE Ausentismos SET Moper=NULL, ISI=0 WHERE ausent_id=$id";
if($result=Queries::query($query)){
	$data['status']=1;
	$data['type']='success';
	$data['msg']=utf8_encode("Moper eliminado en Ausentismo $id");
}else{
	$data['status']=0;
	$data['type']='error';
	$data['msg']=utf8_encode("Error -> ".Queries::error($query)." en $query");
}



//Print JSON
print json_encode($data,JSON_UNESCAPED_UNICODE);



?>



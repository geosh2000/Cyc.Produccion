<?php
include_once("../modules/modules.php");

$id=$_POST['id'];

$query="DELETE FROM Ausentismos WHERE ausent_id=$id";
if($result=Queries::query($query)){
	$query="DELETE FROM `Dias Pendientes Redimidos` WHERE id_ausentismo=$id";
	$result=Queries::query($query);
	
	$data['status']=1;
	$data['type']='success';
	$data['msg']=utf8_encode("Ausentismo $id eliminado");
}else{
	$data['status']=0;
	$data['type']='error';
	$data['msg']=utf8_encode("Error -> ".Queries::error($query)." en $query");
}



//Print JSON
print json_encode($data,JSON_UNESCAPED_UNICODE);



?>


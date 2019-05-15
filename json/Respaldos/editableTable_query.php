<?php

include_once("../modules/modules.php");

$queryIns=$_POST['queryIns'];
$queryUpd=$_POST['queryUpd'];

if(!$result=Queries::query($queryIns)){
	if(!$result=Queries::query($queryUpd)){
		$data['status']='Error';
		$data['msg']=utf8_encode(Queries::error($queryUpd));
	}else{
		$data['status']='OK';
		$data['msg']=utf8_encode("Validacion Exitosa de registro");
	}
}else{
	$data['status']='OK';
	$data['msg']=utf8_encode("Validacion Exitosa de registro");
}

print json_encode($data, JSON_PRETTY_PRINT);
?>
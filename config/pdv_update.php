<?php

include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$id=$_POST['id'];
$field=$_POST['field'];
$newVal="'".utf8_decode($_POST['newVal'])."'";
if($_POST['newVal']==""){$newVal="NULL";}


switch($field){
	case "apertura":
	case "cierre":
	  timeAndRegion::setRegion('Cun');
		if($newVal!="" && $newVal!="NULL"){
			$newVal="'".date('Y-m-d',strtotime($_POST['newVal']))."'";
		}else{
			$newVal='NULL';
		}

    if($newVal=='1969-12-31'){
      $newVal='NULL';
    }
		break;
	default:
		break;
}


switch($field){
	default:
		$query="UPDATE PDVs SET `$field`=$newVal WHERE id='$id'";
		break;
}


if($result=$connectdb->query($query)){

	if($field=='ciudad'){
		$query="UPDATE asesores_plazas SET ciudad=$newVal WHERE oficina=$id";
		if($result=$connectdb->query($query)){
			$data['status']=1;
		}else{
			$data['status']=0;
			$data['msg']=utf8_encode("ERROR! Cambios en PDV correctos, error al actualizar plazas -> ".$connectdb->error." ON $query");
		}
	}else{
		$data['status']=1;
	}

}else{
	$data['status']=0;
	$data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);





?>

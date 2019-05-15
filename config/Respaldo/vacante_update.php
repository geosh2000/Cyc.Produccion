<?php

include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$id=$_POST['id'];
$field=$_POST['field'];
$newVal="'".utf8_decode($_POST['newVal'])."'";
if($_POST['newVal']==""){$newVal="NULL";}


switch($field){
	case "inicio":
	case "inicio":
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
	case 'Activo':
		if($newVal=="'0'"){
			$query="UPDATE asesores_plazas SET `$field`=$newVal, Status=2 WHERE id='$id'";
		}else{
			$query="UPDATE asesores_plazas SET `$field`=$newVal, Status=0 WHERE id='$id'";
		}
//$data['query']=utf8_encode($query);
		break;
	default:
		$query="UPDATE asesores_plazas SET `$field`=$newVal WHERE id='$id'";
		break;
}


if($result=$connectdb->query($query)){
	$data['status']=1;

}else{
	$data['status']=0;
	$data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$data['newVal']=utf8_encode($newVal);
$data['query']=utf8_encode($query);

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);





?>

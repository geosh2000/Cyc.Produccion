<?php

include_once("../../modules/modules.php");

$fecha=$_GET['fecha'];
$skill=$_GET['skill'];
$field=$_GET['field'];
$newVal="'".$_GET['newVal']."'";
if($_GET['newVal']==""){$newVal="NULL";}


$query="INSERT INTO forecast_volume (Fecha, skill, $field) VALUES ('$fecha','$skill',$newVal)";
if(!$result=Queries::query($query)){
	$query="UPDATE forecast_volume SET `$field`=$newVal WHERE Fecha='$fecha' AND skill=$skill";
	if(!$result=Queries::query($query)){
		echo "status- ERROR -status msg- Error al actualizar $skill // $fecha // $field -msg";
    }else{
		echo "status- OK -status msg- Validacion Exitosa de registro UPDATE $skill // $fecha -msg";
	}
}else{
	echo "status- OK -status msg- Validacion Exitosa de registro $skill // $fecha // $field -msg";
}

?>
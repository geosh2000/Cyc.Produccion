<?php
include("../connectMYSQLI.php");
//Build Info

$newDate=date('Y-m-d H:i:s',strtotime($_POST['newVal']));
$id=$_POST['id'];
$updatedBy=$_POST['updatedBy'];

$query="UPDATE bo_tipificacion SET val_changed_original=fecha_recepcion, fecha_recepcion='$newDate', updated_by=$updatedBy WHERE id=$id";
if ($result=$connectdb->query($query)) {
	echo "Done";
}else{
	echo "Error: ".$connectdb->error;
}
unset($result);

?>
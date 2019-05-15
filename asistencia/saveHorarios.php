<?php
include("../connectDB.php");
date_default_timezone_set('America/Bogota');

$asesor=$_POST['asesor'];
$horarios =substr($_POST['horarios'],0,-1);
$originales =substr($_POST['originales'],0,-1);
$inicio=$_POST['inicio'];
$fin=$_POST['fin'];

$query="UPDATE `Historial Programacion` SET asesor=".($asesor*(-1))." WHERE asesor=$asesor AND Fecha BETWEEN '$inicio' AND '$fin'";
if($result=$connectdb->query($query)){
	echo "DONE!";
}else{
	echo $connectdb->error." ON $query";
}

$query="UPDATE `Historial Programacion` SET asesor=$asesor WHERE id IN ($horarios)";
if($result=$connectdb->query($query)){
	echo "DONE!";
}else{
	echo $connectdb->error." ON $query";
}



?>
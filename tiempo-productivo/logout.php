<?php
include("../connectDB.php");
session_start();

$cun_time = new DateTimeZone('America/Bogota');

$asesor=$_POST['asesor'];
$Fecha=date('Y-m-d',strtotime($_POST['fecha']));
$horario=$_POST['horario'];

if(isset($_GET['asesor'])){
	$asesor=$_GET['asesor'];
	$Fecha=date('Y-m-d',strtotime($_GET['fecha']));
}

$query="INSERT INTO horarios_check (asesor, Fecha, horario_aceptado) VALUES ($asesor, '$Fecha', '$horario')";
if($result=$connectdb->query($query)){
	echo "Done";
	session_destroy();	
}else{
	$query="UPDATE horarios_check SET horario_aceptado='$horario' WHERE asesor=$asesor AND Fecha='$Fecha'";
	if($result=$connectdb->query($query)){
		echo "Done";	
		session_destroy();
	}else{
		echo "Error";
	}
}

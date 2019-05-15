<?php
include("../connectDB.php");

$asesor=$_POST['asesor'];
$respuesta=$_POST['respuesta'];
$producto=$_POST['producto'];

if($resultado=$connectdb->query("INSERT INTO Experiencias_Xcaret (asesor,$producto) VALUES ($asesor,'$respuesta')")){
	echo "Done!";
}else{
	if($resultado=$connectdb->query("UPDATE Experiencias_Xcaret SET $producto=$respuesta WHERE asesor=$asesor")){
		echo "Done!";	
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
}



?>
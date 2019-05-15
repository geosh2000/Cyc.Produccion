<?php
include("../modules/modules.php");

//Build Info
$fecha=$_POST['fecha'];
$asesor=$_POST['asesor'];
$skill=$_POST['skill'];
$tipo=$_POST['tipo'];
$espacios=$_POST['content'];
$open=$_POST['open'];

$connectdb=Connection::mysqliDB('CC');

$query="INSERT INTO ausentismos_calendario (Fecha, Departamento, espacios, abierto, asesor) VALUES ('$fecha', $skill, $espacios, $open, $asesor)";
if($result=$connectdb->query($query)){
	echo "Success";
}else{
	
	if($connectdb->errno==1062){
			
		$query="UPDATE ausentismos_calendario SET espacios=$espacios, abierto=$open, asesor=$asesor WHERE Fecha='$fecha' AND Departamento=$skill";
		if($result=$connectdb->query($query)){
			echo "Success";
		}else{
			echo $connectdb->error."<br>Query: $query";
		}
		
	}else{
		echo $connectdb->error."<br>Query: $query";
	}
}

$connectdb->close();

exit;
?>



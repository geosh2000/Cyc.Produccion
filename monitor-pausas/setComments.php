<?php
include("../connectDB.php");

//Build Info

$id=$_POST['id'];
$asesor=$_POST['asesor'];
$comments=utf8_encode($_POST['comentarios']);

$query="INSERT INTO pausas_incidencias (id_pausa, comentarios, creado) VALUES ('$id','$comments','$asesor')";
if ($result=$connectdb->query($query)){
	echo "OK";
}else{
	$query="UPDATE pausas_incidencias SET comentarios='$comments', creado='$asesor' WHERE id_pausa=$id";
	if ($result=$connectdb->query($query)){
		echo "OK";
	}else{
		echo "Error: ".$connectdb-error;
	}
}

?>
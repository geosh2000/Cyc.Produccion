<?php
include("../connectDB.php");

$asesor=$_POST['asesor'];
$respuesta=$_POST['respuesta'];
$evento=$_POST['evento'];

if($respuesta=='si'){
	$query="INSERT INTO Fams (asesor,asistira,Evento) VALUES ($asesor,1,'$evento')";
	
}else{
	$query="UPDATE Fams SET asistira=0 WHERE asesor=$asesor AND Evento='$evento'";
}

mysql_query($query);

if(mysql_error()){
	if($respuesta=='si'){
		$query="UPDATE Fams SET asistira=1 WHERE asesor=$asesor AND Evento='$evento'";
		mysql_query($query);
		if(mysql_error()){
			echo "error: ".mysql_error();	
		}else{
			echo "DONE!";	
		}
	}else{
		echo "error: ".mysql_error();	
	}
}else{
	echo "DONE!";	
}


?>
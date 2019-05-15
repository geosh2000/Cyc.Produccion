<?php
include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$asesor=$_POST['asesor'];
$respuesta=$_POST['respuesta'];
$evento=$_POST['evento'];

if($respuesta=='si'){
	$query="INSERT INTO Fams (asesor,asistira,Evento) VALUES ($asesor,1,'$evento')";

}else{
	$query="UPDATE Fams SET asistira=0 WHERE asesor=$asesor AND Evento='$evento'";
}

if($result=$connectdb->query($query)){
  echo "DONE!";
}else{
  if($respuesta=='si'){
		$query="UPDATE Fams SET asistira=1 WHERE asesor=$asesor AND Evento='$evento'";
		if($result=$connectdb->query($query)){
      echo "DONE!";
    }else{
      echo "error: ".$connectdb->error;
    }
  }else{
    echo "error: ".$connectdb->error;
  }
}


$connectdb->close();


?>

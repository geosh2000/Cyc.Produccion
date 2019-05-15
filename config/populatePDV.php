<?php
include_once("../modules/modules.php");

if($_GET['contra']!='amigofiel'){ echo "ERROR EN CONTRA"; exit; }

$connectdb=Connection::mysqliDB('CC');

$query="SELECT id, Ingreso FROM Asesores WHERE `id Departamento`=29";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    if($fila['Ingreso']==NULL){
      $ingreso='1969-01-01';
    }else{
      $ingreso=$fila['Ingreso'];
    }
    $query="INSERT INTO asesores_pdv (asesor,fecha) VALUES (".$fila['id'].",'".$fila['Ingreso']."')";
    if($resultado=$connectdb->query($query)){
      echo $fila['id']." -> OK<br>";

    }else{
      echo $fila['id']." -> ERROR: ".$connectdb->error." <- ON<br>$query<br><br>";
    }
  }
}else{
  echo "ERROR! -> ".$connectdb->error." <- ON $query";
}

$connectdb->close()



 ?>

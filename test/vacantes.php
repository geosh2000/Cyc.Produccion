<?php

include_once("../modules/modules.php");

if($_GET['clave']!='amigofiel'){ echo "protección activada"; exit;}

$connectdb=Connection::mysqliDB('CC');

$query="SELECT * FROM Asesores WHERE Activo=1 AND `id Departamento` IN (1,2,3,4,5,6,7,8,9,10,11,12,15,16,17,19,20,21,22,23,24,25,26,27,32,35,41,42,45,46,29)";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    echo $fila['id'].": ".$fila['N Corto']." -> ";
    $query="INSERT INTO asesores_plazas (departamento, puesto, inicio, fin, oficina, ciudad, esquema) VALUES (".$fila['id Departamento'].", ".$fila['puesto'].", '".$fila['Ingreso']."', '2030-01-01', 137, 1808, ".$fila['Esquema'].")";
    if($resultado=$connectdb->query($query)){
      $inserted=$connectdb->insert_id;
      echo "OK vacantesDB -> ";
      $query="INSERT INTO asesores_movimiento_vacantes (vacante, fecha_out, fecha_in, asesor_in, userupdate) VALUES ($inserted, '".$fila['Ingreso']."', '".$fila['Ingreso']."', ".$fila['id'].", 170)";
      if($insertid=$connectdb->query($query)){
        echo "OK movimientosDB INSERT<br>";

        $query="UPDATE Asesores SET plaza=$inserted WHERE id=".$fila['id'];
        if($insertid=$connectdb->query($query)){
          echo "OK Asesors UPDATE<br>";
        }else{
          echo "ERROR UPDATE AsesoresDB ---> ".$connectdb->error." <--- ON $query<br><br>";
        }
      }else{
        echo "ERROR ISERT movimientosDB ---> ".$connectdb->error." <--- ON $query<br><br>";
      }
    }else{
      echo "ERROR vacantesDB ---> ".$connectdb->error." <--- ON $query<br><br>";
    }
  }
}else{
  echo "ERROR ---> ".$connectdb->error." <--- ON $query<br><br>";
}

 ?>

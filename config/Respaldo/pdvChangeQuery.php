<?php
include_once("../modules/modules.php");

session_start();

$updater=$_SESSION['asesor_id'];

$type=$_POST['tipo'];

$vacante=$_POST['vacante'];
$fecha=$_POST['fecha'];
$asesor=$_POST['asesor'];

$connectdb=Connection::mysqliDB('CC');

switch($type){
  case 'out':
    $query="SELECT * FROM asesores_movimiento_vacantes WHERE fecha_in='$fecha' AND asesor_in=$asesor";
    if($result=$connectdb->query($query)){
      if($result->num_rows>0){
        $fila=$result->fetch_assoc();
        $query="UPDATE asesores_movimiento_vacantes SET fecha_in=NULL, asesor_in=NULL WHERE id=".$fila['id'];
        if($result=$connectdb->query($query)){
          $data['status']=1;
          $data['msg']=utf8_encode("Movimiento 'OUT' del asesor correcto");
        }else{
          $data['status']=0;
          $data['msg']=utf8_encode("Error al insertar movimiento OUT -> ".$connectdb->error." || $query");
        }
      }else{
        $query="INSERT INTO asesores_movimiento_vacantes (vacante, fecha_out, asesor_out, userupdate) VALUES ($vacante, '$fecha', $asesor, $updater)";
        if($result=$connectdb->query($query)){
          $data['status']=1;
          $data['msg']=utf8_encode("Movimiento 'OUT' del asesor correcto");
        }else{
          $data['status']=0;
          $data['msg']=utf8_encode("Error al insertar movimiento OUT -> ".$connectdb->error." || $query");
        }
      }
    }else{
      $data['status']=0;
      $data['msg']=utf8_encode("Error al consultar DB de movimientos -> ".$connectdb->error." || $query");
    }
    break;
  case 'in':
    $query="UPDATE asesores_movimiento_vacantes SET fecha_in = NULL, asesor_in = NULL WHERE fecha_in='$fecha' AND asesor_in=$asesor";
    if($result=$connectdb->query($query)){
      $query="UPDATE asesores_movimiento_vacantes SET fecha_in=CAST('$fecha' as DATE), asesor_in=$asesor WHERE vacante=$vacante AND (asesor_in IS NULL OR fecha_in=CAST('$fecha' as DATE))";
      if($result=$connectdb->query($query)){
        $data['status']=1;
        $data['msg']=utf8_encode("Movimiento realizado correctamente");
        $data['query']=utf8_encode($query);
      }else{
        $data['status']=0;
        $data['msg']=utf8_encode("Error al actualizar pdv actual -> ".$connectdb->error." || $query");
      }
    }else{
      $data['status']=0;
      $data['msg']=utf8_encode("Error al eliminar movimientos anteriores con fecha actual -> ".$connectdb->error." || $query");
    }
    break;
}

$connectdb->close();

echo json_encode($data,JSON_PRETTY_PRINT);
?>

<?php

include_once('../modules/modules.php');

timeAndRegion::setRegion('Cun');
$connectdb=Connection::mysqliDB('CC');

$id=$_POST['id'];

$query="SELECT a.id as  puestoid, a.asesor, a.fecha, b.id as moveid, asesor_in, vacante FROM asesores_puesto a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.id_cambio WHERE a.id=$id";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $asesor=$fila['asesor'];
  $fecha=$fila['fecha'];
  $puestoid=$fila['puestoid'];
  $moveid=$fila['moveid'];
  $lastVacante=$fila['vacante'];
  $asesor_in=$fila['asesor_in'];
}

if($asesor_in!=NULL){
  $td['status']='error';
  $td['msg']=utf8_encode("ERROR! No es posible eliminar un movimiento de puesto que ya ha sido cubierto por otro asesor!");

  print json_encode($td,JSON_UNESCAPED_UNICODE);

  $connectdb->close();

  exit;
}

$query="SELECT MAX(fecha) as fecha FROM asesores_puesto WHERE asesor=$asesor";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $max=date('Y-m-d', strtotime($fila['fecha']));
}

$query="SELECT MIN(fecha) as fecha FROM asesores_puesto WHERE asesor=$asesor";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $min=date('Y-m-d', strtotime($fila['fecha']));
}

if($min==$fecha){
  $td['status']='error';
  $td['msg']=utf8_encode("No se puede eliminar el primer registro de fechas de puesto" );
  $connectdb->close();
  exit;
}

if($max==$fecha){
  $query="SELECT departamento, puesto FROM asesores_puesto WHERE asesor=$asesor AND fecha<'$max' ORDER BY fecha DESC LIMIT 1";
  if($result=$connectdb->query($query)){
    $fila=$result->fetch_assoc();
    $last=$fila['departamento'];
    $lastpuesto=$fila['puesto'];

    $query="UPDATE Asesores SET `id Departamento`=$last, puesto=$lastpuesto, plaza=$lastVacante, Activo=1, Egreso='2030-01-01' WHERE id=$asesor";
    if($result=$connectdb->query($query)){
      $query="DELETE FROM asesores_puesto WHERE id=$id";
      if($result=$connectdb->query($query)){
        $td['status']='success';

        $query="DELETE FROM asesores_movimiento_vacantes WHERE id=$moveid OR id_cambio=$id";
        if($result=$connectdb->query($query)){

          $query="DELETE FROM asesores_movimiento_vacantes WHERE asesor_in=$asesor AND fecha_in='$fecha'";
          if($result=$connectdb->query($query)){
            $td['status']='success';
            $td['msg']=utf8_encode("Puesto modificado correctamente en DB Asesores y eliminado de tabla de cambios");
          }else{
            $td['status']='error';
            $td['msg']=utf8_encode("ERROR! al eliminar de tabla de movimientos_vacantes -> ".$connectdb->error." || Puesto modificado correctamente en DB Asesores");
          }
        }else{
          $td['status']='error';
          $td['msg']=utf8_encode("ERROR! al eliminar de tabla de movimientos_vacantes -> ".$connectdb->error." || Puesto modificado correctamente en DB Asesores");
        }

      }else{
        $td['status']='error';
        $td['msg']=utf8_encode("ERROR! al eliminar de tabla de cambios -> ".$connectdb->error." || Puesto modificado correctamente en DB Asesores");
      }

    }else{
      $td['status']='error';
      $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query || ERROR en DB Asesores" );
    }
  }else{
    $td['status']='error';
    $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query || ERROR al obtener información para realizar modificaciones. Ningún cambio aplicado" );
  }
}else{
  $query="DELETE FROM asesores_puesto WHERE id=$id";
  if($result=$connectdb->query($query)){
    $td['status']='success';
    $query="DELETE FROM asesores_movimiento_vacantes WHERE id=$moveid OR id_cambio=$id";
    if($result=$connectdb->query($query)){
      $td['status']='success';
      $td['msg']=utf8_encode("Puesto modificado correctamente en DB Asesores y eliminado de tabla de cambios");
    }else{
      $td['status']='error';
      $td['msg']=utf8_encode("ERROR! al eliminar de tabla de movimientos_vacantes -> ".$connectdb->error." || Puesto modificado correctamente en DB Asesores");
    }
  }else{
    $td['status']='error';
    $td['msg']=utf8_encode("ERROR! al eliminar de tabla de cambios -> ".$connectdb->error);
  }
}

print json_encode($td,JSON_UNESCAPED_UNICODE);

$connectdb->close();

 ?>

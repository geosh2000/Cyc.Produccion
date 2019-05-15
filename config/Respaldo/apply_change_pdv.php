<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');
timeAndRegion::setRegion('Cun');

$asesor=$_POST['id'];
$idPuesto=$_POST['idpuesto'];
$new=$_POST['new'];
$fecha=date('Y-m-d', strtotime($_POST['fecha']));


$query="SELECT `id Departamento` as dep FROM Asesores WHERE id=$asesor";
$result=$connectdb->query($query);
$fila=$result->fetch_assoc();
$check=$fila['dep'];

if($check==29){
  $query="SELECT MAX(fecha) as fecha FROM asesores_pdv WHERE asesor=$asesor";
  if($result=$connectdb->query($query)){
    $fila=$result->fetch_assoc();
    $max=date('Y-m-d', strtotime($fila['fecha']));
  }

  switch($_POST['tipo']){
    case 1:
      $query="INSERT INTO asesores_pdv (asesor, fecha, pdv) VALUES ($asesor, '$fecha', $new)";
      if($result=$connectdb->query($query)){

        $inserted=$connectdb->insert_id;

        if($fecha>$max){
          $query="UPDATE Asesores SET pdv=$new WHERE id=$asesor";
          if($result=$connectdb->query($query)){
            $td['status']='success';
            $td['msg']=utf8_encode("PDV guardado correctamente con id -> $inserted y actualizado en DB Asesores");
          }else{
            $td['status']='error';
            $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query || ERROR en Asesores // Guardado correctamente en cambios de PDV" );
          }
        }else{
          $td['status']='success';
          $td['msg']=utf8_encode("PDV guardado correctamente con id -> ".$inserted);
        }
      }else{
        $td['status']='error';
        $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
      }
      break;
    case 2:
      $query="UPDATE asesores_pdv SET pdv=$new WHERE id=$idPuesto";
      if($result=$connectdb->query($query)){
        if($fecha>=$max){
          $query="UPDATE Asesores SET pdv=$new WHERE id=$asesor";
          if($result=$connectdb->query($query)){
            $td['status']='success';
            $td['msg']=utf8_encode("PDV editado correctamente con id -> $inserted y actualizado en DB Asesores");
          }else{
            $td['status']='error';
            $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query || ERROR en Asesores // Editado correctamente en cambios de PDV" );
          }
        }else{
          $td['status']='success';
          $td['msg']=utf8_encode("PDV editado correctamente con id -> ".$idPuesto);
        }
      }else{
        $td['status']='error';
        $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
      }
      break;
  }
}else{
  $td['status']='error';
  $td['msg']=utf8_encode("El asesor debe tener asignado el departamento de PDV para poder registrarlo en los cambios de la base");
}


//return json data
print json_encode($td,JSON_PRETTY_PRINT);


$connectdb->close();
?>

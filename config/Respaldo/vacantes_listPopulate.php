<?php

include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

$fecha=date('Y-m-d', strtotime($_POST['ingreso']));
$tipo=$_POST['tipo'];

$connectdb=Connection::mysqliDB('CC');


if($_POST['pdv']==1){
  $limitPDV=" AND a.departamento=29 ";
}

switch($tipo){
  case "dep":
  $oficina=$_POST['oficina'];

    $query="SELECT a.departamento as depid, c.Departamento FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN PCRCs c ON a.departamento=c.id LEFT JOIN PCRCs_puestos d ON a.puesto=d.id
    				WHERE a.inicio <= '".$fecha."' AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) AND (reemplazable!=0 OR reemplazable IS NULL) AND a.Activo=1 AND a.Status=1 AND asesor_in IS NULL AND a.oficina=$oficina $limitPDV GROUP BY depid ORDER BY c.Departamento";
    if($result=$connectdb->query($query)){
      while($fila=$result->fetch_assoc()){
        $data['vac'][]=array(
            'id' => $fila['depid'],
            'desc' => utf8_encode($fila['Departamento'])
          );
      }
      $data['error']=0;
    }else{
      $data['error']=1;
      $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
    }
    break;
  case "puesto":
    $dep=$_POST['dep'];
    $oficina=$_POST['oficina'];
    $query="SELECT a.id as plaza, a.departamento as depid, a.puesto as puestoid, c.Departamento, d.Puesto, if(fecha_out IS NULL, inicio, fecha_out) as inicio, fin, esquema, if(b.comentarios IS NULL, a.comentarios, b.comentarios) as comentarios, NombreAsesor(asesor_out,1) as AsesorOut FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN PCRCs c ON a.departamento=c.id LEFT JOIN PCRCs_puestos d ON a.puesto=d.id
    				WHERE a.inicio <= '".$fecha."' AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) AND (reemplazable!=0 OR reemplazable IS NULL) AND a.Activo=1 AND a.Status=1 AND asesor_in IS NULL AND a.departamento=$dep AND a.oficina=$oficina $limitPDV ORDER BY d.Puesto";
    if($result=$connectdb->query($query)){
      while($fila=$result->fetch_assoc()){
        $data['vac'][]=array(
            'id' => $fila['puestoid'],
            'desc' => utf8_encode($fila['Puesto']." ->".$fila['esquema']."<- (".$fila['inicio']." - ".$fila['fin'].") || (".$fila['AsesorOut'].")"),
            'esquema' => $fila['esquema'],
            'plaza' => $fila['plaza']
          );
      }
      $data['error']=0;
    }else{
      $data['error']=1;
      $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
    }
    break;
  case "ciudad":
    $query="SELECT a.ciudad as ciudadid, e.Ciudad FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN db_municipios e ON a.ciudad=e.id
    				WHERE a.inicio <= '".$fecha."' AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) AND (reemplazable!=0 OR reemplazable IS NULL) AND a.Activo=1 AND a.Status=1 AND asesor_in IS NULL $limitPDV GROUP BY ciudadid ORDER BY e.Ciudad";
    if($result=$connectdb->query($query)){
      while($fila=$result->fetch_assoc()){
        $data['vac'][]=array(
            'id' => $fila['ciudadid'],
            'desc' => utf8_encode($fila['Ciudad'])
          );
      }
      $data['error']=0;
    }else{
      $data['error']=1;
      $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
    }
    break;
  case "pdv":
    $ciudad=$_POST['ciudad'];
    $query="SELECT a.oficina as oficinaid, e.PDV as Oficina FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN PDVs e ON a.oficina=e.id
    				WHERE a.inicio <= '".$fecha."' AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) AND (reemplazable!=0 OR reemplazable IS NULL) AND asesor_in IS NULL AND a.Activo=1 AND a.Status=1 AND a.ciudad=$ciudad $limitPDV GROUP BY oficinaid ORDER BY e.PDV";
    if($result=$connectdb->query($query)){
      while($fila=$result->fetch_assoc()){
        $data['vac'][]=array(
            'id' => $fila['oficinaid'],
            'desc' => utf8_encode($fila['Oficina'])
          );
      }
      $data['error']=0;
    }else{
      $data['error']=1;
      $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
    }
    break;
}

if(!isset($data['vac'])){
  $data['error']=1;
  $data['msg']=utf8_encode("No hay vacantes para la fecha elegida");
}

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);

 ?>

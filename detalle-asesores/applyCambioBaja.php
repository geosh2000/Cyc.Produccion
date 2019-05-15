<?php
include_once("../modules/modules.php");

session_start();

$connectdb=Connection::mysqliDB('CC');
timeAndRegion::setRegion('Cun');

$input['tipo']=$_POST['tipo'];
$input['asesor']=$_POST['asesor'];
$input['fecha_in']=$_POST['fecha'];
$input['vacante']=$_POST['vacante'];
$input['solicitante']=$_POST['solicitante'];
$input['idSol']=$_POST['idSol'];

if($_POST['replace']==1){
  $input['replace']=1;
  $input['fecha_out']=$_POST['fecha_out'];
}else{
  $input['replace']=0;
  $input['fecha_out']=$_POST['fecha'];
}


$input['updater']=$_SESSION['asesor_id'];



function setOut(){
  GLOBAL $connectdb,  $input, $vac_off;

  $query="SELECT getLastVacante(".$input['asesor'].",1) as Last";
    if($result=$connectdb->query($query)){
      $fila=$result->fetch_assoc();
      $lastMove=$fila['Last'];

      $query="SELECT vacante, fecha_in FROM asesores_movimiento_vacantes WHERE id=$lastMove";
      if($result=$connectdb->query($query)){
        $fila=$result->fetch_assoc();
        $vac_off=$fila['vacante'];
        $last_fecha_in=$fila['fecha_in'];
        $data['vac_off']=$vac_off;

        if($input['replace']==1){
          if(date('Y-m-d',strtotime($last_fecha_in))>=date('Y-m-d',strtotime($input['fecha_out']))){
            $data['status']=0;
            $data['error']=utf8_encode("No es posible fijar la ficha final de una vacante que cuenta con cambios posteriores a la fecha de liberacion -> $last_fecha_in || ".$input['fecha_out']);

            return $data;
          }
        }

        if(date('Y-m-d', strtotime($last_fecha_in))>=date('Y-m-d', strtotime($input['fecha_in']))){
          $data['status']=0;
          $data['error']="No es posible asignar cambios con fechas anteriores al ultimo registrado";

          return $data;
        }

        $query="INSERT INTO asesores_movimiento_vacantes (vacante, fecha_out, asesor_out, userupdate) VALUES ($vac_off, '".$input['fecha_out']."', ".$input['asesor'].", ".$input['updater'].")";
        if($result=$connectdb->query($query)){

          if($input['tipo']==2){
            $query="UPDATE Asesores SET Egreso='".$input['fecha_in']."' WHERE id=".$input['asesor'];
            $connectdb->query($query);
          }

          $data['status']=1;
          return $data;
        }else{
          $data['status']=0;
          $data['error']=utf8_decode($connect->error." ON $query");

          return $data;
        }
      }else{
        $data['status']=0;
        $data['error']=utf8_decode($connect->error." ON $query");

        return $data;
      }
    }else{
      $data['status']=0;
      $data['error']=utf8_decode($connect->error." ON $query");

      return $data;
    }
}

function setIn(){
  GLOBAL $connectdb,  $input;
  $query="SELECT * FROM asesores_movimiento_vacantes WHERE vacante=".$input['vacante']." AND fecha_in IS NULL ORDER BY fecha_out DESC LIMIT 1";
    if($result=$connectdb->query($query)){
      $fila=$result->fetch_assoc();
      $movimiento=$fila['id'];

      $query="UPDATE asesores_movimiento_vacantes SET fecha_in='".$input['fecha_in']."', asesor_in=".$input['asesor'].", userupdate=".$input['updater']." WHERE id=$movimiento";
        if($result=$connectdb->query($query)){
          $data['status']=1;
          return $data;
        }else{
          $data['status']=0;
          $data['error']=utf8_decode($connect->error." ON $query");

          return $data;
        }
    }else{
      $data['status']=0;
      $data['error']=utf8_decode($connect->error." ON $query");

      return $data;
    }
}

function notReplace(){
  GLOBAL $connectdb,  $input, $data;
  $query="UPDATE asesores_plazas SET Activo=0, fin='".$input['fecha_in']."' WHERE id=".$data['out']['vac_off'];
  if($result=$connectdb->query($query)){
    $dat['status']=1;
    return $dat;
  }else{
    $dat['status']=0;
    $dat['error']=utf8_decode($connect->error." ON $query");

    return $dat;
  }
}

$status['in']=1;
$status['out']=1;
$status['replace']=1;

switch($input['tipo']){
  case 1:
    $data['out']=setOut();
    if($data['out']['status']==1){

      $status['out']=1;

      if($input['replace']==0){
        $data['replace']=notReplace();

        if($data['replace']['status']==1){
          $status['replace']=1;
        }else{
          $status['replace']=0;
        }
      }else{
        $status['replace']=1;
      }

      $data['in']=setIn();

      if($data['in']['status']==1){
        $status['in']=1;
      }else{
        $status['in']=0;
      }
    }else{
      $status['out']=0;
    }

    break;

  case 2:
    $data['out']=setOut();
    if($data['out']['status']==1){

      $status['out']=1;

      if($input['replace']==0){
        $data['replace']=notReplace();

        if($data['replace']['status']==1){
          $status['replace']=1;
        }else{
          $status['replace']=0;
        }
      }else{
        $status['replace']=1;
      }
    }else{
      $status['out']=0;
    }

    break;
}

$flag=1;

foreach($status as $type => $result){
  if($result==0){
    $flag=0;
    @$td['msg'].=utf8_decode("$type -> ".$data[$type]['error']." || ");
  }
}

$td['status']=$flag;

if(!isset($td['msg'])){
  $td['msg']=utf8_encode("Cambio aplicado correctamente");
}

if($td['status']==1){
  $td['status_noty']='success';
}else{
  $td['status_noty']='error';
}

//return json data
print json_encode($td,JSON_PRETTY_PRINT);


//Mailing
if($td['status']==1){
    
    $query="UPDATE rrhh_solicitudesCambioBaja SET status=1, comentariosRRHH='Cambio Aprobado', aprobado_por=".$_SESSION['asesor_id'].", fecha_aprobacion='".date('Y-m-d H:i:s')."' WHERE id=".$input['idSol'];
    $connectdb->query($query);
    
    $query="INSERT INTO 
                dep_asesores (Fecha, asesor, dep, puesto, esquema_vacante) 
            SELECT 
                t.Fecha, t.id, t.dep, t.puesto, u.esquema 
            FROM 
                (SELECT 
                    Fecha, id, 
                    IF(Egreso > Fecha, IF(Ingreso<=Fecha,GETDEPARTAMENTO(id, Fecha),NULL), NULL) AS dep,
                    IF(Egreso > Fecha, IF(Ingreso<=Fecha,GETPUESTO(id, Fecha),NULL), NULL) AS puesto, 
                    getVacanteAsesor(id,Fecha) as vacante 
                FROM Fechas 
                JOIN Asesores 
                WHERE Fecha BETWEEN '".date('Y-m-d', strtotime($input['fecha_in']))."' AND '".date('Y-m-d', strtotime($input['fecha_in'].' +90 days'))."' 
                AND id=".$input['asesor']."
                HAVING dep IS NOT NULL) t 
            LEFT JOIN 
                asesores_plazas u ON t.vacante=u.id 
            ON DUPLICATE KEY UPDATE dep=t.dep, puesto=t.puesto, esquema_vacante=u.esquema";
    $connectdb->query($query);
    
    
  $m_data['status']=1;
  $m_data['comentarios']='';
    
    
      
  switch($input['tipo']){
    case 1:
      include_once('../mailing/cambio_puestoOK.php');
      break;
    case 2:
      include_once('../mailing/bajaOK.php');
      break;
  }
}


$connectdb->close();
?>
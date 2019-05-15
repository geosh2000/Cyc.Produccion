<?php
include_once('../modules/modules.php');

session_start();

$connectdb=Connection::mysqliDB('CC');

$asesor=$_POST['id'];
$tipo=$_POST['tipo'];
$fecha=$_POST['fecha'];
$vacante=$_POST['vacante'];
$solicitud=$_POST['solicitud'];
$commRRHH=$_POST['comRRHH'];

if(isset($_POST['rhid'])){
    $rhUpdate=", aprobado_por=".$_POST['rhid'].", fecha_aprobacion= '".date('Y-m-d H:i:s')."' ";
}else{
    $rhUpdate="";
}

if($_POST['replace']=='true'){
    $replace=1;
    $f_replace="'".date('Y-m-d', strtotime($_POST['f_replace']))."'";
}else{
    $replace=0;
    $f_replace="NULL";
}





$comentarios=utf8_decode($_POST['commentarios']);

if($tipo==3){
    $query="UPDATE rrhh_solicitudesCambioBaja SET status=4, comentariosRRHH='".utf8_decode($commRRHH)."'$rhUpdate WHERE id=$solicitud";
    if($connectdb->query($query)){
        $td['status']=1;
    }else{
        $td['status']=0;
        $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
    }
}elseif($tipo==4){
    $query="UPDATE rrhh_solicitudesCambioBaja SET status=3, comentariosRRHH='".utf8_decode($commRRHH)."'$rhUpdate WHERE id=$solicitud";
    if($connectdb->query($query)){
        $td['status']=1;
        $td['tipo']='Declinado: '.$solicitud;
    }else{
        $td['status']=0;
        $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
    }
}elseif($tipo==5){
    $query="UPDATE rrhh_solicitudesCambioBaja SET status=2, comentariosRRHH='".utf8_decode($commRRHH)."'$rhUpdate WHERE id=$solicitud";
    if($connectdb->query($query)){
        $td['status']=1;
        $td['tipo']='En Proceso: '.$solicitud;
    }else{
        $td['status']=0;
        $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
    }
}else{
    $query="SELECT COUNT(*) sols FROM rrhh_solicitudesCambioBaja WHERE asesor=$asesor AND status IN (0,2)";
    if($result=$connectdb->query($query)){
        $fila=$result->fetch_assoc();
        if($fila['sols']>0){
            $td['status']=0;
            $td['msg']=utf8_encode("Existe una solicitud pendiente o en proceso. No es posible tener dos solicitudes pendientes al mismo tiempo. Cancela la actual o solicita a RRHH la resolucion de la misma");
        }else{
            switch($tipo){
                case 1:
                    $query="INSERT INTO rrhh_solicitudesCambioBaja (asesor, tipo, fecha, vacante, reemplazable, fecha_replace, comentarios, solicitado_por)
                        VALUES ($asesor, $tipo, '$fecha', $vacante, $replace, $f_replace, '$comentarios', ".$_SESSION['asesor_id']." )";
                    break;
                case 2:
                    $query="INSERT INTO rrhh_solicitudesCambioBaja (asesor, tipo, fecha, reemplazable, fecha_replace, comentarios, solicitado_por)
                        VALUES ($asesor, $tipo, '$fecha', $replace, $f_replace, '$comentarios', ".$_SESSION['asesor_id']." )";
                    break;
            }
            if($connectdb->query($query)){
                $td['status']=1;
            }else{
                $td['status']=0;
                $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
            }
        }
    }
}








$query="SELECT vacante, fecha_in FROM asesores_movimiento_vacantes WHERE id=getLastVacante($asesor,1)";
if($result=$connectdb->query($query)){
    $fila=$result->fetch_assoc();
    $vac_off=$fila['vacante'];
}

$query="SELECT tipo, solicitado_por, asesor, fecha, fecha_replace, reemplazable FROM rrhh_solicitudesCambioBaja WHERE id=$solicitud";
if($result=$connectdb->query($query)){
    $fila=$result->fetch_assoc();
    $kind=$fila['tipo'];
    $solicitante=$fila['solicitado_por'];
    
    if($tipo==3 || $tipo==4 || $tipo==5){
        $asesor=$fila['asesor'];
        $fecha=$fila['fecha'];
        $fecha_out=$fila['recha_out'];
        $replace=$fila['reemplazable'];
    }
}

if($tipo==1 || $tipo==2){
    $kind=$tipo;
} 

//Mailing
if($td['status']==1){
    
    $td['tipo']=$tipo;
    
    //INICIO TEST
    $input['tipo']=$tipo;
    $input['asesor']=$asesor;
    $input['fecha_in']=$fecha;
    $input['vacante']=$vacante;
    $input['solicitante']=$solicitante;
    $input['idSol']=$solicitud;

    if($replace==1){
      $input['replace']=1;
      $input['fecha_out']=$f_replace;
    }else{
      $input['replace']=0;
      $input['fecha_out']=$fecha;
    }

    $input['updater']=$_SESSION['asesor_id'];
    //FIN TEST
    
    $td['inputs']=$input;

    switch($kind){
        case 1:
            switch($tipo){
                case 1:
                    $td['included']='cambio_puestoSOL';
                  include_once('../mailing/cambio_puestoSOL.php');
                  break;
                case 2:
                    $td['included']='bajaSOL';
                  include_once('../mailing/bajaSOL.php');
                  break;
                case 3:
                    $td['included']='cxlOK';
                  $m_data['comentarios']=$commRRHH;
                  include_once('../mailing/cxlOK.php');
                  break;
                case 4:
                    $td['included']='cambio_puestoOK';
                  $m_data['status']=2;
                  $m_data['comentarios']=$commRRHH;
                  include_once('../mailing/cambio_puestoOK.php');
                  break;
                case 5:
                    $td['included']='epOK';
                  $m_data['comentarios']=$commRRHH;
                  include_once('../mailing/epOK.php');
                  break;

              }
            break;
        case 2:
            switch($tipo){
                case 1:
                    $td['included']='cambio_puestoSOL';
                  include_once('../mailing/cambio_puestoSOL.php');
                  break;
                case 2:
                    $td['included']='bajaSOL';
                  include_once('../mailing/bajaSOL.php');
                  break;
                case 3:
                    $td['included']='cxlOK';
                  $m_data['comentarios']=$commRRHH;
                  include_once('../mailing/cxlOK.php');
                  break;
                case 4:
                    $td['included']='bajaOK';
                  $m_data['status']=2;
                  $m_data['comentarios']=$commRRHH;
                  include_once('../mailing/bajaOK.php');
                  break;
                case 5:
                    $td['included']='epOK';
                  $m_data['comentarios']=$commRRHH;
                  include_once('../mailing/epOK.php');
                  break;


              }
            break;
    }
    
    
  
}

//Print JSON
print json_encode($td,JSON_UNESCAPED_UNICODE);

$connectdb->close();
 ?>
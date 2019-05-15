<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");
include_once("../../common/JWT.php");
include_once("../validateToken.php");

timeAndRegion::setRegion('Cun');


    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $datos['asesor'] = $request->id;
    $datos['fechaBaja'] = $request->fechaBaja;
    $datos['comentarios'] = $request->comentarios;
    $datos['fechaLiberacion'] = $request->fechaLiberacion;
    $datos['applier'] = $request->applier;


    if($request -> reemplazable == "true"){
        $datos['rem'] = true;
    }else{
        $datos['rem'] = false;
    }

    

    function setOut($connectdb, $input){

      $query="SELECT getLastVacante(".$input['asesor'].",1) as Last";
        if($result=$connectdb->query($query)){
          $fila=$result->fetch_assoc();
          $lastMove=$fila['Last'];

          $query="SELECT vacante, fecha_in FROM asesores_movimiento_vacantes WHERE id=$lastMove";
          if($result=$connectdb->query($query)){
            $fila=$result->fetch_assoc();
            $data['vac_off']=$fila['vacante'];
            $last_fecha_in=$fila['fecha_in'];
            $data['vac_off']=$data['vac_off'];

            if($input['replace']==1){
              if(date('Y-m-d',strtotime($last_fecha_in))>=date('Y-m-d',strtotime($input['fecha_out']))){
                $data['status']=0;
                $data['error']=utf8_encode("No es posible fijar la ficha final de una vacante que cuenta con cambios posteriores a la fecha de liberacion -> $last_fecha_in || ".$input['fecha_out']);

                return $data;
              }
            }

            if(date('Y-m-d', strtotime($last_fecha_in))>=date('Y-m-d', strtotime($input['fechaBaja']))){
              $data['status']=0;
              $data['error']="No es posible asignar cambios con fechas anteriores al ultimo registrado";

              return $data;
            }

            $query="INSERT INTO asesores_movimiento_vacantes (vacante, fecha_out, asesor_out, userupdate) VALUES (".$data['vac_off'].", '".$input['fechaLiberacion']."', ".$input['asesor'].", ".$input['applier'].")";
            if($result=$connectdb->query($query)){

              if($input['tipo']=='baja'){
                $query="UPDATE Asesores SET Egreso='".$input['fechaBaja']."' WHERE id=".$input['asesor'];
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

    function notReplace($connectdb, $input, $data){
      $query="UPDATE asesores_plazas SET Activo=0, fin='".$input['$fecha_in']."' WHERE id=".$data['out']['vac_off'];
      if($result=$connectdb->query($query)){
        $dat['status']=1;
        return $dat;
      }else{
        $dat['status']=0;
        $dat['error']=utf8_decode($connect->error." ON $query");

        return $dat;
      }
    }

   $data['out']=setOut($connectdb, $datos);
    if($data['out']['status']==1){

      $status['out']=1;

      if(!$datos['rem']){
        $data['replace']=notReplace($connectdb, $datos, $data);

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

    echo json_encode($td, JSON_PRETTY_PRINT);

exit;

validateTk(function(){
    
},$tkFlag);

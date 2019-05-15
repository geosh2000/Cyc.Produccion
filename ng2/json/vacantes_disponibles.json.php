<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");
include_once("../../common/JWT.php");
include_once("../validateToken.php");

timeAndRegion::setRegion('Cun');

validateTk(function(){

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $fecha=date('Y-m-d', strtotime($request->fechaCambio));
    $tipo=$request->type;

    $puestoSolicitante = $request->puestoClave;
    $viewAllAgentsCred = $request->viewAll;

    if($viewAllAgentsCred == 1 || $puestoSolicitante=="B1"){
        $viewRestrict = "";
    }else{
        switch($puestoSolicitante){
            case "B2":
                $viewRestrict = " AND udns.id = ".$request->udnID;
                break;
            case "B3":
            case "C2":
                $viewRestrict = " AND areas.id = ".$request->areaID;
                break;

        }
    }

    $connectdb=Connection::mysqliDB('CC');

    switch($tipo){
      case "departamento":
      $oficina=$request->oficina;

        $query="SELECT a.departamento as depid, c.Departamento FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN PCRCs c ON a.departamento=c.id LEFT JOIN PCRCs_puestos d ON a.puesto=d.id  LEFT JOIN hc_codigos_Departamento deps ON a.hc_dep=deps.id LEFT JOIN hc_codigos_Areas areas ON deps.area=areas.id LEFT JOIN hc_codigos_UnidadDeNegocio udns ON areas.unidadDeNegocio=udns.id
                        WHERE a.inicio <= '".$fecha."' AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) $viewRestrict AND (reemplazable!=0 OR reemplazable IS NULL) AND a.Activo=1 AND a.Status=1 AND asesor_in IS NULL AND a.oficina=$oficina $limitPDV GROUP BY depid ORDER BY c.Departamento";
        if($result=$connectdb->query($query)){
          while($fila=$result->fetch_assoc()){
            $data['vac'][]=array(
                'id' => $fila['depid'],
                'name' => utf8_encode($fila['Departamento'])
              );
          }
          $data['error']=0;
        }else{
          $data['error']=1;
          $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
        }
        break;
      case "puesto":
        $dep=$request->departamento;
        $oficina=$request->oficina;
        $query="SELECT b.id as movimientoID, a.id as plaza, a.departamento as depid, a.puesto as puestoid, c.Departamento, d.Puesto,
        salarioPuesto(a.hc_puesto, CURDATE()) as salario,
        if(fecha_out IS NULL, inicio, fecha_out) as inicio, fin, esquema, if(b.comentarios IS NULL, a.comentarios, b.comentarios) as comentarios, NombreAsesor(asesor_out,1) as AsesorOut FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN PCRCs c ON a.departamento=c.id LEFT JOIN PCRCs_puestos d ON a.puesto=d.id LEFT JOIN hc_codigos_Departamento deps ON a.hc_dep=deps.id LEFT JOIN hc_codigos_Areas areas ON deps.area=areas.id LEFT JOIN hc_codigos_UnidadDeNegocio udns ON areas.unidadDeNegocio=udns.id

                        WHERE a.inicio <= '".$fecha."' $viewRestrict AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) AND (reemplazable!=0 OR reemplazable IS NULL) AND a.Activo=1 AND a.Status=1 AND asesor_in IS NULL AND a.departamento=$dep AND a.oficina=$oficina $limitPDV ORDER BY d.Puesto";
        if($result=$connectdb->query($query)){
          while($fila=$result->fetch_assoc()){
            $data['vac'][]=array(
    //            'id' => $fila['puestoid'],
                'id' => array(
                              'id' => $fila['puestoid'],
                              'movimientoID' => $fila['movimientoID'],
                              'vacante' => $fila['plaza'],
                              'esquema' => $fila['esquema'],
                              'depid' => $fila['depid'],
                              'puestoid' => $fila['puestoid'],
                              'salario' => $fila['salario']
                            ),
                'name' => utf8_encode($fila['Puesto']." ->".$fila['esquema']."<- (".$fila['inicio']." - ".$fila['fin'].") || (".$fila['AsesorOut'].")"),
                'esquema' => $fila['esquema'],
                'vacante' => $fila['plaza'],
                'movimientoID' => $fila['movimientoID']
              );
          }
          $data['error']=0;
        }else{
          $data['error']=1;
          $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
        }
        break;
      case "ciudad":
        $query="SELECT a.ciudad as ciudadid, e.Ciudad FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN cat_zones e ON a.ciudad=e.id LEFT JOIN hc_codigos_Departamento deps ON a.hc_dep=deps.id LEFT JOIN hc_codigos_Areas areas ON deps.area=areas.id LEFT JOIN hc_codigos_UnidadDeNegocio udns ON areas.unidadDeNegocio=udns.id
                        WHERE a.inicio <= '".$fecha."' $viewRestrict AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) AND (reemplazable!=0 OR reemplazable IS NULL) AND a.Activo=1 AND a.Status=1 AND asesor_in IS NULL $limitPDV GROUP BY ciudadid ORDER BY e.Ciudad";
        if($result=$connectdb->query($query)){
          while($fila=$result->fetch_assoc()){
            $data['vac'][]=array(
                'id' => $fila['ciudadid'],
                'name' => utf8_encode($fila['Ciudad'])
              );
          }
          $data['error']=0;
        }else{
          $data['error']=1;
          $data['msg'][]=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
        }
    //        $data['query']=utf8_encode($query);
        break;
      case "oficina":
        $ciudad=$request->ciudad;
        $query="SELECT a.oficina as oficinaid, e.PDV as Oficina FROM asesores_plazas a LEFT JOIN asesores_movimiento_vacantes b ON a.id=b.vacante LEFT JOIN PDVs e ON a.oficina=e.id LEFT JOIN hc_codigos_Departamento deps ON a.hc_dep=deps.id LEFT JOIN hc_codigos_Areas areas ON deps.area=areas.id LEFT JOIN hc_codigos_UnidadDeNegocio udns ON areas.unidadDeNegocio=udns.id
                        WHERE a.inicio <= '".$fecha."' $viewRestrict AND (fecha_out <= '".$fecha."' or fecha_out IS NULL) AND (reemplazable!=0 OR reemplazable IS NULL) AND asesor_in IS NULL AND a.Activo=1 AND a.Status=1 AND a.ciudad=$ciudad $limitPDV GROUP BY oficinaid ORDER BY e.PDV";
        if($result=$connectdb->query($query)){
          while($fila=$result->fetch_assoc()){
            $data['vac'][]=array(
                'id' => $fila['oficinaid'],
                'name' => utf8_encode($fila['Oficina'])
              );
          }
          $data['error']=0;
        }else{
          $data['error']=1;
          $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
        }
        break;
    }

    if(!isset($data['vac']) || $data['error']==1){
      $data['error']=1;
      $data['msg'][]=utf8_encode("No hay vacantes para la fecha elegida");
    }

    $connectdb->close();

    echo json_encode($data, JSON_PRETTY_PRINT);

},$tkFlag);

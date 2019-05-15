<?php
include_once('../modules/modules.php');

timeAndRegion::setRegion('Cun');


if(!isset($_POST['lu']) || $_POST['lu']==''){
  $data['status']=2;
  $data['msg']=utf8_encode('Modulo no actualizado. Por favor actualiza la pagina');
  echo json_encode($data, JSON_PRETTY_PRINT);
  exit;
}else{
  $lu=$_POST['lu'];
}

$flag=false;

$connectdb=Connection::mysqliDB('Test');

$query="SELECT date_created, IF(date_created>'$lu',1,0) as flag FROM ccexporter.mon_calls_details ORDER BY date_created DESC LIMIT 1";

$data['special']['queryLU']=utf8_encode($query);

if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $newLU=$fila['date_created'];
  if($lu=='first'){
    $flag=true;
  }else{
    if($fila['flag']==1){
      $flag=true;
    }
  }

}else{
  $data['status']=2;
  $data['msg']=utf8_encode('Error -> '.$connectdb->error." ON $query | ".$_POST['lu']);
  echo json_encode($data, JSON_PRETTY_PRINT);
  $connectdb->close();

  exit;
}

if(!$flag){
  $connectdb->close();
  $data['status']=2;
  $data['msg']=utf8_encode('BD sin actualizar, ultima actualizacion: '.$_POST['lu']);
  echo json_encode($data, JSON_PRETTY_PRINT);
  exit;
}

$data['special']['lu']=$newLU;

$query="SELECT DISTINCT cc FROM cc_apoyo WHERE '".date('Y-m-d')."' BETWEEN inicio AND fin";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $data[$fila['cc']]['MontoTotal']=0;
    $data[$fila['cc']]['Hotel']=0;
    $data[$fila['cc']]['Paquetes']=0;
    $data[$fila['cc']]['Locs']=0;
    $data[$fila['cc']]['Vuelo']=0;
    $data[$fila['cc']]['llamadas']=0;
    $data[$fila['cc']]['FC']=0;

    $apoyos[$fila['cc']]=0;

  }
}

$query="SELECT GrupoCC, locs.*, calls.llamadas, locs.Locs/llamadas*100 as FC
        FROM
        (SELECT
        	CASE
        		WHEN cc IS NULL AND Dep!=29 THEN 'CC'
        		WHEN Dep IS NULL THEN 'CC'
        		WHEN cc IS NULL AND Dep=29 THEN 'PDV'
        		WHEN cc IS NOT NULL AND dep=29 THEN cc
        	END as GrupoCC,
        	COUNT(id) as Llamadas
        FROM
        (SELECT a.*, IF(SUBSTR(Agente,1,LOCATE('(', Agente)-2)='',35,getDepartamento(getIdAsesor(SUBSTR(Agente,1,LOCATE('(', Agente)-2),2),Fecha)) as Dep, d.cc
        FROM
        (SELECT a.*, Skill FROM ccexporter.mon_calls_details a LEFT JOIN
        Cola_Skill b ON a.Cola=b.Cola WHERE Fecha=CURDATE() AND Desconexion NOT IN ('Abandon','Unanswered yet') HAVING Skill=35) a
        LEFT JOIN cc_apoyo d ON IF(SUBSTR(Agente,1,LOCATE('(', Agente)-2)='','',getIdAsesor(SUBSTR(Agente,1,LOCATE('(', Agente)-2),2))=d.asesor AND a.Fecha BETWEEN d.inicio AND d.fin) a
        GROUP BY
        GrupoCC) calls
        LEFT JOIN
        (SELECT
        	CASE
        		WHEN cc IS NULL AND dep!=29 THEN 'CC'
        		WHEN dep IS NULL THEN 'CC'
        		WHEN cc IS NULL AND dep=29 THEN 'PDV'
        		WHEN cc IS NOT NULL AND dep=29 THEN cc
        	END as Grupo,
        	SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as MontoTotal,
        	SUM(IF(Servicios LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Hotel,
        	SUM(IF(Servicios NOT LIKE '%Hotel%' AND Servicios  LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Vuelo,
        	SUM(IF(Servicios LIKE '%Hotel%' AND Servicios  LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Paquetes,
          SUM(IF(Servicios NOT LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Otros,
          COUNT(DISTINCT HotelLoc) as LocsHotel, COUNT(DISTINCT VueloLoc) as LocsVuelo, COUNT(DISTINCT PaquetesLoc) as LocsPaquete, COUNT(DISTINCT OtrosLoc) as LocsOtros,
          COUNT(DISTINCT NewLoc) as Locs
        FROM
        (SELECT
        	a.*, getDepartamento(a.asesor,Fecha) as dep, IF(Venta!=0,Localizador, NULL) as NewLoc, cc,
          CASE WHEN Venta!=0 AND Servicios LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%' THEN Localizador END as HotelLoc,
        	CASE WHEN Venta!=0 AND Servicios NOT LIKE '%Hotel%' AND Servicios  LIKE '%Vuelo%' THEN Localizador END as VueloLoc,
        	CASE WHEN Venta!=0 AND Servicios LIKE '%Hotel%' AND Servicios  LIKE '%Vuelo%' THEN Localizador END as PaquetesLoc,
        	CASE WHEN Venta!=0 AND Servicios NOT LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%' THEN Localizador END as OtrosLoc
        FROM
        	d_Locs a
        LEFT JOIN cc_apoyo b ON a.asesor=b.asesor AND Fecha BETWEEN b.inicio AND b.fin
        WHERE Fecha=CURDATE() AND chanId IN(1,2,3,4,5,11,309,332) HAVING dep NOT IN (5)) a
        GROUP BY Grupo) locs
        ON calls.GrupoCC=locs.Grupo";

if($result=$connectdb->query($query)){

  $fields=$result->fetch_fields();

  for($i=2; $i<$result->field_count; $i++){
    $data['PDV'][$fields[$i]->name]=0;
    $data['CC'][$fields[$i]->name]=0;

    foreach($apoyos as $ofi => $info){
      $data[$ofi][$fields[$i]->name]=0;
    }
  }

  while($fila=$result->fetch_array()){
    for($i=2; $i<$result->field_count; $i++){
      if($fila[$i]==NULL){$datos=0;}else{$datos=$fila[$i];}
      switch ($fields[$i]->name) {
        case 'FC':
          $data[$fila[0]][$fields[$i]->name]=number_format($datos,2);
          break;
        default:
          $data[$fila[0]][$fields[$i]->name]=$datos;
          break;
      }

    }
    @$total+=$fila[12];
  }
  //$data['lu']=date('Y-m-d H:i:s');

  foreach($data as $grupo => $info){
    $data[$grupo]['part']=$info['llamadas']/$total*100;
  }

  $data['Total']=$total;
  $data['status']=1;
}else{
  $data['status']=0;
  $data['error']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}
$connectdb->close();

print json_encode($data, JSON_PRETTY_PRINT);

 ?>

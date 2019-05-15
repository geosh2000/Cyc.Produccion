<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$inicio=$_POST['inicio'];
$fin=$_POST['fin'];

$query="SELECT a.Fecha, a.Grupo, Llamadas, Locs, Locs/Reales*100 as FC, MontoTotal/Locs as Av_Tkt, MontoTotal, Hotel, Vuelo, Paquete, Otros
        FROM
        (SELECT Fecha,
        	CASE
          		WHEN cc IS NULL AND dep!=29 THEN 'CC'
          		WHEN dep IS NULL THEN 'CC'
          		WHEN cc IS NULL AND dep=29 THEN 'PDV'
          		WHEN cc IS NOT NULL AND dep=29 THEN cc
          	END as Grupo,
        	COUNT(ac_id) as Llamadas,
        	COUNT(IF(Answered=1 AND NOT (Desconexion='Transferida' AND Duracion_Real<='00:02:00'),ac_id,NULL)) as Reales
        FROM
        (SELECT a.*, Skill, cc, getDepartamento(if(a.asesor REGEXP '^-?[0-9]+$'=1,a.asesor,NULL),Fecha) as dep FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola LEFT JOIN cc_apoyo c ON a.asesor=c.asesor AND Fecha BETWEEN c.inicio AND c.fin WHERE Fecha BETWEEN '$inicio' AND '$fin' AND Answered=1 HAVING Skill=35) a
        GROUP BY Fecha, Grupo) a
        LEFT JOIN
        (SELECT Fecha, COUNT(DISTINCT NewLoc) as Locs,
        	CASE
          		WHEN cc IS NULL AND Dep!=29 THEN 'CC'
          		WHEN Dep IS NULL THEN 'CC'
          		WHEN cc IS NULL AND Dep=29 THEN 'PDV'
          		WHEN cc IS NOT NULL AND dep=29 THEN cc
          	END as Grup,
        	SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as MontoTotal,
        	SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto,
        	SUM(IF(Servicios LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Hotel,
        	SUM(IF(Servicios NOT LIKE '%Hotel%' AND Servicios  LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Vuelo,
        	SUM(IF(Servicios LIKE '%Hotel%' AND Servicios  LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Paquete,
        	SUM(IF(Servicios NOT LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%',VentaMXN+OtrosIngresosMXN+EgresosMXN,0)) as Otros
        FROM
        (SELECT
        		a.*, cc,  getDepartamento(a.asesor,Fecha) as Dep, IF(Venta!=0,Localizador,NULL) as NewLoc
        	FROM t_Locs a
        	LEFT JOIN cc_apoyo c ON a.asesor=c.asesor AND Fecha BETWEEN c.inicio AND c.fin
        	WHERE Fecha BETWEEN '$inicio' AND '$fin' AND chanId IN(1,2,3,4,5,11,309,332) AND Venta!=0
        	HAVING Dep NOT IN (5)) a
        GROUP BY Fecha, Grup) b
        ON a.Fecha=b.Fecha AND a.Grupo=b.Grup";
if($result=$connectdb->query($query)){
  //Status OK
  $data['status']=1;

  //Build
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array()){
    for($i=0;$i<$result->field_count;$i++){
      //rows
      switch ($fields[$i]->name) {
        case 'FC':
          $info=number_format($fila[$i],2)." %";
          $class='t_right';
          break;
        case 'Fecha':
        case 'Grupo':
          $info=utf8_encode($fila[$i]);
          $class='t_center';
          break;
        case 'Locs':
        case 'Llamadas':
          $info=utf8_encode(number_format($fila[$i],0));
          $class='t_center';
          break;
        default:
          $info=utf8_encode("<span style='text-align:left';>$</span><span style='text-align:right;'>".number_format($fila[$i],2))."</span>";
          $class='t_right';
          break;
      }
      $table[$fila[0].$fila[1]][]=array("html"=> $info, "class"=>$class);
    }
  }
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("ERROR -> ".$connectdb->error." ON $query");
}

for($i=0;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$fields[$i]->name));
}

unset($result);

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($table as $id =>$info){
	$row[]=array("cells" => $info);
}

$connectdb->close();

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

$data['table']=$table;

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);
?>

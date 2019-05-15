<?php
include("../modules/modules.php");

session_start();

$inicio=date('Y-m-d', strtotime($_POST['inicio']));
$fin=date('Y-m-d', strtotime($_POST['fin']));
$dep=$_POST['dep'];
$tipo=$_POST['tipo'];

switch ($tipo) {
  case 'locs':
    $query="SELECT
            locs_id, Fecha, nombreAsesor(asesor,2) as Asesor, FindSupDay(asesor,Fecha) as Supervisor, Localizador, Afiliado, VentaMXN as Venta, OtrosIngresosMXN as Otros_Ingresos, EgresosMXN as Egresos, VentaMXN+OtrosIngresosMXN+EgresosMXN as Monto_Total
          FROM
            (SELECT dep, a.* FROM
              (SELECT Fecha, id, getDepartamento(id,Fecha) as dep FROM Fechas JOIN Asesores WHERE Fecha BETWEEN '$inicio' AND '$fin' HAVING dep=$dep) ases
            RIGHT JOIN
              (SELECT * FROM t_Locs WHERE Fecha BETWEEN '$inicio' AND '$fin' AND asesor NOT IN (-1,0)) a
            ON ases.id=a.asesor AND ases.Fecha=a.Fecha
            HAVING dep=$dep) tabla
          ORDER BY Asesor, Fecha, Localizador";
    break;
  case 'pordia':
    $query="SELECT
	locs_id, Fecha, Nombre, Supervisor, COUNT(DISTINCT DisLoc) as Localizadores_Nuevos, SUM(Venta) as Venta, SUM(Otros_Ingresos) as Otros_Ingresos, SUM(Egresos) as Egresos, SUM(Monto_Total) as Monto_Total
FROM
(SELECT
            locs_id, Fecha, nombreAsesor(asesor,2) as Nombre, FindSupDay(asesor,Fecha) as Supervisor, IF(SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN)=0,NULL,Localizador) as DisLoc, SUM(VentaMXN) as Venta, SUM(OtrosIngresosMXN) as Otros_Ingresos, SUM(EgresosMXN) as Egresos, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto_Total
          FROM
            (SELECT dep, a.* FROM
              (SELECT Fecha, id, getDepartamento(id,Fecha) as dep FROM Fechas JOIN Asesores WHERE Fecha BETWEEN '$inicio' AND '$fin' HAVING dep=$dep) ases
            RIGHT JOIN
              (SELECT *, if(Venta!=0, Localizador,NULL) as LocDis FROM t_Locs WHERE Fecha BETWEEN '$inicio' AND '$fin' AND asesor NOT IN (-1,0)) a
            ON ases.id=a.asesor AND ases.Fecha=a.Fecha
            HAVING dep=$dep) tabla
            GROUP BY Localizador
            ) total
GROUP BY Nombre, Fecha
ORDER BY Nombre, Fecha";
    break;
}

if ($result=Queries::query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=1;$i<$result->field_count;$i++){
      switch($info_field[$i]->name){
        case 'Monto_Total':
        case 'Venta':
        case 'Otros_Ingresos':
        case 'Egresos':
          $data[$fila[0]][]=utf8_encode("$".number_format($fila[$i],2));
          break;
        default:
          $data[$fila[0]][]=utf8_encode($fila[$i]);
          break;
      }
		}
  }
}

for($i=1;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$info_field[$i]->name));
}

unset($result);

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($data as $id =>$info){
  $row[]=$info;
}


//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>

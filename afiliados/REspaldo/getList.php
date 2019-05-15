<?php
include("../modules/modules.php");

session_start();

$fechai=$_POST['fechai'];
$fechaf=$_POST['fechaf'];
$reporte=$_POST['reporte'];

$veci="SELECT
        Calls.Fecha, Calls.Canal, Calls.Total, Calls.Contestadas, Calls.Abandonadas, Calls.Abandonadas/Calls.Total*100 as Abandon, Calls.AHT,Calls.ASA,Calls.Talking_Time,Calls.Total_Wait,
        Calls.SLA20, Calls.SLA20/Calls.Total*100 as SLA,
        Reservas, Monto
      FROM
        (SELECT
            a.Fecha, Canal, Canal as CH, COUNT(ac_id) as Total, COUNT(IF(Answered=1,ac_id,NULL)) as Contestadas, COUNT(IF(Answered=0,ac_id,NULL)) as Abandonadas,
            AVG(IF(Answered=1,TIME_TO_SEC(Duracion_Real),NULL)) as AHT,
            AVG(IF(Answered=1,TIME_TO_SEC(Espera),NULL)) as ASA, SUM(IF(Answered=1,TIME_TO_SEC(Duracion_Real),NULL)) as Talking_Time,
            SUM(TIME_TO_SEC(Espera)) as Total_Wait,
            COUNT(IF(Answered=1 AND Espera<='00:00:20',ac_id,NULL)) SLA20
          FROM
          (
            SELECT a.Fecha as Fecha, Canal, ac_id, Answered, Duracion_Real, Espera, Skill
            FROM t_Answered_Calls a
            LEFT JOIN
              Cola_Skill b ON a.Cola=b.Cola
            LEFT JOIN
              Dids c ON a.DNIS=c.DID
            WHERE
              a.Fecha BETWEEN '$fechai' AND '$fechaf'
            HAVING
            (c.Canal LIKE '%Veci%' OR c.Canal LIKE '%Liverpool%')  AND
            Skill=3
          ) a
          GROUP BY
            Fecha, Canal
        ) Calls
      LEFT JOIN
        (SELECT
          a.Fecha,
          CASE
            WHEN chanId IN (659,660,661,662) THEN 'Liverpool'
            WHEN chanId IN (396,397,578,579,879,880,881,882) THEN 'VECI MX'
            WHEN chanId IN (389,399,580,581) THEN 'VECI CO'
            WHEN chanId IN (843,844,845,846) THEN 'VECI PA'
          END as CH, COUNT(a.locs_id) as Reservas,
          IF(VentaMXN=0 AND OtrosIngresosMXN=0 AND EgresosMXN=0,SUM(Venta+OtrosIngresos+Egresos)*Dolar, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN)) as Monto
        FROM
          t_Locs a
        LEFT JOIN
          Fechas b ON a.Fecha=b.Fecha
        WHERE
          a.Fecha BETWEEN '$fechai' AND '$fechaf' AND
          (Afiliado LIKE '%corteingles%' OR Afiliado LIKE '%liverpool%') AND
          asesor!=-1 AND
          Venta!=0
        GROUP BY
          a.Fecha, CH
        ) Locs
      ON
        Calls.Fecha=Locs.Fecha AND
        Calls.CH=Locs.CH";

if($_SESSION['default']==0){
  $reporte=$_POST['reporte'];
}else{
  $reporte=$_POST['reporte'];
}

switch($reporte){
  case 'veci':
    $query=$veci;
      break;
  case 'copa':
    $query=$copa;
    break;
}


if ($result=Queries::query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
      switch($info_field[$i]->name){
        case 'AHT':
        case 'ASA':
          $data[$fila[0]."-".$fila[1]][]=array("text"=> utf8_encode(number_format($fila[$i],2)), "class"=>'right');
          break;
        case 'Total_Wait':
        case 'Talking_Time':
          $data[$fila[0]."-".$fila[1]][]=array("text"=> utf8_encode(number_format($fila[$i],2)), "class"=>'right');
          break;
        case 'Monto':
          $data[$fila[0]."-".$fila[1]][]=array("text"=> utf8_encode("$".number_format($fila[$i],2)), "class"=>'right');
          break;
        case 'Abandon':
        case 'SLA':
          $data[$fila[0]."-".$fila[1]][]=array("text"=> utf8_encode(number_format($fila[$i],2))."%", "class"=>'right');
          break;
        default:
          $data[$fila[0]."-".$fila[1]][]=array("text"=> utf8_encode($fila[$i]), "class" => 'center');
          break;
      }
		}
  }
}

for($i=0;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$info_field[$i]->name));
}

unset($result);

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($data as $id =>$info){
  $row[]=array("cells" => $info);
}


//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>

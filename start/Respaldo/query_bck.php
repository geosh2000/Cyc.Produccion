<?php
include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

header("content-type: application/json");
setlocale(LC_TIME,'es_ES');

//GET Info
$asesor=$_GET['asesor'];
$fecha_i=$_GET['fechai'];
$fecha_f=$_GET['fechaf'];
$type=$_GET['type'];
$md=$_GET['mdt'];
$mt=$_GET['mt'];
$dep=$_GET['dep'];

$connectdb=Connection::mysqliDB('CC');

$query="DROP TEMPORARY TABLE IF EXISTS t_locs_mon;
        DROP TEMPORARY TABLE IF EXISTS d_locs_mon;
        DROP TEMPORARY TABLE IF EXISTS t_calls_mon;
        DROP TEMPORARY TABLE IF EXISTS d_calls_mon;

        CREATE TEMPORARY TABLE t_locs_mon (SELECT
                asesor as LocalizadoresT_asesor, Fecha as LocalizadoresT_Fecha, COUNT(DISTINCT NewLoc) as LocsT, SUM(Monto) as t_Monto
              FROM
                (
                  SELECT
                    asesor, Fechas.Fecha, IF(Venta!=0,Localizador,NULL) as NewLoc, VentaMXN+OtrosIngresosMXN+EgresosMXN as Monto
                  FROM
                    Fechas
                  LEFT JOIN
                    t_Locs
                  ON
                    Fechas.Fecha=t_Locs.Fecha
                  WHERE
                    Fechas.Fecha BETWEEN '$fecha_i' AND '$fecha_f'
                    AND asesor=$asesor
                ) Locs1
              GROUP BY
                asesor,Fecha);
                
        CREATE TEMPORARY TABLE d_locs_mon (SELECT
                asesor as LocalizadoresD_asesor, Fecha as LocalizadoresD_Fecha, COUNT(DISTINCT NewLoc) as LocsD, SUM(Monto) as d_Monto
              FROM
                (
                  SELECT
                    asesor, Fecha, IF(Venta!=0,Localizador,NULL) as NewLoc, VentaMXN+OtrosIngresosMXN+EgresosMXN as Monto
                  FROM
                    d_Locs
                  WHERE
                    Fecha=CURDATE()
                    AND asesor=$asesor
                ) Locs1
              GROUP BY
                asesor,Fecha);
                
        CREATE TEMPORARY TABLE t_calls_mon (SELECT
              Fecha as calls_Fecha, 
              asesor as calls_asesor, 
              COUNT(*)-IF(COUNT(IF(Desconexion='Transferida' AND Duracion_Real<='00:02:00',ac_id,NULL)) IS NULL, 0,COUNT(IF(Desconexion='Transferida' AND Duracion_Real<='00:02:00',ac_id,NULL))) as llamadas
            FROM
              (SELECT a.*, Skill FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha BETWEEN '$fecha_i' AND '$fecha_f' AND asesor=$asesor HAVING (CASE 
                WHEN $dep=3 THEN Skill IN (3,35)
                WHEN $dep=35 THEN Skill IN (3,35)
                ELSE Skill=$dep
              END)) a
            GROUP BY
              Fecha);				
              
        CREATE TEMPORARY TABLE d_calls_mon (SELECT
              Fecha as dcalls_Fecha, asesor as dcalls_asesor, SUM(Calls) as dllamadas
            FROM
              d_PorCola
            WHERE
              Fecha=CURDATE() AND
              asesor=$asesor AND
              CASE 
                WHEN $dep=3 THEN Skill IN (3,35)
                WHEN $dep=35 THEN Skill IN (3,35)
                ELSE Skill=$dep
              END
            GROUP BY
              Fecha);	";
              
$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}

$query="SELECT
          Fecha, $asesor as id, NombreAsesor($asesor,1) as 'N Corto', IF(Fecha=CURDATE(), IF(LocsD IS NULL,0,LocsD), IF(LocsT IS NULL,0,LocsT)) as Localizadores, IF(Fecha=CURDATE(),IF(d_Monto IS NULL,0,d_Monto),IF(t_Monto IS NULL,0,t_Monto))* 1 as Monto, IF(Fecha=CURDATE(),IF(dllamadas IS NULL,0,dllamadas),IF(llamadas IS NULL,0,llamadas)) as llamadas,
            Aus, DiasHabiles, fc_meta
        FROM
          (
            SELECT
              Fecha
            FROM
              Fechas
            WHERE
              Fecha BETWEEN '$fecha_i' AND '$fecha_f'
          ) Fechas
        LEFT JOIN
            t_locs_mon LocalizadoresT
          ON
            Fecha=LocalizadoresT_Fecha
        LEFT JOIN
            d_locs_mon LocalizadoresD
          ON
            Fecha=LocalizadoresD_Fecha
        LEFT JOIN
          t_calls_mon calls
        ON
          Fecha=calls_Fecha
        LEFT JOIN
          d_calls_mon dcalls
        ON
          Fecha=dcalls_Fecha 
        LEFT JOIN
          (
            SELECT
              Fecha as Prog_Fecha, a.asesor as Prog_asesor, IF(tipo_ausentismo!=10 OR tipo_ausentismo IS NOT NULL OR (`jornada start`=`jornada end`),0,1) as Aus, DiasHabiles
            FROM
              `Historial Programacion` a
            LEFT JOIN
              Ausentismos b
            ON
              a.asesor=b.asesor AND
              Fecha BETWEEN Inicio AND Fin
            LEFT JOIN
              (
                SELECT
                  a.asesor, SUM(IF(tipo_ausentismo!=10 OR tipo_ausentismo IS NOT NULL OR (`jornada start`=`jornada end`),0,1)) as DiasHabiles
                FROM
                  `Historial Programacion` a
                LEFT JOIN
                  Ausentismos b
                ON
                  a.asesor=b.asesor AND
                  Fecha BETWEEN Inicio AND Fin
                WHERE
                  a.asesor=$asesor AND
                  Fecha Between '$fecha_i' AND '$fecha_f'
                GROUP BY
                  asesor
              ) c
            ON
              a.asesor=c.asesor
            WHERE
              a.asesor=$asesor AND
              Fecha Between '$fecha_i' AND '$fecha_f'
          ) Prog
        ON
          Fecha=Prog_Fecha
        JOIN 
          (
            SELECT meta*100 as fc_meta FROM metas_kpi	WHERE mes=MONTH('$fecha_i') AND anio=YEAR('$fecha_i') AND skill=35 AND tipo='fc'
          ) metafc";

//echo $query."<br>";
if($result=$connectdb->query($query)){
	$row_cnt = $result->num_rows;
	$rows=$result->field_count;
	$fields=$result->fetch_fields();
	$x=0;
	while($fila=$result->fetch_row()){
		for($i=0;$i<$rows;$i++){
			$data[$fields[$i]->name][$x]=$fila[$i];
		}
		$x++;
	}
}else{
	echo "Error: ".$connectdb->error;
}

//Calculated Fields
for($i=0;$i<$row_cnt;$i++){
	if(date("I", strtotime($data['Fecha'][$i]))==1){$minus="4";}else{$minus="5";} //Convert Date for HighCharts
	//$datetmp_dt=new DateTime($data['Fecha'][$i]);
	//$datetmp=strftime("%s", strtotime($data['Fecha'][$i].' -'.$minus.' hours'))*1000;
	$datetmp="Date.UTC(".date('Y',strtotime($data['Fecha'][$i])).",".(date('m',strtotime($data['Fecha'][$i]))-1).",".date('d',strtotime($data['Fecha'][$i])).")";
	//echo $data['Fecha'][$i].' -'.$minus.' hours -> '.$datetmp.'<br>';
	$xdata[$i]=$datetmp;
	
	$data['locs'][$i]=intval($data['Localizadores'][$i]);
	@$data['metadiaria'][$i]=intval($mt/intval($data['DiasHabiles'][$i]));
	
	$acumulado['Monto']+=$data['Monto'][$i];
    $acumulado['Localizadores']+=$data['Localizadores'][$i];
    $acumulado['Llamadas']+=$data['llamadas'][$i];
    
    @$data['acumfc'][$i]=$acumulado['Localizadores']/$acumulado['Llamadas']*100;
    @$data['acummonto'][$i]=intval($acumulado['Monto']);
    @$data['arfc'][$i]=floatval($data['Localizadores'][$i]/$data['llamadas'][$i]*100);
    @$data['armonto'][$i]=intval($data['Monto'][$i]);
	$data['metafc'][$i]=intval($data['fc_meta'][$i]);
    
    if($data['Aus'][$i]==1){
        $acumulado['md']+=intval($mt/intval($data['DiasHabiles'][$i]));
    }
    @$data['metadiaria'][$i]=intval($mt/intval($data['DiasHabiles'][$i]));
    @$data['mdtacum'][$i]=intval($acumulado['md']);
}

//print_r($data);

$xData=array();
$datasets=array();
$inf=array();
$datamonto=array();
$datalocs=array();


$xData[]=array("categories"=>$data['Fecha']);

switch($type){
    case "montos":
        $datasets[]=array("name"=>"Meta Diaria","data"=>$data['metadiaria'],"valueDecimals"=>2,"yAxis"=>1,"type"=>"line","color"=>"#606060","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Meta Acumulada por Dia","data"=>$data['mdtacum'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"line","color"=>"#89CC6F","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Por dia","data"=>$data['armonto'],"valueDecimals"=>2,"yAxis"=>1,"type"=>"column","color"=>"#85ACDB","marker"=>array("enabled"=>true));
        $datasets[]=array("name"=>"Acumulado","data"=>$data['acummonto'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline","color"=>"#C7000D","marker"=>array("enabled"=>true));
        //$datasets[]=array("name"=>"Por dia","data"=>$monto,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>1,"type"=>"column");
        //$datasets[]=array("name"=>"Acumulado","data"=>$acum,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline");
        break;
    case "montot":
        $info=$data['acummonto'];
        echo  json_encode($info, JSON_PRETTY_PRINT);
        exit;
        break;
    case "montod":
        $info=$data['armonto'];
        echo  json_encode($info, JSON_PRETTY_PRINT);
        exit;
        break;
    case "fc":
        $datasets[]=array("name"=>"Meta","data"=>$data['metafc'],"valueDecimals"=>2,"yAxis"=>0);
		$datasets[]=array("name"=>"Por Dia","data"=>$data['arfc'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"column","color"=>"#85ACDB");
        $datasets[]=array("name"=>"Acumulado","data"=>$data['acumfc'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"line","color"=>"##C7000D");
        break;
}
    //$datasets[]=array("name"=>"FC","data"=>$fc,"valueDecimals"=>0,"yAxis"=>1,"type"=>"spline");

$connectdb->close();
    
$info=array("xData"=>$xData,"datasets"=>$datasets);
print json_encode($info,JSON_PRETTY_PRINT);

//print_r($fechas);
?>


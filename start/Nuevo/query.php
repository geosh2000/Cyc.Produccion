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

$query="SELECT
	Fecha, id, `N Corto`, IF(Fecha=CURDATE(), LocsD, LocsT) as Localizadores, IF(Fecha=CURDATE(),d_Monto,t_Monto)* 1 as Monto, IF(Fecha=CURDATE(),dllamadas,llamadas) as llamadas,
    Aus, DiasHabiles, fc_meta
FROM
	(
		SELECT
			Fecha, Dolar
		FROM
			Fechas
		WHERE
			Fecha BETWEEN '$fecha_i' AND '$fecha_f'
	) Fechas
JOIN
	(
		SELECT
			id, `N Corto`
		FROM
			Asesores
		WHERE
			id=$asesor
	) Asesores
LEFT JOIN
		(
			SELECT
				asesor as LocalizadoresT_asesor, Fecha as LocalizadoresT_Fecha, COUNT(*) as LocsT, SUM(Monto) as MontoPorDia
			FROM
				(
					SELECT
						asesor, Fechas.Fecha, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN)* 1 as Monto
					FROM
						Fechas
					LEFT JOIN
						t_Locs
					ON
						Fechas.Fecha=t_Locs.Fecha
					WHERE
						Venta!=0 AND
						Fechas.Fecha BETWEEN '$fecha_i' AND '$fecha_f'
					GROUP BY
						asesor, Fechas.Fecha, Localizador
					HAVING
						Monto!=0
				) Locs1
			GROUP BY
				asesor,Fecha
		) LocalizadoresT
	ON
		Fecha=LocalizadoresT_Fecha AND
		id=LocalizadoresT_asesor
LEFT JOIN
		(
			SELECT
				asesor as LocalizadoresD_asesor, Fecha as LocalizadoresD_Fecha, COUNT(*) as LocsD, SUM(Monto) as MontoPorDia
			FROM
				(
					SELECT
						asesor, Fechas.Fecha, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN)* 1 as Monto
					FROM
						Fechas
					LEFT JOIN
						d_Locs
					ON
						Fechas.Fecha=d_Locs.Fecha
					WHERE
						Venta!=0 AND
						Fechas.Fecha BETWEEN '$fecha_i' AND '$fecha_f'
					GROUP BY
						asesor, Fechas.Fecha, Localizador
					HAVING
						Monto!=0
				) Locs1
			GROUP BY
				asesor,Fecha
		) LocalizadoresD
	ON
		Fecha=LocalizadoresD_Fecha AND
		id=LocalizadoresD_asesor
LEFT JOIN
	(
		SELECT Fecha as locs_Fecha, asesor as locs_asesor, SUM(Monto) as t_Monto, COUNT(localizador) as t_Localizadores
		FROM
			(
				SELECT Fecha, asesor, localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto
				FROM
					t_Locs
				WHERE
					Fecha BETWEEN '$fecha_i' AND '$fecha_f'
				GROUP BY
					asesor, localizador

			) lc
		GROUP BY
			Fecha, asesor
	) locs
ON
	Fecha=locs_Fecha AND
	id=locs_asesor
LEFT JOIN
	(
		SELECT Fecha as locsd_Fecha, asesor as locsd_asesor, SUM(Monto) as d_Monto, COUNT(localizador) as d_Localizadores
		FROM
			(
				SELECT Fecha, asesor, localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto
				FROM
					d_Locs
				WHERE
					Fecha BETWEEN '$fecha_i' AND '$fecha_f'
				GROUP BY
					asesor, localizador

			) lc
		GROUP BY
			Fecha, asesor
	) locsd
ON
	Fecha=locsd_Fecha AND
	id=locsd_asesor
LEFT JOIN
	(
		SELECT
			Fecha as calls_Fecha, asesor as calls_asesor, count(IF(Answered=1,ac_id,NULL))-IF(COUNT(IF(Desconexion='Transferida' AND Duracion_Real<='00:02:30',ac_id,NULL)) IS NULL, 0,COUNT(IF(Desconexion='Transferida' AND Duracion_Real<='00:02:30',ac_id,NULL))) as llamadas
		FROM
			t_Answered_Calls a
		LEFT JOIN
			Cola_Skill b
		ON
			a.Cola=b.Cola
		WHERE
			CASE 
				WHEN $dep=3 THEN Skill IN (3,35)
				WHEN $dep=35 THEN Skill IN (3,35)
				ELSE Skill=$dep
			END AND Asesor=$asesor AND
			Fecha BETWEEN '$fecha_i' AND '$fecha_f'
		GROUP BY
			Fecha
	) calls
ON
	Fecha=calls_Fecha AND
	id=calls_asesor
LEFT JOIN
	(
		SELECT
			Fecha as dcalls_Fecha, asesor as dcalls_asesor, SUM(Calls) as dllamadas
		FROM
			d_PorCola
		WHERE
			Fecha BETWEEN '$fecha_i' AND '$fecha_f' AND
			asesor=$asesor AND
			CASE 
				WHEN $dep=3 THEN Skill IN (3,35)
				WHEN $dep=35 THEN Skill IN (3,35)
				ELSE Skill=$dep
			END
		GROUP BY
			Fecha
	) dcalls
ON
	Fecha=dcalls_Fecha AND
	id=dcalls_asesor
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
	Fecha=Prog_Fecha AND
	id=Prog_asesor
JOIN 
	(
		SELECT meta*100 as fc_meta FROM metas_kpi	WHERE mes=MONTH('$fecha_i') AND anio=YEAR('$fecha_i') AND skill=$dep AND tipo='fc'
	) metafc

";

//echo $query."<br>";
if($result=Queries::query($query)){
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
	echo "Error: ".Queries::error();
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

    
$info=array("xData"=>$xData,"datasets"=>$datasets);
print json_encode($info,JSON_PRETTY_PRINT);

//print_r($fechas);
?>


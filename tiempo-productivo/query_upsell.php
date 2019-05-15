<?php
include("../connectDB.php");
date_default_timezone_set('America/Mexico_city');
header("content-type: application/json");

$asesor=$_GET['asesor'];
$fecha_i=$_GET['fechai'];
$fecha_f=$_GET['fechaf'];
$type=$_GET['type'];
$md=$_GET['mdt'];
$mt=$_GET['mt'];

$query="SELECT
	Fecha, id, `N Corto`, IF(t_Localizadores IS NULL, d_Localizadores, t_Localizadores) as Localizadores, IF(t_Monto IS NULL,d_Monto,t_Monto)*Dolar as Monto,
    Aus, DiasHabiles, contactos, locs
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
		SELECT Fecha as locs_Fecha, asesor as locs_asesor, SUM(Monto) as t_Monto, COUNT(localizador) as t_Localizadores
		FROM
			(
				SELECT Fecha, asesor, localizador, SUM(Venta+OtrosIngresos+Egresos) as Monto
				FROM
					t_Locs
				WHERE
					Fecha BETWEEN '$fecha_i' AND '$fecha_f' AND
					Venta!=0
				GROUP BY
					asesor, localizador
				HAVING
					Monto!=0
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
				SELECT Fecha, asesor, localizador, SUM(Venta+OtrosIngresos+Egresos) as Monto
				FROM
					d_Locs
				WHERE
					Fecha BETWEEN '$fecha_i' AND '$fecha_f' AND
					Venta!=0
				GROUP BY
					asesor, localizador
				HAVING
					Monto!=0
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
			Fecha as dcalls_Fecha, asesor as dcalls_asesor, SUM(Calls) as dllamadas
		FROM
			d_PorCola
		WHERE
			Fecha BETWEEN '$fecha_i' AND '$fecha_f' AND
			asesor=$asesor AND
			Skill=3
		GROUP BY
			Fecha
	) dcalls
ON
	Fecha=dcalls_Fecha AND
	id=dcalls_asesor
LEFT JOIN
	(
		SELECT
			Fecha as Prog_Fecha, a.asesor as Prog_asesor, IF(tipo_ausentismo!=10 OR tipo_ausentismo IS NOT NULL OR (`jornada start`='00:00:00' AND `jornada end`='00:00:00'),0,1) as Aus, DiasHabiles
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
					a.asesor, SUM(IF(tipo_ausentismo!=10 OR tipo_ausentismo IS NOT NULL OR (`jornada start`='00:00:00' AND `jornada end`='00:00:00'),0,1)) as DiasHabiles
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
LEFT JOIN
	(
	SELECT
		Fecha as FC_Fecha,
		id as FC_asesor,
		COUNT(if(Desconexion='Abandono',ac_id,NULL)) as intentos,
		AVG(if(Desconexion='Abandono',TIME_TO_SEC(Espera),NULL)) as AT_intentos,
		COUNT(if(Desconexion!='Abandono',ac_id,NULL)) as contestadas,
		COUNT(DISTINCT if(Desconexion!='Abandono',Llamante,NULL)) as contactos,
		locs,
		CAST(locs/COUNT(DISTINCT Llamante) as DECIMAL(5,2)) as FC
	FROM
		t_Answered_Calls a
	LEFT JOIN
		Asesores b
	ON
		a.asesor=b.id
	LEFT JOIN
		(
			SELECT
				loc_asesor, loc_Fecha, COUNT(*) as locs
			FROM
				(
					SELECT
						asesor as loc_asesor, Fecha as loc_Fecha, SUM(Venta+OtrosIngresos+Egresos) as monto
					FROM
						t_Locs
					WHERE
						Venta!=0 AND
						Fecha BETWEEN '$fecha_i' AND '$fecha_f'
					GROUP BY
						asesor, Fecha, localizador
					HAVING
						monto!=0
				) locs1
			GROUP BY
				loc_asesor, loc_Fecha
		) locs
	ON
		a.asesor=loc_asesor AND
		a.Fecha=loc_Fecha
	WHERE
		Cola LIKE '%Outbound%' AND
		Fecha BETWEEN '$fecha_i' AND '$fecha_f'
	GROUP BY
		asesor, Fecha
	) FC
ON
	Fecha=FC_Fecha AND
	id=FC_asesor";
$result=mysql_query($query);
$num=mysql_num_rows($result);

//echo "ERROR: ".mysql_error()."<br>$query<br>Rows: $num<br>";
$i=0;
$mdacumulado=0;
while($i<$num){
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    //echo mysql_result($result,$i,'Fecha')."<br>";
    $id[$i]=mysql_result($result,$i,'id');
    if(date("I", strtotime($fecha[$i]))==1){$minus="4";}else{$minus="5";}
    $datetmp=strftime("%s", strtotime($fecha[$i].' -'.$minus.' hours'))*1000;
    $tot_locs[$i]=mysql_result($result,$i,'Localizadores');
    $tot_calls[$i]=mysql_result($result,$i,'llamadas');
    $locs[$i]=array($datetmp,intval(mysql_result($result,$i,'Localizadores')));
    $acumulado+=mysql_result($result,$i,'Monto');
    $acum_locs+=mysql_result($result,$i,'Localizadores');
    $acum_calls+=mysql_result($result,$i,'contactos');
    $acumfc[$i]=array($datetmp,intval($acum_locs/$acum_calls*100));
    $acum[$i]=array($datetmp,intval($acumulado));
    $fc[$i]=array($datetmp,intval(mysql_result($result,$i,'Localizadores')/mysql_result($result,$i,'contactos')*100));
    $xdata[$i]=$datetmp;
    $monto[$i]=array($datetmp,intval(mysql_result($result,$i,'Monto')));
    $acum[$i]=array($datetmp,intval($acumulado));
    if(mysql_result($result,$i,'Aus')==1){
        $mdacumulado+=intval($mt/intval(mysql_result($result,$i,'DiasHabiles')));
    }
    $metadiaria[$i]=array($datetmp,intval($mt/intval(mysql_result($result,$i,'DiasHabiles'))));
    $mdtacum[$i]=array($datetmp,intval($mdacumulado));
    //$monto[$i]=intval(mysql_result($result,$i,'Monto'));
    //$acum[$i]=intval($acumulado);

$i++;
}
    $xData=array();
    $datasets=array();
    $inf=array();
    $datamonto=array();
    $datalocs=array();


    $xData[]=array("categories"=>$fecha);

switch($type){
    case "montos":
        $datasets[]=array("name"=>"Meta Diaria","data"=>$metadiaria,"valueDecimals"=>2,"yAxis"=>1,"type"=>"line","color"=>"#606060","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Meta Acumulada por Dia","data"=>$mdtacum,"valueDecimals"=>2,"yAxis"=>0,"type"=>"line","color"=>"#89CC6F","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Por dia","data"=>$monto,"valueDecimals"=>2,"yAxis"=>1,"type"=>"column","color"=>"#85ACDB","marker"=>array("enabled"=>true));
        $datasets[]=array("name"=>"Acumulado","data"=>$acum,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline","color"=>"#C7000D","marker"=>array("enabled"=>true));
        //$datasets[]=array("name"=>"Por dia","data"=>$monto,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>1,"type"=>"column");
        //$datasets[]=array("name"=>"Acumulado","data"=>$acum,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline");
        break;
    case "montot":
        $info=$acum;
        echo  json_encode($info, JSON_PRETTY_PRINT);
        exit;
        break;
    case "montod":
        $info=$monto;
        echo  json_encode($info, JSON_PRETTY_PRINT);
        exit;
        break;
    case "fc":
        $datasets[]=array("name"=>"Por Dia","data"=>$fc,"valueDecimals"=>2,"yAxis"=>0,"type"=>"column","color"=>"#85ACDB");
        $datasets[]=array("name"=>"Acumulado","data"=>$acumfc,"valueDecimals"=>2,"yAxis"=>0,"type"=>"line","color"=>"##C7000D");
        break;
}
    //$datasets[]=array("name"=>"FC","data"=>$fc,"valueDecimals"=>0,"yAxis"=>1,"type"=>"spline");


    $info=array("xData"=>$xData,"datasets"=>$datasets);
echo  json_encode($info, JSON_PRETTY_PRINT);

//print_r($fechas);
?>


<?php

include("../connectMYSQLI.php");

date_default_timezone_set('America/Mexico_City');

$from=date('Y-m-d',strtotime($_POST['from']));

if(date('Y-m-d',strtotime($_POST['from']))<date('Y-m-d')){
	$db="t_Locs";
}else{
	$db="d_Locs";
}

//Venta
	$query="SELECT
				Canal, Hora_int as Hora,
				SUM(Monto) as Monto
			FROM
				(
					SELECT
			     		a.Fecha, x.Hora_int, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
			     		SUM(VentaMXN+OtrosIngresosMXN) as Venta,
			     		SUM(EgresosMXN) as Xld,
			     	   `id Departamento` as PCRC, `N Corto`, Dolar, New_Loc,
			     	   CASE 
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (`id Departamento` NOT IN (28,29,30,31,5) AND `id Departamento` IS NOT NULL)))) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (`id Departamento` IN (29,30,31) OR `id Departamento` IS NULL)) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (`id Departamento` IN (3,35,4,6,9,43))) THEN 'ibMT'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (`id Departamento`=5)) THEN 'us'
	     	   	WHEN (New_Loc IS NOT NULL) THEN 'us'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%') THEN 'PDV'
	     	   	WHEN (a.asesor=-1 AND New_Loc IS NULL AND (Afiliado LIKE 'pricetravel.com%' OR Afiliado LIKE 'm.pricetravel.com%')) THEN 'ol'
	     	   	WHEN (a.asesor=-100) THEN 'rb'
	     	   	WHEN (Afiliado LIKE'%shop.pricetravel.co') THEN 'PDVCO'
					ELSE 'Otro'
						END as Canal 
			     	FROM
			     		HoraGroup_Table15 x
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							$db
						WHERE
							Fecha='$from') a ON a.Hora BETWEEN x.Hora_time AND Hora_end
			     	LEFT JOIN
			     		Asesores b
			     	ON
			     		a.asesor=b.id
			     	LEFT JOIN
			     		Fechas c
			     	ON
			     		a.Fecha=c.Fecha
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							pdv_registro_llamadas d
						WHERE
							CAST(d.Last_Update as DATE)='$from') d
			     	ON
			     		a.Localizador=d.New_Loc AND
			     		a.Fecha=CAST(d.Last_Update as DATE)
			     	WHERE
			     		a.Fecha='$from'
			     	GROUP BY
			     		a.Localizador, x.Hora_int) locs
			GROUP BY 
				Canal, Hora_int";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$data['TD'][$fila['Canal']][$fila['Hora']]=$fila['Monto'];
			switch($fila['Canal']){
				case 'ibMP':
				case 'us':
				case 'ol':
				case 'PDV':	
					@$data['TD']['All'][$fila['Hora']]+=$fila['Monto'];
					if($fila['Canal']!='PDV'){
						@$data['TD']['PTMX'][$fila['Hora']]+=$fila['Monto'];
					}
					break;
				default:
					break;
			}
		}	
	}
	
//Venta Y 
	$query="SELECT
				Canal, Hora_int as Hora,
				SUM(Monto) as Monto
			FROM
				(
					SELECT
			     		a.Fecha, x.Hora_int, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
			     		SUM(VentaMXN+OtrosIngresosMXN) as Venta,
			     		SUM(EgresosMXN) as Xld,
			     	   `id Departamento` as PCRC, `N Corto`, Dolar, New_Loc,
			     	   CASE 
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (`id Departamento` NOT IN (28,29,30,31,5) AND `id Departamento` IS NOT NULL)))) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (`id Departamento` IN (29,30,31) OR `id Departamento` IS NULL)) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (`id Departamento` IN (3,35,4,6,9,43))) THEN 'ibMT'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (`id Departamento`=5)) THEN 'us'
	     	   	WHEN (New_Loc IS NOT NULL) THEN 'us'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%') THEN 'PDV'
	     	   	WHEN (a.asesor=-1 AND New_Loc IS NULL AND (Afiliado LIKE 'pricetravel.com%' OR Afiliado LIKE 'm.pricetravel.com%')) THEN 'ol'
	     	   	WHEN (a.asesor=-100) THEN 'rb'
	     	   	WHEN (Afiliado LIKE'%shop.pricetravel.co') THEN 'PDVCO'
					ELSE 'Otro'
						END as Canal 
			     	FROM
			     		HoraGroup_Table15 x
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							t_Locs
						WHERE
							Fecha=ADDDATE('$from',-1)) a ON a.Hora BETWEEN x.Hora_time AND Hora_end
			     	LEFT JOIN
			     		Asesores b
			     	ON
			     		a.asesor=b.id
			     	LEFT JOIN
			     		Fechas c
			     	ON
			     		a.Fecha=c.Fecha
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							pdv_registro_llamadas d
						WHERE
							CAST(d.Last_Update as DATE)=ADDDATE('$from',-1)) d
			     	ON
			     		a.Localizador=d.New_Loc AND
			     		a.Fecha=CAST(d.Last_Update as DATE)
			     	WHERE
			     		a.Fecha=ADDDATE('$from',-1)
			     	GROUP BY
			     		a.Localizador, x.Hora_int) locs
			GROUP BY 
				Canal, Hora_int";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$data['YD'][$fila['Canal']][$fila['Hora']]=$fila['Monto'];
			switch($fila['Canal']){
				case 'ibMP':
				case 'us':
				case 'ol':
				case 'PDV':	
					@$data['YD']['All'][$fila['Hora']]+=$fila['Monto'];
					if($fila['Canal']!='PDV'){
						@$data['YD']['PTMX'][$fila['Hora']]+=$fila['Monto'];
					}
					break;
				default:
					break;
			}
		}	
	}

	//Venta LY
	$query="SELECT
				Canal, Hora_int as Hora,
				SUM(Monto) as Monto
			FROM
				(
					SELECT
			     		a.Fecha, x.Hora_int, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
			     		SUM(VentaMXN+OtrosIngresosMXN) as Venta,
			     		SUM(EgresosMXN) as Xld,
			     	   `id Departamento` as PCRC, `N Corto`, Dolar, New_Loc,
			     	   CASE 
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (`id Departamento` NOT IN (28,29,30,31,5) AND `id Departamento` IS NOT NULL)))) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (`id Departamento` IN (29,30,31) OR `id Departamento` IS NULL)) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (`id Departamento` IN (3,35,4,6,9,43))) THEN 'ibMT'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (`id Departamento`=5)) THEN 'us'
	     	   	WHEN (New_Loc IS NOT NULL) THEN 'us'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%') THEN 'PDV'
	     	   	WHEN (a.asesor=-1 AND New_Loc IS NULL AND (Afiliado LIKE 'pricetravel.com%' OR Afiliado LIKE 'm.pricetravel.com%')) THEN 'ol'
	     	   	WHEN (a.asesor=-100) THEN 'rb'
	     	   	WHEN (Afiliado LIKE'%shop.pricetravel.co') THEN 'PDVCO'
					ELSE 'Otro'
						END as Canal 
			     	FROM
			     		HoraGroup_Table15 x
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							t_Locs
						WHERE
							Fecha=ADDDATE('$from',-371)) a ON a.Hora BETWEEN x.Hora_time AND Hora_end
			     	LEFT JOIN
			     		Asesores b
			     	ON
			     		a.asesor=b.id
			     	LEFT JOIN
			     		Fechas c
			     	ON
			     		a.Fecha=c.Fecha
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							pdv_registro_llamadas d
						WHERE
							CAST(d.Last_Update as DATE)=ADDDATE('$from',-371)) d
			     	ON
			     		a.Localizador=d.New_Loc AND
			     		a.Fecha=CAST(d.Last_Update as DATE)
			     	WHERE
			     		a.Fecha=ADDDATE('$from',-371)
			     	GROUP BY
			     		a.Localizador, x.Hora_int) locs
			GROUP BY 
				Canal, Hora_int";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$data['LY'][$fila['Canal']][$fila['Hora']]=$fila['Monto'];
			switch($fila['Canal']){
				case 'ibMP':
				case 'us':
				case 'ol':
				case 'PDV':	
					@$data['LY']['All'][$fila['Hora']]+=$fila['Monto'];
					if($fila['Canal']!='PDV'){
						@$data['LY']['PTMX'][$fila['Hora']]+=$fila['Monto'];
					}
					break;
				default:
					break;
			}
			
			
		}	
	}
	
	//Venta LW
	$query="SELECT
				Canal, Hora_int as Hora,
				SUM(Monto) as Monto
			FROM
				(
					SELECT
			     		a.Fecha, x.Hora_int, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
			     		SUM(VentaMXN+OtrosIngresosMXN) as Venta,
			     		SUM(EgresosMXN) as Xld,
			     	   `id Departamento` as PCRC, `N Corto`, Dolar, New_Loc,
			     	   CASE 
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (`id Departamento` NOT IN (28,29,30,31,5) AND `id Departamento` IS NOT NULL)))) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (`id Departamento` IN (29,30,31) OR `id Departamento` IS NULL)) THEN 'ibMP'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (`id Departamento` IN (3,35,4,6,9,43))) THEN 'ibMT'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (`id Departamento`=5)) THEN 'us'
	     	   	WHEN (New_Loc IS NOT NULL) THEN 'us'
	     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%') THEN 'PDV'
	     	   	WHEN (a.asesor=-1 AND New_Loc IS NULL AND (Afiliado LIKE 'pricetravel.com%' OR Afiliado LIKE 'm.pricetravel.com%')) THEN 'ol'
	     	   	WHEN (a.asesor=-100) THEN 'rb'
	     	   	WHEN (Afiliado LIKE'%shop.pricetravel.co') THEN 'PDVCO'
					ELSE 'Otro'
						END as Canal 
			     	FROM
			     		HoraGroup_Table15 x
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							t_Locs
						WHERE
							Fecha=ADDDATE('$from',-7)) a ON a.Hora BETWEEN x.Hora_time AND Hora_end
			     	LEFT JOIN
			     		Asesores b
			     	ON
			     		a.asesor=b.id
			     	LEFT JOIN
			     		Fechas c
			     	ON
			     		a.Fecha=c.Fecha
			     	LEFT JOIN
			     		(SELECT
							*
						FROM
							pdv_registro_llamadas d
						WHERE
							CAST(d.Last_Update as DATE)=ADDDATE('$from',-7)) d
			     	ON
			     		a.Localizador=d.New_Loc AND
			     		a.Fecha=CAST(d.Last_Update as DATE)
			     	WHERE
			     		a.Fecha=ADDDATE('$from',-7)
			     	GROUP BY
			     		a.Localizador, x.Hora_int) locs
			GROUP BY 
				Canal, Hora_int";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$data['LW'][$fila['Canal']][$fila['Hora']]=$fila['Monto'];
			switch($fila['Canal']){
				case 'ibMP':
				case 'us':
				case 'ol':
				case 'PDV':	
					@$data['LW']['All'][$fila['Hora']]+=$fila['Monto'];
					if($fila['Canal']!='PDV'){
						@$data['LW']['PTMX'][$fila['Hora']]+=$fila['Monto'];
					}
					break;
				default:
					break;
			}
		}	
	}

$query="SELECT MAX(Last_Update) as lu FROM d_Locs WHERE Fecha='$from'";
$result=$connectdb->query($query);
$fila=$result->fetch_assoc();

$lu=new DateTime($fila['lu'].' America/Mexico_City');
$cuntime= new DateTimeZone('America/Bogota');

$lu->setTimezone($cuntime);

$acum['lu']=utf8_encode($lu->format('Y-m-d H:i:s'));

	$connectdb->close();
	
foreach($data as $ref => $info3){
	foreach($info3 as $canal => $info){
		for($i=0;$i<96;$i++){
			if($i==0){
				$acum[$ref][$canal][]=floatval($info[$i]);
			}else{
				$acum[$ref][$canal][]=$info[$i]+$acum[$ref][$canal][$i-1];
			}
		}
	}
}



print json_encode($acum,JSON_PRETTY_PRINT);

/*echo "<pre>";
print_r($acum);
echo "</pre>";*/


 ?>


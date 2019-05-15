<?php

include("../connectDB.php");
date_default_timezone_set('America/Bogota');

$fecha=date('Y-m-d',strtotime($_POST['fecha']));

$query="SELECT
	a.Hora, 
	AVG(Total) as Total,
	SUM(IF(Canal='ol',Locs,0)) as 'OL',
	SUM(IF(Canal='ibMPCC' OR Canal='ibMPPDV',Locs,0)) as 'CC',
	SUM(IF(Canal='usCC' OR Canal='usPDV',Locs,0)) as 'US', lu
FROM
(SELECT 
	a.Hora_int as Hora, 
	a.Hora_group as HoraG, 
	COUNT(b.id) as Total,
	MAX(Last_Update) as lu
FROM 
	HoraGroup_Table a 
LEFT JOIN 
	(SELECT 
		CAST(Fecha as TIME) as Hora, 
		id, Last_Update
	FROM 
		us_basereservas  
	WHERE 
		CAST(Fecha as DATE)='$fecha'
	) b ON b.Hora BETWEEN a.Hora_time AND ADDTIME(a.Hora_time,'00:29:59') 
GROUP BY Hora_group) a
LEFT JOIN
	(SELECT
        	a.Hora_group as Hora,
			Canal,
        	COUNT(Localizador) as Locs
        FROM
        	HoraGroup_Table a 
      	LEFT JOIN
        	(SELECT
        		Hora, a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta,
        	   `id Departamento` as PCRC, `N Corto`, Dolar, New_Loc,
		     	   CASE 
     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (`id Departamento` NOT IN (28,29,30,31,5) AND `id Departamento` IS NOT NULL)))) THEN 'ibMPCC'
     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (`id Departamento` IN (29,30,31) OR `id Departamento` IS NULL)) THEN 'ibMPPDV'
     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (`id Departamento` IN (3,35,4,6,9,43))) THEN 'ibMT'
     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (`id Departamento`=5)) THEN 'usCC'
     	   	WHEN (New_Loc IS NOT NULL) THEN 'usPDV'
     	   	WHEN (a.asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%') THEN 'PDV'
     	   	WHEN (a.asesor=-1 AND New_Loc IS NULL) THEN 'ol'
     	   	WHEN (a.asesor=-100) THEN 'rb'
     	   	WHEN (Afiliado LIKE'%shop.pricetravel.co') THEN 'PDVCO'
				ELSE 'Otro'
					END as Canal 
        	FROM
        		d_Locs a
        	LEFT JOIN
        		Asesores b
        	ON
        		a.asesor=b.id
        	LEFT JOIN
        		Fechas c
        	ON
        		a.Fecha=c.Fecha
        	LEFT JOIN
        		pdv_registro_llamadas d
        	ON
        		a.Localizador=d.New_Loc AND
        		a.Fecha=CAST(d.Last_Update as DATE)
        	WHERE
        		a.Fecha='$fecha'
        	GROUP BY
        		a.Localizador
        	HAVING
        		Venta>0 AND Monto>0) locs ON locs.Hora BETWEEN a.Hora_time AND ADDTIME(a.Hora_time,'00:29:59') 
        	GROUP BY
        		Canal, Hora_group) c ON c.Hora=a.HoraG
GROUP BY Hora";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$data['Total'][$fila['Hora']]=intval($fila['Total'])-$fila['OL']-$fila['US'];
		$data['OL'][$fila['Hora']]=intval($fila['OL']);
		$data['US'][$fila['Hora']]=intval($fila['US']);
	}
}

$query="SELECT MAX(Last_Update) as lu FROM us_basereservas";
if($result=$connectdb->query($query)){
	$fila=$result->fetch_assoc();
	$data['lu']="Last Update: ".$fila['lu'];
}


print json_encode($data,JSON_UNESCAPED_UNICODE);

 ?>


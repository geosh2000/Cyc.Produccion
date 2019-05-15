<?php
include("connectDB.php");

$query="SELECT
		`N Corto` as Asesor, Calls, TIME_TO_SEC(Avg_Time_Calls) as AHT,
        d_PorCola.Fecha,
		InboundAllLocs/Calls as FC,
		InboundAllLocs, InboundMPLocs,InboundITLocs,InboundCOPALocs,InboundCOOMEVALocs,
		InboundAllMonto, InboundMPMonto,InboundITMonto,InboundCOPAMonto,InboundCOOMEVAMonto
	FROM Asesores LEFT JOIN d_PorCola ON Asesores.id=d_PorCola.asesor LEFT JOIN
	(
		SELECT
        		`N Corto` as Asesor, Fecha,
				COUNT(IF((PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundAllLocs,
        		COUNT(IF(Afiliado LIKE'%pricetravel.com.mx%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundMPLocs,
        		COUNT(IF((PCRC=5),Localizador,NULL)) as OutboundLocs,
        		COUNT(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundITLocs,
        		COUNT(IF(Afiliado LIKE'%copa%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundCOPALocs,
        		COUNT(IF(Afiliado LIKE'%coomeva%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundCOOMEVALocs,
        		SUM(IF((PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundAllMonto,
        		SUM(IF(Afiliado LIKE'%pricetravel.com.mx%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundMPMonto,
        		SUM(IF((PCRC=5),Monto,NULL)) * Dolar as OutboundMonto,
        		SUM(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundITMonto,
        		SUM(IF(Afiliado LIKE'%copa%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundCOPAMonto,
        		SUM(IF(Afiliado LIKE'%coomeva%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundCOOMEVAMonto
         FROM
        	(SELECT
        		a.Fecha, Afiliado, Localizador, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta,
        		`id Departamento` as PCRC, `N Corto`, Dolar
        	FROM
        		d_Locs a,
        		Asesores b,
        		Fechas c
        	WHERE
        		a.asesor=b.id AND
        		a.Fecha=c.Fecha
        	GROUP BY
        		Localizador
        	HAVING
        		Venta>0 AND Monto>0) locs
        	GROUP BY
        		`N Corto`, Fecha
	) c
	ON Asesores.`N Corto`=c.Asesor AND d_PorCola.Fecha=c.Fecha
	WHERE d_PorCola.Skill=4 AND `id Departamento`=4
	ORDER BY Asesor
	" ;
$result=mysql_query($query);

$numAHTsc=mysql_numrows($result);

mysql_close();



$i=0;
while ($i < $numAHTsc) {

$name[$i]=mysql_result($result,$i,"Asesor");
$aht[$i]=mysql_result($result,$i,"AHT");
$calls[$i]=mysql_result($result,$i,"Calls");
$acum[$i]=100;
$fecha[$i]=mysql_result($result,$i,"Fecha");

$i++;
}
?>
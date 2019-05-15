<?php
include("../../connectDB.php");
$query="SELECT `N Corto`, a.Fecha,  Localizador, Afiliado, Venta, OtrosIngresos, Egresos, CAST(Dolar as DECIMAL(5,2)) as TipoDeCambio, CAST((Venta+OtrosIngresos+Egresos)*Dolar as DECIMAL(11,2)) as MontoTotal
	FROM t_Locs a, Asesores b, PCRCs c, Fechas d
	WHERE a.asesor=b.id AND b.`id Departamento`=c.id AND a.Fecha=d.Fecha AND Venta!=0
		AND a.Fecha>='2016-02-15' AND a.Fecha<='2016-02-20' AND c.id=5
	ORDER BY
		asesor, a.Fecha, Afiliado, Localizador";
$result=mysql_query($query);
$num=mysql_numrows($result);


?>
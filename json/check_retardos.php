<?php
session_start();
include("../connectDB.php");
date_default_timezone_set('America/Bogota');

$today=date('Y-m-d');
$month=date('m',strtotime($today.' -1 days'));
$year=date('Y',strtotime($today.' -1 days'));

$query="SELECT
	*
FROM
	(
		SELECT
			Fecha, id,Sesiones_id, asesor, dif, IF(dif<-10,1,0) as Ret10
		FROM
			(
				SELECT
					Fecha
				FROM
				 	Fechas
				WHERE
					MONTH(Fecha)=$month AND
					YEAR(Fecha)=$year
			) Fechas
		JOIN
			(
				SELECT
					id, `N Corto` as asesor
				FROM
					Asesores
				WHERE
					Activo=1
			) Asesores
		LEFT JOIN
			(
				SELECT
					Sesiones_Fecha, Sesiones_Asesor, jornada_start, Sesiones_id, Inicio, (TIME_TO_SEC(jornada_start)-TIME_TO_SEC(Inicio)-IF(Verano=0,3600,0))/60 as dif
				FROM
					(
						SELECT
							Fecha, Verano
						FROM
							Fechas
						WHERE
							MONTH(Fecha)=$month AND
							YEAR(Fecha)=$year
					) Dates
				LEFT JOIN
					(
						SELECT
							Fecha as Sesiones_Fecha, asesor as Sesiones_asesor, id as Sesiones_id, `jornada start` as jornada_start
						FROM
							`Historial Programacion`
						WHERE
							MONTH(Fecha)=$month AND
							YEAR(Fecha)=$year AND
							(`jornada start`!='00:00:00' AND `jornada end`!='00:00:00')
						GROUP BY
							Fecha, asesor
					) Ses
				ON
					Dates.Fecha=Sesiones_Fecha
				LEFT JOIN
					(
						SELECT
							Fecha_in as Logueo_Fecha, asesor as Logueo_Asesor, LogAsesor(Fecha_in,asesor,'in') as Inicio
						FROM
							t_Sesiones
						WHERE
							MONTH(Fecha_in)=$month AND
							YEAR(Fecha_in)=$year
						GROUP BY
							Fecha_in, asesor
					) Logueo
				ON
					Sesiones_Fecha=Logueo_Fecha AND
					Sesiones_asesor=Logueo_Asesor
				GROUP BY
					Sesiones_Fecha,Sesiones_asesor
			) Sesiones
		ON
			Fechas.Fecha=Sesiones_Fecha AND
			Asesores.id=Sesiones_asesor
		LEFT JOIN
			(
				SELECT
						Fecha as Ausentismo_Fecha, asesor as Ausentismo_asesor, Ausentismo
				FROM
					Fechas
				JOIN
					Ausentismos
				ON
					Fechas.Fecha BETWEEN Inicio AND Fin
				LEFT JOIN
					`Tipos Ausentismos` a
				ON
					tipo_ausentismo=a.id
				WHERE
					MONTH(Fecha)=$month AND
					YEAR(Fecha)=$year
				GROUP BY
					Ausentismo_Fecha, Ausentismo_asesor
			) Ausentismo
		ON
			Fechas.Fecha=Ausentismo_Fecha AND
			Asesores.id=Ausentismo_asesor
		LEFT JOIN
			(
				SELECT
					horario_id, asesor as RJS_Asesor, Codigo
				FROM
					PyA_Exceptions a,
					`Tipos Excepciones` b
				WHERE
					a.tipo=b.exc_type_id
				GROUP BY
					horario_id, asesor
			) RJS
		ON
			Sesiones_id=horario_id AND
			Asesores.id=RJS_Asesor
		WHERE
			Ausentismo_asesor IS NULL AND
			RJS_Asesor IS NULL
		HAVING
			dif<-1
	) Rets
ORDER BY
    Fecha
";

$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    $id[$i]=mysql_result($result,$i,'id');
    $hid[$i]=mysql_result($result,$i,'Sesiones_id');
    $asesor[$i]=mysql_result($result,$i,'asesor');
    $dif[$i]=mysql_result($result,$i,'dif');
    $r10m[$i]=mysql_result($result,$i,'Ret10');
$i++;
}

foreach($fecha as $key => $date){
    $time= $dif[$key]*(-1);
    if($r10m[$key]==1){$tipo=1;}else{$tipo=2;}
    $query="INSERT INTO bit_bitacora (Fecha,horario_id,asesor,incidencia,observaciones) VALUES ('$date','$hid[$key]','$id[$key]','$tipo','Retardo de $time minutos')";
    mysql_query($query);
}



?>
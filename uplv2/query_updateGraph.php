<?php

include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

switch($_POST['q']){
  case 1:
    $query="INSERT INTO module_grafVentas (fecha, asesor, llamadas) (SELECT * FROM (SELECT 
          Fecha, asesor, COUNT(ac_id)-COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:00',ac_id,NULL)) as Llamadas
        FROM
          (SELECT
            a.*, Skill
          FROM 
            t_Answered_Calls a 
          LEFT JOIN 
            Cola_Skill b ON a.Cola=b.Cola 
          LEFT JOIN
            dep_asesores c ON a.asesor=c.asesor AND a.Fecha=c.Fecha
          WHERE 
            a.Fecha BETWEEN CAST(CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-01') as DATE) AND CAST(CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-15') as DATE) AND 
            Answered=1
          HAVING 
            Skill IN (3,35)) a
        GROUP BY Fecha, asesor) as a) ON DUPLICATE KEY UPDATE llamadas=a.Llamadas";
    break;
  case 2:
    $query="INSERT INTO module_grafVentas (fecha, asesor, llamadas) (SELECT * FROM (SELECT 
          Fecha, asesor, COUNT(ac_id)-COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:02:00',ac_id,NULL)) as Llamadas
        FROM
          (SELECT
            a.*, Skill
          FROM 
            t_Answered_Calls a 
          LEFT JOIN 
            Cola_Skill b ON a.Cola=b.Cola 
          LEFT JOIN
            dep_asesores c ON a.asesor=c.asesor AND a.Fecha=c.Fecha
          WHERE 
            a.Fecha BETWEEN CAST(CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-15') as DATE) AND CURDATE() AND 
            Answered=1
          HAVING 
            Skill IN (3,35)) a
        GROUP BY Fecha, asesor) as a) ON DUPLICATE KEY UPDATE llamadas=a.Llamadas";
    break;
   case 3:
    $query="INSERT INTO module_grafVentas (fecha, asesor, monto, rsvas) (SELECT * FROM (SELECT 
          Fecha, asesor, SUM(Monto_Total) as Monto, COUNT(DISTINCT NewLoc) as Locs
        FROM 
        (SELECT
				Fecha, asesor, IF(SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN)=0,NULL,Localizador) as NewLoc, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto_Total,
				CASE
					WHEN chanId IN (1,2,3,4,5,11,178,193,279,309,332,382,409,410,411,423,553,557,577,618) THEN 'MP'
					WHEN chanId IN (25,192,370) THEN 'SHOP'
					ELSE 'MT'
				END as Canal
			FROM
				(SELECT a.*, IF(Venta=0,Null,Localizador) as NewLoc FROM	t_Locs a 
        LEFT JOIN 
          dep_asesores b ON a.asesor=b.asesor AND a.Fecha=b.Fecha 
        WHERE 
          a.Fecha BETWEEN CAST(CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-01') as DATE) AND CURDATE() AND
          dep IN (3,35)) a GROUP BY Fecha, Localizador) a
        GROUP BY
          Fecha, asesor) as a ) ON DUPLICATE KEY UPDATE monto=a.Monto, rsvas=a.Locs";
    break;
   case 4:
    $query="INSERT INTO module_grafVentas (fecha, asesor, ausentismo, diashabiles) (SELECT * FROM (SELECT
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
                  Fecha BETWEEN CAST(CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-01') as DATE) AND ADDDATE(CAST(CONCAT(IF(MONTH(CURDATE())=12,YEAR(CURDATE())+1,YEAR(CURDATE())),'-',MONTH(CURDATE())+1,'-01') as DATE),-1)
                GROUP BY
                  asesor
              ) c
            ON
              a.asesor=c.asesor
            WHERE
              Fecha BETWEEN CAST(CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-01') as DATE) AND ADDDATE(CAST(CONCAT(IF(MONTH(CURDATE())=12,YEAR(CURDATE())+1,YEAR(CURDATE())),'-',MONTH(CURDATE())+1,'-01') as DATE),-1)) a) ON DUPLICATE KEY UPDATE ausentismo=a.Aus, diashabiles=a.DiasHabiles";
              break;

}

if($result=$connectdb->query($query)){
  $data['status']=1;
}else{
  $data['status']=0;
  $data['msg']= "Error en query de llamadas -> ".$connectdb->error." ON $query<br><br>";
}

echo json_encode($data,JSON_PRETTY_PRINT);

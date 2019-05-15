<?php
include_once("../modules/modules.php");

$asesor=$_POST['asesor'];

$query="SELECT a.asesor, a.motivo, dias_asignados, IF(dias_redimidos IS NULL, 0, dias_redimidos) as dias_redimidos, dias_asignados - IF(dias_redimidos IS NULL, 0, dias_redimidos) as dias_pendientes
		FROM
			(SELECT id as asesor, SUM(`dias asignados`) as dias_asignados, motivo FROM `Dias Pendientes` WHERE id=$asesor GROUP BY motivo, asesor) a
		LEFT JOIN
			(SELECT id as asesor, SUM(`dias`) as dias_redimidos, motivo FROM `Dias Pendientes Redimidos` WHERE id=$asesor GROUP BY motivo, asesor) b
		ON a.asesor=b.asesor AND a.motivo=b.motivo
		HAVING dias_pendientes>0";
		
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		$data['dias'][utf8_encode($fila['motivo'])]=$fila['dias_pendientes'];
	}
}

@$count=count($data['dias']);

$data['total']=$count;

print json_encode($data,JSON_PRETTY_PRINT);
?>



<?php
include("../connectDB.php");
include("../common/scripts.php");



include("../common/menu.php");
?>
<table width='100%' class='t2'>
    <tr class='title'>
        <th>Monitoreo BackOffice</th>
    </tr>
</table>

<?php

$i=0;
while($i<48){
    $a_time=intval(($i)/2).":".((($i) % 2)/2*60).":00";
    $a_start=date('H:i:s',strtotime($a_time));
    $a_end=date('H:i:s',strtotime($a_time.'+ 30 minutes'));
    $q.= ",COUNT(IF(CAST(CONCAT(HOUR(date_created),':',MINUTE(date_created),':',SECOND(date_created)) as TIME)>='$a_start' AND CAST(CONCAT(HOUR(date_created),':',MINUTE(date_created),':',SECOND(date_created)) as TIME)<'$a_end',a.actividad,NULL)) as Casos_$i<br>";
$i++;
}

$query="SELECT
		`N Corto` as 'Asesor',
		CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as Fecha,
		CAST(CONCAT(HOUR(date_created),':',MINUTE(date_created),':',SECOND(date_created)) as TIME) as Hora, d.actividad,
		count(a.actividad) as Casos $q
	FROM bo_mailing a, userDB b, Asesores c, bo_actividades d
	WHERE a.user=b.userid AND b.username=c.Usuario AND a.actividad=d.bo_act_id
    AND date_created>='2016-02-08' AND date_created<='2016-02-08 23:59:59'
	GROUP BY a.user, CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE), a.actividad";

echo "$query<br>";
?>
<?php
include("connectDB.php");

$query="SELECT * FROM `Cambios de Turno`";
$result=mysql_query($query);
$cambios_num=mysql_numrows($result);

$i=0;

while ($i < $cambios_num){
	$cambios_id=mysql_result($result,$i,'id');
	$cambios_id_asesor=mysql_result($result,$i,'id_asesor');
	$cambios_tipo=mysql_result($result,$i,'tipo');
	$cambios_fechaI=mysql_result($result,$i,'fecha_inicio');
	$cambios_fechaF=mysql_result($result,$i,'fecha_fin');
	$cambios_fechaDescansoI=mysql_result($result,$i,'fechaDescanso_inicio');
	$cambios_fechaDescansoF=mysql_result($result,$i,'fechaDescanso_fin');
	$cambios_id_asesor_cambio=mysql_result($result,$i,'id_asesor_cambio');
	$cambios_caso=mysql_result($result,$i,'caso');
$i++;
}

mysql_close;

?>
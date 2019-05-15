<?php
include("connectDB.php");

$query="SELECT * FROM `Historial Llamadas Forecast` LEFT JOIN (SELECT `id`, `Dia`, `Mes`, `Anio`, `Skill` FROM `Historial Llamadas`) AS HL ON `Historial Llamadas Forecast`.id=`HL`.id";
$result=mysql_query($query);

$FLLnum=mysql_numrows($result);

mysql_close();

$i=0;
while ($i < $FLLnum) {

$FLLid[$i]=mysql_result($result,$i,"id");
$FLLDia[$i]=mysql_result($result,$i,"Dia");
$FLLMes[$i]=mysql_result($result,$i,"Mes");
$FLLAnio[$i]=mysql_result($result,$i,"Anio");
$FLLSkill[$i]=mysql_result($result,$i,"Skill");


$i1=1;
while ($i1<=48){
	
	$FLLc[$FLLid[$i]][$i1]=mysql_result($result,$i,$i1+1);
$i1++;
}

$i++;
}


?>
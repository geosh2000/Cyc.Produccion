<?php
include("connectDB.php");

$query="SELECT * FROM `Historial Llamadas AHT` LEFT JOIN (SELECT `id`, `Dia`, `Mes`, `Anio`, `Skill` FROM `Historial Llamadas`) AS HL ON `Historial Llamadas AHT`.id=`HL`.id";
$result=mysql_query($query);

$AHTnum=mysql_numrows($result);

mysql_close();

$i=0;
while ($i < $AHTnum) {

$AHTid[$i]=mysql_result($result,$i,"id");
$AHTDia[$i]=mysql_result($result,$i,"Dia");
$AHTMes[$i]=mysql_result($result,$i,"Mes");
$AHTAnio[$i]=mysql_result($result,$i,"Anio");
$AHTSkill[$i]=mysql_result($result,$i,"Skill");


$i1=1;
while ($i1<=48){
	
	$AHTc[$AHTid[$i]][$i1]=mysql_result($result,$i,$i1+1);
$i1++;
}

$i++;
}


?>
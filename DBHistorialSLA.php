<?php
include("connectDB.php");

$query1="SELECT * FROM `Historial Llamadas SLA` LEFT JOIN (SELECT `id`, `Dia`, `Mes`, `Anio`, `Skill` FROM `Historial Llamadas`) AS HL ON `Historial Llamadas SLA`.id=`HL`.id WHERE `time`='20'";
$query2="SELECT * FROM `Historial Llamadas SLA` LEFT JOIN (SELECT `id`, `Dia`, `Mes`, `Anio`, `Skill` FROM `Historial Llamadas`) AS HL ON `Historial Llamadas SLA`.id=`HL`.id WHERE `time`='30'";
$result=mysql_query($query1);
$result2=mysql_query($query2);

$SLAnum1=mysql_numrows($result);
$SLAnum2=mysql_numrows($result2);

mysql_close();

$i=0;
while ($i < $SLAnum1) {

$SLA20id[$i]=mysql_result($result,$i,"id");
$SLA20Dia[$i]=mysql_result($result,$i,"Dia");
$SLA20Mes[$i]=mysql_result($result,$i,"Mes");
$SLA20Anio[$i]=mysql_result($result,$i,"Anio");
$SLA20Skill[$i]=mysql_result($result,$i,"Skill");


$i1=1;
while ($i1<=48){
	
	$SLA20c[$SLA20id[$i]][$i1]=mysql_result($result,$i,$i1+1);
$i1++;
}

$i++;
}

$i=0;
while ($i < $SLAnum2) {

$SLA30id[$i]=mysql_result($result2,$i,"id");
$SLA30Dia[$i]=mysql_result($result2,$i,"Dia");
$SLA30Mes[$i]=mysql_result($result2,$i,"Mes");
$SLA30Anio[$i]=mysql_result($result2,$i,"Anio");
$SLA30Skill[$i]=mysql_result($result2,$i,"Skill");


$i1=1;
while ($i1<=48){
	
	$SLA30c[$SLA30id[$i]][$i1]=mysql_result($result2,$i,$i1+1);
$i1++;
}

$i++;
}
?>
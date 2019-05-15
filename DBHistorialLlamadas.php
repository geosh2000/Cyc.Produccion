<?php
include("connectDB.php");

$query="SELECT * FROM `Historial Llamadas`";
$result=mysql_query($query);

$HLLnum=mysql_numrows($result);

mysql_close();

$i=0;
while ($i < $HLLnum) {

$HLLid[$i]=mysql_result($result,$i,"id");
$HLLDia[$i]=mysql_result($result,$i,"Dia");
$HLLMes[$i]=mysql_result($result,$i,"Mes");
$HLLAnio[$i]=mysql_result($result,$i,"Anio");
$HLLSkill[$i]=mysql_result($result,$i,"Skill");
$HLLLupdate[$i]=mysql_result($result,$i,"LastUpdate");

$i1=1;
while ($i1<=48){
	$HLLc[$HLLid[$i]][$i1]=mysql_result($result,$i,$i1+5);
$i1++;
}

$i++;
}

?>
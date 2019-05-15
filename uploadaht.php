<?php


//Parametros para accesar SQL
$ud_registros=$_GET['reg'];

$r=1;

while ($r <= $ud_registros) {
$aht[$r]=$_GET["a".$r];
$calls[$r]=$_GET["c".$r];
$acum[$r]=$_GET["ac".$r];
$r++;
}
$date=$_GET["date"];

include("../connectDB.php");

$i=1;
while ($i<=$ud_registros){
$query="UPDATE `AHT Ventas` SET aht='$aht[$i]',Llamadas='$calls[$i]', acumulado='$acum[$i]', fecha='$date' WHERE ID='$i'";
mysql_query($query);
$i++;
}

echo "Record Updated";




/*
$ud_ID=1;
$ud_TMonto=$_GET['tmonto'];
$ud_YMonto=$_GET['ymonto'];
$ud_LWMonto=$_GET['lwmonto'];




$id=1;
include("../connectDB.php");


$query="UPDATE `AHT Ventas` SET TMonto='$ud_TMonto',YMonto='$ud_YMonto',LWMonto='$ud_LWMonto' WHERE ID='$ud_ID'";
mysql_query($query);
echo "Record Updated";


$query="SELECT * FROM `SLA Ventas`";
$result=mysql_query($query);

$num=mysql_numrows($result);



echo "<b><center>Database Output</center></b><br><br>";

$i=0;
while ($i < $num) {

$e_tmonto=mysql_result($result,$i,"TMonto");
$e_ymonto=mysql_result($result,$i,"YMonto");
$e_lwmonto=mysql_result($result,$i,"LWMonto");


$i++;
}
echo "<br>Monto Hoy: $e_tmonto<br>Monto Ayer: $e_ymonto<br>Monto LW: $e_lwmonto<hr><br>";
*/
?>
<?php


//Parametros para accesar SQL
$ud_registros=$_GET['reg'];
$ud_registros1=$_GET['reg1'];

$r=$ud_registros1;

while ($r <= $ud_registros) {

$calls[$r]=$_GET["calls".$r];

$locs[$r]=$_GET["locs".$r];
$fc[$r]=$_GET["fc".$r];
$fcmp[$r]=$_GET["fcmp".$r];
$r++;
}
$date=$_GET["date"];



include("../connectDB.php");

$i=$ud_registros1;
while ($i<=$ud_registros){
$query="UPDATE `FC Ventas` SET Locs='$locs[$i]', Llamadas='$calls[$i]', FC='$fc[$i]',  fecha='$date' WHERE ID='$i'";
mysql_query($query);
$i++;
}

echo "Record Updated";





?>
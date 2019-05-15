<?php


//Parametros para accesar SQL
$ud_registros=$_GET['reg'];
$ud_registros1=$_GET['reg1'];

$r=$ud_registros1;

while ($r <= $ud_registros) {


$monto[$r]=$_GET["monto".$r];
$mmp[$r]=$_GET["mmp".$r];

$r++;
}
$date=$_GET["date"];

include("../connectDB.php");

$i=$ud_registros1;
while ($i<=$ud_registros){
$query="UPDATE `FC Ventas` SET Monto='$monto[$i]', MP='$mmp[$i]', fecha='$date' WHERE ID='$i'";
mysql_query($query);
$i++;
}

echo "Record Updated";





?>
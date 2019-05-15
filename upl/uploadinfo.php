<?php


//Parametros para accesar SQL

$ud_ID=1;
$ud_SVentas=$_GET['tvsla'];
$ud_SSC=$_GET['tscsla'];
$ud_HVentas=$_GET['tvaht'];
$ud_HSC=$_GET['tscaht'];
$ud_VLlamadas=$_GET['tvcalls'];
$ud_VLLMP=$_GET['tmpcalls'];
$ud_SCLlamadas=$_GET['tsccalls'];
$ud_VLW=$_GET['lwvcalls'];
$ud_MPLW=$_GET['lwmpcalls'];
$ud_SCLW=$_GET['lwsccalls'];
$ud_VY=$_GET['yvcalls'];
$ud_MPY=$_GET['ympcalls'];
$ud_SCY=$_GET['ysccalls'];
$ud_HVY=$_GET['yvaht'];
$ud_HSCY=$_GET['yscaht'];
$ud_HVLW=$_GET['lwvaht'];
$ud_HSCLW=$_GET['lwscaht'];
$ud_date=$_GET['date'];




$id=1;
include("../connectDB.php");


$query="UPDATE `SLA Ventas` SET SVentas='$ud_SVentas', SSC='$ud_SSC', HVentas='$ud_HVentas', VLlamadas='$ud_VLlamadas',SCLlamadas='$ud_SCLlamadas',VLLMP='$ud_VLLMP', VLW='$ud_VLW',MPLW='$ud_MPLW',SCLW='$ud_SCLW',VY='$ud_VY',MPY='$ud_MPY',SCY='$ud_SCY',HSC='$ud_HSC',HVY='$ud_HVY',HSCY='$ud_HSCY',HVLW='$ud_HVLW',HSCLW='$ud_HSCLW',date='$ud_date' WHERE ID='$ud_ID'";
mysql_query($query);
echo "Record Updated";


$query="SELECT * FROM `SLA Ventas`";
$result=mysql_query($query);

$num=mysql_numrows($result);



echo "<b><center>Database Output</center></b><br><br>";

$i=0;
while ($i < $num) {

$ID=mysql_result($result,$i,"ID");
$SVentas=mysql_result($result,$i,"SVentas");
$SSC=mysql_result($result,$i,"SSC");
$HVentas=mysql_result($result,$i,"HVentas");
$HSC=mysql_result($result,$i,"HSC");
$VLlamadas=mysql_result($result,$i,"VLlamadas");
$VLLMP=mysql_result($result,$i,"VLLMP");
$SCLlamadas=mysql_result($result,$i,"SCLlamadas");
$VLW=mysql_result($result,$i,"VLW");
$MPLW=mysql_result($result,$i,"MPLW");
$SCLW=mysql_result($result,$i,"SCLW");
$VY=mysql_result($result,$i,"VY");
$SCY=mysql_result($result,$i,"SCY");
$MPY=mysql_result($result,$i,"MPY");


echo "<br>Llamadas Ventas: $VLlamadas<br>Llamadas MP: $VLLMP<br>SLA Ventas: $SVentas<br>Llamadas SC: $SCLlamadas<br>SLA SC: $SSC<br>AHT Ventas: $HVentas<br>Ventas LW: $VLW<br>MP LW: $MPLW<br>SC LW: $SCLW<br>Ventas Y: $VY<br>MP Y: $MPY<br>SC Y: $SCY<br>AHT SC: $HSC<br>ID: $ID<br><hr><br>";

$i++;
}


?>
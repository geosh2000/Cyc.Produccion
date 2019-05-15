<?php
//Parametros para accesar SQL

$ud_ID=1;
$ud_SVentas=$_POST['ud_SVentas'];
$ud_SSC=$_POST['ud_SSC'];
$ud_HVentas=$_POST['ud_HVentas'];
$ud_HSC=$_POST['ud_HSC'];
$ud_VLlamadas=$_POST['ud_VLlamadas'];
$ud_VLLMP=$_POST['ud_VLLMP'];
$ud_SCLlamadas=$_POST['ud_SCLlamadas'];
$ud_VLW=$_POST['ud_VLW'];
$ud_MPLW=$_POST['ud_MPLW'];
$ud_SCLW=$_POST['ud_SCLW'];
$ud_VY=$_POST['ud_VY'];
$ud_MPY=$_POST['ud_MPY'];
$ud_SCY=$_POST['ud_SCY'];

$id=1;
include("../connectDB.php");


$query="UPDATE `SLA Ventas` SET SVentas='$ud_SVentas', SSC='$ud_SSC', HVentas='$ud_HVentas', VLlamadas='$ud_VLlamadas',SCLlamadas='$ud_SCLlamadas',VLLMP='$ud_VLLMP', VLW='$ud_VLW',MPLW='$ud_MPLW',SCLW='$ud_SCLW',VY='$ud_VY',MPY='$ud_MPY',SCY='$ud_SCY',HSC='$ud_HSC' WHERE ID='$ud_ID'";
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
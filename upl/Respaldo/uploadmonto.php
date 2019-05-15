<?php


//Parametros para accesar SQL

$ud_ID=1;
$ud_TMonto=$_GET['tmonto'];
$ud_YMonto=$_GET['ymonto'];
$ud_LWMonto=$_GET['lwmonto'];
$l30v=$_GET['l30v'];
$l30sc=$_GET['l30sc'];
$fc=$_GET['fc'];
$fcmp=$_GET['fcmp'];
$bfall=$_GET['bfall'];
$bfmp=$_GET['bfmp'];
$bfmonto=$_GET['bfmonto'];
$my=$_GET['my'];
$bfmall=$_GET['bfmall'];
$mlw=$_GET['mlw'];
$yint=$_GET['yint'];
$hint=$_GET['hint'];
$lwint=$_GET['lwint'];
$hci=$_GET['hi'];
$yci=$_GET['yi'];
$lwci=$_GET['lwi'];
$fcint=$_GET['fci'];







$id=1;
include("../connectDB.php");
echo "<b><center>Database Output</center></b><br><br>";

$query="UPDATE `SLA Ventas` SET TMonto='$ud_TMonto',YMonto='$ud_YMonto',LWMonto='$ud_LWMonto',l30V='$l30v',l30SC='$l30sc', fc='$fc', fcmp='$fcmp', bfall='$bfall', bfmp='$bfmp', bfmonto='$bfmonto',bfmall='$bfmall',my='$my',mlw='$mlw',`hoymontointer`='$hint',`ymontointer`='$yint',`lwmontointer`='$lwint',`hcallsinter`='$hci',`ycallsinter`='$yci', `lwcallsinter`='$lwci',`fcinter`='$fcint' WHERE ID='$ud_ID'";
mysql_query($query);
if(mysql_error()){
    echo mysql_error()."<br>$query";
}else{
    echo "Record Updated";  
}



$query="SELECT * FROM `SLA Ventas`";
$result=mysql_query($query);

$num=mysql_numrows($result);





$i=0;
while ($i < $num) {

$e_tmonto=mysql_result($result,$i,"TMonto");
$e_ymonto=mysql_result($result,$i,"YMonto");
$e_lwmonto=mysql_result($result,$i,"LWMonto");


$i++;
}
echo "<br>Monto Hoy: $e_tmonto<br>Monto Ayer: $e_ymonto<br>Monto LW: $e_lwmonto<hr><br>";

?>
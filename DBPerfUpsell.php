<?php
include("connectDB.php");


$query="SELECT * FROM `PerfUpsell`";
$result=mysql_query($query);
$numPUp=mysql_numrows($result);

$i=0;
while ($i<$numPUp){
$PUpid[$i]=mysql_result($result,$i,"id");
$PUplocs[$i]=mysql_result($result,$i,"locs");
$PUpmonto[$i]=mysql_result($result,$i,"monto");
$PUpht[$i]=mysql_result($result,$i,"ht");
$fecha[$i]=mysql_result($result,$i,"fecha");
$i++;
}

mysql_close();

?>
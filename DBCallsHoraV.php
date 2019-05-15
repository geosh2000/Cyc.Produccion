<?php
include("connectDB.php");
$query="SELECT * FROM `Comportamiento Hora`";
$result=mysql_query($query);
$numCallsV=mysql_numrows($result);

mysql_close();

$i=0;
while ($i < $numCallsV){

    $CVid[$i]=mysql_result($result,$i,"id");
    $CVHora[$i]=mysql_result($result,$i,"hora");
    $CVlw[$i]=mysql_result($result,$i,"lw");
    $CVy[$i]=mysql_result($result,$i,"y");
    $CVf[$i]=mysql_result($result,$i,"forecast");
    $CVt[$i]=mysql_result($result,$i,"t");
    $CVdate[$i]=mysql_result($result,$i,"fecha");

$i++;
}

?>
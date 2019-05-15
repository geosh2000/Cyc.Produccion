<?php
include("connectDB.php");

$query="SELECT * FROM `Dias Pendientes`";
$result=mysql_query($query);

$DPnum=mysql_numrows($result);

mysql_close();

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$i=0;
while ($i < $DPnum) {

$DPid[$i]=mysql_result($result,$i,"id");
$DPdias[$i]=mysql_result($result,$i,"dias asignados");
$DPday[$i]=mysql_result($result,$i,"day");
$DPmonth[$i]=mysql_result($result,$i,"month");
$DPyear[$i]=mysql_result($result,$i,"year");
$DPmotivo[$i]=mysql_result($result,$i,"motivo");

$i++;
}

?>
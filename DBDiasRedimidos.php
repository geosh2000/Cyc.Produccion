<?php
include("connectDB.php");

$query="SELECT * FROM `Dias Pendientes Redimidos`";
$result=mysql_query($query);

$DPRnum=mysql_numrows($result);

mysql_close();

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$i=0;
while ($i < $DPRnum) {

$DPRid[$i]=mysql_result($result,$i,"id");
$DPRdias[$i]=mysql_result($result,$i,"dias");
$DPRday[$i]=mysql_result($result,$i,"day");
$DPRmonth[$i]=mysql_result($result,$i,"month");
$DPRyear[$i]=mysql_result($result,$i,"year");
$DPRmotivo[$i]=mysql_result($result,$i,"motivo");
$DPRcaso[$i]=mysql_result($result,$i,"caso");

$i++;
}

?>
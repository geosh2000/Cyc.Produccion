<?php
include("connectDB.php");

$id=$_GET['id1'];
$mes=$_GET['mes'];
$dia=$_GET['dia'];
$rcode=$_GET['e1'];

$num=0;
$query = "SELECT * FROM `Historial PyA` WHERE (idnumber='$id' AND mes='$mes' AND dia='$dia')";
$query0 = "SELECT * FROM `Historial PyA`";
$result=mysql_query($query);
$num=mysql_numrows($result);
$result0=mysql_query($query0);
$num0=mysql_numrows($result0);



if($num!=0){
$query2 = "UPDATE `Historial PyA` SET rcode='$rcode' WHERE (idnumber='$id' AND mes='$mes' AND dia='$dia')";
}else{
$indice=mysql_result($result0,$num0-1,"indice")+1;
$query2 = "INSERT INTO `Historial PyA` (idnumber,dia,mes,rcode,indice) VALUES (".$id.",".$dia.",".$mes.",".$rcode.",".$indice.")";

}

mysql_query($query2);

mysql_close();

echo "Dia= ".$dia." Mes= ".$mes." ID= ".$id." Code= ".$rcode."<br>".$query2."<br>Rows= ".$num0."<br>Indice= ".$indice;


?>
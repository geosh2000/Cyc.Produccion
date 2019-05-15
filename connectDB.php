<?php
$username="comeycom_wfm";
$password="pricetravel2015";
$database="comeycom_WFM";
//$host="67.227.144.24";
$host="localhost";

//Conectar a DB

mysql_connect($host,$username,$password);
@mysql_select_db($database) or die( "Error en la base de datos Actual");

mysql_query("SET NAMES 'utf-8'");

/*$query="SET time_zone = \"-05:00\";";
mysql_query($query);*/

//msqli
$connectdb = new mysqli($host, $username, $password, $database);



//wfm parameters
$username="ccexporter.usr";
$password="IFaoCJiH09rEqLVZVLsj";
$database="ccexporter";
$host="localhost";

//msqli
$connectdbcc = new mysqli($host, $username, $password, $database);

?>

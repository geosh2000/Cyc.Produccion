<?php


//wfm parameters
$username="ccexporter.usr";
$password="IFaoCJiH09rEqLVZVLsj";
$database="ccexporter";
$host="localhost";

/*
//CyC parameters
$username="comeycom_wfm";
$password="pricetravel2015";
$database="comeycom_WFM";
$host="localhost";
*/

//Conectar a DB

mysql_connect($host,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

mysql_query("SET NAMES 'utf-8'");

$query="SET time_zone = \"-05:00\";";
mysql_query($query);



?>

<?
$username="comeycom_wfm";
$password="pricetravel2015";
$database="comeycom_WFM";

//Conectar a DB

mysql_connect('localhost',$username,$password);
@mysql_select_db($database) or die( "Error en la base de datos");

mysql_query("SET NAMES 'utf-8'");

$query="SET time_zone = \"-05:00\";";
mysql_query($query);



?>

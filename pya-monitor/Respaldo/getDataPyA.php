<?
//Parametros para accesar SQL



$id=1;
$username="comeycom_wfm";
$password="pricetravel2015";
$database="comeycom_SLA";

//Conectar a DB

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");


mysql_query($query);



$query="SELECT * FROM `PyA Monitor`";
$result=mysql_query($query);

$num=mysql_numrows($result);

mysql_close();

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");



while ($i < $num) {

$idnumber[$i]=mysql_result($result,$i,"id");
$NCorto[$i]=mysql_result($result,$i,"N Corto");
$tiempo[$i]=mysql_result($result,$i,"Tiempo");
$control[$i]=mysql_result($result,$i,"Control");
$fecha[$i]=mysql_result($result,$i,"Fecha");
$horario[$i]=mysql_result($result,$i,"Horario");
$exc[$i]=mysql_result($result,$i,"Excepcion");


$i++;
}



?>
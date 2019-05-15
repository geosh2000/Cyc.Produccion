<?php
include("pt/connectDB.php");

date_default_timezone_set('America/Bogota');

$PUpmonth=intval(date('m'));
$PUpday=intval(date('d'));
$PUpyear=intval(date('y'));
$PUphour=date('H');
$PUpminute=date('i');
$PUptime= $PUphour.":".$PUpminute." hrs. ".$PUpday."-".$PUpmonth."-".$PUpyear;

$i=0;
while ($i<100){
	$id[$i]=$_GET['id'.$i];
	$monto[$i]=$_GET['monto'.$i];
	$ht[$i]=$_GET['ht'.$i];
	$locs[$i]=$_GET['locs'.$i];
$i++;
}


$i=0;
while($i<100){
	if ($id!=NULL){
		$query="UPDATE `PerfUpsell` SET monto='$monto[$i]', ht='$ht[$i]', locs='$locs[$i]', fecha='$PUptime' WHERE id='$id[$i]'";
		mysql_query($query);
	}
$i++;
}



echo "Records Updated";

?>
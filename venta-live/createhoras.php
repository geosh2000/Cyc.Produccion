<?php 

include("../connectPDO.php");
date_default_timezone_set('America/Bogota');


for($i=0;$i<96;$i++){
	$data[$i][':HoraGroup']=($i/4);
	$data[$i][':Hora_int']=$i;
	$data[$i][':Hora_pretty']=floor($i/4).":".(($i/4-floor($i/4))*60);
	$data[$i][':Hora_time']=date('H:i:s', strtotime(floor($i/4).":".(($i/4-floor($i/4))*60).":00"));
}

$insert=$pdodb->prepare("INSERT INTO HoraGroup_Table15 VALUES (:HoraGroup, :Hora_int, :Hora_pretty, :Hora_time, ADDTIME(:Hora_time,'00:14:59'))");

foreach($data as $index => $info){
	if($insert->execute($info)){
		echo $index." -> OK<br>";
	}else{
		echo $index." -> Error<br>";
	}
}

$pdodb=null;
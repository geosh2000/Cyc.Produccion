<?php
include_once('../modules/modules.php');
include_once("../common/erlangC.php");

timeAndRegion::setRegion('Cun');


if(isset($_POST['start'])){
	$inicio=date('Y-m-d', strtotime($_POST['start']));
}else{
	$inicio=date('Y-m-d', strtotime('-7 days'));
}

if(isset($_POST['end'])){
	$fin=date('Y-m-d', strtotime($_POST['end']));
}else{
	$fin=date('Y-m-d', strtotime('-1 days'));
}

$skill=$_POST['skill'];

//is inbound?
$query="SELECT * FROM PCRCs WHERE id=$skill";
if($result=Queries::query($query)){
	$fila=$result->fetch_assoc();
	$inbound=$fila['inbound_calls'];
}

//SLAS
function getTarget($tipo,$skill,$date){

	//Set Data
	IF($skill==3 || $skill==35){
		if(date('Y-m-d',strtotime($date))>=date('Y-m-d',strtotime('2016-08-01'))){
			$slr=0.80;
			$tat=20;
		}else{
			$slr=0.80;
			$tat=20;
		}
	}else{
		$slr=0.70;
		$tat=30;
	}

	//Return Info
	switch($tipo){
		case 'slr':
			return $slr;
			break;
		case 'tat':
			return $tat;
			break;
	}
}

//Forecast
$query="SELECT
        a.Fecha, hora, ROUND(volumen*participacion) as forecast, volumen, AHT, Reductores
      FROM
        forecast_volume a
      RIGHT JOIN
        forecast_participacion b ON a.Fecha=b.Fecha AND a.skill=b.skill
      WHERE
        a.skill=$skill AND
        a.Fecha BETWEEN '$inicio' AND '$fin'";
IF($result=Queries::query($query)){
    while($fila=$result->fetch_assoc()){
      $data[$fila['Fecha']]['forecast'][$fila['hora']]=$fila['forecast'];
      $data[$fila['Fecha']]['fc_volumen']=$fila['volumen'];
      $data[$fila['Fecha']]['fc_AHT']=$fila['AHT'];
      if($fila['forecast']/1800*$fila['AHT']==0){
        $data[$fila['Fecha']]['erlang'][$fila['hora']]=0;
      }else{
        $data[$fila['Fecha']]['erlang'][$fila['hora']]=intval(agentno($fila['forecast']/1800*$fila['AHT'], getTarget('tat', $skill, $fila['Fecha']),$fila['AHT'],getTarget('slr', $skill, $fila['Fecha'])));
      }
      $data[$fila['Fecha']]['necesarios'][$fila['hora']]=intval($data[$fila['Fecha']]['erlang'][$fila['hora']]/(1-$fila['Reductores']));
      if($data[$fila['Fecha']]['necesarios'][$fila['hora']]=="" || $data[$fila['Fecha']]['necesarios'][$fila['hora']]==NULL){
        $data[$fila['Fecha']]['necesarios'][$fila['hora']]=0;
      }
    }
}

?>

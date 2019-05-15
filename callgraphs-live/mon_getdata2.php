<?php

include("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');
$connectdbcc=Connection::mysqliDB('WFM');

date_default_timezone_set('America/Bogota');

$depart=$_POST['dep'];
if(isset($_POST['fecha'])){
	$date=date('Y-m-d',strtotime($_POST['fecha']));	
}else{
	$date=date('Y-m-d');
}

if(isset($_POST['to'])){
	$dateto=date('Y-m-d',strtotime($_POST['to']));	
}else{
	$dateto=date('Y-m-d');
}



//Volumen
	$query="SELECT Canal, SUM(IF(Calls IS NULL,0,Calls)+IF(Unanswered IS NULL,0,Unanswered)) as Llamadas, MAX(Last_Update) as lu 
			FROM d_dids_calls a LEFT JOIN Dids b ON a.Did=b.DID 
			WHERE a.Skill IN (30,35) AND a.Fecha BETWEEN '$date' AND '$dateto' AND ((a.Did='Untracked' AND Skill=35) OR Canal LIKE '%MP MX%' OR Canal LIKE '%PriceLab%') 
			GROUP BY Canal";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			switch($fila['Canal']){
				case "MP MX":
					$canal="main";
					break;
				case "MP MX Movil":
					$canal="movil";
					break;
				case "MP MX Promo":
					$canal="promo";
					break;
				case "MP MX Promo Aereo":
					$canal="promoaereo";
					break;
				case "PriceLab Puebla":
					$canal="pricelabpbx";
					break;
				default:
					$canal="transfer";
					break;
			}
				    
		    $data[$canal]=intval($fila['Llamadas']);
			
			if($data['lu']==NULL || date('Y-m-d H:i:s',strtotime($fila['lu']))>$data['lu']){
				$data['lu']=date('Y-m-d H:i:s',strtotime($fila['lu']));	
			}
		}
	}else{
		echo $connectdb->error."<br>";
	}
	

	

if($data['main']==NULL){
		$data['main']=0;
}

if($data['promo']==NULL){
		$data['promo']=0;
}

if($data['promoaereo']==NULL){
		$data['promoaereo']=0;
}

if($data['pricelabpbx']==NULL){
		$data['pricelabpbx']=0;
}

if($data['transfer']==NULL){
		$data['transfer']=0;
}

if($data['movil']==NULL){
		$data['movil']=0;
}

//Forecast
$query="SELECT hora, a.Skill, participacion*volumen as fc FROM forecast_participacion a LEFT JOIN forecast_volume b ON a.Fecha=b.Fecha AND a.skill=b.skill  WHERE a.Fecha BETWEEN '$date' AND '$dateto' ORDER BY hora";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$output_tmp[$fila['Skill']]['forecast'][$fila['hora']]=intval($fila['fc']);	
	}
}


//Calls Hour
$query="SELECT Hora_int, Skill, COUNT(IF(Desconexion='Abandon',a.id,NULL) )as Abandoned, COUNT(IF(Desconexion!='Abandon',a.id,NULL)) as Answered, AVG(Duracion) as AHT, COUNT(IF(Desconexion!='Abandon' AND Wait<=IF(Skill IN (3,35),20,30),a.id,NULL))/COUNT(a.id)*100 as SLA FROM HoraGroup_Table c LEFT JOIN (SELECT a.id, Skill, Hora, Desconexion, Duracion, Wait FROM mon_calls_details a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha BETWEEN '$date' AND '$dateto') a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:29:29') GROUP BY Hora_group, Skill ORDER BY Hora_int";
if($result=$connectdbcc->query($query)){
		while($fila=$result->fetch_assoc()){
			$output_tmp[$fila['Skill']]['Answered'][$fila['Hora_int']]=intval($fila['Answered']);
			$output_tmp[$fila['Skill']]['Abandoned'][$fila['Hora_int']]=intval($fila['Abandoned']);
			$output_tmp[$fila['Skill']]['AHT'][$fila['Hora_int']]=intval($fila['AHT']);
			$output_tmp[$fila['Skill']]['SLA'][$fila['Hora_int']]=floatval($fila['SLA']);				
		}
}	

//Calls Hour LY
$query="SELECT Hora_int, Skill, COUNT(*)as Calls FROM HoraGroup_Table c LEFT JOIN (SELECT a.ac_id, Skill, Hora FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha=ADDDATE('$date',-371)) a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:29:29') GROUP BY Hora_group, Skill ORDER BY Hora_int";
if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$output_tmp[$fila['Skill']]['CallsLY'][$fila['Hora_int']]=intval($fila['Calls']);
		}
}else{
	echo $connectdb->error;
}

//Calls Hour Yd
$query="SELECT Hora_int, Skill, COUNT(*)as Calls FROM HoraGroup_Table c LEFT JOIN (SELECT a.ac_id, Skill, Hora FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha=ADDDATE('$date',-1)) a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:29:29') GROUP BY Hora_group, Skill ORDER BY Hora_int";
if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$output_tmp[$fila['Skill']]['CallsYd'][$fila['Hora_int']]=intval($fila['Calls']);
		}
}else{
	echo $connectdb->error;
}	

//Calls Hour LW
$query="SELECT Hora_int, Skill, COUNT(*)as Calls FROM HoraGroup_Table c LEFT JOIN (SELECT a.ac_id, Skill, Hora FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha=ADDDATE('$date',-7)) a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:29:29') GROUP BY Hora_group, Skill ORDER BY Hora_int";
if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$output_tmp[$fila['Skill']]['CallsLW'][$fila['Hora_int']]=intval($fila['Calls']);
		}
}else{
	echo $connectdb->error;
}	

foreach($output_tmp as $skill => $info){
	foreach($info as $categoria => $info2){
		for($i=0;$i<48;$i++){
			if(!isset($output_tmp[$skill][$categoria][$i])){
				$output[$skill][$categoria][]=0;
			}else{
				$output[$skill][$categoria][]=$output_tmp[$skill][$categoria][$i];
			}
		}
	}
}

$connectdbcc->close();
$connectdb->close();

$output['main']=$data['main']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['promo']=$data['promo']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['promoaereo']=$data['promoaereo']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['pricelabpbx']=$data['pricelabpbx']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['transfer']=$data['transfer']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['movil']=$data['movil']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;

$last_u = new DateTime($data['lu'].' America/Mexico_City');
$cuntime = new DateTimeZone('America/Bogota');

$last_u= $last_u->setTimezone($cuntime);
$output['lu']=$last_u->format('Y-m-d H:i:s');

foreach($output as $index => $info){
	if($index==NULL){
		$output[$index]=0;
	}
}

print json_encode($output,JSON_PRETTY_PRINT);

/*echo "<pre>";
print_r($output);
echo "</pre>";*/


 ?>


<?php

include("../connectMYSQLI.php");

date_default_timezone_set('America/Bogota');

$depart=$_GET['dep'];
if(isset($_GET['fecha'])){
	$date=date('Y-m-d',strtotime($_GET['fecha']));	
}else{
	$date=date('Y-m-d');
}

if(isset($_GET['to'])){
	$dateto=date('Y-m-d',strtotime($_GET['to']));	
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

	$connectdb->close();

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
	
	
$output['main']=$data['main']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['promo']=$data['promo']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['promoaereo']=$data['promoaereo']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['pricelabpbx']=$data['pricelabpbx']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['transfer']=$data['transfer']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['movil']=$data['movil']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['lu']=$data['lu'];

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


<?php

include_once("../modules/modules.php");

date_default_timezone_set('America/Bogota');

$connectdb=Connection::mysqliDB('CC');
$connectdbcc=Connection::mysqliDB('WFM');

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

$date=$dateto;


//Volumen
	$query="SELECT IF(Canal IS NULL,'Xfer',Canal) as Canal, SUM(IF(Calls IS NULL,0,Calls)+IF(Unanswered IS NULL,0,Unanswered)) as Llamadas, MAX(Last_Update) as lu
			FROM d_dids_calls a LEFT JOIN Dids b ON a.Did=b.DID
			WHERE a.Skill IN (30,35) AND IF(CAST('$date' as DATE)<CURDATE(),a.Fecha = '$date', a.Fecha=CURDATE()) AND ((a.Did='Untracked' AND Skill=35) OR Grupo_Canal LIKE '%MP%')
			GROUP BY b.Canal";
	$qTd = $query;
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){

		    $calls_d[$fila['Canal']]=intval($fila['Llamadas']);

			if($data['lu']==NULL || date('Y-m-d H:i:s',strtotime($fila['lu']))>$data['lu']){
				$data['lu']=date('Y-m-d H:i:s',strtotime($fila['lu']));
			}
		}
	}else{
		echo $connectdb->error."<br>";
	}

$query="SELECT DISTINCT Canal FROM Dids WHERE Grupo_Canal='MP' ORDER BY Canal";
if($result=$connectdb->query($query)){
  $i=0;
  while($fila=$result->fetch_assoc()){
    $canal[]=$fila['Canal'];
    $i++;
  }
}

$canal[]='Xfer';


foreach($canal as $ind => $info){
  @$calls[$info]=$calls_d[$info]/array_sum($calls_d)*100;
  $output['pie'][]=array($info,$calls[$info]);
}

//Forecast
$query="SELECT
	Hora_int as hora,
	fc/2 as fc
FROM
	HoraGroup_Table15 c
LEFT JOIN
	(SELECT
		hora, participacion*volumen as fc
	FROM
		forecast_participacion a
	LEFT JOIN
	 	forecast_volume b ON a.Fecha=b.Fecha AND a.skill=b.skill
	WHERE
		a.Fecha BETWEEN '$date' AND '$dateto' AND
		a.Skill=35
	ORDER BY hora) a ON a.hora=FLOOR(c.Hora_int/2)
GROUP BY
	Hora_group
ORDER BY
	Hora_int
	";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$output['forecast'][]=intval($fila['fc']);
	}
}

$query="DROP TEMPORARY TABLE IF EXISTS monptdid;
        CREATE TEMPORARY TABLE monptdid (SELECT
            a.id, Hora, Desconexion,
            IF(SUBSTR(Agente,1,LOCATE('(', Agente)-2)='',0,getIdAsesor(SUBSTR(Agente,1,LOCATE('(', Agente)-2),2)) as asesorID
          FROM
            ccexporter.mon_calls_details a
          LEFT JOIN
            Cola_Skill b ON a.Cola=b.Cola
          WHERE
            Fecha = '$date'
            AND Skill=35)";
$qTd = $query;

$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}


//Calls Hour
$query="SELECT
          Hora_int, COUNT(IF(Desconexion='Abandon',a.id,NULL) )as Abandoned, COUNT(IF(Desconexion!='Abandon' && desborde IS NULL,a.id,NULL)) as Answered ,
          COUNT(IF(Desconexion!='Abandon' && desborde IS NOT NULL,a.id,NULL)) as Desborde
        FROM
          HoraGroup_Table15 c
        LEFT JOIN
          (SELECT
          a.id, Hora, Desconexion, b.*, IF(dep=29 AND puesto!=1 AND cc IS NULL,'Desborde',NULL) as desborde
        FROM
          monptdid a
        LEFT JOIN
          daily_dep b ON asesorID!=0 AND a.asesorID=b.asesor
      	LEFT JOIN
      		cc_apoyo c ON b.asesor=c.asesor AND CURDATE() BETWEEN c.inicio AND c.fin) a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:14:59')
        GROUP BY Hora_group ORDER BY Hora_int";
if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$output['Answered'][]=intval($fila['Answered']);
			$output['Abandoned'][]=intval($fila['Abandoned']);
			$output['Desborde'][]=intval($fila['Desborde']);
		}
}

//Calls Hour LY
$query="SELECT Hora_int, COUNT(*)as Calls FROM HoraGroup_Table15 c LEFT JOIN (SELECT a.ac_id, Hora FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha=ADDDATE('$date',-364) AND Skill=35) a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:14:59') GROUP BY Hora_group ORDER BY Hora_int";
if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$output['CallsLY'][]=intval($fila['Calls']);
		}
}else{
	echo $connectdb->error;
}

//Calls Hour Yd
$query="SELECT Hora_int, COUNT(*)as Calls FROM HoraGroup_Table15 c LEFT JOIN (SELECT a.ac_id, Hora FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha=ADDDATE('$date',-1) AND Skill=35) a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:14:59') GROUP BY Hora_group ORDER BY Hora_int";
if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$output['CallsYd'][]=intval($fila['Calls']);
		}
}else{
	echo $connectdb->error;
}

//Calls Hour LW
$query="SELECT Hora_int, COUNT(*)as Calls FROM HoraGroup_Table15 c LEFT JOIN (SELECT a.ac_id, Hora FROM t_Answered_Calls a LEFT JOIN Cola_Skill b ON a.Cola=b.Cola WHERE Fecha=ADDDATE('$date',-7) AND Skill=35) a ON a.Hora BETWEEN c.Hora_time AND ADDTIME(c.Hora_time,'00:14:59') GROUP BY Hora_group ORDER BY Hora_int";
if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$output['CallsLW'][]=intval($fila['Calls']);
		}
}else{
	echo $connectdb->error;
}

$connectdbcc->close();
$connectdb->close();

/*
$output['main']=$data['main']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['promo']=$data['promo']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['promoaereo']=$data['promoaereo']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['pricelabpbx']=$data['pricelabpbx']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['transfer']=$data['transfer']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;
$output['movil']=$data['movil']/($data['main']+$data['promo']+$data['promoaereo']+$data['pricelabpbx']+$data['transfer']+$data['movil'])*100;

$output['pie'][]=array('Main',$output['main']);
$output['pie'][]=array('promo',$output['promo']);
$output['pie'][]=array('promoaereo',$output['promoaereo']);
$output['pie'][]=array('pricelabpbx',$output['pricelabpbx']);
$output['pie'][]=array('transfer',$output['transfer']);
$output['pie'][]=array('movil',$output['movil']);*/

$last_u = new DateTime($data['lu'].' America/Mexico_City');
$cuntime = new DateTimeZone('America/Bogota');

$last_u= $last_u->setTimezone($cuntime);
$output['lu']=$last_u->format('Y-m-d H:i:s');

foreach($output as $index => $info){
	if($index==NULL){
		$output[$index]=0;
	}
}

$output['qTd']=$qTd;

print json_encode($output,JSON_PRETTY_PRINT);

/*echo "<pre>";
print_r($output);
echo "</pre>";*/


 ?>

<?php
include("../connectDB.php");
date_default_timezone_set('America/Bogota');

$fecha=date('Y-m-d', strtotime($_POST['fecha']));

//Error Handler

function divError(){
 echo "";
}
set_error_handler("divError");

$query="SELECT 
			Hora_int, Skill, sla20, sla30, Calls
		FROM 
			HoraGroup_Table a
		LEFT JOIN
			(
				SELECT 
					b.Skill,
					HOUR(Hora)*2 + IF(MINUTE(Hora)>=30,1,0) as HoraG,
					COUNT(IF(Answered=1 AND TIME_TO_SEC(Espera<=20),ac_id,NULL)) as sla20, 
					COUNT(IF(Answered=1 AND TIME_TO_SEC(Espera<=30),ac_id,NULL)) as sla30, 
					COUNT(*) as Calls
				FROM
					t_Answered_Calls a
				LEFT JOIN
					Cola_Skill b ON a.Cola=b.Cola
				WHERE
					Fecha='$fecha'
				GROUP BY
					HoraG, Skill
			) b
		ON a.Hora_int=b.HoraG";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		
			$td[$fila['Skill']][$fila['Hora_int']]['sla20']=$fila['sla20'];
			$td[$fila['Skill']][$fila['Hora_int']]['sla30']=$fila['sla30'];
			$td[$fila['Skill']][$fila['Hora_int']]['calls']=$fila['Calls'];
		
	}
}

foreach($td as $skill => $info){
	for($i=0;$i<48;$i++){
		if(count($info[$i])==0){
			$td[$skill][$i]['sla20']=0;
			$td[$skill][$i]['sla30']=0;
			$td[$skill][$i]['Calls']=0;
		}
	}
}

print json_encode($td,JSON_PRETTY_PRINT);



?>

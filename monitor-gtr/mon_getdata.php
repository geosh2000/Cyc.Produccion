<?php
//Old Version Connect
	//include("../connectDB.php");

//New Version Connect
	include("../connectDB_WFM.php");
	
date_default_timezone_set('America/Bogota');

$depart=$_GET['dep'];
if(isset($_GET['fecha'])){
	$date=date('Y-m-d',strtotime($_GET['fecha']));	
}else{
	$date=date('Y-m-d');
}

//New Version Connect
if(date('Y-m-d',strtotime($date))==date('Y-m-d')){
	include("../connectDB_WFM.php");
}else{
	include("../connectDB_cyc.php");	
}

//echo "$date<br>";

//header('Content-Type: text/html; charset=utf-8');

//Volumen
if(date('Y-m-d',strtotime($date))==date('Y-m-d')){
	$query="SELECT 
				Fecha, CONCAT(HOUR(Hora),if(MINUTE(Hora)>=30,'.5','')) as HoraGroup, Skill, COUNT(*) as Ofrecidas,
				COUNT(IF(Answered=0,a.id,NULL)) as Abandonadas,
				COUNT(IF(Wait<=20,a.id,NULL)) as SLA20, COUNT(IF(Wait<=30,a.id,NULL)) as SLA30, 
				AVG(IF(Answered=1,Duracion,NULL)) as AHT
			FROM 
				mon_calls_details a 
			LEFT JOIN 
				Cola_Skill b ON a.Cola=b.Cola 
			WHERE 
				Fecha=CURDATE() 
			GROUP BY 
				Fecha, HoraGroup, Skill";
}else{
	$query="SELECT 
				Fecha, CONCAT(HOUR(Hora),if(MINUTE(Hora)>=30,'.5','')) as HoraGroup, Skill, COUNT(*) as Ofrecidas,
				COUNT(IF(Answered=0,ac_id,NULL)) as Abandonadas,
				COUNT(IF(TIME_TO_SEC(Espera)<=20,a.ac_id,NULL)) as SLA20, COUNT(IF(TIME_TO_SEC(Espera)<=30,a.ac_id,NULL)) as SLA30, 
				AVG(IF(Answered=1,Duracion_Real,NULL)) as AHT
			FROM 
				t_Answered_Calls a 
			LEFT JOIN 
				Cola_Skill b ON a.Cola=b.Cola 
			WHERE 
				Fecha='$date' AND 
				Skill IN (3,35,4,7,8,9)
			GROUP BY 
				Fecha, HoraGroup, Skill";	
}
	$result=mysql_query($query);
	//echo $query;
	if(mysql_error()){
//		echo mysql_error()."<br>";
	}
	$num=mysql_numrows($result);
	//echo "$num<br>";
	for($i=0;$i<$num;$i++){
		$time=30;

	    //Rename Deps for SQL
	    switch(mysql_result($result,$i,'Skill')){
	        case 4:
	            $query_dep="SAC";
	            $skill=4;
	            break;
	        case 9:
	            $query_dep="TMP";
	            $skill=9;
	            break;
	        case 8:
	            $query_dep="TMT";
	            $skill=8;
	            break;
	        case 7:
	            $query_dep="Agencias";
	            $skill=7;
	            break;
	        case 3:
	            $query_dep="Ventas";
	            $skill=3;
	            $time=20;
	            break;
	        case 35:
	            $query_dep="VentasMP";
	            $skill=35;
	            $time=20;
	            break;
	        default:
	            $query_dep=$deps;
	            $time=20;
	            break;
	    }	
	    
	    $data['volumen'][$query_dep][mysql_result($result,$i,'HoraGroup')]=intval(mysql_result($result,$i,'Ofrecidas'));
		$data['sla'][$query_dep][mysql_result($result,$i,'HoraGroup')]=intval(mysql_result($result,$i,'SLA'.$time));
		$data['aht'][$query_dep][mysql_result($result,$i,'HoraGroup')]=intval(mysql_result($result,$i,'AHT'));
		$data['abandon'][$query_dep][mysql_result($result,$i,'HoraGroup')]=intval(mysql_result($result,$i,'Abandonadas')/mysql_result($result,$i,'Ofrecidas')*100);
        
	}
	
	

$departamentos=["Ventas","VentasMP","SAC","TMP","TMT","Agencias"];
$horarios=[0,0.5,1,1.5,2,2.5,3,3.5,4,4.5,5,5.5,6,6.5,7,7.5,8,8.5,9,9.5,10,10.5,11,11.5,12,12.5,13,13.5,14,14.5,15,15.5,16,16.5,17,17.5,18,18.5,19,19.5,20,20.5,21,21.5,22,22.5,23,23.5];

//Fill Blanks
foreach($data as $metrica => $info){
	foreach($info as $departamento => $info2){
		$x=0;
		foreach($horarios as $index => $horaok){
			if(!isset($data[$metrica][$departamento]["$horaok"]) || $data[$metrica][$departamento]["$horaok"]==NULL){
				$data[$metrica][$departamento]["$horaok"]=0;	
			}
			
			//Volumen
			$output['vol'][$departamento][$x]=$data['volumen'][$departamento]["$horaok"];
				
	        //SLA
	        if($data['volumen'][$departamento]["$horaok"]==0){
	            $output['sla'][$departamento][$x]=null;
	        }else{
	            $output['sla'][$departamento][$x]=$data['sla'][$departamento]["$horaok"]/$data['volumen'][$departamento]["$horaok"]*100;
	        }
	
	        //AHT
	        $output['aht'][$departamento][$x]=$data['aht'][$departamento]["$horaok"];
			
			//Abandonadas
	        $output['abandon'][$departamento][$x]=$data['abandon'][$departamento]["$horaok"];
	
	        //Forecast
	        //$output['fc'][$deps][]=$out['fc'];
	
	        //Precision de Pronostico
	        /*if($out['fc']==0 ){
	            $output['fc'][$deps][]=null;
	        }else{
	            $output['prec'][$deps][]=$out['volumen']/$out['fc']*100;
	        }*/
	        $x++;
		}
		unset($index,$horaok);
	}
	unset($departamento,$info2);
}
unset($metrica,$info);


/*

foreach($departamentos as $index => $deps){

    $time=30;

    //Rename Deps for SQL
    switch($deps){
        case "SAC":
            $query_dep="Servicio a Cliente";
            $skill=4;
            break;
        case "TMP":
            $query_dep="Trafico MP";
            $skill=9;
            break;
        case "TMT":
            $query_dep="Trafico MT";
            $skill=8;
            break;
        case "Agencias":
            $query_dep="Soporte Agencias";
            $skill=7;
            break;
        case "Ventas":
            $query_dep="Ventas";
            $skill=3;
            $time=20;
            break;
        case "VentasMP":
            $query_dep="VentasMP";
            $skill=35;
            $time=20;
            break;
        default:
            $query_dep=$deps;
            $time=20;
            break;
    }

    //GET DATA VOL FROM SQL
    $query="SELECT * FROM `Historial Llamadas` WHERE Fecha=$date AND Skill='$query_dep'";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $numfields=mysql_num_fields($result);
    for($x=0;$x<$numfields;$x++){
        $data['volumen'][$deps][mysql_field_name($result,$x)]=intval(mysql_result($result,0,$x));
        $id=mysql_result($result,0,'id');
        $lu=mysql_result($result,0,'Last_Update');
    }

    //GET DATA SLA FROM SQL
    $query="SELECT * FROM `Historial Llamadas SLA` WHERE id=$id AND time=$time";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $numfields=mysql_num_fields($result);
    for($x=0;$x<$numfields;$x++){
        $data['sla'][$deps][mysql_field_name($result,$x)]=intval(mysql_result($result,0,$x));
    }

    //GET DATA AHT FROM SQL
    $query="SELECT * FROM `Historial Llamadas AHT` WHERE id=$id";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $numfields=mysql_num_fields($result);
    for($x=0;$x<$numfields;$x++){
        $data['aht'][$deps][mysql_field_name($result,$x)]=intval(mysql_result($result,0,$x));
    }

    //Forecast
    $query="SELECT
                    *
                    FROM
                        Fechas
                    LEFT JOIN
        	            (SELECT
                            *
                        FROM
                            `Historial Llamadas`
                        WHERE
                            Skill='$query_dep'
                        ) as Llamadas
                	ON
                	    WEEK(Fechas.Fecha- INTERVAL 365 day,1)=WEEK(Llamadas.Fecha- INTERVAL 365 day,1)-IF(Fechas.Fecha BETWEEN '2016-03-14' AND '2016-04-03',1,0)  AND
                	    WEEKDAY(Fechas.Fecha- INTERVAL 365 day)+1=WEEKDAY(Llamadas.Fecha- INTERVAL 365 day)+1
                	WHERE
                        Fechas.Fecha=$date AND
                        YEAR(Llamadas.Fecha)=YEAR(Fechas.Fecha)-1";


    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $numfields=mysql_num_fields($result);
    $numfields=mysql_num_fields($result);
    for($x=0;$x<$numfields;$x++){
        $data['fc'][$deps][mysql_field_name($result,$x)]=mysql_result($result,0,$x);
    }

    //PRINT DATA
    for($i=1;$i<=48;$i++){

        //Last Update
        $output['lu']=$lu;

        //Prevent NULL errors
        foreach($data as $kind => $info){
            if($info[$deps][$i]==NULL){
                $out[$kind]=0;
            }else{
                $out[$kind]=$info[$deps][$i];
                //FC
                if($kind=='fc'){
                    $out[$kind]=intval($info[$deps][$i]*$info[$deps]["forecast_".$skill]);
                    $forecast=$info[$deps]["forecast_".$skill];
                }
            }

        }

        $output['vol'][$deps][]=$out['volumen'];

        //SLA
        if($out['volumen']==0){
            $output['sla'][$deps][]=null;
        }else{
            $output['sla'][$deps][]=$out['sla']/$out['volumen']*100;
        }

        //AHT
        $output['aht'][$deps][]=$out['aht'];

        //Forecast
        $output['fc'][$deps][]=$out['fc'];

        //Precision de Pronostico
        if($out['fc']==0 ){
            $output['fc'][$deps][]=null;
        }else{
            $output['prec'][$deps][]=$out['volumen']/$out['fc']*100;
        }

        //Forecast
        //$output['forecast'][$deps]=$query;
    }

}
*/

print json_encode($output,JSON_PRETTY_PRINT);

/*echo "<pre>";
print_r($output);
echo "</pre>";*/


 ?>


<?
include("../DBCallsHoraV.php");
date_default_timezone_set('America/Mexico_City');
include("../connectDB.php");
include("../common/erlangC.php");
include("../common/scripts.php");


if($_GET['date']==NULL){$date=date('Y/m/d');}else{$date=date('Y/m/d',strtotime($_GET['date']));}
$skill=$_GET['s'];
$s=$_GET['skill'];
switch($s){
    case "Ventas":
		$aht=500;
        $tat=20;
        $slr=0.8;
		break;
	case "Servicio a Cliente":
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
	case "Trafico MP":
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
	case "Trafico MT":
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
	case "Soporte Agencias":
		$aht=0600;
        $tat=30;
        $slr=0.7;
		break;
    case "Corporativo":
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
}

$queryF="SELECT
            *
            FROM
                Fechas
            LEFT JOIN
	            (SELECT
                    *
                FROM
                    `Historial Llamadas`
                WHERE
                    Skill='$s'
                ) as Llamadas
        	ON
        	    WEEK(Fechas.Fecha- INTERVAL 365 day,1)=WEEK(Llamadas.Fecha- INTERVAL 365 day,1)-IF(Fechas.Fecha BETWEEN '2016-03-14' AND '2016-04-03',1,0)  AND
        	    WEEKDAY(Fechas.Fecha- INTERVAL 365 day)+1=WEEKDAY(Llamadas.Fecha- INTERVAL 365 day)+1
        	WHERE
                Fechas.Fecha='$date' AND
                YEAR(Llamadas.Fecha)=YEAR(Fechas.Fecha)-1";


$resultF=mysql_query($queryF);
//echo "$queryT<br>$queryF<br><br>";

$queryT="SELECT * FROM `Historial Llamadas` WHERE (Fecha='$date' AND Skill='$s')";
$resultT=mysql_query($queryT);
$id=mysql_result($resultT,0,"id");
$queryAHT="SELECT * FROM `Historial Llamadas AHT` WHERE id='$id'";
$resultAHT=mysql_query($queryAHT);

$i=1;
while($i<=48){
    $c_f[$i]=intval(mysql_result($resultF,0,$i+15)*mysql_result($resultF,0,'forecast_'.$skill));

$i++;
}


/*foreach($CVHora as $key => $hora){

	$verano=date('I', strtotime($date));
	
	if(intval($hora)!=$hora){$min="30"; $min2="40"; $hr=intval($hora); }else{$min="00"; $min2="10"; $hr=$hora;}
	
	if($verano==0){
		switch($key){
			case 0:
				$tmp=46;
				break;
			case 1:
				$tmp=47;
				break;
			default:
				$tmp=$key-2;
				break;
		}

		
	}
	if($key>=46){$x=1; $y=0;}else{$x=0; $y=1;}
	if($hr<10){$hr="0$hr";}
	$query="SELECT
            count(`Historial Programacion`.asesor)-count(tipo_ausentismo) as asesores
            FROM
                `Historial Programacion`
            LEFT JOIN
                Ausentismos
            ON
                `Historial Programacion`.asesor=Ausentismos.asesor AND
                `Historial Programacion`.Fecha BETWEEN Ausentismos.Inicio AND Ausentismos.Fin
            LEFT JOIN
                Asesores
            ON
                `Historial Programacion`.asesor=Asesores.id
            WHERE
    		Fecha=if('$hr:$min:00'>='04:00:00','$date','$date' - INTERVAL 1 DAY) AND
    		`id Departamento`='$skill' AND Activo=1 AND
    		(if('$hr:$min:00'>='04:00:00',SEC_TO_TIME(TIME_TO_SEC('$hr:$min:00')+60),SEC_TO_TIME(TIME_TO_SEC('$hr:$min:00')+86460))
    			BETWEEN
    				`jornada start`
    				AND if(`jornada end`>='04:00:00',`jornada end`,SEC_TO_TIME(TIME_TO_SEC(`jornada end`)+86400))
    		) AND NOT
        	(`jornada start`='00:00:00' AND
        	`jornada end`='00:00:00'
            )
            ";



	$result=mysql_query($query);
	$n_asesores[$tmp]=mysql_result($result,0,'asesores');
    //echo "$hora: ($key) // $n_asesores_ad[$key]<br>$query<br>$query_ad<br><br>";


	

}
*/

$query="SELECT
	asesor,`jornada start`,`jornada end`,`id Departamento`,
   COUNT(DISTINCT IF('00:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_0, COUNT(DISTINCT IF('00:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_1,
	COUNT(DISTINCT IF('01:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_2, COUNT(DISTINCT IF('01:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_3,
	COUNT(DISTINCT IF('02:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_4, COUNT(DISTINCT IF('02:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_5,
	COUNT(DISTINCT IF('03:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_5, COUNT(DISTINCT IF('03:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_7,
	COUNT(DISTINCT IF('04:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_6, COUNT(DISTINCT IF('04:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_9,
	COUNT(DISTINCT IF('05:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_10, COUNT(DISTINCT IF('05:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_11,
	COUNT(DISTINCT IF('06:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_12, COUNT(DISTINCT IF('06:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_13,
	COUNT(DISTINCT IF('07:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_14, COUNT(DISTINCT IF('07:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_15,
	COUNT(DISTINCT IF('08:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_16, COUNT(DISTINCT IF('08:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_17,
	COUNT(DISTINCT IF('09:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_18, COUNT(DISTINCT IF('09:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_19,
	COUNT(DISTINCT IF('10:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_20, COUNT(DISTINCT IF('10:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_21,
	COUNT(DISTINCT IF('11:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_22, COUNT(DISTINCT IF('11:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_23,
	COUNT(DISTINCT IF('12:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_24, COUNT(DISTINCT IF('12:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_25,
	COUNT(DISTINCT IF('13:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_26, COUNT(DISTINCT IF('13:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_27,
	COUNT(DISTINCT IF('14:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_28, COUNT(DISTINCT IF('14:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_29,
	COUNT(DISTINCT IF('15:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_30, COUNT(DISTINCT IF('15:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_31,
	COUNT(DISTINCT IF('16:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_32, COUNT(DISTINCT IF('16:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_33,
	COUNT(DISTINCT IF('17:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_34, COUNT(DISTINCT IF('17:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_35,
	COUNT(DISTINCT IF('18:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_36, COUNT(DISTINCT IF('18:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_37,
	COUNT(DISTINCT IF('19:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_38, COUNT(DISTINCT IF('19:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_39,
	COUNT(DISTINCT IF('20:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_40, COUNT(DISTINCT IF('20:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_41,
	COUNT(DISTINCT IF('21:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_42, COUNT(DISTINCT IF('21:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_43,
	COUNT(DISTINCT IF('22:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_44, COUNT(DISTINCT IF('22:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_45,
	COUNT(DISTINCT IF('23:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_46, COUNT(DISTINCT IF('23:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_47,
	COUNT(DISTINCT IF('00:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_0, COUNT(DISTINCT IF('00:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_1,
	COUNT(DISTINCT IF('01:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_2, COUNT(DISTINCT IF('01:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_3,
	COUNT(DISTINCT IF('02:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_4, COUNT(DISTINCT IF('02:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_5,
	COUNT(DISTINCT IF('03:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_5, COUNT(DISTINCT IF('03:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_7,
	COUNT(DISTINCT IF('04:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_6, COUNT(DISTINCT IF('04:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_9,
	COUNT(DISTINCT IF('05:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_10, COUNT(DISTINCT IF('05:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_11,
	COUNT(DISTINCT IF('06:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_12, COUNT(DISTINCT IF('06:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_13,
	COUNT(DISTINCT IF('07:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_14, COUNT(DISTINCT IF('07:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_15,
	COUNT(DISTINCT IF('08:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_16, COUNT(DISTINCT IF('08:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_17,
	COUNT(DISTINCT IF('09:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_18, COUNT(DISTINCT IF('09:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_19,
	COUNT(DISTINCT IF('10:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_20, COUNT(DISTINCT IF('10:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_21,
	COUNT(DISTINCT IF('11:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_22, COUNT(DISTINCT IF('11:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_23,
	COUNT(DISTINCT IF('12:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_24, COUNT(DISTINCT IF('12:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_25,
	COUNT(DISTINCT IF('13:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_26, COUNT(DISTINCT IF('13:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_27,
	COUNT(DISTINCT IF('14:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_28, COUNT(DISTINCT IF('14:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_29,
	COUNT(DISTINCT IF('15:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_30, COUNT(DISTINCT IF('15:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_31,
	COUNT(DISTINCT IF('16:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_32, COUNT(DISTINCT IF('16:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_33,
	COUNT(DISTINCT IF('17:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_34, COUNT(DISTINCT IF('17:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_35,
	COUNT(DISTINCT IF('18:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_36, COUNT(DISTINCT IF('18:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_37,
	COUNT(DISTINCT IF('19:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_38, COUNT(DISTINCT IF('19:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_39,
	COUNT(DISTINCT IF('20:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_40, COUNT(DISTINCT IF('20:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_41,
	COUNT(DISTINCT IF('21:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_42, COUNT(DISTINCT IF('21:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_43,
	COUNT(DISTINCT IF('22:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_44, COUNT(DISTINCT IF('22:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_45,
	COUNT(DISTINCT IF('23:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_46, COUNT(DISTINCT IF('23:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_47,
	COUNT(DISTINCT IF('00:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_0, COUNT(DISTINCT IF('00:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_1,
	COUNT(DISTINCT IF('01:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_2, COUNT(DISTINCT IF('01:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_3,
	COUNT(DISTINCT IF('02:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_4, COUNT(DISTINCT IF('02:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_5,
	COUNT(DISTINCT IF('03:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_5, COUNT(DISTINCT IF('03:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_7,
	COUNT(DISTINCT IF('04:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_6, COUNT(DISTINCT IF('04:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_9,
	COUNT(DISTINCT IF('05:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_10, COUNT(DISTINCT IF('05:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_11,
	COUNT(DISTINCT IF('06:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_12, COUNT(DISTINCT IF('06:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_13,
	COUNT(DISTINCT IF('07:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_14, COUNT(DISTINCT IF('07:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_15,
	COUNT(DISTINCT IF('08:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_16, COUNT(DISTINCT IF('08:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_17,
	COUNT(DISTINCT IF('09:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_18, COUNT(DISTINCT IF('09:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_19,
	COUNT(DISTINCT IF('10:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_20, COUNT(DISTINCT IF('10:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_21,
	COUNT(DISTINCT IF('11:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_22, COUNT(DISTINCT IF('11:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_23,
	COUNT(DISTINCT IF('12:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_24, COUNT(DISTINCT IF('12:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_25,
	COUNT(DISTINCT IF('13:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_26, COUNT(DISTINCT IF('13:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_27,
	COUNT(DISTINCT IF('14:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_28, COUNT(DISTINCT IF('14:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_29,
	COUNT(DISTINCT IF('15:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_30, COUNT(DISTINCT IF('15:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_31,
	COUNT(DISTINCT IF('16:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_32, COUNT(DISTINCT IF('16:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_33,
	COUNT(DISTINCT IF('17:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_34, COUNT(DISTINCT IF('17:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_35,
	COUNT(DISTINCT IF('18:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_36, COUNT(DISTINCT IF('18:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_37,
	COUNT(DISTINCT IF('19:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_38, COUNT(DISTINCT IF('19:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_39,
	COUNT(DISTINCT IF('20:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_40, COUNT(DISTINCT IF('20:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_41,
	COUNT(DISTINCT IF('21:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_42, COUNT(DISTINCT IF('21:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_43,
	COUNT(DISTINCT IF('22:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_44, COUNT(DISTINCT IF('22:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_45,
	COUNT(DISTINCT IF('23:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_46, COUNT(DISTINCT IF('23:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_47
	FROM
		`Historial Programacion` a
	LEFT JOIN
		Asesores b
	ON
		a.asesor=b.id
    LEFT JOIN
        (
            SELECT
                asesor as Aus_asesor, tipo_ausentismo
            FROM
                Ausentismos
            WHERE
                '$date' BETWEEN Inicio AND Fin
        ) c
    ON
        a.asesor=c.Aus_asesor
	WHERE
		Fecha='$date' AND
		`id Departamento`=$skill AND
        `jornada start`!= `jornada end`  AND
        Activo=1 AND
        tipo_ausentismo IS NULL
";
$result=mysql_query($query);
$i=0;
while($i<48){
    $n_asesores[$i]=mysql_result($result,0,'Hora_'.$i)+mysql_result($result,0,'Extra_'.$i)+mysql_result($result,0,'Extra2_'.$i);
$i++;
}

//echo "$query<br>";

if(date('Y/m/d')==date('Y/m/d',strtotime($date)) && intval($hr)>=4){$ad_query="Sesiones";}else{$ad_query="t_Sesiones";}
        $query_ad="SELECT
	asesor,Hora,Hora_out,Skill,
	COUNT(DISTINCT IF('00:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_0, COUNT(DISTINCT IF('00:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_1,
	COUNT(DISTINCT IF('01:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_2, COUNT(DISTINCT IF('01:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_3,
	COUNT(DISTINCT IF('02:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_4, COUNT(DISTINCT IF('02:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_5,
	COUNT(DISTINCT IF('03:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_5, COUNT(DISTINCT IF('03:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_7,
	COUNT(DISTINCT IF('04:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_6, COUNT(DISTINCT IF('04:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_9,
	COUNT(DISTINCT IF('05:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_10, COUNT(DISTINCT IF('05:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_11,
	COUNT(DISTINCT IF('06:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_12, COUNT(DISTINCT IF('06:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_13,
	COUNT(DISTINCT IF('07:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_14, COUNT(DISTINCT IF('07:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_15,
	COUNT(DISTINCT IF('08:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_16, COUNT(DISTINCT IF('08:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_17,
	COUNT(DISTINCT IF('09:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_18, COUNT(DISTINCT IF('09:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_19,
	COUNT(DISTINCT IF('10:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_20, COUNT(DISTINCT IF('10:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_21,
	COUNT(DISTINCT IF('11:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_22, COUNT(DISTINCT IF('11:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_23,
	COUNT(DISTINCT IF('12:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_24, COUNT(DISTINCT IF('12:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_25,
	COUNT(DISTINCT IF('13:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_26, COUNT(DISTINCT IF('13:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_27,
	COUNT(DISTINCT IF('14:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_28, COUNT(DISTINCT IF('14:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_29,
	COUNT(DISTINCT IF('15:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_30, COUNT(DISTINCT IF('15:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_31,
	COUNT(DISTINCT IF('16:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_32, COUNT(DISTINCT IF('16:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_33,
	COUNT(DISTINCT IF('17:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_34, COUNT(DISTINCT IF('17:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_35,
	COUNT(DISTINCT IF('18:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_36, COUNT(DISTINCT IF('18:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_37,
	COUNT(DISTINCT IF('19:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_38, COUNT(DISTINCT IF('19:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_39,
	COUNT(DISTINCT IF('20:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_40, COUNT(DISTINCT IF('20:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_41,
	COUNT(DISTINCT IF('21:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_42, COUNT(DISTINCT IF('21:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_43,
	COUNT(DISTINCT IF('22:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_44, COUNT(DISTINCT IF('22:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_45,
	COUNT(DISTINCT IF('23:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_46, COUNT(DISTINCT IF('23:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_47
	FROM
		Sesiones a
	LEFT JOIN
		Asesores b
	ON
		a.asesor=b.id
	WHERE
		Fecha='$date' AND
		`id Departamento`=$skill AND
		Skill=$skill";
$result_adh=mysql_query($query_ad);
$i=0;
while($i<48){
    $asesores_adh[$i]=mysql_result($result_adh,0,'Hora_'.$i);
$i++;
}

//print_r($asesores_adh);
//echo $query_ad;



?>
<script>
$(function(){
    $('#data').tablesorter({
        theme: 'blue',
        headerTemplate: '{content}',
        widthFixed: false,
        widgets: [ 'uitheme','zebra', 'stickyHeaders'],
        widgetOptions: {
           uitheme: 'jui',
            columns: [
                "primary",
                "secondary",
                "tertiary"
                ],
            columns_tfoot: false,
            columns_thead: true,
            filter_childRows: false,
            filter_columnFilters: true,
            filter_cssFilter: "tablesorter-filter",
            filter_functions: null,
            filter_hideFilters: false,
            filter_ignoreCase: true,
            filter_reset: null,
            filter_searchDelay: 300,
            filter_startsWith: false,
            filter_useParsedData: false,
            resizable: true,
            saveSort: true,
            stickyHeaders_attachTo : '#config-contain'


        }
    });
});
</script>
<table id='data' width='100%' style='text-align:center;'>
<thead>
    <tr>
        <th>Hora</th>
        <th>Programados</th>
        <th>Necesarios</th>
        <th>Calidad</th>
    </tr>
</thead>
<tbody>
<?php
//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Hora","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Programados","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Reales","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Adherencia","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Necesarios","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Nec. Live","pattern"=>"","type"=>"number");

        

	$i=1;
       while ($i<=48){
       	if($n_asesores[$i-1]==0){$adh=NULL;}else{$adh=$asesores_adh[$i-1]/$n_asesores[$i+1];}
        //$ahtlive=intval(agentno(intval($c_f[$i])/1800*$AHTc[$id][$i],$tat,$AHTc[$id][$i],$slr));
        $needed=intval(agentno(intval($c_f[$i])/1800*$aht,$tat,$aht,$slr));
          $aht_live=intval(agentno(intval(mysql_result($resultT,0,$i+5))/1800*mysql_result($resultAHT,0,$i+2),$tat,mysql_result($resultAHT,0,$i+2),$slr));
          $rows[] = array("c"=>array(array("v"=>$CVHora[$i-1],"f"=>null),array("v"=>$n_asesores[$i+1],"f"=>null),array("v"=>$asesores_adh[$i-1],"f"=>null),array("v"=>$adh,"f"=>null),array("v"=>$needed,"f"=>null),array("v"=>$aht_live,"f"=>null)));
          $q_prog=number_format(100-$needed/$n_asesores[$i+1]*100,2);
          echo "<tr>\n\t<td>".$CVHora[$i-1]."</td>\n\t<td>$needed</td>\n\t<td>".$n_asesores[$i+1]."</td>\n\t<td>$q_prog%</td>\n\t</tr>";
          $i++;
       }
       $a = array("cols"=>$cols,"rows"=>$rows);







//echo  json_encode($a);

//print_r($a);





?>
</tbody>
</table>
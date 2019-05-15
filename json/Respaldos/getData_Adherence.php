<?
include("../DBCallsHoraV.php");
date_default_timezone_set('America/Mexico_City');
include("../connectDB.php");
include("../common/erlangC.php");


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

//Forecast
function forecast(){
    global $skill, $s, $date, $forecast;
    $query_Forecast="SELECT
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


    $result_Forecast=mysql_query($query_Forecast);
    $i=0;
    while($i<48){
        $forecast[$i]=intval(mysql_result($result_Forecast,0,$i+16)*mysql_result($result_Forecast,0,'forecast_'.$skill));
    $i++;
    }
}


//Today
function today(){
    global $skill, $s, $date, $today;
    $query_Today="SELECT * FROM `Historial Llamadas` WHERE (Fecha='$date' AND Skill='$s')";
    $result_Today=mysql_query($query_Today);
    $i=0;
    while($i<48){
        $today[$i]=mysql_result($result_Today,0,$i+6);
    $i++;
    }
}

//SLA
function sla(){
    global $skill, $s, $date, $sla, $tat;
    $query_idHist="SELECT * FROM `Historial Llamadas` WHERE (Fecha='$date' AND Skill='$s')";
    $result_idHist=mysql_query($query_idHist);
    $id=mysql_result($result_idHist,0,"id");
    $query_SLA="SELECT * FROM `Historial Llamadas SLA` WHERE id=$id AND time=$tat";
    $result_SLA=mysql_query($query_SLA);
    $i=0;
    while($i<48){
        $sla[$i]=mysql_result($result_SLA,0,$i+2);
    $i++;
    }
}

//AHT
function aht(){
    global $skill, $s, $date, $aht_info;
    $query_idHist="SELECT * FROM `Historial Llamadas` WHERE (Fecha='$date' AND Skill='$s')";
    $result_idHist=mysql_query($query_idHist);
    $id=mysql_result($result_idHist,0,"id");
    $query_AHT="SELECT * FROM `Historial Llamadas AHT` WHERE id=$id";
    $result_AHT=mysql_query($query_AHT);
    $i=0;
    while($i<48){
        $aht_info[$i]=intval(mysql_result($result_AHT,0,$i+2));
    $i++;
    }
}




//Programacion
function programacion(){
    global $skill, $s, $date, $programacion;
    $query_Programacion="SELECT
    	asesor,`jornada start`,`jornada end`,`id Departamento`,
       COUNT(DISTINCT IF('00:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_0, COUNT(DISTINCT IF('00:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_1,
    	COUNT(DISTINCT IF('01:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_2, COUNT(DISTINCT IF('01:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_3,
    	COUNT(DISTINCT IF('02:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_4, COUNT(DISTINCT IF('02:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_5,
    	COUNT(DISTINCT IF('03:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_6, COUNT(DISTINCT IF('03:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_7,
    	COUNT(DISTINCT IF('04:09:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_8, COUNT(DISTINCT IF('04:39:00' BETWEEN `jornada start` AND IF(`jornada end`<'04:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`),asesor,NULL)) as Hora_9,
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
    	COUNT(DISTINCT IF('03:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_6, COUNT(DISTINCT IF('03:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_7,
    	COUNT(DISTINCT IF('04:09:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_8, COUNT(DISTINCT IF('04:39:00' BETWEEN `extra1 start` AND IF(`extra1 end`<'04:00:00',ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`),asesor,NULL)) as Extra_9,
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
    	COUNT(DISTINCT IF('03:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_6, COUNT(DISTINCT IF('03:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_7,
    	COUNT(DISTINCT IF('04:09:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_8, COUNT(DISTINCT IF('04:39:00' BETWEEN `extra2 start` AND IF(`extra2 end`<'04:00:00',ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`),asesor,NULL)) as Extra2_9,
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

    $result_Programacion=mysql_query($query_Programacion);
    $i=0;
    while($i<48){
        $index=$i;
        $programacion[$i]=mysql_result($result_Programacion,0,'Hora_'.$index)+mysql_result($result_Programacion,0,'Extra_'.$index)+mysql_result($result_Programacion,0,'Extra2_'.$index);
    $i++;
    }
}

//Real
function real(){
    global $skill, $s, $date, $real;
    if(date('Y/m/d')==date('Y/m/d',strtotime($date)) && intval($hr)>=4){$ad_query="Sesiones";}else{$ad_query="t_Sesiones";}
    $query_Real="SELECT
    	asesor,Hora,Hora_out,Skill,
    	COUNT(DISTINCT IF('00:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_0, COUNT(DISTINCT IF('00:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_1,
    	COUNT(DISTINCT IF('01:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_2, COUNT(DISTINCT IF('01:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_3,
    	COUNT(DISTINCT IF('02:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_4, COUNT(DISTINCT IF('02:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_5,
    	COUNT(DISTINCT IF('03:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_6, COUNT(DISTINCT IF('03:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_7,
    	COUNT(DISTINCT IF('04:09:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_8, COUNT(DISTINCT IF('04:39:00' BETWEEN Hora AND Hora_out,asesor,NULL)) as Hora_9,
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
    $result_Real=mysql_query($query_Real);
    $i=0;
    while($i<48){
        $real[$i]=intval(mysql_result($result_Real,0,'Hora_'.$i));
    $i++;
    }
}


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

        if(date('I',strtotime($date))==0){$x=-3;}else{$x=-1;}

            programacion();
            real();
            forecast();
            today();

            if($programacion[$i+$x]==0 || $programacion[$i+$x]==NULL){$adh=NULL;}else{$adh=$real[$i-1]/$programacion[$i+$x];}
            $needed=intval(agentno(intval($forecast[$i])/1800*$aht,$tat,$aht,$slr));
            $aht_live=0;

            $rows[] = array("c"=>array(array("v"=>$CVHora[$i],"f"=>null),array("v"=>$programacion[$i+$x],"f"=>null),array("v"=>$real[$i-1],"f"=>null),array("v"=>$adh,"f"=>null),array("v"=>$needed,"f"=>null),array("v"=>$aht_live,"f"=>null)));

       $i++;
       }

    $a = array("cols"=>$cols,"rows"=>$rows);
      
echo  json_encode($a);







?>
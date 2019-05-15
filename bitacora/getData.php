<?php

include_once('../modules/modules.php');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$skill=$_POST['skill'];
$fecha="'".$_POST['fecha']."'";

if(isset($_GET['skill'])){
    $skill=$_GET['skill'];
    $fecha="'".$_GET['fecha']."'";
}

$query="SELECT * FROM metas_kpi WHERE anio=".date('Y',strtotime($_POST['fecha']))." AND mes=".date('m',strtotime($_POST['fecha']))." AND skill=$skill";
if($result=$connectdb->query($query)){
    $data['status']=1;
    while($fila=$result->fetch_assoc()){
        switch($fila['tipo']){
            case 'abandon':
                $data['metas'][$fila['tipo']]=number_format($fila['meta']*100,2);
                break;
            case 'sla':
                $data['metas'][$fila['tipo']]=number_format($fila['meta']*100,2);
                break;
            case 'aht':
                $data['metas'][$fila['tipo']]=number_format($fila['meta'],2);
                break;
        }
        
    }
}

$query="SELECT 
            Hora_int, COUNT(*) AS Programados
        FROM
            HoraGroup_Table h
                LEFT JOIN
            `Historial Programacion` a ON (a.`jornada start` <= Hora_time
                AND a.`jornada end` >= ADDTIME(Hora_time, '00:29:59'))
                OR (a.`extra1 start` <= Hora_time
                AND a.`extra1 end` >= ADDTIME(Hora_time, '00:29:59'))
                OR (a.`extra2 start` <= Hora_time
                AND a.`extra2 end` >= ADDTIME(Hora_time, '00:29:59'))
                LEFT JOIN
            Ausentismos b ON a.asesor = b.asesor
                AND a.Fecha BETWEEN Inicio AND Fin
                LEFT JOIN
            `Tipos Ausentismos` c ON b.tipo_ausentismo = c.id
                LEFT JOIN
            dep_asesores d ON a.Fecha = d.Fecha
                AND a.asesor = d.asesor
                LEFT JOIN
            Asesores e ON a.asesor = e.id
        WHERE
            a.Fecha = $fecha
                AND (adherencia = 0 OR adherencia IS NULL)
                AND dep = $skill
                AND d.puesto = 1
                AND Egreso > a.Fecha
        GROUP BY
            Hora_int";
if($result=$connectdb->query($query)){
    $data['status']=1;
    while($fila=$result->fetch_assoc()){
        $data['programados'][$fila['Hora_int']]=$fila['Programados'];
    }   
}

$query="SELECT 
            Hora_int, COUNT(asesor) AS Sentados
        FROM
            HoraGroup_Table h
                LEFT JOIN
            (SELECT 
                a.asesor, MIN(Hora) AS entrada, MAX(Hora_out) AS salida
            FROM
                Sesiones a
            LEFT JOIN dep_asesores b ON a.asesor = b.asesor
                AND a.Fecha = b.Fecha
            WHERE
                a.Fecha = $fecha AND dep = $skill
                    AND puesto = 1
            GROUP BY a.asesor) a 
            ON a.entrada <= Hora_time
                AND a.salida >= ADDTIME(Hora_time, '00:29:59')
        GROUP BY Hora_int

";
if($result=$connectdb->query($query)){
    $data['status']=1;
    while($fila=$result->fetch_assoc()){
        $data['sentados'][$fila['Hora_int']]=$fila['Sentados'];
    }   
}

$query="SELECT 
    hora, ROUND(participacion * volumen) AS forecast
FROM
    (SELECT 
        *
    FROM
        forecast_participacion
    WHERE
        Fecha = $fecha AND skill = $skill) a
        LEFT JOIN
    forecast_volume b ON a.Fecha = b.Fecha AND a.skill = b.skill";
if($result=$connectdb->query($query)){
    $data['status']=1;
    while($fila=$result->fetch_assoc()){
        $data['forecast'][$fila['hora']]['calls']=$fila['forecast'];
    }
}

//echo date('Y-m-d',strtotime($fecha))." || ".date('Y-m-d')."<br>";

if(date('Y-m-d',strtotime($_POST['fecha']))==date('Y-m-d')){
    $query="SELECT 
                Hora_int,
                Skill,
                COUNT(*) AS llamadas,
                COUNT(IF(Desconexion='Abandon',a.id,NULL))/COUNT(*)*100 as Abandon,
                AVG(Duracion) AS AHT,
                COUNT(IF(Desconexion!='Abandon' AND Wait <= (IF(Skill IN (3 , 35), 20, 30)),
                    a.id,
                    NULL)) / COUNT(*)*100 AS SLA
            FROM
                ccexporter.mon_calls_details a
                    LEFT JOIN
                comeycom_WFM.Cola_Skill b ON a.Cola = b.Cola
                    LEFT JOIN
                comeycom_WFM.HoraGroup_Table c ON a.Hora BETWEEN c.Hora_time AND ADDTIME(Hora_time, '00:29:59')
            WHERE
                Fecha = CURDATE()
            GROUP BY
                Hora_time, Skill
            HAVING 
                Skill=$skill";
    

}else{
    $query="SELECT 
                Hora_int,
                Skill,
                COUNT(*) AS llamadas,
                COUNT(IF(Answered=0,a.ac_id,NULL))/COUNT(*)*100 as Abandon,
                AVG(TIME_TO_SEC(Duracion_Real)) AS AHT,
                COUNT(IF(Answered!=0 AND TIME_TO_SEC(Espera) <= (IF(Skill IN (3 , 35), 20, 30)),
                    a.ac_id,
                    NULL)) / COUNT(*)*100 AS SLA
            FROM
                t_Answered_Calls a
                    LEFT JOIN
                comeycom_WFM.Cola_Skill b ON a.Cola = b.Cola
                    LEFT JOIN
                comeycom_WFM.HoraGroup_Table c ON a.Hora BETWEEN c.Hora_time AND ADDTIME(Hora_time, '00:29:59')
            WHERE
                Fecha = $fecha
            GROUP BY
                Hora_time, Skill
            HAVING 
                Skill=$skill";
}
if($result=$connectdb->query($query)){
    
    $data['status']=1;
    while($fila=$result->fetch_assoc()){
        $data['info'][$fila['Hora_int']]['llamadas']=$fila['llamadas'];
        $data['info'][$fila['Hora_int']]['abandon']=number_format($fila['Abandon'],2);
        $data['info'][$fila['Hora_int']]['aht']=number_format($fila['AHT'],0);
        $data['info'][$fila['Hora_int']]['sla']=number_format($fila['SLA'],2);
        
        if($fila['AHT']>$data['metas']['aht']*1.25){
            $data['class'][$fila['Hora_int']]['aht']='danger';
        }else{
            $data['class'][$fila['Hora_int']]['aht']='';
        }
        
        if($fila['SLA']<$data['metas']['sla']*.85){
            $data['class'][$fila['Hora_int']]['sla']='danger';
        }elseif($fila['SLA']>$data['metas']['sla']*1.1){
            $data['class'][$fila['Hora_int']]['sla']='success';
        }else{
            $data['class'][$fila['Hora_int']]['sla']='';
        }
        
        if($fila['Abandon']>$data['metas']['abandon']*1.15){
            $data['class'][$fila['Hora_int']]['abandon']='danger';
        }else{
            $data['class'][$fila['Hora_int']]['abandon']='';
        }
        
        @$data['forecast'][$fila['Hora_int']]['prec']=number_format($fila['llamadas']/$data['forecast'][$fila['Hora_int']]['calls']*100,2);
        
        if($data['forecast'][$fila['Hora_int']]['prec']<85){
            $data['class'][$fila['Hora_int']]['prec']='warning';
        }elseif($data['forecast'][$fila['Hora_int']]['prec']>115){
            $data['class'][$fila['Hora_int']]['prec']='danger';
        }else{
            $data['class'][$fila['Hora_int']]['prec']='';
        }
    }
}

$data['acciones'][][]="";

$query="SELECT * FROM
    bitacora_base WHERE skill=$skill AND Fecha=$fecha";
if($result=$connectdb->query($query)){
    while($fila=$result->fetch_assoc()){
        $data['acciones'][$fila['intervalo']][$fila['level']]=utf8_encode($fila['comments']);
        
    }
}

echo json_encode($data);

$connectdb->close();
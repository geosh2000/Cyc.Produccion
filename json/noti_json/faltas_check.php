<?php
include("../connectDB.php");  
$query="SELECT a.asesor, `N Corto`, a.Fecha, `jornada start` as jornada_start, ADDTIME(Hora,'01:00:00') as inicio, e.Ausentismo, IF(Hora IS NULL AND Ausentismo IS NULL,SEC_TO_TIME(TIME_TO_SEC('".date('H:i:s')."')-TIME_TO_SEC(`jornada start`)),NULL) as offtime
	FROM `Historial Programacion` a
	LEFT JOIN
	Sesiones b
	ON
	a.asesor=b.asesor AND
	a.Fecha=b.Fecha
	LEFT JOIN
	Ausentismos c
	ON
	a.Fecha BETWEEN c.Inicio AND c.Fin AND
	a.asesor=c.asesor
	LEFT JOIN
	`Tipos Ausentismos` e
	ON
	c.tipo_ausentismo=e.id
	LEFT JOIN
	Asesores d
	ON
	a.asesor=d.id

	WHERE a.Fecha='".date('Y-m-d')."'
	AND `jornada start`!='00:00:00'
	AND `id Departamento`!=1
	AND Activo=1
	HAVING offtime>'00:30:00' AND `N Corto`IS NOT NULL";

$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $asesor[$i]=mysql_result($result,$i,'asesor');
    $nombre[$i]=mysql_result($result,$i,'N Corto');
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    $Duracion[$i]=mysql_result($result,$i,'offtime');
$i++;
}

if($num!=0){
    foreach($asesor as $key => $info){
        $query="SELECT * FROM pausas_excedidas WHERE asesor='$info' AND Fecha='$fecha[$key]' AND categoria='1'";
        $result=mysql_query($query);
        if(mysql_numrows($result)==0){
        	$query="INSERT INTO pausas_excedidas (asesor,fecha,categoria) VALUES ('$info','$fecha[$key]','1')";
        	mysql_query($query);
            $query="SELECT userid FROM userDB WHERE asesor_id='$info'";
            $user=mysql_result(mysql_query($query),0,'userid');
            $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND message='Hola $nombre[$key]. El sistema ha registrado que tienes mas de 30 minutos de retraso por lo que se ha considerado como FALTA. Por favor acercate con GTR para validarlo.'";
                        $result=mysql_query($query);
                        if(mysql_numrows($result)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,dialog, dialog_message,valid_thru_date) VALUES ('info','$user','FALTA','Hola $nombre[$key]. El sistema ha registrado que tienes mas de 30 minutos de retraso por lo que se ha considerado como FALTA. Por favor acercate con GTR para validarlo.','0','1','1','Hola $nombre[$key]. El sistema ha registrado que tienes mas de 30 minutos de retraso por lo que se ha considerado como FALTA. Por favor acercate con GTR para validarlo.','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
        	    			mysql_query($query);
                        }

        	$query="SELECT userid FROM Asesores a, userDB b WHERE a.Usuario=b.username AND a.`id Departamento`=12";
        	$result=mysql_query($query);
        	$num=mysql_numrows($result);
        	$i=0;
        	while($i<=$num){
        		if($i==$num){
        			$x=2;
    		    	while($x<=5){
    		    		switch($x){
    		    			case 5:
    		    				$user=11;
    		    				break;
    		    			default:
    		    				$user=$x;
    		    				break;
        				}
                        $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] tiene mas de 30 min de retraso por lo que se considera FALTA. Se ha enviado un mensaje a su sesion para que en caso de loguarse, se dirija con GTR para su validacion.'";
                        $result2=mysql_query($query);
                        if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date) VALUES ('info','$user','FALTA','El asesor $nombre[$key] tiene mas de 30 min de retraso por lo que se considera FALTA. Se ha enviado un mensaje a su sesion para que en caso de loguarse, se dirija con GTR para su validacion.','0','1','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
        	    			mysql_query($query);
                        }
        			$x++;
        			}


        		}else{
        			$user=mysql_result($result,$i,'userid');
        			$query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] tiene mas de 30 min de retraso por lo que se considera FALTA. Se ha enviado un mensaje a su sesion para que en caso de loguarse, se dirija con GTR para su validacion.'";
                    $result2=mysql_query($query);
                    if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date) VALUES ('info','$user','FALTA','El asesor $nombre[$key] tiene mas de 30 min de retraso por lo que se considera FALTA. Se ha enviado un mensaje a su sesion para que en caso de loguarse, se dirija con GTR para su validacion.','0','1','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
    	    			    mysql_query($query);
                    }
        		}


        	$i++;
        	}

        }
    }
}

unset($user, $query, $result, $num, $key, $info, $i, $x, $Duracion, $nombre, $asesor, $fecha);


?>
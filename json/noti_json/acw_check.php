<?php
include("../connectDB.php");
date_default_timezone_set('America/Bogota');
$query="SELECT ComidaId, a.asesor, a.Fecha, `N Corto`, Pausa, Inicio, notificada, SEC_TO_TIME(TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio)) as Duracion
	FROM Comidas a
	LEFT JOIN Tipos_pausas c
	ON a.tipo=c.pausa_id
	LEFT JOIN Asesores d
	ON  a.asesor=d.id
	WHERE
		a.Fecha='".date('Y-m-d')."' AND
		a.tipo=10 AND
		a.notificada IS NULL
	HAVING
	Duracion>'00:02:00'";

$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $asesor[$i]=mysql_result($result,$i,'asesor');
    $nombre[$i]=mysql_result($result,$i,'N Corto');
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    $id[$i]=mysql_result($result,$i,'ComidaId');
    $pausa[$i]=mysql_result($result,$i,'Pausa');
    $inicio[$i]=mysql_result($result,$i,'Duracion');
$i++;
}

if($num!=0){
    foreach($asesor as $key => $info){
        $query="SELECT userid FROM userDB WHERE asesor_id='$info'";
            $user=mysql_result(mysql_query($query),0,'userid');
            $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND message='Hola $nombre[$key]. El tiempo de pausa para ACW ha excedido los 2 minutos. Si tienes problemas por favor acercate con tu supervisor.' AND pause_id='$id[$key]'";
                        $result=mysql_query($query);
                        if(mysql_numrows($result)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,dialog, dialog_message,valid_thru_date,pause_id) VALUES ('info','$user','ACW EXCEDIDO','Hola $nombre[$key]. El tiempo de pausa para ACW ha excedido los 2 minutos. Si tienes problemas por favor acercate con tu supervisor.','0','6','1','Hola $nombre[$key]. El tiempo de pausa para ACW ha excedido los 2 minutos. Si tienes problemas por favor acercate con tu supervisor.','".date('Y-m-d H:i:s',strtotime('+1 hour'))."','$id[$key]')";
        	    			mysql_query($query);
                            $query="UPDATE Comidas SET notificada=1 WHERE ComidaId='$id[$key]'";
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
                        $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] ha excedido los 2 minutos de ACW. Favor de Gestionar.' AND pause_id='$id[$key]'";
                        $result2=mysql_query($query);
                        if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date,pause_id) VALUES ('info','$user','ACW EXCEDIDO','El asesor $nombre[$key] ha excedido los 2 minutos de ACW. Favor de Gestionar.','0','6','".date('Y-m-d H:i:s',strtotime('+1 hour'))."','$id[$key]')";
        	    			mysql_query($query);
                        }
        			$x++;
        			}


        		}else{
        			$user=mysql_result($result,$i,'userid');
        			$query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] ha excedido los 2 minutos de ACW. Favor de Gestionar.' AND pause_id='$id[$key]'";
                    $result2=mysql_query($query);
                    if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date,pause_id) VALUES ('info','$user','ACW EXCEDIDO','El asesor $nombre[$key] ha excedido los 2 minutos de ACW. Favor de Gestionar.','0','6','".date('Y-m-d H:i:s',strtotime('+1 hour'))."','$id[$key]')";
    	    			    mysql_query($query);
                    }
        		}


        	$i++;
        	}


    }

}

unset($user, $query, $result, $num, $key, $info, $i, $x, $Duracion, $nombre, $asesor, $fecha);


?>
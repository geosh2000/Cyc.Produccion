<?php
include("../connectDB.php");
$query="SELECT ComidaId, a.asesor, a.Fecha, `N Corto`, Pausa, Inicio, notificada
	FROM Comidas a
	LEFT JOIN Tipos_pausas c
	ON a.tipo=c.pausa_id
	LEFT JOIN pausas_excedidas b
	ON a.asesor=b.asesor AND a.Fecha=b.fecha
	LEFT JOIN Asesores d
	ON  a.asesor=d.id
	WHERE
		a.Fecha='".date('Y-m-d')."' AND
		categoria=4 AND
		b.Last_Update<a.`Last Update` AND
		a.tipo=11 AND
		a.notificada IS NULL";

$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $asesor[$i]=mysql_result($result,$i,'asesor');
    $nombre[$i]=mysql_result($result,$i,'N Corto');
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    $id[$i]=mysql_result($result,$i,'ComidaId');
    $pausa[$i]=mysql_result($result,$i,'Pausa');
    $inicio[$i]=mysql_result($result,$i,'Inicio');
$i++;
}

if($num!=0){
    foreach($asesor as $key => $info){
        $query="SELECT userid FROM userDB WHERE asesor_id='$info'";
            $user=mysql_result(mysql_query($query),0,'userid');
            $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND message='Hola $nombre[$key]. Recuerda que tu tiempo de pausa se ha agotado. Si necesitas otra pausa, debes solicitarla a GTR.' AND pause_id='$id[$key]'";
                        $result=mysql_query($query);
                        if(mysql_numrows($result)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,dialog, dialog_message,valid_thru_date,pause_id) VALUES ('info','$user','TIEMPO PNP AGOTADO','Hola $nombre[$key]. Recuerda que tu tiempo de pausa se ha agotado. Si necesitas otra pausa, debes solicitarla a GTR.','0','5','1','Hola $nombre[$key]. Recuerda que tu tiempo de pausa se ha agotado. Si necesitas otra pausa, debes solicitarla a GTR.','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."','$id[$key]')";
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
                        $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] ha seleccionado $pausa[$key] y ya no cuenta con tiempo disponible. Favor de Gestionar.' AND pause_id='$id[$key]'";
                        $result2=mysql_query($query);
                        if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date,pause_id) VALUES ('info','$user','TIEMPO PNP AGOTADO','El asesor $nombre[$key] ha seleccionado $pausa[$key] y ya no cuenta con tiempo disponible. Favor de Gestionar.','0','5','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."','$id[$key]')";
        	    			mysql_query($query);
                        }
        			$x++;
        			}


        		}else{
        			$user=mysql_result($result,$i,'userid');
        			$query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] ha seleccionado $pausa[$key] y ya no cuenta con tiempo disponible. Favor de Gestionar.' AND pause_id='$id[$key]'";
                    $result2=mysql_query($query);
                    if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date,pause_id) VALUES ('info','$user','TIEMPO PNP AGOTADO','El asesor $nombre[$key] ha seleccionado $pausa[$key] y ya no cuenta con tiempo disponible. Favor de Gestionar.','0','5','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."','$id[$key]')";
    	    			    mysql_query($query);
                    }
        		}


        	$i++;
        	}


    }
}

unset($user, $query, $result, $num, $key, $info, $i, $x, $Duracion, $nombre, $asesor, $fecha);


?>
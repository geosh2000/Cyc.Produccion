<?php
include("../connectDB.php");       
$query="SELECT Fecha, asesor, `N Corto`, SEC_TO_TIME(TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio)) as Duracion, Comida
        FROM Comidas a, Asesores b, PNP_tiempos c
        WHERE a.asesor=b.id AND b.Esquema=c.Esquema AND
        Fecha='".date('Y-m-d')."' AND a.tipo=3
        HAVING Duracion>ADDTIME(Comida,'00:05:00')";

$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $asesor[$i]=mysql_result($result,$i,'asesor');
    $nombre[$i]=mysql_result($result,$i,'N Corto');
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    $Duracion[$i]=mysql_result($result,$i,'Duracion');
$i++;
}

foreach($asesor as $key => $info){
    $query="SELECT * FROM pausas_excedidas WHERE asesor='$info' AND Fecha='$fecha[$key]' AND categoria='3'";
    $result=mysql_query($query);
    if(mysql_numrows($result)==0){
    	$query="INSERT INTO pausas_excedidas (asesor,fecha,categoria) VALUES ('$info','$fecha[$key]','3')";
    	mysql_query($query);
        $query="SELECT userid FROM userDB WHERE asesor_id='$info'";
        $user=mysql_result(mysql_query($query),0,'userid');
        $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND message='Hola $nombre[$key]. Has excedido tu tiempo de comida, y se descontara de tu tiempo de pausas no productivas.'";
                    $result=mysql_query($query);
                    if(mysql_numrows($result)==0){
                        $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,dialog, dialog_message,valid_thru_date) VALUES ('info','$user','TIEMPO DE COMIDA EXCEDIDO','Hola $nombre[$key]. Has excedido tu tiempo de comida, y se descontara de tu tiempo de pausas no productivas.','0','3','1','Hola $nombre[$key]. Has excedido tu tiempo de comida, y se descontara de tu tiempo de pausas no productivas.','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
    	    			mysql_query($query);
                    }

    	$query="SELECT userid FROM Asesores a, userDB b WHERE a.Usuario=b.username AND a.`id Departamento`=12";
    	$result=mysql_query($query);
    	$num=mysql_numrows($result);
    	$i=0;
    	while($i<=$num){
    		if($i==$num){
    			$x=1;
		    	while($x<=5){
		    		switch($x){
		    			case 5:
		    				$user=11;
		    				break;
		    			default:
		    				$user=$x;
		    				break;
    				}
                    $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] ha excedido su tiempo de alimentos'";
                    $result2=mysql_query($query);
                    if(mysql_numrows($result2)==0){
                        $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date) VALUES ('info','$user','TIEMPO DE COMIDA EXCEDIDO','El asesor $nombre[$key] ha excedido su tiempo de alimentos','0','3','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
    	    			mysql_query($query);
                    }
    			$x++;
    			}


    		}else{
    			$user=mysql_result($result,$i,'userid');
    			$query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='El asesor $nombre[$key] ha excedido su tiempo de alimentos'";
                $result2=mysql_query($query);
                if(mysql_numrows($result2)==0){
                        $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date) VALUES ('info','$user','TIEMPO DE COMIDA EXCEDIDO','El asesor $nombre[$key] ha excedido su tiempo de alimentos','0','3','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
	    			    mysql_query($query);
                }
    		}


    	$i++;
    	}

    }
}

unset($user, $query, $result, $num, $key, $info, $i, $x, $Duracion, $nombre, $asesor, $fecha);


?>
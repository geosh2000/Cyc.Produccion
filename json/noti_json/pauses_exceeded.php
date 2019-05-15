<?php
include("../connectDB.php");  
$query="SELECT `N Corto`, asesor, Fecha, SEC_TO_TIME(SUM(TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio))) as Duracion, d.Tiempo
        FROM Comidas a, Asesores b, Tipos_pausas c, PNP_tiempos d
        WHERE a.asesor=b.id AND a.tipo=c.pausa_id AND b.Esquema=d.Esquema AND Fecha='".date('Y-m-d')."' AND Seleccionables=1 group by Fecha, asesor HAVING SUM(TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio))>TIME_TO_SEC(d.Tiempo)";

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
    $query="SELECT * FROM pausas_excedidas WHERE asesor='$info' AND Fecha='$fecha[$key]' AND categoria='4'";
    $result=mysql_query($query);
    if(mysql_numrows($result)==0){
    	$query="INSERT INTO pausas_excedidas (asesor,fecha,categoria) VALUES ('$info','$fecha[$key]','4')";
    	mysql_query($query);
        $query="SELECT userid FROM userDB WHERE asesor_id='$info'";
        $user=mysql_result(mysql_query($query),0,'userid');
        $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND message='Hola $nombre[$key]. Has llegado al limite de tiempo disponible para tus pausas del dia. Si necesitas otra pausa, por favor solicitala a GTR a traves del Grupo de Spark.'";
                    $result=mysql_query($query);
                    if(mysql_numrows($result)==0){
                        $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,dialog, dialog_message,valid_thru_date) VALUES ('info','$user','TIEMPO DE PAUSA EXCEDIDO','Hola $nombre[$key]. Has llegado al limite de tiempo disponible para tus pausas del dia. Si necesitas otra pausa, por favor solicitala a GTR a traves del Grupo de Spark.','0','4','1','Hola $nombre[$key]. Has llegado al limite de tiempo disponible para tus pausas del dia. Si necesitas otra pausa, por favor solicitala a GTR a traves del Grupo de Spark.','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
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
                    $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_send)='".date('m')."' AND DAY(date_send)='".date('d')."' AND YEAR(date_send)='".date('Y')."' AND message='El asesor $nombre[$key] ha excedido su tiempo maximo de pausas al dia ($Duracion[$key])'";
                    $result2=mysql_query($query);
                    $num2=mysql_numrows($result2);
                    if($num2==0){
                        $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date) VALUES ('info','$user','TIEMPO DE PAUSA EXCEDIDO','El asesor $nombre[$key] ha excedido su tiempo maximo de pausas al dia ($Duracion[$key])','0','4','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
    	    			mysql_query($query);
                    }
    			$x++;
    			}


    		}else{
    			$user=mysql_result($result,$i,'userid');
    			$query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND message='El asesor $nombre[$key] ha excedido su tiempo maximo de pausas al dia ($Duracion[$key])'";
                $result2=mysql_query($query);
                $num2=mysql_numrows($result2);
                    if($num2==0){
                        $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date) VALUES ('info','$user','TIEMPO DE PAUSA EXCEDIDO','El asesor $nombre[$key] ha excedido su tiempo maximo de pausas al dia ($Duracion[$key])','0','4','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."')";
	    			    mysql_query($query);
                }
    		}


    	$i++;
    	}

    }
}

unset($user, $query, $result, $num, $key, $info, $i, $x, $Duracion, $nombre, $asesor, $fecha);


?>
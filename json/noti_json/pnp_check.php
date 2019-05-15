<?php
include("../connectDB.php"); 
$categoria='7';
function mensajeAsesor($valor1=NULL, $valor2=NULL,$valor3=NULL){
    global $msgGTR, $msgAsesor,$titleAsesor,$titleGTR;
    $msgAsesor= "Hola $valor1. El tiempo de pausa para PNP ha excedido los 5 minutos. Recuerda que no debes exceder este limite de tiempo en tus pausas.";

}

function titleAsesor($valor1=NULL, $valor2=NULL,$valor3=NULL){
   global $msgGTR, $msgAsesor,$titleAsesor,$titleGTR;
     $titleAsesor= "TIEMPO DE PNP EXCEDIDO";

}

function mensajeGTR($valor1=NULL, $valor2=NULL,$valor3=NULL){
    global $msgGTR, $msgAsesor,$titleAsesor,$titleGTR;
    $msgGTR= "El asesor $valor1 ha excedido los 5 minutos permitidos para su PNP.";

}

function titleGTR($valor1=NULL, $valor2=NULL,$valor3=NULL){
    global $msgGTR, $msgAsesor,$titleAsesor,$titleGTR;
    $titleGTR= "TIEMPO DE PNP EXCEDIDO";

}

$query="SELECT ComidaId, a.asesor, a.Fecha, `N Corto`, Pausa, Inicio, notificada, SEC_TO_TIME(TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio)) as Duracion
	FROM Comidas a
	LEFT JOIN Tipos_pausas c
	ON a.tipo=c.pausa_id
	LEFT JOIN Asesores d
	ON  a.asesor=d.id
	WHERE
		a.Fecha='".date('Y-m-d')."' AND
		a.tipo=11 AND
		a.notificada IS NULL
	HAVING
	Duracion>'00:05:30'";

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
        mensajeAsesor($nombre[$key]);
        titleAsesor($nombre[$key]);
        mensajeGTR($nombre[$key]);
        titleGTR($nombre[$key]);
        $query="SELECT userid FROM userDB WHERE asesor_id='$info'";
            $user=mysql_result(mysql_query($query),0,'userid');
            $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND message='$msgAsesor' AND pause_id='$id[$key]'";
                        $result=mysql_query($query);
                        if(mysql_numrows($result)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,dialog, dialog_message,valid_thru_date,pause_id) VALUES ('info','$user','$titleAsesor','$msgAsesor','0','$categoria','1','$msgAsesor','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."','$id[$key]')";
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
                        $query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='$msgGTR' AND pause_id='$id[$key]'";
                        $result2=mysql_query($query);
                        if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date,pause_id) VALUES ('info','$user','$titleGTR','$msgGTR','0','$categoria','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."','$id[$key]')";
        	    			mysql_query($query);
                        }
        			$x++;
        			}


        		}else{
        			$user=mysql_result($result,$i,'userid');
        			$query="SELECT * FROM noti_mensajes WHERE `to`='$user' AND MONTH(date_sent)='".date('m')."' AND DAY(date_sent)='".date('d')."' AND YEAR(date_sent)='".date('Y')."' AND message='$msgGTR' AND pause_id='$id[$key]'";
                    $result2=mysql_query($query);
                    if(mysql_numrows($result2)==0){
                            $query="INSERT INTO noti_mensajes (type,`to`,title,message,recieved,categoria,valid_thru_date,pause_id) VALUES ('info','$user','$titleGTR','$msgGTR','0','$categoria','".date('Y-m-d H:i:s',strtotime('+ 30 minutes'))."','$id[$key]')";
    	    			    mysql_query($query);
                    }
        		}


        	$i++;
        	}


    }
}

unset($user, $query, $result, $num, $key, $info, $i, $x, $Duracion, $nombre, $asesor, $fecha);


?>
<?php

include_once("../modules/modules.php");

timeAndRegion::setRegion('Mex');

$connectdb=Connection::mysqliDB('CC');

$fecha=date('Y-m-d', strtotime($_POST['fecha']));
$tipo=$_POST['tipo'];

$skill=$_POST['skill'];
$accion=$_POST['accion'];
$accion_name=$_POST['accion_name'];
$user=$_POST['user'];
$comments=$_POST['comments'];
$level=$_POST['level'];
$indice=$_POST['indice'];

if(date('I',strtotime($fecha))==0){
	$hora=$_POST['hora']-2;
}else{
	$hora=$_POST['hora'];
}

//Error Handler

function divError(){
 echo "";
}
set_error_handler("divError");

switch($tipo){
	case 'get':
		$query="SELECT
					accion, comments, last_update, `N Corto` as username
				FROM
					bitacora_base a
				LEFT JOIN
					Asesores b ON a.user=b.id
				WHERE
					skill=$skill AND
					Fecha='$fecha' AND
					intervalo=$hora AND
					level=$level";
		if($result=$connectdb->query($query)){
			$fila=$result->fetch_assoc();
			$td['accion']=utf8_encode($fila['accion']);
			$td['comments']=utf8_encode($fila['comments']);
			$td['last_update']=utf8_encode($fila['last_update']);
			$td['username']=utf8_encode($fila['username']);
			$td['info']="Skill: $skill // Hora: ".($hora/2)."<br>Ultima actualizacion por: ".$fila['username']." (".$fila['last_update'].")<br>".$fila['comments'];
		}else{
			$td['info']="SIN ACCIONES REGISTRADAS<br>hora: ".($hora/2)." // skill= $skill";
		}

		if($td['accion']==NULL){
			$td['info']="SIN ACCIONES REGISTRADAS<br>hora: ".($hora/2)." // skill= $skill";
		}
		break;
	case "send":
		$query="INSERT INTO bitacora_base (intervalo, skill, Fecha, level, accion, user, comments) VALUES ($hora, $skill, '$fecha',$level,$accion,$user,'$comments')";
		if($result=$connectdb->query($query)){
			$td['status']=1;
			$td['accion']=utf8_decode($accion_name);
			$td['comments']=$comments;
			$td['indice']=$indice;
		}else{
			$query="UPDATE bitacora_base SET accion=$accion, user=$user, comments='$comments' WHERE intervalo=$hora AND skill=$skill AND Fecha='$fecha' AND level=$level";
			if($result=$connectdb->query($query)){
				$td['status']=1;
				$td['accion']=utf8_decode($accion_name);
				$td['comments']=$comments;
				$td['indice']=$indice;
			}else{
				$td['status']=0;
				$td['accion']="";
				$td['comments']="";
				$td['indice']="";
				$td['error']=$connectdb->error." ON $query";
			}
		}
		break;
	case "delete":
		$query="DELETE FROM bitacora_base WHERE intervalo=$hora AND skill=$skill AND Fecha='$fecha' AND level=$level";
		if($result=$connectdb->query($query)){
			$td['status']=1;
		}else{
			$td['status']=0;
			$td['error']=$connectdb->error." ON $query";
		}
		break;
}

$connectdb->close();

print json_encode($td,JSON_PRETTY_PRINT);



?>

<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$mx_time = new DateTimeZone('America/Mexico_City');

$flag=TRUE;

$asesor1=$_POST['asesor1'];
$tipo=$_POST['tipo'];

IF($_POST['asesor2'] == NULL){
	$asesor2='NULL';
}else{
	$asesor2=$_POST['asesor2'];
}
$user=$_POST['user'];

IF($_POST['caso'] == NULL){
	$caso='NULL';
}else{
	$caso=$_POST['caso'];
}
$user=$_POST['user'];

foreach($_POST['data'] as $key => $info){
	foreach($info as $key2 => $info2){
		switch(substr($key2, 0, -2)){
			default:
				$datos[substr($key2,-1,1)][substr($key2,-2,1)][substr($key2, 0, -2)]=$info2;
		}
	}
}

if(count($datos>0)){
	foreach($datos as $key => $info){
		foreach($info as $key3 => $info3){		
			foreach($info3 as $key2 => $info2){
			
				switch($key2){
					case "fecha":
						$data[$key][$key3][$key2]=date('Y-m-d',strtotime($info2));
						break;
					case "js":
					case "je":
					case "cs":
					case "ce":
					case "x2s":
					case "x2e":
					case "x1s":
					case "x1e":
						$tmp_index=$key;
						$tmp_date=date('Y-m-d',strtotime($info[$key3]['fecha']));
						$tmp = new DateTime($tmp_date." ".$info[$key3][$key2].":00 America/Bogota");
						$tmp -> setTimezone($mx_time);
						//echo $tmp_date." ".$info[$key2].":00 America/Bogota<br>";
						$data[$key][$key3][$key2]=$tmp -> format('H:i:s');
						break;
					default:
						$data[$key][$key3][$key2]=$info2;
				}
			}
		}
	}
}

if(count($data)>0){
	foreach($data as $index => $infok){
		foreach($infok as $key3 => $infoq){
		
			if($infoq['js']==$infoq['je']){
				$js='00:00:00';
				$je='00:00:00';
			}else{
				$js=$infoq['js'];
				$je=$infoq['je'];
			}
			
			if($infoq['cs']==$infoq['ce']){
				$cs='00:00:00';
				$ce='00:00:00';
			}else{
				$cs=$infoq['cs'];
				$ce=$infoq['ce'];
			}
			
			if($infoq['x1s']==$infoq['x1e']){
				$x1s='00:00:00';
				$x1e='00:00:00';
			}else{
				$x1s=$infoq['x1s'];
				$x1e=$infoq['x1e'];
			}
			
			if($infoq['x2s']==$infoq['x2e']){
				$x2s='00:00:00';
				$x2e='00:00:00';
			}else{
				$x2s=$infoq['x2s'];
				$x2e=$infoq['x2e'];
			}
			
			$query="SELECT * FROM `Historial Programacion` WHERE id='".$infoq['idh']."'";
			//echo "<br>$query<br>";
			if($result=$connectdb->query($query)){
				while($fila=$result->fetch_assoc()){
					$original['asesor']=$fila['asesor'];
					$original['fecha']=$fila['Fecha'];
					$original['js']=$fila['jornada start'];
					$original['je']=$fila['jornada end'];
					$original['cs']=$fila['comida start'];
					$original['ce']=$fila['comida end'];
					$original['x1s']=$fila['extra1 start'];
					$original['x1e']=$fila['extra1 end'];
					$original['x2s']=$fila['extra2 start'];
					$original['x2e']=$fila['extra2 end'];
				}
				
				$query="UPDATE `Historial Programacion` SET "
							."`jornada start`='$js', "
							."`jornada end`='$je', "
							."`comida start`='$cs', "
							."`comida end`='$ce', "
							."`extra1 start`='$x1s', "
							."`extra1 end`='$x1e', "
							."`extra2 start`='$x2s', "
							."`extra2 end`='$x2e' "
						." WHERE id='".$infoq['idh']."'";
				if($result=$connectdb->query($query)){
					if($asesor1==$original['asesor']){
						$t_asesor1=$asesor1;
						$t_asesor2=$asesor2;
					}else{
						$t_asesor2=$asesor1;
						$t_asesor1=$asesor2;
					}
					
					$query="INSERT INTO `Cambios de Turno` (id_horario, id_asesor, `id_asesor 2`, tipo, caso, fecha, "
															."`jornada start old`, `jornada end old`, `comida start old`, `comida end old`, "
															."`extra1 start old`, `extra1 end old`, `extra2 start old`, `extra2 end old`, "
															."`jornada start new`, `jornada end new`, `comida start new`, `comida end new`, "
															."`extra1 start new`, `extra1 end new`, `extra2 start new`, `extra2 end new`, "
															."User) VALUES ("
															."".$infoq['idh'].",$t_asesor1,$t_asesor2,$tipo,$caso,'".$original['fecha']."', "
															."'".$original['js']."','".$original['je']."','".$original['cs']."','".$original['ce']."', "
															."'".$original['x1s']."','".$original['x1e']."','".$original['x2s']."','".$original['x2e']."', "
															."'$js','$je','$cs','$ce', "
															."'$x1s','$x1e','$x2s','$x2e', "
															."$user)";
					if($result=$connectdb->query($query)){
						$inserted=$connectdb->insert_id;
						$query="UPDATE `Historial Programacion` SET `change`=$inserted WHERE id=".$infoq['idh'];
						$connectdb->query($query);
					}else{
						$flag=FALSE;
						$error[$index]=$connectdb->error." ON <br>$query<br><br>";
					}
					
				}else{
					$flag=FALSE;
					$error[$index]=$connectdb->error;
					//echo $connectdb->error."<br>ON $query<br>";
					
					$query="UPDATE `Historial Programacion` SET "
							."`jornada start`='".$original['js']."', "
							."`jornada end`='".$original['je']."', "
							."`comida start`='".$original['cs']."', "
							."`comida end`='".$original['ce']."', "
							."`extra1 start`='".$original['x1s']."', "
							."`extra1 end`='".$original['x1e']."', "
							."`extra2 start`='".$original['x2s']."', "
							."`extra2 end`='".$original['x2e']."' "
						." WHERE id='".$infoq['idh']."'";
					$connectdb->query($query);
					
				}
				
			}else{
				echo "ERROR retrieving originals: ".$connectdb->error;
				exit;
			}
		}
	unset($original);
	}
}


if($flag){
	echo "DONE";
	
}else{
	foreach($error as $index => $info){
		echo "$index: $info // ";
	}
}

$connectdb->close();
//print_r($original);
?>

<?php
include("../connectDB.php");

$tipo=$_POST['md'];

$i=0;
while($i<=100){
	$x=1;
	while($x<500){
		$data[$i][$x]=$_POST['a'.$i.'b'.$x];
	$x++;
	}
$i++;
}

$fecha1=$_POST['f1'];
$fecha2=$_POST['f2'];
$skill=$_POST['s'];
$date=explode("/",$fecha1);
$fecha1="$date[2]-$date[1]-$date[0]";
$date=explode("/",$fecha2);
$fecha2="$date[2]-$date[1]-$date[0]";

switch($tipo){
	case 1:
		$db="t_pausas";
		$fechaok=$fecha1;
		$md_inicio='Hora_Inicio';
		$md_fin='Hora_Fin';
		$md_field='pausas_id';
		break;
	case 2:
		$db="Comidas";
		$fechaok=$fecha1;
		$md_inicio='Inicio';
		$md_fin='Fin';
		$md_field='ComidaId';
		break;
		
}

$query="SELECT `COLUMN_NAME` as 'Column' FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='$db' AND TABLE_SCHEMA='comeycom_WFM'";
echo "$query<br>";
if($result=$connectdb->query($query)){
	$i=1;
	while($fila=$result->fetch_assoc()){
		$col[$i]=$fila['Column'];
		echo $col[$i]."<br>";
		$i++;
	}
}

function addTime($timeB, $timeA) {
    
    $timeAinSeconds = intval(date('H', strtotime($timeA)))*60*60 + intval(date('i', strtotime($timeA)))*60 + intval(date('s', strtotime($timeA)));
    $timeBinSeconds = intval(date('H', strtotime($timeB)))*60*60 + intval(date('i', strtotime($timeB)))*60 + intval(date('s', strtotime($timeB)));
    
   

    $timeABinSeconds = $timeAinSeconds - $timeBinSeconds;

    $timeABsec = $timeABinSeconds % 60;
    $timeABmin = (($timeABinSeconds - $timeABsec) / 60) % 60;
    $timeABh = ($timeABinSeconds - $timeABsec - $timeABmin*60) / 60 / 60;

    return str_pad((int) $timeABh,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABmin,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABsec,2,"0",STR_PAD_LEFT);
}

foreach($data as $key1 => $row){
	$x=0;
	$q_var="";
	$q_val="";
	foreach($row as $key2 => $info){ 
		if($info!=NULL){
			if($key2==1){
				$q_var=$col[$key2+1];
				$q_val="'$info'";
				$q_ins=$col[$key2+1]."='$info'";
			}else{
			switch($key2){
				case 8:
					
					$q_var="$q_var,".$col[$key2+1];
					$q_val="$q_val,'$info'";
					$q_ins=$q_ins.",".$col[$key2+1]."='$info'";
					break;
				case 9:
					$q_var="$q_var,".$col[$key2+1];
					$q_val="$q_val,'$info'";
					$q_ins=$q_ins.",".$col[$key2+1]."='$info'";
					break;
				default:
					$q_var="$q_var,".$col[$key2+1];
					$q_val="$q_val,'$info'";
					$q_ins=$q_ins.",".$col[$key2+1]."='$info'";
					break;
					}
			}
			
			$x++;
			
		}
	}
	

	
	if($q_var!=""){
		$query="SELECT * FROM $db WHERE Fecha='$fechaok' AND asesor='".$data[$key1][1]."' AND $md_inicio='".$data[$key1][3]."' AND Skill=$skill AND tipo='".$data[$key1][2]."'";
		echo "$query<br><br>";
		if($result=$connectdb->query($query)){
			$fila=$result->fetch_assoc();
			if($result->num_rows!=0){
				
				switch($tipo){
					case 1:
						$md_id=$fila['pausas_id'];
						break;
					case 2:
						$md_id=$fila['ComidaId'];
						break;
				}
				
				$query="UPDATE $db SET $q_ins, Fecha='$fechaok', Duracion='".addTime($data[$key1][3],$data[$key1][4])."', Skill='$skill' WHERE $md_field=$md_id";
			}else{
				$query="INSERT INTO $db ($q_var,Fecha,Duracion,Skill) VALUES ($q_val, '$fechaok', '".addTime($data[$key1][3],$data[$key1][4])."','$skill')";		
			}
		}
		echo "$query<br>";
		$connectdb->query($qerror);
		if($result=$connectdb->query($query)){
		
		}else{
			echo "$key1 // $key_xfered MySQL error ".$connectdb->error."\n<br>When executing <br>\n$query\n<br><br>";
			$error_error=$connectdb->real_escape_string($connectdb->error);
			$error_query=$connectdb->real_escape_string($query);
			$error_string=$connectdb->real_escape_string($_SERVER["QUERY_STRING"]);
		            $qerror="INSERT INTO Errores (site, error, query,string) VALUES ('ses.php','".$error_error."','$error_query','".$error_string."')";
		            $connectdb->query($qerror);
		}
        }
}

$query="SELECT $md_field, asesor, tipo, MIN($md_inicio) as iniciomin, COUNT($md_field) as Pausas, $md_fin FROM $db WHERE Fecha='$fechaok' GROUP BY asesor, tipo, Fin HAVING Pausas>1";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$query="DELETE FROM $db WHERE Fecha='$fechaok' AND $md_fin='".$fila[$md_fin]."' AND asesor=".$fila['asesor']." AND $md_inicio!='".$fila['iniciomin']."'";
		$connectdb->query($query);
	}
}






?>
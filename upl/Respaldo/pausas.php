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
		break;
	case 2:
		$db="comidas";
		$fechaok=$fecha2;
		break;
}
$query="SELECT `COLUMN_NAME` as 'Column' FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='$db'";
echo "$query<br>";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i <$num){
	$col[$i+1]=mysql_result($result,$i,'Column');
	echo $col[$i+1]."<br>";
$i++;
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
		$query="SELECT * FROM $db WHERE Fecha='$fechaok' AND asesor='".$data[$key1][1]."' AND Hora_Inicio='".$data[$key1][3]."' AND Skill=$skill";
		echo "$query<br><br>";
		$num=mysql_numrows(mysql_query($query));
		
		if($num>0){
			$md_id=mysql_result(mysql_query($query),0,'pausas_id');
			$query="UPDATE $db SET $q_ins, Fecha='$fechaok', Duracion='".addTime($data[$key1][3],$data[$key1][4])."', Skill='$skill' WHERE pausas_id=$md_id";
			echo "$query<br>";
			mysql_query($query);
		}else{ 
			$query="INSERT INTO $db ($q_var,Fecha,Duracion,Skill) VALUES ($q_val, '$fechaok', '".addTime($data[$key1][3],$data[$key1][4])."','$skill')";
			echo "$query<br>";
			mysql_query($query);
		}
	}
}

echo "Updated!";











?>
<?php
include("../connectDB.php");

$tipo=$_GET['md'];

$i=1;
while($i<=20){
	$x=1;
	while($x<500){
		$data[$i][$x]=$_GET['a'.$i.'b'.$x];
	$x++;
	}
$i++;
}

$fecha1=$_GET['f1'];
$fecha2=$_GET['f2'];
$date=explode("/",$fecha1);
$fecha1="$date[2]-$date[1]-$date[0]";
$date=explode("/",$fecha2);
$fecha2="$date[2]-$date[1]-$date[0]";

switch($tipo){
	case 1:
		$db="t_Montos_Diarios";
		$fechaok=$fecha1;
		break;
	case 2:
		$db="t_Montos_Diarios_Acumulado";
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
$i++;
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
				$q_var="$q_var,".$col[$key2+1];
				$q_val="$q_val,'$info'";
				$q_ins=$q_ins.",".$col[$key2+1]."='$info'";
			}
			
			$x++;
			
		}
	}
	if($q_var!=""){
		$query="SELECT * FROM $db WHERE Fecha='$fechaok' AND asesor='".$data[$key1][1]."'";
		$num=mysql_numrows(mysql_query($query));
		
		if($num>0){
			$md_id=mysql_result(mysql_query($query),0,'md_id');
			$query="UPDATE $db SET $q_ins, Fecha='$fechaok' WHERE md_id=$md_id"; 
			echo "$query<br>";
			mysql_query($query);
		}else{ 
			$query="INSERT INTO $db ($q_var, Fecha) VALUES ($q_val, '$fechaok')"; 
			echo "$query<br>";
			mysql_query($query);
		}
	}
}

echo "Updated!";











?>
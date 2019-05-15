<?php

$start='2016-01-01';
$end='2016-12-31';
if(isset($_GET['reg'])){$reg=$_GET['reg'];}else{$reg=0;}


include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");
?>
<table width='100%' class='t2'><form name='dates' method='post' action='<?php $_SERVER['PHP_SELF'] ?>'>
	<tr class='title'>
		<th colspan=100>Validacion de Llamadas Transferidas</th>
	</tr>
	</form></table><br><br>
<?php
function GetQuery($query, $db) {

	//Get Column Names
	$querydb="SELECT `COLUMN_NAME` as 'Column' FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='$db'";
	
	$resultdb=mysql_query($querydb);
	$numdb=mysql_numrows($resultdb);
	$i=0;
	while($i <$numdb){
		$col[$i]=mysql_result($resultdb,$i,'Column');
	$i++;
	}
	
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$i=0;
	while($i<$num){
		$y=0;
		while($y<$numdb){
			$data[$i][$col[$y]]=mysql_result($result,$i,'$col[$y]');
		$y++;
		}
	$i++;
	}
	
	return $data;
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

function addTimePlus($timeB, $timeA) {
    
    $timeAinSeconds = intval(date('H', strtotime($timeA)))*60*60 + intval(date('i', strtotime($timeA)))*60 + intval(date('s', strtotime($timeA)));
    $timeBinSeconds = intval(date('H', strtotime($timeB)))*60*60 + intval(date('i', strtotime($timeB)))*60 + intval(date('s', strtotime($timeB)));
    
   

    $timeABinSeconds = $timeAinSeconds + $timeBinSeconds;

    $timeABsec = $timeABinSeconds % 60;
    $timeABmin = (($timeABinSeconds - $timeABsec) / 60) % 60;
    $timeABh = ($timeABinSeconds - $timeABsec - $timeABmin*60) / 60 / 60;

    return str_pad((int) $timeABh,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABmin,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABsec,2,"0",STR_PAD_LEFT);
}

$fecha='2016-01-01';

$query="SELECT
    Fecha, Hora, Llamante, Hora_fin, AsteriskID, COUNT(*) as CountOf
FROM
    t_Answered_Calls

GROUP BY
    Fecha, Hora_fin
HAVING 
    COUNT(*) > 1 AND
    Fecha>='$start' AND
    FECHA<='$end'";
    
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	echo"$query<br><br>Total Rows: $num<br><br>";
	$i=0;
	while($i<$num){
		$y=0;
		
			$data[$i]['Fecha']=mysql_result($result,$i,'Fecha');
			$data[$i]['Hora']=mysql_result($result,$i,'Hora');
			$data[$i]['Llamante']=mysql_result($result,$i,'Llamante');
			$data[$i]['Hora_fin']=mysql_result($result,$i,'Hora_fin');
			$data[$i]['CountOf']=mysql_result($result,$i,'CountOf');
			$data[$i]['AsteriskID']=mysql_result($result,$i,'AsteriskID');
		
	$i++;
	}
    
foreach($data as $key1 => $row){
    if($key1>$reg+10000){
        echo "<script type=\"text/javascript\">
location.replace(\"xfer_checktest.php?reg=".($reg+10000)."\");
</script>";
    }
    if($key1>$reg){
	unset($data_xfered);
	$query="SELECT
			*
		FROM
			t_Answered_Calls
		WHERE
			Fecha='".$row['Fecha']."' AND Hora_fin='".$row['Hora_fin']."'
		ORDER BY
			Hora DESC";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	echo "------->".($key1+1)."<-------<br>:$query<br><br>";
	$i=0;
	while($i<$num){
		$y=0;
		
			$data_xfered[$i]['Fecha']=mysql_result($result,$i,'Fecha');
			$data_xfered[$i]['Hora']=mysql_result($result,$i,'Hora');
			$data_xfered[$i]['Llamante']=mysql_result($result,$i,'Llamante');
			$data_xfered[$i]['Hora_fin']=mysql_result($result,$i,'Hora_fin');
			$data_xfered[$i]['Duracion']=mysql_result($result,$i,'Duracion');
            $data_xfered[$i]['IVR']=mysql_result($result,$i,'IVR');
            $data_xfered[$i]['Espera']=mysql_result($result,$i,'Espera');
            $data_xfered[$i]['IVR_duration']=mysql_result($result,$i,'IVR_duration');
			$data_xfered[$i]['AsteriskID']=mysql_result($result,$i,'AsteriskID');
		
	$i++;
	}
	


	foreach($data_xfered as $key_xfered => $row_xfered){
		$total=count($data_xfered)-1;

		if($key_xfered!=0){
			$flag=1;
			if($data_xfered[$key_xfered]['Llamante']!=$data_xfered[$key_xfered-1]['Llamante']){
				echo " != Llamante //";
				$timeA=addTime($data_xfered[$key_xfered-1]['Duracion'],$data_xfered[$key_xfered]['Duracion']);
				if($data_xfered[$key_xfered-1]['Hora']==addTimePlus($data_xfered[$key_xfered]['Hora'],$timeA)){
					echo " == Hora Inicio:<br>";
					$flag=1;
				}else{echo " != Hora Inicio:<br>"; $flag=0;}
			
			}else{echo " == Llamante:<br>";$flag=1;}
			if($flag==1){
				//TO
				$time=addTime(addtimePlus($data_xfered[$key_xfered]['Hora'],addTimePlus($data_xfered[$key_xfered]['IVR'],addTimePlus($data_xfered[$key_xfered]['Espera'],$data_xfered[$key_xfered]['IVR_duration']))),$data_xfered[$key_xfered-1]['Hora']);
				echo "addTime(addtimePlus(".$data_xfered[$key_xfered]['Hora'].",addTimePlus(".$data_xfered[$key_xfered]['IVR'].",addTimePlus(".$data_xfered[$key_xfered]['Espera'].",".$data_xfered[$key_xfered]['IVR_duration']."))),".$data_xfered[$key_xfered-1]['Hora'].");";
				$query_check="SELECT * FROM t_transferencias WHERE asteriskID='".$row_xfered['AsteriskID']."'";
				$result_check=mysql_query($query_check);
				$num_check=mysql_num_rows($result_check);
				if($num_check==0){
					$query="INSERT INTO t_transferencias (asteriskID,xfer_to,Real_Lenght,initial_call,Total_xfers) VALUES ('".$row_xfered['AsteriskID']."','".$data_xfered[$key_xfered-1]['AsteriskID']."','$time','".$data_xfered[$total]['AsteriskID']."','".($total+1)."')";
				}else{
					$query="UPDATE t_transferencias SET xfer_to='".$data_xfered[$key_xfered-1]['AsteriskID']."', Real_Lenght='$time', initial_call='".$data_xfered[$total]['AsteriskID']."', Total_xfers='".($total+1)."' WHERE asteriskID='".$row_xfered['AsteriskID']."'";
				}
				$query_tabla="UPDATE t_Answered_Calls SET Desconexion='Transferida', Duracion_Real='$time' WHERE AsteriskID='".$row_xfered['AsteriskID']."'";
				mysql_query($query_tabla);
				echo "$query<br>$query_check<br>$query_tabla<br><br>";
				mysql_query($query);
				if(mysql_errno()){
				    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
				}
				
				//From
				
				$query_check="SELECT * FROM t_transferencias WHERE asteriskID='".$data_xfered[$key_xfered-1]['AsteriskID']."'";
				$result_check=mysql_query($query_check);
				$num_check=mysql_num_rows($result_check);
				if($num_check==0){
					$query="INSERT INTO t_transferencias (asteriskID,xfer_from,initial_call,Total_xfers) VALUES ('".$data_xfered[$key_xfered-1]['AsteriskID']."','".$data_xfered[$key_xfered]['AsteriskID']."','".$data_xfered[$total]['AsteriskID']."','".($total+1)."')";
				}else{
					$query="UPDATE t_transferencias SET xfer_from='".$data_xfered[$key_xfered]['AsteriskID']."', initial_call='".$data_xfered[$total]['AsteriskID']."',Total_xfers='".($total+1)."' WHERE asteriskID='".$data_xfered[$key_xfered-1]['AsteriskID']."'";
				}
				
				mysql_query($query_tabla);
				echo "$query<br>$query_check<br><br>";
				mysql_query($query);
				if(mysql_errno()){
				    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                         $err_count++;
				}
			}

		}
	}



}
}
/*
$query="SELECT ac_id, Duracion, Duracion_Real FROM t_Answered_Calls WHERE Answered='1'";
$result=mysql_query($query);
$num=mysql_num($result);
$i=0;
while($i<$num){
	$dr=mysql_result($result,$i,'Duracion_Real');
	$d=mysql_result($result,$i,'Duracion');
	$id=mysql_result($result,$i,'ac_id');
	if($dr==NULL){
		$query="UPDATE t_Answered_Calls SET Duracion_Real='$d' WHERE ac_id='$id'";
		mysql_query($query);
		if(mysql_errno()){
		    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
		         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
		}
	}
$i++;
}
  */
echo "Done!<br>$err_count Errors found";

?>
<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}

$menu_uploads="class='active'";
$credential="upload_info";
date_default_timezone_set('America/Bogota');
if(isset($_POST['start'])){$start=date('Y-m-d', strtotime($_POST['start']));}else{$start=date('Y-m-d');}
if(isset($_POST['end'])){$end=date('Y-m-d', strtotime($_POST['end']));}else{$end=date('Y-m-d');}
$err_count=0;
include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");
?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>
	
$(function () {
	
	$('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});
	
});

</script>


<table style='margin: auto; width:80%' class='t2'><form name='dates' method='post' action='<?php $_SERVER['PHP_SELF'] ?>'>
	<tr class='title'>
		<th colspan=100>Validacion de Llamadas Transferidas</th>
	</tr>
	<tr class='pair'>
		<td class='title' width='30%'>Periodo</td>
		<td><input type='text' id='inicio' name='start' value='<?php echo $inicio; ?>' required><input type='text' id='fin' name='end' value='<?php echo $fin; ?>' required></td>
        <td class='title'>Solo Llamadas</td>
        <td><input type="checkbox" name='onlycalls' /></td>
	</tr>
	<tr class='total'>
		<td colspan=100><input type='submit' name='consulta' value='Run'></td>
	</tr>
</form></table><br><br>
<?php
if(!isset($_POST['consulta'])){exit;}
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

function callQuery($query,$varname){
    global $data;
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $field_num=mysql_num_fields($result);
    $x=0;
    while($x<$field_num){
        $field[$x]=mysql_field_name($result,$x);

    $x++;
    }
    $i=0;
    while($i<$num){
        foreach($field as $key => $campo){
            $okcampo=str_replace(' ','_',$campo);
            $data[$varname][$okcampo][$i]=mysql_result($result,$i,$key);
        }

    $i++;
    }

}

function insertCaso($area, $variable,$key,$prog){
    global $data, $info, $err_case_count;
    $query="SELECT * FROM bo_casos WHERE area='$area' AND id='$info' AND programa='$prog'";
     if(mysql_numrows(mysql_query($query))==NULL){
        $query="INSERT INTO bo_casos (caso,fecha,hora,fecha_registro, Hora_registro, user,area,id,programa)
        VALUES ('".$data[$variable]['em'][$key]."','".$data[$variable]['fecha_recepcion'][$key]."',
        '".$data[$variable]['hora_recepcion'][$key]."','".date('Y-m-d',strtotime($data[$variable]['date_created'][$key]))."',
        '".date('H:i:s',strtotime($data[$variable]['date_created'][$key]))."','".$data[$variable]['user'][$key]."',
        '$area','$info','$prog')";
        mysql_query($query);
            if(mysql_errno()){
				    echo "Caso $key // MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                         $err_case_count++;
				}else{echo "Caso $key // OK<br><br> ";}
     }else{echo "Caso $key // Already Exists<br><br> ";}
}

$fecha='2016-01-01';
//casos
if(!isset($_POST['onlycalls'])){
$query="SELECT * FROM bo_mailing WHERE date_created>='$start' AND date_created<'".date('Y-m-d', strtotime($end.' +1 days'))."' AND actividad!=8";
echo "$query<br>";
callQuery($query,'mailing');
foreach($data['mailing']['confirming_id'] as $key => $info){
    insertCaso('2','mailing',$key,'6');
}
$query="SELECT * FROM bo_mejora_continua WHERE date_created>='$start' AND date_created<'".date('Y-m-d', strtotime($end.' +1 days'))."'";
echo "$query<br>";
callQuery($query,'mc');
foreach($data['mc']['mejora_id'] as $key => $info){
    insertCaso('3','mc',$key,'6');
}
$query="SELECT * FROM bo_reembolsos WHERE date_created>='$start' AND date_created<'".date('Y-m-d', strtotime($end.' +1 days'))."'";
echo "$query<br>";
callQuery($query,'reembolsos');
foreach($data['reembolsos']['confirming_id'] as $key => $info){
    insertCaso('4','reembolsos',$key,'6');
}
$query="SELECT * FROM bo_confirming WHERE date_created>='$start' AND date_created<'".date('Y-m-d', strtotime($end.' +1 days'))."'";
echo "$query<br>";
callQuery($query,'confirming');
foreach($data['confirming']['confirming_id'] as $key => $info){
    insertCaso('1','confirming',$key,'6');
}

}
//Xfers

$query="SELECT
    ac_id, Fecha, Hora, Llamante, Hora_fin, AsteriskID, COUNT(*) as CountOf
FROM
    t_Answered_Calls
WHERE
    Fecha>='$start' AND
    FECHA<='$end'
GROUP BY
    Fecha, Hora_fin

HAVING 
    CountOf > 1

";
    
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	echo"$query<br><br>Total Rows: $num<br><br>";
	$i=0;
	while($i<$num){
		$y=0;
		    $data[$i]['id']=mysql_result($result,$i,'ac_id');
			$data[$i]['Fecha']=mysql_result($result,$i,'Fecha');
			$data[$i]['Hora']=mysql_result($result,$i,'Hora');
			$data[$i]['Llamante']=mysql_result($result,$i,'Llamante');
			$data[$i]['Hora_fin']=mysql_result($result,$i,'Hora_fin');
			$data[$i]['CountOf']=mysql_result($result,$i,'CountOf');
			$data[$i]['AsteriskID']=mysql_result($result,$i,'AsteriskID');
		
	$i++;
	}

foreach($data as $key1 => $row){
    if($key1>=0){
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
            $data_xfered[$i]['id']=mysql_result($result,$i,'ac_id');
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
				$query_check="SELECT * FROM t_transferencias WHERE asteriskID='".$row_xfered['AsteriskID']."' LIMIT 1";
				$result_check=mysql_query($query_check);
				$num_check=mysql_num_rows($result_check);
				if($num_check==0){
					$query="INSERT INTO t_transferencias (asteriskID,xfer_to,Real_Lenght,initial_call,Total_xfers) VALUES ('".$row_xfered['AsteriskID']."','".$data_xfered[$key_xfered-1]['AsteriskID']."','$time','".$data_xfered[$total]['AsteriskID']."','".($total+1)."')";
				}else{
					$query="UPDATE t_transferencias SET xfer_to='".$data_xfered[$key_xfered-1]['AsteriskID']."', Real_Lenght='$time', initial_call='".$data_xfered[$total]['AsteriskID']."', Total_xfers='".($total+1)."' WHERE asteriskID='".$row_xfered['AsteriskID']."'";
				}
				$query_tabla="UPDATE t_Answered_Calls SET Desconexion='Transferida', Duracion_Real='$time' WHERE ac_id='".$row_xfered['id']."'";
				mysql_query($query_tabla);
				echo "$query<br>$query_check<br>$query_tabla<br><br>";
				mysql_query($query);
				if(mysql_errno()){
				    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
				}
				
				//From
				
				$query_check="SELECT * FROM t_transferencias WHERE asteriskID='".$data_xfered[$key_xfered-1]['AsteriskID']."' LIMIT 1";
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

echo "Done!<br>$err_count Errors on calls and <br> $err_case_count Errors on EM found";

?>
<?php

include("../connectDB.php");
$start=$_POST['start'];
$end=$_POST['end'];
$datestart=strtotime($start);
$dateend=strtotime($end);



$tipo=$_POST['tipo'];
switch($tipo){
	case "ans":
		$db="t_Answered_Calls";
		$astid=13;
		break;
	case "abn":
		$db="t_Unanswered_Calls";
		$astid=14;
		break;
}



//Upload File
	$target_dir = "../uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOK = 1;
	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$filename = $target_dir . "tmp." . $FileType;
	
	if($FileType!='csv'){
		$uploadOK=0;
		
	}
	
	if($uploadOK==0){
			$result= "Ivalid File! // Ext: $FileType";
		}else{
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename)) {
		        $result= "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		    } else {
		        $result= "Sorry, there was an error uploading your file.";
	    	}
	    
	    	
	}

function addTime($timeB, $timeA) {
    
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

//Get Column Names
$query="SELECT `COLUMN_NAME` as 'Column' FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='$db'";

$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i <$num){
	$col[$i]=mysql_result($result,$i,'Column');
$i++;
}
	
//Read CSV
function printTable(){
	global $data, $regs, $tipo;
	$fila = 1;
	if (($gestor = fopen("../uploads/tmp.csv", "r")) !== FALSE) {
		echo "\n<table class='t2' style='width:100%'>\n";
	    while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
	    	$regs=$fila;
	    	$data[]=$datos;
	    	if($fila==1){
	    		if($tipo=="ans" && $datos[1]!="Llamante"){ echo "El archivo seleccionado no coincide con el formato para llamadas contestadas"; exit;}
	    		if($tipo=="abn" && $datos[1]!="Agente"){ echo "El archivo seleccionado no coincide con el formato para llamadas no contestadas"; exit;}
	    	}
	        if($fila % 2 == 0){$class="pair";}else{$class="odd";}
	        if($fila==1){$class="title";}
	        
	        $numero = count($datos);
	        echo "\t<tr class='$class'>\n";
	        
	        for ($c=0; $c < $numero; $c++) {
	        	
	            echo "\t\t<td>$datos[$c]</td>\n";
	        }
	        $fila++;
	        echo "\t</tr>\n";
	    }
	    	echo "<table>";
	    fclose($gestor);
	}
	unlink("../uploads/tmp.csv");
}
?>

<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

<?php include("../common/menu.php") ?>

<table class='t2' style="width:100%">
	<tr class='title'>
		<th colspan=2>Resultados de archivo subido</th>
	</tr>
	
</table>

<br><br>

<?php printTable();


?>
<br>

<?php

/*
<table class='t2' style='width:100%'>
	<tr class='title'>
		<th>Asesor</th>
		<th>Id</h>
		<th>Fecha</th>
		<th>Jornada Start</th>
		<th>Jornada End</th>
		<th>Comida Start</th>
		<th>Comida End</th>
	</tr>
*/?>

<?php

foreach($data as $key => $printinfo){
	if($key!=0){
	$i=1;
	if($tipo=="ans"){
	//get userid
		$query="SELECT `id` FROM `Asesores` WHERE `N Corto`='".substr($printinfo[8],0,-6)."'";
		$result=mysql_query($query);
		$userid[$key]=mysql_result($result,0,'id');
	}else{$userid[$key]="";}
	$x=0;
		
		$query_check="SELECT `ac_id` FROM `$db` WHERE `Fecha`='".date('Y-m-d', strtotime('2016-'.substr($printinfo[0],0,2).'-'.substr($printinfo[0],4,2)))."' AND AsteriskID=".$data[$key][$astid];
		
		
		$result_check=mysql_query($query_check);
		$rows_check=mysql_numrows($result_check);
		foreach($printinfo as $key2 => $info){ 
			
				if($key2==1){
					$hora_inicio=date('H:i:s', strtotime(substr($printinfo[$key2-1],8,8)));
					$fecha_inicio=date('Y-m-d', strtotime('2016-'.substr($printinfo[$key2-1],0,2).'-'.substr($printinfo[$key2-1],4,2)));
					$q_var=$col[$x].",".$col[$x+1];
					$q_val="'$fecha_inicio', '$hora_inicio'";
					$q_ins=$col[$x]."='".date('Y-m-d', strtotime('2016-'.substr($printinfo[$key2-1],0,2).'-'.substr($printinfo[$key2-1],4,2)))."', ".$col[$x+1]."='".date('H:i:s', strtotime(substr($printinfo[$key2-1],8,8)))."'";
					$x++;
				}else{
				//if($printinfo[$key2-1]=="";){$printinfo[$key2-1]="/n";}
					if($tipo=="ans"){
					switch($key2){
						/*case 4:
						case 5:
						case 6:
						case 16:
						case 17:
							$time = explode(":",$printinfo[$key2-1]);
							if(strlen($printinfo[$key2-1])>6){
								
								$printinfo[$key2-1]=date('H:i:s', strtotime('00:'.str_pad((int) $time[0],2,"0",STR_PAD_LEFT).':'.str_pad((int) $time[1],2,"0",STR_PAD_LEFT)));
							}else{
								$printinfo[$key2-1]=date('H:i:s', strtotime(str_pad((int) $time[0],2,"0",STR_PAD_LEFT).':'.str_pad((int) $time[1],2,"0",STR_PAD_LEFT).':'.str_pad((int) $time[2],2,"0",STR_PAD_LEFT)));
							}
							$q_var="$q_var,".$col[$x];
							$q_val="$q_val,'".$printinfo[$key2-1]."'";
							$q_ins=$q_ins.",".$col[$x]."='".$printinfo[$key2-1]."'";
							break;*/
						case 9:
							$q_var="$q_var,".$col[$x];
							$q_val="$q_val,'".$userid[$key]."'";
							$q_ins=$q_ins.",".$col[$x]."='".$userid[$key]."'";
							break;
						case 2:
						case 19:
							if(substr($printinfo[$key2-1],0,1)=="'"){$printinfo[$key2-1]=substr($printinfo[$key2-1],1);}
							$q_var="$q_var,".$col[$x];
							$q_val="$q_val,'".$printinfo[$key2-1]."'";
							$q_ins=$q_ins.",".$col[$x]."='".$printinfo[$key2-1]."'";
							break;
						
						default:
							
							$q_var="$q_var,".$col[$x];
							$q_val="$q_val,'".$printinfo[$key2-1]."'";
							$q_ins=$q_ins.",".$col[$x]."='".$printinfo[$key2-1]."'";
							break;
					}
					}else{
					switch($key2){
						case 3:
						case 18:
							if(substr($printinfo[$key2-1],0,1)=="'"){$printinfo[$key2-1]=substr($printinfo[$key2-1],1);}
							$q_var="$q_var,".$col[$x];
							$q_val="$q_val,'".$printinfo[$key2-1]."'";
							$q_ins=$q_ins.",".$col[$x]."='".$printinfo[$key2-1]."'";
							break;
						
						default:
							$q_var="$q_var,".$col[$x];
							$q_val="$q_val,'".$printinfo[$key2-1]."'";
							$q_ins=$q_ins.",".$col[$x]."='".$printinfo[$key2-1]."'";
							break;
					}
					}
				}
				
				$x++;
				
			
		}
		if($tipo=='ans'){
			$hora_fin=addtime($hora_inicio,addtime($data[$key][4],addtime($data[$key][16],$data[$key][5])));
		}else{
			$hora_fin=addtime($hora_inicio,addtime($data[$key][7],$data[$key][15]));
		}
		if($rows_check==0){
			$query="INSERT INTO `$db` ($q_var,Hora_fin) VALUES ($q_val,'$hora_fin')";
		}else{
		
			$qid=mysql_result($result_check,0,'ac_id');
			$query="UPDATE `$db` SET $q_ins,Hora_fin='$hora_fin' WHERE `ac_id`='$qid'";
		}
		
		mysql_query($query);
		if(mysql_errno()){
		    echo "$key MySQL error ".mysql_errno().": "
		         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
		}
		
	
	
	}


}
?>
</table>
<?php


NoDatesError:
if($error==1){echo "No dates selected or Invalid Dates";}

?>
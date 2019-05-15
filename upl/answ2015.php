<?php

include("../connectDB.php");
$start=$_POST['start'];
$end=$_POST['end'];
$datestart=strtotime($start);
$dateend=strtotime($end);
$err_count=0;



$tipo=$_POST['tipo'];
switch($tipo){
	case "ans":
		$db="t_Answered_Calls";
		$astid=13;
		$answ=1;
		break;
	case "abn":
		$db="t_Answered_Calls";
		$astid=14;
		$answ=0;
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
	    		if($tipo=="ans" && ($datos[1]!="Llamante" || $datos[18]!="DNIS")){ echo "El archivo seleccionado no coincide con el formato para llamadas contestadas"; exit;}
	    		if($tipo=="abn" && ($datos[1]!="Agente" || $datos[17]!="DNIS")){ echo "El archivo seleccionado no coincide con el formato para llamadas no contestadas"; exit;}
	    	}
	        if($fila % 2 == 0){$class="pair";}else{$class="odd";}
	        if($fila==1){$class="title";}
	        
	        $numero = count($datos);
	        //echo "\t<tr class='$class'>\n";
	        
	        for ($c=0; $c < $numero; $c++) {
	        	
	           // echo "\t\t<td>$datos[$c]</td>\n";
	        }
	        $fila++;
	        //echo "\t</tr>\n";
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
ob_start();
echo "Start ".count($data)." Regs...<br>";
ob_flush();
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
		
		$query_check="SELECT `ac_id` FROM `$db` WHERE AsteriskID=".$data[$key][$astid];
		

		$result_check=mysql_query($query_check);
		$rows_check=mysql_numrows($result_check);
		foreach($printinfo as $key2 => $info){ 
			
				if($key2==1){
					$hora_inicio=date('H:i:s', strtotime(substr($printinfo[$key2-1],8,8)));
					$fecha_inicio='2015-'.substr($printinfo[$key2-1],0,2).'-'.substr($printinfo[$key2-1],3,2);
					$q_var=$col[$x].",".$col[$x+1];
					$q_val="'$fecha_inicio', '$hora_inicio'";
					$q_ins=$col[$x]."='$fecha_inicio', ".$col[$x+1]."='$hora_inicio'";
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
						case 2:
							$column=$col[10];
							break;
						case 3:
							$column=$col[3];
							break;
						case 4:
							$column=$col[4];
							break;
						case 5:
							$column=$col[9];
							break;
						case 6:
							$column=$col[24];
							break;
						case 7:
							$column=$col[5];
							break;
						case 8:
							$column=$col[6];
							break;
						case 9:
							$column=$col[8];
							break;
						case 10:
							$column=$col[11];
							break;
						case 11:
							$column=$col[12];
							break;
						case 12:
							$column=$col[25];
							break;
						case 13:
							$column=$col[13];
							break;
						case 14:
							$column=$col[14];
							break;
						case 15:
							$column=$col[15];
							break;
						case 16:
							$column=$col[18];
							break;
						case 17:
							$column=$col[19];
							break;
						case 18:
							$column=$col[20];
							break;
						case 19:
							$column=$col[21];
							break;
						case 20:
							$column=$col[22];
							break;
						
					}
					switch($key2){
						
						case 3:
						case 18:
							if(substr($printinfo[$key2-1],0,1)=="'"){$printinfo[$key2-1]=substr($printinfo[$key2-1],1);}
							$q_var="$q_var,".$column;
							$q_val="$q_val,'".$printinfo[$key2-1]."'";
							$q_ins=$q_ins.",".$column."='".$printinfo[$key2-1]."'";
							break;
						
						default:
							$q_var="$q_var,".$column;
							$q_val="$q_val,'".$printinfo[$key2-1]."'";
							$q_ins=$q_ins.",".$column."='".$printinfo[$key2-1]."'";
							break;
					}
					}
				}
				
				$x++;
				
			
		}
		if($data[$key][1]!='*'){
			if($tipo=="ans"){
				if($data[$key][16]==NULL || $data[$key][16]==""){$data[$key][16]="00:00:00";}
				$hora_fin=addtime($hora_inicio,addtime($data[$key][4],addtime($data[$key][5],$data[$key][3])));
				$dr=$data[$key][5];
			}else{
				$hora_fin=addtime($hora_inicio,addtime($data[$key][7],$data[$key][8]));
				$dr="";
			}

			if($rows_check==0){
				$query="INSERT INTO `$db` ($q_var,Hora_fin,Answered,Duracion_Real) VALUES ($q_val,'$hora_fin','$answ','$dr')";
			}else{
			
				$qid=mysql_result($result_check,0,'ac_id');
				$query="UPDATE `$db` SET $q_ins,Hora_fin='$hora_fin', Answered='$answ' WHERE `ac_id`='$qid'";
			}
			
			mysql_query($query);
            echo "$key:<br>$query<br>";
			if(mysql_errno()){
			    echo "$key MySQL error ".mysql_errno().": "
			         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                     $err_count++;
			}else{echo "OK<br><br>";}
		}else{echo "Llamante = *<br><br>";}
		
	
	
	}

ob_flush();
}

echo "Done!...<br>$err_count errors.";
ob_end_flush();
?>
</table>
<?php


NoDatesError:
if($error==1){echo "No dates selected or Invalid Dates";}

?>
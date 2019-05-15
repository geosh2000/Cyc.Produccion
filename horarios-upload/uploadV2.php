<?php

include_once("../modules/modules.php");

initSettings::start(true,'schedules_upload');

$mx_zone = new DateTimeZone('America/Mexico_City');

$start=$_POST['start'];
$end=$_POST['end'];
$datestart=date('Y-m-d', strtotime($start));
$dateend=date('Y-m-d', strtotime($end));


$flag=false;

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
				$flag=true;
		    } else {
		        $result= "Sorry, there was an error uploading your file.";
	    	}
	    
	    	
	}

echo "$result<br>";

if($flag){
	$fila = 1;
	if (($gestor = fopen("../uploads/tmp.csv", "r")) !== FALSE) {
		while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
			$data[]=$datos;
	        $numero = count($datos);
			if($numero!=16){echo "Archivo no Valido... columnas no corresponden"; unlink("../uploads/tmp.csv"); exit;}
	    }
	}
}
	
unlink("../uploads/tmp.csv");

?>
<br>
<table class='t2' style="width:100%">
	<tr class='title'>
		<th colspan=2>Resultados de archivo subido</th>
	</tr>
	<tr class='subtitle'>
		 <td colspan=100><?php echo "De ".date('d/m/Y', strtotime($start))." a ".date('d/m/Y', strtotime($end)); ?></td>
	</tr>
</table>
<br>
<table class='t2' style='width: 95%; margin: auto; text-align: center;'>
	<tr>
		<?php
			foreach($data[0] as $index => $info){
				echo "<th>$info</th>\n";
			}
		?>
		<th>Upload Status</th>
	</tr>
	<?php
		
		$connectdb=Connection::mysqliDB('CC');
	
		foreach($data as $index => $info){
			if($index==0){continue;}
			echo "<tr>";
			foreach($info as $index2 => $info3){
				echo "<td>$info3</td>\n";
			}
			unset($index2,$info3);
			
			for($i=date('Y-m-d',strtotime($datestart));$i<=date('Y-m-d',strtotime($dateend));$i=date('Y-m-d',strtotime($i.' +1 days'))){
				unset($error);
				foreach($info as $index2 => $info3){
					if($index2>=14){continue;}
					if($info3=="Descanso" || $info3=="NA"){
							$upload[$index2]['inicio']='00:00:00';
							$upload[$index2]['fin']='00:00:00';
						}elseif(strlen($info3)==13){
							$tmp=explode("-", str_replace(" ", "", $info3));
							
							$tmpinicio = new DateTime($i.' '.$tmp[0].':00 America/Bogota');
							$tmpinicio -> setTimezone($mx_zone);
							$upload[$index2]['inicio']=$tmpinicio -> format('H:i:s');
							
							$tmpinicio = new DateTime($i.' '.$tmp[1].':00 America/Bogota');
							$tmpinicio -> setTimezone($mx_zone);
							$upload[$index2]['fin']=$tmpinicio -> format('H:i:s');
							
							if($upload[$index2]['fin']==$upload[$index2]['inicio']){
								$upload[$index2]['inicio']='00:00:00';
								$upload[$index2]['fin']='00:00:00';
							}
							
						}else{
							$error.="| Error($index2) -> ".$connectdb->error." ";
						}
				}
				if(!isset($error)){
					$dow=date('N', strtotime($i));
					$query="INSERT INTO `Historial Programacion` (`id`,`asesor`,`Fecha`,`jornada start`,`jornada end`,`comida start`,`comida end`) VALUES (NULL,getByUser('".$info[15]."'),'$i','".$upload[$dow+6]['inicio']."','".$upload[$dow+6]['fin']."','".$upload[$dow-1]['inicio']."','".$upload[$dow-1]['fin']."')";
					if(!$result=$connectdb->query($query)){
						
						if($connectdb->errno==1062){
							$query="UPDATE `Historial Programacion` SET `jornada start`='".$upload[$dow+6]['inicio']."',`jornada end`='".$upload[$dow+6]['fin']."',`comida start`='".$upload[$dow-1]['inicio']."',`comida end`='".$upload[$dow-1]['fin']."' WHERE `asesor`=getByUser('".$info[15]."') AND `Fecha`='$i'";		
							if(!$result=$connectdb->query($query)){
								$error=$connectdb->error;
							}else{
								$success="OK";
							}
						}else{
							$error=$connectdb->error;
						}
						
					
					}else{
						$success="OK";
					}			

				}
			}
			unset($upload);
			if(!isset($error)){
				echo "<td>$success</td>\n";
			}else{
				echo "<td>$error</td>\n";
			}
			echo "</tr>";
		}
		
		
		$connectdb->close();

	?>
</table>


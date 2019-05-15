<?php
include_once("../modules/modules.php");

initSettings::start(true);

$start=$_POST['start'];
$end=$_POST['end'];
$datestart=date('Y-m-d', strtotime($start));
$dateend=date('Y-m-d', strtotime($end));
$skill=$_POST['skill'];
$errores=0;




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
	
//Read CSV
$fila = 1;

if (($gestor = fopen("../uploads/tmp.csv", "r")) !== FALSE) {
	while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
    	$numero = count($datos);
		
		if ($fila==1){
        	for ($c=0; $c < $numero; $c++) {
        		switch($c){
	        		case 0:
	        			$validate="Lunes";
	        			break;
	                   case 1:
	        			$validate="Martes";
	        			break;
	                   case 2:
	        			$validate="Miercoles";
	        			break;
	                   case 3:
	        			$validate="Jueves";
	        			break;
	                   case 4:
	        			$validate="Viernes";
	        			break;
	        		case 5:
	        			$validate="Sabado";
	        			break;
	        		case 6:
	        			$validate="Domingo";
	        			break;
	        	}
	        	
	        	if($datos[$c]!=$validate){
	        		echo "ERROR! Las columnas no coinciden con el formato necesario -> ".$datos[$c]." not like $validate";
	        		unlink("../uploads/tmp.csv");
	        		exit();
	        	}
	        		
	        }
			$fila++;
        }else{
        	$data[]=$datos;
        }
		
    }
    fclose($gestor);
}
unlink("../uploads/tmp.csv");


exit;
?>
<br>
<table class='t2' style='margin:auto'>
	<?php 
	
	$connectdb=Connection::mysqliDB('CC');
	foreach($data as $hora => $info){
		foreach($hora as $dow => $info2){
			
		}
	}
	
	for($x=0;$x<48;$x++){
		echo "<tr>\n\t";
		if($x==0){
			for($i=$datestart;date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($dateend));$i=date('Y-m-d',strtotime($i.' +1 days'))){
				echo "<th>$i</th>\n\t";
			}
			echo "</tr>\n\t<tr>"; 
		}
		
		for($i=$datestart;date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($dateend));$i=date('Y-m-d',strtotime($i.' +1 days'))){
			echo "<td>".$data[$x][date('w',strtotime($i))]."</td>\n\t";
			$query="INSERT INTO forecast_participacion VALUES ('$i','$skill','$x','".$data[$x][date('w',strtotime($i))]."')";
			if(!$result=$connectdb->query($query)){
        		$query="UPDATE forecast_participacion SET participacion='".$data[$x][(date('N',strtotime($i))-1)]."' WHERE Fecha='$i' AND skill='$skill' AND hora='$x'";
					if(!$result=$connectdb->query($query)){	
	          			$errores++;
					}
			}
		}
		echo "</tr>\n\t";
			
	}
	$connectdb->close();
	?>
	
</table>

<?php echo "Errores: $errores"; ?>

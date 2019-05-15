<?php
include_once('../modules/modules.php');

initSettings::start(true,'upload_info');
initSettings::printTitle('Subir Llamadas Oreka');
timeAndRegion::setRegion('Cun');

$pdodb=Connection::pdoDB('CC');

$err_count=0;

$normalizeChars = array(
	    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
	    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
	    'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
	    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
	    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
	    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
	    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
	    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
	);



//Upload CSV File
	$target_dir = "../uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOK = 1;
	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$filename = $target_dir . "tmp_oreka." . $FileType;

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
if (($gestor = fopen("../uploads/tmp_oreka.csv", "r")) !== FALSE) {
    $fila=1;

    while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {

      $rowInfo=explode(",",$datos[0]);

      if($fila==1){
        $titles=$rowInfo;
        $fila++;
      }

      if($rowInfo[0]!='Start Date'){
          $row[]=$rowInfo;
      }
    }

    fclose($gestor);
    unlink("../uploads/tmp_oreka.csv");

}

foreach ($row as $key => $value) {
  foreach ($value as $key2 => $value2) {
    $rowOK[$key][":".strtolower(str_replace(")","",str_replace("(","",str_replace(" ","_",$titles[$key2]))))]=$value2;
  }
}
echo "<pre>";
print_r($rowOK);
echo "</pre>";

  $inserted=0;
  $updated =0;
  $errors=0;
  $lasterror=[];

  $query="INSERT INTO oreka_calls VALUES (NULL,:start_date,:end_date,:duration_sec,:call_id,:dir,:local_party,:remote_party,:lastname,:firstname)
          ON DUPLICATE KEY UPDATE
          Inicio=:start_date,
          Fin= :end_date,
          duracion=:duration_sec";

  $insert = $pdodb->prepare($query);

  foreach($rowOK as $index => $fila){

    if($insert->execute($fila)){
        $inserted++;

      }else{
        $error=$insert->errorInfo();
        $errors++;
        $lasterror[]="insert $index: 1->".$error[0]." 2-> ".$error[2];
      }
        $insert->closeCursor();//optional
  }
?>

<br>

<?php
echo "<p style='width: 80%; height: 30px; background: white; margin: auto; padding: 0;'>Inserted: $inserted // Updated: $updated // Errores: $errors</p>";
echo "<br>";

foreach($lasterror as $index => $info){
echo "<p>$info</p>";
}



exit;

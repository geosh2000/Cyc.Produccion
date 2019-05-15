<?php
include_once('../modules/modules.php');

initSettings::start(true,'upload_info');
initSettings::printTitle('Subir Llamadas');
timeAndRegion::setRegion('Cun');

$pdodb=Connection::pdoDB('CC');

$year=$_POST['year'];

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


//functions
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

//Read CSV
if (($gestor = fopen("../uploads/tmp.csv", "r")) !== FALSE) {
    $fila=1;
    $i=0;
    while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {

        if($fila==1){
        	//Create Titles
            foreach($datos as $key => $title){
                $data_title[$key]=str_replace(" ", "_", strtr($title, $normalizeChars));
            }
            unset($key,$title);

			//Validate Type
			switch($data_title[1]){
				case "Llamante":
				case "Caller":
					$tipo='Answered';
					$answered=1;
					break;
				case "Agente":
				case "Agent":
					$tipo='Unanswered';
					$answered=0;
					break;
			}

        }else{

			//Build Info Array
            foreach($datos as $key => $info){

				switch($data_title[$key]){
					case "Fecha":
						$data[$i][":Fecha"]=date('Y-m-d',strtotime($year."/".substr($info, 0,5)));
						$data[$i][":Hora"]=date('H:i:s',strtotime(substr($info, 8,100)));
						break;
					case "Administradas_por":
					case "Agente":
						$data[$i][":Asesor"]=substr($info, 0, strpos($info, "(")-1);
						break;
					case "Pos.":
						$data[$i][":Pos"]=$info;
						break;
					case "DNIS":
						$data[$i][":DNIS"]=str_replace("'", "", $info);
						break;
					case "":
					case "Tag":
					case "Feat":
					case "Vars":
					case "Logro":
					case "Variables":
					case "Codigo":
					case "Clave":
					case "Stints":
					case "Server":
						case "Variabiles":
						break;
					default:
						$data[$i][":".$data_title[$key]]=$info;
				}
			}
            unset($key,$info);

			//End Call
	        if($data[$i][':Duracion_IVR']==NULL || $data[$i]['Duracion_IVR']==""){
	        	$data[$i][':Duracion_IVR']="00:00:00";
			}

	        if($answered==1){
	            $data[$i][':Hora_fin']=addtime($data[$i][':Hora'],addtime($data[$i][':Espera'],addtime($data[$i][':Duracion'],$data[$i][':IVR'])));
	            $data[$i][':Duracion_Real']=$data[$i][':Duracion'];
	        }else{
	            $data[$i][':Hora_fin']=addtime($data[$i][':Hora'],addtime($data[$i][':IVR'],$data[$i][':Espera']));
				unset($data[$i][":Asesor"]);
	        }


            $i++;
        }
        $fila++;
    }

    fclose($gestor);
    unlink("../uploads/tmp.csv");

}

switch($answered){
	case 1:
		$queryInsert="INSERT INTO t_Answered_Calls "
					."(Fecha, Hora, Llamante, Cola, IVR, Espera, Duracion, Pos, Desconexion, asesor, Intentos, AsteriskID, MOH__events, MOH_duration, IVR_duration, IVR_path, DNIS, Hora_fin, Pos_salida, Answered, Duracion_Real)"
					." VALUES "
					." (:Fecha, :Hora, :Llamante, :Cola, :IVR, :Espera, :Duracion, :Pos, :Desconexion, getIdAsesor( :Asesor , 2), :Intentos, :Asterisk_ID,:Eventos_de_Musica_en_Espera,:Duracion_Musica_en_Espera,:Duracion_IVR,:Ruta_IVR,:DNIS,:Hora_fin,NULL,1,:Duracion_Real)";
		$queryUpdate="UPDATE t_Answered_Calls SET "
					."Fecha= :Fecha , "
					."Hora= :Hora , "
					."Llamante= :Llamante , "
					."Cola= :Cola , "
					."IVR= :IVR , "
					."Espera= :Espera , "
					."Duracion= :Duracion , "
					."Pos= :Pos , "
					."Desconexion= :Desconexion , "
					."asesor= getIdAsesor( :Asesor , 2) , "
					."Intentos= :Intentos , "
					."MOH__events= :Eventos_de_Musica_en_Espera , "
					."MOH_duration= :Duracion_Musica_en_Espera , "
					."IVR_duration= :Duracion_IVR , "
					."IVR_path= :Ruta_IVR , "
					."DNIS= :DNIS , "
					."Hora_fin= :Hora_fin , "
					."Pos_salida= NULL , "
					."Answered= 1 , "
					."Duracion_Real= :Duracion_Real "
					."WHERE AsteriskID= :Asterisk_ID ";
		break;
	case 0:
		$queryInsert="INSERT INTO t_Answered_Calls "
					."(Fecha, Hora, Llamante, Cola, IVR, Espera, Duracion, Pos, Desconexion, asesor, Intentos, AsteriskID, MOH__events, MOH_duration, IVR_duration, IVR_path, DNIS, Hora_fin, Pos_salida, Answered, Duracion_Real)"
					." VALUES "
					." (:Fecha, :Hora, :Llamante, :Cola, :IVR, :Espera, '00:00:00', :Pos, :Desconexion, NULL, :Intentos, :Asterisk_ID,NULL,NULL,:Duracion_IVR,:Ruta_IVR,:DNIS,:Hora_fin,:Posicion,0,NULL)";
		$queryUpdate="UPDATE t_Answered_Calls SET "
					."Fecha= :Fecha , "
					."Hora= :Hora , "
					."Llamante= :Llamante , "
					."Cola= :Cola , "
					."IVR= :IVR , "
					."Espera= :Espera , "
					."Duracion= '00:00:00' , "
					."Pos= :Pos , "
					."Desconexion= :Desconexion , "
					."asesor= NULL, "
					."Intentos= :Intentos , "
					."MOH__events= NULL , "
					."MOH_duration= NULL , "
					."IVR_duration= :Duracion_IVR , "
					."IVR_path= :Ruta_IVR , "
					."DNIS= :DNIS , "
					."Hora_fin= :Hora_fin , "
					."Pos_salida= :Posicion , "
					."Answered= 0 , "
					."Duracion_Real= NULL "
					."WHERE AsteriskID= :Asterisk_ID ";
		break;
}

$insert = $pdodb->prepare($queryInsert);
$update = $pdodb->prepare($queryUpdate);
ob_start();



?>
<style>
	.load{
		float: left;
		background: green;
		margin: 0;
		padding: 0;
		height: 26px;
		width: <?php echo 100/count($data); ?>%;
	}
</style>
<br>
<br>
<p style='width: 80%; height: 30px; background: white; margin: auto; padding: 0;'>Uploading... Tipo de llamadas: <b><?php echo $tipo; ?></b> // No. de registros: <b><?php echo count($data);?></b></p>
<div style='width: 80%; height: 30px; background: white; border: solid 2px black; margin: auto; padding: 0;'>
	<?php
		$inserted=0;
		$updated =0;
		$errors=0;
		$lasterror=[];

		foreach($data as $index => $fila){

			if($fila[':Llamante']!='*'){

				if($insert->execute($fila)){
					$inserted++;

				}else{
					$error=$insert->errorInfo();
					if($error[1]!=1062){
						$errors++;
						$lasterror[]="insert $index: 1->".$error[0]." 2-> ".$error[2];
					}else{
						if($update->execute($fila)){
							$updated++;
						}else{
							$errors++;
							$error=$update->errorInfo();
							$lasterror[]="update $index: 1->".$error[0]." 2-> ".$error[2];
						}
						$update->closeCursor();//optional
					}

				}
			    $insert->closeCursor();//optional
			}

		    flush();
			ob_flush();
			echo "<div class='load'></div>\n";
		}
	?>
</div>
<br>

<?php
echo "<p style='width: 80%; height: 30px; background: white; margin: auto; padding: 0;'>Inserted: $inserted // Updated: $updated // Errores: $errors</p>";
echo "<br>";

foreach($lasterror as $index => $info){
	echo "<p>$info</p>";
}



exit;

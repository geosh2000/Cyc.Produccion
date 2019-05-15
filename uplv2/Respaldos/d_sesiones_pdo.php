<?php
include("../connectPDO.php");
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set('America/Mexico_City');

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

$getdata = strtr(utf8_encode($_POST['Array']), $normalizeChars);

$filas=explode('|',$getdata);

foreach($filas as $index => $info){
	if($index==0){
		$tipo=$info;
	}else{
		$data[]=explode(',',$info);	
	}
	
}

function addTime($fechaB, $timeB, $fechaA, $timeA) {
    
    $timeAinSeconds = intval(date('H', strtotime($timeA)))*60*60 + intval(date('i', strtotime($timeA)))*60 + intval(date('s', strtotime($timeA)));
    $timeBinSeconds = intval(date('H', strtotime($timeB)))*60*60 + intval(date('i', strtotime($timeB)))*60 + intval(date('s', strtotime($timeB)));
    
    if(date("Y-m-d",strtotime($fechaA))>date("Y-m-d",strtotime($fechaB))){
    	$timeAinSeconds=$timeAinSeconds+(24*60*60);
    }

    $timeABinSeconds = $timeAinSeconds - $timeBinSeconds;

    $timeABsec = $timeABinSeconds % 60;
    $timeABmin = (($timeABinSeconds - $timeABsec) / 60) % 60;
    $timeABh = ($timeABinSeconds - $timeABsec - $timeABmin*60) / 60 / 60;

    return str_pad((int) $timeABh,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABmin,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABsec,2,"0",STR_PAD_LEFT);
}

foreach($data as $index => $fila){
	if($index!=0){
		foreach($fila as $row => $info){
			if($info=="" || $info==NULL){
				$info_ok=NULL;
			}else{
				$info_ok=$info;
			}
			
			if(isset($data[0][$row])){
				$flag=$data[0][$row];
			}else{
				$flag='nothing';
			}
			
			switch($flag){
				case 'id':
					$datos[$index][':asesor']=$info_ok;
					$datos_ud[$index][':asesor']=$info_ok;
					break;
				case 'Start hour':
					$tmp_inicio=$info_ok;
					break;
				case 'End hour':
					$tmp_fin=$info_ok;
					break;
				case 'skill':
					$datos[$index][':skill']=$info_ok;
					$datos_ud[$index][':skill']=$info_ok;
					break;
				case 'year':
					$datos[$index][':fi']=$info_ok."/".substr($tmp_inicio,0,5);
					$datos[$index][':hi']=substr($tmp_inicio,-8);
					$datos[$index][':fe']=$info_ok."/".substr($tmp_fin,0,5);
					$datos[$index][':he']=substr($tmp_fin,-8);
					$datos[$index][':duracion']=addTime($datos[$index][':fi'],$datos[$index][':hi'],$datos[$index][':fe'],$datos[$index][':he']);
					$datos_ud[$index][':fi']=$info_ok."/".substr($tmp_inicio,0,5);
					$datos_ud[$index][':hi']=substr($tmp_inicio,-8);
					$datos_ud[$index][':fe']=$info_ok."/".substr($tmp_fin,0,5);
					$datos_ud[$index][':he']=substr($tmp_fin,-8);
					$datos_ud[$index][':duracion']=addTime($datos[$index][':fi'],$datos[$index][':hi'],$datos[$index][':fe'],$datos[$index][':he']);
					break;
				default:
					break;
			}
		}
	}
}
unset($index,$fila,$row);

switch($tipo){
	case 1:
		$insert = $pdodb->prepare("INSERT INTO t_Sesiones (asesor, Fecha_in, Hora_in, Fecha_out, Hora_out, Skill, Duracion) VALUES (:asesor,:fi,:hi,:fe,:he,:skill,:duracion)");
		$update = $pdodb->prepare("UPDATE t_Sesiones SET Fecha_out= :fe, Hora_out= :he, Duracion= :duracion WHERE asesor= :asesor AND Fecha_in= :fi AND Hora_in= :hi AND Skill= :skill");
		break;
	case 2:
		$insert = $pdodb->prepare("INSERT INTO Sesiones (asesor, Fecha, Hora, Fecha_out, Hora_out, Skill, Duracion) VALUES (:asesor,:fi,:hi,:fe,:he,:skill,:duracion)");
		$update = $pdodb->prepare("UPDATE Sesiones SET Fecha_out= :fe, Hora_out= :he, Duracion= :duracion WHERE asesor= :asesor AND Fecha= :fi AND Hora= :hi AND Skill= :skill");
		break;
}
 

$start=date('H:i:s');

$inserted=0;
$updated =0;
$errors=0;
$lasterror=[];

foreach($datos as $index => $fila){
	if($insert->execute($fila)){
		$inserted++;
		
	}else{
		$error=$insert->errorInfo();
		if($error[1]!=1062){
			$errors++;
			$lasterror[]="insert: 1->".$error[0]." 2-> ".$error[2];		
		}else{
			if($update->execute($datos_ud[$index])){
				$updated++;
			}else{
				$errors++;
				$error=$update->errorInfo();
				$lasterror[]="update: 1->".$error[0]." 2-> ".$error[2];	
			}
			$update->closeCursor();//optional
		}
		
	}
    $insert->closeCursor();//optional
$end=date('H:i:s');
}


echo "Inserted: $inserted // Existing: $updated // Errores: $errors // Inicio: $start Fin: $end";
echo "<pre>";
print_r($lasterror);

echo "</pre>";
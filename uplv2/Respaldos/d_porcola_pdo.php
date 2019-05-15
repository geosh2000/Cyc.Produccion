<?php
include_once("../modules/modules.php");
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set('America/Mexico_City');

$pdodb=Connection::pdoDB('CC');
$connectdb=Connection::mysqliDB('CC');

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
				case 'Calls':
					$datos[$index][':calls']=$info_ok;
					$datos_ud[$index][':calls']=$info_ok;
					break;
				case 'TCTime':
					$hora=intval($info_ok*24).":".intval((($info_ok*24)-intval($info_ok*24))*60).":".round(((($info_ok*24)-intval($info_ok*24))*60-intval((($info_ok*24)-intval($info_ok*24))*60))*60,0,PHP_ROUND_HALF_UP);
					$datos[$index][':ttc']=date('H:i:s', strtotime($hora));
					$datos_ud[$index][':ttc']=$info_ok;
					unset($hora);
					break;
				case 'ACTime':
					$hora=intval($info_ok*24).":".intval((($info_ok*24)-intval($info_ok*24))*60).":".round(((($info_ok*24)-intval($info_ok*24))*60-intval((($info_ok*24)-intval($info_ok*24))*60))*60,0,PHP_ROUND_HALF_UP);
					$datos[$index][':atc']=date('H:i:s', strtotime($hora));
					$datos_ud[$index][':atc']=$info_ok;
					unset($hora);
					break;
				case 'AWTime':
					$hora=intval($info_ok*24).":".intval((($info_ok*24)-intval($info_ok*24))*60).":".round(((($info_ok*24)-intval($info_ok*24))*60-intval((($info_ok*24)-intval($info_ok*24))*60))*60,0,PHP_ROUND_HALF_UP);
					$datos[$index][':awt']=date('H:i:s', strtotime($hora));
					$datos_ud[$index][':awt']=$info_ok;
					unset($hora);
					break;
				case 'skill':
					$datos[$index][':skill']=$info_ok;
					$datos_ud[$index][':skill']=$info_ok;
					break;
				case 'fecha':
					$datos[$index][':fecha']=date('Y-m-d',strtotime($info_ok));
					$datos_ud[$index][':fecha']=date('Y-m-d',strtotime($info_ok));
					break;
				default:
					break;
			}
		}
	}
}
unset($index,$fila,$row);

if(date('H:i:s')<date('H:i:s',strtotime('00:30:00'))){
  $query="DELETE FROM d_PorCola WHERE Fecha='".date('Y-m-d')."'";
  $result=$connectdb->query($query);
}

switch($tipo){
	case 1:
		$insert = $pdodb->prepare("INSERT INTO d_PorCola (asesor, Calls, Total_Time_Calls, Avg_Time_Calls, Avg_Wait_Time, Skill, Fecha) VALUES (:asesor, :calls, :ttc, :atc, :awt, :skill, :fecha)");
		$update = $pdodb->prepare("UPDATE d_PorCola SET Calls= :calls, Total_Time_Calls= :ttc, Avg_Time_Calls= :atc, Avg_Wait_Time= :awt WHERE asesor= :asesor AND Fecha= :fecha AND Skill= :skill");
		break;
	case 2:
		$insert = $pdodb->prepare("INSERT INTO d_PorCola (asesor, Calls, Total_Time_Calls, Avg_Time_Calls, Avg_Wait_Time, Skill, Fecha) VALUES (:asesor, :calls, :ttc, :atc, :awt, :skill, :fecha)");
		$update = $pdodb->prepare("UPDATE d_PorCola SET Calls= :calls, Total_Time_Calls= :ttc, Avg_Time_Calls= :atc, Avg_Wait_Time= :awt WHERE asesor= :asesor AND Fecha= :fecha AND Skill= :skill");
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

$connectdb->close();
$pdodb=NULL;
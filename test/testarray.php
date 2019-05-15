<?php
include("../connectDB.php");

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
	$data[$index]=explode(',',$info);
}


foreach($data as $index => $fila){
	foreach($fila as $row => $info){
		if($info=="" || $info==NULL){
			$info_ok=NULL;
		}else{
			$info_ok=$info;
		}
		switch($row){
			case 1:
				$datos[$index][':loc']=$info_ok;
				break;
			case 2:
				$datos[$index][':chan']=$info_ok;
				break;
			case 3:
				$datos[$index][':afi']=$info_ok;
				break;
			case 5:
				$datos[$index][':status']=$info_ok;
				break;
			case 6:
				$datos[$index][':asesor']=$info_ok;
				break;
			case 7:
				$datos[$index][':type']=$info_ok;
				break;
			case 9:
				$datos[$index][':balance']=$info_ok;
				break;
			case 10:
				$datos[$index][':total']=$info_ok;
				break;
			case 4:
				if($info_ok==NULL){
					$datos[$index][':fecha']=NULL;
				}else{
					$datos[$index][':fecha']=date('Y-m-d H:i:s',strtotime($info_ok));
				}
				break;
			case 8:
				if($info_ok==NULL){
					$datos[$index][':exp']=NULL;
				}else{
					$datos[$index][':exp']=date('Y-m-d H:i:s',strtotime($info_ok));
				}
				break;
			default:
				break;
		}
	}
}
unset($index,$fila,$row);

$stmt = $pdodb->prepare("INSERT INTO us_basereservas (Localizador, channelId, Afiliado, Fecha, ObStatus, Asesor, LocType, Expiracion, AmountBalance, AmountTotal) VALUES (:loc,:chan,:afi,:fecha,:status,:asesor,:type,:exp,:balance,:total)");

foreach($datos as $index => $fila){
	$stmt->execute($fila);
    $stmt->closeCursor();//optional
}

 
 /*foreach($datos as $index => $fila){
	$query="INSERT INTO us_basereservas (Localizador, channelId, Afiliado, Fecha, ObStatus, Asesor, LocType, Expiracion, AmountBalance, AmountTotal) VALUES (";
	foreach($fila as $row => $info){
		$query.="$info,";
	}
	$query=substr($query, 0, -1).")";
	if($result=$connectdb->query($query)){
		$regsuploaded++;
	}else{
		if($connectdb->errno==1062){
			$regsexists++;
		}else{
			$regserror++;
		}
	}
			
}
*/

echo "<pre>Uploaded: $regsuploaded // Already Exists: $regsexists // Error: $regserror</pre>";


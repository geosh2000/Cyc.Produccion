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

$getdata = strtr($_POST['Array'], $normalizeChars);

$filas=explode('|',$getdata);

foreach($filas as $index => $info){
	if($index==0){
		$tipo=$info;
	}else{
		$data[]=explode(',',$info);	
	}
	
}


foreach($data as $index => $fila){
	if($index!=0){
		foreach($fila as $row => $info){
			if($info=="" || $info==NULL){
				$info_ok=NULL;
			}else{
				$info_ok=$info;
			}
			switch($row){
				case 0:
					$datos[$index][':asesor']=$info_ok;
					$datos_ud[$index][':asesor']=$info_ok;
					break;
				case 1:
					$datos[$index][':localizador']=$info_ok;
					$datos_ud[$index][':localizador']=$info_ok;
					break;
				case 3:
					$datos[$index][':afiliado']=$info_ok;
					break;
				case 4:
					$date=explode('/',substr($info_ok,0,10));
					$date_ok=$date[2]."-".$date[1]."-".$date[0];
					$datos[$index][':fecha']=$date_ok;
					$datos_ud[$index][':fecha']=$info_ok;
					unset($date,$date_ok,$hour);
					if(strpos($info_ok,'/')>0){
						$date=explode('/',substr($info_ok,0,10));
						$date_ok=$date[2]."-".$date[1]."-".$date[0];
						$datos[$index][':fecha']=$date_ok;
						$datos_ud[$index][':fecha']=$info_ok;
						unset($date,$date_ok,$hour);
					}else{
						$date= ($info_ok - 25569) * 86400;
						$datos[$index][':fecha']=gmdate("Y-m-d", $date);
						$datos_ud[$index][':fecha']=gmdate("Y-m-d", $date);
					}
					break;
				case 5:
					$hora=intval($info_ok*24).":".intval((($info_ok*24)-intval($info_ok*24))*60).":".round(((($info_ok*24)-intval($info_ok*24))*60-intval((($info_ok*24)-intval($info_ok*24))*60))*60,0,PHP_ROUND_HALF_UP);
					$datos[$index][':hora']=date('H:i:s', strtotime($hora));
					$datos_ud[$index][':hora']=date('H:i:s', strtotime($hora));
					unset($hora);
					break;
				case 6:
					$datos[$index][':servicios']=$info_ok;
					break;
				case 7:
					$datos[$index][':venta']=$info_ok;
					$datos_ud[$index][':venta']=$info_ok;
					break;
				case 8:
					$datos[$index][':oi']=$info_ok;
					break;
				case 9:
					$datos[$index][':egresos']=$info_ok;
					break;
				case 10:
					$datos[$index][':currency']=$info_ok;
					break;
				case 11:
					$datos[$index][':branchid']=$info_ok;
					break;
				case 12:
					//$datos[$index][':stand']=$info_ok;
					break;
				case 13:
					$datos[$index][':ventamxn']=$info_ok;
					break;
				case 14:
					$datos[$index][':oimxn']=$info_ok;
					break;
				case 15:
					$datos[$index][':egresosmxn']=$info_ok;
					break;
				case 16:
					$datos[$index][':chanid']=$info_ok;
					$datos_ud[$index][':chanid']=$info_ok;
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
		$db="t_Locs";
		break;
	case 2:
		$db="d_Locs";
		break;
}
 

$insert = $pdodb->prepare("INSERT INTO $db (asesor, Localizador, Afiliado, Servicios, Venta, OtrosIngresos, Egresos, MonedaBase, branchid, VentaMXN, OtrosIngresosMXN, EgresosMXN, Fecha, Hora, chanId) VALUES (:asesor,:localizador,:afiliado,:servicios,:venta,:oi,:egresos,:currency,:branchid,:ventamxn,:oimxn,:egresosmxn,:fecha,:hora, :chanid)");
$update = $pdodb->prepare("UPDATE $db SET asesor= :asesor, chanId= :chanid WHERE Localizador= :localizador AND Fecha= :fecha AND Hora= :hora AND Venta= :venta");

$start=date('H:i:s');

$inserted=0;
$updated =0;
$errors=0;
$lasterror=[];

foreach($datos as $index => $fila){
	if($insert->execute($fila)){
		$inserted++;
		
	}else{
		if($update->execute($datos_ud[$index])){
			$updated++;
		}else{
			$errors++;
			$lasterror[]=$update->errorInfo();	
		}
		$update->closeCursor();//optional
	}
    $insert->closeCursor();//optional
$end=date('H:i:s');
}

$pdodb=null;

echo "Inserted: $inserted // Updated: $updated // Errores: $errors // Inicio: $start Fin: $end";

echo "TIPO: $tipo";
echo "<pre>";
print_r($lasterror);

echo "</pre>";


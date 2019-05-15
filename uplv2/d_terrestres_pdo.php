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
				case 'ItemLocator':
				case 'Locator':
					$datos[$index][':localizador']=substr($info_ok, 0, strpos($info_ok,'-'));
					$datos[$index][':item']=substr($info_ok, strpos($info_ok,'-')+1,3);
					break;
				case 'Fecha de Venta':
				case 'Fecha de venta':
					if($info_ok!=NULL){
						if(strpos($info_ok,'.')>0){
							$datos[$index][':fechaT']=str_replace('.','-',$info_ok);
						}else{
							$date= ($info_ok - 25569) * 86400;
							$datos[$index][':fechaT']=gmdate("Y-m-d", $date);
						}
					}else{
						$datos[$index][':fechaT']=NULL;
					}
					unset($date,$date_ok,$hour);
					break;
				case 'fecha de servicio':
				case 'Fecha de servicio':
					if($info_ok!=NULL){
						$date= ($info_ok - 25569) * 86400;
						$datos[$index][':fechaS']=gmdate("Y-m-d", $date);
					}else{
						$datos[$index][':fechaS']=NULL;
					}
					unset($date,$date_ok,$hour);
					break;
				case 'Tarifa Venta':
				case 'Tarifa de venta (precio)':
					$datos[$index][':venta']=$info_ok;
					break;
				case 'pax':
				case 'Pax':
					$datos[$index][':pax']=$info_ok;
					break;
				case 'noches':
				case 'Noches':
					if($info_ok==""){
						$datos[$index][':noches']=0;
					}else{
						$datos[$index][':noches']=$info_ok;
					}
					break;
				case 'destino':
					if($info_ok==""){
						$datos[$index][':destino']=null;
					}else{
						$datos[$index][':destino']=utf8_encode($info_ok);
					}
					break;
				case 'tipo de servicio':
					switch($info_ok){
						case 'tour':
							$datos[$index][':servicio']=1;
							break;
						case 'traslado':
							$datos[$index][':servicio']=2;
							break;
						case 'circuito':
						case 'circuitos':
							$datos[$index][':servicio']=3;
							break;
						case 'crucero':
							$datos[$index][':servicio']=4;
							break;	
					}
					break;
				case 'proveedor':
				case 'Proveedor':
					$datos[$index][':proveedor']=utf8_encode($info_ok);
					break;
				case 'Nombre de Barco':
				case 'nombre de la actividad':
				case 'Nombre del circuito':
					$datos[$index][':nombre_serv']=utf8_encode($info_ok);
					
					if($data[0][$row]=='Nombre del circuito'){
						$datos[$index][':destino']=null;
						$datos[$index][':servicio']=3;
					}
					
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
		$db="t_terrestres";
		break;
	case 2:
		$db="t_terrestres";
		break;
}
 

$insert = $pdodb->prepare("INSERT INTO $db (localizador, item, fecha_transaccion, fecha_servicio, venta, servicio, pax, noches, destino, nombre_servicio, proveedor) VALUES (:localizador,:item,:fechaT,:fechaS,:venta,:servicio,:pax,:noches,:destino,:nombre_serv,:proveedor)");
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
			$lasterror[]=$error[2];	
		}else{
			$updated++;
			$lasterror[]=$error[2];	
		}
	}
    $insert->closeCursor();//optional
$end=date('H:i:s');
}


echo "Inserted: $inserted // Existing: $updated // Errores: $errors // Inicio: $start Fin: $end";
echo "<pre>";
print_r($lasterror);

echo "</pre>";


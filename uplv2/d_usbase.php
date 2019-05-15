<?php
include("../modules/modules.php");

$pdodb=Connection::pdoDB('CC');
timeAndRegion::setRegion('Mex');

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

$getdata = utf8_decode($_POST['Array']);

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
			case 11:
				$datos[$index][':ncompleto']=ucwords(strtolower($info_ok));
				break;
			case 12:
				$datos[$index][':nombre']=ucwords(strtolower($info_ok));
				break;
			case 13:
				$datos[$index][':apellido']=ucwords(strtolower($info_ok));
				break;
			case 14:
				$datos[$index][':mail']=strtolower($info_ok);
				break;
			case 15:
				$datos[$index][':tel1']=preg_replace("/[^0-9,.]/", "",$info_ok);
				break;
			case 16:
				$datos[$index][':tel2']=preg_replace("/[^0-9,.]/", "",$info_ok);
				break;
			case 17:
				$datos[$index][':serv']=$info_ok;
				break;
			case 4:
				if($info_ok==NULL){
					$datos[$index][':fecha']=NULL;
				}else{
					$date=explode('/',substr($info_ok,0,10));
					$hour=date('H:i:s',strtotime(str_replace('a.m.', 'AM', str_replace('p.m.', 'PM', substr($info_ok, 11,100)))));
					$date_ok=$date[2]."-".$date[1]."-".$date[0]." ".$hour;
					$datos[$index][':fecha']=$date_ok;
					unset($date,$date_ok,$hour);
				}
				break;
			case 8:
				if($info_ok==NULL){
					$datos[$index][':exp']=NULL;
				}else{
					$date=explode('/',substr($info_ok,0,10));
					$hour=date('H:i:s',strtotime(str_replace('a.m.', 'AM', str_replace('p.m.', 'PM', substr($info_ok, 11,100)))));
					$date_ok=$date[2]."-".$date[1]."-".$date[0]." ".$hour;
					$datos[$index][':exp']=$date_ok;
					unset($date,$date_ok,$hour);
				}
				break;
			default:
				break;
		}
	}
}
unset($index,$fila,$row);

$insert = $pdodb->prepare("INSERT INTO us_basereservas (Localizador, channelId, Afiliado, Fecha, ObStatus, Asesor, LocType, Expiracion, AmountBalance, AmountTotal, NombreCompleto, Nombre, Apellido, correo, tel1, tel2, Servicios) VALUES (:loc,:chan,:afi,:fecha,:status,:asesor,:type,:exp,:balance,:total,:ncompleto,:nombre,:apellido,:mail,:tel1,:tel2, :serv)");
$update = $pdodb->prepare("UPDATE us_basereservas SET channelId=:chan , Afiliado=:afi , Fecha=:fecha , ObStatus=:status, Asesor=:asesor , LocType=:type , Expiracion=:exp , AmountBalance=:balance , AmountTotal=:total, NombreCompleto=:ncompleto, Nombre=:nombre, Apellido=:apellido, correo=:mail, tel1=:tel1, tel2=:tel2, Servicios= :serv WHERE Localizador=:loc");

$start=date('H:i:s');

foreach($datos as $index => $fila){
	if($insert->execute($fila)){
		$inserted++;

	}else{
		if($update->execute($fila)){
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

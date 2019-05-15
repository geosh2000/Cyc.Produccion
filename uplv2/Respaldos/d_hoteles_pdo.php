<?php
include("../modules/modules.php");

header('Content-Type: text/html; charset=UTF-8');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');
$pdodb=Connection::pdoDB('CC');

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

$query="SELECT id, Nombre FROM Asesores";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$asesor[strtr(utf8_encode($fila['Nombre']), $normalizeChars)]=$fila['id'];
	}
}





//$getdata = strtr($_POST['Array'], $normalizeChars);
$getdata = $_POST['Array'];

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
				case 2:
          $pos=strpos($info_ok,'-');
					$datos[$index][':localizador']=utf8_decode(substr($info_ok,0,$pos));
					$datos_ud[$index][':localizador']=utf8_decode(substr($info_ok,0,$pos));
          $datos[$index][':item']=utf8_decode(substr($info_ok,$pos+1,10));
          $datos_ud[$index][':item']=utf8_decode(substr($info_ok,$pos+1,10));
					break;
				case 3:
          $datos[$index][':itemType']=utf8_decode($info_ok);
          break;
                case 6:
					$date=explode('/',substr(utf8_decode($info_ok),0,10));
					$date_ok=$date[2]."-".$date[1]."-".$date[0];
					$datos[$index][':fecha']=$date_ok;
					$datos_ud[$index][':fecha']=$date_ok;
					unset($date,$date_ok,$hour);
					if(strpos(utf8_decode($info_ok),'/')>0){
						$date=explode('/',substr(utf8_decode($info_ok),0,10));
						$date_ok=$date[2]."-".$date[1]."-".$date[0];
						$datos[$index][':fecha']=$date_ok;
						$datos_ud[$index][':fecha']=$date_ok;
						unset($date,$date_ok,$hour);
					}else{
						$date= (utf8_decode($info_ok) - 25569) * 86400;
						$datos[$index][':fecha']=gmdate("Y-m-d", $date);
						$datos_ud[$index][':fecha']=gmdate("Y-m-d", $date);
					}
					break;
				case 7:
					$hora=intval(utf8_decode($info_ok)*24).":".intval(((utf8_decode($info_ok)*24)-intval(utf8_decode($info_ok)*24))*60).":".round((((utf8_decode($info_ok)*24)-intval(utf8_decode($info_ok)*24))*60-intval(((utf8_decode($info_ok)*24)-intval(utf8_decode($info_ok)*24))*60))*60,0,PHP_ROUND_HALF_UP);
					$datos[$index][':hora']=date('H:i:s', strtotime($hora));
					$datos_ud[$index][':hora']=date('H:i:s', strtotime($hora));
					unset($hora);
					break;
				case 8:
					$datos[$index][':venta']=utf8_decode($info_ok);
					$datos_ud[$index][':venta']=utf8_decode($info_ok);
					break;
				case 9:
					$datos[$index][':oi']=utf8_decode($info_ok);
					break;
				case 10:
					$datos[$index][':egresos']=utf8_decode($info_ok);
					break;
				case 12:
					$datos[$index][':branchid']=utf8_decode($info_ok);
					break;
				case 14:
					$datos[$index][':ventamxn']=utf8_decode($info_ok);
					break;
				case 15:
					$datos[$index][':oimxn']=utf8_decode($info_ok);
					break;
				case 16:
					$datos[$index][':egresosmxn']=utf8_decode($info_ok);
					break;
				case 17:
					$datos[$index][':chanid']=utf8_decode($info_ok);
					break;
				case 18:
					$datos[$index][':tipo']=utf8_decode($info_ok);
					$datos_ud[$index][':tipo']=utf8_decode($info_ok);
					break;
        case 19:
					$datos[$index][':hotel']=utf8_decode($info_ok);
          break;
        case 20:
					$datos[$index][':corporativo']=utf8_decode($info_ok);
          break;
        case 21:
					$datos[$index][':destination']=utf8_decode($info_ok);
          break;
        case 22:
					$date=explode('/',substr(utf8_decode($info_ok),0,10));
					$date_ok=$date[2]."-".$date[1]."-".$date[0];
					$datos[$index][':checkOut']=$date_ok;
					$datos_ud[$index][':checkOut']=$date_ok;
					unset($date,$date_ok,$hour);
					if(strpos(utf8_decode($info_ok),'/')>0){
						$date=explode('/',substr(utf8_decode($info_ok),0,10));
						$date_ok=$date[2]."-".$date[1]."-".$date[0];
						$datos[$index][':checkOut']=$date_ok;
						$datos_ud[$index][':checkOut']=$date_ok;
						unset($date,$date_ok,$hour);
					}else{
						$date= (utf8_decode($info_ok) - 25569) * 86400;
						$datos[$index][':checkOut']=gmdate("Y-m-d", $date);
						$datos_ud[$index][':checkOut']=gmdate("Y-m-d", $date);
					}
					break;
        case 23:
            $date=explode('/',substr(utf8_decode($info_ok),0,10));
            $date_ok=$date[2]."-".$date[1]."-".$date[0];
            $datos[$index][':checkIn']=$date_ok;
            $datos_ud[$index][':checkIn']=$date_ok;
            unset($date,$date_ok,$hour);
            if(strpos(utf8_decode($info_ok),'/')>0){
                $date=explode('/',substr(utf8_decode($info_ok),0,10));
                $date_ok=$date[2]."-".$date[1]."-".$date[0];
                $datos[$index][':checkIn']=$date_ok;
                $datos_ud[$index][':checkIn']=$date_ok;
                unset($date,$date_ok,$hour);
            }else{
                $date= (utf8_decode($info_ok) - 25569) * 86400;
                $datos[$index][':checkIn']=gmdate("Y-m-d", $date);
                $datos_ud[$index][':checkIn']=gmdate("Y-m-d", $date);
            }
            break;
        case 24:
            $datos[$index][':adults']=utf8_decode($info_ok);
            $datos_ud[$index][':adults']=utf8_decode($info_ok);
            break;
        case 25:
            $datos[$index][':juniors']=utf8_decode($info_ok);
            $datos_ud[$index][':juniors']=utf8_decode($info_ok);
            break;
        case 26:
            $datos[$index][':kidsPay']=utf8_decode($info_ok);
            $datos_ud[$index][':kidsPay']=utf8_decode($info_ok);
            break;
        case 27:
            $datos[$index][':tercero']=utf8_decode($info_ok);
            break;
        case 28:
            $datos[$index][':tipoContrato']=utf8_decode($info_ok);
            break;
        case 29:
            $datos[$index][':RN']=utf8_decode($info_ok);
            $datos_ud[$index][':RN']=utf8_decode($info_ok);
            break;
        case 30:
            $datos[$index][':categoryId']=utf8_decode($info_ok);
            break;
        case 31:
            $datos[$index][':itemLocatorIdParent']=utf8_decode($info_ok);
            break;
        case 32:
            $datos[$index][':clientNights']=utf8_decode($info_ok);
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
		$db="t_hoteles";
		break;
	case 2:
		$db="d_hoteles";
		break;
}


$insert = $pdodb->prepare("INSERT INTO $db (
    Localizador, 
    item, 
    Venta,
    OtrosIngresos,
    Egresos,
    branchid,
    VentaMXN,
    OtrosIngresosMXN,
    EgresosMXN,
    Fecha,
    Hora,
    chanId,
    tipo,
    Hotel,
    Corporativo,
    Destination,
    checkIn,
    checkOut,
    adults,
    juniors,
    kidsPay,
    RN,
    tercero,
    tipoContrato,
    itemType,
    categoryId,
    itemLocatorIdParent,
    clientNights) VALUES 
    (
        :localizador,
        :item,
        :venta,
        :oi,
        :egresos,
        :branchid,
        :ventamxn,
        :oimxn,
        :egresosmxn,
        :fecha,
        :hora,
        :chanid, 
        :tipo,
        :hotel,
        :corporativo,
        :destination,
        :checkIn,
        :checkOut,
        :adults,
        :juniors,
        :kidsPay,
        :RN,
        :tercero,
        :tipoContrato,
        :itemType,
        :categoryId,
        :itemLocatorIdParent,
        :clientNights) 
            ON DUPLICATE KEY UPDATE 
        checkIn= :checkIn, 
        checkOut=:checkOut, 
        adults= :adults,
        juniors= :juniors,
        kidsPay= :kidsPay,
        RN= :RN, 
        tercero= :tercero,
        tipo= :tipo,
        categoryId= :categoryId,
        itemLocatorIdParent= :itemLocatorIdParent,
        clientNights= :clientNights");
$update = $pdodb->prepare("UPDATE $db SET checkIn= :checkIn, checkOut=:checkOut, adults= :adults, juniors= :juniors, kidsPay= :kidsPay, RN= :RN, tipo= :tipo WHERE Localizador= :localizador AND item= :item AND Fecha= CAST(:fecha as DATE) AND Hora= :hora AND Venta= :venta");

$start=date('H:i:s');

$inserted=0;
$updated =0;
$errors=0;
$lasterror=[];

//print_r($datos);

foreach($datos as $index => $fila){
	if($insert->execute($fila)){
		$inserted++;
        
	}else{
        $errors++;
        $lasterror[]=$insert->errorInfo();
		//echo "<br>insert $index: ";
		//print_r($insert->errorInfo());
		//if($update->execute($datos_ud[$index])){
			//echo "<br>UPDATE $db SET asesor= ".$datos_ud[$index][':asesor'].", chanId= ".$datos_ud[$index][':chanid'].", Nombre= ".$datos_ud[$index][':nombre'].", tipo= ".$datos_ud[$index][':tipo']." WHERE Localizador= ".$datos_ud[$index][':localizador']." AND Fecha= ".$datos[$index][':fecha']." AND Hora= ".$datos_ud[$index][':hora']." AND Venta= ".$datos_ud[$index][':venta'];
			//$updated++;
		//}else{
			//echo "<br>update $index: ";
			//print_r($update->errorInfo());
			//print_r($datos_ud);
		//	$errors++;
		//	$lasterror[]=$update->errorInfo();
		//}
		//$update->closeCursor();//optional
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

$pdoDB=NULL;
$connectdb->close();

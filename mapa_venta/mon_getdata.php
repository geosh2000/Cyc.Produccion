<?php

include_once("../modules/modules.php");
$connectDB=Connection::mysqliDB('CC');
timeAndRegion::setRegion("Mex");

//Color Gradient Array (60steps)
$gradient = array("FF7300","FF7702","FF7C04","FF8107","FF8509","FF8A0B","FF8F0E","FF9310","FF9812","FF9D15","FFA117","FFA61A","FFAB1C","FFAF1E","FFB421","FFB923","FFBD25","FFC228","FFC72A","FFCB2C","FFD02F","FFD531","FFD934","FFDE36","FFE338","FFE73B","FFEC3D","FFF13F","FFF542","FFFA44","FFFF47","F6FC44","EEFA42","E6F73F","DEF53D","D6F23A","CEF038","C6ED35","BEEB33","B6E830","AEE62E","A6E42C","9EE129","96DF27","8EDC24","85DA22","7DD71F","75D51D","6DD21A","65D018","5DCE16","55CB13","4DC911","45C60E","3DC40C","35C109","2DBF07","25BC04","1DBA02","15B800");



//Venta del dia

$query="SELECT a.id, a.NCorto, IF(b.monto IS NULL, 0, b.monto)+IF(c.monto IS NULL, 0, c.monto) as Monto
FROM 
	(SELECT id, `N Corto` as NCorto FROM Asesores WHERE `id Departamento` IN (3,35) AND Activo=1) a
LEFT JOIN
	(SELECT asesor, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as monto FROM d_Locs WHERE Fecha=CURDATE() AND (Afiliado LIKE '%pricetravel%' OR Afiliado LIKE '%price-travel%') GROUP BY asesor) b
ON a.id=b.asesor
LEFT JOIN
	(SELECT asesor, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as monto FROM t_Locs WHERE Fecha BETWEEN '2016-12-01' AND '".date('Y-m-d', strtotime('-1 days'))."' AND (Afiliado LIKE '%pricetravel%' OR Afiliado LIKE '%price-travel%') GROUP BY asesor) c
ON a.id=c.asesor
ORDER BY Monto DESC, NCorto";

IF($result=$connectDB->query($query)){
	while($fila=$result->fetch_assoc()){
		if($fila['Monto']>180000){
			$color="#15B800";
		}elseif($fila['Monto']>=0){
			$color="#".$gradient[intval($fila['Monto']/1000/3)];
		}else{
			$color="#ff0000";
		}
		
		$data['Series'][]=array("y"=>round($fila['Monto'],2,PHP_ROUND_HALF_DOWN),"color"=>$color);
		$data['Categories'][]=$fila['NCorto'];
	}
}

//Lasts

$query="SELECT a.id, Fecha, a.NCorto, IF(monto IS NULL, 0, monto) as Monto
FROM 
	(SELECT id, `N Corto` as NCorto FROM Asesores WHERE `id Departamento` IN (3,35) AND Activo=1) a
LEFT JOIN
	(SELECT Fecha, asesor, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as monto FROM t_Locs WHERE Fecha IN ('2016-11-28') AND (Afiliado LIKE '%pricetravel%' OR Afiliado LIKE '%price-travel%') GROUP BY Fecha, asesor) b
ON a.id=b.asesor
ORDER BY Monto DESC, NCorto";

IF($result=$connectDB->query($query)){
	while($fila=$result->fetch_assoc()){
		if($fila['Monto']>60000){
			$color="#15B800";
		}elseif($fila['Monto']>=0){
			$color="#".$gradient[intval($fila['Monto']/1000)];
		}else{
			$color="#ff0000";
		}
		
		$monday[$fila['NCorto']]=array("y"=>round($fila['Monto'],2,PHP_ROUND_HALF_DOWN),"color"=>'#d0d7e2');
	}
}

//LastUpdate

$query="SELECT MAX(Last_Update) as lu FROM d_Locs WHERE Fecha=CURDATE()";
IF($result=$connectDB->query($query)){
	while($fila=$result->fetch_assoc()){
		$mxtime=new DateTime($fila['lu'].' America/Mexico_City');
		$cunzone=new DateTimeZone('America/Bogota');
		$mxtime->setTimezone($cunzone);
		$data['lu']=$mxtime->format('Y-m-d H:i:s');
	}
}

foreach($data['Series'] as $index => $info){
	$data['Monday'][]=$monday[$data['Categories'][$index]];
}

print json_encode($data,JSON_PRETTY_PRINT);

/*echo "<pre>";
print_r($acum);
echo "</pre>";*/


 ?>


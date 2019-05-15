<?php
include("../connectDB.php");
header("Content-Type:  application/json;charset=utf-8");

//Build Info

$from=date('Y-m-d',strtotime($_GET['from']));
$to=date('Y-m-d',strtotime($_GET['to']));
$currency=$_GET['currency'];

switch($currency){
	case 'usd':
		$sumaMontoF="(SUM(Venta+OtrosIngresos+Egresos))";
		$sumaMonto="Venta+OtrosIngresos+Egresos";
		break;
	case 'mxn':
		$sumaMontoF="(SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN))";
		$sumaMonto="VentaMXN+OtrosIngresosMXN+EgresosMXN";
		break;
	case 'cop':
		$sumaMontoF="(SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN))*155.511041";
		$sumaMonto="VentaMXN+OtrosIngresosMXN+EgresosMXN";
		break;
}

$query="SELECT 
			montos.Fecha_ as Fecha, montos.Afiliado, Monto, Localizadores 
		FROM
			(SELECT 
				IF(Hora>='04:00:00',Fecha,ADDDATE(Fecha,-1)) as Fecha_ , Afiliado, CAST($sumaMontoF as DECIMAL(15,2)) as Monto 
			FROM 
				t_Locs 
			WHERE 
				((Fecha BETWEEN '$from' AND '$to') OR (Fecha=ADDDATE('$to',1) AND Hora<'04:00:00')) AND
				(Afiliado LIKE '%tiquetes%' OR Afiliado LIKE 'shop.pricetravel.co')
			GROUP BY Fecha_, Afiliado) montos
		LEFT JOIN
			(SELECT 
				Fecha_ as Fecha, Afiliado, COUNT(Distinct Localizador) as Localizadores
			FROM 
				(SELECT IF(Hora>='04:00:00',Fecha,ADDDATE(Fecha,-1)) as Fecha_ , Afiliado, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as monto, SUM(Venta) as Venta
					FROM t_Locs 
					WHERE 
						((Fecha BETWEEN '$from' AND '$to') OR (Fecha=ADDDATE('$to',1) AND Hora<'04:00:00')) AND 
						(Afiliado LIKE '%tiquetes%' OR Afiliado LIKE 'shop.pricetravel.co')
					GROUP BY
						Localizador
					HAVING
						monto>0) locs
			WHERE 
				Fecha_ BETWEEN '$from' AND '$to' AND 
				(Afiliado LIKE '%tiquetes%' OR Afiliado LIKE 'shop.pricetravel.co') AND
				Venta!=0
			GROUP BY Fecha, Afiliado) locs
		ON montos.Fecha_=locs.Fecha AND montos.Afiliado=locs.Afiliado
		HAVING Fecha BETWEEN '$from' AND '$to'";
	
	if ($result=$connectdb->query($query)) {
		$info_field_act=$result->fetch_fields();
	   while ($fila = $result->fetch_assoc()) {
			$data[$fila['Fecha']][$fila['Afiliado']]['Monto']=$fila['Monto'];
			$data[$fila['Fecha']][$fila['Afiliado']]['Localizador']=$fila['Localizadores'];
			$afiliados[$fila['Afiliado']]=1;
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
		
	//Add titles to tableheaders
	$dataheaders[]=utf8_encode("Fecha");
	foreach($afiliados as $afiliado => $info){
		if(!stristr($afiliado, 'shop')){	
			$dataheaders[]=utf8_encode(str_replace("_", " ", $afiliado));
		}
	}
	$dataheaders[]=utf8_encode("Total LTB");
	$dataheaders[]=utf8_encode("Shop");
	unset($info);
	
	//Format Rows
	$i=0;
	foreach($data as $fecha => $info){
		$datarow[$i][]=$fecha;
		
		$x=0;
	
		foreach($afiliados as $afiliado => $info2){
			if(!stristr($afiliado, 'shop')){
				if(!isset($data[$fecha][$afiliado]['Monto']) || $data[$fecha][$afiliado]['Monto']==NULL){
					$datarow[$i][]="$0";
				}else{
					$datarow[$i][]="$".number_format($data[$fecha][$afiliado]['Monto'],2);
				}

				if(!stristr($afiliado, 'shop')){
					if(!isset($totalLTB)){
						$totalLTB=$data[$fecha][$afiliado]['Monto'];
					}else{
						$totalLTB+=$data[$fecha][$afiliado]['Monto'];
					}
				}
				
				if(!isset($total[$x])){
					$total[$x]=$data[$fecha][$afiliado]['Monto'];
				}else{
					$total[$x]+=$data[$fecha][$afiliado]['Monto'];
				}
			}
			$x++;
		}
		$datarow[$i][]="$".number_format($totalLTB,2);
		$datarow[$i][]="$".number_format($data[$fecha]['shop.pricetravel.co']['Monto'],2);
		
		//Total LTB
		if(!isset($total[$x])){
			$total[$x]=$totalLTB;
		}else{
			$total[$x]+=$totalLTB;
		}
		$x++;
		
		//Total Shop
		if(!isset($total[$x])){
			$total[$x]=$data[$fecha]['shop.pricetravel.co']['Monto'];
		}else{
			$total[$x]+=$data[$fecha]['shop.pricetravel.co']['Monto'];
		}
		
		unset($info2,$totalLTB);
		$i++;
	}
	unset($info);
		
	unset($result);
	
	//<---------------------------------- FIN BO ---------------------------------->

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($datarow as $id =>$info){
	$row[]=$info;
}

//Create Footer
$footer[]=array("text"=>"Total");
foreach($total as $id =>$info){
	$footer[]=array("text"=>"$".number_format($info,2));
}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers),"footers"=>array($footer));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>



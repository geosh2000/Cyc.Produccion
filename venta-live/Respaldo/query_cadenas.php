<?php 

include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$query="SELECT
	CASE
		WHEN Canal='MP' THEN 
			CASE
				WHEN (dep IN (3,35,4,6,9) OR (dep=29 AND Afiliado NOT LIKE '%shop%') OR (dep=29 AND Afiliado LIKE '%shop%' AND tipo=2) OR (asesor>=0 AND dep IS NULL)) THEN 'MP-IN'
				WHEN dep IN (5) OR (dep=29 AND Afiliado LIKE '%shop%' AND tipo=1) THEN 'MP-OUT'
				WHEN dep=29 AND Afiliado LIKE '%shop%' AND tipo NOT IN (1,2) THEN 'PDV-Presencial'
				WHEN asesor=-1 THEN 'Online'
			END
		WHEN Canal='MT' THEN
			CASE
				WHEN dep NOT IN (7,8) THEN 'MT-IN'
				WHEN asesor=-1 THEN 'MT-Online'
			END
	END as Grupo,
	CASE
		WHEN Corporativo IN ('AM Resorts', 'Barcelo', 'RIU', 'Krystal', 'Emporio', 'GRUPO POSADAS', 'Mundo Imperial', 'Oasis', 'Las Brisas', 'Melia', 'Iberostar', 
		'Presidente Intercontinental', 'Marival', 'RCD HOTELS', 'Arriva' 'Playa Resorts', 'Solaris', 'Park Royal', 'Sandos', 'Posada Real', 'Palace', 'BlueBay', 
		'Bahia Principe', 'Fiesta Hotel Group', 'Pueblo Bonito', 'Karisma', 'Sunset') THEN Corporativo
		ELSE 'Otros'
	END as CorporativoOK,
	SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto,
	SUM(RNOK) as RN
FROM
(SELECT a.*, b.asesor, dep, d.Afiliado, Canal, Pais, IF(Venta!=0,RN,0) as RNOK
FROM (SELECT * FROM d_hoteles WHERE Fecha=CURDATE()) a
LEFT JOIN (SELECT asesor, Localizador FROM d_Locs WHERE Fecha=CURDATE() AND Servicios LIKE '%Hotel%') b ON a.Localizador=b.Localizador	
LEFT JOIN daily_dep c ON b.asesor=c.asesor
LEFT JOIN chanIds d ON a.chanId=d.id) a
GROUP BY
Grupo, CorporativoOK
HAVING Grupo IS NOT NULL
ORDER BY Last_Update";

if($result=$connectdb->query($query)){

  $datos['status']=1;

  $fields=$result->fetch_fields();
  while($fila=$result->fetch_assoc()){
    $data[$fila['Grupo']][$fila['CorporativoOK']]['Monto']=$fila['Monto'];
    @$total[$fila['Grupo']]['Monto']+=$fila['Monto'];
    
    $data[$fila['Grupo']][$fila['CorporativoOK']]['RN']=$fila['RN'];
    @$total[$fila['Grupo']]['RN']+=$fila['RN'];
    
    @$corps[$fila['CorporativoOK']]['Monto'][]=$fila['Monto'];
    @$corps[$fila['CorporativoOK']]['RN'][]=$fila['RN'];
    @$total['Total']['Monto']+=$fila['Monto'];
    @$total['Total']['RN']+=$fila['RN'];
    $datos['lu']=$fila['Last_Update'];
  }
  
  $query="SELECT MAX(Last_Update) as LU FROM d_hoteles";
  if($result=$connectdb->query($query)){
    $fila=$result->fetch_assoc();
    $datos['lu']=utf8_encode("Last Update: ".$fila['LU']);
  }
  
  ksort($corps);
  ksort($data);
  ksort($total);
  
  $tmp=$data['MT-IN'];
  unset($data['MT-IN']);
  $data['MT-IN']=$tmp;
  
  $tmp=$total['Total'];
  unset($total['Total']);
  $total['Total']=$tmp;

  $dataheaders[]='Corporativo';
  $dh2[]='Cadena';

  foreach($data as $grupo => $info){
    $dataheaders[]=$grupo;
    $dh2[]='Monto';
    $dh2[]='RN';
  }
  
  $dataheaders[]='Total';
  $dh2[]='Monto';
  $dh2[]='RN';

  foreach($dataheaders as $index => $info){
    if($info=='Corporativo'){
      $headers[]=array('text'=>$info, 'colspan'=>1);
    }else{
      $headers[]=array('text'=>$info, 'colspan'=>2);
    }
  }
  
  foreach($dh2 as $index => $info){
    $headers2[]=array('text'=>$info);
  }

  foreach($corps as $corporativo => $info){
    $rowInfo[]=array('text'=>utf8_encode($corporativo), 'class'=>'ts_left');
    foreach($data as $group => $info2){
      
      if(isset($data[$group][$corporativo])){
        $valor=$data[$group][$corporativo]['Monto'];
        $valorRN=$data[$group][$corporativo]['RN'];
      }else{
        $valor=0;
        $valorRN=0;
      }
      
      $rowInfo[]=array('text'=>utf8_encode("$".number_format($valor,2)), 'class'=>'ts_right');
      $rowInfo[]=array('text'=>utf8_encode(number_format($valorRN,0)), 'class'=>'ts_center');
    }
    $rowInfo[]=array('text'=>utf8_encode("$".number_format(array_sum($info['Monto']),2)), 'class'=>'ts_total');
    $rowInfo[]=array('text'=>utf8_encode(number_format(array_sum($info['RN']),0)), 'class'=>'ts_total_c');
    $row[]=$rowInfo;
    unset($rowInfo);
  }
  
  $footInfo[]=array('text'=>'Total');
  foreach($data as $group => $info2){
    
    if(isset($total[$group])){
      $Tvalor=$total[$group]['Monto'];
      $TvalorRN=$total[$group]['RN'];
    }else{
      $Tvalor=0;
      $TvalorRN=0;
    }
    
    $footInfo[]=utf8_encode("$".number_format($Tvalor,2));
    $footInfo[]=utf8_encode(number_format($TvalorRN,0));
  }
  $footInfo[]=utf8_encode("$".number_format($total['Total']['Monto'],2));
  $footInfo[]=utf8_encode(number_format($total['Total']['RN'],0));
  $foot=array('text'=>$footInfo);
  
}else{
  $datos['status']=0;
  $datos['msg']="ERROR! -> ".$connectdb->error." ON <br>$query";
}

$connectdb->close();

$datos['table']=array('headers'=>array($headers,$headers2), 'footers'=>$foot, 'rows'=>$row);

echo json_encode($datos,JSON_PRETTY_PRINT);

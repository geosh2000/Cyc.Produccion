<?php
include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

//Error Handler

function divError(){
 echo "";
}
set_error_handler("divError");

$pais=$_POST['pais'];

if(isset($_GET['pais'])){
  $pais=$_GET['pais'];
}

$localzone = new DateTime('now');
$localtime= $localzone->format('H:i:s');
$localdate= $localzone->format('Y-m-d');

$mxzone = new DateTimeZone('America/Mexico_City');
$localzone->setTimezone($mxzone);
$mxtime= $localzone->format('H:i:s');
$mxdate= $localzone->format('Y-m-d');

/*Afiliado Construct*/

$flag=false;
$query="SELECT * FROM monitor_kpiLive_Afiliados WHERE activo=1 AND CURDATE() BETWEEN inicio AND fin AND pais='$pais'";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $afi[$fila['id']]['title']=$fila['title'];
    $afi[$fila['id']]['channels']=$fila['channels'];
    $afi[$fila['id']]['nombre']=$fila['nombre'];
  }
}

$qu['chanid']="CASE ";

if(isset($afi)){
  $flag=true;
  foreach($afi as $id => $info){
    $qu['chanid'].="WHEN chanID IN (".$info['channels'].") THEN '".$info['title']."' ";
  }
}else{
  $flag=false;
}

$qu['chanid'].="ELSE 'Otros' "
              ."END as chanID";
              
if(!$flag){
  $qu['chanid']="2 as ChanId";
}
              
/* ----- END Afiliado Construct ----- */

$query="SELECT canales FROM PtChannels";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $ptChannels=$fila['canales'];
}
                


/*Canal Construct*/
$flag=false;

$qu['Canal']="CASE ";

$query="SELECT * FROM monitor_kpiLive_modules WHERE activo=1 AND pais='$pais' AND CURDATE() BETWEEN inicio AND fin";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $flag=true;
    $qu['Canal'].=utf8_encode(str_replace('$ptChannels',$ptChannels,$fila['query'])." ");
  }
}

$qu['Canal'].="ELSE 'Otro' END as Canal";

if(!$flag){
  $qu['Canal']="1 as Canal";
}

/* ----- END Canal Construct ----- */

$query="SELECT
      Canal,
      chanID,
      SUM(Monto) as Monto,
			SUM(Venta) as Venta,
			SUM(Xld) as Xld, 
			SUM(NewLocs) as Locs,
			SUM(PDVOUT) as PDVOUT, 
			SUM(PDVIN) as PDVIN, 
			SUM(PDVP) as PDVP, 
						SUM(PDVIN_Locs) as PDVIN_Locs,
						SUM(PDVOUT_Locs) as PDVOUT_Locs,
						SUM(PDVP_Locs) as PDVP_Locs
		FROM
			(
				SELECT
		     		a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
		     		SUM(VentaMXN+OtrosIngresosMXN) as Venta,
		     		SUM(EgresosMXN) as Xld, COUNT(DISTINCT NewLoc) as NewLocs,
		     		SUM(PDVOUT) as PDVOUT, SUM(PDVIN) as PDVIN, SUM(PDVP) as PDVP, COUNT(DISTINCT PDVIN_Locs) as PDVIN_Locs, COUNT(DISTINCT PDVOUT_Locs) as PDVOUT_Locs, COUNT(DISTINCT PDVP_Locs) as PDVP_Locs,
		     	   `id Departamento` as PCRC, `N Corto`, Dolar, ".$qu['chanid'].",
		     	   ".$qu['Canal']."
		     	FROM
		     		(SELECT a.*, dep as DepOK, IF(Venta!=0,Localizador,NULL) as NewLoc, 
              IF(Afiliado LIKE '%shop%' AND tipo=1,(VentaMXN+OtrosIngresosMXN+EgresosMXN),0) as PDVOUT,
              IF(Afiliado LIKE '%shop%' AND tipo=2,(VentaMXN+OtrosIngresosMXN+EgresosMXN),0) as PDVIN,
              IF(Afiliado LIKE '%shop%' AND tipo NOT IN (1,2),(VentaMXN+OtrosIngresosMXN+EgresosMXN),0) as PDVP,
              IF(Afiliado LIKE '%shop%' AND tipo=2 AND Venta!=0,Localizador,NULL) as PDVIN_Locs,
              IF(Afiliado LIKE '%shop%' AND tipo=1 AND Venta!=0,Localizador,NULL) as PDVOUT_Locs,
              IF(Afiliado LIKE '%shop%' AND tipo NOT IN (1,2) AND Venta!=0,Localizador,NULL) as PDVP_Locs
            FROM d_Locs a LEFT JOIN daily_dep b ON a.asesor=b.asesor WHERE Fecha=CURDATE()) a
		     	LEFT JOIN
		     		Asesores b
		     	ON
		     		a.asesor=b.id
		     	LEFT JOIN
		     		Fechas c
		     	ON
		     		a.Fecha=c.Fecha
		     	WHERE
		     		a.Fecha=CURDATE()
		     	GROUP BY
		     		a.Localizador
					  ) locs
		GROUP BY
			Canal, chanID";
//echo "query 105: <br>$query<br><br>";
if ($today_info=$connectdb->query($query)) {
   while ($fila = $today_info->fetch_assoc()) {
   		//Monto
   		$td[$fila['Canal']][$fila['chanID']]['Todo']['Td']['monto']=$fila['Monto'];
	    $td[$fila['Canal']][$fila['chanID']]['Todo']['Td']['venta']=$fila['Venta'];
		  $td[$fila['Canal']][$fila['chanID']]['Todo']['Td']['xld']=$fila['Xld'];
		  $td[$fila['Canal']][$fila['chanID']]['Todo']['Td']['loc']=$fila['Locs'];

      @$td[$fila['Canal']]['Total']['Todo']['Td']['loc']+=$fila['Locs'];
      @$td[$fila['Canal']]['Total']['Todo']['Td']['monto']+=$fila['Monto'];
      @$td[$fila['Canal']]['Total']['Todo']['Td']['venta']+=$fila['Venta'];
      @$td[$fila['Canal']]['Total']['Todo']['Td']['xld']+=$fila['Xld'];
      
      $td[$fila['Canal']][$fila['chanID']]['PDVIN']['Td']['Monto']=$fila['PDVIN'];
      $td[$fila['Canal']][$fila['chanID']]['PDVOUT']['Td']['Monto']=$fila['PDVOUT'];
      $td[$fila['Canal']][$fila['chanID']]['PDVP']['Td']['Monto']=$fila['PDVP'];
      $td[$fila['Canal']][$fila['chanID']]['PDVIN']['Td']['Locs']=$fila['PDVIN_Locs'];
      $td[$fila['Canal']][$fila['chanID']]['PDVOUT']['Td']['Locs']=$fila['PDVOUT_Locs'];
      $td[$fila['Canal']][$fila['chanID']]['PDVP']['Td']['Locs']=$fila['PDVP_Locs'];

      @$td[$fila['Canal']]['Total']['PDVIN']['Td']['Monto']+=$fila['PDVIN'];
      @$td[$fila['Canal']]['Total']['PDVOUT']['Td']['Monto']+=$fila['PDVOUT'];
      @$td[$fila['Canal']]['Total']['PDVP']['Td']['Monto']+=$fila['PDVP'];
      @$td[$fila['Canal']]['Total']['PDVIN']['Td']['Locs']+=$fila['PDVIN_Locs'];
      @$td[$fila['Canal']]['Total']['PDVOUT']['Td']['Locs']+=$fila['PDVOUT_Locs'];
      @$td[$fila['Canal']]['Total']['PDVP']['Td']['Locs']+=$fila['PDVP_Locs'];
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}


$query="SELECT
				locs.Fecha,
				Canal, chanID,
				CASE
					WHEN (Canal='ibMP' OR Canal='ibMP') THEN LlamadasMP
					WHEN Canal='ibMT' THEN LlamadasIT
					ELSE NULL
				END as Llamadas,
				CASE
					WHEN (Canal='ibMP' OR Canal='ibMP') THEN AnsweredMP
					WHEN Canal='ibMT' THEN AnsweredIT
					ELSE NULL
				END as Answered,
				SUM(Monto) as Monto, SUM(Xld) as Xld, SUM(Venta) as Venta,
        SUM(NewLocs) as Locs,
        SUM(PDVOUT) as PDVOUT, 
        SUM(PDVIN) as PDVIN, 
        SUM(PDVP) as PDVP, 
        			SUM(PDVIN_Locs) as PDVIN_Locs,
						SUM(PDVOUT_Locs) as PDVOUT_Locs,
						SUM(PDVP_Locs) as PDVP_Locs
		 FROM
			(SELECT
				a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN+OtrosIngresosMXN) as Venta, SUM(VentaMXN) as VentaMXN, SUM(EgresosMXN) as Xld,
				`id Departamento` as PCRC, `N Corto`, Dolar, COUNT(DISTINCT NewLoc) as NewLocs, SUM(PDVOUT) as PDVOUT, SUM(PDVIN) as PDVIN, SUM(PDVP) as PDVP, COUNT(DISTINCT PDVIN_Locs) as PDVIN_Locs, COUNT(DISTINCT PDVOUT_Locs) as PDVOUT_Locs, COUNT(DISTINCT PDVP_Locs) as PDVP_Locs,
				".$qu['chanid'].",
		     	   ".$qu['Canal']."
			FROM
				(SELECT a.*, dep as DepOK, IF(Venta!=0,Localizador,NULL) as NewLoc,
              IF(Afiliado LIKE '%shop%' AND tipo=1,(VentaMXN+OtrosIngresosMXN+EgresosMXN),0) as PDVOUT,
              IF(Afiliado LIKE '%shop%' AND tipo=2,(VentaMXN+OtrosIngresosMXN+EgresosMXN),0) as PDVIN,
              IF(Afiliado LIKE '%shop%' AND tipo NOT IN (1,2),(VentaMXN+OtrosIngresosMXN+EgresosMXN),0) as PDVP,
              IF(Afiliado LIKE '%shop%' AND tipo=2 AND Venta!=0,Localizador,NULL) as PDVIN_Locs,
              IF(Afiliado LIKE '%shop%' AND tipo=1 AND Venta!=0,Localizador,NULL) as PDVOUT_Locs,
              IF(Afiliado LIKE '%shop%' AND tipo NOT IN (1,2) AND Venta!=0,Localizador,NULL) as PDVP_Locs
         FROM t_Locs a LEFT JOIN dep_asesores b ON a.asesor=b.asesor AND a.Fecha=b.Fecha WHERE a.Fecha='".date('Y-m-d',strtotime('-1 days'))."' OR a.Fecha='".date('Y-m-d',strtotime('-7 days'))."') a
				LEFT JOIN
				Asesores b
				ON a.asesor=b.id
				LEFT JOIN
				Fechas c
				ON a.Fecha=c.Fecha
			WHERE
			(a.Fecha='".date('Y-m-d',strtotime('-1 days'))."' OR
			a.Fecha='".date('Y-m-d',strtotime('-7 days'))."') AND
			Hora<='$mxtime'
		GROUP BY
			a.Localizador
		) locs
	    LEFT JOIN
			(
				SELECT
					d.Fecha,
					COUNT(IF(Skill=3,ac_id,NULL)) as LlamadasAll,
					COUNT(IF(Skill=35,ac_id,NULL)) as LlamadasMP,
					COUNT(IF(Skill=3,ac_id,NULL)) as LlamadasIT,
					COUNT(IF(Skill=35 AND Answered=1,ac_id,NULL)) as AnsweredMP,
					COUNT(IF(Skill=3 AND Answered=1,ac_id,NULL)) as AnsweredIT,
					COUNT(IF(Skill=3 AND Canal='COPA',ac_id,NULL)) as LlamadasCOPA,
					COUNT(IF(Skill=3 AND Canal='COOMEVA',ac_id,NULL)) as LlamadasCOOMEVA
				FROM
					Fechas d
				JOIN
					t_Answered_Calls a
				ON
					d.Fecha=a.Fecha
				LEFT JOIN
					Cola_Skill b
				ON
					a.Cola=b.Cola
				LEFT JOIN
					Dids c
				ON
					a.DNIS=c.DID
				WHERE
					(a.Fecha='".date('Y-m-d',strtotime('-1 days'))."' OR
	        		a.Fecha='".date('Y-m-d',strtotime('-7 days'))."') AND
	        		Hora<='$mxtime'
				GROUP BY
					a.Fecha
			) Calls
		ON
			locs.Fecha=Calls.Fecha
		GROUP BY
			Fecha, Canal, chanID



        ";
//echo "query 248: <br>$query<br><br>";
if ($lw_y_info=$connectdb->query($query)) {
   while ($fila = $lw_y_info->fetch_assoc()) {

		if($fila['Fecha']==date('Y-m-d',strtotime('-1 days'))){$fechaarray="Y";}else{$fechaarray="LW";}

      $td[$fila['Canal']][$fila['chanID']]['Todo'][$fechaarray]['monto']=$fila['Monto'];
      $td[$fila['Canal']][$fila['chanID']]['Todo'][$fechaarray]['venta']=$fila['Venta'];
      $td[$fila['Canal']][$fila['chanID']]['Todo'][$fechaarray]['xld']=$fila['Xld'];
      $td[$fila['Canal']][$fila['chanID']]['Todo'][$fechaarray]['loc']=$fila['Locs'];

      @$td[$fila['Canal']]['Total']['Todo'][$fechaarray]['monto']+=$fila['Monto'];
      @$td[$fila['Canal']]['Total']['Todo'][$fechaarray]['venta']+=$fila['Venta'];
      @$td[$fila['Canal']]['Total']['Todo'][$fechaarray]['xld']+=$fila['Xld'];
      @$td[$fila['Canal']]['Total']['Todo'][$fechaarray]['loc']+=$fila['Locs'];

      $td[$fila['Canal']]['Total']['Todo'][$fechaarray]['calls']=$fila['Answered'];
      $td[$fila['Canal']]['Total']['Todo'][$fechaarray]['callstotal']=intval($fila['Llamadas']);
      
      $td[$fila['Canal']][$fila['chanID']]['PDVIN'][$fechaarray]['Monto']=$fila['PDVIN'];
      $td[$fila['Canal']][$fila['chanID']]['PDVOUT'][$fechaarray]['Monto']=$fila['PDVOUT'];
      $td[$fila['Canal']][$fila['chanID']]['PDVP'][$fechaarray]['Monto']=$fila['PDVP'];
      $td[$fila['Canal']][$fila['chanID']]['PDVIN'][$fechaarray]['Locs']=$fila['PDVIN_Locs'];
      $td[$fila['Canal']][$fila['chanID']]['PDVOUT'][$fechaarray]['Locs']=$fila['PDVOUT_Locs'];
      $td[$fila['Canal']][$fila['chanID']]['PDVP'][$fechaarray]['Locs']=$fila['PDVP_Locs'];

      @$td[$fila['Canal']]['Total']['PDVIN'][$fechaarray]['Monto']+=$fila['PDVIN'];
      @$td[$fila['Canal']]['Total']['PDVOUT'][$fechaarray]['Monto']+=$fila['PDVOUT'];
      @$td[$fila['Canal']]['Total']['PDVP'][$fechaarray]['Monto']+=$fila['PDVP'];
      @$td[$fila['Canal']]['Total']['PDVIN'][$fechaarray]['Locs']+=$fila['PDVIN_Locs'];
      @$td[$fila['Canal']]['Total']['PDVOUT'][$fechaarray]['Locs']+=$fila['PDVOUT_Locs'];
      @$td[$fila['Canal']]['Total']['PDVP'][$fechaarray]['Locs']+=$fila['PDVP_Locs'];
 
	}
}else{
	echo $lw_y_info->error."<br> ON <br>$query<br>";
}

//Calls TD
$query="SELECT SUM(Calls) as Calls, SUM(Unanswered) as Unanswered, Skill FROM d_dids_calls WHERE Fecha=CURDATE() GROUP BY Skill";
//echo "query 323: <br>$query<br><br>";
if ($calls_td=$connectdb->query($query)) {
   while ($fila = $calls_td->fetch_assoc()) {
		switch($fila['Skill']){
			case 35:
				$dept_calls='pin';
				$td['ibMP']['Total']['Todo']['Td']['calls']=intval($fila['Calls']);
	            if($fila['Unanswered']==""){
	                $td['ibMP']['Total']['Todo']['Td']['uncalls']=intval(0);
					$td['ibMP']['Total']['Todo']['Td']['callstotal']=intval($fila['Calls']);
	            }else{
	                $td['ibMP']['Total']['Todo']['Td']['uncalls']=intval($fila['Unanswered']);
					$td['ibMP']['Total']['Todo']['Td']['callstotal']=intval($fila['Calls'])+intval($fila['Unanswered']);
	            }

				break;
			case 3:
				$dept_calls='it';
				$td['ibMT']['Total']['Todo']['Td']['calls']=intval($fila['Calls']);
				if($fila['Unanswered']==""){
	                $td['ibMT']['Total']['Todo']['Td']['uncalls']=intval(0);
					$td['ibMT']['Total']['Todo']['Td']['callstotal']=intval($fila['Calls']);
	            }else{
	                $td['ibMT']['Total']['Todo']['Td']['uncalls']=intval($fila['Unanswered']);
	                $td['ibMT']['Total']['Todo']['Td']['callstotal']=intval($fila['Calls'])+intval($fila['Unanswered']);
	            }
				break;
			default:
				break;
		}
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

//Servicios
$query="SELECT
			Canal, chanID,
			SUM(Monto) as Monto,
			Servicio,
      COUNT(DISTINCT NewLoc) as Locs
		FROM
			(
				SELECT
		     		a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
		     		SUM(VentaMXN+OtrosIngresosMXN) as Venta,
		     		SUM(EgresosMXN) as Xld,
		     	   `id Departamento` as PCRC, `N Corto`, Dolar, IF(Venta!=0,Localizador,NULL) as NewLoc, ".$qu['chanid'].",
		     	   ".$qu['Canal'].",
				CASE
					WHEN Servicios LIKE '%Vuelo%' AND Servicios LIKE '%Hotel%' THEN 'Paquete'
					WHEN Servicios LIKE '%Vuelo%' AND Servicios NOT LIKE '%Hotel%' THEN 'Vuelo'
					WHEN Servicios NOT LIKE '%Vuelo%' AND Servicios LIKE '%Hotel%' THEN 'Hotel'
				END as Servicio
		     	FROM
		     		(SELECT
            a.*, dep as DepOK
					FROM
						d_Locs a
					LEFT JOIN
						daily_dep b ON a.asesor=b.asesor
					WHERE
						Fecha=CURDATE()  AND
						Hora<='$mxtime') a
				LEFT JOIN
		     		Asesores b
		     	ON
		     		a.asesor=b.id
		     	LEFT JOIN
		     		Fechas c
		     	ON
		     		a.Fecha=c.Fecha
		     	WHERE
		     		a.Fecha=CURDATE()
		     	GROUP BY
		     		a.Localizador
			) locs
    GROUP BY
			Canal, chanID, Servicio
    HAVING Servicio IS NOT NULL";
//echo "query 403: <br>$query<br><br>";
if ($result=$connectdb->query($query)) {
   while ($fila = $result->fetch_assoc()) {
		$td[$fila['Canal']][$fila['chanID']][$fila['Servicio']]['Td']['Monto']=$fila['Monto'];
    $td[$fila['Canal']][$fila['chanID']][$fila['Servicio']]['Td']['Locs']=$fila['Locs'];

    @$td[$fila['Canal']]['Total'][$fila['Servicio']]['Td']['Monto']+=$fila['Monto'];
    @$td[$fila['Canal']]['Total'][$fila['Servicio']]['Td']['Locs']+=$fila['Locs'];
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}


//Servicios Y, LW
$query="SELECT
			IF(Fecha=ADDDATE(CURDATE(),-1),'Y','LW') as FechaRef,
			Canal, chanID,
			SUM(Monto) as Monto,
			Servicio,
      COUNT(DISTINCT NewLoc) as Locs
		FROM
			(
				SELECT
		     		a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
		     		SUM(VentaMXN+OtrosIngresosMXN) as Venta, ".$qu['chanid'].",
		     		SUM(EgresosMXN) as Xld,
		     	   `id Departamento` as PCRC, `N Corto`, Dolar, IF(Venta!=0,Localizador,NULL) as NewLoc,
		     	   ".$qu['Canal'].",
				CASE
					WHEN Servicios LIKE '%Vuelo%' AND Servicios LIKE '%Hotel%' THEN 'Paquete'
					WHEN Servicios LIKE '%Vuelo%' AND Servicios NOT LIKE '%Hotel%' THEN 'Vuelo'
					WHEN Servicios NOT LIKE '%Vuelo%' AND Servicios LIKE '%Hotel%' THEN 'Hotel'
				END as Servicio
		     	FROM
		     		(SELECT
            a.*, dep as DepOK
					FROM
						d_Locs a
					LEFT JOIN dep_asesores b ON a.asesor=b.asesor AND a.Fecha=b.Fecha WHERE a.Fecha IN (ADDDATE(CURDATE(),-1),ADDDATE(CURDATE(),-7)) AND
						Hora<='$mxtime') a
				LEFT JOIN
		     		Asesores b
		     	ON
		     		a.asesor=b.id
		     	LEFT JOIN
		     		Fechas c
		     	ON
		     		a.Fecha=c.Fecha
		     	WHERE
		     		a.Fecha IN (ADDDATE(CURDATE(),-1),ADDDATE(CURDATE(),-7))
		     	GROUP BY
		     		a.Localizador
			) locs
		GROUP BY
			FechaRef, Canal, chanID, Servicio
    HAVING Servicio IS NOT NULL";
//echo "query 462: <br>$query<br><br>";
if ($result=$connectdb->query($query)) {
   while ($fila = $result->fetch_assoc()) {
     $td[$fila['Canal']][$fila['chanID']][$fila['Servicio']][$fila['FechaRef']]['Monto']=$fila['Monto'];
     $td[$fila['Canal']][$fila['chanID']][$fila['Servicio']][$fila['FechaRef']]['Locs']=$fila['Locs'];

     @$td[$fila['Canal']]['Total'][$fila['Servicio']][$fila['FechaRef']]['Monto']+=$fila['Monto'];
     @$td[$fila['Canal']]['Total'][$fila['Servicio']][$fila['FechaRef']]['Locs']+=$fila['Locs'];

	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

//Base Upsell
$query="SELECT CASE
      		WHEN CAST(a.Fecha as DATE)=CURDATE() THEN 'Td'
      		WHEN CAST(a.Fecha as DATE)=DATE_ADD(CURDATE(),INTERVAL -1 DAY) THEN 'Y'
      		WHEN CAST(a.Fecha as DATE)=DATE_ADD(CURDATE(),INTERVAL -7 DAY) THEN 'LW'
      	END as 'Fechas', ".str_replace('chanID','channelId',$qu['chanid'])."_ok, OL as Tipo, Servicio as Servicios_ok, COUNT(*) as Reservas FROM
(SELECT base.*, IF(locs_id IS NULL, 'Base', 'OL') as OL, IF(locs_id IS NULL, ServBase, ServLoc) as Servicio

 FROM
(SELECT *, CASE
		WHEN Servicios LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%' THEN 'Hotel'
		WHEN Servicios LIKE '%Vuelo%' AND Servicios NOT LIKE '%Hotel%' THEN 'Vuelo'
		WHEN Servicios LIKE '%Hotel%' AND Servicios LIKE '%Vuelo%' THEN 'Paquete'
		ELSE 'Otro'
	END as ServBase FROM us_basereservas a WHERE (CAST(a.Fecha as DATE)=CURDATE() OR CAST(a.Fecha as DATE)=DATE_ADD(CURDATE(),INTERVAL -1 DAY) OR CAST(a.Fecha as DATE)=DATE_ADD(CURDATE(),INTERVAL -7 DAY)) AND CAST(a.Fecha as TIME)<'$mxtime' AND correo NOT LIKE ('%test%') AND correo NOT LIKE ('%pricetravel%') GROUP BY correo
) base LEFT JOIN (SELECT *,
	CASE
		WHEN Servicios LIKE '%Hotel%' AND Servicios NOT LIKE '%Vuelo%' THEN 'Hotel'
		WHEN Servicios LIKE '%Vuelo%' AND Servicios NOT LIKE '%Hotel%' THEN 'Vuelo'
		WHEN Servicios LIKE '%Hotel%' AND Servicios LIKE '%Vuelo%' THEN 'Paquete'
		ELSE 'Otro'
	END as ServLoc FROM d_Locs WHERE asesor=-1 AND Hora<'$mxtime' AND (Fecha= CURDATE() OR Fecha=DATE_ADD(CURDATE(),INTERVAL -1 DAY)  OR Fecha=DATE_ADD(CURDATE(),INTERVAL -7 DAY)) GROUP BY Localizador) locs ON base.Localizador=locs.Localizador AND CAST(base.Fecha as DATE)=locs.Fecha) a GROUP BY Fechas, OL, channelId_ok, Servicios_ok";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    @$td['us']['Total']['Base'][$fila['Fechas']][$fila['Tipo']]+=$fila['Reservas'];
    @$td['us']['Total']['BaseDesg'][$fila['Fechas']][$fila['Tipo']][$fila['Servicios_ok']]+=$fila['Reservas'];
    $td['us'][$fila['channelId_ok']]['BaseDesg'][$fila['Fechas']][$fila['Tipo']][$fila['Servicios_ok']]+=$fila['Reservas'];
  }
}
//echo "query 506: <br>$query<br><br>";
if(isset($td['us']['Total']['Base'])){
    foreach($td['us']['Total']['Base'] as $date => $info){
      @$td['us']['Total']['Base'][$date]['FCS']=$td['us']['Total']['Todo'][$date]['loc']/$info['Base']*100;
    }
}



//LastUpdate

$query="SELECT MAX(Last_Update) as Last FROM d_Locs";
if ($last_UD=$connectdb->query($query)) {
   while ($fila = $last_UD->fetch_assoc()) {
   		$cun_zone = new DateTimeZone('America/Bogota');
   		$tmp = new DateTime($fila['Last']." America/Mexico_City");
   		$tmp -> setTimezone($cun_zone);
		$td['lu']=$tmp->format('Y-m-d H:i:s');
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

foreach($td as $canal => $info){

	switch($canal){
		case 'ibMP':
		case 'ibMT':
			$td[$canal]['Total']['Todo']['Td']['fc']=($info['Total']['Todo']['Td']['loc']+$td['PDV']['Total']['PDVIN']['Td']['Locs'])/$info['Total']['Todo']['Td']['calls']*100;
			$td[$canal]['Total']['Todo']['Y']['fc']=($info['Total']['Todo']['Y']['loc']+$td['PDV']['Total']['PDVIN']['Y']['Locs'])/$info['Total']['Todo']['Y']['calls']*100;
			$td[$canal]['Total']['Todo']['LW']['fc']=($info['Total']['Todo']['LW']['loc']+$td['PDV']['Total']['PDVIN']['LW']['Locs'])/$info['Total']['Todo']['LW']['calls']*100;
			break;
	}
}

$connectdb->close();


print json_encode($td,JSON_PRETTY_PRINT);



?>

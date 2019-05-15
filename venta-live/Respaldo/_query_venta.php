<?php
include("../connectDB.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');

	$td['Y']['loc']['pin']=0;
    $td['Y']['loc']['ppdv']=0;
    $td['Y']['loc']['it']=0;
    $td['Y']['loc']['pus']=0;
	$td['Y']['loc']['pl']=0;
    $td['Y']['monto']['pin']=0;
    $td['Y']['monto']['ppdv']=0;
    $td['Y']['monto']['it']=0;
    $td['Y']['monto']['pus']=0;
    $td['Y']['monto']['ol']=0;
    $td['Y']['calls']['pin']=0;
    $td['Y']['calls']['it']=0;
	$td['LW']['loc']['pin']=0;
    $td['LW']['loc']['ppdv']=0;
    $td['LW']['loc']['it']=0;
    $td['LW']['loc']['pus']=0;
	$td['LW']['loc']['pl']=0;
    $td['LW']['monto']['pin']=0;
    $td['LW']['monto']['ppdv']=0;
    $td['LW']['monto']['it']=0;
    $td['LW']['monto']['pus']=0;
    $td['LW']['monto']['ol']=0;
    $td['LW']['calls']['pin']=0;
    $td['LW']['calls']['it']=0;
    $td['Td']['uncalls']['it']=0;
    $td['Td']['uncalls']['pin']=0;
	$td['Y']['loc']['pin']=0;
    $td['Y']['loc']['ppdv']=0;
    $td['Y']['loc']['it']=0;
    $td['Y']['loc']['pus']=0;
	$td['Y']['loc']['pl']=0;
    $td['Y']['monto']['pin']=0;
    $td['Y']['monto']['ppdv']=0;
    $td['Y']['monto']['it']=0;
    $td['Y']['monto']['pus']=0;
    $td['Y']['monto']['ol']=0;
	$td['Y']['monto']['uspdv']=0;
    $td['Y']['monto']['ustotal']=0;
    $td['Y']['calls']['pin']=0;
    $td['Y']['calls']['it']=0;
	$td['Y']['loc']['uspdv']=0;
	$td['Y']['loc']['ustotal']=0;
	


//Error Handler

function divError(){
 echo "";
}
set_error_handler("divError");


$localzone = new DateTime('now');
$localtime= $localzone->format('H:i:s');
$localdate= $localzone->format('Y-m-d');

$mxzone = new DateTimeZone('America/Mexico_City');
$localzone->setTimezone($mxzone);
$mxtime= $localzone->format('H:i:s');
$mxdate= $localzone->format('Y-m-d');

//query today info
$query="SELECT
        		SUM(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC IN (3,35,4,6,9,43))) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))),Monto,NULL)) * 1 as InboundMPMonto,
        		SUM(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC IN (3,35,4,6,9,43))) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))),Venta,NULL)) * 1 as InboundMPMontoVenta,
        		SUM(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC IN (3,35,4,6,9,43))) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))),Xld,NULL)) * 1 as InboundMPMontoXld,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC NOT IN (28,29,30,31,5) AND PCRC IS NOT NULL))),Monto,NULL)) * 1 as InboundMPMontoCC,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC NOT IN (28,29,30,31,5) AND PCRC IS NOT NULL))),Monto,NULL)) * 1 as InboundMPMontoCCVenta,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC NOT IN (28,29,30,31,5) AND PCRC IS NOT NULL))),Xld,NULL)) * 1 as InboundMPMontoCCXld,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (PCRC=5),Monto,NULL)) * 1 as OutboundMonto,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (PCRC=5),Venta,NULL)) * 1 as OutboundVenta,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (PCRC=5),Xld,NULL)) * 1 as OutboundXld,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC IN (3,35,4,6,9,43)),Monto,NULL)) * 1 as InboundITMonto,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC IN (3,35,4,6,9,43)),Venta,NULL)) * 1 as InboundITMontoVenta,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC IN (3,35,4,6,9,43)),Xld,NULL)) * 1 as InboundITMontoXld,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%',Monto,NULL)) * 1 as PDVMonto,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%',Venta,NULL)) * 1 as PDVMontoVenta,
        		SUM(IF(asesor NOT IN (-1,-100)  AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%',Xld,NULL)) * 1 as PDVMontoXld,
        		SUM(IF(asesor=-1 AND New_Loc IS NULL,Monto,0)) as OnlineMPMonto,
        		SUM(IF(asesor=-1 AND New_Loc IS NULL,Venta,0)) as OnlineMPMontoVenta,
        		SUM(IF(asesor=-1 AND New_Loc IS NULL,Xld,0)) as OnlineMPMontoXld,
        		SUM(IF(New_Loc IS NOT NULL,Monto,0)) as PdvOutMonto,
        		SUM(IF(New_Loc IS NOT NULL,Venta,0)) as PdvOutMontoVenta,
        		SUM(IF(New_Loc IS NOT NULL,Xld,0)) as PdvOutMontoXld,
        		SUM(IF(asesor=-100,Monto,0)) as ReserbotMonto,
        		SUM(IF(asesor=-100,Venta,0)) as ReserbotMontoVenta,
        		SUM(IF(asesor=-100,Xld,0)) as ReserbotMontoXld
         FROM
        	(SELECT
        		a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
        		SUM(VentaMXN+OtrosIngresosMXN) as Venta,
        		SUM(EgresosMXN) as Xld,
        	   `id Departamento` as PCRC, `N Corto`, Dolar, New_Loc
        	FROM
        		d_Locs a
        	LEFT JOIN
        		Asesores b
        	ON
        		a.asesor=b.id
        	LEFT JOIN
        		Fechas c
        	ON
        		a.Fecha=c.Fecha
        	LEFT JOIN
        		pdv_registro_llamadas d
        	ON
        		a.Localizador=d.New_Loc AND
        		a.Fecha=CAST(d.Last_Update as DATE)
        	WHERE
        		a.Fecha=CURDATE()
        	GROUP BY
        		a.Localizador
        	) locs";

if ($today_info=$connectdb->query($query)) {
   while ($fila = $today_info->fetch_assoc()) {
		$td['Td']['monto']['pin']=number_format($fila['InboundMPMonto'],2);
	    $td['Td']['monto']['pincc']=number_format($fila['InboundMPMontoCC'],2);
	    $td['Td']['monto']['pinnocc']=number_format($fila['InboundMPMonto']-$fila['InboundMPMontoCC'],2);
	    $td['Td']['monto']['ppdv']=number_format($fila['PDVMonto'],2);
	    $td['Td']['monto']['it']=number_format($fila['InboundITMonto'],2);
	    $td['Td']['monto']['pus']=number_format($fila['OutboundMonto'],2);
	    $td['Td']['monto']['ol']=number_format($fila['OnlineMPMonto'],2);
	    $td['Td']['monto']['rb']=number_format($fila['ReserbotMonto'],2);
	    $td['Td']['monto']['uspdv']=number_format($fila['PdvOutMonto'],2);
	    $td['Td']['monto']['ustotal']=number_format($fila['PdvOutMonto']+$fila['OutboundMonto'],2);
	    
	    //Venta
	    $td['Td']['venta']['pin']=number_format($fila['InboundMPMontoVenta'],2);
	    $td['Td']['venta']['pincc']=number_format($fila['InboundMPMontoCCVenta'],2);
	    $td['Td']['venta']['pinnocc']=number_format($fila['InboundMPMontoVenta']-$fila['InboundMPMontoCCVenta'],2);
	    $td['Td']['venta']['ppdv']=number_format($fila['PDVMontoVenta'],2);
	    $td['Td']['venta']['it']=number_format($fila['InboundITMontoVenta'],2);
	    $td['Td']['venta']['pus']=number_format($fila['OutboundMontoVenta'],2);
	    $td['Td']['venta']['ol']=number_format($fila['OnlineMPMontoVenta'],2);
	    $td['Td']['venta']['rb']=number_format($fila['ReserbotMontoVenta'],2);
	    $td['Td']['venta']['uspdv']=number_format($fila['PdvOutMontoVenta'],2);
	    $td['Td']['venta']['ustotal']=number_format($fila['PdvOutMontoVenta']+$fila['OutboundMontoVenta'],2);
		
		//Xld
		$td['Td']['xld']['pin']=number_format($fila['InboundMPMontoXld'],2);
	    $td['Td']['xld']['pincc']=number_format($fila['InboundMPMontoCCXld'],2);
	    $td['Td']['xld']['pinnocc']=number_format($fila['InboundMPMontoXld']-$fila['InboundMPMontoCCXld'],2);
	    $td['Td']['xld']['ppdv']=number_format($fila['PDVMontoXld'],2);
	    $td['Td']['xld']['it']=number_format($fila['InboundITMontoXld'],2);
	    $td['Td']['xld']['pus']=number_format($fila['OutboundMontoXld'],2);
	    $td['Td']['xld']['ol']=number_format($fila['OnlineMPMontoXld'],2);
	    $td['Td']['xld']['rb']=number_format($fila['ReserbotMontoXld'],2);
	    $td['Td']['xld']['uspdv']=number_format($fila['PdvOutMontoXld'],2);
	    $td['Td']['xld']['ustotal']=number_format($fila['PdvOutMontoXld']+$fila['OutboundMontoXld'],2);
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}




//query today locs
$query="SELECT
        		COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)"
        		//Next Line Adds Locs from ONLINE SITE made by agents (but Upsell)
        		."  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))"
				."),Monto,NULL)) as InboundMPLocs,
        		COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND (PCRC=5),Localizador,NULL)) as OutboundLocs,
        		COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel%' AND Afiliado NOT LIKE'%paqcer%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9),Localizador,NULL)) as InboundITLocs,
        		COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%',Localizador,NULL)) as PDVLocs,
        		COUNT(IF(asesor=-1 AND New_Loc IS NULL,Localizador,NULL)) as OnlineMPLocs,
        		COUNT(IF(New_Loc IS NOT NULL,Localizador,NULL)) as PdvOutLocs,
        		COUNT(IF(asesor=-100,Localizador,NULL)) as ReserbotLocs

         FROM
        	(SELECT
        		a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta,
        	   `id Departamento` as PCRC, `N Corto`, Dolar, New_Loc
        	FROM
        		d_Locs a
        	LEFT JOIN
        		Asesores b
        	ON
        		a.asesor=b.id
        	LEFT JOIN
        		Fechas c
        	ON
        		a.Fecha=c.Fecha
        	LEFT JOIN
        		pdv_registro_llamadas d
        	ON
        		a.Localizador=d.New_Loc AND
        		a.Fecha=CAST(d.Last_Update as DATE)
        	WHERE
        		a.Fecha='".date('Y-m-d')."'
        	GROUP BY
        		a.Localizador
        	HAVING
        		Venta>0 AND Monto>0) locs

        ";
        
if ($today_locs=$connectdb->query($query)) {
   while ($fila = $today_locs->fetch_assoc()) {
		$td['Td']['loc']['pin']=$fila['InboundMPLocs'];
	    $td['Td']['loc']['ppdv']=$fila['PDVLocs'];
	    $td['Td']['loc']['it']=$fila['InboundITLocs'];
	    $td['Td']['loc']['pus']=$fila['OutboundLocs'];
		$td['Td']['loc']['ol']=$fila['OnlineMPLocs'];
		$td['Td']['loc']['rb']=$fila['ReserbotLocs'];
		$td['Td']['loc']['uspdv']=$fila['PdvOutLocs'];
		$td['Td']['loc']['ustotal']=$fila['PdvOutLocs']+$fila['OutboundLocs'];
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

//Query LW Y
$query="SELECT
				locs.Fecha,
				LlamadasAll, LlamadasMP, LlamadasIT, LlamadasCOPA, LlamadasCOOMEVA,
				SUM(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)"
				//Next Line Adds Locs from ONLINE SITE made by agents (but Upsell)
				."  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))"
				."),Monto,NULL)) * 1 as InboundMPMonto,
				SUM(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND (PCRC=5),Monto,NULL)) * 1 as OutboundMonto,
				SUM(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%',Monto,NULL)) * 1 as PDVMonto,
		        SUM(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9),Monto,NULL)) * 1 as InboundITMonto,
		        SUM(IF(asesor=-1 AND New_Loc IS NULL,Monto,0)) as OnlineMPMonto,
		        SUM(IF(asesor=-100 AND New_Loc IS NULL,Monto,0)) as ReserbotMonto,
		        SUM(IF(New_Loc IS NOT NULL,Monto,0)) as PdvOutMonto
		 FROM
			(SELECT
				a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
				`id Departamento` as PCRC, `N Corto`, Dolar, New_Loc
			FROM
				t_Locs a
				LEFT JOIN
				Asesores b
				ON a.asesor=b.id
				LEFT JOIN
				Fechas c
				ON a.Fecha=c.Fecha
			LEFT JOIN
	    		pdv_registro_llamadas d
	    	ON
	    		a.Localizador=d.New_Loc AND
	    		a.Fecha=CAST(d.Last_Update as DATE)
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
			Fecha



        ";
        
if ($lw_y_info=$connectdb->query($query)) {
   while ($fila = $lw_y_info->fetch_assoc()) {
			
		if($fila['Fecha']==date('Y-m-d',strtotime('-1 days'))){$fechaarray="Y";}else{$fechaarray="LW";}
	    
	    $td[$fechaarray]['monto']['pin']=number_format($fila['InboundMPMonto'],2);
	    $td[$fechaarray]['monto']['ppdv']=number_format($fila['PDVMonto'],2);
	    $td[$fechaarray]['monto']['it']=number_format($fila['InboundITMonto'],2);
	    $td[$fechaarray]['monto']['pus']=number_format($fila['OutboundMonto'],2);
	    $td[$fechaarray]['calls']['pin']=$fila['LlamadasMP'];
	    $td[$fechaarray]['calls']['it']=$fila['LlamadasIT'];
	    $td[$fechaarray]['monto']['ol']=number_format($fila['OnlineMPMonto'],2);
	    $td[$fechaarray]['monto']['rb']=number_format($fila['ReserbotMonto'],2);
	    $td[$fechaarray]['monto']['uspdv']=number_format($fila['PdvOutMonto'],2);
	    $td[$fechaarray]['monto']['ustotal']=number_format($fila['PdvOutMonto']+$fila['OutboundMonto'],2);
	}
}else{
	echo $lw_y_info->error."<br> ON <br>$query<br>";
}

//Query LW Y  LOC
$query="SELECT
			locs.Fecha,
			COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado NOT LIKE '%outlet%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9))"
			//Next Line Adds Locs from ONLINE SITE made by agents (but Upsell)
			." OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)"
			."),Localizador,NULL)) as InboundMPLocs,
			COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado LIKE'%shop.pricetravel.com.mx%',Localizador,NULL)) as PDVLocs,
	        COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND (PCRC=5),Localizador,NULL)) as OutboundLocs,
			COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND (Afiliado NOT LIKE'%pricetravel%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9),Localizador,NULL)) as InboundITLocs,
			COUNT(IF(asesor NOT IN (-1,-100) AND New_Loc IS NULL AND Afiliado LIKE'%copa%' AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundCOPALocs,
			COUNT(IF(asesor=-1 AND New_Loc IS NULL,Localizador,NULL)) as OnlineMPLocs,
			COUNT(IF(asesor=-100 AND New_Loc IS NULL,Localizador,NULL)) as ReserbotLocs,
			COUNT(IF(New_Loc IS NOT NULL,Localizador,NULL)) as PdvOutLocs
	 FROM
		(SELECT
			a.Fecha, a.asesor, Afiliado, a.Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
			`id Departamento` as PCRC, `N Corto`, Dolar, New_Loc
		FROM
			t_Locs a
		LEFT JOIN
			Asesores b
		ON
			a.asesor=b.id
		LEFT JOIN
			Fechas c
		ON a.Fecha=c.Fecha
		LEFT JOIN
    		pdv_registro_llamadas d
    	ON
    		a.Localizador=d.New_Loc AND
    		a.Fecha=CAST(d.Last_Update as DATE)
    	WHERE
			(a.Fecha='".date('Y-m-d',strtotime('-1 days'))."' OR
			a.Fecha='".date('Y-m-d',strtotime('-7 days'))."') AND
			Hora<='$mxtime'
		GROUP BY
			a.Localizador
		HAVING
	        		VentaMXN>0 AND Monto>0
	        	) locs
	    GROUP BY
			Fecha";
			
if ($lw_y_loc=$connectdb->query($query)) {
   while ($fila = $lw_y_loc->fetch_assoc()) {
			
		if($fila['Fecha']==date('Y-m-d',strtotime('-1 days'))){$fechaarray="Y";}else{$fechaarray="LW";}
	    
	    $td[$fechaarray]['loc']['pin']=$fila['InboundMPLocs'];
	    $td[$fechaarray]['loc']['ppdv']=$fila['PDVLocs'];
	    $td[$fechaarray]['loc']['it']=$fila['InboundITLocs'];
	    $td[$fechaarray]['loc']['pus']=$fila['OutboundLocs'];
	    $td[$fechaarray]['loc']['ol']=$fila['OnlineMPLocs'];
	    $td[$fechaarray]['loc']['rb']=$fila['ReserbotLocs'];
	    $td[$fechaarray]['loc']['uspdv']=$fila['PdvOutLocs'];
	    $td[$fechaarray]['loc']['ustotal']=$fila['PdvOutLocs']+$fila['OutboundLocs'];
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}


if(!isset($td['Y'])){
    $td['Y']['loc']['pin']=0;
    $td['Y']['loc']['ppdv']=0;
    $td['Y']['loc']['it']=0;
    $td['Y']['loc']['pus']=0;
	$td['Y']['loc']['pl']=0;
    $td['Y']['monto']['pin']=0;
    $td['Y']['monto']['ppdv']=0;
    $td['Y']['monto']['it']=0;
    $td['Y']['monto']['pus']=0;
    $td['Y']['monto']['ol']=0;
    $td['Y']['calls']['pin']=0;
    $td['Y']['calls']['it']=0;
	$td['Y']['loc']['uspdv']=0;
	$td['Y']['loc']['ustotal']=0;
	$td['Y']['loc']['uslocs']=0;
	$td['Y']['loc']['uslocstotal']=0;
}

//Calls TD
$query="SELECT SUM(Calls) as Calls, SUM(Unanswered) as Unanswered, Skill FROM d_dids_calls WHERE Fecha=CURDATE() GROUP BY Skill";

if ($calls_td=$connectdb->query($query)) {
   while ($fila = $calls_td->fetch_assoc()) {
		switch($fila['Skill']){
			case 35:
				$dept_calls='pin';
				$td['Td']['calls'][$dept_calls]=$fila['Calls'];
	            if($fila['Unanswered']==""){
	                $td['Td']['uncalls'][$dept_calls]=0;
	            }else{
	                $td['Td']['uncalls'][$dept_calls]=$fila['Unanswered'];
	            }
	
				break;
			case 3:
				$dept_calls='it';
				$td['Td']['calls'][$dept_calls]=$fila['Calls'];
				if($fila['Unanswered']==""){
	                $td['Td']['uncalls'][$dept_calls]=0;
	            }else{
	                $td['Td']['uncalls'][$dept_calls]=$fila['Unanswered'];
	            }
				break;
			default:
				break;
		}
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

//LastUpdate

$query="SELECT MAX(Last_Update) as Last FROM d_Locs";
if ($last_UD=$connectdb->query($query)) {
   while ($fila = $last_UD->fetch_assoc()) {
		$last_update=$fila['Last'];
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

echo "<pre>";
print_r($td);
echo "</pre>";

foreach($td as $date => $info){
    foreach($info as $tipo => $data){
        foreach($data as $canal => $data2){
        	if($data2==NULL){$datatmp=0;}else{$datatmp=$data2;}
            echo "$canal$tipo$date- $data2 -$canal$tipo$date<br>";
        }
    }
}

echo "lu- $last_update -lu<br>";

mysql_close();
?>

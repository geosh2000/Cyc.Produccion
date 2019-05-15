<?
 session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="tablas_all";
$menu_tablas="class='active'";
//Error Handler

function divError(){
 echo "";
}
set_error_handler("divError");

date_default_timezone_set('America/Bogota');

$localzone = new DateTime('now');
$localtime= $localzone->format('H:i:s');
$localdate= $localzone->format('Y-m-d');

$mxzone = new DateTimeZone('America/Mexico_City');
$localzone->setTimezone($mxzone);
$mxtime= $localzone->format('H:i:s');
$mxdate= $localzone->format('Y-m-d');

include("../connectDB.php");



$query="SELECT * FROM `SLA Ventas`";
$result=mysql_query($query);

$num=mysql_numrows($result);

mysql_query($query2);
$query2="SELECT * FROM `FC Ventas`";
$result2=mysql_query($query2);
$num2=mysql_numrows($result2);


$i=0;
while ($i < $num) {

$ID=mysql_result($result,$i,"ID");
$SVentaMXNs=mysql_result($result,$i,"SVentaMXNs");
$SSC=mysql_result($result,$i,"SSC");
$HVentaMXNs=mysql_result($result,$i,"HVentaMXNs");
$HSC=mysql_result($result,$i,"HSC");
$fecha=mysql_result($result,$i,"date");
$VLlamadas=mysql_result($result,$i,"VLlamadas");
$VLLMP=mysql_result($result,$i,"VLLMP");
$SCLlamadas=mysql_result($result,$i,"SCLlamadas");
$VLW=mysql_result($result,$i,"VLW");
$SCLW=mysql_result($result,$i,"SCLW");
$MPLW=mysql_result($result,$i,"MPLW");
$VY=mysql_result($result,$i,"VY");
$SCY=mysql_result($result,$i,"SCY");
$MPY=mysql_result($result,$i,"MPY");
$e_tmonto=mysql_result($result,$i,"TMonto");
$e_ymonto=mysql_result($result,$i,"YMonto");
$e_lwmonto=mysql_result($result,$i,"LWMonto");
$e_fc=mysql_result($result,$i,"fc");
$e_fcmp=mysql_result($result,$i,"fcmp");
$bfall=mysql_result($result,$i,"bfall");
$bfmp=mysql_result($result,$i,"bfmp");
$bfmonto=mysql_result($result,$i,"bfmonto");
$bfmall=mysql_result($result,$i,"bfmall");
$my=mysql_result($result,$i,"my");
$mlw=mysql_result($result,$i,"mlw");
$hint=mysql_result($result,$i,"hoymontointer");
$yint=mysql_result($result,$i,"ymontointer");
$lwint=mysql_result($result,$i,"lwmontointer");
$hcint=mysql_result($result,$i,"hcallsinter");
$ycint=mysql_result($result,$i,"ycallsinter");
$lwcint=mysql_result($result,$i,"lwcallsinter");
$fcint=mysql_result($result,$i,"fcinter");


$SV1=$SVentaMXNs;
$SSC1=$SSC;

$i++;
}

$i2=0;
while ($i2 < $num2) {

$ncorto[$i2]=mysql_result($result2,$i2,"N Corto");
$monto[$i2]=mysql_result($result2,$i2,"Monto");
$calls[$i2]=mysql_result($result2,$i2,"Llamadas");
$mmp[$i2]=mysql_result($result2,$i2,"MP");
$locs[$i2]=mysql_result($result2,$i2,"Locs");
$fc[$i2]=mysql_result($result2,$i2,"FC");
$fecha2[$i2]=mysql_result($result2,$i2,"fecha");

$i2++;
}

$isum=0;
$totloc=0;
$totmonto=0;
$totcalls=0;
while ($isum<$num2){
	$totloc=$totloc + $locs[$isum];
	$totmonto=$totmonto + $monto[$isum];
	$totcalls=$totcalls + $calls[$isum];
$isum++;
}

//Historico MP
$date_q=substr($fecha2[2],11,12);
$hour_q=substr($fecha2[2],0,5).':00';
if(date('I',strtotime($date_q))==0){$hour_q_ok=date('H:i:s',strtotime($hour_q.' -1 hours'));}else{$hour_q_ok=date('H:i:s',strtotime($hour_q));}
$query_calls="SELECT
                Fecha, COUNT(ac_id) as calls
                FROM
                    t_Answered_Calls
                WHERE
                    Cola LIKE '%PT%' AND
                    Hora<'".date('H:i:s',strtotime($hour_q_ok))."' AND
                    (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
                    Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
                GROUP BY
                    Fecha
                ORDER BY
                    Fecha";
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),0,'Fecha')))==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_callslw_mp=mysql_result(mysql_query($query_calls),0,'calls');
}else{$db_callsy_mp=mysql_result(mysql_query($query_calls),0,'calls');}
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),1,'Fecha')))==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_callsy_mp=mysql_result(mysql_query($query_calls),1,'calls');}
$query_hist="SELECT
	(SUM(VentaMXN)+SUM(OtrosIngresosMXN)+SUM(EgresosMXN))*17 as Total, Fecha
	FROM
		t_Locs a, Asesores b
    WHERE
        a.asesor=b.id AND
        (`id Departamento`=3 OR `id Departamento`=4) AND
        Afiliado LIKE '%agentes.pricetravel.com%' AND
        Hora < '".date('H:i:s',strtotime($hour_q_ok))."' AND
        (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
        Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
    GROUP BY
        Fecha
    ORDER BY
        Fecha";

if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_hist),0,'Fecha')))==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_montolw_mp=mysql_result(mysql_query($query_hist),0,'Total');
}else{$db_montoy_mp=mysql_result(mysql_query($query_hist),0,'Total');}
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_hist),1,'Fecha')))==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_montoy_mp=mysql_result(mysql_query($query_hist),1,'Total');}

//Historico UPSELL MP
$query_hist="SELECT
	(SUM(VentaMXN)+SUM(OtrosIngresosMXN)+SUM(EgresosMXN))*17 as Total, Fecha
	FROM
		t_Locs a, Asesores b
    WHERE
        a.asesor=b.id AND
        (`id Departamento`=5) AND
        Afiliado LIKE '%pricetravel.com%' AND
        Hora < '".date('H:i:s',strtotime($hour_q_ok))."' AND
        (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
        Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
    GROUP BY
        Fecha
    ORDER BY
        Fecha";

if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_hist),0,'Fecha')))==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_montolw_usmp=mysql_result(mysql_query($query_hist),0,'Total');
}else{$db_montoy_usmp=mysql_result(mysql_query($query_hist),0,'Total');}
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_hist),1,'Fecha')))==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_montoy_usmp=mysql_result(mysql_query($query_hist),1,'Total');}

//Historico Intertours
$query_calls="SELECT
                Fecha, COUNT(ac_id) as calls
                FROM
                    t_Answered_Calls
                WHERE
                    DNIS LIKE '%6836%' AND
                    Hora<'".date('H:i:s',strtotime($hour_q_ok))."' AND
                    (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
                    Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
                GROUP BY
                    Fecha
                ORDER BY
                    Fecha";
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),0,'Fecha')))==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_callslw_it=mysql_result(mysql_query($query_calls),0,'calls');
}else{$db_callsy_it=mysql_result(mysql_query($query_calls),0,'calls');}
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),1,'Fecha')))==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_callsy_it=mysql_result(mysql_query($query_calls),1,'calls');}
$query_hist="SELECT
	(SUM(VentaMXN)+SUM(OtrosIngresosMXN)+SUM(EgresosMXN))*17 as Total, Fecha
	FROM
		t_Locs a, Asesores b
    WHERE
        a.asesor=b.id AND
        (`id Departamento`=3 OR `id Departamento`=4) AND
        Afiliado LIKE '%intertours%' AND
        Hora < '".date('H:i:s',strtotime($hour_q_ok))."' AND
        (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
        Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
    GROUP BY
        Fecha
    ORDER BY
        Fecha";

if(mysql_result(mysql_query($query_hist),0,'Fecha')==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_montolw_it=mysql_result(mysql_query($query_hist),0,'Total');
}else{$db_montoy_it=mysql_result(mysql_query($query_hist),0,'Total');}
if(mysql_result(mysql_query($query_hist),1,'Fecha')==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_montoy_it=mysql_result(mysql_query($query_hist),1,'Total');}

//Historico All
$query_calls="SELECT
                Fecha, COUNT(ac_id) as calls
                FROM
                    t_Answered_Calls
                WHERE
                    (Cola LIKE '%VentaMXNs%' OR
                    Cola LIKE '%PT%' OR
                    Cola='LTMB') AND
                    Hora<'".date('H:i:s',strtotime($hour_q_ok))."' AND
                    (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
                    Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
                GROUP BY
                    Fecha
                ORDER BY
                    Fecha";
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),0,'Fecha')))==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_callslw_all=mysql_result(mysql_query($query_calls),0,'calls');
}else{$db_callsy_all=mysql_result(mysql_query($query_calls),0,'calls');}
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),1,'Fecha')))==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_callsy_all=mysql_result(mysql_query($query_calls),1,'calls');}
$query_hist="SELECT
	(SUM(VentaMXN)+SUM(OtrosIngresosMXN)+SUM(EgresosMXN))*17 as Total, Fecha
	FROM
		t_Locs a, Asesores b
    WHERE
        a.asesor=b.id AND
        (`id Departamento`=3 OR `id Departamento`=4) AND
        Hora < '".date('H:i:s',strtotime($hour_q_ok))."' AND
        (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
        Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
    GROUP BY
        Fecha
    ORDER BY
        Fecha";

if(mysql_result(mysql_query($query_hist),0,'Fecha')==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_montolw_all=mysql_result(mysql_query($query_hist),0,'Total');
}else{$db_montoy_all=mysql_result(mysql_query($query_hist),0,'Total');}
if(mysql_result(mysql_query($query_hist),1,'Fecha')==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_montoy_all=mysql_result(mysql_query($query_hist),1,'Total');}

//Historico LTB
$query_calls="SELECT
                Fecha, COUNT(ac_id) as calls
                FROM
                    t_Answered_Calls
                WHERE
                    COLA='LTMB' AND
                    Hora<'".date('H:i:s',strtotime($hour_q_ok))."' AND
                    (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
                    Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
                GROUP BY
                    Fecha
                ORDER BY
                    Fecha";
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),0,'Fecha')))==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_callslw_ltb=mysql_result(mysql_query($query_calls),0,'calls');
}else{$db_callsy_ltb=mysql_result(mysql_query($query_calls),0,'calls');}
if(date('Y-m-d',strtotime(mysql_result(mysql_query($query_calls),1,'Fecha')))==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_callsy_ltb=mysql_result(mysql_query($query_calls),1,'calls');}
$query_hist="SELECT
	(SUM(VentaMXN)+SUM(OtrosIngresosMXN)+SUM(EgresosMXN))*17 as Total, Fecha
	FROM
		t_Locs a, Asesores b
    WHERE
        a.asesor=b.id AND
        Afiliado LIKE '%tiquetes%' AND
        (`id Departamento`=3 OR `id Departamento`=4) AND
        Hora < '".date('H:i:s',strtotime($hour_q_ok))."' AND
        (Fecha='".date('Y-m-d',strtotime($date_q.'-1 days'))."' OR
        Fecha='".date('Y-m-d',strtotime($date_q.'-7 days'))."')
    GROUP BY
        Fecha
    ORDER BY
        Fecha";

if(mysql_result(mysql_query($query_hist),0,'Fecha')==date('Y-m-d',strtotime($date_q.'-7 days'))){$db_montolw_ltb=mysql_result(mysql_query($query_hist),0,'Total');
}else{$db_montoy_ltb=mysql_result(mysql_query($query_hist),0,'Total');}
if(mysql_result(mysql_query($query_hist),1,'Fecha')==date('Y-m-d',strtotime($date_q.'-1 days'))){$db_montoy_ltb=mysql_result(mysql_query($query_hist),1,'Total');}

//query today info
$query="SELECT
        		SUM(IF(Afiliado NOT LIKE '%outlet%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC IS NULL)),Monto,NULL)) * 1 as InboundMPMonto,
        		SUM(IF(PCRC IN (3,4,6,9),Monto,NULL)) * 1 as InboundAllMonto,
		        SUM(IF((PCRC=5),Monto,NULL)) * 1 as OutboundMonto,
        		SUM(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9),Monto,NULL)) * 1 as InboundITMonto,
        		SUM(IF(Afiliado LIKE'%shop.pricetravel.com.mx%',Monto,NULL)) * 1 as PDVMonto
         FROM
        	(SELECT
        		a.Fecha, Afiliado, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
        	   `id Departamento` as PCRC, `N Corto`, Dolar
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
        	WHERE
        		a.Fecha='".date('Y-m-d')."'
        	GROUP BY
        		Localizador
        	) locs";
$result=mysql_query($query);
$num_td=mysql_numrows($result);
$i=0;
while($i<$num_td){
    $td['ib']['monto']['all']=mysql_result($result,0,'InboundAllMonto');
    $td['ib']['monto']['mp']=mysql_result($result,0,'InboundMPMonto');
    $td['ib']['monto']['it']=mysql_result($result,0,'InboundITMonto');
    $td['ib']['monto']['copa']=mysql_result($result,0,'InboundCOPAMonto');
    $td['ib']['monto']['coomeva']=mysql_result($result,0,'InboundCOOMEVAMonto');
    $td['ob']['monto']['all']=mysql_result($result,0,'OutboundMonto');
$i++;
}

//query today locs
$query="SELECT
        		COUNT(IF(Afiliado NOT LIKE '%outlet%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC IS NULL)),Monto,NULL)) as InboundMPLocs,
        		COUNT(IF((PCRC=5),Localizador,NULL)) as OutboundLocs,
        		COUNT(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9),Localizador,NULL)) as InboundITLocs,
        		COUNT(IF(Afiliado LIKE'%shop.pricetravel.com.mx%',Localizador,NULL)) as PDVLocs

         FROM
        	(SELECT
        		a.Fecha, Afiliado, Localizador, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta,
        	   `id Departamento` as PCRC, `N Corto`, Dolar
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
        	WHERE
        		a.Fecha='".date('Y-m-d')."'
        	GROUP BY
        		Localizador
        	HAVING
        		Venta>0 AND Monto>0) locs

        ";
$result=mysql_query($query);
$num_td=mysql_numrows($result);
$i=0;
while($i<$num_td){
    $td['ib']['loc']['all']=mysql_result($result,0,'InboundAllLocs');
    $td['ib']['loc']['mp']=mysql_result($result,0,'InboundMPLocs');
    $td['ib']['loc']['it']=mysql_result($result,0,'InboundITLocs');
    $td['ib']['loc']['copa']=mysql_result($result,0,'InboundCOPALocs');
    $td['ib']['loc']['coomeva']=mysql_result($result,0,'InboundCOOMEVALocs');
    $td['ob']['loc']['all']=mysql_result($result,0,'OutboundLocs');
$i++;
}


//Query LW Y
$query="SELECT
		locs.Fecha,
		LlamadasAll, LlamadasMP, LlamadasIT, LlamadasCOPA, LlamadasCOOMEVA,
		SUM(IF(Afiliado LIKE'%pricetravel.com.mx%' AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9),Monto,NULL)) * 1 as InboundMPMonto,
        SUM(IF(PCRC IN (3,4,6,9),Monto,NULL)) * 1 as InboundAllMonto,
		SUM(IF((PCRC=5),Monto,NULL)) * 1 as OutboundMonto,
		SUM(IF(Afiliado LIKE'%shop.pricetravel.com.mx%',Monto,NULL)) * 1 as PDVMonto,
        SUM(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9),Monto,NULL)) * 1 as InboundITMonto
 FROM
	(SELECT
		a.Fecha, Afiliado, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
		`id Departamento` as PCRC, `N Corto`, Dolar
	FROM
		d_Locs a
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
		Localizador
	) locs
    LEFT JOIN
		(
			SELECT
				d.Fecha,
				COUNT(IF(Skill=3,ac_id,NULL)) as LlamadasAll,
				COUNT(IF(Skill=3 AND Canal='MP MX',ac_id,NULL)) as LlamadasMP,
				COUNT(IF(Skill=3 AND Canal='Intertours',ac_id,NULL)) as LlamadasIT,
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
		Fecha";
$result=mysql_query($query);
$num_td=mysql_numrows($result);
$i=0;
while($i<$num_td){
    $td[mysql_result($result,$i,'Fecha')]['ib']['monto']['all']=mysql_result($result,$i,'InboundAllMonto');
    $td[mysql_result($result,$i,'Fecha')]['ib']['monto']['mp']=mysql_result($result,$i,'InboundMPMonto');
    $td[mysql_result($result,$i,'Fecha')]['ib']['monto']['it']=mysql_result($result,$i,'InboundITMonto');
    $td[mysql_result($result,$i,'Fecha')]['ib']['monto']['copa']=mysql_result($result,$i,'InboundCOPAMonto');
    $td[mysql_result($result,$i,'Fecha')]['ib']['monto']['coomeva']=mysql_result($result,$i,'InboundCOOMEVAMonto');
    $td[mysql_result($result,$i,'Fecha')]['ob']['monto']['all']=mysql_result($result,$i,'OutboundMonto');
    $td[mysql_result($result,$i,'Fecha')]['ib']['calls']['all']=mysql_result($result,$i,'LlamadasAll');
    $td[mysql_result($result,$i,'Fecha')]['ib']['calls']['mp']=mysql_result($result,$i,'LlamadasMP');
    $td[mysql_result($result,$i,'Fecha')]['ib']['calls']['it']=mysql_result($result,$i,'LlamadasIT');
$i++;
}

//Query LW Y  LOC
$query="SELECT
		locs.Fecha,
		COUNT(IF(Afiliado LIKE'%pricetravel.com.mx%' AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9),Localizador,NULL)) as InboundMPLocs,
		COUNT(IF(Afiliado LIKE'%shop.pricetravel.com.mx%',Localizador,NULL)) as PDVLocs,
        COUNT(IF((PCRC=5),Localizador,NULL)) as OutboundLocs,
		COUNT(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6 OR PCRC=9),Localizador,NULL)) as InboundITLocs,
		COUNT(IF(Afiliado LIKE'%copa%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundCOPALocs
 FROM
	(SELECT
		a.Fecha, Afiliado, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
		`id Departamento` as PCRC, `N Corto`, Dolar
	FROM
		t_Locs a,
		Asesores b,
		Fechas c
	WHERE
		a.asesor=b.id AND
		a.Fecha=c.Fecha AND
		(a.Fecha='".date('Y-m-d',strtotime('-1 days'))."' OR
		a.Fecha='".date('Y-m-d',strtotime('-7 days'))."') AND
		Hora<='$mxtime'
	GROUP BY
		Localizador
	HAVING
        		VentaMXN>0 AND Monto>0
        	) locs
    GROUP BY
		Fecha



        ";
$result=mysql_query($query);
$num_td=mysql_numrows($result);
$i=0;
while($i<$num_td){
    $td[mysql_result($result,$i,'Fecha')]['ib']['loc']['all']=mysql_result($result,$i,'InboundAllLocs');
    $td[mysql_result($result,$i,'Fecha')]['ib']['loc']['mp']=mysql_result($result,$i,'InboundMPLocs');
    $td[mysql_result($result,$i,'Fecha')]['ib']['loc']['it']=mysql_result($result,$i,'InboundITLocs');
    $td[mysql_result($result,$i,'Fecha')]['ib']['loc']['copa']=mysql_result($result,$i,'InboundCOPALocs');
    $td[mysql_result($result,$i,'Fecha')]['ib']['loc']['coomeva']=mysql_result($result,$i,'InboundCOOMEVALocs');
    $td[mysql_result($result,$i,'Fecha')]['ob']['loc']['all']=mysql_result($result,$i,'OutboundLocs');
$i++;
}


//LastUpdate

$query="SELECT MAX(Last_Update) as Last FROM d_Locs";
$last_update=mysql_result(mysql_query($query),0,'Last');
?>


<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>
<? include("../common/scripts.php"); include("../common/menu.php") ?>
<script>
setTimeout(function() {
    window.location.reload();
}, 50000);
</script>
<script>

var total =50000;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
   total= total-1000;
    $('#demo').text("   //   Reload in " + total/1000 + " sec.");
}
</script>



<table style='width:100%' class='t2'>
	<tr class='title'>
		<th>KPIs VentaMXNs</th>
	</tr>
	<tr class='subtitle'>
		<td><strong>Ultima Actualizacion:   </strong><?php echo "$last_update"; ?><x id="demo"></x></TD>
	</tr>
</table>

<br><br>



<table class="tred" style="width:100%; text-align:center">


  <tr class='title'>

    <td style="width: 12%">Canal</td>
    <th style="width: 22%">Monto</th>
    <th style="width: 22%">Locs</th>
    <th style="width: 22%">FC %</th>

    <th style="width: 22%">VarLW% $</th>


  </tr>
  <?php $i=0; ?>
  <tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">

    <th class="subtitle">All (IB)</th>
    <td>$<?php echo number_format($td['ib']['monto']['all']); ?></td>
    <td><?php echo number_format($td['ib']['loc']['all']); ?></td>
    <td><?php echo number_format($td['ib']['loc']['all']/$VLlamadas*100,2); ?>%</td>
    <td><?php echo number_format(($td['ib']['monto']['all']/$td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['all']-1)*100,2); ?>%</td>
            <?php $i++; ?>

    

      </tr>

      </tr>

        <tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">

      	<th class="subtitle">UpSell MP</th>



      	<td>$<?php echo number_format($td['ob']['monto']['all']); ?></td>

        <td><?php echo number_format($td['ob']['loc']['all']); ?></td>

        <td></td>

        <td><?php echo number_format(($td['ob']['monto']['all']/$td[date('Y-m-d',strtotime('-7 days'))]['ob']['monto']['all']-1)*100,2); ?>%</td>

    <?php $i++; ?>



    	</tr>

      <tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">

      	<th  class="subtitle">MP</th>

        <td>$<?php echo number_format($td['ib']['monto']['mp']); ?></td>

        <td><?php echo number_format($td['ib']['loc']['mp']); ?></td>

        <td><?php echo number_format($td['ib']['loc']['mp']/$VLLMP*100,2); ?>%</td>

        <td><?php echo number_format(($td['ib']['monto']['mp']/$td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['mp']-1)*100,2); ?>%</td>

    <?php $i++; ?>



    	</tr>

    	<tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">

      	<th class="subtitle">IT</th>

        <td>$<?php echo number_format($td['ib']['monto']['it']); ?></td>

        <td><?php echo number_format($td['ib']['loc']['it']); ?></td>

        <td><?php echo number_format($td['ib']['loc']['it']/$hcint*100,2); ?>%</td>

        <td><?php echo number_format(($td['ib']['monto']['it']/$td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['it']-1)*100,2); ?>%</td>

    <?php $i++; ?>



    	</tr>



</table>

<br>



<table class="tblue" style="width:100%">

  <tr class='title'>

    <th style="width:12%"></th>

    <th style="width:22%">Today</th>

    <th style="width:22%">VarLW</th>

    <th style="width:22%">Y</th>

    <th style="width:22%">LW</th>

  </tr>

  <?php $i=0; ?>

  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>

    <td class="subtitle">C. All (IB)</td>

    <td <?php if($VLlamadas > $td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['all']*1.1){echo "class='u'";}else{if($VLlamadas < $td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['all']*0.9){echo "class='d'";}}?>><?php echo number_format($VLlamadas); ?></td>

    <td ><?php echo number_format(($VLlamadas /$td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['all']-1)*100,2); ?>%</td>

    <td ><?php echo number_format($td[date('Y-m-d',strtotime('-1 days'))]['ib']['calls']['all']); ?></td>

    <td ><?php echo number_format($td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['all']); ?></td>

    <?php $i++; ?>

  </tr>

<tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>

    <td class="subtitle">C. MP</td>

    <td <?php if($VLLMP > $td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['mp']*1.1){echo "class='u'";}else{if($VLLMP < $td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['mp']*0.9){echo "class='d'";}}?>><?php echo number_format($VLLMP); ?></td>

    <td ><?php echo number_format(($VLLMP /$td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['mp']-1)*100,2); ?>%</td>

    <td ><?php echo number_format($td[date('Y-m-d',strtotime('-1 days'))]['ib']['calls']['mp']); ?></td>

    <td ><?php echo number_format($td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['mp']); ?></td>

    <?php $i++; ?>

  </tr>

  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>

    <td class="subtitle">C. IT</td>

    <td <?php if($hcint > $td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['it']*1.1){echo "class='u'";}else{if($hcint < $td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['it']*0.9){echo "class='d'";}}?>><?php echo number_format($hcint); ?></td>

    <td ><?php echo number_format(($hcint/$td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['it']-1)*100,2); ?>%</td>

    <td ><?php echo number_format($td[date('Y-m-d',strtotime('-1 days'))]['ib']['calls']['it']); ?></td>

    <td ><?php echo number_format($td[date('Y-m-d',strtotime('-7 days'))]['ib']['calls']['it']); ?></td>

    <?php $i++; ?>

  </tr>



  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>

    <td class="subtitle">$ All (IB)</td>

    <td <?php if($td['ib']['monto']['all'] > $td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['all']*1.1){echo "class='u'";}else{if($td['ib']['monto']['all'] < $db_montolw_all*0.9){echo "class='d'";}}?>>$<?php echo number_format($td['ib']['monto']['all']); ?></td>

    <td><?php echo number_format(($td['ib']['monto']['all'] /$td[date('Y-m-d'.strtotime('-7 days'))]['ib']['monto']['all']-1)*100,2); ?>%</td>

    <td>$<?php echo number_format($td[date('Y-m-d',strtotime('-1 days'))]['ib']['monto']['all'],2); ?></td>

    <td>$<?php echo number_format($td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['all'],2); ?></td>

    <?php $i++; ?>

  </tr>

  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>

    <td class="subtitle">$ UpSell MP</td>

    <td <?php if($td['ob']['monto']['all'] > $td[date('Y-m-d',strtotime('-7 days'))]['ob']['monto']['all']){echo "class='u'";}else{if($td['ob']['monto']['all'] < $db_montolw_usmp*0.9){echo "class='d'";}}?>>$<?php echo number_format($td['ob']['monto']['all']); ?></td>

    <td><?php echo number_format(($td['ob']['monto']['all'] /$td[date('Y-m-d',strtotime('-7 days'))]['ob']['monto']['all']-1)*100,2); ?>%</td>

    <td>$<?php echo number_format($td[date('Y-m-d',strtotime('-1 days'))]['ob']['monto']['all']); ?></td>

    <td>$<?php echo number_format($td[date('Y-m-d',strtotime('-7 days'))]['ob']['monto']['all']); ?></td>

    <?php $i++; ?>

  </tr>

<tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>

    <td class="subtitle">$ MP</td>

    <td <?php if($td['ib']['monto']['mp'] > $td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['mp']*1.1){echo "class='u'";}else{if($td['ib']['monto']['mp'] < $db_montolw_mp*0.9){echo "class='d'";}}?>>$<?php echo number_format($td['ib']['monto']['mp']); ?></td>

    <td><?php echo number_format(($td['ib']['monto']['mp'] /$td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['mp']-1)*100,2); ?>%</td>

    <td>$<?php echo  number_format($td[date('Y-m-d',strtotime('-1 days'))]['ib']['monto']['mp'],2); ?></td>

    <td>$<?php echo  number_format($td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['mp'],2); ?></td>

    <?php $i++; ?>

  </tr>

  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>

    <td class="subtitle">$ IT</td>

    <td <?php if($td['ib']['monto']['it'] > $td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['it']*1.1){echo "class='u'";}else{if($td['ib']['monto']['it'] < $db_montolw_it*0.9){echo "class='d'";}}?>>$<?php echo number_format($td['ib']['monto']['it']); ?></td>

    <td><?php echo number_format(($td['ib']['monto']['it'] /$td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['it']-1)*100,2); ?>%</td>

    <td>$<?php echo  number_format($td[date('Y-m-d',strtotime('-1 days'))]['ib']['monto']['it'],2); ?></td>

    <td>$<?php echo  number_format($td[date('Y-m-d',strtotime('-7 days'))]['ib']['monto']['it'],2); ?></td>

    <?php $i++; ?>

  </tr>





</table>







<p id="1" style="font-size:16px; color:#ffffff; text-align: center;"></p>

<p id="2" style="font-size:16px; color:#ffffff; text-align: center;"></p>





<p style="text-align: center;"><span style="font-size:20px; color:#ffffff;"><strong></span></p>

<p  style="font-size:16px; color:#ffffff; text-align: center;"></p>







 </body>
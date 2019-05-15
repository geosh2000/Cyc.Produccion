<?php
//header('Location: venta_2.php');
 session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
include("../common/scripts.php");

?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");



?>
<style>
	.row{
		width: 1115px;
		height: 94px;
		text-align: center;
		margin-top: 7px;
		background: #215086;
		color: white;
		-webkit-border-radius: 3px;
		border: 1px solid rgba(0,0,0,0.5);
		border-top: 1px solid rgba(0,0,0,0.001);
		-webkit-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0.0.0.0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
	}
	.hora{
		display: inline-block;
		text-align: center;
		width: 50px;
		height: 40px;
		background: #779ECB;
		-webkit-border-radius: 3px;
		border: 1px solid rgba(0,0,0,0.5);
		border-top: 1px solid rgba(0,0,0,0.001);
		-webkit-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0.0.0.0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
		font-size: 37px;
		text-shadow: 1px 1px 2px black, 0 0 25px #215086, 0 0 5px darkblue;
		text-decoration: none;
		font-weight: bold;
		font-style: normal;
		font-family: Arial, Helvetica, sans-serif;
		text-align: center;
		color: white;
	}
	.block{
		vertical-align: middle;
		display: inline-block;
		text-align: center;
		width: 204px;
		height: 82px;
		margin-top: 5px;
		background: #779ECB;
		-webkit-border-radius: 3px;
		border: 1px solid rgba(0,0,0,0.5);
		border-top: 1px solid rgba(0,0,0,0.001);
		-webkit-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0.0.0.0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
	}
	.today{
		display: inline-block;
		width: 204px;
		height: 44px;
		margin:0px;
		padding-top: 10px;
		font-size: 37px;
		text-shadow: 1px 1px 2px black, 0 0 25px #215086, 0 0 5px darkblue;
		text-decoration: none;
		font-weight: bold;
		font-style: normal;
		font-family: Arial, Helvetica, sans-serif;
		text-align: center;
		color: white;
	}
	.yd{
		display: inline-block;
		width: 99px;
		height: 20px;
		margin:0px;
		margin-top:8px;
		border-right: solid 1px black;
		background: navy;
		font-size: 17px;
		text-shadow: 1px 1px 2px black, 0 0 25px #215086, 0 0 5px darkblue;
		text-decoration: none;
		font-weight: bold;
		font-style: normal;
		font-family: Arial, Helvetica, sans-serif;
		text-align: center;
		color: white;
	}
	.lw{
		display: inline-block;
		width: 99px;
		height: 20px;
		margin:0px;
		margin-top:8px;
		border-right: 0px solid black;
		background: gray;
		font-size: 17px;
		text-shadow: 1px 1px 2px black, 0 0 25px #215086, 0 0 5px darkblue;
		text-decoration: none;
		font-weight: bold;
		font-style: normal;
		font-family: Arial, Helvetica, sans-serif;
		text-align: center;
		color: white;
	}
	.ib{
		background: #AD2E4E;
	}
	.us{
		background: #D35A78;
	}
	.pdv{
		background: #E292A6;
	}
	.ol{
		background: #efc2cd;
	}
	.mt{
		background: #779ECB;
	}
	
</style>

<div class="hora" style='width:100%'>
Venta por Hora || Last Update: 2016-08-12 12:54:08
</div>

<div class='row'>
			<div class='hora'>T</div>
			<div class='block ib'>
				<div class='today' style='font-size:25px;'>Inbound<p style='font-size:20; margin: -4px;'>Hoy</p></div>
				<div class='yd'>YD</div>
				<div class='lw'>LW</div>
			</div>
			<div class='block us'>
				<div class='today' style='font-size:25px;'>Upsell<p style='font-size:20; margin: -4px;'>Hoy</p></div>
				<div class='yd'>YD</div>
				<div class='lw'>LW</div>
			</div>
			<div class='block pdv'>
				<div class='today' style='font-size:25px;'>PDV<p style='font-size:20; margin: -4px;'>Hoy</p></div>
				<div class='yd'>YD</div>
				<div class='lw'>LW</div>
			</div>
			<div class='block ol'>
				<div class='today' style='font-size:25px;'>Online<p style='font-size:20; margin: -4px;'>Hoy</p></div>
				<div class='yd'>YD</div>
				<div class='lw'>LW</div>
			</div>
			<div class='block mt'>
				<div class='today' style='font-size:25px;'>MT<p style='font-size:20; margin: -4px;'>Hoy</p></div>
				<div class='yd'>YD</div>
				<div class='lw'>LW6</div>
			</div>
		</div>

<?php

	//Query TD
	$query="SELECT
	        		Hora, SUM(IF(asesor!=-1 AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))),Monto,NULL)) * 1 as InboundMPMonto,
	        		SUM(IF(asesor!=-1 AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC NOT IN (28,29,30,31,5) AND PCRC IS NOT NULL))),Monto,NULL)) * 1 as InboundMPMontoCC,
	        		SUM(IF(asesor!=-1 AND (PCRC=5),Monto,NULL)) * 1 as OutboundMonto,
	        		SUM(IF(asesor!=-1 AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9),Monto,NULL)) * 1 as InboundITMonto,
	        		SUM(IF(asesor!=-1 AND Afiliado LIKE'%shop.pricetravel.com.mx%',Monto,NULL)) * 1 as PDVMonto,
	        		SUM(IF(asesor=-1,Monto,0)) as OnlineMPMonto
	         FROM
	        	(SELECT
	        		a.Fecha, asesor, Afiliado, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
	        	   `id Departamento` as PCRC, `N Corto`, Dolar, HOUR(Hora) as Hora
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
	        		a.Fecha=CURDATE()
	        	GROUP BY
	        		Localizador
	        	) locs
	      GROUP BY
	      	Hora
	      ORDER BY 
	      	Hora";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$data[mysql_result($result, $i, 'Hora')]['ib']['td']=mysql_result($result, $i, 'InboundMPMonto');
		$data[mysql_result($result, $i, 'Hora')]['ob']['td']=mysql_result($result, $i, 'OutboundMonto');
		$data[mysql_result($result, $i, 'Hora')]['pdv']['td']=mysql_result($result, $i, 'PDVMonto');
		$data[mysql_result($result, $i, 'Hora')]['ol']['td']=mysql_result($result, $i, 'OnlineMPMonto');
		$data[mysql_result($result, $i, 'Hora')]['mt']['td']=mysql_result($result, $i, 'InboundITMonto');
	}
	
	//Query YD
	$query="SELECT
	        		Hora, SUM(IF(asesor!=-1 AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))),Monto,NULL)) * 1 as InboundMPMonto,
	        		SUM(IF(asesor!=-1 AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC NOT IN (28,29,30,31,5) AND PCRC IS NOT NULL))),Monto,NULL)) * 1 as InboundMPMontoCC,
	        		SUM(IF(asesor!=-1 AND (PCRC=5),Monto,NULL)) * 1 as OutboundMonto,
	        		SUM(IF(asesor!=-1 AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9),Monto,NULL)) * 1 as InboundITMonto,
	        		SUM(IF(asesor!=-1 AND Afiliado LIKE'%shop.pricetravel.com.mx%',Monto,NULL)) * 1 as PDVMonto,
	        		SUM(IF(asesor=-1,Monto,0)) as OnlineMPMonto
	         FROM
	        	(SELECT
	        		a.Fecha, asesor, Afiliado, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
	        	   `id Departamento` as PCRC, `N Corto`, Dolar, HOUR(Hora) as Hora
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
	        		a.Fecha='".date('Y-m-d',strtotime('-1 days'))."'
	        	GROUP BY
	        		Localizador
	        	) locs
	      GROUP BY
	      	Hora
	      ORDER BY 
	      	Hora";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$data[mysql_result($result, $i, 'Hora')]['ib']['yd']=mysql_result($result, $i, 'InboundMPMonto');
		$data[mysql_result($result, $i, 'Hora')]['ob']['yd']=mysql_result($result, $i, 'OutboundMonto');
		$data[mysql_result($result, $i, 'Hora')]['pdv']['yd']=mysql_result($result, $i, 'PDVMonto');
		$data[mysql_result($result, $i, 'Hora')]['ol']['yd']=mysql_result($result, $i, 'OnlineMPMonto');
		$data[mysql_result($result, $i, 'Hora')]['mt']['yd']=mysql_result($result, $i, 'InboundITMonto');
	}
	
	//Query LW
	$query="SELECT
	        		Hora, SUM(IF(asesor!=-1 AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND (PCRC IS NULL OR PCRC=28)) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND PCRC!=5)  OR (Afiliado LIKE'pricetravel.com.mx%' AND (PCRC!=5 OR PCRC IS NULL))),Monto,NULL)) * 1 as InboundMPMonto,
	        		SUM(IF(asesor!=-1 AND Afiliado NOT LIKE '%outlet%' AND Afiliado NOT LIKE '%me.pricetravel%' AND (((Afiliado LIKE'%pricetravel.com.mx%' OR Afiliado LIKE'%Cerrados%') AND (PCRC NOT IN (28,29,30,31,5) AND PCRC IS NOT NULL))),Monto,NULL)) * 1 as InboundMPMontoCC,
	        		SUM(IF(asesor!=-1 AND (PCRC=5),Monto,NULL)) * 1 as OutboundMonto,
	        		SUM(IF(asesor!=-1 AND (Afiliado NOT LIKE'%pricetravel.com.mx%' AND Afiliado NOT LIKE'%Cerrados%') AND (PCRC=3 OR PCRC=35 OR PCRC=4 OR PCRC=6 OR PCRC=9),Monto,NULL)) * 1 as InboundITMonto,
	        		SUM(IF(asesor!=-1 AND Afiliado LIKE'%shop.pricetravel.com.mx%',Monto,NULL)) * 1 as PDVMonto,
	        		SUM(IF(asesor=-1,Monto,0)) as OnlineMPMonto
	         FROM
	        	(SELECT
	        		a.Fecha, asesor, Afiliado, Localizador, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(VentaMXN) as VentaMXN,
	        	   `id Departamento` as PCRC, `N Corto`, Dolar, HOUR(Hora) as Hora
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
	        		a.Fecha='".date('Y-m-d',strtotime('-7 days'))."'
	        	GROUP BY
	        		Localizador
	        	) locs
	      GROUP BY
	      	Hora
	      ORDER BY 
	      	Hora";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$data[mysql_result($result, $i, 'Hora')]['ib']['lw']=mysql_result($result, $i, 'InboundMPMonto');
		$data[mysql_result($result, $i, 'Hora')]['ob']['lw']=mysql_result($result, $i, 'OutboundMonto');
		$data[mysql_result($result, $i, 'Hora')]['pdv']['lw']=mysql_result($result, $i, 'PDVMonto');
		$data[mysql_result($result, $i, 'Hora')]['ol']['lw']=mysql_result($result, $i, 'OnlineMPMonto');
		$data[mysql_result($result, $i, 'Hora')]['mt']['lw']=mysql_result($result, $i, 'InboundITMonto');
	}



for($i=0;$i<24;$i++){
	echo "<div class='row'>
			<div class='hora'>$i</div>
			<div class='block ib'>
				<div class='today'>$".number_format($data[$i]['ib']['td'],0)."</div>
				<div class='yd'>$".number_format($data[$i]['ib']['yd'],0)."</div>
				<div class='lw'>$".number_format($data[$i]['ib']['lw'],0)."</div>
			</div>
			<div class='block us'>
				<div class='today'>$".number_format($data[$i]['ob']['td'],0)."</div>
				<div class='yd'>$".number_format($data[$i]['ob']['yd'],0)."</div>
				<div class='lw'>$".number_format($data[$i]['ob']['lw'],0)."</div>
			</div>
			<div class='block pdv'>
				<div class='today'>$".number_format($data[$i]['pdv']['td'],0)."</div>
				<div class='yd'>$".number_format($data[$i]['pdv']['yd'],0)."</div>
				<div class='lw'>$".number_format($data[$i]['pdv']['lw'],0)."</div>
			</div>
			<div class='block ol'>
				<div class='today'>$".number_format($data[$i]['ol']['td'],0)."</div>
				<div class='yd'>$".number_format($data[$i]['ol']['yd'],0)."</div>
				<div class='lw'>$".number_format($data[$i]['ol']['lw'],0)."</div>
			</div>
			<div class='block mt'>
				<div class='today'>$".number_format($data[$i]['mt']['td'],0)."</div>
				<div class='yd'>$".number_format($data[$i]['mt']['yd'],0)."</div>
				<div class='lw'>$".number_format($data[$i]['mt']['lw'],0)."</div>
			</div>
		</div>";
}

?>
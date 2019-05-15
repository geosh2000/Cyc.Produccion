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
$SVentas=mysql_result($result,$i,"SVentas");
$SSC=mysql_result($result,$i,"SSC");
$HVentas=mysql_result($result,$i,"HVentas");
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


$SV1=$SVentas;
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
	(SUM(Venta)+SUM(OtrosIngresos)+SUM(Egresos))*17 as Total, Fecha
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
	(SUM(Venta)+SUM(OtrosIngresos)+SUM(Egresos))*17 as Total, Fecha
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
	(SUM(Venta)+SUM(OtrosIngresos)+SUM(Egresos))*17 as Total, Fecha
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
                    (Cola LIKE '%Ventas%' OR
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
	(SUM(Venta)+SUM(OtrosIngresos)+SUM(Egresos))*17 as Total, Fecha
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
	(SUM(Venta)+SUM(OtrosIngresos)+SUM(Egresos))*17 as Total, Fecha
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
?>


<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

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
    document.getElementById("demo").innerHTML = "   //   Reload in " + total/1000 + " sec.";
}
</script>
<? include("../common/menu.php") ?>

<table style='width:100%' class='t2'>
	<tr class='title'>
		<th>KPIs Ventas</th>
	</tr>
	<tr class='subtitle'>
		<td><strong>Ultima Actualizacion:   </strong><?php echo "$fecha2[2]"; ?><x id="demo"></x></TD>
	</tr>
</table>

<br><br>



<table class="tred" style="width:100%; text-align:center">


  <tr class='title'>
    
    <td style="width: 12%">Canal</td>
    <th style="width: 22%">Monto</th>
    <th style="width: 22%">FC %</th>
    
    <th style="width: 22%">VarLW% C</th>
    <th style="width: 22%">VarLW% $</th>
    

  </tr>
  <?php $i=0; ?>
  <tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">
    
    <th class="subtitle">All (IB)</th>
    <td>$<?php echo number_format($totmonto); ?></td>
    <td><?php echo number_format($e_fc,2); ?>%</td>
    
        
            <td><?php echo number_format(($VLlamadas/$db_callslw_all-1)*100,2); ?>%</td>
            <td><?php echo number_format(($totmonto/$db_montolw_all-1)*100,2); ?>%</td>
            <?php $i++; ?>
    
      </tr>
      </tr>
        <tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">
      	<th class="subtitle">UpSell MP</th>

      	<td>$<?php echo number_format($hltb); ?></td>
    <td><?php echo number_format($fc_usmp,2); ?>%</td>
    <td><?php echo number_format(($hc_usmp/$lw_usmp-1)*100,2); ?>%</td>
    <td><?php echo number_format(($h_usmp/$db_montolw_usmp-1)*100,2); ?>%</td>
    <?php $i++; ?>

    	</tr>
      <tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">
      	<th  class="subtitle">MP</th>
      	
      	<td>$<?php echo number_format($e_tmonto); ?></td>
    <td><?php echo number_format($e_fcmp,2); ?>%</td>
    <td><?php echo number_format(($VLLMP/$db_callslw_mp-1)*100,2); ?>%</td>
    <td><?php echo number_format(($e_tmonto/$db_montolw_mp-1)*100,2); ?>%</td>
    <?php $i++; ?>
    
    	</tr>
    	<tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">
      	<th class="subtitle">IT</th>
      	
      	<td>$<?php echo number_format($hint); ?></td>
    <td><?php echo number_format($fcint,2); ?>%</td>
    <td><?php echo number_format(($hcint/$db_callslw_it-1)*100,2); ?>%</td>
    <td><?php echo number_format(($hint/$db_montolw_it-1)*100,2); ?>%</td>
    <?php $i++; ?>
    
    	</tr>
    	<tr class="<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>">
      	<th class="subtitle">LTB</th>

      	<td>$<?php echo number_format($hltb); ?></td>
    <td><?php echo number_format($fcltb,2); ?>%</td>
    <td><?php echo number_format(($hcltb/$db_callslw_ltb-1)*100,2); ?>%</td>
    <td><?php echo number_format(($hltb/$db_montolw_ltb-1)*100,2); ?>%</td>
    <?php $i++; ?>


    	
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
    <td <?php if($VLlamadas > $db_callslw_all*1.1){echo "class='u'";}else{if($VLlamadas < $db_callslw_all*0.9){echo "class='d'";}}?>><?php echo number_format($VLlamadas); ?></td>
    <td ><?php echo number_format(($VLlamadas /$db_callslw_all-1)*100,2); ?>%</td>
    <td ><?php echo number_format($db_callsy_all); ?></td>
    <td ><?php echo number_format($db_callslw_all); ?></td>
    <?php $i++; ?>
  </tr>
<tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">C. MP</td>
    <td <?php if($VLLMP > $db_callslw_mp*1.1){echo "class='u'";}else{if($VLLMP < $db_callslw_mp*0.9){echo "class='d'";}}?>><?php echo number_format($VLLMP); ?></td>
    <td ><?php echo number_format(($VLLMP /$db_callslw_mp-1)*100,2); ?>%</td>
    <td ><?php echo number_format($db_callsy_mp); ?></td>
    <td ><?php echo number_format($db_callslw_mp); ?></td>
    <?php $i++; ?>
  </tr>
  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">C. IT</td>
    <td <?php if($hcint > $db_callslw_it*1.1){echo "class='u'";}else{if($hcint < $db_callslw_it*0.9){echo "class='d'";}}?>><?php echo number_format($hcint); ?></td>
    <td ><?php echo number_format(($hcint/$db_callslw_it-1)*100,2); ?>%</td>
    <td ><?php echo number_format($db_callsy_it); ?></td>
    <td ><?php echo number_format($db_callslw_it); ?></td>
    <?php $i++; ?>
  </tr>
  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">C. LTB</td>
    <td <?php if($hcltb > $db_callslw_ltb*1.1){echo "class='u'";}else{if($hcltb < $db_callslw_ltb*0.9){echo "class='d'";}}?>><?php echo number_format($hcltb); ?></td>
    <td ><?php echo number_format(($hcltb/$db_callslw_ltb-1)*100,2); ?>%</td>
    <td ><?php echo number_format($db_callsy_ltb); ?></td>
    <td ><?php echo number_format($db_callslw_ltb); ?></td>
    <?php $i++; ?>
  </tr>
  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">$ All (IB)</td>
    <td <?php if($totmonto > $db_montolw_all*1.1){echo "class='u'";}else{if($totmonto < $db_montolw_all*0.9){echo "class='d'";}}?>>$<?php echo number_format($totmonto); ?></td>
    <td><?php echo number_format(($totmonto /$db_montolw_all-1)*100,2); ?>%</td>
    <td>$<?php echo number_format($db_montoy_all,2); ?></td>
    <td>$<?php echo number_format($db_montolw_all,2); ?></td>
    <?php $i++; ?>
  </tr>
  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">$ UpSell MP</td>
    <td <?php if($h_usmp > $db_montolw_usmp*1.1){echo "class='u'";}else{if($h_usmp < $db_montolw_usmp*0.9){echo "class='d'";}}?>>$<?php echo number_format($hltb); ?></td>
    <td><?php echo number_format(($h_usmp /$db_montolw_usmp-1)*100,2); ?>%</td>
    <td>$<?php echo number_format($db_montoy_usmp); ?></td>
    <td>$<?php echo number_format($db_montolw_usmp); ?></td>
    <?php $i++; ?>
  </tr>
<tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">$ MP</td>
    <td <?php if($e_tmonto > $db_montolw_mp*1.1){echo "class='u'";}else{if($e_tmonto < $db_montolw_mp*0.9){echo "class='d'";}}?>>$<?php echo number_format($e_tmonto); ?></td>
    <td><?php echo number_format(($e_tmonto /$db_montolw_mp-1)*100,2); ?>%</td>
    <td>$<?php echo  number_format($db_montoy_mp,2); ?></td>
    <td>$<?php echo  number_format($db_montolw_mp,2); ?></td>
    <?php $i++; ?>
  </tr>
  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">$ IT</td>
    <td <?php if($hint > $db_montolw_it*1.1){echo "class='u'";}else{if($hint < $db_montolw_it*0.9){echo "class='d'";}}?>>$<?php echo number_format($hint); ?></td>
    <td><?php echo number_format(($hint /$db_montolw_it-1)*100,2); ?>%</td>
    <td>$<?php echo  number_format($db_montoy_it,2); ?></td>
    <td>$<?php echo  number_format($db_montolw_it,2); ?></td>
    <?php $i++; ?>
  </tr>
  <tr class='<?php if($i % 2 == 0){echo 'pair';}else{echo 'odd';} ?>'>
    <td class="subtitle">$ LTB</td>
    <td <?php if($hltb > $db_montolw_ltb*1.1){echo "class='u'";}else{if($hltb < $db_montolw_ltb*0.9){echo "class='d'";}}?>>$<?php echo number_format($hltb); ?></td>
    <td><?php echo number_format(($hltb /$db_montolw_ltb-1)*100,2); ?>%</td>
    <td>$<?php echo number_format($db_montoy_ltb); ?></td>
    <td>$<?php echo number_format($db_montolw_ltb); ?></td>
    <?php $i++; ?>
  </tr>

  


</table>



<p id="1" style="font-size:16px; color:#ffffff; text-align: center;"></p>
<p id="2" style="font-size:16px; color:#ffffff; text-align: center;"></p>


<p style="text-align: center;"><span style="font-size:20px; color:#ffffff;"><strong></span></p>
<p  style="font-size:16px; color:#ffffff; text-align: center;"></p>



 </body>
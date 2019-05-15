<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="tabla_precision_ib";
$menu_tablas="class='active'";


include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set('America/Bogota');
include("../common/scripts.php");

//Get Variables
if(isset($_POST['inicio'])){$inicio=date('Y-m-d', strtotime($_POST['inicio']));}else{$inicio=date('Y-m-d');}
if(isset($_POST['final'])){$final=date('Y-m-d', strtotime($_POST['final']));}else{$final=date('Y-m-d');}
if(isset($_POST['inferior'])){$inf=$_POST['inferior'];}else{$inf=0.85;}
if(isset($_POST['superior'])){$sup=$_POST['superior'];}else{$sup=1.15;}
$prime_start=19;
$prime_end=45;

if(isset($_POST['consulta'])){
//List PCRCs

    $query="SELECT * FROM PCRCs WHERE inbound_calls=1 ORDER BY Departamento";
    $result=mysql_query($query);
    $num_deps=mysql_numrows($result);
    $i=0;
    while($i<$num_deps){
        $dep_id[$i]=mysql_result($result,$i,'id');
        $dep_name[$i]=mysql_result($result,$i,'Departamento');
    $i++;
    }

//query
foreach($dep_id as $key => $id){
    $i=$prime_start;
    $vars="";
    $vars_r="";
    $reals="";
    while($i<=$prime_end){
        $vars.=",Llamadas.$i*forecast_$id as pron_$i";
        $vars_r.=",reales_$i";
        $a_time=intval(($i-1)/2).":".((($i-1) % 2)/2*60).":00";
        //echo "$i // $a_time<br>";
        $a_start=date('H:i:s',strtotime($a_time));
        $a_end=date('H:i:s',strtotime($a_time.'+ 30 minutes'));
        if($i==48){$reals.=",COUNT(if(Hora>='$a_start' AND Hora<='23:59:59',ac_id,NULL)) as reales_$i";}else{
        $reals.=",COUNT(if(Hora>='$a_start' AND Hora<'$a_end',ac_id,NULL)) as reales_$i"; }
    $i++;
    }
    $query="SELECT
            Fechas.Fecha, forecast_$id as factor,(Llamadas.1+Llamadas.2+Llamadas.3+Llamadas.4+Llamadas.5+Llamadas.6+Llamadas.7
            +Llamadas.8+Llamadas.9+Llamadas.10+Llamadas.11+Llamadas.12+Llamadas.13+Llamadas.14
            +Llamadas.15+Llamadas.16+Llamadas.17+Llamadas.18+Llamadas.19+Llamadas.20+Llamadas.21
            +Llamadas.22+Llamadas.23+Llamadas.24+Llamadas.25+Llamadas.26+Llamadas.27+Llamadas.28
            +Llamadas.29+Llamadas.30+Llamadas.31+Llamadas.32+Llamadas.33+Llamadas.34+Llamadas.35
            +Llamadas.36+Llamadas.37+Llamadas.38+Llamadas.39+Llamadas.40+Llamadas.41+Llamadas.42
            +Llamadas.43+Llamadas.44+Llamadas.45+Llamadas.46+Llamadas.47+Llamadas.48) * forecast_$id as pronostico,
            Llamadas as volumen $vars $vars_r
            FROM
                Fechas
            LEFT JOIN
	            (SELECT
                    *
                FROM
                    `Historial Llamadas`
                WHERE
                    Skill='$dep_name[$key]'
                ) as Llamadas
        	ON
        	    WEEK(Fechas.Fecha- INTERVAL 365 day,1)=WEEK(Llamadas.Fecha- INTERVAL 365 day,1)-IF(Fechas.Fecha BETWEEN '2016-03-14' AND '2016-04-03',1,0) AND
        	    WEEKDAY(Fechas.Fecha- INTERVAL 365 day)+1=WEEKDAY(Llamadas.Fecha- INTERVAL 365 day)+1
        	LEFT JOIN
        		(
        			SELECT Fecha, COUNT(*) as llamadas $reals
        			FROM `t_Answered_Calls` a, Cola_Skill b
        			WHERE a.Cola=b.Cola AND b.skill=$id
        			GROUP BY Fecha
        		) reales
        	ON
				Fechas.fecha=reales.fecha
        	WHERE
                Fechas.Fecha BETWEEN '$inicio' AND '$final' AND
                YEAR(Llamadas.Fecha)=YEAR(Fechas.Fecha)-1
         GROUP BY
         	Fechas.Fecha";

    $result=mysql_query($query);
    //echo "$query<br>";
    $numrows[$key]=mysql_numrows($result);
    $x=0;
    while($x<$numrows[$key]){
        $date[$key][$x]=mysql_result($result,$x,'Fecha');
        $factor[$key][$x]=mysql_result($result,$x,'factor');
        $pronostico[$key][$x]=mysql_result($result,$x,'pronostico');
        $volumen[$key][$x]=mysql_result($result,$x,'volumen');
        $total_pronostico[$key]=$total_pronostico[$key]+$pronostico[$key][$x];
        $total_volumen[$key]=$total_volumen[$key]+$volumen[$key][$x];
        $y=$prime_start;
        while($y<=$prime_end){
            $pron_hora[$key][$x][$y]=mysql_result($result,$x,"pron_".$y);
            $reales_hora[$key][$x][$y]=mysql_result($result,$x,"reales_".$y);
        $y++;
        }
    $x++;
    }
}
unset($key,$id);
}


include("../common/menu.php");
?>
<script>
  $(function() {
    $( "#inicio" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 2,
      onClose: function( selectedDate ) {
        $( "#final" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#final" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 2,
      onClose: function( selectedDate ) {
        $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

    $( "#accordion" ).accordion({
      collapsible: false,
      heightStyle: "content",
      active: false
    });

  });
  </script>

<table width='100%' class='t2'><form action="<?php $_SERVER['PHP_SELF']?>" method='POST'>
    <tr class='title'>
        <th colspan=100>Precisión de pronósticos</th>
    </tr>
    <tr class='subtitle'>
        <td>Fecha Inicial</td>
        <td class='pair'><input type="text" id='inicio' name='inicio' value='<?php echo $inicio; ?>' required/></td>
        <td>Fecha Final</td>
        <td class='pair'><input type="text" id='final' name='final' value='<?php echo $final; ?>' required/></td>
        <td>Rango Inferior</td>
        <td class='pair'><input type="number" id='inferior' name='inferior' value='<?php echo $inf; ?>' step='0.05' required/></td>
        <td>Rango Superior</td>
        <td class='pair'><input type="number" id='superior' name='superior' value='<?php echo $sup; ?>' step='0.05' required/></td>
        <td class='total'><input type="submit" name='consulta' value='Consultar' /></td>
    </tr></form>
</table>
<br><br>
<?php
if(!isset($_POST['consulta'])){exit;}
?>
<div id="accordion">
    <h3>Precisión Acumulado</h3>
        <div>
            <table width='100%' class='t2'>
                <tr class='title'>
                    <th>Precisión</th>
                    <?php
                        foreach($dep_name as $key => $departamento){
                            $width=90/$num_deps;
                            echo "\t<th width='$width%'>$departamento</th>\n";
                        }
                        unset($key,$departamento);
                    ?>
                </tr>
                <tr class='pair'>
                    <td class='title'>Sumatoria</td>
                    <?php
                        foreach($dep_name as $key => $departamento){
                            $class='pair';
                            $precision=$total_volumen[$key]/$total_pronostico[$key];
                             if($precision>=$inf && $precision<=$sup){$corner_class="green_$class";}else{$corner_class="red_$class";}
                                $resultado=number_format(($precision)*100,2);
                            echo "\t<th class='$corner_class'>$resultado %</th>\n";
                        }
                        unset($key,$departamento);
                    ?>
                </tr>
                <tr class='odd'>
                    <td class='title'>Nivel Día</td>
                    <?php
                        foreach($dep_name as $key2 => $departamento){
                            $class='odd' ;
                            $prec_rango=0;
                            $counter=0;
                            foreach($date[$key2] as $key => $fecha){
                                $precision=$volumen[$key2][$key]/($pronostico[$key2][$key]);
                                if($precision>=$inf && $precision<=$sup){$prec_rango+=1;}
                                $counter++;
                            }
                            $precision=$prec_rango/$counter;
                            if($precision>=$inf && $precision<=$sup){$corner_class="green_$class";}else{$corner_class="red_$class";}
                                $resultado=number_format(($precision)*100,2);
                            echo "\t<th class='$corner_class'>$resultado %</th>\n";


                        }
                        unset($key,$departamento,$key2,$fecha);
                    ?>
                </tr>
                <tr class='pair'>
                    <td class='title'>Nivel Intervalo</td>
                    <?php
                        foreach($dep_name as $key2 => $departamento){
                            $class='pair' ;
                            $prec_rango=0;
                            $counter=0;
                            foreach($date[$key2] as $key => $fecha){
                                foreach($pron_hora[$key2][$key] as $key3 => $fch){
                                    $precision=$reales_hora[$key2][$key][$key3]/($fch);
                                    if($precision>=$inf-0.15 && $precision<=$sup+0.15){$prec_rango+=1;}
                                    $counter++;
                                }
                            }
                            $precision=$prec_rango/$counter;
                            if($precision>=$inf && $precision<=$sup){$corner_class="green_$class";}else{$corner_class="red_$class";}
                                $resultado=number_format(($precision)*100,2);
                            echo "\t<th class='$corner_class'>$resultado %</th>\n";


                        }
                        unset($key,$departamento,$key2,$fecha,$key3,$fch);
                    ?>
                </tr>
            </table>
        </div>
    <h3>Precisión por día</h3>
        <div>
            <table width='100%' class='t2'>
                <tr class='title'>
                    <th rowspan=2>Métrica</th>
                    <?php
                        foreach($dep_name as $key => $departamento){
                            $width=90/$num_deps;
                            echo "\t<th width='$width%' colspan=2>$departamento</th>\n";
                        }
                        unset($key,$departamento,$key2,$fecha);
                    ?>
                </tr>
                <tr class='title'>
                    <?php
                        foreach($dep_name as $key => $departamento){
                            $width=90/$num_deps/2;
                            echo "\t<th width='$width%'>Nivel<br>Dia</th>\n";
                            echo "\t<th width='$width%'>Nivel<br>Intervalo</th>\n";
                        }
                        unset($key,$departamento,$key2,$fecha);
                    ?>
                </tr>

                    <?php
                        foreach($date[0] as $key => $fecha){
                            if($key % 2 == 0){$class='pair';}else{$class='odd';}
                            echo "<tr>
                                    <td class='title'>$fecha</td>";
                            foreach($dep_name as $key2 => $departamento){
                                $precision=$volumen[$key2][$key]/($pronostico[$key2][$key]);
                                if($precision>=$inf && $precision<=$sup){$corner_class="green_$class";}else{$corner_class="red_$class";}
                                $resultado=($precision)*100;
                                echo "\t<td class='$corner_class'>".number_format($resultado,2)." %</td>\n";
                                    $prec_intra=0;
                                    $counter=0;
                                foreach($pron_hora[$key2][$key] as $key3 => $fch){
                                    if($reales_hora[$key2][$key][$key3]/$fch>=$inf-0.15 && $reales_hora[$key2][$key][$key3]/$fch<=$sup+0.15){
                                        $prec_intra++;
                                    }
                                    $counter++;

                                }
                                $resu=$prec_intra/$counter;
                                if($resu>=$inf-0.15 && $resu<=$sup+0.15){$corner_class="green_$class";}else{$corner_class="red_$class";}
                                $resultado=($resu)*100;
                                echo "\t<td class='$corner_class'>".number_format($resultado,2)." %</td>\n";
                            }
                            echo "</tr>";
                        }
                        unset($key,$departamento,$key2,$fecha,$key3,$fch);
                    ?>

            </table>
        </div>

</div>

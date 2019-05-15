<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="asesor_cuartiles";
$menu_asesores="class='active'";


?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

//GET Variables
$dep=$_POST['pcrc'];
if(isset($_POST['inicio'])){$inicio=date('Y-m-d',strtotime($_POST['inicio']));}else{$inicio=date('Y-m-d');}
if(isset($_POST['fin'])){$fin=date('Y-m-d',strtotime($_POST['fin']));}else{$fin=date('Y-m-d');}
$perc_defined=0.8;

//SELECT functions
function printDeps($variable){
    $query="SELECT a.id as id, Departamento
            FROM PCRCs_Parent a, PCRCs b
            WHERE a.id=b.id AND Cuartiles=1 ORDER BY Departamento";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while($i<$num){
        if($variable==mysql_result($result,$i,'id')){$selected="selected";}else{$selected="";}
        echo "<option value='".mysql_result($result,$i,'id')."' $selected>";
        echo mysql_result($result,$i,'Departamento');
        echo "</option>\n";
    $i++;
    }

}

function printMonth($variable){
    $i=1;
    while($i<=12){
        if($variable==$i){$selected="selected";}else{$selected="";}
        echo "<option value='$i' $selected>";
        $date="2016-$i-01";
        echo date('F',strtotime($date));
        echo "</option>\n";
    $i++;
    }
}

function printYear($variable){
    $query="SELECT DISTINCT YEAR(Fecha) as year FROM t_Answered_Calls";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while($i<$num){
        if($variable==mysql_result($result,$i,'year')){$selected="selected";}else{$selected="";}
        echo "<option value='".mysql_result($result,$i,'year')."' $selected>";
        echo mysql_result($result,$i,'year');
        echo "</option>\n";
    $i++;
    }
}

include("../common/scripts.php");

?>
<script>

$(document).ready(function()
    {
        $("#info").tablesorter();
    }
);

$(function(){
    $( "#inicio" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#fin" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#fin" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
});
</script>
<?php
include("../common/menu.php");
?>
<table class='t2' width='100%'><form action="<?php $_SERVER['PHP_SELF'] ?>" method="Post">
    <tr class='title'>
        <th colspan=100>Cuartiles por Programa Por Dia<br>*La información puede diferir del acumulado mensual</th>
    </tr>
    <tr class='subtitle'>
        <td width='14%' >Inicio</td>
        <td width='14%'  class='pair'><input type="text" id='inicio' name='inicio' value='<?php echo $inicio; ?>' /></td>
        <td width='14%' >Fin</td>
        <td width='14%'  class='pair'><input type="text" id='fin' name='fin' value='<?php echo $fin; ?>' /></td>
        <td width='14%' >PCRC</td>
        <td width='14%'  class='pair'><select name="pcrc" id="pcrc" required><option value="">Select...</option>><?php printDeps($dep); ?></select></td>
        <td class='total'><input type="submit" name="consultar"></td>
    </tr>
</form></table>

<? if(!isset($_POST['consultar'])){exit;}

$query="SELECT Sesiones.asesor as id, ADH.Nombre, Fechas.Fecha, Sesiones.Esquema, Sesiones.Skill,FindSuper(MONTH(Fechas.Fecha),YEAR(Fechas.Fecha),Sesiones.asesor) as Supervisor, SUM(TIME_TO_SEC(Sesiones.Duracion)/60) as 'Sesiones', SUM(TIME_TO_SEC(PNP)/60) as Pausas_no_Productivas, SUM(TIME_TO_SEC(PP)/60) as Pausas_Productivas, 1-SUM(TIME_TO_SEC(PNP))/SUM(TIME_TO_SEC(Duracion)) as Utilizacion, SUM(time_adh)/SUM(Duracion_jornada) as Adherencia, SUM(Llamadas) as Llamadas, SUM(Llamadas)-SUM(TransferidasMin) as Llamadas_Reales, SUM(ColgadasAsesor) as ColgadasAsesor, SUM(ColgadasAsesor)/SUM(Llamadas) as Colgadas_porcentaje, SUM(Transferidas) as Transferidas, SUM(TransferidasMin) as Transferidas_1min, AVG(AHT) as AHT, SUM(Localizadores) as Localizadores, SUM(Localizadores)/(SUM(Llamadas)-SUM(TransferidasMin)) as FC, SUM(mxn_total)+SUM(usd_total)/USD+SUM(cop_total)*COP as Monto FROM

#Fechas

( SELECT Fecha FROM Fechas WHERE Fecha>='$inicio' AND Fecha<='$fin') as Fechas LEFT JOIN

#Sesiones

( SELECT Esquema, asesor, Fecha_in as Fecha, SEC_TO_TIME(sum(TIME_TO_SEC(Duracion))) as Duracion, Skill FROM t_Sesiones a, Asesores b WHERE a.asesor=b.id AND b.`id Departamento`=$dep AND Activo=1 AND Skill=$dep GROUP BY Fecha_in, asesor ) as Sesiones ON Fechas.Fecha=Sesiones.Fecha LEFT JOIN

#Pausas
( SELECT asesor, Fecha, SEC_TO_TIME(sum(TIME_TO_SEC(if(codigo!=10 AND codigo!=0,Duracion,NULL)))) as PNP, SEC_TO_TIME(sum(TIME_TO_SEC(if(codigo=10 OR codigo=0,Duracion,NULL)))) as PP, Skill FROM t_pausas a, Asesores b WHERE a.asesor=b.id AND b.`id Departamento`=$dep AND Activo=1 AND Skill=$dep GROUP BY Fecha, asesor ) as Pausas ON Sesiones.asesor=Pausas.asesor AND Sesiones.Fecha=Pausas.Fecha LEFT JOIN

#Aderencia
( SELECT id, Nombre, Fecha, SUM(time_adh) as time_adh, SUM(Duracion_jornada) as Duracion_jornada, SUM(Retardo) as Retardos, SUM(if(Duracion_jornada=0,0,if(time_adh=0,1,0))) as Faltas FROM ( SELECT a1.id, a1.Nombre, Fecha, if(adherencia=1,0,time_adh) as time_adh, if(adherencia=1,0,Duracion_jornada) as Duracion_jornada, tipo_ausentismo, adherencia, Retardo FROM ( SELECT id, `N Corto` as Nombre, Fecha, TIME_TO_SEC( TIMEDIFF( if(Logout IS NULL, 0, if(Logout<= if(jornada_start='00:00:00' AND jornada_end='00:00:00',jornada_end, if(jornada_end>='00:00:00' AND jornada_end<='05:00:00', ADDTIME(jornada_end,'24:00:00'),jornada_end) ) ,if(jornada_start='00:00:00' AND jornada_end='00:00:00',Logout, if(Logout>='00:00:00' AND Logout<='05:00:00', ADDTIME(Logout,'24:00:00'), Logout) ) ,if(jornada_start='00:00:00' AND jornada_end='00:00:00', jornada_end, if(jornada_end>='00:00:00' AND jornada_end<='05:00:00', ADDTIME(jornada_end,'24:00:00'), jornada_end) ) ) ), if(Login IS NULL, 0, if(Login>=ADDTIME(jornada_start,'00:01:00'), Login, jornada_start) ) ) ) as time_adh, if(jornada_start='00:00:00' AND jornada_end='00:00:00',0,if(Login IS NULL, 0, if(Login>=jornada_start, 1, 0) )) as Retardo, TIME_TO_SEC(TIMEDIFF(if(jornada_start='00:00:00' AND jornada_end='00:00:00',jornada_end,if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',ADDTIME(jornada_end,'24:00:00'),jornada_end)),jornada_start)) as Duracion_jornada FROM ( SELECT Asesores.id, `N Corto`, Fechas.Fecha, if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,if(`jornada start`<'01:00:00',ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00')),ADDTIME(`jornada start`,if(Fechas.Verano=0,'-01:00:00','00:00:00')))) as jornada_start, if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,if(`jornada end`<'01:00:00',ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00')),ADDTIME(`jornada end`,if(Fechas.Verano=0,'-01:00:00','00:00:00')))) as jornada_end, if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'in')) as Login, if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'out')) as Logout FROM Asesores JOIN Fechas LEFT JOIN `Historial Programacion` ON Asesores.id=`Historial Programacion`.asesor AND Fechas.Fecha=`Historial Programacion`.Fecha LEFT JOIN t_Sesiones ON `Historial Programacion`.asesor=t_Sesiones.asesor AND `Historial Programacion`.Fecha=t_Sesiones.Fecha_in WHERE Activo=1 AND Fechas.Fecha>='$inicio' AND Fechas.Fecha<='$fin' AND `id Departamento`=$dep GROUP BY Asesores.id, Fechas.Fecha) as Jornadas GROUP BY id, Fecha ) as a1 LEFT JOIN (SELECT asesor,tipo_ausentismo, Inicio, Fin, adherencia FROM Ausentismos a, Asesores b, `Tipos Ausentismos` c WHERE a.asesor=b.id AND a.tipo_ausentismo=c.id AND b.`id Departamento`=$dep AND Inicio>='$inicio' AND Inicio<='$fin' ) as b1 ON a1.id=b1.asesor AND a1.Fecha BETWEEN Inicio AND Fin ) as Adherencia GROUP BY Fecha, id) as ADH ON Sesiones.asesor=ADH.id AND Sesiones.Fecha=ADH.Fecha LEFT JOIN

#Telefonia
( SELECT asesor, Fecha, count(ac_id) as Llamadas, COUNT(if(Desconexion='Agente',ac_id,NULL)) as ColgadasAsesor, COUNT(if(Desconexion='Transferida',ac_id,NULL)) as Transferidas, COUNT(if(Desconexion='Transferida' AND Duracion_Real<'00:01:00',ac_id,NULL)) as TransferidasMin, AVG(TIME_TO_SEC(Duracion_Real)) as AHT FROM t_Answered_Calls a, Cola_Skill b, Asesores c WHERE a.asesor=c.id AND a.Cola=b.Cola AND b.Skill=c.`id Departamento` AND c.`id Departamento`=$dep AND Fecha>='$inicio' AND Fecha<='$fin' GROUP BY asesor, Fecha ) as Telefonia ON Telefonia.asesor=Sesiones.asesor AND Telefonia.Fecha=Sesiones.Fecha LEFT JOIN

#Localizadores
( SELECT asesor, Fecha, count(DISTINCT Localizador) as Localizadores FROM t_Locs a, Asesores b WHERE a.asesor=b.id AND b.`id Departamento`=$dep AND Venta!=0 AND Fecha>='$inicio' AND Fecha<='$fin' GROUP BY Asesor, Fecha ) as Locs ON Sesiones.asesor=Locs.asesor AND Sesiones.Fecha=Locs.Fecha LEFT JOIN

#Montos
( SELECT asesor, Fecha, mxn_total, usd_total, cop_total FROM t_Montos_Diarios a, Asesores b WHERE a.asesor=b.id AND b.`id Departamento`=$dep AND Fecha>='$inicio' AND Fecha<='$fin' GROUP BY asesor, Fecha ) as Montos ON Sesiones.asesor=Montos.asesor AND Sesiones.Fecha=Montos.Fecha LEFT JOIN

#Tipo de cambio
( SELECT Fecha, AVG(Dolar) as USD, AVG(COP) as COP FROM Fechas WHERE Fecha>='$inicio' AND Fecha<='$fin' ) as TipoCambio ON Sesiones.Fecha=TipoCambio.Fecha LEFT JOIN

#Retardos Justificados
( SELECT Fecha, a.asesor, COUNT(if(tipo=$dep OR tipo=8,tipo,NULL)) as RJ FROM PyA_Exceptions a, Asesores b, `Historial Programacion` c WHERE a.asesor=b.id AND b.`id Departamento`=$dep AND a.horario_id=c.id AND Fecha>='$inicio' AND Fecha<='$fin' GROUP BY asesor, Fecha ) as Rets ON Sesiones.Fecha=Rets.Fecha AND Sesiones.asesor=Rets.asesor GROUP BY Nombre";
//echo $query;

$result=mysql_query($query);
$num=mysql_numrows($result);
$fields=mysql_num_fields($result);

$fi=0;
$i=0;
while($i<$num){
     $x=0;
     while($x<$fields){
        $field[$x]=mysql_field_name($result,$x);
        $data_asesores[$i][$field[$x]]=mysql_result($result,$i,$x);
        $data_values[$field[$x]][$i]=mysql_result($result,$i,$x);
     $x++;
     }
$i++;
}

$avg_calls=array_sum($data_values['Llamadas'])/$num*0.8;
foreach($data_values['Esquema'] as $tipo => $value){
    switch($value){
        case 4:
            $ses_4=$ses_4+$data_values['Sesiones'][$tipo];
            $ses_4_count++;
            break;
        case 6:
            $ses_6=$ses_6+$data_values['Sesiones'][$tipo];
            $ses_6_count++;
            break;
        case 8:
            $ses_8=$ses_8+$data_values['Sesiones'][$tipo];
            $ses_8_count++;
            break;
        case 10:
            $ses_10=$ses_10+$data_values['Sesiones'][$tipo];
            $ses_10_count++;
            break;

    }
}
unset($tipo);
unset($value);
$avg_ses4=$ses_4/$ses_4_count*$perc_defined;
$avg_ses6=$ses_6/$ses_6_count*$perc_defined;
$avg_ses8=$ses_8/$ses_8_count*$perc_defined;
$avg_ses10=$ses_10/$ses_10_count*$perc_defined;

foreach($data_values as $key => $valores){
    foreach($valores as $key2 => $info){
        switch($data_values['Esquema'][$key2]){
                case 4:
                    if($data_values['Sesiones'][$key2]>=$avg_ses4){$sort[$key][]=$info;}
                    break;
                case 6:
                    if($data_values['Sesiones'][$key2]>=$avg_ses6){$sort[$key][]=$info;}
                    break;
                case 8:
                    if($data_values['Sesiones'][$key2]>=$avg_ses8){$sort[$key][]=$info;}
                    break;
                case 10:
                    if($data_values['Sesiones'][$key2]>=$avg_ses10){$sort[$key][]=$info;}
                    break;
        }

    }
    unset($key2, $info);
    switch($key){
        case 'Colgadas_porcentaje':
        case 'AHT':
        case 'Retardos':
        case 'Faltas':
            sort($sort[$key]);
            break;
        default:
            rsort($sort[$key]);
            break;

    }

}
unset($key, $valores);

$q_1=intval(count($sort['AHT'])/4);
$q_2=intval(count($sort['AHT'])/4)*2;
$q_3=intval(count($sort['AHT'])/4)*3;

?>

<br><br>
<table class='t2' id='info'>
    <thead>
    <tr class='title'>
    <?php
         $csv_hdr="Info,";
        foreach($field as $key => $campo){
            $campo_ok=str_replace('_','<br>',$campo);
            echo "\t<th>$campo_ok</th>\n";
            $csv_hdr.=$campo.",";
        }
        unset($key);
        unset($campo);
        $csv_hdr=substr($csv_hdr,0,-1);
    ?>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach($data_asesores as $key => $info){
            if($key % 2 == 0){$type_class="pair"; $class="class='pair'";}else{$type_class="odd"; $class="class='odd'";}

            switch($info['Esquema']){
                case 4:
                    if($info['Sesiones']<$avg_ses4){$class="class='not'";}
                    break;
                case 6:
                    if($info['Sesiones']<$avg_ses6){$class="class='not'";}
                    break;
                case 8:
                    if($info['Sesiones']<$avg_ses8){$class="class='not'";}
                    break;
                case 10:
                    if($info['Sesiones']<$avg_ses10){$class="class='not'";}
                    break;
            }

            echo "<tr $class>\n";
            $csv1_output="";
            $csv2_output="";
            foreach($field as $key2=> $parameter){

                switch($parameter){
                    case 'Utilizacion':
                    case 'Adherencia':
                    case 'Localizadores':
                    case 'FC':
                    case 'Monto':
                        if($class!="class='not'"){
                            if($info[$parameter]>=$sort[$parameter][$q_3]){$p_class="class='orange_$type_class'"; $q=$dep;}else{$p_class="class='red_$type_class'"; $q=4;}
                            if($info[$parameter]>=$sort[$parameter][$q_2]){$p_class="class='yellow_$type_class'"; $q=2;}
                            if($info[$parameter]>=$sort[$parameter][$q_1]){$p_class="class='green_$type_class'"; $q=1;}
                        }else{$p_class="";}
                        break;
                    case 'Colgadas_porcentaje':
                    case 'AHT':
                    case 'Retardos':
                    case 'Faltas':
                        if($class!="class='not'"){
                            if($info[$parameter]>$sort[$parameter][$q_1]){$p_class="class='yellow_$type_class'"; $q=2;}else{$p_class="class='green_$type_class'"; $q=1;}
                            if($info[$parameter]>$sort[$parameter][$q_2]){$p_class="class='orange_$type_class'"; $q=$dep;}
                            if($info[$parameter]>$sort[$parameter][$q_3]){$p_class="class='red_$type_class'"; $q=4;}
                        }else{$p_class="";}
                        break;
                    default:
                        $p_class="";
                         $q="";
                        break;

                }
                switch($parameter){
                    case 'Utilizacion':
                    case 'Adherencia':
                    case 'Colgadas_porcentaje':
                    case 'FC':
                        $result=number_format($info[$parameter]*100,2)." %";
                        break;
                    case 'Monto':
                        $result="$".number_format($info[$parameter],2);
                        break;
                    case 'AHT':
                    case 'Sesiones':
                    case 'Pausas_no_Productivas':
                    case 'Pausas_Productivas':
                        $result=number_format($info[$parameter],2);
                        break;
                    case 'id':
                    case 'Nombre':
                    case 'Esquema':
                    case 'Mes':
                    case 'Skill':
                        $result=$info[$parameter];
                        $q=$info[$parameter];
                        break;
                    default:
                        $result=$info[$parameter];
                        break;
                }

                echo "\t<td $p_class>$result<br></td>\n";
                $csv1_output.=$info[$parameter].",";
                $csv2_output.=$q.",";
            }
            echo "</tr>\n";
            $csv_output.="Datos,".substr($csv1_output,0,-1)."\n";
            $csv_output.="Cuartil,".substr($csv2_output,0,-1)."\n";
        }
    ?>
    </tbody>
</table>

</table>
<br>
<form action="http://pt.comeycome.com/common/exportcsv.php" method="post" name="export"><input type="submit" value="Export" />
<input type="hidden" name="csv_hdr" value="<? echo $csv_hdr; ?>" />
<input type="hidden" name="csv_output" value="<? echo $csv_output; ?>" /></form>
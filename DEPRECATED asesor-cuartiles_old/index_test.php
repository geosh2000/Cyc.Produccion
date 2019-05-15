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
//header("Content-Type: text/html;charset=utf-8");

//GET Variables
$dep=$_POST['pcrc'];
if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d',strtotime('-1 months'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d',strtotime('-1 days'));}
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
<style>
    .selector {
        width: 140px;
        padding:5px;
        border: 0px solid;
        margin: auto;
       }
       .selector .option1{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: green;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option2{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: #b3b300;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option3{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: orange;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option4{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: red;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .qlegend{
            font: 12px arial, sans-serif;
            background-color:  #fffae6;
            color: orange;
            width: 100%;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 0px;
            margin-top:2px;
            margin-bottom:2px;
        }

</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script>
$(function(){

    $('#contain').hide();

    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
        $( "#accordion, #accordion_sups" ).accordion({
          collapsible: true,
          heightStyle: "content",
          active: false
        });
      }
    });

    function getData(){
        $('#p-bar').show();
        $('#contain').hide();
        $('#p-bar').progressbar({
          value: false
        });
        v_from=$('#from').val();
        v_to=$('#to').val();
        v_pcrc=$('#pcrc').val();
        var urlsend= "inbound_test.php?from="+v_from+"&to="+v_to+"&pcrc="+v_pcrc;
        var xmlhttp;
        var text;

        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else { // code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                text= xmlhttp.responseText;
                $('#p-bar').hide();
                $('#contain').html(text);
                $('#contain').show();
            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();

    }

    $('#send').click(function(){getData();})
});
</script>
<?php
include("../common/menu.php");
?>
<table class='t2' width='100%'>
    <tr class='title'>
        <th colspan=100>Cuartiles por Programa</th>
    </tr>
    <tr class='subtitle'>
        <td width='14%' >Inicio</td>
        <td width='14%'  class='pair'><input type="text" id='from' name='from' value='<?php echo $from; ?>' required/></td>
        <td width='14%' >Fin</td>
        <td width='14%'  class='pair'><input type="text" id='to' name='to' value='<?php echo $to; ?>' required/></td>
        <td width='14%' >PCRC</td>
        <td width='14%'  class='pair'><select name="pcrc" id="pcrc" required><option value="">Select...</option>><?php printDeps($dep); ?></select></td>
        <td class='total'><button class='button button_blue_w' id='send'>Consultar</button></td>
    </tr>
</table>
<br><br>

<div id='p-bar'>

</div>

<div id='contain'>
<div id='accordion'>
    <h3>Configuracion de Grupos</h3>
    <div id='config-contain' style='height: 400px; overflow: scroll;padding:0; position: relative'>
    <table id='config' style='font-size:12px; vertical-align: middle'>
    <thead>
        <tr>
            <th>Grupo</th>
            <td>Utilizacion</td>
<td>Adherencia</td>
<td>Colgadas</td>
<td>AHT</td>
<td>Retardos</td>
<td>Faltas</td>
<td>Localizadores</td>
<td>Cancelaciones</td>
<td>FC</td>
<td>Monto</td>
<td>Calidad</td>
        </tr>
    </thead>
    <tbody>
        <tr><td>Comportamental  1</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_111' id='c_Utilizacion_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_112' id='c_Utilizacion_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_113' id='c_Utilizacion_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_114' id='c_Utilizacion_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_111' id='c_Adherencia_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_112' id='c_Adherencia_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_113' id='c_Adherencia_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_114' id='c_Adherencia_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_111' id='c_Colgadas_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_112' id='c_Colgadas_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_113' id='c_Colgadas_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_114' id='c_Colgadas_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_111' id='c_AHT_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_112' id='c_AHT_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_113' id='c_AHT_113'  checked></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_114' id='c_AHT_114'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_111' id='c_Retardos_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_112' id='c_Retardos_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_113' id='c_Retardos_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_114' id='c_Retardos_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_111' id='c_Faltas_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_112' id='c_Faltas_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_113' id='c_Faltas_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_114' id='c_Faltas_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_111' id='c_Localizadores_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_112' id='c_Localizadores_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_113' id='c_Localizadores_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_114' id='c_Localizadores_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_111' id='c_Cancelaciones_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_112' id='c_Cancelaciones_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_113' id='c_Cancelaciones_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_114' id='c_Cancelaciones_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_111' id='c_FC_111'  checked></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_112' id='c_FC_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_113' id='c_FC_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_114' id='c_FC_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_111' id='c_Monto_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_112' id='c_Monto_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_113' id='c_Monto_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_114' id='c_Monto_114' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_111' id='c_Calidad_111' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_112' id='c_Calidad_112' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_113' id='c_Calidad_113' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_114' id='c_Calidad_114' ></section>
</div></td>
</tr>
<tr><td>Comportamental  2</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_121' id='c_Utilizacion_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_122' id='c_Utilizacion_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_123' id='c_Utilizacion_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_124' id='c_Utilizacion_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_121' id='c_Adherencia_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_122' id='c_Adherencia_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_123' id='c_Adherencia_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_124' id='c_Adherencia_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_121' id='c_Colgadas_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_122' id='c_Colgadas_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_123' id='c_Colgadas_123'  checked></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_124' id='c_Colgadas_124'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_121' id='c_AHT_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_122' id='c_AHT_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_123' id='c_AHT_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_124' id='c_AHT_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_121' id='c_Retardos_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_122' id='c_Retardos_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_123' id='c_Retardos_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_124' id='c_Retardos_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_121' id='c_Faltas_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_122' id='c_Faltas_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_123' id='c_Faltas_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_124' id='c_Faltas_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_121' id='c_Localizadores_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_122' id='c_Localizadores_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_123' id='c_Localizadores_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_124' id='c_Localizadores_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_121' id='c_Cancelaciones_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_122' id='c_Cancelaciones_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_123' id='c_Cancelaciones_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_124' id='c_Cancelaciones_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_121' id='c_FC_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_122' id='c_FC_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_123' id='c_FC_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_124' id='c_FC_124' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_121' id='c_Monto_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_122' id='c_Monto_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_123' id='c_Monto_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_124' id='c_Monto_124'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_121' id='c_Calidad_121' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_122' id='c_Calidad_122' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_123' id='c_Calidad_123' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_124' id='c_Calidad_124' ></section>
</div></td>
</tr>
<tr><td>Comportamental  3</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_131' id='c_Utilizacion_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_132' id='c_Utilizacion_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_133' id='c_Utilizacion_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_134' id='c_Utilizacion_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_131' id='c_Adherencia_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_132' id='c_Adherencia_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_133' id='c_Adherencia_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_134' id='c_Adherencia_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_131' id='c_Colgadas_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_132' id='c_Colgadas_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_133' id='c_Colgadas_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_134' id='c_Colgadas_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_131' id='c_AHT_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_132' id='c_AHT_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_133' id='c_AHT_133'  checked></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_134' id='c_AHT_134'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_131' id='c_Retardos_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_132' id='c_Retardos_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_133' id='c_Retardos_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_134' id='c_Retardos_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_131' id='c_Faltas_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_132' id='c_Faltas_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_133' id='c_Faltas_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_134' id='c_Faltas_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_131' id='c_Localizadores_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_132' id='c_Localizadores_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_133' id='c_Localizadores_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_134' id='c_Localizadores_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_131' id='c_Cancelaciones_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_132' id='c_Cancelaciones_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_133' id='c_Cancelaciones_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_134' id='c_Cancelaciones_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_131' id='c_FC_131'  checked></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_132' id='c_FC_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_133' id='c_FC_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_134' id='c_FC_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_131' id='c_Monto_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_132' id='c_Monto_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_133' id='c_Monto_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_134' id='c_Monto_134' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_131' id='c_Calidad_131' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_132' id='c_Calidad_132' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_133' id='c_Calidad_133' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_134' id='c_Calidad_134' ></section>
</div></td>
</tr>
<tr><td>Comportamental  4</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_141' id='c_Utilizacion_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_142' id='c_Utilizacion_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_143' id='c_Utilizacion_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_144' id='c_Utilizacion_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_141' id='c_Adherencia_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_142' id='c_Adherencia_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_143' id='c_Adherencia_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_144' id='c_Adherencia_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_141' id='c_Colgadas_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_142' id='c_Colgadas_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_143' id='c_Colgadas_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_144' id='c_Colgadas_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_141' id='c_AHT_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_142' id='c_AHT_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_143' id='c_AHT_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_144' id='c_AHT_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_141' id='c_Retardos_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_142' id='c_Retardos_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_143' id='c_Retardos_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_144' id='c_Retardos_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_141' id='c_Faltas_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_142' id='c_Faltas_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_143' id='c_Faltas_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_144' id='c_Faltas_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_141' id='c_Localizadores_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_142' id='c_Localizadores_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_143' id='c_Localizadores_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_144' id='c_Localizadores_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_141' id='c_Cancelaciones_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_142' id='c_Cancelaciones_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_143' id='c_Cancelaciones_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_144' id='c_Cancelaciones_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_141' id='c_FC_141'  checked></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_142' id='c_FC_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_143' id='c_FC_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_144' id='c_FC_144' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_141' id='c_Monto_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_142' id='c_Monto_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_143' id='c_Monto_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_144' id='c_Monto_144'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_141' id='c_Calidad_141' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_142' id='c_Calidad_142' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_143' id='c_Calidad_143' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_144' id='c_Calidad_144' ></section>
</div></td>
</tr>
<tr><td>Desarrollo  1</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_211' id='c_Utilizacion_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_212' id='c_Utilizacion_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_213' id='c_Utilizacion_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_214' id='c_Utilizacion_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_211' id='c_Adherencia_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_212' id='c_Adherencia_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_213' id='c_Adherencia_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_214' id='c_Adherencia_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_211' id='c_Colgadas_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_212' id='c_Colgadas_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_213' id='c_Colgadas_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_214' id='c_Colgadas_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_211' id='c_AHT_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_212' id='c_AHT_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_213' id='c_AHT_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_214' id='c_AHT_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_211' id='c_Retardos_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_212' id='c_Retardos_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_213' id='c_Retardos_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_214' id='c_Retardos_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_211' id='c_Faltas_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_212' id='c_Faltas_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_213' id='c_Faltas_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_214' id='c_Faltas_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_211' id='c_Localizadores_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_212' id='c_Localizadores_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_213' id='c_Localizadores_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_214' id='c_Localizadores_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_211' id='c_Cancelaciones_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_212' id='c_Cancelaciones_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_213' id='c_Cancelaciones_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_214' id='c_Cancelaciones_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_211' id='c_FC_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_212' id='c_FC_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_213' id='c_FC_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_214' id='c_FC_214'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_211' id='c_Monto_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_212' id='c_Monto_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_213' id='c_Monto_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_214' id='c_Monto_214' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_211' id='c_Calidad_211' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_212' id='c_Calidad_212' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_213' id='c_Calidad_213' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_214' id='c_Calidad_214' ></section>
</div></td>
</tr>
<tr><td>Desarrollo  2</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_221' id='c_Utilizacion_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_222' id='c_Utilizacion_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_223' id='c_Utilizacion_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_224' id='c_Utilizacion_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_221' id='c_Adherencia_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_222' id='c_Adherencia_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_223' id='c_Adherencia_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_224' id='c_Adherencia_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_221' id='c_Colgadas_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_222' id='c_Colgadas_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_223' id='c_Colgadas_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_224' id='c_Colgadas_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_221' id='c_AHT_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_222' id='c_AHT_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_223' id='c_AHT_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_224' id='c_AHT_224'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_221' id='c_Retardos_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_222' id='c_Retardos_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_223' id='c_Retardos_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_224' id='c_Retardos_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_221' id='c_Faltas_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_222' id='c_Faltas_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_223' id='c_Faltas_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_224' id='c_Faltas_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_221' id='c_Localizadores_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_222' id='c_Localizadores_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_223' id='c_Localizadores_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_224' id='c_Localizadores_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_221' id='c_Cancelaciones_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_222' id='c_Cancelaciones_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_223' id='c_Cancelaciones_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_224' id='c_Cancelaciones_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_221' id='c_FC_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_222' id='c_FC_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_223' id='c_FC_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_224' id='c_FC_224' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_221' id='c_Monto_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_222' id='c_Monto_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_223' id='c_Monto_223'  checked></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_224' id='c_Monto_224'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_221' id='c_Calidad_221' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_222' id='c_Calidad_222' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_223' id='c_Calidad_223' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_224' id='c_Calidad_224' ></section>
</div></td>
</tr>
<tr><td>Desarrollo  3</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_231' id='c_Utilizacion_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_232' id='c_Utilizacion_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_233' id='c_Utilizacion_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_234' id='c_Utilizacion_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_231' id='c_Adherencia_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_232' id='c_Adherencia_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_233' id='c_Adherencia_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_234' id='c_Adherencia_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_231' id='c_Colgadas_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_232' id='c_Colgadas_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_233' id='c_Colgadas_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_234' id='c_Colgadas_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_231' id='c_AHT_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_232' id='c_AHT_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_233' id='c_AHT_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_234' id='c_AHT_234'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_231' id='c_Retardos_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_232' id='c_Retardos_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_233' id='c_Retardos_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_234' id='c_Retardos_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_231' id='c_Faltas_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_232' id='c_Faltas_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_233' id='c_Faltas_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_234' id='c_Faltas_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_231' id='c_Localizadores_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_232' id='c_Localizadores_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_233' id='c_Localizadores_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_234' id='c_Localizadores_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_231' id='c_Cancelaciones_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_232' id='c_Cancelaciones_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_233' id='c_Cancelaciones_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_234' id='c_Cancelaciones_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_231' id='c_FC_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_232' id='c_FC_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_233' id='c_FC_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_234' id='c_FC_234'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_231' id='c_Monto_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_232' id='c_Monto_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_233' id='c_Monto_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_234' id='c_Monto_234' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_231' id='c_Calidad_231' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_232' id='c_Calidad_232' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_233' id='c_Calidad_233' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_234' id='c_Calidad_234' ></section>
</div></td>
</tr>
<tr><td>Desarrollo  4</td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Utilizacion_241' id='c_Utilizacion_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Utilizacion_242' id='c_Utilizacion_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Utilizacion_243' id='c_Utilizacion_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Utilizacion_244' id='c_Utilizacion_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Adherencia_241' id='c_Adherencia_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Adherencia_242' id='c_Adherencia_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Adherencia_243' id='c_Adherencia_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Adherencia_244' id='c_Adherencia_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Colgadas_241' id='c_Colgadas_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Colgadas_242' id='c_Colgadas_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Colgadas_243' id='c_Colgadas_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Colgadas_244' id='c_Colgadas_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_AHT_241' id='c_AHT_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_AHT_242' id='c_AHT_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_AHT_243' id='c_AHT_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_AHT_244' id='c_AHT_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Retardos_241' id='c_Retardos_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Retardos_242' id='c_Retardos_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Retardos_243' id='c_Retardos_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Retardos_244' id='c_Retardos_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Faltas_241' id='c_Faltas_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Faltas_242' id='c_Faltas_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Faltas_243' id='c_Faltas_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Faltas_244' id='c_Faltas_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Localizadores_241' id='c_Localizadores_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Localizadores_242' id='c_Localizadores_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Localizadores_243' id='c_Localizadores_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Localizadores_244' id='c_Localizadores_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Cancelaciones_241' id='c_Cancelaciones_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Cancelaciones_242' id='c_Cancelaciones_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Cancelaciones_243' id='c_Cancelaciones_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Cancelaciones_244' id='c_Cancelaciones_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_FC_241' id='c_FC_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_FC_242' id='c_FC_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_FC_243' id='c_FC_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_FC_244' id='c_FC_244'  checked></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Monto_241' id='c_Monto_241' ></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Monto_242' id='c_Monto_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Monto_243' id='c_Monto_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Monto_244' id='c_Monto_244' ></section>
</div></td>
<td><div class='selector'>
<section class='option1'>Q1  <input type='checkbox' name='c_Calidad_241' id='c_Calidad_241'  checked></section>
<section class='option2'>Q2  <input type='checkbox' name='c_Calidad_242' id='c_Calidad_242' ></section>
<section class='option3'>Q3  <input type='checkbox' name='c_Calidad_243' id='c_Calidad_243' ></section>
<section class='option4'>Q4  <input type='checkbox' name='c_Calidad_244' id='c_Calidad_244' ></section>
</div></td>
</tr>

    </tbody>
    </table>
    </div>

</div>

<br>
<div style='text-align:left; width:120px; float:left; display: inline-block;'>
<button type="button" tipo="" number='' class='group_select button button_white_r'>Reset</button>
</div>
<div style='text-align:left; width:35%; float:left; display: inline-block;'>
<button type="button" tipo="1" number='1' class='group_select buttonlarge button_redpastel_w'>Comportamental 1</button>
<button type="button" tipo="1" number='2' class='group_select buttonlarge button_redpastel_w'>Comportamental 2</button>
<button type="button" tipo="1" number='3' class='group_select buttonlarge button_redpastel_w'>Comportamental 3</button>
<button type="button" tipo="1" number='4' class='group_select buttonlarge button_redpastel_w'>Comportamental 4</button>
</div>
<div style='text-align:left; width:35%; float:left; display: inline-block;'>
<button type="button" tipo="2" number='1' class='group_select buttonlarge button_orange_w'>Desarrollable 1</button>
<button type="button" tipo="2" number='2' class='group_select buttonlarge button_orange_w'>Desarrollable 2</button>
<button type="button" tipo="2" number='3' class='group_select buttonlarge button_orange_w'>Desarrollable 3</button>
<button type="button" tipo="2" number='4' class='group_select buttonlarge button_orange_w'>Desarrollable 4</button>
</div>
<div style='text-align:right; width:100px; float:right; display: inline-block;'>
<input type="button" id='export' value="Export" class='button button_blue_w'/>  </div>
<br>

<div id='container-cuartiles' style='max-height:800px; width: 100%; overflow: scroll; position: relative'>
<table id='info' style='font-size:12px'>
    <thead>
    <tr class='title'>
    	<th colspan=1 class='drag-enable'>id</th>
	<th colspan=2 class='drag-enable'>Asesor</th>
	<th colspan=1 class='drag-enable'>Supervisor</th>
	<th colspan=1 class='drag-enable'>Esquema</th>
	<th colspan=1 class='drag-enable'>Dep</th>
	<th colspan=1 class='drag-enable'>Fechas</th>
	<th colspan=1 class='drag-enable'>Duracion<br>Sesion</th>
	<th colspan=1 class='drag-enable'>Pausas<br>No<br>Productivas</th>
	<th colspan=1 class='drag-enable'>Pausas<br>Productivas</th>
	<th colspan=1 class='drag-enable'>Utilizacion</th><th>Cuartil<br>Utilizacion</th>
	<th colspan=1 class='drag-enable'>Adherencia</th><th>Cuartil<br>Adherencia</th>
	<th colspan=1 class='drag-enable'>Retardos</th><th>Cuartil<br>Retardos</th>
	<th colspan=1 class='drag-enable'>Faltas</th><th>Cuartil<br>Faltas</th>
	<th colspan=1 class='drag-enable'>Ausentismos<br>Autorizados</th>
	<th colspan=1 class='drag-enable'>Llamadas</th>
	<th colspan=1 class='drag-enable'>Llamadas<br>Coomeva</th>
	<th colspan=1 class='drag-enable'>Llamadas<br>Reales</th>
	<th colspan=1 class='drag-enable'>Llamadas<br>Colgadas</th>
	<th colspan=1 class='drag-enable'>Porcentaje<br>Colgadas</th><th>Cuartil<br>Porcentaje_Colgadas</th>
	<th colspan=1 class='drag-enable'>Transferidas</th>
	<th colspan=1 class='drag-enable'>Transferidas<br>1min</th>
	<th colspan=1 class='drag-enable'>AHT</th><th>Cuartil<br>AHT</th>
	<th colspan=1 class='drag-enable'>Localizadores</th><th>Cuartil<br>Localizadores</th>
	<th colspan=1 class='drag-enable'>FC</th><th>Cuartil<br>FC</th>
	<th colspan=1 class='drag-enable'>Monto</th><th>Cuartil<br>Monto</th>
	<th colspan=1 class='drag-enable'>LocalizadoresMP</th><th>Cuartil<br>LocalizadoresMP</th>
	<th colspan=1 class='drag-enable'>MontoMP</th><th>Cuartil<br>MontoMP</th>
	<th colspan=1 class='drag-enable'>Traslados<br>items</th><th>Cuartil<br>Traslados_items</th>
	<th colspan=1 class='drag-enable'>Traslados<br>pax</th>
	<th colspan=1 class='drag-enable'>Traslados<br>noches</th>
	<th colspan=1 class='drag-enable'>Tours<br>items</th><th>Cuartil<br>Tours_items</th>
	<th colspan=1 class='drag-enable'>Tours<br>pax</th>
	<th colspan=1 class='drag-enable'>Tours<br>noches</th>
	<th colspan=1 class='drag-enable'>Cruceros<br>items</th><th>Cuartil<br>Cruceros_items</th>
	<th colspan=1 class='drag-enable'>Cruceros<br>pax</th>
	<th colspan=1 class='drag-enable'>Cruceros<br>noches</th>
	<th colspan=1 class='drag-enable'>Circuitos<br>items</th><th>Cuartil<br>Circuitos_items</th>
	<th colspan=1 class='drag-enable'>Circuitos<br>paxs</th>
	<th colspan=1 class='drag-enable'>Circuitos<br>noches</th>
    </tr>
    </thead>
    <tbody>
    <tr style='text-align: center'>
	<td>2 </td>
	<td>Alex Carmona </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td>Rafael Acosta </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>938.00 </td>
	<td>82.57 </td>
	<td>1.98 </td>
	<td>91.20 % </td><td></td></td>
	<td>51.78 % </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>0 </td>
	<td>117 </td>
	<td>0 </td>
	<td>108 </td>
	<td>3 </td>
	<td>2.56 % </td><td></td></td>
	<td>12 </td>
	<td>9 </td>
	<td>383.01 </td><td></td></td>
	<td>14 </td><td></td></td>
	<td>12.96 % </td><td></td></td>
	<td>$194,512.04 </td><td></td></td>
	<td>10 </td><td></td></td>
	<td>$111,856.07 </td><td></td></td>
	<td>3 </td><td></td></td>
	<td>9 </td>
	<td>0 </td>
	<td>4 </td><td></td></td>
	<td>8 </td>
	<td>0 </td>
	<td>0 </td><td></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>9051 </td>
	<td>Alfredo Alexandres </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td> </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>4 </td>
	<td>Anayeli Rojas </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,354.00 </td>
	<td>207.32 </td>
	<td>0.48 </td>
	<td>91.19 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>176 </td>
	<td>0 </td>
	<td>169 </td>
	<td>20 </td>
	<td>11.36 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>8 </td>
	<td>7 </td>
	<td>549.60 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>40 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>23.67 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$612,777.03 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>24 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$287,900.79 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>6 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>17 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>241 </td>
	<td>Ashley Flores </td><td></td></td>
	<td>Rafael Acosta </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,497.00 </td>
	<td>79.55 </td>
	<td>0.00 </td>
	<td>94.69 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>161 </td>
	<td>0 </td>
	<td>150 </td>
	<td>23 </td>
	<td>14.29 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>14 </td>
	<td>11 </td>
	<td>512.80 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>20 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>13.33 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$215,074.01 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>14 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$154,715.32 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>206 </td>
	<td>Atalia Landa </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,456.00 </td>
	<td>231.85 </td>
	<td>0.00 </td>
	<td>90.56 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>212 </td>
	<td>0 </td>
	<td>210 </td>
	<td>9 </td>
	<td>4.25 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>6 </td>
	<td>2 </td>
	<td>498.95 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>30 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>14.29 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$316,938.23 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>18 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$149,041.67 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>1 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>3 </td>
	<td>0 </td>
	<td>1 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>9049 </td>
	<td>Betzabe Aguila </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td> </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>11 </td>
	<td>Cesar Ojeda </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td>Rafael Acosta </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>5 </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>12 </td>
	<td>Danya Ibarra </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,964.00 </td>
	<td>157.08 </td>
	<td>17.50 </td>
	<td>92.00 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>98.59 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>179 </td>
	<td>0 </td>
	<td>175 </td>
	<td>13 </td>
	<td>7.26 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>10 </td>
	<td>4 </td>
	<td>424.35 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>27 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>15.43 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$229,684.38 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>12 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$90,836.50 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>2 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>6 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>13 </td>
	<td>David Ramirez </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,299.00 </td>
	<td>295.00 </td>
	<td>0.00 </td>
	<td>87.17 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>99.96 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>139 </td>
	<td>0 </td>
	<td>136 </td>
	<td>4 </td>
	<td>2.88 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>8 </td>
	<td>3 </td>
	<td>729.95 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>32 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>23.53 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$573,171.19 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>26 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$445,620.65 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>7 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>27 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>240 </td>
	<td>Debora Sanchez </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,840.00 </td>
	<td>39.12 </td>
	<td>38.00 </td>
	<td>97.87 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>101.33 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>146 </td>
	<td>0 </td>
	<td>139 </td>
	<td>13 </td>
	<td>8.90 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>9 </td>
	<td>7 </td>
	<td>618.62 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>28 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>20.14 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$228,614.79 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>15 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$104,304.32 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>2 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>4 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>14 </td>
	<td>Dulce Cespedes </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$1,704.05 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>234 </td>
	<td>Edgar Cruz </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,391.00 </td>
	<td>256.73 </td>
	<td>0.00 </td>
	<td>89.26 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>99.70 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>3 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>259 </td>
	<td>0 </td>
	<td>244 </td>
	<td>14 </td>
	<td>5.41 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>20 </td>
	<td>15 </td>
	<td>383.98 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>35 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>14.34 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$470,592.99 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>24 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$295,576.60 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>5 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>17 </td>
	<td>0 </td>
	<td>1 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>15 </td>
	<td>Edward Fuentes </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,927.00 </td>
	<td>204.93 </td>
	<td>0.00 </td>
	<td>89.37 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>98.54 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>151 </td>
	<td>0 </td>
	<td>141 </td>
	<td>12 </td>
	<td>7.95 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>13 </td>
	<td>10 </td>
	<td>556.63 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>28 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>19.86 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$290,852.75 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>14 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$77,856.30 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>2 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>4 </td>
	<td>0 </td>
	<td>1 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>24 </td>
	<td>Enrique Alcocer </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,255.00 </td>
	<td>293.55 </td>
	<td>7.12 </td>
	<td>86.98 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>97.41 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>194 </td>
	<td>0 </td>
	<td>172 </td>
	<td>8 </td>
	<td>4.12 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>26 </td>
	<td>22 </td>
	<td>449.89 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>28 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>16.28 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$464,119.81 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>15 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$182,075.15 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>3 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>8 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>20 </td>
	<td>Estrella Benitez </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,471.00 </td>
	<td>250.58 </td>
	<td>0.00 </td>
	<td>89.86 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>191 </td>
	<td>0 </td>
	<td>176 </td>
	<td>13 </td>
	<td>6.81 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>17 </td>
	<td>15 </td>
	<td>532.81 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>37 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>21.02 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$472,808.20 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>19 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$198,525.52 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>3 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>8 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>18 </td>
	<td>Francisco Aguilar </td><td></td></td>
	<td>Rafael Acosta </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,730.00 </td>
	<td>177.72 </td>
	<td>0.00 </td>
	<td>89.73 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>98.62 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>1 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>116 </td>
	<td>0 </td>
	<td>112 </td>
	<td>2 </td>
	<td>1.72 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>7 </td>
	<td>4 </td>
	<td>718.47 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>31 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>27.68 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$412,187.56 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>27 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$372,776.44 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>8 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>26 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>22 </td>
	<td>Geronimo Torres </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,800.00 </td>
	<td>243.07 </td>
	<td>8.87 </td>
	<td>86.50 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>74.33 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>1 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>156 </td>
	<td>0 </td>
	<td>155 </td>
	<td>12 </td>
	<td>7.69 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>5 </td>
	<td>1 </td>
	<td>410.06 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>27 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>17.42 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$310,650.81 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>18 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$254,818.34 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>3 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>7 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>244 </td>
	<td>Hector Alabat </td><td></td></td>
	<td>Rafael Acosta </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,550.00 </td>
	<td>42.63 </td>
	<td>9.17 </td>
	<td>97.25 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>108.82 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>113 </td>
	<td>0 </td>
	<td>111 </td>
	<td>10 </td>
	<td>8.85 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>4 </td>
	<td>2 </td>
	<td>755.04 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>22 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>19.82 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$419,193.06 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>14 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$264,707.39 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>6 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>18 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>207 </td>
	<td>Israel Briseno </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,290.00 </td>
	<td>165.82 </td>
	<td>0.00 </td>
	<td>92.76 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>170 </td>
	<td>0 </td>
	<td>159 </td>
	<td>8 </td>
	<td>4.71 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>12 </td>
	<td>11 </td>
	<td>572.75 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>42 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>26.42 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$376,487.61 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>22 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$151,127.20 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>1 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>3 </td>
	<td>0 </td>
	<td>2 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>37 </td>
	<td>Ivette Ortega </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td>Rafael Acosta </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>4 </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$-3,574.15 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$-3,574.15 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>233 </td>
	<td>Jenilee Ayala </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,051.00 </td>
	<td>200.45 </td>
	<td>17.28 </td>
	<td>90.23 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>175 </td>
	<td>0 </td>
	<td>155 </td>
	<td>9 </td>
	<td>5.14 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>28 </td>
	<td>20 </td>
	<td>489.72 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>28 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>18.06 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$350,477.89 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>20 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$172,310.13 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>4 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>12 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>237 </td>
	<td>Jessica Cordova </td><td></td></td>
	<td>Rafael Acosta </td>
	<td>4 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,319.00 </td>
	<td>31.22 </td>
	<td>0.00 </td>
	<td>97.63 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>69 </td>
	<td>0 </td>
	<td>69 </td>
	<td>12 </td>
	<td>17.39 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>1,046.32 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>23 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>33.33 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$427,335.48 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>12 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$131,889.96 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>1 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>4 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>25 </td>
	<td>Joel Zavala </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,468.00 </td>
	<td>243.68 </td>
	<td>14.93 </td>
	<td>90.13 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>98.42 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>183 </td>
	<td>0 </td>
	<td>165 </td>
	<td>7 </td>
	<td>3.83 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>20 </td>
	<td>18 </td>
	<td>532.38 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>30 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>18.18 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$450,273.87 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>17 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$223,280.94 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>6 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>14 </td>
	<td>0 </td>
	<td>2 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>6 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>239 </td>
	<td>Karina Gonzalez </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,095.00 </td>
	<td>33.27 </td>
	<td>0.00 </td>
	<td>96.96 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>75.00 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>85 </td>
	<td>0 </td>
	<td>82 </td>
	<td>1 </td>
	<td>1.18 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>4 </td>
	<td>3 </td>
	<td>624.32 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>25 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>30.49 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$235,209.64 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>18 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$164,663.05 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>1 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>4 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>15 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>27 </td>
	<td>Karla Castillo </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,427.00 </td>
	<td>342.73 </td>
	<td>0.00 </td>
	<td>85.88 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>99.96 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>1 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>176 </td>
	<td>0 </td>
	<td>168 </td>
	<td>6 </td>
	<td>3.41 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>15 </td>
	<td>8 </td>
	<td>556.09 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>26 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>15.48 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$227,920.56 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>20 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$131,767.29 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>1 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>3 </td>
	<td>0 </td>
	<td>5 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>20 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>30 </td>
	<td>Kenny Morales </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,412.00 </td>
	<td>205.27 </td>
	<td>7.42 </td>
	<td>91.49 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>165 </td>
	<td>0 </td>
	<td>161 </td>
	<td>4 </td>
	<td>2.42 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>13 </td>
	<td>4 </td>
	<td>644.62 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>25 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>15.53 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$258,498.57 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>15 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$108,221.66 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>235 </td>
	<td>Lilia Gonzalez </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,850.00 </td>
	<td>127.67 </td>
	<td>0.00 </td>
	<td>93.10 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>195 </td>
	<td>0 </td>
	<td>181 </td>
	<td>12 </td>
	<td>6.15 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>19 </td>
	<td>14 </td>
	<td>399.82 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>22 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>12.15 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$345,423.68 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>12 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$190,127.40 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>9048 </td>
	<td>Lilia Rojas </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td> </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>9 </td>
	<td>Lisseth Perez </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,963.00 </td>
	<td>265.63 </td>
	<td>0.00 </td>
	<td>86.47 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>99.94 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>1 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>145 </td>
	<td>0 </td>
	<td>143 </td>
	<td>9 </td>
	<td>6.21 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>6 </td>
	<td>2 </td>
	<td>488.13 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>17 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>11.89 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$173,326.92 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>11 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$69,813.36 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>243 </td>
	<td>Lucila Menez </td><td></td></td>
	<td>Rafael Acosta </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,446.00 </td>
	<td>40.55 </td>
	<td>3.65 </td>
	<td>97.20 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>78.89 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>3 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>103 </td>
	<td>0 </td>
	<td>103 </td>
	<td>14 </td>
	<td>13.59 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>1 </td>
	<td>0 </td>
	<td>724.66 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>22 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>21.36 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$266,823.56 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>14 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$97,995.02 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>2 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>7 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>242 </td>
	<td>Marco Rosas </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,473.00 </td>
	<td>60.27 </td>
	<td>0.00 </td>
	<td>95.91 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>80.00 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>132 </td>
	<td>0 </td>
	<td>125 </td>
	<td>7 </td>
	<td>5.30 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>13 </td>
	<td>7 </td>
	<td>535.35 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>21 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>16.80 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$233,925.83 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>8 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>$29,114.15 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>1 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>245 </td>
	<td>Maria Mondragon </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,451.00 </td>
	<td>216.07 </td>
	<td>23.05 </td>
	<td>91.18 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>177 </td>
	<td>0 </td>
	<td>157 </td>
	<td>4 </td>
	<td>2.26 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>27 </td>
	<td>20 </td>
	<td>637.56 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>37 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>23.57 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$500,485.17 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>23 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$304,438.68 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>3 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>5 </td>
	<td>0 </td>
	<td>1 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>31 </td>
	<td>Martha Tavarez </td><td></td></td>
	<td>Fabrizio Bond </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,856.00 </td>
	<td>191.80 </td>
	<td>0.00 </td>
	<td>89.67 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>165 </td>
	<td>0 </td>
	<td>155 </td>
	<td>17 </td>
	<td>10.30 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>11 </td>
	<td>10 </td>
	<td>516.76 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>27 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>17.42 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$376,622.46 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>18 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$200,552.30 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>32 </td>
	<td>Nachyelli Cancino </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,951.00 </td>
	<td>311.65 </td>
	<td>0.00 </td>
	<td>84.03 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>97.19 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>140 </td>
	<td>0 </td>
	<td>136 </td>
	<td>10 </td>
	<td>7.14 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>12 </td>
	<td>4 </td>
	<td>504.20 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>28 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>20.59 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$382,354.13 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>19 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$172,256.63 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>4 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>12 </td>
	<td>0 </td>
	<td>1 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>3 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>34 </td>
	<td>Omar Villada </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,554.00 </td>
	<td>150.12 </td>
	<td>0.00 </td>
	<td>90.34 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>79.01 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>75 </td>
	<td>0 </td>
	<td>72 </td>
	<td>1 </td>
	<td>1.33 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>4 </td>
	<td>3 </td>
	<td>869.12 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>24 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>33.33 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$225,236.15 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>18 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$135,668.08 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>6 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>12 </td>
	<td>0 </td>
	<td>1 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>23 </td>
	<td>Oswaldo Ibarra </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,911.00 </td>
	<td>320.90 </td>
	<td>0.00 </td>
	<td>83.21 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>96.98 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>180 </td>
	<td>0 </td>
	<td>169 </td>
	<td>6 </td>
	<td>3.33 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>15 </td>
	<td>11 </td>
	<td>377.49 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>30 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>17.75 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$458,909.45 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>16 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$82,415.92 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>2 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>2 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>7 </td>
	<td>Priscila Victoria </td><td></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,834.00 </td>
	<td>188.57 </td>
	<td>10.33 </td>
	<td>89.72 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>87.60 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>128 </td>
	<td>0 </td>
	<td>121 </td>
	<td>8 </td>
	<td>6.25 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>12 </td>
	<td>7 </td>
	<td>522.84 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>30 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>24.79 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$336,200.02 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>15 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$104,102.96 </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>1 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>5 </td>
	<td>0 </td>
	<td>1 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>7 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>10 </td>
	<td>Roberto Cervantes </td><td></td></td>
	<td>Rafael Acosta </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>2,095.00 </td>
	<td>178.28 </td>
	<td>0.00 </td>
	<td>91.49 % </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>100.60 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>176 </td>
	<td>0 </td>
	<td>167 </td>
	<td>21 </td>
	<td>11.93 % </td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td></td>
	<td>17 </td>
	<td>9 </td>
	<td>607.14 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>29 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>17.37 % </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>$459,609.27 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>24 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$355,552.29 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>6 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>23 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>9050 </td>
	<td>Siriel Jacobo </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td> </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>8 </td>
	<td>Tibisay Romero </td><td></td></td>
	<td>Eynar Rodriguez </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>1,952.00 </td>
	<td>33.08 </td>
	<td>0.00 </td>
	<td>98.31 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>100.00 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>129 </td>
	<td>0 </td>
	<td>119 </td>
	<td>1 </td>
	<td>0.78 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>12 </td>
	<td>10 </td>
	<td>616.05 </td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td></td>
	<td>28 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>23.53 % </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>$515,787.99 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>17 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>$160,478.32 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>3 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>14 </td>
	<td>0 </td>
	<td>0 </td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
	<td>0 </td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td></td>
	<td>0 </td>
	<td>0 </td>
</tr>
<tr style='text-align: center'>
	<td>9052 </td>
	<td>Valeria Rodriguez </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td> </td>
	<td>6 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td>$0.00 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
<tr style='text-align: center'>
	<td>39 </td>
	<td>Veronica Gongora </td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td></td>
	<td>Paulyna Gomez </td>
	<td>8 </td>
	<td>3 </td>
	<td>2016-05-01<br>a<br>2016-05-05 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 </td>
	<td>0.00 % </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>0 </td><td></td></td>
	<td>0 </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td> </td>
	<td>0.00 % </td><td></td></td>
	<td> </td>
	<td> </td>
	<td>0.00 </td><td></td></td>
	<td>1 </td><td></td></td>
	<td>0.00 % </td><td></td></td>
	<td>$12,603.96 </td><td></td></td>
	<td>1 </td><td></td></td>
	<td>$10,475.52 </td><td></td></td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
	<td> </td><td></td></td>
	<td> </td>
	<td> </td>
</tr>
    </tbody>
</table>
</div>
<br>
<table id='info_aht' style='font-size:12px; vertical-align: middle; margin:auto; width:400px;'>
    <thead>
        <tr>
            <th>Supervisor</th>
            <th>AHT</th>
        </tr>
    </thead>
    <tbody>
        <tr><td></td><td style='text-align:center'>364.14</td></tr>
	<tr><td>Cristian Calderon</td><td style='text-align:center'>464.54</td></tr>
	<tr><td>Edgar Canul</td><td style='text-align:center'>461.12</td></tr>
	<tr><td>Eynar Rodriguez</td><td style='text-align:center'>580.74</td></tr>
	<tr><td>Fabrizio Bond</td><td style='text-align:center'>532.71</td></tr>
	<tr><td>Paulyna Gomez</td><td style='text-align:center'>470.41</td></tr>
	<tr><td>Rafael Acosta</td><td style='text-align:center'>642.96</td></tr>

    </tbody>
    </table>
<br>
<div class='qlegend' id='qlegend'><p id='qtlegend'></p></div>

</div>
<style>
/* optional styling */
caption {
  /* override bootstrap adding 8px to the top & bottom of the caption */
  padding: 0;
}
.ui-sortable-placeholder {
  /* change placeholder (seen while dragging) background color */
  background: #ddd;
}
div.table-handle-disabled {
  /* optional red background color indicating a disabled drag handle */
  background-color: rgba(255,128,128,0.5);
  /* opacity set to zero for disabled handles in the dragtable.mod.css file */
  opacity: 0.7;
}
/* fix cursor */
.tablesorter-blue .tablesorter-header {
  cursor: default;
}
.sorter {
  cursor: pointer;
}
</style>

<script type="text/javascript" src="/js/tablesorter/js/extras/jquery.dragtable.mod.js"></script>
<script>

$(document).ready(function()
    {
        $('#qlegend').hide();

        $('#info, #info_aht').tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output' , 'stickyHeaders',  'resizable'],
            widgetOptions: {

               resizable_addLastColumn : true,
               resizable_widths : [ ,,,,,,'65px' ],
               uitheme: 'jui',
                columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_tfoot: false,
                columns_thead: true,
                filter_childRows: false,
                filter_columnFilters: true,
                filter_cssFilter: "tablesorter-filter",
                filter_functions: null,
                filter_hideFilters: false,
                filter_ignoreCase: true,
                filter_reset: null,
                filter_searchDelay: 300,
                filter_startsWith: false,
                filter_useParsedData: false,
                resizable: true,
                saveSort: true,
                output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                output_ignoreColumns : [0],          // columns to ignore [0, 1,... ] (zero-based index)
                output_hiddenColumns : false,       // include hidden columns in the output
                output_includeFooter : true,        // include footer rows in the output
                output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                output_headerRows    : true,        // output all header rows (multiple rows)
                output_delivery      : 'd',         // (p)opup, (d)ownload
                output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                output_replaceQuote  : '\u201c;',   // change quote to left double quote
                output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                output_wrapQuotes    : false,       // wrap every cell output in quotes
                output_popupStyle    : 'width=580,height=310',
                output_saveFileName  : 'cuartiles___3.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                stickyHeaders_attachTo : '#container-cuartiles'


            }
        });

        $("#config").tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'uitheme','zebra', 'stickyHeaders'],
            widgetOptions: {
               uitheme: 'jui',
                columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_tfoot: false,
                columns_thead: true,
                filter_childRows: false,
                filter_columnFilters: true,
                filter_cssFilter: "tablesorter-filter",
                filter_functions: null,
                filter_hideFilters: false,
                filter_ignoreCase: true,
                filter_reset: null,
                filter_searchDelay: 300,
                filter_startsWith: false,
                filter_useParsedData: false,
                resizable: true,
                saveSort: true,
                stickyHeaders_attachTo : '#config-contain'


            }
        });

        $( "#accordion, #accordion_sups" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });

        $('#export').click(function(){
            $('#info').trigger('outputTable');

        });




         $('.group_select').click(function(){
            var tipo;
            var numero;
            var grupo;


            tipo=$(this).attr('tipo');
            numero=$(this).attr('number');

            if(tipo==1){grupo="Comportamental "+numero+": ";}else{grupo="Desarrollable "+numero+": ";}

            var tmp;
            var legend="";
            var Utilizacion_filter='';
 var Utilizacion_flag=0;
 var separator_Utilizacion='';
 var Adherencia_filter='';
 var Adherencia_flag=0;
 var separator_Adherencia='';
 var Colgadas_filter='';
 var Colgadas_flag=0;
 var separator_Colgadas='';
 var AHT_filter='';
 var AHT_flag=0;
 var separator_AHT='';
 var Retardos_filter='';
 var Retardos_flag=0;
 var separator_Retardos='';
 var Faltas_filter='';
 var Faltas_flag=0;
 var separator_Faltas='';
 var Localizadores_filter='';
 var Localizadores_flag=0;
 var separator_Localizadores='';
 var Cancelaciones_filter='';
 var Cancelaciones_flag=0;
 var separator_Cancelaciones='';
 var FC_filter='';
 var FC_flag=0;
 var separator_FC='';
 var Monto_filter='';
 var Monto_flag=0;
 var separator_Monto='';
 var Calidad_filter='';
 var Calidad_flag=0;
 var separator_Calidad='';
             var i=1;
            while(i<=4){
                if($('#c_Utilizacion_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Utilizacion_flag!=0){separator_Utilizacion='|';}

                                if(tmp==1){Utilizacion_filter=Utilizacion_filter+separator_Utilizacion+i;
                                Utilizacion_flag=Utilizacion_flag+1;
                                legend=legend+' Utilizacion Q'+i+' // ';
                                }

if($('#c_Adherencia_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Adherencia_flag!=0){separator_Adherencia='|';}

                                if(tmp==1){Adherencia_filter=Adherencia_filter+separator_Adherencia+i;
                                Adherencia_flag=Adherencia_flag+1;
                                legend=legend+' Adherencia Q'+i+' // ';
                                }

if($('#c_Colgadas_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Colgadas_flag!=0){separator_Colgadas='|';}

                                if(tmp==1){Colgadas_filter=Colgadas_filter+separator_Colgadas+i;
                                Colgadas_flag=Colgadas_flag+1;
                                legend=legend+' Colgadas Q'+i+' // ';
                                }

if($('#c_AHT_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(AHT_flag!=0){separator_AHT='|';}

                                if(tmp==1){AHT_filter=AHT_filter+separator_AHT+i;
                                AHT_flag=AHT_flag+1;
                                legend=legend+' AHT Q'+i+' // ';
                                }

if($('#c_Retardos_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Retardos_flag!=0){separator_Retardos='|';}

                                if(tmp==1){Retardos_filter=Retardos_filter+separator_Retardos+i;
                                Retardos_flag=Retardos_flag+1;
                                legend=legend+' Retardos Q'+i+' // ';
                                }

if($('#c_Faltas_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Faltas_flag!=0){separator_Faltas='|';}

                                if(tmp==1){Faltas_filter=Faltas_filter+separator_Faltas+i;
                                Faltas_flag=Faltas_flag+1;
                                legend=legend+' Faltas Q'+i+' // ';
                                }

if($('#c_Localizadores_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Localizadores_flag!=0){separator_Localizadores='|';}

                                if(tmp==1){Localizadores_filter=Localizadores_filter+separator_Localizadores+i;
                                Localizadores_flag=Localizadores_flag+1;
                                legend=legend+' Localizadores Q'+i+' // ';
                                }

if($('#c_Cancelaciones_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Cancelaciones_flag!=0){separator_Cancelaciones='|';}

                                if(tmp==1){Cancelaciones_filter=Cancelaciones_filter+separator_Cancelaciones+i;
                                Cancelaciones_flag=Cancelaciones_flag+1;
                                legend=legend+' Cancelaciones Q'+i+' // ';
                                }

if($('#c_FC_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(FC_flag!=0){separator_FC='|';}

                                if(tmp==1){FC_filter=FC_filter+separator_FC+i;
                                FC_flag=FC_flag+1;
                                legend=legend+' FC Q'+i+' // ';
                                }

if($('#c_Monto_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Monto_flag!=0){separator_Monto='|';}

                                if(tmp==1){Monto_filter=Monto_filter+separator_Monto+i;
                                Monto_flag=Monto_flag+1;
                                legend=legend+' Monto Q'+i+' // ';
                                }

if($('#c_Calidad_'+tipo+numero+i).is(':checked')){

                                    tmp=1;

                                }else{

                                    tmp=0;

                                }

                                if(Calidad_flag!=0){separator_Calidad='|';}

                                if(tmp==1){Calidad_filter=Calidad_filter+separator_Calidad+i;
                                Calidad_flag=Calidad_flag+1;
                                legend=legend+' Calidad Q'+i+' // ';
                                }


            i=i+1;
            }



           var filters = [],
              col = '31', // zero-based index
              txt = FC_filter; // text to add to filter

            filters['11'] = Utilizacion_filter;
            filters['13'] = Adherencia_filter;
            filters['23'] = Colgadas_filter;
            filters['27'] = AHT_filter;
            filters['15'] = Retardos_filter;
            filters['17'] = Faltas_filter;
            filters['29'] = Localizadores_filter;
            filters['31'] = FC_filter;
            filters['33'] = Monto_filter;

            // using "table.hasFilters" here to make sure we aren't targeting a sticky header
            $.tablesorter.setFilters( $('#info'), filters, true ); // new v2.9

            if(tipo!=""){
                document.getElementById('qtlegend').innerText=grupo+legend;
                $('#qlegend').show();
            }else{
                document.getElementById('qtlegend').innerText="";
                $('#qlegend').hide();
            }
            return false;
          });

          $('#info').trigger('refreshColumnSelector', [ [2,3,4] ]);
    }
);

</script>
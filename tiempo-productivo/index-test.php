<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$menu_asesores="class='active'";
date_default_timezone_set('America/Bogota');


?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");


include("../common/scripts.php");
include("../common/list_asesores.php");
include("../common/menu.php");

if($_POST['user']!=""){
	$query="SELECT * FROM Asesores a, userDB b WHERE a.Usuario=b.username AND id='".$_POST['user']."'";
	$user=mysql_result(mysql_query($query),0,'userid');
}else{
	$user=$_SESSION['id']; 
	$query="SELECT * FROM Asesores a, userDB b WHERE a.Usuario=b.username AND userid='".$user."'";
}
$esquema=mysql_result(mysql_query($query),0,'Esquema');
$asesor=mysql_result(mysql_query($query),0,'id');
$name=mysql_result(mysql_query($query),0,'N Corto');
if(isset($_POST['dep'])){$dep=$_POST['dep'];}else{$dep=$_SESSION['dep'];}
?>

<script type="text/javascript">
  google.charts.load("current", {packages:["timeline"]});
  google.charts.setOnLoadCallback(drawChartOK);
  function drawChartOK() {
    var container = document.getElementById('timeline');
    var chart = new google.visualization.Timeline(container);

    var options = {
      timeline: { groupByRowLabel: true }
    };

    function drawChart(){
      var jsonData = $.ajax({
          url: "../json/getData_ses_pauses.php?usuario=<?php echo $user; ?>",
          dataType: "json",

          async: false
          }).responseText;

      var data = new google.visualization.DataTable(jsonData);

      chart.draw(data, options);

      }

    drawChart();

    setInterval(function(){ drawChart() }, 30000);

  }
</script>
<script>


$(function(){

    $('#historial').click(function(){
           window.open("/noti/historial-notificaciones.php", "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=100, left=50, width=800, height=600")
        });

    function RequestInf(){
        if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById('tiempos').innerHTML = xmlhttp.responseText;

                }
            }

            xmlhttp.open("GET","../json/get_table_tiempos.php?esquema=<?php echo $esquema; ?>&asesor=<?php echo $asesor; ?>",true);
            xmlhttp.send();
            
    }
    RequestInf();
    setInterval(function(){ RequestInf() }, 30000);

});

</script>

<?php

$query="SELECT TIME_TO_SEC(Tiempo) as Tiempo FROM PNP_tiempos WHERE Esquema=$esquema";
$pausa_disponible=mysql_result(mysql_query($query),0,'Tiempo');
$query="SELECT TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio) as tiempo FROM Comidas a, Tipos_pausas b WHERE a.tipo=b.pausa_id AND asesor=$asesor AND Fecha='".date('Y-m-d')."' AND Seleccionables=1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$pausas_cant=$num;
$i=0;
while($i<$num){
    $pausas_time+=intval(mysql_result($result,$i,'tiempo'));
$i++;
}
$pausas_temp="00:00:00";
$pausas_tiempo=date('H:i:s', strtotime($pausas_temp ."+ $pausas_time seconds"));
$pausas_rest_temp=$pausa_disponible-$pausas_time;
if($pausas_rest_temp<0){
    $pausas_restante="00:00:00";
    $pausas_rest_temp*=((-1));
    $pausas_excedido=date('H:i:s', strtotime($pausas_temp ."+ $pausas_rest_temp seconds"));
}else{
    $pausas_excedido="00:00:00";
    $pausas_restante=date('H:i:s', strtotime($pausas_temp ."+ $pausas_rest_temp seconds"));
}


$startmonth=date('Y-m').'-01';
$endmonth=date('Y-m-d',strtotime(date('Y-m',strtotime($startmonth.' +1 month'))."-01 -1 days"));

$qmeta="SELECT Meta_Individual, Meta_Diaria FROM metas WHERE mes=".date('m')." AND anio=".date('Y')." AND skill=$dep";
$meta=mysql_result(mysql_query($qmeta),0,'Meta_Individual');
$metad=mysql_result(mysql_query($qmeta),0,'Meta_Diaria');

?>
<style>
.tablesorter tbody > tr > td[contenteditable=true]:focus {
  outline: #08f 1px solid;
  background: #eee;
  resize: none;
}
td.no-edit, span.no-edit {
  background-color: rgba(230,191,153,0.5);
}
.focused {
  color: blue;
}
td.editable_updated {
  background-color: green;
  color: red;
}
</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script>


var status;
function sendRequestForm(id,field,newVal){
        $( "#process" ).dialog("open");
        var urlsend= "/json/formularios/asesores_update.php?id="+id+"&field="+field+"&newVal="+newVal;
        //document.getElementById('testresult').innerText=urlsend;
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
                var status = text.match("status- (.*) -status");
                 var startlogin='no';
                var notif_msg = text.match("msg- (.*) -msg");
                if(status[1]=='OK'){
                    tipo_noti='success';
                    $('#d'+id).hide('slow', function(){ $('#d'+id).remove(); });
                    status=true;
                }else{
                    tipo_noti='error';
                    status=false;
                }
                $( "#process" ).dialog("close");
                new noty({
                    text: notif_msg[1],
                    type: tipo_noti,
                    timeout: 10000,
                    animation: {
                        open: {height: 'toggle'}, // jQuery animate function property object
                        close: {height: 'toggle'}, // jQuery animate function property object
                        easing: 'swing', // easing
                        speed: 500 // opening & closing animation speed
                    }
                });

            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();


    }

  $(function() {
    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });
    $( "#Ingreso, #Egreso, #Fecha Nacimiento" ).datepicker({
        dateFormat: "yyyy/mm/dd"
    });

    var validation;

     function checkRegexp( o, regexp) {
      if ( !( regexp.test( o ) ) ) {
        return false;
      } else {
        return true;
      }
    }


    $('.tablesorter').tablesorter({
        theme: 'blue',
        headerTemplate: '{content}',
        widthFixed: false,
        widgets: [ 'zebra','editable' ],
        widgetOptions: {

           uitheme: 'jui',
            columns: [
                "primary",
                "secondary",
                "tertiary"
                ],
            columns_tfoot: false,
            columns_thead: true,
            editable_columns       : [3,4,5],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
            editable_autoResort    : false,         // auto resort after the content has changed.
            editable_validate      : function(txt, orig, columnIndex, $element){
                                        if(txt==""){
                                                validation=true;
                                                if(columnIndex==3 || columnIndex==4){
                                                    return "##########";
                                                }else{
                                                    return "###@##.com";
                                                }
                                        }else{
                                            if(columnIndex==3 || columnIndex==4){
                                                var t = /(?:^|\s)([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])(?=\s|$)/.test(txt);
                                                validation=t;
                                                var mensaje="El formato telefonico debe ser de 10 numeros sin espacios";
                                            }else{
                                                var t = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(txt);
                                                validation=t;
                                                var mensaje="El formato de correo electronico no es correcto";
                                            }

                                        }
                                        // only allow one word

                                        if(t==false){

                                            new noty({
                                                text: mensaje,
                                                type: "error",
                                                timeout: 10000,
                                                animation: {
                                                    open: {height: 'toggle'}, // jQuery animate function property object
                                                    close: {height: 'toggle'}, // jQuery animate function property object
                                                    easing: 'swing', // easing
                                                    speed: 500 // opening & closing animation speed
                                                }
                                            });
                                             return orig;
                                        }else{
                                            return txt;
                                        }
                                      },          // return a valid string: function(text, original, columnIndex){ return text; }
            editable_focused       : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.addClass('focused');
            },
            editable_blur          : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.removeClass('focused');
            },
            editable_selectAll     : function(txt, columnIndex, $element){
              // note $element is the div inside of the table cell, so use $element.closest('td') to get the cell
              // only select everthing within the element when the content starts with the letter "B"
              return /^b/i.test(txt) && columnIndex === 0;
            },
            editable_wrapContent   : '<div>',       // wrap all editable cell content... makes this widget work in IE, and with autocomplete
            editable_trimContent   : true,          // trim content ( removes outer tabs & carriage returns )
            editable_noEdit        : 'no-edit',     // class name of cell that is not editable
            editable_editComplete  : 'editComplete' // event fired after the table content has been edited

        }
    }).children('tbody').on('editComplete', 'td', function(event, config){
      var $this = $(this),
        newContent = $this.text(),
        cellIndex = this.cellIndex, // there shouldn't be any colspans in the tbody
        rowIndex = $this.closest('tr').attr('id'),// data-row-index stored in row id
        col = $(this).attr('col');
        if(validation==true){
            sendRequestForm(rowIndex,col,newContent);
        }

      // Do whatever you want here to indicate
      // that the content was updated
      $this.addClass( 'editable_updated' ); // green background + white text
      setTimeout(function(){
        $this.removeClass( 'editable_updated' );
      }, 500);

      /*
      $.post("mysite.php", {
        "row"     : rowIndex,
        "cell"    : cellIndex,
        "content" : newContent
      });
      */
    });
  });
</script>
<table style='width:80%; margin: auto' class='t2'>
    <tr class='title'>
        <th>¡Bienvenido <? echo $_SESSION['name']; ?>!</th>

    <?php if($_SESSION['select_user_viewhome']!=1){goto NoViewHome;} ?>
        <form action='<?php $_SERVER['PHP_SELF']; ?>' method='POST'>
        <th  width='10%'>Departamento</th>
        <th class='pair'  width='10%'><select name="dep" onchange='this.form.submit();'><?php list_departamentos($dep); ?></select></th>        <th width='30%'>Asesor</th>
        <th class='pair'  width='10%'><?php listAsesores('user',1,$dep,1,$asesor); ?></th>
        <th class='total'  width='10%'><input type="submit" /></th>
        </form>
    <?php NoViewHome: ?>
    </tr>
</table>
<?php
$query="SELECT
            Nombre, num_colaborador, id, RFC, telefono1, telefono2, correo_personal
        FROM
            Asesores
        WHERE
            id=".$_SESSION['asesor_id'];
$result=mysql_query($query);
$id=mysql_result($result,0,'id');
$nombre=mysql_result($result,0,'nombre');
$num_colaborador=mysql_result($result,0,'num_colaborador');
$RFC=mysql_result($result,0,'RFC');
$telefono2=mysql_result($result,0,'telefono2');
$telefono1=mysql_result($result,0,'telefono1');
$correo_personal=mysql_result($result,0,'correo_personal');
if($correo_personal==NULL){$correo_personal="@";}
if($telefono2==NULL){$telefono2="#";}
if($telefono1==NULL){$telefono1="#";}  

?>
<br>

<table class='tablesorter' style='text-align:center; width:950px; margin: auto'>
<thead>
    <tr>
    <th>Nombre</th>
    <th>Colaborador</th>
    <th>RFC</th>
    <th>Tel 1</th>
    <th>Tel 2</th>
    <th>Correo Personal</th>
    </tr>
</thead>
<tbody>
    <tr id='<?php echo $id; ?>'>
        <td col='Nombre'><?php echo $nombre; ?></td>
        <td col='num_colaborador'><?php echo $num_colaborador; ?></td>
        <td col='RFC'><?php echo $RFC; ?></td>
        <td col='telefono1' style='background:white'><?php echo $telefono1; ?></td>
        <td col='telefono2' style='background:white'><?php echo $telefono2; ?></td>
        <td col='correo_personal' style='background:white'><?php echo $correo_personal; ?></td>
    </tr>
</tbody>
</table>
<br>
<?
    if(isset($_POST['dep'])){$depart=$dep;}else{$depart=$_SESSION['dep'];}
    switch($depart){
        case 3:
            include("ventas.php");
            break;
    }
?>
<br>
<table style='width:80%; margin: auto' class='t2'>
    <tr class='title'>
        <th colspan=100>Sesion del dia</th>
    </tr>
    <tr class='title'>
        <td>Esquema</td>
        <td>Total de<br>Pausas No Productivas</td>
        <td>Tiempo total en<br>Pausa no Productiva</td>
        <td>Tiempo restante para<br>Pausas No Productivas</td>
        <td>Tiempo Excedido de<br>Pausas No Productivas</td>
    </tr>
    <tr class='odd' id='tiempos'>

    </tr>
    <tr class='pair'>
        <td colspan=100 id='timeline' ></td>
    </tr>
    <tr class='total'>
        <td colspan=100><input type="button" id='historial' Value='Historial' /></td>
    </tr>
</table> <br>




<?php if($_SESSION['performance_dia_ventas']==1){ ?>

<div style='width:80%; background: navy; margin:auto;'>
    <div style='float: right; '>
    <a href="/reporte-performance-venta-dia/"><button class='buttonlarge button_redpastel_w' id='Performance_dia' >Performance Ventas</button></a>
    </div>
</div>

<?php } ?>


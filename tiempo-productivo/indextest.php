<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$menu_asesores="class='active'";
date_default_timezone_set('America/Bogota');
header("Content-Type: text/html;charset=utf-8");

include("../connectDB.php");
include("../common/scripts.php");
include("../common/list_asesores.php");
include("../common/menu.php");

$cun_time = new DateTimeZone('America/Bogota');

if($_POST['user']!=""){
	$query="SELECT userid, Esquema, id, `N Corto` FROM Asesores a, userDB b WHERE a.Usuario=b.username AND id='".$_POST['user']."'";
	if($result=$connectdb->query($query)){
		$fila=$result->fetch_assoc();
		$user=$fila['userid'];	
	}
}else{
	$user=$_SESSION['id']; 
	$query="SELECT Esquema, id, `N Corto` FROM Asesores a, userDB b WHERE a.Usuario=b.username AND userid='".$user."'";
	if($result=$connectdb->query($query)){
		$fila=$result->fetch_assoc();
	}
}
$esquema=$fila['Esquema'];
$asesor=$fila['id'];
$name=$fila['N Corto'];

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

    setInterval(function(){ drawChart() }, 60000);

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
if($result=$connectdb->query($query)){
	$fila=$result->fetch_assoc();
	$pausa_disponible=$fila['Tiempo'];	
}
$query="SELECT TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio) as tiempo FROM Comidas a, Tipos_pausas b WHERE a.tipo=b.pausa_id AND asesor=$asesor AND Fecha='".date('Y-m-d')."' AND Seleccionables=1";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$pausas_time+=intval($fila['tiempo']);
	}	
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
  	
  	dialogLoad=$( "#dialog-load" ).dialog({
      modal: true,
      autoOpen: false
    });
    
    confirmLogout=$( "#dialog-message" ).dialog({
      modal: true,
      autoOpen: false,
      width: 500,
      buttons: [
		    {
		      text: "Cancel",
		      click: function() {
		        $( this ).dialog( "close" );
		      }
		 	}
		  ],
		close: function() {
			$('#check_horario').prop('checked', false);
			$('#ok_logout').hide();
			
		}
    });
    
    okLogout=$( "#dialog-message-out" ).dialog({
      modal: true,
      autoOpen: false,
      buttons: [
		    {
		      text: "OK",
		      click: function() {
		        $( this ).dialog( "close" );
		      }
		 	}
		  ],
		close: function() {
			window.location.href = "http://pt.comeycome.com";
		}
    });
    
    $('#ok_logout').hide();
  	
    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });
    $( "#Ingreso, #Egreso, #Fecha Nacimiento" ).datepicker({
        dateFormat: "yyyy/mm/dd"
    });
    
    <?php if($_POST['user']==$_POST['asesor_id'] || !isset($_POST['user'])){echo "$('#visa, #pasaporte').datepicker({
    	dateFormat: \"yy-mm-dd\"
    });";} ?>
    
    
    $('#visa, #pasaporte').change(function(){
    	sendRequestForm($(this).attr('index'),$(this).attr('col'),$(this).val());
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
            editable_columns       : [<?php if($_POST['user']==$_POST['asesor_id'] || !isset($_POST['user'])){echo "3,4,5";} ?>],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
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

    $('#viewAsesor').click(function(){
        $('#search').submit();
    });

    $('#viewAsesor').click(function(){
        $('#searchall').submit();
    });

    $('#dep').change(function(){
        var tmpval=$(this).val();
        $('#depall').val(tmpval);
    });
    
    $('#logout').click(function(){
    	$('.dynamic_data').remove();
    	dialogLoad.dialog('open');
    	getHorarioTm();
    	
    });

	$('#check_horario').change(function(){
		var flag = $(this).prop('checked');
		if(flag){
			$('#ok_logout').show();
		}else{
			$('#ok_logout').hide();
		}
	});
	
	$('#ok_logout').click(function(){
		confirmLogout.dialog('close');
		logOutSet();
	});
	
	progressbarload=$('#progressbarload').progressbar({
	      value: false
	});
	
	
	function getHorarioTm(){
		
		$.ajax({
			url: 'getHorarios.php',
			type: "POST",
            data: {asesor: '<?php echo $_SESSION['asesor_id']; ?>', fecha: '<?php if(intval(date('H'))<4){echo date('Y-m-d');}else{echo date('Y-m-d',strtotime('+1 day'));} ?>'},
            dataType: "json", // will automatically convert array to JavaScript
            success: function(array) {
            	data=array;
            	var table=$('#nd_horario');
            	
	            	for(i=0;i<=1;i++){
	            		var flag = (typeof data['fecha'+i] === 'undefined') ? false : true;
	            		if(flag){
	            			table.find('.title').append("<th class='dynamic_data'>"+data['fecha'+i]['fecha']+"</th>");
	            			table.find('.pair').append("<th class='dynamic_data'>"+data['fecha'+i]['horario']+"</th>");
	            		}
	            	}
	            	dialogLoad.dialog('close');
	            	confirmLogout.dialog('open');
            	
            },
            error: function(){
            	dialogLoad.dialog('close');
            	alert('Error al recibir informacion de Horarios. Intentalo nuevamente o revisa con GTR');
            	
            }
		});
	}
	
	function logOutSet(){
		dialogLoad.dialog('open');
		$.ajax({
			url: 'logout.php',
			type: "POST",
            data: {asesor: '<?php echo $_SESSION['asesor_id']; ?>', fecha: '<?php echo date('Y-m-d');?>', horario: $('#nd_horario').html()},
            dataType: "html",
            success: function(data) {
            	dialogLoad.dialog('close');
            	if(data=='Done'){
            		okLogout.dialog('open');
            	}else{
            		alert('Error, intenta de nuevo o acercate con GTR');
            	}
            },
            error: function(){
            	dialogLoad.dialog('close');
            	alert('Error al enviar info, intenta de nuevo o acercate con GTR');
            	
            }
		});
	}
  });
</script>
<table style='width:80%; margin: auto' class='t2'>
    <tr class='title'>
        <th>!Bienvenido <? echo $_SESSION['name']; ?>!</th>

    <?php if($_SESSION['select_user_viewhome']!=1){ $sesion_asesor=$_SESSION['asesor_id']; goto NoViewHome;}else{if(!isset($_POST['user'])){$sesion_asesor=$_SESSION['asesor_id'];}else{$sesion_asesor=$_POST['user'];}}
		
	 ?>
        <form id='search' action='<?php $_SERVER['PHP_SELF']; ?>' method='POST'>
        <th  width='10%'>Departamento</th>
        <th class='pair'  width='10%'><select id='dep' name="dep" onchange='this.form.submit();'><?php list_departamentos($dep); ?></select></th>        <th width='30%'>Asesor</th>
        <th class='pair'  width='10%'><?php listAsesores('user',1,$dep,1,$asesor); ?></th>
        <th class=''  width='10%'><button class='button button_red_w' id='viewAsesor'>Asesor</button></th>
        </form>
        <form  id='searchall' action='<?php $_SERVER['PHP_SELF']; ?>' method='POST'>
        <input type='hidden' id='depall' name='dep' value='<?php echo $dep; ?>'>
        <input type='hidden' id='tipo' name='tipo' value='all'>
        <th class=''  width='10%'><button class='button button_red_w' id='viewDay'>Todo</button></th>
        </form>
    <?php NoViewHome: ?>
		<th><button class='button button_red_w' id='logout'>Logout</button></th>
    </tr>
</table>

<?php


include("fam_aviso.php");


$query="SELECT
            Nombre, num_colaborador, id, RFC, telefono1, telefono2, correo_personal, Vigencia_Pasaporte, Vigencia_Visa, Locker
        FROM
            Asesores a 
        LEFT JOIN
        	Lockers b ON a.id=b.asesor
        WHERE
            id=".$sesion_asesor;
			
if($result=$connectdb->query($query)){
	$num_fields=$result->field_count;
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_row()){
		for($i=0;$i<$num_fields;$i++){
			$datos_personales[$fields[$i]->name]=utf8_encode($fila[$i]);
		}
	}
}

if($datos_personales['correo_personal']==NULL){$datos_personales['correo_personal']="@";}
if($datos_personales['telefono2']==NULL){$datos_personales['telefono2']="#";}
if($datos_personales['telefono1']==NULL){$datos_personales['telefono1']="#";}  

//Horarios
$query="SELECT 
	f.Fecha, `jornada start`, `jornada end`, getAusentismo(asesor,f.Fecha,1) as Ausentismo
FROM
	Fechas f
LEFT JOIN 
	(SELECT * FROM `Historial Programacion` WHERE asesor=".$sesion_asesor." AND Fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL +6 DAY)) a ON f.Fecha=a.Fecha
WHERE 
	f.Fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL +6 DAY)";


if($result=$connectdb->query($query)){
	$num_fields=$result->field_count;
	$fields=$result->fetch_fields();
	$x=0;
	while($fila=$result->fetch_row()){
		for($i=0;$i<$num_fields;$i++){
			$data_horarios[$x][$fields[$i]->name]=$fila[$i];
		}
		$x++;
	}
}

foreach($data_horarios as $index => $info){
	if($info['jornada start']==$info['jornada end']){
		if($info['jornada start']==NULL){
			$horarios[$info['Fecha']]="No Capturado";
		}else{
			$horarios[$info['Fecha']]="Descanso";
		}
	}else{
		$js = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['jornada start'].' America/Mexico_City');
		$js -> setTimezone($cun_time);
		$je = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['jornada end'].' America/Mexico_City');
		$je -> setTimezone($cun_time);
		
		$horarios[$info['Fecha']]=$js->format('H:i')." - ".$je->format('H:i');
		$horarios_in[$info['Fecha']]=$js->format('H:i:s');
		$horarios_out[$info['Fecha']]=$je->format('H:i:s');	
	}
	$ausentismo[$info['Fecha']]=$info['Ausentismo'];
}



?>
<br>

<table class='tablesorter' style='text-align:center; width:1200px; margin: auto'>
<thead>
    <tr>
    <th>Nombre</th>
    <th>Colaborador</th>
    <th>RFC</th>
    <th>Tel 1</th>
    <th>Tel 2</th>
    <th>Correo Personal</th>
    <th>Vigencia Pasaporte</th>
    <th>Vigencia Visa</th>
    <th>Locker</th>
    </tr>
</thead>
<tbody>
    <tr id='<?php echo $id; ?>'>
    	
        <td col='Nombre'><?php echo $datos_personales['Nombre']; ?></td>
        <td col='num_colaborador'><?php echo $datos_personales['num_colaborador']; ?></td>
        <td col='RFC'><?php echo $datos_personales['RFC']; ?></td>
        <td col='telefono1' style='background:white'><?php echo $datos_personales['telefono1']; ?></td>
        <td col='telefono2' style='background:white'><?php echo $datos_personales['telefono2']; ?></td>
        <td col='correo_personal' style='background:white'><?php echo $datos_personales['correo_personal']; ?></td>
         <td col='Vigencia_Pasaporte'><input type='text' value='<?php echo $datos_personales['Vigencia_Pasaporte']; ?>' id='pasaporte' name='pasaporte' col='Vigencia_Pasaporte' index='<?php echo $id; ?>' style='width:84px; text-align:center' <?php if($_POST['user']!=$_POST['asesor_id']){echo "readonly";} ?>></td>
         <td col='Vigencia_Visa'><input type='text' value='<?php echo $datos_personales['Vigencia_Visa']; ?>' id='visa' name='visa' col='Vigencia_Visa' index='<?php echo $id; ?>' style='width:84px; text-align:center' <?php if($_POST['user']!=$_POST['asesor_id']){echo "readonly";} ?>></td>
         <td col='locker'><?php echo $datos_personales['Locker']; ?></td>
    </tr>
</tbody>
</table>
<br>
<?
    if(isset($_POST['dep'])){$depart=$dep;}else{$depart=$_SESSION['dep'];}
    if($_POST['tipo']=='all'){$depart='all';}
    switch($depart){
        case 3:
        case 35:
            $query="SELECT Meta_Individual, Meta_Diaria, Meta_Diaria_Total FROM metas WHERE mes=".date('m')." AND anio=".date('Y')." AND skill=$dep";
			if($result=$connectdb->query($query)){
				$fila=$result->fetch_assoc();
				$meta=$fila['Meta_Individual'];
	            $metad=$fila['Meta_Diaria'];
	            $metadt=$fila['Meta_Diaria_Total'];
			}
            include("ventas.php");
            break;
        case 5:
            $query="SELECT Meta_Individual, Meta_Diaria, Meta_Diaria_Total FROM metas WHERE mes=".date('m')." AND anio=".date('Y')." AND skill=$dep";
            if($result=$connectdb->query($query)){
				$fila=$result->fetch_assoc();
				$meta=$fila['Meta_Individual'];
	            $metad=$fila['Meta_Diaria'];
	            $metadt=$fila['Meta_Diaria_Total'];
			}
            include("upsell.php");
            break;
        case 28:
		case 29:
		case 30:
            $query="SELECT Meta_Individual, Meta_Diaria, Meta_Diaria_Total FROM metas WHERE mes=".date('m')." AND anio=".date('Y')." AND skill=$dep";
            if($result=$connectdb->query($query)){
				$fila=$result->fetch_assoc();
				$meta=$fila['Meta_Individual'];
	            $metad=$fila['Meta_Diaria'];
	            $metadt=$fila['Meta_Diaria_Total'];
			}
            include("pdv.php");
            break;
		case 35:
            $query="SELECT Meta_Individual, Meta_Diaria, Meta_Diaria_Total FROM metas WHERE mes=".date('m')." AND anio=".date('Y')." AND skill=$dep";
            if($result=$connectdb->query($query)){
				$fila=$result->fetch_assoc();
				$meta=$fila['Meta_Individual'];
	            $metad=$fila['Meta_Diaria'];
	            $metadt=$fila['Meta_Diaria_Total'];
			}
            include("ventasmp.php");
            break;
        case 'all':
            $query="SELECT Meta_Individual, Meta_Diaria, Meta_Diaria_Total FROM metas WHERE mes=".date('m')." AND anio=".date('Y')." AND skill=$depart".$_POST['dep'];
            if($result=$connectdb->query($query)){
				$fila=$result->fetch_assoc();
				$meta=$fila['Meta_Individual'];
	            $metad=$fila['Meta_Diaria'];
	            $metadt=$fila['Meta_Diaria_Total'];
			}
            $skill=$_POST['dep'];
            include("porhora.php");
            break;
    }
?>
<?php 
	if($_SESSION['dep']==28 || $_SESSION['dep']==29 || $_SESSION['dep']==30){ goto EndHorarios;}
?>
<br>
<table style='width:80%; margin: auto; text-align:center' class='t2'>
    <tr class='title'>
        <th colspan=100>Horarios de la Semana</th>
    </tr>
    <tr class='title'>
        <?php
        	foreach($horarios as $date => $info){
        		echo "<th>".date('l',strtotime($date))."<br>$date</th>";
        	}
        ?>
    </tr>
    <tr class='odd' id='horarios'>
		<?php
        	foreach($horarios as $date => $info){
        		echo "<td>$info<br>".$ausentismo[$date]."</td>";
        	}
        ?>
    </tr>
</table> <br>

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
        <td colspan=100 id='timeline' style='height:250px'></td>
    </tr>
    <tr class='total'>
        <td colspan=100><input type="button" id='historial' Value='Historial' /></td>
    </tr>
</table> <br>
<?php EndHorarios: ?>



<?php if($_SESSION['performance_dia_ventas']==1){ ?>

<div style='width:80%; background: navy; margin:auto;'>
    <div style='float: right; '>
    <a href="/reporte-performance-venta-dia/"><button class='buttonlarge button_redpastel_w' id='Performance_dia' >Performance Ventas</button></a>
    </div>
</div>

<?php }  ?>

<div id="dialog-message" title="Cerrar Sesión" style='text-align: center'>
  <p style='text-align: center'>¡Verifica tu horario de mañana!</p>
  <table id='nd_horario' class='t2' style='text-align:center; margin: auto'>
  	<tr class='title'>
  		
  	</tr>
  	<tr class='pair'>
  		
  	</tr>
  </table>
  <p><input id='check_horario' type='checkbox' req='1'> He revisado mi horario para mi siguiente día laboral</p>
  <p><button class='button button_red_w' id='ok_logout'>Logout</button></a></p>
</div>

<div id="dialog-message-out" title="Sesión Finalizada" style='text-align: center'>
  <p style='text-align: center'>¡Hasta pronto!</p>
</div>

<div id="dialog-load" title="Sesión Finalizada" style='text-align: center'>
	<div id="progressbarload"></div>
</div>



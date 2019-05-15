<?php
 session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="validate_retardos";
$menu_programaciones="class='active'";
include("../connectDB.php");
include("../common/scripts.php");



?>
<style>
.cards{
    width:451px;
    height:260px;
    float: left;
    margin:5px;
    padding: 5px;
}

.concepto{
    float:left;
    width: 150px;
    background: #3280cd;
    color:white;
    font-size:14px;
    text-align:left;
    height: 28px;
    padding-left:10px;
    padding-top:15px;
    padding-bottom:5px;
    margin-top:5px;
}

.contenido{
    float:right;
    color:black;
    font-size:14px;
    text-align:right;
    height: 38px;
    padding-right:10px;
    padding-top:5px;
    padding-bottom:5px;
    margin-top:5px;
    width: 250px;
}

.validacion{
    width:100%;
    float:left;
    text-align:center;
    margin-top:5px;
}

.title{
    background: #3280cd;
    color:white;
    font-size:18px;
    width:436px;
    text-align:center;
    padding: 5px;
    border: solid 3px navajowhite;
    height:25px;
    padding-top:10px;
}

.bodycontain{
    padding: 5px;
    width: 436px;
    border: solid 3px navajowhite;
    height: 210px;
    padding-bottom:0px;
    margin-bottom: 5px;
    margin-top: -1px;
}

.legend{
    width:45px;
    float: left;
    height: 45px;
    color: white;
    font-size:20px;
}

</style>

<script>
function sendRequest(id,reg,val){
        $( "#process" ).dialog("open");
        var urlsend= "/json/formularios/bit_validacion.php?db=bit_bitacora&fieldid=incidencia_id&valid="+reg+"&field2=validado&val2="+val+"&field1=validado_por&val1=<?php echo $_SESSION['id']; ?>";
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
                }else{
                    tipo_noti='error';
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

$(function(){

    $( "#progressbar" ).progressbar({
      value: false
    });

    $( "#process" ).dialog({
        autoOpen: false,
        closeOnEscape: false,
        resizable: false,
        modal: true
      });

    $('.legend').button().on( "click", function(){
        var grupo=$(this).attr('group');
        var flag=$(this).attr('fg');
        if(flag==0){
            $(this).html('<span class="ui-button-text">S</span>');
            $(this).attr('fg','1');
            $('*[agrupador="'+grupo+'"]').hide('slow');
        }else{
            $(this).html('<span class="ui-button-text">H</span>');
            $(this).attr('fg','0');
            $('*[agrupador="'+grupo+'"]').show('slow');
        }

    });

    $('.validar').click(function(){

        var iden=$(this).attr('title');
        var reg=$(this).attr('reg');
        sendRequest(iden,reg,1);

    });

    $( "#confirm" ).dialog({
      autoOpen: false,
      resizable: false,
      height:300,
      width:500,
      modal: true,
      buttons: {
        "Omitir": function() {
          $( this ).dialog( "close" );

        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });

     $('.omitir').click(function(){

        var iden=$(this).attr('title');
        var reg=$(this).attr('reg');

        $( "#confirm" ).dialog( "option", "buttons",
          [
            {
              text: "Omitir",
              click: function() {
                $( this ).dialog( "close" );
                sendRequest(iden,reg,2);
              }

              // Uncommenting the following line would hide the text,
              // resulting in the label being used as a tooltip
              //showText: false
            },
            {
              text: "Cancelar",
              click: function() {
                $( this ).dialog( "close" );

              }

              // Uncommenting the following line would hide the text,
              // resulting in the label being used as a tooltip
              //showText: false
            }
          ]
        );
        $( "#confirm" ).dialog("open");


    });

    var delay = (function(){
      var timer = 0;
      return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
      };
    })();

    $('#searcher').keyup(function(){
            var searchtext=$(this).val().toUpperCase();
            if(searchtext==""){
                $(".cards").show("slow");
            }else{
                delay(function(){
                    $(".cards").hide("slow");
                    $('*[title*="'+searchtext+'"]').show('slow');
                }, 1000 );
            }
        });


});
</script>
<?php include("../common/menu.php");

$query="SELECT * FROM bit_bitacora a, Asesores b, bit_incidencias_tipos c, PCRCs d WHERE  a.asesor=b.id AND b.`id Departamento`=d.id AND a.incidencia=c.bit_inc_id AND validado=0 ORDER BY Fecha,`N Corto`";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $reg[$i]=mysql_result($result,$i,'incidencia_id');
    $id[$i]=mysql_result($result,$i,'asesor');
    $nombre[$i]=mysql_result($result,$i,'N Corto');
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    $incidencia[$i]=mysql_result($result,$i,'incidencia_nombre');
    $tipo[$i]=mysql_result($result,$i,'incidencia');
    $time[$i]=mysql_result($result,$i,'observaciones');
    $hid[$i]=mysql_result($result,$i,'horario_id');
    $dept[$i]=mysql_result($result,$i,'Departamento');
$i++;
}

//echo $query;
?>
<table width='100%' class='t2'>
    <tr class='title'>
        <th>Validacion de Incidencias por Retardos</th>
    </tr>
</table>

<br><br>
<div id='test'></div>
<div style='width:1719px; margin: auto'>
<div style='width:194px; height:800px; border: solid 3px #B8D4FF; margin:10px; padding:10px; display:inline-block'>
    <div style="width: 184px; margin:0px;padding: 5px;float: left;margin-right: 10px;">
        <div data-role="page">
             <div data-role="content">
                <input type="text" data-type="search" id='searcher' name="searcher" value="" />
            </div>
        </div>

    </div>
    <div style="width: 184px; margin:0px;padding: 5px;float: left;margin-right: 10px;">
        <div class='legend' group='1' fg='0' style="background: #DEA5A4;">H</div>
        <div style="width:127px; float: right; height: 45px;font-size: 12px;font-family: inherit;padding-top: 5px;"> Retardos de 1 a 10 min</div>
    </div>
    <div style="width: 184px; margin:0px;padding: 5px;float: left;margin-right: 10px;">
        <div class='legend' group='2' fg='0' style="background: #FFB347;">H</div>
        <div style="width:127px; float: right; height: 45px;font-size: 12px;font-family: inherit;padding-top: 5px;"> Retardos > 10 min</div>
    </div>
    <div style="width: 184px; margin:0px;padding: 5px;float: left;margin-right: 10px;">
        <div class='legend' group='3' fg='0' style="background: #C23B22;">H</div>
        <div style="width:127px; float: right; height: 45px;font-size: 12px;font-family: inherit;padding-top: 5px;">Acumulacion de Retardos (Suspension)</div>
    </div>
    <div style="width: 184px; margin:0px;padding: 5px;float: left;margin-right: 10px;">
        <div class='legend' group='4' fg='0' style="background: #836953;">H</div>
        <div style="width:127px; float: right; height: 45px;font-size: 12px;font-family: inherit;padding-top: 5px;">Acumulacion de Faltas (Acta)</div>
    </div>

</div>
<div style=' margin-left: 3px; width:1430px; height:800px; margin: 10px; border: solid 3px #B8D4FF;overflow-y:auto; padding:10px; display:inline-block'>
        <?php foreach($nombre as $key => $asesor){
            switch($tipo[$key]){
                case 1:
                    $colorbg="#DEA5A4";
                    $group=1;
                    break;
                case 2:
                    $colorbg="#FFB347";
                    $group=2;
                    break;
                case 3:
                case 5:
                case 6:
                case 7:
                    $colorbg="#C23B22";
                    $group=3;
                    break;
                case 8:
                    $colorbg="#836953";
                    $group=4;
                    break;
                default:
                    $colorbg="";
                    $group="";
                    break;
            }
            echo "<div class='cards' id='d$id[$key]' agrupador='$group' title='".strtoupper("$asesor $dept[$key] $fecha[$key]")."'>
            <div id='nombre_$reg[$key]' title='$asesor' class='title'>$asesor ($dept[$key])</div>
            <div class='bodycontain' style='background: $colorbg'>
                <div class='concepto'>Fecha</div>
                <div id='fecha_$reg[$key]' class='contenido'>$fecha[$key]</div>
                <div class='concepto'>Tipo Incidencia</div>
                <div class='contenido'>$incidencia[$key]</div>
                <div class='concepto'>Tiempo de Retardo</div>
                <div class='contenido'>$time[$key]</div>
                <div class='validacion' style='floar:clear'>
                    <button class='button button_green_w validar' id='b_v_$id[$key]' reg='$reg[$key]' title='$id[$key]' hid='$hid[$key]' type='$tipo[$key]' asesor='$id[$key]' user='".$_SESSION['id']."'>Validar</button>
                    <button class='button button_red_w cancel' id='b_c_$id[$key]' reg='$reg[$key]' title='$id[$key]' hid='$hid[$key]' type='$tipo[$key]' asesor='$id[$key]' user='".$_SESSION['id']."'>Cancelar</button>
                    <button class='button button_black_w omitir' id='o_c_$id[$key]' reg='$reg[$key]' title='$id[$key]' hid='$hid[$key]' type='$tipo[$key]' asesor='$id[$key]' user='".$_SESSION['id']."'>Omitir</button>
                </div>
            </div>
        </div>";
        }
        ?>

</div>
</div>
<div id="process" title="Procesando">
  <div id="progressbar"></div>
</div>
<div id="confirm" title="Omitir Registro">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Esta opcion determina que esta alerta no sera tomada en cuenta en la bitacora.<br><br>Debes estar seguro que el retardo/falta se encuentra justificado en sistema, de lo contrario da click en el boton CANCELAR.<br><br>Estas seguro?</p>
</div>

<?php
include("../common/add_exception.php"); ?>
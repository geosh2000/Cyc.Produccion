<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formulario_mt";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");


$asesor=$_SESSION['asesor_id'];
$area=2;
?>
<link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.css"/>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js'></script>
<script>

$(function(){
	
	$('#datein').datetimepicker({
	  value: '<?php echo date('Y-m-d H:i'); ?>',
	  step: 1,
	  format:'Y-m-d H:i',
	  inline:true,
	  maxDate: '<?php echo date('Y.m.d H:i'); ?>',
	  lang:'es'
	});
	
	

    $("#login").hide();
/*
    $( "#f_other" ).autocomplete({
       source: 'search_other.php'
    });

    $( "#f_localidad" ).autocomplete({
        source: 'search_localidad.php'
    });

    $( "#f_nombre" ).autocomplete({
        source: 'search_nombre.php'
    });
*/
    function loginPop(variable) {
        if(variable=='ok'){
          var page="/common/login.php?modal=on";
          var $dialog = $('#login')
          .html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
          .dialog({
            title: "Login",
            autoOpen: false,
            dialogClass: 'dialog_fixed,ui-widget-header',
            modal: true,
            height: 500,
            minWidth: 600,
            minHeight: 400,
            draggable:true,
            /*close: function () { $(this).remove(); },*/
            buttons: { "Ok": function () {         $(this).dialog("close"); } }
          });
          $dialog.dialog('open');
        }
    }

    function sendRequest(variables){

    var urlok='query.php';
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
                    $('#error').text("");
                    $('.input').val('');
                    start_fields();
                }else{
                    if(status[1]=='DISC'){
                        tipo_noti='error';
                        startlogin='ok';
                    }else{
                        $('#error').text(urlok+"?"+variables);
                        tipo_noti='error';
                    }
                }
                new noty({
                    text: notif_msg[1],
                    type: tipo_noti,
                    timeout: 10000,
                    animation: {
                        open: {height: 'toggle'}, // jQuery animate function property object
                        close: {height: 'toggle'}, // jQuery animate function property object
                        easing: 'swing', // easing
                        speed: 500 // opening & closing animation speed
                    },
                    callback: {
                        onShow: function(){
                            loginPop(startlogin);
                        }
                        }
                });
				$('#submit_form').prop('disabled',false);
            }
        }
        xmlhttp.open("POST",urlok,true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(variables);

    }

    flag=true;

    function start_fields(){
        $('#f_localizador, #f_tipo, #datein, #dateout').val('');
        $('#contain-localizador, #contain-tipo, #contain-datein, #contain-dateout').attr('req',1);

    }

    start_fields();

    function validate(){
        //Datec
        if($('#contain-datein').attr('req')==1 && $('#datein').val()==''){
            flag_datein=false;
            $('#contain-datein div p').addClass('error');
        }else{
            flag_datein=true;
            $('#contain-datein div p').removeClass('error');
        }

		
        //FC
        if($('#contain-localizador').attr('req')==1 && $('#f_localizador').val()==''){
            flag_localizador=false;
            $('#f_localizador').addClass('error');
        }else{
            flag_localizador=true;
            $('#f_localizador').removeClass('error');
        }

		//Tipo
        if($('#contain-tipo').attr('req')==1 && $('#f_status').val()==''){
            flag_tipo=false;
            $('#f_tipo').addClass('error');
        }else{
            flag_tipo=true;
            $('#f_tipo').removeClass('error');
        }

        if(flag_localizador && flag_datein &&  flag_tipo){flag=true;}else{flag=false; $('#submit_form').prop('disabled',false);}
		
        
    }
    
    
    //BUTTON Submit
    $('#submit_form').click(function(){
    	$('#submit_form').prop('disabled',true);
        validate();
        
        if(flag){
        	var temp=$('#f_localizador').val();
            var casotemp=temp.substr(0,13)+" ... "+temp.substr(temp.length-10,10);
            var casosok=temp.replace(/\s/g, " ");
            var casos=casosok;
        	var variables="asesor=<?php echo $asesor; ?>&area=<?php echo $area; ?>&caso=" + casos + "&localizador=" + $('#f_localizador').val() + "&datec=" +  $('#datein').val() + "&tipo=" +  $('#f_tipo').val();
            //alert(variables);
            sendRequest(variables);
        }
    });
    
    //Change to UPPERCASE
    $('#f_pnr').keyup(function(){
        var name=$(this).val();
        var newname=name.toUpperCase();
        $(this).val(newname);
    });

});

</script>

<style>
.formulario{
    width: 800px;
    height: 100%;
    margin: auto;
    overflow: auto;
}

.titulo{
    width: 800px;
    height: 65px;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin: auto;
    margin-top: -19px;
    border-radius: 15px;
    background: #008CBA
}

.campo{
    width: 520px;
    height: 80px;
    margin: auto;
    margin-top: 20px;
    border-radius: 15px;
}

.campo .name{
    float: left;
    height: 100%;
    width: 40%;
    background: #008CBA;
    border-radius: 15px 0 0 15px;
    color: white;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}

.campo .name p{
    padding-top:12px;
}

.campo .opcion{
    float: left;
    height: 100%;
    width: 60%;
    background: #E7F5FE;
    border-radius: 0 15px 15px 0;
    color: black;
    font-size: 20px;
    text-align: center;
}

.campo .opcion .seleccion{
    padding-top:5px;
}

.seleccion select, .seleccion input{
    width: 200px;
}

.error{
    background: #FFE8E0;
    color: black;
}



</style>
<?php


?>
<div style='float: left; width:70%; margin: auto;'>
<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>BackOffice - Mailing</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'><?php echo $_SESSION['name'];?></p>
    </div>
    <div id='contain-localizador' style='height: 200px' class='campo'>
        <div class='name'>
            <p>Caso</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><td class='pair'><textarea name='rango' id='f_localizador' value='' rows='10' req='1'></textarea></td>*</p>
        </div>
    </div>
    <div id='contain-datein' class='campo' style='height:220px'>
        <div class='name'>
            <p>Fecha Recepcion</p>
        </div>
        <div class='opcion'>
            <p class='seleccion' id='datein'></p>
        </div>
    </div>
    <div id='contain-tipo' class='campo'>
        <div class='name'>
            <p>Status</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_tipo' class='input' req='1'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM bo_status WHERE area=$area ORDER BY status";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'status')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>
    
</div>
</div>
<div style='float:left; width: 30%; margin: auto;'>
<div id="sidebar">
   <table width='100%' class='t2'>
        <tr class='title' colspan=100>
            <th>Registros Exitosos</th>
        </tr>
   </table>
   <table width='100%' id='hor-minimalist-a' class='tablesorter' >
        <thead>
        <tr class='title'>
            <th width='25%'>Hora</th>
            <th width='25%'>Actividad</th>
            <th width='25%'>Fecha</th>
            <th width='25%'>EM</th>
        </tr>
        </thead>
        <tbody>

        <?php
            unset($key,$iden);
            foreach($reg_id as $key => $iden){
                echo "<tr>\n";
                echo "<td width='25%'>$reg_hora[$key]</td>\n";
                echo "<td width='25%'>$reg_actividad[$key]</td>\n";
                echo "<td width='25%'>$reg_fecha[$key]</td>\n";
                echo "<td width='25%'>$reg_em[$key]</td>\n";
                echo "</tr>\n";
            }

        ?>
        </tbody>
   </table>
 </div>
 </div>
<div id='login'></div>
<div id='error'></div>

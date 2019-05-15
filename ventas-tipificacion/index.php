<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_tipificacion_ventas";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");


$asesor=$_SESSION['asesor_id'];

?>

<script>

$(function(){

    $("#login").hide();

    $( "#f_ncompra" ).autocomplete({
        source: 'search_ncompra.php'
    });
    
    $( "#f_destinos1, #f_destinos2, #f_destinos3, #f_destinos4" ).autocomplete({
        source: 'search_destinos.php'
    });
    
    $('#f_destinos1, #f_destinos2, #f_destinos3, #f_destinos4, #f_ncompra').keyup(function(){
        var name=$(this).val();
        var newname=name.toUpperCase();
        $(this).val(newname);
    });

    
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
                    $('.input').val('');
                    start_fields();
                }else{
                    if(status[1]=='DISC'){
                        tipo_noti='error';
                        startlogin='ok';
                    }else{
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

            }
        }
        xmlhttp.open("POST",urlok,true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(variables);

    }

    flag=true;

    function start_fields(){
        $('#contain-localizador, #contain-ncompra').hide();
        $('#contain-canal, #contain-destinos, #contain-cotizaciones, #contain-submit, #contain-status').show().attr('req',1);
        $('#contain-localizador, #contain-ncompra').attr('req',0);
    }

    start_fields();

    function validate(){
        //Canal
        if($('#contain-canal').attr('req')==1 && $('#f_canal').val()==''){
            flag_canal=false;
            $('#f_canal').addClass('error');
        }else{
            flag_canal=true;
            $('#f_canal').removeClass('error');
        }
        
        //Destinos
        if($('#contain-destinos').attr('req')==1 && $('#f_destinos1').val()=='' && $('#f_destinos2').val()=='' && $('#f_destinos3').val()=='' && $('#f_destinos4').val()==''){
            flag_destinos=false;
            $('#f_destinos1, #f_destinos2, #f_destinos3, #f_destinos4').addClass('error');
        }else{
            flag_destinos=true;
            $('#f_destinos1, #f_destinos2, #f_destinos3, #f_destinos4').removeClass('error');
        }
        
        //cotizaciones
        if($('#contain-cotizaciones').attr('req')==1 && $('#f_cotizaciones').val()==''){
            flag_cotizaciones=false;
            $('#f_cotizaciones').addClass('error');
        }else{
            flag_cotizaciones=true;
            $('#f_cotizaciones').removeClass('error');
        }
        
        //Localizador
        if($('#contain-localizador').attr('req')==1 && $('#f_localizador').val()==''){
            flag_localizador=false;
            $('#f_localizador').addClass('error');
        }else{
            flag_localizador=true;
            $('#f_localizador').removeClass('error');
        }
        
        //status
        if($('#contain-status').attr('req')==1 && $('#f_status').val()==''){
            flag_status=false;
            $('#f_status').addClass('error');
        }else{
            flag_status=true;
            $('#f_status').removeClass('error');
        }
        
        //No Compra
        if($('#contain-ncompra').attr('req')==1 && $('#f_ncompra').val()==''){
            flag_ncompra=false;
            $('#f_ncompra').addClass('error');
        }else{
            flag_ncompra=true;
            $('#f_ncompra').removeClass('error');
        }

        

        if(flag_canal && flag_destinos && flag_cotizaciones && flag_localizador && flag_ncompra){flag=true;}else{flag=false;}

    }

    $('#f_localizador').keyup(function(){
        var regex = /^[0-9]*(?:\.\d{1,2})?$/;    // allow only numbers [0-9]
        var loc=$(this).val();
        if( !regex.test(loc) ) {
              var newloc=loc.substr(0,loc.length-1);
              $(this).val(newloc);
        }
    });
    
    //Status Change
    $('#f_status').change(function(){
    	var status=$(this).val();
    	switch(status){
    		case '0':
    			$('#contain-ncompra').show('200').attr('req',1);
    			$('#contain-localizador').hide('200').attr('req',0);
    			$('#f_localizador').val('');
    			break;
    		case '1':
    			$('#contain-localizador').show('200').attr('req',1);
    			$('#contain-ncompra').hide('200').attr('req',0);
    			$('#f_ncompra').val('');
    			break;
    		default:
    			$('#contain-localizador').hide('200').attr('req',0);
    			$('#contain-ncompra').hide('200').attr('req',0);
    			$('#f_localizador').val('');
    			$('#f_ncompra').val('');
    			break;
    	}
    });

    $('#submit_form').click(function(){
        validate();
        if(flag){
            var variables="asesor=<?php echo $asesor; ?>&canal=" + $('#f_canal').val() + "&destinos1=" + $('#f_destinos1').val() + "&destinos2=" + $('#f_destinos2').val() + "&destinos3=" + $('#f_destinos3').val() + "&destinos4=" + $('#f_destinos4').val() + "&cotizaciones=" + $('#f_cotizaciones').val() + "&localizador=" + $('#f_localizador').val() + "&ncompra=" + $('#f_ncompra').val() + "&status=" + $('#f_status').val();
            //alert(variables);
            sendRequest(variables);
        }
    })

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
    height: 50px;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin: auto;
    margin-top: -19px;
    border-radius: 15px;
    background: #008CBA
}

.campo{
    width: 600px;
    height: 80px;
    margin: auto;
    margin-top: 20px;
    border-radius: 15px;
}

.campo .name{
    float: left;
    height: 100%;
    width: 60%;
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
    width: 40%;
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
}



</style>

<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>Tipificación de Llamadas - Ventas</p>
    </div>
    <div id='contain-canal' class='campo'>
        <div class='name'>
            <p>Canal</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_canal' class='input'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM Dids WHERE formulario_tipificacion=1 ORDER BY Canal";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'dids_id')."'>".mysql_result($result,$i,'Canal')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-destinos' class='campo' style='height: 160px'>
        <div class='name'>
            <p>Destinos Cotizados</p>
            <p style='margin-top: -15; font-size: 15px; font-weight: normal'>(separados por coma)</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='hidden' id='f_destinos' class='input' value=''>
            					<input type='text' id='f_destinos1' class='input' value=''><br>
            					<input type='text' id='f_destinos2' class='input' value=''><br>
            					<input type='text' id='f_destinos3' class='input' value=''><br>
            					<input style='width: 193px;' type='text' id='f_destinos4' class='input' value=''>*</p>
        </div>
    </div>
    <div id='contain-cotizaciones' class='campo'>
        <div class='name'>
            <p># de Cotizaciones</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input style='width:48px; height:30px; text-align: center' type='number' id='f_cotizaciones' class='input' value=''>*</p>
        </div>
    </div>
    <div id='contain-status' class='campo'>
        <div class='name'>
            <p>Reserva Concretada?</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_status' class='input' value=''><option value=''>Selecciona...</option><option value='1'>Reserva Concretada</option><option value='0'>No compró</option></select></p>
        </div>
    </div>
    <div id='contain-localizador' class='campo'>
        <div class='name'>
            <p>Localizador</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_localizador' class='input' value=''></p>
        </div>
    </div>
    <div id='contain-ncompra' class='campo'>
        <div class='name'>
            <p>Motivo de No Compra</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_ncompra' class='input' value=''></p>
        </div>
    </div>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>

</div>
<div id='login'></div>
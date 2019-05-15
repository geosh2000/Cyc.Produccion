<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formulario_mp";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");


$asesor=$_SESSION['asesor_id'];
$area="llamadas";
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
                    $( '#regs' ).attr( 'src', function ( i, val ) { return val; });
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
        $('#f_nombre, #f_localizador, #f_tipo, #f_motivo, #f_tel, #f_canal').val('');
        $('#contain-motivo, #contain-tipo, #contain-canal').attr('req',1);

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
        
        //Caso
        if($('#contain-nombre').attr('req')==1 && $('#f_nombre').val()==''){
            flag_nombre=false;
            $('#f_nombre').addClass('error');
        }else{
            flag_nombre=true;
            $('#f_nombre').removeClass('error');
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
        if($('#contain-tipo').attr('req')==1 && $('#f_tipo').val()==''){
            flag_tipo=false;
            $('#f_tipo').addClass('error');
        }else{
            flag_tipo=true;
            $('#f_tipo').removeClass('error');
        }
		
		//Motivo
        if($('#contain-motivo').attr('req')==1 && $('#f_motivo').val()==''){
            flag_motivo=false;
            $('#f_motivo').addClass('error');
        }else{
            flag_motivo=true;
            $('#f_motivo').removeClass('error');
        }
		
		//Tel
        if($('#contain-tel').attr('req')==1 && $('#f_tel').val()==''){
            flag_tel=false;
            $('#f_tel').addClass('error');
        }else{
            flag_tel=true;
            $('#f_tel').removeClass('error');
        }

        if(flag_canal && flag_nombre && flag_tel && flag_localizador && flag_motivo &&  flag_tipo){flag=true;}else{flag=false; $('#submit_form').prop('disabled',false);}
		
        
    }
    
    
    //BUTTON Submit
    $('#submit_form').click(function(){
    	$('#submit_form').prop('disabled',true);
        validate();
        if(flag){
            var variables="asesor=<?php echo $asesor; ?>&area=<?php echo $area; ?>&nombre=" + $('#f_nombre').val() + "&canal=" + $('#f_canal').val() + "&localizador=" + $('#f_localizador').val() + "&tel=" +  $('#f_tel').val() + "&tipo=" +  $('#f_tipo').val() + "&motivo=" +  $('#f_motivo').val();
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
<div style='float: left; width:60%; margin: auto;'>
<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>Tráfico MP - Llamadas</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'><?php echo $_SESSION['name'];?></p>
    </div>
    <div id='contain-localizador' class='campo'>
        <div class='name'>
            <p>Localizador</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_localizador' class='input' value=''  req='0'></p>
        </div>
    </div>
    <div id='contain-nombre' class='campo'>
        <div class='name'>
            <p>Nombre Cliente</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_nombre' class='input' value=''  req='0'></p>
        </div>
    </div>
    <div id='contain-tel' class='campo'>
        <div class='name'>
            <p>Telefono</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_tel' class='input' value=''  req='0'></p>
        </div>
    </div>
    <div id='contain-canal' class='campo'>
        <div class='name'>
            <p>Canal</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_canal' class='input' req='1'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM trfMP_canales WHERE activo=1 ORDER BY canal";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".utf8_encode(mysql_result($result,$i,'canal'))."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-motivo' class='campo'>
        <div class='name'>
            <p>Motivo de la Llamada</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_motivo' class='input' req='1'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM trfMP_motivos_llamadas WHERE activo=1 ORDER BY motivo";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".utf8_encode(mysql_result($result,$i,'motivo'))."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-tipo' class='campo'>
        <div class='name'>
            <p>Tipo de Reserva</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_tipo' class='input' req='1'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM trfMP_tipo_reserva WHERE activo=1 ORDER BY tipo";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".utf8_encode(mysql_result($result,$i,'tipo'))."</option>";
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
<div style='float:right; width: 30%; margin: auto;'>
<div id="sidebar">
   <iframe id='regs' width='100%' height='100%' style='border: 0;' src='registros.php?area=<?php echo $area; ?>'></iframe>
 </div>
 </div>
<div id='login'></div>
<div id='error'></div>

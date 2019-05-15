<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formulario_ag";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");


$asesor=$_SESSION['asesor_id'];

?>

<script>

$(function(){

    $("#login").hide();

    $( "#f_agencia" ).autocomplete({
        source: 'search_agencia.php'
    });

    $( "#f_localidad" ).autocomplete({
        source: 'search_localidad.php'
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
        $('.campo, .id_tipo, .id_soporte').hide();
        $('#contain-agencia, #contain-localizador, #contain-canal, #contain-motivo, #contain-submit').show().attr('req',1);
        $('#contain-localizador').show().attr('req',0);
    }

    start_fields();

    function validate(){
        //Nombre Agencia
        if($('#contain-agencia').attr('req')==1 && $('#f_agencia').val()==''){
            flag_agencia=false;
            $('#f_agencia').addClass('error');
        }else{
            flag_agencia=true;
            $('#f_agencia').removeClass('error');
        }

        //Canal
        if($('#contain-canal').attr('req')==1 && $('#f_canal').val()==''){
            flag_canal=false;
            $('#f_canal').addClass('error');
        }else{
            flag_canal=true;
            $('#f_canal').removeClass('error');
        }

        //Localidad
        if($('#contain-localidad').attr('req')==1 && $('#f_localidad').val()==''){
            flag_localidad=false;
            $('#f_localidad').addClass('error');
        }else{
            flag_localidad=true;
            $('#f_localidad').removeClass('error');
        }

        //Motivo
        if($('#contain-motivo').attr('req')==1 && $('#f_motivo').val()==''){
            flag_motivo=false;
            $('#f_motivo').addClass('error');
        }else{
            flag_motivo=true;
            $('#f_motivo').removeClass('error');
        }

        //Tipo
        if($('#contain-tipo').attr('req')==1 && $('#f_tipo').val()==''){
            flag_tipo=false;
            $('#f_tipo').addClass('error');
        }else{
            flag_tipo=true;
            $('#f_tipo').removeClass('error');
        }

        //Soporte
        if($('#contain-soporte').attr('req')==1 && $('#f_soporte').val()==''){
            flag_soporte=false;
            $('#f_soporte').addClass('error');
        }else{
            flag_soporte=true;
            $('#f_soporte').removeClass('error');
        }

        if(flag_agencia && flag_canal && flag_localidad && flag_motivo && flag_tipo && flag_soporte){flag=true;}else{flag=false;}

    }

    //Motivos
    $('#f_motivo').change(function(){
        $('#f_tipo').val('');
        var valor=$(this).val();
        $('.id_tipo').hide('200').attr('req',0);
        $('.tipo'+valor).show('200').attr('req',1);
        if($('#f_motivo').val()=='1'){
            $('#contain-soporte').show('200').attr('req',1);
            $('.id_soporte').hide('200').attr('req',0);
        }else{
            $('#contain-soporte, .id_soporte').hide('200').attr('req',0);
        }
        if($('#f_motivo').val()!=''){
            $('#contain-tipo').show('200').attr('req',1);
        }else{
            $('#contain-tipo').hide('200').attr('req',0);
        }

        //Titulo Motivos
        if(valor==2){
            $('#title_tipo').text('Transferencia a');
        }else{
            $('#title_tipo').text('Tipo');
        }

    });

    //Canal
    $('#f_canal').change(function(){
        $('#f_localidad').val('');
        var valor=$(this).val();
        if(valor==4){
            $('#contain-localidad').show('200').attr('req',1);
        }else{
            $('#contain-localidad').hide('200').attr('req',0);
        }
    });

    //Tipos
    $('#f_tipo').change(function(){
        $('#f_soporte').val('');
        var valor=$(this).val();
        $('.id_soporte').hide('200').attr('req',0);
        $('.soporte'+valor).show('200').attr('req',1);
    });

    $('#f_agencia, #f_localidad').keyup(function(){
        var name=$(this).val();
        var newname=name.toUpperCase();
        $(this).val(newname);
    });

    $('#f_localizador').keyup(function(){
        var regex = /^[0-9]*(?:\.\d{1,2})?$/;    // allow only numbers [0-9]
        var loc=$(this).val();
        if( !regex.test(loc) ) {
              var newloc=loc.substr(0,loc.length-1);
              $(this).val(newloc);
        }
    });

    $('#submit_form').click(function(){
        validate();
        if(flag){
            var variables="asesor=<?php echo $asesor; ?>&agencia=" + $('#f_agencia').val() + "&canal=" + $('#f_canal').val() + "&localidad=" + $('#f_localidad').val() + "&motivo=" + $('#f_motivo').val() + "&tipo=" + $('#f_tipo').val() + "&soporte=" + $('#f_soporte').val() + "&localizador=" + $('#f_localizador').val();
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
    width: 450px;
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
}



</style>

<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>Tipificación de Llamadas - Agencias</p>
    </div>
    <div id='contain-agencia' class='campo'>
        <div class='name'>
            <p>Nombre Agencia</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_agencia' class='input' value=''>*</p>
        </div>
    </div>
    <div id='contain-canal' class='campo'>
        <div class='name'>
            <p>Canal</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_canal' class='input'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM ag_canal ORDER BY Canal";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'Canal')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-localidad' class='campo'>
        <div class='name'>
            <p>Localidad</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_localidad' class='input' value=''>*</p>
        </div>
    </div>
    <div id='contain-motivo' class='campo'>
        <div class='name'>
            <p>Motivo</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_motivo' class='input'><option value=''>Selecciona...</option><?php
                $query="SELECT * FROM ag_motivos ORDER BY Motivo";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'Motivo')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-tipo' class='campo'>
        <div class='name'>
            <p id='title_tipo'>Tipo</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_tipo' class='input'><option value=''>Selecciona...</option><?php
                $query="SELECT * FROM ag_tipo ORDER BY tipo";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option class='id_tipo tipo".mysql_result($result,$i,'motivo')."' value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'tipo')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-soporte' class='campo'>
        <div class='name'>
            <p>Tipo de Soporte</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_soporte' class='input'><option value=''>Selecciona...</option><?php
                $query="SELECT * FROM ag_soporte ORDER BY tipo_soporte";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option class='id_soporte soporte".mysql_result($result,$i,'soporte')."' value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'tipo_soporte')."</option>";
                $i++;
                }
            ?></select>*</p>
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
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>

</div>
<div id='login'></div>
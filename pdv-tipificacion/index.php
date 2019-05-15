<?php
ini_set('session.gc_maxlifetime', 28800);
session_start();
header('Content-Type: text/html; charset=utf-8');


$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_tipificacion_pdv";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");


$asesor=$_SESSION['asesor_id'];

?>

<script>

$(function(){

    $("#login").hide();

    $( "#f_PDV" ).autocomplete({
       source: 'search_PDV.php',
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
                    var notif_regs = text.match("regs- (.*) -regs");
                    $('#regs').text(notif_regs[1]);
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
                $('#submit_form').prop('disabled',false);

            }
        }
        xmlhttp.open("POST",urlok,true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(variables);

    }

    flag=true;

    function start_fields(){
        $('#contain-Objecion, #contain-NewLoc').hide().attr('req',0);
        $('#contain-PDV, #contain-Localizador, #contain-Resolucion').attr('req',1);
	}

    start_fields();

    function validate(){
        //PDV
        if($('#contain-PDV').attr('req')==1 && $('#f_PDV').val()==''){
            flag_PDV=false;
            $('#f_PDV').addClass('error');
        }else{
            flag_PDV=true;
            $('#f_PDV').removeClass('error');
        }
        
        //Localizador
        if($('#contain-Localizador').attr('req')==1 && $('#f_Localizador').val()==''){
            flag_Localizador=false;
            $('#f_Localizador').addClass('error');
        }else{
            flag_Localizador=true;
            $('#f_Localizador').removeClass('error');
        }
        
        //Resolucion
        if($('#contain-Resolucion').attr('req')==1 && $('#f_Resolucion').val()==''){
            flag_Resolucion=false;
            $('#f_Resolucion').addClass('error');
        }else{
            flag_Resolucion=true;
            $('#f_Resolucion').removeClass('error');
        }
        
        //NewLoc
        if($('#contain-NewLoc').attr('req')==1 && $('#f_NewLoc').val()==''){
            flag_NewLoc=false;
            $('#f_NewLoc').addClass('error');
        }else{
            flag_NewLoc=true;
            $('#f_NewLoc').removeClass('error');
        }
        
        //Objecion
        if($('#contain-Objecion').attr('req')==1 && $('#f_Objecion').val()==''){
            flag_Objecion=false;
            $('#f_Objecion').addClass('error');
        }else{
            flag_Objecion=true;
            $('#f_Objecion').removeClass('error');
        }

        

        if(flag_PDV && flag_Localizador && flag_Resolucion && flag_NewLoc && flag_Objecion ){flag=true;}else{flag=false; $('#submit_form').prop('disabled',false);}

    }

    //Resolucion
    $('#f_Resolucion').change(function(){
    	$('#contain-NewLoc, #contain-Objecion').fadeOut('200').attr('req',0);
    	$('#f_NewLoc, #f_Objecion').val('');
    	campo=($('option:selected', this).attr('activate'));
    	if(campo!=''){
    		$('#contain-'+campo).fadeIn('200').attr('req',1);
    	}   
    });

    //BUTTON Submit
    $('#submit_form').click(function(){
    	$(this).prop('disabled',true);
        validate();
        if(flag){
            var variables="asesor=<?php echo $asesor; ?>&base=" + $('#f_base').val() + 
            "&pdv=" + $('#f_PDV').val() + 
            "&localizador=" + $('#f_Localizador').val() + 
            "&resolucion=" + $('#f_Resolucion').val() +
            "&newloc=" + $('#f_NewLoc').val() + 
            "&objecion=" + $('#f_Objecion').val();
            //alert(variables);
            sendRequest(variables);
        }
    })

	//Change to UPPERCASE
    /*$('#f_PDV').keyup(function(){
        var name=$(this).val();
        var newname=name.toUpperCase();
        $(this).val(newname);
    });
    */
});

</script>

<style>
.formulario{
    width: 900;
    height: 100%;
    margin: auto;
    overflow: auto;
    text-align: center
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
    width: auto;
    height: 80px;
    margin: 10;
    border-radius: 15px;
    display: inline-block;
}

.campo .name{
    float: left;
    height: 100%;
    width: 170px;
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
    width: 305px;
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
    width: 280px;
    text-align: center;
}

.error{
    background: #FFE8E0;
}



</style>
<?php

$query="SELECT COUNT(id) as Registros FROM pdv_registro_llamadas WHERE CAST(Last_Update as DATE)=CURDATE() AND asesor=$asesor";
$result=mysql_query($query);
$regs=mysql_result($result,0,'Registros');
?>
<div style='width:90%; margin: auto;'>
<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>Registro de Llamadas - PDV</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'>Registros: <regs id='regs'><?php echo $regs; ?></regs></p>
    </div>
    <div id='contain-PDV' class='campo'>
        <div class='name'>
            <p>PDV</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_PDV' class='input' value=''></p>
        </div>
    </div>
    <div id='contain-Localizador' class='campo'>
        <div class='name'>
            <p>Localizador Original</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_Localizador' class='input' value=''></p>
        </div>
    </div>
    <div id='contain-Resolucion' class='campo'>
        <div class='name'>
            <p>Resolucion</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_Resolucion' class='input'><option value=''>Selecciona...</option><?php
                $query="SELECT * FROM us_resolucion WHERE active=1 ORDER BY Resolucion";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option activate='".mysql_result($result,$i,'opt_habilitar')."' value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'Resolucion')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-Objecion' class='campo'>
        <div class='name'>
            <p>Objecion</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_Objecion' class='input'><option value=''>Selecciona...</option><?php
                $query="SELECT * FROM us_objecion WHERE active=1 ORDER BY Objecion";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".utf8_encode(mysql_result($result,$i,'Objecion'))."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-NewLoc' class='campo'>
        <div class='name'>
            <p>Localizador Nuevo</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_NewLoc' class='input' value=''></p>
        </div>
    </div>
    <div id='contain-submit' style='text-align: center;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>

</div>
<div id='errordiv'></div>
</div>

		
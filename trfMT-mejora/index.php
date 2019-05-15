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
        $('#f_caso, #f_fc, #f_tipo, #datein, #dateout').val('');
        $('#contain-caso, #contain-fc, #contain-tipo, #contain-datein, #contain-dateout').attr('req',1);

    }

    start_fields();

    function validate(){
        //Caso
        if($('#contain-caso').attr('req')==1 && $('#f_caso').val()==''){
            flag_caso=false;
            $('#f_caso').addClass('error');
        }else{
            flag_caso=true;
            $('#f_caso').removeClass('error');
        }
        
        //Datec
        if($('#contain-datein').attr('req')==1 && $('#datein').val()==''){
            flag_datein=false;
            $('#contain-datein div p').addClass('error');
        }else{
            flag_datein=true;
            $('#contain-datein div p').removeClass('error');
        }

		
        //FC
        if($('#contain-fc').attr('req')==1 && $('#f_fc').val()==''){
            flag_fc=false;
            $('#f_fc').addClass('error');
        }else{
            flag_fc=true;
            $('#f_fc').removeClass('error');
        }

		//Tipo
        if($('#contain-tipo').attr('req')==1 && $('#f_status').val()==''){
            flag_tipo=false;
            $('#f_tipo').addClass('error');
        }else{
            flag_tipo=true;
            $('#f_tipo').removeClass('error');
        }

        if(flag_caso && flag_fc && flag_datein &&  flag_tipo){flag=true;}else{flag=false; $('#submit_form').prop('disabled',false);}
		
        
    }
    
    
    //BUTTON Submit
    $('#submit_form').click(function(){
    	$('#submit_form').prop('disabled',true);
        validate();
        if(flag){
            var variables="asesor=<?php echo $asesor; ?>&caso=" + $('#f_caso').val() + "&fc=" + $('#f_fc').val() + "&datec=" +  $('#datein').val() + "&tipo=" +  $('#f_tipo').val();
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

$query="SELECT a.Fecha, a.asesor, a.Calls as llamadas, b.Registros  FROM
            (SELECT * FROM d_PorCola WHERE asesor=$asesor AND Fecha=CURDATE() AND Skill=4 ) a
            LEFT JOIN
            (SELECT CAST(Last_Update as DATE) as Fecha, COUNT(id) as Registros, asesor FROM sac_tipificacion WHERE CAST(Last_Update as DATE)=CURDATE() AND asesor=$asesor) b
            ON a.Fecha=b.Fecha AND a.asesor=b.asesor";
$result=mysql_query($query);
$calls=mysql_result($result,0,'llamadas');
$regs=mysql_result($result,0,'Registros');
$perc=number_format($regs/$calls*100,2);

?>

<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>Emisiones - Tráfico MT</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'><?php echo $_SESSION['name'];?></p>
    </div>
    <div id='contain-caso' class='campo'>
        <div class='name'>
            <p>Caso</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_caso' class='input' value=''  req='1'>*</p>
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
    <div id='contain-fc' class='campo'>
        <div class='name'>
            <p>Primer Contacto</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_fc' class='input' req='1'><option value=''>Selecciona...</option>
            <option value='1'>Si</option><option value='2'>No</option></select>*</p>
        </div>
    </div>
    <div id='contain-tipo' class='campo'>
        <div class='name'>
            <p>Tipo de Respuesta</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_tipo' class='input' req='1'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM trfMT_mejora_seguimientos ORDER BY id";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'Seguimiento')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>
    
</div>
<div id='login'></div>
<div id='error'></div>

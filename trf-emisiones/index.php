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

            }
        }
        xmlhttp.open("POST",urlok,true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(variables);

    }

    flag=true;

    function start_fields(){
        $('#f_gds, #f_pnr, #f_tipo, #datein').val('');
        $('#contain-pnr, #contain-gds, #contain-status, #contain-datein, #contain-tipo').attr('req',1);

    }

    start_fields();

    function validate(){
        //GDS
        if($('#contain-gds').attr('req')==1 && $('#f_gds').val()==''){
            flag_gds=false;
            $('#f_gds').addClass('error');
        }else{
            flag_gds=true;
            $('#f_gds').removeClass('error');
        }
        
        //Datec
        if($('#contain-datein').attr('req')==1 && $('#datein').val()==''){
            flag_datein=false;
            $('#contain-datein div p').addClass('error');
        }else{
            flag_datein=true;
            $('#contain-datein div p').removeClass('error');
        }

        //PNR
        if($('#contain-pnr').attr('req')==1 && $('#f_pnr').val()==''){
            flag_pnr=false;
            $('#f_pnr').addClass('error');
        }else{
            flag_pnr=true;
            $('#f_pnr').removeClass('error');
        }

		//Status
        if($('#contain-status').attr('req')==1 && $('#f_status').val()==''){
            flag_status=false;
            $('#f_status').addClass('error');
        }else{
            flag_status=true;
            $('#f_status').removeClass('error');
        }
        
        //Tipo
        if($('#contain-tipo').attr('req')==1 && $('#f_status').val()==''){
            flag_tipo=false;
            $('#f_tipo').addClass('error');
        }else{
            flag_tipo=true;
            $('#f_tipo').removeClass('error');
        }

        if(flag_gds && flag_pnr && flag_status && flag_datein && flag_tipo){flag=true;}else{flag=false;}
		
        
    }
    
    //Netactica
    $('#f_gds').change(function(){
    	if($(this).val()==5){
    		$('#contain-datein').hide('200').attr('req',0);
    		$('#datein').val('');
    	}else{
    		$('#contain-datein').hide('200').show('req',0);
    		$('#datein').val('');	
    	}	
    });
    
    //BUTTON Submit
    $('#submit_form').click(function(){
        validate();
        if(flag){
            var variables="asesor=<?php echo $asesor; ?>&gds=" + $('#f_gds').val() + "&pnr=" + $('#f_pnr').val() + "&status=" + $('#f_status').val() + "&datec=" +  $('#datein').val() + "&tipo=" +  $('#f_tipo').val();
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
    <div id='contain-gds' class='campo'>
        <div class='name'>
            <p>GDS</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_gds' class='input' req='1'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM trf_gds ORDER BY gds";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'trf_gds_id')."'>".mysql_result($result,$i,'gds')."</option>";
                $i++;
                }
            ?></select>*</p>
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
            <p>Tipo</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_tipo' class='input' req='1'><option value=''>Selecciona...</option>
            <option value='1'>Emision</option><option value='2'>Revisado</option><option value='3'>Cancelación</option></select>*</p>
        </div>
    </div>
    <div id='contain-status' class='campo'>
        <div class='name'>
            <p>Status</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_status' class='input' req='1'><option value=''>Selecciona...</option>
            <option value='1'>Emitido</option><option value='0'>Regresado</option></select>*</p>
        </div>
    </div>
    <div id='contain-pnr' class='campo'>
        <div class='name'>
            <p>PNR</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_pnr' class='input' value=''  req='1'>*</p>
        </div>
    </div>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>
    
</div>
<div id='login'></div>
<div id='error'></div>

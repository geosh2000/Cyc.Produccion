<?php
ini_set('session.gc_maxlifetime', 28800);
session_start();
header('Content-Type: text/html; charset=utf-8');


$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_tipificacion_sac";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");


$asesor=$_SESSION['asesor_id'];

?>

<script>

$(function(){

    $("#login").hide();

    $( "#f_other" ).autocomplete({
       source: 'search_other.php'
    });
    
    $('#searchbox').keyup(function(){
    	valor=$(this).val();
    	search_motivo(valor);
    })
    
    //search to form
    $('#result_search').on("click",'.b_search',function(){
    	var mg=$(this).attr('mg');
    	var me=$(this).attr('me');
    	var d=$(this).attr('d');
    	
    	//$('#errordiv').html("mg: "+mg+"<br>me: "+me+"<br>d: "+d);
    	
    	setTimeout(function(){
    		$('#f_mg').val(mg);
    		chMG(mg);
    		setTimeout(function(){
    			$('#f_me').val(me);	
    			chME(me);
    			setTimeout(function(){
    				$('#f_detalle').val(d);
    				chDE($('#f_detalle option:selected').attr('txt'));
    			},100);
    		},100);
    	},100);

    });
/*
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
    
    function search_motivo(variables){

	    var urlok='search_motivo.php';
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
                var total_res = text.match("-t-(.*)-t-");
                var total=parseInt(total_res[1]);
                var restxt="";
                var i=0;
					while(i<total){
						tmptxt = text.match("-route"+i+"-(.*)-route"+i+"-");
						tmpmg = text.match("-mg"+i+"-(.*)-mg"+i+"-");
						tmpme = text.match("-me"+i+"-(.*)-me"+i+"-");
						tmpd = text.match("-d"+i+"-(.*)-d"+i+"-");
						restxt=restxt+"<p class='searched'><button class='button button_green_w b_search' style='width:20px; height: 20px' mg='"+tmpmg[1]+"' me='"+tmpme[1]+"' d='"+tmpd[1]+"'>+</button>"+tmptxt[1]+"</p>\n";
					i++;
					} 
				$('#result_search').html(restxt);
            }
        }
        xmlhttp.open("POST",urlok,true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send("term="+variables);

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
                    var notif_calls = text.match("calls- (.*) -calls");
                    var notif_regs = text.match("regs- (.*) -regs");
                    var notif_lrreg = text.match("lrreg- (.*) -lrreg");
                    var notif_lrloc = text.match("lrloc- (.*) -lrloc");
                    $('#calls').text(notif_calls[1]);
                    $('#regs').text(notif_regs[1]);
                    $('#lr_reg').text(notif_lrreg[1]);
                    $('#lr_loc').text(notif_lrloc[1]);
                    perc= parseInt(notif_regs[1])/parseInt(notif_calls[1])*100;
                    $('#perc').text(perc.toFixed(2));
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
        $('#contain-me, #contain-detalle, #contain-other').hide().attr('req',0);
		$('#contain-canal, #contain-producto, #contain-mg').attr('req',1);
		$('#searchbox').val('');
		$('#result_search').empty();
    }

    start_fields();

    function validate(){
        //Nombre Canal
        if($('#contain-canal').attr('req')==1 && $('#f_canal').val()==''){
            flag_canal=false;
            $('#f_canal').addClass('error');
        }else{
            flag_canal=true;
            $('#f_canal').removeClass('error');
        }

        //Producto
        if($('#contain-producto').attr('req')==1 && $('#f_producto').val()==''){
            flag_producto=false;
            $('#f_producto').addClass('error');
        }else{
            flag_producto=true;
            $('#f_producto').removeClass('error');
        }

        //me
        if($('#contain-me').attr('req')==1 && $('#f_me').val()==''){
            flag_me=false;
            $('#f_me').addClass('error');
        }else{
            flag_me=true;
            $('#f_me').removeClass('error');
        }

        //mg
        if($('#contain-mg').attr('req')==1 && $('#f_mg').val()==''){
            flag_mg=false;
            $('#f_mg').addClass('error');
        }else{
            flag_mg=true;
            $('#f_mg').removeClass('error');
        }

        //detalle
        if($('#contain-detalle').attr('req')==1 && $('#f_detalle').val()==''){
            flag_detalle=false;
            $('#f_detalle').addClass('error');
        }else{
            flag_detalle=true;
            $('#f_detalle').removeClass('error');
        }

        //Otro
        if($('#contain-other').attr('req')==1 && $('#f_other').val()==''){
            flag_other=false;
            $('#f_other').addClass('error');
        }else{
            flag_other=true;
            $('#f_other').removeClass('error');
        }
        
        //Localizador
        if($('#contain-localizador').attr('req')==1 && $('#f_other').val()==''){
            flag_localizador=false;
            $('#f_localizador').addClass('error');
        }else{
            flag_localizador=true;
            $('#f_localizador').removeClass('error');
        }


        if(flag_canal && flag_producto && flag_mg && flag_me && flag_detalle && flag_other && flag_localizador){flag=true;}else{flag=false; $('#submit_form').prop('disabled',false);}

    }

    //MG
    function chMG(thisval){
    	$('#f_other').val('');
        $('#f_me').val('');
        $('#f_detalle').val('');
        $('#contain-other').hide('200').attr('req',0);
        var valor=thisval;
        $('.id_me').hide('200').attr('req',0);
        $('.me'+valor).show('200').attr('req',1);
        if($('.me'+valor).length==1){
        	var tmpval=$('.me'+valor).val();
        	//alert(tmpval);
        	$('#f_me').val(tmpval);
            $('#contain-detalle').show('200').attr('req',1);
            chME(tmpval);
            /*if($('.detalle'+tmpval).length==1){
            	//alert(tmpval);
	        	chME(tmpval);
	        }*/
            
        }else{chME('');}
        if($('#f_mg').val()!=''){
            $('#contain-me').show('200').attr('req',1);
        }else{
            $('#contain-me').hide('200').attr('req',0);
		}
    }
    
    $('#f_mg').change(function(){
    	chMG($(this).val());    
    });

    //ME
    function chME(thisval){
    	$('#f_detalle').val('');
        $('#f_other').val('');
        $('#contain-other').hide('200').attr('req',0);
        var valor=thisval;
        $('.id_detalle').hide('200').attr('req',0);
        $('.detalle'+valor).show('200').attr('req',1);
        //alert('.detalle'+valor+' // '+thisval);
        if($('.detalle'+valor).length==1){
        	var tmpval=$('.detalle'+valor).val();
        	$('#f_detalle').val(tmpval);
            $('#contain-detalle').show('200').attr('req',1);
            chDE($('option:selected', '#f_detalle').attr('txt'));
        }
        if($('#f_me').val()!=''){
            $('#contain-detalle').show('200').attr('req',1);
        }else{
            $('#contain-detalle').hide('200').attr('req',0);
        }	
    }
    $('#f_me').change(function(){
    	chME($(this).val());
    });


    //Detalle
    function chDE(thisval){
    	if(thisval==1){
            $('#contain-other').show('200').attr('req',1);
        }else{
            $('#contain-other').hide('200').attr('req',0);
        }	
    }
    $('#f_detalle').change(function(){
    	chDE($('option:selected', this).attr('txt'));    
    });


    //BUTTON Submit
    $('#submit_form').click(function(){
    	$(this).prop('disabled',true);
        validate();
        if(flag){
            var variables="asesor=<?php echo $asesor; ?>&canal=" + $('#f_canal').val() + "&producto=" + $('#f_producto').val() + "&mg=" + $('#f_mg').val() + "&me=" + $('#f_me').val() + "&detalle=" + $('#f_detalle').val() + "&otro=" + $('#f_other').val() + "&localizador=" + $('#f_localizador').val();
            //alert(variables);
            sendRequest(variables);
        }
    })

	//Change to UPPERCASE
    $('#f_other').keyup(function(){
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
    height: auto;
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

$query="SELECT Last_Update as Fecha, Localizador FROM sac_tipificacion WHERE asesor=$asesor ORDER BY Last_Update DESC LIMIT 1";
$result=mysql_query($query);
$lr_reg=mysql_result($result,0,'Fecha');
$lr_loc=mysql_result($result,0,'Localizador');

?>
<div style='float: left; width:60%; margin: auto;'>
<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>Tipificación de Llamadas - SAC</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'>Llamadas: <calls id='calls'><?php echo $calls; ?></calls> || Registros: <regs id='regs'><?php echo $regs; ?></regs> || <perc id='perc'><?php echo $perc; ?></perc>% de registros</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'>Ultimo Registro <lastreg id='lr_reg'><?php echo $lr_reg; ?></lastreg> || Localizador: <lastloc id='lr_loc'><?php echo $lr_loc; ?></lastloc></p>
    </div>
    <div id='contain-canal' class='campo'>
        <div class='name'>
            <p>Canal</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_canal' class='input'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM sac_canal ORDER BY Canal";
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
    <div id='contain-localizador' class='campo'>
        <div class='name'>
            <p>Localizador</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_localizador' class='input' value=''></p>
        </div>
    </div>
    <div id='contain-producto' class='campo'>
        <div class='name'>
            <p>Producto o Servicio</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_producto' class='input'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM sac_productos ORDER BY Producto";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'Producto')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-mg' class='campo'>
        <div class='name'>
            <p>Motivo General</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_mg' class='input'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM sac_motivos_generales WHERE Activo=1 ORDER BY Motivo_General";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option value='".mysql_result($result,$i,'id')."'>".mysql_result($result,$i,'Motivo_General')."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-me' class='campo'>
        <div class='name'>
            <p id='title_me'>Motivo Específico</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_me' class='input'><option value=''>Selecciona...</option><?php
                $query="SELECT * FROM sac_motivos_especificos ORDER BY Motivo_Especifico";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option class='id_me me".mysql_result($result,$i,'motivo_general')."' value='".mysql_result($result,$i,'id')."'>".utf8_encode(mysql_result($result,$i,'Motivo_Especifico'))."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-detalle' class='campo'>
        <div class='name'>
            <p id='title_detalle'>Motivo Detallado</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_detalle' class='input'><option value=''>Selecciona...</option><?php
                $query="SELECT * FROM sac_detalle ORDER BY Detalle";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option class='id_detalle detalle".mysql_result($result,$i,'motivo_especifico')."' value='".mysql_result($result,$i,'id')."' txt='".mysql_result($result,$i,'Texto')."'>".utf8_encode(mysql_result($result,$i,'Detalle'))."</option>\n\t";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-other' class='campo'>
        <div class='name'>
            <p>Otro</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_other' class='input' value=''>*</p>
        </div>
    </div>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>

</div>
<div id='errordiv'></div>
</div>
<div style='float:right; width: 30%; margin: auto;'>
	<div class='formulario' style='width:100%'>
		<div class='titulo' style='width:100%; z-index: 100'>
			<p style='padding-top: 13px; color: white;'>Búsqueda de Motivos</p>
        	<p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'>Busca el motivo para conocer la ruta correcta</p>
		</div>
		<p style='text-align: center'><input type='text' id='searchbox' placeholder="Escribe tu palabra clave">
		<div id='result_search' style='text-align: left'></div>
	</div>
</div>
		
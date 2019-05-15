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
$area="funciones";

$query="SELECT * FROM ME_opts WHERE activo=1 ORDER BY nivel, opcion";
if($result=$connectdb->query($query)){
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_array(MYSQLI_BOTH)){
		for($i=0;$i<$result->field_count;$i++){
			$opts[$fila['actividad']][$fila['nivel']]['titulo']=utf8_encode($fila['titulo']);
			$opts[$fila['actividad']][$fila['nivel']]['tipo']=utf8_encode($fila['tipo']);
			$opts[$fila['actividad']][$fila['nivel']][$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
		}
	}
}

//print_r($opts);


?>
<link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.css"/>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js'></script>

<script>
$(function(){
	
	$("#login").hide();
	
	dialogLoad=$( "#dialog-load" ).dialog({
      modal: true,
      autoOpen: false
    });
    
    progressbarload=$('#progressbarload').progressbar({
	      value: false
	});
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

    function sendRequest(){
    	
    	dialogLoad.dialog('open');
    	
    	validateFields();
        if(validfields){
        	
        	dataString['asesor']=<?php echo $_SESSION['asesor_id']; ?>;
        	
        	$.ajax({
        		url: 'save.php',
        		type: 'POST',
        		data: dataString, 
        		dataType: 'html',
        		success: function(data){
        			if(data=='DONE'){
        				var msg = "Registro Exitoso";
        				var tipo = 'success';
        				var open = 'animated flipInX';
        				var time = 3000;
        				$('input').val('');
        				$('select').val('');
        			}else{
        				var msg = data;
        				var tipo = 'error';
        				var open = 'animated shake';
        				var time = 10000;
        			}
        			
        			dialogLoad.dialog('close');
        			
        			new noty({
	        			text: msg,
	                    type: tipo,
	                    timeout: time,
	                    layout: 'topCenter',
	                    animation: {
	                        open: open, // jQuery animate function property object
	                        close: {height: 'toggle'}, // jQuery animate function property object
	                        easing: 'swing', // easing
	                        speed: 500 // opening & closing animation speed
	                    }
	                });
	                
        		},
        		error: function(){
        			
        			dialogLoad.dialog('close');
        			
        			new noty({
	        			text: "Error de comunicacion",
	                    type: 'error',
	                    timeout: 5000,
	                    layout: 'topCenter',
	                    animation: {
	                        open: 'animated shake', // jQuery animate function property object
	                        close: {height: 'toggle'}, // jQuery animate function property object
	                        easing: 'swing', // easing
	                        speed: 500 // opening & closing animation speed
	                    }
	                });
	                
        		}
        	});
        }else{
        	
        	dialogLoad.dialog('close');
        	
        	new noty({
    			text: "Falta seleccionar un campo, o el texto no coincide con el formato requerido",
                type: 'error',
                timeout: 1500,
                layout: 'center',
                animation: {
                    open: 'animated pulse', // jQuery animate function property object
                    close: 'animated fadeOutUpBig', // jQuery animate function property object
                    easing: 'swing', // easing
                    speed: 500 // opening & closing animation speed
                }
            });
        }

    }

    function startFields(){
    	//$('.containers, .opts, .default').hide();
    	//$('.camposel').removeClass('error').val("").attr('req',0);
    	$('#f_actividad').attr('req',1);
    }
    
    startFields();

    //BUTTON Submit
    $('#submit_form').click(function(){
    	sendRequest();
    });
    
    //Change to UPPERCASE
    $('#f_pnr, #f_codigo_aerolinea').keyup(function(){
        var name=$(this).val();
        var newname=name.toUpperCase();
        $(this).val(newname);
    });
    
    validfields = true;
    
    
    function validateFields(){
    	 validfields = true;
    	 
    	 parameters = {};
    	
    	$('.ch_send').each(function(){
    		var key=$(this).find('input').attr('id');
    		var value=$(this).find('input').val();
    		
    	});
    	
    	 $('select').each(function(){
    	 	$(this).removeClass('error');
    	 	parameters[$(this).attr('id')] = $(this).val();
    	 	if($(this).val()==''){
    	 		validfields = false;
			    $(this).addClass('error');
    	 	}
    	 });
    	 
    	 $('input:text').each(function(){
    	 	$(this).removeClass('error');
    	 	parameters[$(this).attr('id')] = $(this).val()
    	 	
    	 	var regextxt = (typeof $(this).attr('regex') === 'undefined') ? false : true;
    	 	var required = $(this).attr('req');
    	 	var texto=$(this).val();
    	 	
    	 	if(required==1 && texto==""){
    	 		validfields = false;
	       		$(this).addClass('error');
    	 	}
    	 	
    	 	if(texto!=""){
    	 		if(regextxt){
	    	 		var regex=new RegExp($(this).attr('regex'));
					var test_r = regex.test(texto);
			       	if(!test_r){
			       		validfields = false;
			       		$(this).addClass('error');
			       		
			       	}
	    	 	}
	    	 }
    	 });
    	 
    	 dataString = parameters;
    }
    
    function setDefaults(){
    }
    
    $('.opts').each(function(){
		var parent = $(this).attr('parent');
		
		if(parent!=0){
			$(this).hide();
		}
		
	});
	
	$('.camposel').change(function(){
		var valor=$(this).val();
		$('.opts').each(function(){
			var parent = $(this).attr('parent');
			
			if(parent!=0){
				
				parentflag=false;
				
				$('.camposel').each(function(){
					if($(this).val()==parent){
						parentflag=true;
					}
				});
				
				if(parentflag){
					$(this).show();
				}else{
					$(this).hide();
					if($(this).closest('select').val()==$(this).attr('value')){
						$(this).closest('select').val('');
					}
				}
				
			}
			
		});
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
        <p style='padding-top: 13px; color: white;'>Tráfico MP - Funciones</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'><?php echo $_SESSION['name'];?></p>
    </div>
    <?php
    	foreach($opts as $actividad => $data){
    		foreach($data as $nivel => $info){
				echo "<div id='contain-level".$nivel."' class='campo containers actividad_$actividad' level='$nivel'>
			        	<div class='name'>
				            <p id='lev".$nivel."title'>".$info['titulo']."</p>
				        </div>
				        <div class='opcion'>";	
				
				switch($info['tipo']){
					case 'select':	
						echo "<p class='seleccion'><select id='f_level".$nivel."' class='input levelSelect camposel' req='1' tipo='select'><option value=''>Selecciona...</option>";
						
						foreach($info as $id => $opcion){
							if($id == 'titulo' || $id == 'tipo'){ continue; }
							echo "<option value='$id' id='input_$id' nombre='".$opcion['opcion']."' class='options$nivel act_$actividad opts' trig='".$opcion['trigger']."' parent='".$opcion['parent']."'>".$opcion['opcion']."</option>";
						}	
						
						echo "</select>*</p>";
						break;
					case 'text':
						echo "<p class='seleccion'>";
							foreach($info as $id => $opcion){
								if($id == 'titulo' || $id == 'tipo'){ continue; }
								echo "<input type='text' id='f_level".$nivel."' nombre='".$opcion['opcion']."' parent='".$opcion['parent']."' class='input levelSelect camposel act_$actividad' req='".$opcion['required']."' regex='".$opcion['regex']."'>";
							}
		    			echo "</p>";
						break;
				}
				
				echo "</div>
					  </div>\n\n";					
			}
		}
			
    ?>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>
    
</div>
</div>
<div style='float:left; width: 30%; margin: auto;'>
<div id="sidebar">
   <iframe id='regs' width='100%' height='100%' style='border: 0;' src='registros.php?area=<?php echo $area; ?>'></iframe>
 </div>
 </div>
<div id='login'></div>
<div id='error'></div>
<div id="dialog-load" title="Guardando Registro" style='text-align: center'>
	<div id="progressbarload"></div>
</div>

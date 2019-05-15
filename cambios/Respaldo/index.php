<?php
include_once("../modules/modules.php");

initSettings::start(true,"schedules_change");
initSettings::printTitle('Cambios de Turno');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$query="SELECT `N Corto`, id, `id Departamento` FROM Asesores WHERE Activo=1 ORDER BY `N Corto`";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$selectAsesores.="<option value='".$fila['id']."' class='selectable dep".$fila['id Departamento']."'>".$fila['N Corto']."</option>\n\t";
	}
}else{
	echo "Error al bajar info de asesores -> ".$connectdb->error;
}

?>

<script>

$(function(){


	$('#display_data').hide();

	dialogDetail = $( "#dialog-detail" ).dialog({
      modal: true,
      width: 900,
      autoOpen: false,
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });

	//$('#display').hide();

	//format timepicker
    $('body').on('focus',".timepicker", function(){
	    $(this).timepicker();
	});

	$(  '.sh_restringido' ).tooltip({
		items: "[title]",
    	content: function(){
    		var element=$(this);
    		if(element.is("[title]")){
    			return element.attr('title');
    		}
    	}
    });

	$('#name').attr('disabled',true);
	$('#tipo').attr('disabled',true);
	$('.asesor_selection2, .fecha_selection2').hide();

	$('#fecha1').periodpicker({
		lang: 'en',
		norange: true,
		formatDate: 'YYYY-MM-DD',
		animation: true,
		onAfterHide: function () {
				if($('#fecha1').val()!=""){
					$( "#name" ).catcomplete( "option", "source", '/config/search_name.php?date='+$('#fecha1').val() );
					$('#name').attr('disabled',false).val('');
				}else{
					$('#name').attr('disabled',true).val('');
					$('#tipo').attr('disabled',true).val('');
				}

				$('#tipo').attr('disabled',true).val('');
				$('.asesor_selection2, .fecha_selection2').hide();
				$('#asesor2').val('');
				$('#fecha2').periodpicker('clear');
		 }
	});

	$('#fecha2').periodpicker({
		lang: 'en',
		norange: true,
		formatDate: 'YYYY-MM-DD',
		animation: true
	});

	$('#tipo').change(function(){
		var selection = $(this).val();
		switch(selection){
			case '1':
				populateAsesor(2);
				$('#asesor_selection1').attr('rowspan',1).find('select').attr('req',1);
				$('.asesor_selection2').show().find('select').attr('req',1);
				$('#fecha_selection1').attr('rowspan',2).find('input').attr('req',1);
				$('.fecha_selection2').hide().find('#fecha2').val('').attr('req',0);
				break;
			case '2':
			case '5':
				populateAsesor(2);
				$('#asesor_selection1').attr('rowspan',1).find('select').attr('req',1);
				$('.asesor_selection2').show().find('select').attr('req',1);
				$('#fecha_selection1').attr('rowspan',1).find('input').attr('req',1);
				$('.fecha_selection2').show().find('input').attr('req',1);
				break;
			case '3':
				$('#asesor_selection1').attr('rowspan',2).find('select').attr('req',1);
				$('.asesor_selection2').hide().find('#asesor2').val('').attr('req',0);
				$('#fecha_selection1').attr('rowspan',2).find('input').attr('req',1);
				$('.fecha_selection2').hide().find('#fecha2').val('').attr('req',0);
				break;
			case '4':
				populateAsesor(2);
				$('#asesor_selection1').attr('rowspan',1).find('select').attr('req',1);
				$('.asesor_selection2').show().find('select').attr('req',1);
				$('#fecha_selection1').attr('rowspan',2).find('input').attr('req',1);
				$('.fecha_selection2').hide().find('#fecha2').val('').attr('req',0);
				break;
		}
	});

	$('#departamento').change(function(){
		var dep = $(this).val();
		$('#asesor1, #asesor2').val('');
		$('.selectable').hide();
		$('.dep'+dep).show();
	});

	$('#test').click(function(){
		if($('#asesor1').attr('rowspan')==2){
			$('#asesor1').attr('rowspan',1);
			$('#asesor2').show();
		}else{
			$('#asesor1').attr('rowspan',2);
			$('#asesor2').hide();
		}

	});

	function addRowShow(indice_asesor, indice_fecha){
		$('#show_horarios').append("<tr class='pair show_dynamic' id='show_asesor"+indice_asesor+"_f"+indice_fecha+"'>"
		+"<td class='sh_idh'>Id Horario</td>"
        +"<td class='sh_ida'>Id Asesor</td>"
		+"<td class='sh_nombre'>Asesor</td>"
		+"<td class='sh_esquema'>Esquema</td>"
		+"<td class='sh_fecha'>Fecha</td>"
		+"<td class='sh_horario'>Horario</td>"
        +"<td class='sh_comida'>Comida</td>"
        +"<td class='sh_x1'>Horas Extra 1</td>"
        +"<td class='sh_x2'>Horas Extra 2</td>"
        +"<td class='sh_restringido'>Restringido</td>"
		+"<td class='sh_cambios'>Cambios en<br>el mes</td>"
		+"</tr>");
	}

	function addRowChange(indice_asesor, indice_fecha){
		$('#show_changes').append("<tr class='pair show_dynamic' id='show_ch_asesor"+indice_asesor+"_f"+indice_fecha+"'>"
		+"<td class='ch_idh ch_send'><input type='text' value='' class='idh' id='idh"+indice_fecha+indice_asesor+"' readonly></td>"
        +"<td class='ch_nombre'>Asesor</td>"
		+"<td class='ch_esquema'>Esquema</td>"
		+"<td class='ch_fecha ch_send'><input type='text' value='' class='fecha' id='fecha"+indice_fecha+indice_asesor+"' readonly></td>"
		+"<td class='ch_js ch_send'><input type='text' value='' class='timepicker js' id='js"+indice_fecha+indice_asesor+"'></td>"
        +"<td class='ch_je ch_send'><input type='text' value='' class='timepicker je' id='je"+indice_fecha+indice_asesor+"'></td>"
        +"<td class='ch_cs ch_send'><input type='text' value='' class='timepicker cs' id='cs"+indice_fecha+indice_asesor+"'></td>"
        +"<td class='ch_ce ch_send'><input type='text' value='' class='timepicker ce' id='ce"+indice_fecha+indice_asesor+"'></td>"
        +"<td class='ch_x1s ch_send'><input type='text' value='' class='timepicker x1s' id='x1s"+indice_fecha+indice_asesor+"'></td>"
        +"<td class='ch_x1e ch_send'><input type='text' value='' class='timepicker x1e' id='x1e"+indice_fecha+indice_asesor+"'></td>"
        +"<td class='ch_x2s ch_send'><input type='text' value='' class='timepicker x2s' id='x2s"+indice_fecha+indice_asesor+"'></td>"
        +"<td class='ch_x2e ch_send'><input type='text' value='' class='timepicker x2e' id='x2e"+indice_fecha+indice_asesor+"'></td>"
        +"</tr>");


	}

	function getData(){

		showLoader('Obteniendo Información');

		var tipo = $('#tipo').val();
		asesor = [];
		fecha = [];
		switch(tipo){
			case '1':
				asesor[1] = $('#asesor1').val();
				asesor[2] = $('#asesor2').val();
				fecha[1] = $('#fecha1').val();
				fecha[2] = $('#fecha1').val();
				break;
			case '2':
			case '5':
				asesor[1] = $('#asesor1').val();
				asesor[2] = $('#asesor2').val();
				fecha[1] = $('#fecha1').val();
				fecha[2] = $('#fecha2').val();
				break;
			case '3':
				asesor[1] = $('#asesor1').val();
				asesor[2] = $('#asesor1').val();
				fecha[1] = $('#fecha1').val();
				fecha[2] = $('#fecha1').val();
				break;
			case '4':
				asesor[1] = $('#asesor1').val();
				asesor[2] = $('#asesor2').val();
				fecha[1] = $('#fecha1').val();
				fecha[2] = $('#fecha1').val();
				break;
		}

		fecha['orig1'] = $('#fecha1').val();
		fecha['orig2'] = $('#fecha2').val();
		asesor['orig1'] = $('#asesor1').val();
		asesor['orig2'] = $('#asesor2').val();

		//alert('asesor1: '+asesor[1]+', asesor2: '+asesor[2]+', fecha1: '+fecha[1]+', fecha2: '+fecha[2]);

		$.ajax({
			url: "query.php",
            type: "POST",
            data: {asesor1: asesor[1], asesor2: asesor[2], fecha1: fecha[1], fecha2: fecha[2] },
            dataType: "json", // will automatically convert array to JavaScript
            success: function(array) {
            	data=array;

            	for(i=1;i<=2;i++){
            		for(x=1;x<=2;x++){
            			var start = (typeof data[asesor['orig'+i]] === 'undefined') ? false : true;

            			if(start){
            				var flag = (typeof data[asesor[i]][fecha['orig'+x]] === 'undefined') ? false : true;
            			}else{
            				var flag = false;
            			}

            			if(flag){

            				//SHOW
            				addRowShow(i,x);
            				$('#show_asesor'+i+'_f'+x).find('.sh_idh').text(data[asesor[i]][fecha[x]]['id']);
            				$('#show_asesor'+i+'_f'+x).find('.sh_ida').text(data[asesor[i]][fecha[x]]['asesor']);
            				$('#show_asesor'+i+'_f'+x).find('.sh_nombre').text(data[asesor[i]][fecha[x]]['Nombre']);
            				$('#show_asesor'+i+'_f'+x).find('.sh_esquema').text(data[asesor[i]][fecha[x]]['Esquema']);
            				$('#show_asesor'+i+'_f'+x).find('.sh_fecha').text(data[asesor[i]][fecha[x]]['Fecha']);
            				if(data[asesor[i]][fecha[x]]['je'] == data[asesor[i]][fecha[x]]['js']){
            					$('#show_asesor'+i+'_f'+x).find('.sh_horario').text("Descanso");
	            				$('#show_asesor'+i+'_f'+x).find('.sh_comida').text("Descanso");
	            				$('#show_asesor'+i+'_f'+x).find('.sh_x1').text("Descanso");
	            				$('#show_asesor'+i+'_f'+x).find('.sh_x2').text("Descanso");
            				}else{
            					$('#show_asesor'+i+'_f'+x).find('.sh_horario').text(data[asesor[i]][fecha[x]]['js']+" - "+data[asesor[i]][fecha[x]]['je']);
	            				$('#show_asesor'+i+'_f'+x).find('.sh_comida').text(data[asesor[i]][fecha[x]]['cs']+" - "+data[asesor[i]][fecha[x]]['ce']);
	            				$('#show_asesor'+i+'_f'+x).find('.sh_x1').text(data[asesor[i]][fecha[x]]['x1s']+" - "+data[asesor[i]][fecha[x]]['x1e']);
	            				$('#show_asesor'+i+'_f'+x).find('.sh_x2').text(data[asesor[i]][fecha[x]]['x2s']+" - "+data[asesor[i]][fecha[x]]['x2e']);
            				}

            				$('#show_asesor'+i+'_f'+x).find('.sh_restringido').text(data[asesor[i]][fecha[x]]['restringido']).attr('title',data[asesor[i]][fecha[x]]['restriccion']);
            				if(data[asesor[i]][fecha[x]]['restringido']!='No'){
            					$('#show_asesor'+i+'_f'+x).find('.sh_restringido').addClass('required');
            					flag_restrict = false;
            				}else{
            					$('#show_asesor'+i+'_f'+x).find('.sh_restringido').removeClass('required');
            				}

            				$('#show_asesor'+i+'_f'+x).find('.sh_cambios').text(data[asesor[i]][fecha[x]]['cambios']);

            				//Change
            				addRowChange(i,x);
            				$('#show_ch_asesor'+i+'_f'+x).find('.ch_idh').find('input').val(data[asesor[i]][fecha[x]]['id']);
            				$('#show_ch_asesor'+i+'_f'+x).find('.ch_nombre').text(data[asesor[i]][fecha[x]]['Nombre']);
            				$('#show_ch_asesor'+i+'_f'+x).find('.ch_esquema').text(data[asesor[i]][fecha[x]]['Esquema']);
            				$('#show_ch_asesor'+i+'_f'+x).find('.ch_fecha').find('input').val(data[asesor[i]][fecha[x]]['Fecha']);
            			}
            		}
            	}

            	switch(tipo){
            		case '1':
						$('#show_ch_asesor1_f1').find('.ch_js').find('input').val(data[asesor[2]][fecha[1]]['js']);
						$('#show_ch_asesor1_f1').find('.ch_je').find('input').val(data[asesor[2]][fecha[1]]['je']);
						$('#show_ch_asesor1_f1').find('.ch_cs').find('input').val(data[asesor[2]][fecha[1]]['cs']);
						$('#show_ch_asesor1_f1').find('.ch_ce').find('input').val(data[asesor[2]][fecha[1]]['ce']);
						$('#show_ch_asesor1_f1').find('.ch_x1s').find('input').val(data[asesor[2]][fecha[1]]['x1s']);
						$('#show_ch_asesor1_f1').find('.ch_x1e').find('input').val(data[asesor[2]][fecha[1]]['x1e']);
						$('#show_ch_asesor1_f1').find('.ch_x2s').find('input').val(data[asesor[2]][fecha[1]]['x2s']);
						$('#show_ch_asesor1_f1').find('.ch_x2e').find('input').val(data[asesor[2]][fecha[1]]['x2e']);

						$('#show_ch_asesor2_f1').find('.ch_js').find('input').val(data[asesor[1]][fecha[1]]['js']);
						$('#show_ch_asesor2_f1').find('.ch_je').find('input').val(data[asesor[1]][fecha[1]]['je']);
						$('#show_ch_asesor2_f1').find('.ch_cs').find('input').val(data[asesor[1]][fecha[1]]['cs']);
						$('#show_ch_asesor2_f1').find('.ch_ce').find('input').val(data[asesor[1]][fecha[1]]['ce']);
						$('#show_ch_asesor2_f1').find('.ch_x1s').find('input').val(data[asesor[1]][fecha[1]]['x1s']);
						$('#show_ch_asesor2_f1').find('.ch_x1e').find('input').val(data[asesor[1]][fecha[1]]['x1e']);
						$('#show_ch_asesor2_f1').find('.ch_x2s').find('input').val(data[asesor[1]][fecha[1]]['x2s']);
						$('#show_ch_asesor2_f1').find('.ch_x2e').find('input').val(data[asesor[1]][fecha[1]]['x2e']);

						$('#send_changes').find("th").prepend("<input class='show_dynamic' id='caso' req='1' type='text'>  ");
						break;
					case '2':
					case '5':
						$('#show_ch_asesor1_f1').find('.ch_js').find('input').val(data[asesor[2]][fecha[1]]['js']);
						$('#show_ch_asesor1_f1').find('.ch_je').find('input').val(data[asesor[2]][fecha[1]]['je']);
						$('#show_ch_asesor1_f1').find('.ch_cs').find('input').val(data[asesor[2]][fecha[1]]['cs']);
						$('#show_ch_asesor1_f1').find('.ch_ce').find('input').val(data[asesor[2]][fecha[1]]['ce']);
						$('#show_ch_asesor1_f1').find('.ch_x1s').find('input').val(data[asesor[2]][fecha[1]]['x1s']);
						$('#show_ch_asesor1_f1').find('.ch_x1e').find('input').val(data[asesor[2]][fecha[1]]['x1e']);
						$('#show_ch_asesor1_f1').find('.ch_x2s').find('input').val(data[asesor[2]][fecha[1]]['x2s']);
						$('#show_ch_asesor1_f1').find('.ch_x2e').find('input').val(data[asesor[2]][fecha[1]]['x2e']);

						$('#show_ch_asesor2_f1').find('.ch_js').find('input').val(data[asesor[1]][fecha[1]]['js']);
						$('#show_ch_asesor2_f1').find('.ch_je').find('input').val(data[asesor[1]][fecha[1]]['je']);
						$('#show_ch_asesor2_f1').find('.ch_cs').find('input').val(data[asesor[1]][fecha[1]]['cs']);
						$('#show_ch_asesor2_f1').find('.ch_ce').find('input').val(data[asesor[1]][fecha[1]]['ce']);
						$('#show_ch_asesor2_f1').find('.ch_x1s').find('input').val(data[asesor[1]][fecha[1]]['x1s']);
						$('#show_ch_asesor2_f1').find('.ch_x1e').find('input').val(data[asesor[1]][fecha[1]]['x1e']);
						$('#show_ch_asesor2_f1').find('.ch_x2s').find('input').val(data[asesor[1]][fecha[1]]['x2s']);
						$('#show_ch_asesor2_f1').find('.ch_x2e').find('input').val(data[asesor[1]][fecha[1]]['x2e']);

						$('#show_ch_asesor1_f2').find('.ch_js').find('input').val(data[asesor[2]][fecha[2]]['js']);
						$('#show_ch_asesor1_f2').find('.ch_je').find('input').val(data[asesor[2]][fecha[2]]['je']);
						$('#show_ch_asesor1_f2').find('.ch_cs').find('input').val(data[asesor[2]][fecha[2]]['cs']);
						$('#show_ch_asesor1_f2').find('.ch_ce').find('input').val(data[asesor[2]][fecha[2]]['ce']);
						$('#show_ch_asesor1_f2').find('.ch_x1s').find('input').val(data[asesor[2]][fecha[2]]['x1s']);
						$('#show_ch_asesor1_f2').find('.ch_x1e').find('input').val(data[asesor[2]][fecha[2]]['x1e']);
						$('#show_ch_asesor1_f2').find('.ch_x2s').find('input').val(data[asesor[2]][fecha[2]]['x2s']);
						$('#show_ch_asesor1_f2').find('.ch_x2e').find('input').val(data[asesor[2]][fecha[2]]['x2e']);

						$('#show_ch_asesor2_f2').find('.ch_js').find('input').val(data[asesor[1]][fecha[2]]['js']);
						$('#show_ch_asesor2_f2').find('.ch_je').find('input').val(data[asesor[1]][fecha[2]]['je']);
						$('#show_ch_asesor2_f2').find('.ch_cs').find('input').val(data[asesor[1]][fecha[2]]['cs']);
						$('#show_ch_asesor2_f2').find('.ch_ce').find('input').val(data[asesor[1]][fecha[2]]['ce']);
						$('#show_ch_asesor2_f2').find('.ch_x1s').find('input').val(data[asesor[1]][fecha[2]]['x1s']);
						$('#show_ch_asesor2_f2').find('.ch_x1e').find('input').val(data[asesor[1]][fecha[2]]['x1e']);
						$('#show_ch_asesor2_f2').find('.ch_x2s').find('input').val(data[asesor[1]][fecha[2]]['x2s']);
						$('#show_ch_asesor2_f2').find('.ch_x2e').find('input').val(data[asesor[1]][fecha[2]]['x2e']);

						if(tipo==2){
							$('#send_changes').find("th").prepend("<input class='show_dynamic' id='caso' req='1' type='text'>  ");
						}
						break;
					case '3':
						$('#show_ch_asesor1_f1').find('.ch_js').find('input').val(data[asesor[1]][fecha[1]]['js']);
						$('#show_ch_asesor1_f1').find('.ch_je').find('input').val(data[asesor[1]][fecha[1]]['je']);
						$('#show_ch_asesor1_f1').find('.ch_cs').find('input').val(data[asesor[1]][fecha[1]]['cs']);
						$('#show_ch_asesor1_f1').find('.ch_ce').find('input').val(data[asesor[1]][fecha[1]]['ce']);
						$('#show_ch_asesor1_f1').find('.ch_x1s').find('input').val(data[asesor[1]][fecha[1]]['x1s']);
						$('#show_ch_asesor1_f1').find('.ch_x1e').find('input').val(data[asesor[1]][fecha[1]]['x1e']);
						$('#show_ch_asesor1_f1').find('.ch_x2s').find('input').val(data[asesor[1]][fecha[1]]['x2s']);
						$('#show_ch_asesor1_f1').find('.ch_x2e').find('input').val(data[asesor[1]][fecha[1]]['x2e']);

						break;
					case '4':
						$('#show_ch_asesor1_f1').find('.ch_js').find('input').val(data[asesor[2]][fecha[1]]['js']);
						$('#show_ch_asesor1_f1').find('.ch_je').find('input').val(data[asesor[2]][fecha[1]]['je']);
						$('#show_ch_asesor1_f1').find('.ch_cs').find('input').val(data[asesor[2]][fecha[1]]['cs']);
						$('#show_ch_asesor1_f1').find('.ch_ce').find('input').val(data[asesor[2]][fecha[1]]['ce']);
						$('#show_ch_asesor1_f1').find('.ch_x1s').find('input').val(data[asesor[2]][fecha[1]]['x1s']);
						$('#show_ch_asesor1_f1').find('.ch_x1e').find('input').val(data[asesor[2]][fecha[1]]['x1e']);
						$('#show_ch_asesor1_f1').find('.ch_x2s').find('input').val(data[asesor[2]][fecha[1]]['x2s']);
						$('#show_ch_asesor1_f1').find('.ch_x2e').find('input').val(data[asesor[2]][fecha[1]]['x2e']);

						$('#show_ch_asesor2_f1').find('.ch_js').find('input').val(data[asesor[1]][fecha[1]]['js']);
						$('#show_ch_asesor2_f1').find('.ch_je').find('input').val(data[asesor[1]][fecha[1]]['je']);
						$('#show_ch_asesor2_f1').find('.ch_cs').find('input').val(data[asesor[1]][fecha[1]]['cs']);
						$('#show_ch_asesor2_f1').find('.ch_ce').find('input').val(data[asesor[1]][fecha[1]]['ce']);
						$('#show_ch_asesor2_f1').find('.ch_x1s').find('input').val(data[asesor[1]][fecha[1]]['x1s']);
						$('#show_ch_asesor2_f1').find('.ch_x1e').find('input').val(data[asesor[1]][fecha[1]]['x1e']);
						$('#show_ch_asesor2_f1').find('.ch_x2s').find('input').val(data[asesor[1]][fecha[1]]['x2s']);
						$('#show_ch_asesor2_f1').find('.ch_x2e').find('input').val(data[asesor[1]][fecha[1]]['x2e']);

						break;
            	}
            	dialogLoad.dialog('close');
            	$('#display_data').show();
            },
            error: function(){
            	dialogLoad.dialog('close');
            	alert('Error');
        	}
        });
	}



    $('#search').click(function(){
    	$('#test').html();
    	$('#display').hide();
    	var allFields = $([]).add($('#asesor1')).add($('#asesor2')).add($('#fecha1')).add($('#fecha2')).add($('#tipo'));
    	var flag=true;
    	allFields.each(function(){
    		$(this).removeClass('required').parent().removeClass("required");
    		//alert($(this).attr('req'));
    		if($(this).attr('req')==1){
    			if($(this).val()==''){
    				flag=false;
    				$(this).addClass( "required" ).parent().addClass("required");
    			}
    		}
    	});

    	if(flag){
	    	$('.show_dynamic').remove();
	    	flag_restrict = true;
	    	getData();
	    }else{
	    	new noty({
                text: "Los campos marcados en rojo, son requeridos",
                type: "error",
                timeout: 3000,
                animation: {
                    open: {height: 'toggle'}, // jQuery animate function property object
                    close: {height: 'toggle'}, // jQuery animate function property object
                    easing: 'swing', // easing
                    speed: 500 // opening & closing animation speed
                }
            });
	    }
    });

    function applyChange(){
    	showLoader('Aplicando Cambios');
    	parameters = [];

    	$('.ch_send').each(function(){
    		var key=$(this).find('input').attr('id');
    		var value=$(this).find('input').val();
    		parameters.push({[key]:value});
    	});

    	dataString = parameters;

    	$.ajax({
    		url: 'applyChange.php',
    		type: 'POST',
    		data: {data: dataString, tipo: $('#tipo').val(), asesor1: $('#asesor1').val(),asesor2: $('#asesor2').val(), caso: $('#caso').val(), user: <?php echo $_SESSION['id']; ?>},
    		dataType: 'html',
    		success: function(data){
    			if(data=="DONE"){
    				new noty({
		                text: "Cambios aplicados",
		                type: "success",
		                timeout: 3000,
		                layout: 'topCenter',
		                animation: {
		                    open: 'animated bounceInDown', // jQuery animate function property object
		                    close: 'animated zoomOutUp', // jQuery animate function property object
		                    easing: 'swing', // easing
		                    speed: 500 // opening & closing animation speed
		                }
		            });
		            dialogLoad.dialog('close');
		          	$('#display_data').hide();
    			}else{
    				new noty({
		                text: data,
		                type: "error",
		                timeout: 10000,
		                layout: 'topCenter',
		                animation: {
		                    open: 'animated shake', // jQuery animate function property object
		                    close: 'animated zoomOutUp', // jQuery animate function property object
		                    easing: 'swing', // easing
		                    speed: 500 // opening & closing animation speed
		                }
		            });
		            $('#test').html(data);
    				dialogLoad.dialog('close');
    			}

    		},
    		error: function(){
    			new noty({
		                text: "Error al cargar informacion",
		                type: "error",
		                timeout: 10000,
		                layout: 'topCenter',
		                animation: {
		                    open: 'animated shake', // jQuery animate function property object
		                    close: 'animated zooOutUp', // jQuery animate function property object
		                    easing: 'swing', // easing
		                    speed: 500 // opening & closing animation speed
		                }
		            });
    				dialogLoad.dialog('close');
    		}
    	});

    }

    function getChanges(){
    	showLoader('Obteniendo Cambios');

    	$.ajax({
    		url: 'getChanges.php',
    		type: 'POST',
    		data: {asesor1: asesor[1], asesor2: asesor[2], fecha1: fecha[1], fecha2: fecha[2] },
    		dataType: 'html',
    		success: function(data){
    			$('#dialog-detail').html(data);
    			dialogDetail.dialog('open');
    			dialogLoad.dialog('close');
    		},
    		error: function(){
    			alert("Error al recibir detalle de cambios");
    			dialogLoad.dialog('close');
    		}
    	});
    }

    $('#aplicar').click(function(){
    	$('#test').html();
    	var apply_flag=true;

    	if($('#caso').attr('req')==1){
    		var caso=$('#caso').val();
    		var patt= /^\d{6,7}$/g;
    		if(!patt.test(caso)){
    			apply_flag=false;
    			alert('El caso debe contener solo numeros, y no puede estar vacio');
    		}
    	}

    	if(apply_flag){
    		applyChange();
    	}

    });

    $('#detalle').click(function(){
    	getChanges();
    });

		$.widget( "custom.catcomplete", $.ui.autocomplete, {
				_create: function() {
					this._super();
					this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
				},
				_renderMenu: function( ul, items ) {
					var that = this,
						currentCategory = "";
					$.each( items, function( index, item ) {
						var li;
						if ( item.category != currentCategory ) {
							ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
							currentCategory = item.category;
						}
						li = that._renderItemData( ul, item );
						if ( item.category ) {
							li.attr( "aria-label", item.category + " : " + item.label );
						}
					});
				}
			});

			$( "#name" ).catcomplete({
				delay: 0,
				minLenght: 3,
				source: '/config/search_name.php',
				select: function(ev, ui){
					$('#asesor1').val(ui.item.id);
					depSel = ui.item.depid;
					puestoSel = ui.item.puestoid;
					$('#tipo').attr('disabled',false);
					//console.log("id asesor seleccionado: "+asesorSelected);
				}
			});



			function populateAsesor(index){

				showLoader('Obteniendo Asesores');

				$.ajax({
					url: 'search_name.php',
					type: 'POST',
					data: {fecha: $('#fecha1').val(), dep: depSel, puesto: puestoSel},
					dataType: 'json',
					success: function(array){
							data=array;

							dialogLoad.dialog('close');

							$('#asesor'+index).empty();
							listElement = $('#asesor'+index);

							if(data['error']==1){

								showNoty('error', data['msg'],4000);

							}else{

								listElement.append('<option value="">Selecciona...</option>');

								$.each(data['asesor'], function(i,info){
									listElement.append('<option value="' + info.id + '">' + info.desc + '</option>');
								});
							}


						},
					error: function(){
						dialogLoad.dialog('close');
						showNoty('error', 'Error de conexión',4000);
					}

				});

			}

});

</script>

<style>
    .ui-tooltip {
    width: 60px;
    height: auto;      h
    padding: 10px 20px;
    color: black;
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-transform: uppercase;
    text-align: center;
    box-shadow: 0 0 7px black;
  }

  .timepicker{
    width: 60px;
  }

  .required{
  	border-color: red;
  	background: #ffeaed;
  }

  .idh{
  	width: 65px;
  	text-align: center;
  }

  .fecha{
  	width: 100px;
  	text-align: center;
  }

	.ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }

	.overflow { height: 200px; }

</style>

<table  class='t2' style='width: 100%; margin: auto;'>
	<tr>
		<td class='subtitle'>Fecha 1</td>
		<td class='subtitle'>Asesor 1</td>
		<td class='subtitle'>Tipo de Cambio</td>
		<td class='subtitle asesor_selection2'>Asesor 2</td>
		<td class='subtitle fecha_selection2'>Fecha 2</td>
		<td class='subtitle' rowspan=10><button class='button button_green_w' id='search'>Consultar</button></td>
	</tr>
	<tr>
		<td class='' style='text-align: center' id='fecha_selection1'><input type='text' id='fecha1'></td>
		<td  class='pair'><input type='text' id='name' placeholder='Nombre del asesor' size=50><input type='hidden' id='asesor1'></td>
        <td class='pair'>
        	<select id="tipo" req='1'>
        		<option value="">Selecciona...</option>
        		<option value='1'>Turno</option>
        		<option value='2'>Descanso</option>
        		<option value='3'>Ajuste</option>
        		<option value='4'>Ajuste Turnos</option>
        		<option value='5'>Ajuste Descanso</option>
        	</select>
        </td>
        <td class='pair asesor_selection2'><select id='asesor2'></select></td>
        <td class='fecha_selection2' style='text-align: center'><input type='text' id='fecha2'></td>
	</tr>

</table>

<div id='display_data'>

<br>

<table  class='t2' style='width: 80%; margin: auto;' id='show_horarios'>
	<tr class='title' >
		<th  colspan=100>Horario Actual</th>
	</tr>
	<tr class='subtitle'>
		<th>Id Horario</th>
        <th>Id Asesor</th>
		<th>Asesor</th>
		<th>Esquema</th>
		<th>Fecha</th>
		<th>Horario</th>
        <th>Comida</th>
        <th>Horas Extra 1</th>
        <th>Horas Extra 2</th>
        <th>Restringido</th>
		<th>Cambios en<br>el mes</th>
    </tr>


</table>
<table  class='t2' style='width: 80%; margin: auto; text-align: right' id='show_horarios'>
	<tr class='title' >
		<th  colspan=100 style='text-align: right'><button id='detalle' class='buttonlarge button_green_w'>Detalle Cambios</button></th>
	</tr>
</table>
<br>
<table  class='t2' style='width: 80%; margin: auto;' id='show_changes'>
	<tr class='title' >
		<th  colspan=100>Horario Nuevo</th>
	</tr>
	<tr class='subtitle'>
		<th>Id Horario</th>
        <th>Asesor</th>
		<th>Esquema</th>
		<th>Fecha</th>
		<th>Jornada Inicio</th>
        <th>Jornada Fin</th>
        <th>Comida Inicio</th>
        <th>Comida Fin</th>
        <th>Extra 1 Inicio</th>
        <th>Extra 1 Fin</th>
        <th>Extra 2 Inicio</th>
        <th>Extra 2 Fin</th>

    </tr>


</table>
<table  class='t2' style='width: 80%; margin: auto;' id='send_changes'>
	<tr class='subtitle'>
		<th><button class='button button_red_w' id='aplicar'>Aplicar</button></th>
	</tr>
</table>


</div>
<div id='test'></div>
<div id="dialog-detail" title="Detalle de Cambios">

</div>

<?php $connectdb->close(); ?>

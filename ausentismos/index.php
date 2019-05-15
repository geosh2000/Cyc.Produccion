<?php

include_once("../modules/modules.php");

initSettings::start(true,'schedules_change');
initSettings::printTitle('Asignación de Ausentismos');

//Get Variables
$casereq="req='1'";
$dep=$_POST['dep'];
$asesor=$_POST['asesor'];
$ausentismo=$_POST['tipo'];
$dias=$_POST['dias'];
$motivo=$_POST['motivo'];

$tbody="<td><input type='text' id='name' placeholder='Nombre del asesor' size=50><input type='hidden' id='asesor' name='asesor' size=50></td><td>Tipo</td><td><select class='filterSelect' id='tipo' nombre='tipo' req='1'><option value=''>Selecciona...</option>";
$query="SELECT * FROM `Tipos Ausentismos` ORDER BY Ausentismo";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		$tbody.= "<option value='".$fila['id']."' max='".$fila['max_days']."' moper='".$fila['needs_moper']."'>".utf8_encode($fila['Ausentismo'])."</option>";
	}
}

$tbody.="</select></td><td class='td_motivo'>Motivo</td><td class='td_motivo' id='motivo_select'><select class='filterSelect' id='motivo' nombre='motivo' req='1'><option value=''>Selecciona...</option>";
$tbody.="</select></td>";

Filters::showFilterNOFORM('search', 'Consultar', $tbody);
?>
<style>
	.error{
		background: #eab5b5;
	}
	.ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }
</style>
<script>
	$(function(){

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
	        $('#asesor').val(ui.item.id);
	        asesorSelected_text = ui.item.label;
	        $('#baja_name').text(asesorSelected_text);
	        console.log("id asesor seleccionado: "+ui.item.id);
	      }
	    });

		//DatePicker
		$('#datestart, #dateend').periodpicker({
			lang: 'en',
			animation: true,
			norange: true
		});

		$('#dateend').periodpicker('disable');

		//init Menu
		$('.sel_asesor, .td_motivo, #showForms').hide();
		$('#motivo').attr('req','0');

		//Asesor segun departamento
		$('#departamento').change(function(){$('#tipo, #motivo, #dias').val();
			$('.sel_asesor, .td_motivo').hide();
			$('#motivo').attr('req','0');
			$('#asesor, #motivo, #dias, #tipo').val('');
			$('.sel_asesor_dep_'+$(this).val()).show();
		});

		$('#asesor').change(function(){
			$('#tipo, #motivo, #dias').val('');
			$('.td_motivo').hide();
			$('#motivo').attr('req','0');
		});

		//Dias segun tipo
		$('#tipo').change(function(){
			var tipo = $(this).val();
			var selection = "";

			if(tipo!=5){
				var dias = $('option:selected', this).attr('max');
				$('#dias_select').empty();
				$('.td_motivo').hide();
				$('#motivo').attr('req','0');

				selection = "<select id='dias' nombre='dias' req='1'><option value=''>Selecciona...</option>";

				for(i=1;i<=dias;i++){
					selection = selection + "<option value='"+i+"'>"+i+"</option>";
				}

				selection = selection + "</select>";
			}else{

				if($('#asesor').val()==''){
					showNoty('error','Debes seleccionar un asesor primero',4000);
					$('#asesor').addClass('error');
					$('#tipo').val('');
				}else{

					showLoader('Obteniendo Dias Pendientes');

					$.ajax({
						url: '<?php if(defined(MODULE_PATH)){ echo MODULE_PATH;} ?>get_pendientes.php',
						type: 'POST',
						data: {asesor: $('#asesor').val()},
						dataType: 'json',
						success: function(array){
							data=array;

							if(data['total']>0){
								$('.td_motivo').show();
								$('#motivo').attr('req','1');
								var motivo;

								$('#motivo_select').empty();

								var select_motivo = "<select id='motivo' nombre='motivo' req='1'><option value=''>Selecciona...</option>";

								$.each(data['dias'], function(i,val){
									if(val>0){
										select_motivo = select_motivo + "<option value='"+i+"' dias='"+val+"'>"+i+"</option>";
									}
								});

								select_motivo = select_motivo + "</select>";
								$('#motivo_select').html(select_motivo);
								dialogLoad.dialog('close');
							}else{
								dialogLoad.dialog('close');
								showNoty('error', 'El asesor no tiene dias pendientes', 6000);
								$('#tipo').val('');
							}
						},
						error: function(){
							dialogLoad.dialog('close');
							showNoty('error','Error al obtener Información',4000);
						}
					});


				}
			}

			$('#dias_select').html(selection);
		});


		$(document).on('change', '#motivo', function() {
			var dias = $('option:selected', this).attr('dias');
			$('#dias_select').empty();

			selection = "<select id='dias' nombre='dias' req='1'><option value=''>Selecciona...</option>";

			for(i=1;i<=dias;i++){
				selection = selection + "<option value='"+i+"'>"+i+"</option>";
			}

			selection = selection + "</select>";
			$('#dias_select').html(selection);
		});

		$('#search').click(function(){
			var flag = true;
			selections = $('#showFilter').find('select');

			$.each(selections,function(){
				if($(this).attr('req')==1){
					if($(this).val()==''){
						flag=false;
						$(this).addClass('error');
					}else{
						$(this).removeClass('error');
					}
				}
			});

			if(!flag){
				showNoty('error','Todos los campos deben ser seleccionados',4000);
			}else{
				$('#asesor_name').text($('#name').val()).attr('asesor',$('#asesor').val());
				$('#asesor_tipo').text($('#tipo option:selected').text()).attr('tipo',$('#tipo').val());
				$('#asesor_motivo').text($('#motivo').val());

				if($('#tipo').val()==1){
					$('#beneficios').prop('disabled',false);
				}else{
					$('#beneficios').prop('disabled',true);
				}

				$.each($('#showForms').find('input'),function(){
					$(this).val('');
				});

				$.each($('#showForms').find('select'),function(){
					$(this).val('');
				});

				$('#datestart').periodpicker('change');
				$('#dateend').periodpicker('change');

				$('#isichk').prop('checked',false);
				$('#beneficios, #descansos').val(0);

				if($('#tipo option:selected').attr('moper')==1){
					$('#caso, #isichk').prop('disabled',false);
					$('#moper').prop('disabled',true);
				}else{
					$('#moper, #caso, #isichk').prop('disabled',true).val('').prop('checked',false);
				}

				$('input, select').removeClass('error');

				$('#resultSave').empty();

				printTable();

				mopResume.accordion('refresh');

				$('#showForms').show();
			}
		});

		$('.filterSelect').change(function(){
			$('#showForms').hide();
		});

		$('#isichk').change(function(){
			if($(this).prop('checked')){
				$('#moper').prop('disabled',false);

			}else{
				$('#moper').prop('disabled',true).val('');
			}
		});

		//Calculate dates
		function datechange(){
			var descansos = $('#descansos').val()*86400000;
	   	    var beneficios = $('#beneficios').val()*86400000;
	        var val = $('#datestart').val();
	        var days = ($('#dias').val()-1)*86400000;
	        var extra=days+descansos+beneficios;
	        var dateend = Date.parse(val);
	        var dateend = new Date(dateend + extra);
	        var month = dateend.getMonth()+1;
	        if(month<10){month= '0'+month;}
	        var day = dateend.getDate();
	        if(day<10){day= '0'+day;}
	        var year  = dateend.getFullYear();
	        $('#dateend').val(year + '-' + month + '-' + day);
	        $('#dateend').periodpicker('change');

	   }

	   $(document).on('change', '#datestart, #descansos, #beneficios, #dias', function(){
	        datechange();
	    });

	    $('#save').click(function(){
	    	var flag=true;
	    	var errores="";

	    	$.each($('#aus_details').find('input'), function(){
	    		if(!$(this).prop('disabled') && $(this).attr('req')==1){
	    			if($(this).val()==''){
	    				$(this).addClass('error');
	    				flag=false;
	    				errores = errores + " | "+$(this).attr('id');
	    			}else{
	    				$(this).removeClass('error');
	    			}
	    		}
	    	});

	    	if($('#dias').val()==''){
	    		$('#dias').addClass('error');
				flag=false;
	    	}else{
				$('#dias').removeClass('error');
			}

	    	if(!flag){
	    		showNoty('error','Los campos '+errores+' deben completarse',4000);
	    	}else{
	    		saveAusentismo();
	    	}
	    });

	    //Save Ausentismo
	    function saveAusentismo(){
	    	showLoader('Aplicando Ausentismo');
	    	if($('#isichk').prop('checked')){
	    		var isMoper = 1;
	    	}else{
	    		var isMoper = 0;
	    	}


	    	$.ajax({
	    		url: 'saveAusentismo.php',
	    		type: 'POST',
	    		data: {asesor: $('#asesor_name').attr('asesor'), dias: $('#dias').val(), tipo: $('#asesor_tipo').attr('tipo'), inicio: $('#datestart').val(), fin: $('#dateend').val(), descansos: $('#descansos').val(), beneficios: $('#beneficios').val(), caso: $('#caso').val(),  comments: $('#comments').val(), isMoper: isMoper, moper: $('#moper').val(), motivo: $('#motivo').val() ,user: <?php echo $_SESSION['id']; ?>},
	    		dataType: 'json',
	    		success: function(array){
	    			dialogLoad.dialog('close');

	    			data=array;
	    			result="";
	    			var flag=true;

	    			if(data['ausentismo']['status']==1){

	    				result="<p>Ausentismo <b>Aplicado</b> con id: "+ data['ausentismo']['id'] +"</p>";

	    				switch($('#asesor_tipo').attr('tipo')){
	    					case '5':
	    						if(data['dpr']['status']==1){
	    							result = result + "<p>Dias Redimidos en DB con id "+data['dpr']['id'] + "</p>";
	    						}else{
	    							flag=false;
	    							result = result + "<p style='color: red'>Error al guardar en DB de dias Redimidos</p>";
	    						}
	    						break;
	    					case '12':
	    						if(data['pya']['status']==1){
	    							result = result + "<p>Excepciones eliminadas de PyA</p>";
	    						}else{
	    							flag=false;
	    							result = result + "<p style='color: red'>Error al borrar excepciones en DB de PyA</p>";
	    						}
	    						break;
	    				}

	    				if(flag){
	    					showNoty('success', 'Ausentismo Guardado Correctamente',4000);
	    					$('#showForms').hide();
	    				}else{
	    					showNoty('error', 'Error al guardar Ausentismo (error en Pya o Dias Redimidos)', 4000);
	    				}

	    				$('#tipo').val('');

	    			}else if(data['ausentismo']['status']==10){
	    				showNoty('error', 'Error al guardar Ausentismo // Ya existen ausentismos asignados en las fechas seleccionadas', 4000);
	    			}else{
	    				showNoty('error', 'Error al guardar Ausentismo', 4000);
	    				result = "<p>"+data['ausentismo']['error']+"</p>";
	    			}

	    			$('#resultSave').html(result);
	    		},
	    		error: function(){
	    			dialogLoad.dialog('close');
	    			showNoty('error', 'Error al aplicar ausentismo (Problema con conexión)', 4000);
	    		}
	    	});
	    }

	    //MOPERS
	    mopResume = $( "#accordion" ).accordion({
	      collapsible: true,
	      active: false,
	      heightStyle: 'content'
	    });

	    $tableresult = $('#result-table-Cuartiles table');

		function printTable(){
			showLoader('Obteniendo Mopers Existentes');

			$('#result-table-Cuartiles').empty();
			$('#result-AHT').empty();

			$.ajax({
				url: 'getAusentismos.php',
				type: 'POST',
				data: {asesor: $('#asesor_name').attr('asesor')},
				dataType: 'json',
				success: function(array){
							var dataTable=array;

							drawTable(dataTable);

							dialogLoad.dialog('close');
						  	$('#exportar').show();
					},

					error: function(){
						dialogLoad.dialog('close');
						showNoty('error','Error al recibir información de Mopers',4000);
					}
			});


		}


		function drawTable(data){
			$('#result-table-Cuartiles').tablesorter({
			    theme: 'jui',
			    headerTemplate: '{content} {icon}',
				widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders'],
			    tableClass: 'center',
			    data: data,
			    widgetOptions: {
			    	//Filters
	      			filter_childRows: false,
	                filter_columnFilters: true,
	                filter_cssFilter: "tablesorter-filter",
	                filter_functions: null,
	                filter_hideFilters: false,
	                filter_ignoreCase: true,
	                filter_reset: null,
	                filter_searchDelay: 300,
	                filter_startsWith: false,
	                filter_useParsedData: false,

	                //Outputs
	                output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
	                output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)
	                output_hiddenColumns : false,       // include hidden columns in the output
	                output_includeFooter : true,        // include footer rows in the output
	                output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
	                output_headerRows    : true,        // output all header rows (multiple rows)
	                output_delivery      : 'd',         // (p)opup, (d)ownload
	                output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
	                output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
	                output_replaceQuote  : '\u201c;',   // change quote to left double quote
	                output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
	                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
	                output_wrapQuotes    : false,       // wrap every cell output in quotes
	                output_popupStyle    : 'width=580,height=310',
	                output_saveFileName  : 'cuartiles.csv',
	                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
	                output_encoding      : 'data:application/octet-stream;charset=utf8,',

	                //Sticky
	                stickyHeaders_attachTo : '#container-cuartiles'
			    }
		  	});
		}

		$(document).on('click','.editMoper',function(){
			$('#a_id').val($(this).attr('ausid'));
			moper=$(this).closest('tr').find('td');
	    	$('#moperold').val(moper[5].textContent);
	    	moperEdit.dialog('open');
		});

		$(document).on('click','.removeAus',function(){
			$('#del_id').val($(this).attr('ausid'));
			aus_tr = $(this).closest('tr');
			deleteAusentismo.dialog('open');
		});

		moperForm=$( "#dialog-form" ).find('form');

		moperEdit = $( "#dialog-form" ).dialog({
	      autoOpen: false,
	      height: 300,
	      width: 530,
	      modal: true,
	      buttons: {
	        "Enviar": function(){
	        		SaveMoper();
	        	},
	        "Eliminar": function(){
	        		deleteMoper.dialog('open');
	        	},
	        Cancel: function() {
	          moperEdit.dialog( "close" );
	        }
	      },
	      close: function() {
	        moperForm[0].reset();
	        $( "#dialog-form input" ).removeClass( "error" );
	      }
	    });

	    deleteMoper=$( "#dialog-confirm" ).dialog({
	      resizable: false,
	      height:190,
	      width:400,
	      autoOpen: false,
	      modal: true,
	      position: {
	      	my: 'center',
	      	at: 'center',
	      	of: '#dialog-form'
	      },
	      buttons: {
	        "Eliminar Moper": function() {
	          DeleteMoper();
	          $( this ).dialog( "close" );
	        },
	        Cancel: function() {
	          $( this ).dialog( "close" );
	        }
	      }
	    });

	    deleteAusentismo=$( "#dialog-confirm2" ).dialog({
	      resizable: false,
	      height:190,
	      width:400,
	      autoOpen: false,
	      modal: true,
	      position: {
	      	my: 'top',
	      	at: 'top',
	      	of: '#accordion'
	      },
	      buttons: {
	        "Eliminar Ausentismo": function() {
	          DeleteAus();
	        },
	        Cancel: function() {
	          $( this ).dialog( "close" );
	        }
	      }
	    });

	    function DeleteAus() {

	    	showLoader('Eliminando Ausentismo');

	    	$.ajax({
	    		url: 'deleteAus.php',
	    		type: 'POST',
	    		data: {id: $('#del_id').val()},
	    		dataType: 'json',
	    		success: function(array){
	    			dialogLoad.dialog('close');
	    			data=array;
	    			showNoty(data['type'],data['msg'],4000);

	    			if(data['status']==1){
	    				deleteAusentismo.dialog('close');
	    				$(aus_tr).remove();
	    			}
	    		},
	    		error: function(){
	    			dialogLoad.dialog('close');
	    			showNoty('error', 'Error de conexión al borrar ausentismo',4000);
	    		}
	    	});
	    }

	    function DeleteMoper() {

	    	showLoader('Eliminando Moper');

	    	$.ajax({
	    		url: 'deleteMoper.php',
	    		type: 'POST',
	    		data: {id: $('#a_id').val()},
	    		dataType: 'json',
	    		success: function(array){
	    			dialogLoad.dialog('close');
	    			data=array;
	    			showNoty(data['type'],data['msg'],4000);

	    			if(data['status']==1){
	    				moperEdit.dialog('close');
	    				$(moper.get(5)).text('NULL');
	    			}
	    		},
	    		error: function(){
	    			dialogLoad.dialog('close');
	    			showNoty('error', 'Error de conexión al borrar moper',4000);
	    		}
	    	});
	    }

	    function SaveMoper() {
	    	$('#mopernew').removeClass('error');
	    	showLoader('Cargando Moper');

	    	var this_moper=$('#mopernew').val();

	    	if($('#mopernew').val()!=""){

		    	$.ajax({
		    		url: 'saveMoper.php',
		    		type: 'POST',
		    		data: {id: $('#a_id').val(), moper: this_moper},
		    		dataType: 'json',
		    		success: function(array){
		    			dialogLoad.dialog('close');
		    			data=array;
		    			showNoty(data['type'],data['msg'],4000);

		    			if(data['status']==1){
		    				moperEdit.dialog('close');
		    				$(moper.get(5)).text(this_moper);
		    			}
		    		},
		    		error: function(){
		    			dialogLoad.dialog('close');
		    			showNoty('error', 'Error de conexión al guardar moper',4000);
		    		}
		    	});

		    }else{
		    	showNoty('error', 'Debes escribir un nuevo número de moper',4000);
		    	$('#mopernew').addClass('error');
		    }
	    }




	});
</script>
<br>
<div id='showForms'>
	<table style='width: 1000px; margin: auto;' class='t2'>
		<tr class='title'>
			<th colspan=100>Datos de la selección</th>
		</tr>
		<tr class='title'>
			<td width='10%'>Asesor</td>
			<td width='10%' id='asesor_name' class='pair' asesor=''></td>
			<td width='10%'>Tipo</td>
			<td width='10%' id='asesor_tipo' class='pair' tipo=''></td>
			<td width='10%'>Motivo</td>
			<td width='10%' id='asesor_motivo' class='pair'></td>
			<td width='10%'>Dias</td>
			<td width='10%' class='pair' id='dias_select'><select id='dias' nombre='dias' req='1'><option value=''>Selecciona...</option></select></td>
		</tr>
	</table>
	<br>
	<table style='width: 1000px; margin: auto;' class='t2' id='aus_details'>
		<tr class='title'>
			<th colspan=100>Detalles del ausentismo</th>
		</tr>
		<tr class='title'>
			<td>Descansos</td>
			<td  class='pair'><input req='1' id='descansos' type='number' value=0></td>
			<td>Beneficios</td>
			<td  class='pair'><input req='1' id='beneficios' type='number' value=0></td>
		</tr>
		<tr class='title'>
			<td width='25%'>Fecha Inicial:</td>
			<td  class='odd' width='25%'><input req='1' type='text' name='inicio' id="datestart" req='1'></td>
			<td width='25%'>Fecha Final:</td>
			<td class='odd' width='25%'><input type='text' name='fin' id="dateend" readonly req='1'></td>
		</tr>
		<tr class='title'>
			<td width='25%'>Caso:</td>
			<td  class='pair' width='25%'><input req='1' type='text' name='caso' id="caso" size=8></td>
			<td width='25%'>Observaciones:</td>
			<td class='pair' width='25%'><input type='text' name='comment' id="comments"></td>
		</tr>
		<tr class='title'>
			<td width='25%'>ISI RRHH:</td>
			<td  class='odd' width='25%'>Hecho: <input type='checkbox' name='isichk' id='isichk'></td>
			<td width='25%'>Moper:</td>
			<td class='odd' width='25%'><input req='1' type='text' name='moper' id='moper'</td>
		</tr>
		<tr class='title'>
			<td colspan=100><button class='button button_green_w' id='save'>Guardar</button></td>
		</tr>
	</table>
	<br>
	<div id="accordion" style='width: 80%; margin: auto'>
	  <h3>Ausentismos Registrados</h3>
		<div id='result-table-Cuartiles'></div>
	</div>
</div>
<div id='resultSave' style='text-align: center; font-size: 18px; display: block; line-height: normal;'>

</div>
<div id="dialog-form" title="Cambiar Moper">
 <p class="validateTips">Fill the required Fields.</p>

  <form>
    <fieldset>
        <table width='480px'>
            <tr>
                <td width='30%'><label for="a_id">ID</label></td>
                <td><input type="text" name="a_id" id="a_id" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
            <tr>
                <td width='30%'><label for="date">Moper Anterior</label></td>
                <td><input type="text" name="moperold" id="moperold" value="" class="text ui-widget-content ui-corner-all" readonly></td>

            </tr>
            <tr>
                <td width='30%'><label for="date">Moper Nuevo</label></td>
                <td><input type="text" name="mopernew" id="mopernew" value="" class="text ui-widget-content ui-corner-all">
                <input type="text" name="target" id="target" value="" class="text ui-widget-content ui-corner-all" hidden></td>

            </tr>
            </table>
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
<div id="dialog-confirm" title="Eliminar Moper">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El moper se eliminará del registro<br>¿Estás seguro?</p>
</div>
<div id="dialog-confirm2" title="Eliminar Ausentismo">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El AUSENTISMO id:<dh id='del_id'></dh> se eliminará del registro<br>¿Estás seguro?</p>
</div>

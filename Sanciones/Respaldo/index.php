<?php

include_once('../modules/modules.php');

initSettings::start(true,'sanciones');
initSettings::printTitle('Sanciones');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

if(isset($_POST['start'])){
	$inicio=date('Y-m-d', strtotime($_POST['start']));
}else{
	$inicio=date('Y-m-d', strtotime('-7 days'));
}

if(isset($_POST['end'])){
	$fin=date('Y-m-d', strtotime($_POST['end']));
}else{
	$fin=date('Y-m-d', strtotime('-1 days'));
}

if($_POST['search_asesor_h']==''){
	$searchAsesor='';
}else{
	$searchAsesor="asesor='".$_POST['search_asesor_h']."' ";
}

$search_flag=$_POST['searchby'];

if($search_flag=='asesor'){
	$sel_as='checked';
	$hidden='.s_bydate';
}elseif($search_flag=='date'){
	$sel_date='checked';
	$hidden='.s_byasesor';
}else{
	$hidden='.s_byasesor, .s_bydate';
}
//$skill=$_POST['skill'];

$tbody="<td><fieldset><label class='labcheck' for='bydate'>Por Fecha</label>
	    <input type='radio' name='searchby' id='bydate' value='date' $sel_date required>
	    <label class='labcheck' for='byasesor'>Por Asesor</label>
	    <input type='radio' name='searchby' id='byasesor' value='asesor' $sel_as required></fieldset></td><td  class='s_bydate'>Periodo</td><td class='s_bydate'><input type='text' name='start' id='inicio' value='$inicio' required><input type='text' name='end' id='fin' value='$fin' required></td>"
		."<td class='s_byasesor'>Programa</td><td class='s_byasesor'><input type='hidden' name='submit' value=1><input type='hidden' name='search_asesor_h' id='search_asesor_h' value='".$_POST['search_asesor_h']."'><input type='text' name='search_asesor' id='search_asesor' value='".$_POST['search_asesor']."' placeholder='Asesor (opcional)' size=25></td><td><button class='button button_blue_w' id='clearSearch'>Limpiar</button></td>";
Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'submit', 'Consultar', $tbody);

//QUERY
if(isset($_POST['submit'])){

	//Sanciones
	switch($_POST['searchby']){
		case 'asesor':
			$query="SELECT
							a.id, b.Nombre, Departamento, CASE WHEN tipo=1 THEN 'Acta Administrativa' WHEN tipo=2 THEN 'Accion Disciplinaria' END as Documento, motivo, fecha_incidencia, fecha_aplicacion, fecha_afectacion_inicio, fecha_afectacion_fin, no_suspensiones_aplicables, documento_entregado, fecha_registro, "
						."observaciones, last_update, d.`N Corto` as ultima_actualizacion, c.`N Corto` as Registrado_por FROM "
						."Sanciones a "
						."LEFT JOIN "
						."Asesores b ON a.asesor=b.id "
						."LEFT JOIN "
						."Asesores c ON a.registered_by=c.id "
						."LEFT JOIN "
						."Asesores d ON a.last_user_update=d.id "
						."LEFT JOIN "
						."PCRCs e ON getDepartamento(asesor,a.fecha_incidencia)=e.id "
						."WHERE "
						."$searchAsesor";
			break;
		case 'date':
			$query="SELECT
							a.id, b.Nombre, Departamento, CASE WHEN tipo=1 THEN 'Acta Administrativa' WHEN tipo=2 THEN 'Accion Disciplinaria' END as Documento, motivo, fecha_incidencia, fecha_aplicacion, fecha_afectacion_inicio, fecha_afectacion_fin, no_suspensiones_aplicables, documento_entregado, fecha_registro, "
						."observaciones, last_update, d.`N Corto` as ultima_actualizacion, c.`N Corto` as Registrado_por FROM "
						."Sanciones a "
						."LEFT JOIN "
						."Asesores b ON a.asesor=b.id "
						."LEFT JOIN "
						."Asesores c ON a.registered_by=c.id "
						."LEFT JOIN "
						."Asesores d ON a.last_user_update=d.id "
						."LEFT JOIN "
						."PCRCs e ON getDepartamento(asesor,a.fecha_incidencia)=e.id "
						."WHERE "
						."CAST(a.fecha_incidencia as DATE) BETWEEN '$inicio' AND '$fin'";
			break;
		default:
			exit;
			break;
	}


	if ($result=$connectdb->query($query)) {
		$info_field=$result->fetch_fields();
	   while ($fila = $result->fetch_row()) {
			for($i=1;$i<$result->field_count;$i++){
				$data[$fila[0]][$info_field[$i]->name]=utf8_encode($fila[$i]);
			}
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);
}
?>
<style>
  .ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }
</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.timepicker.min.css">
<script src="/js/periodpicker/build/jquery.timepicker.min.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script>
$(function(){

	$( "#bydate, #byasesor" ).checkboxradio();

	$('<?php echo $hidden; ?>').hide();

	$("#bydate, #byasesor").change(function(){
		var id=$(this).attr('id');
		if(id=='bydate'){
			$('.s_bydate').show();
			$('.s_byasesor').hide();
			searchFlag='date';
		}else{
			$('.s_bydate').hide();
			$('.s_byasesor').show();
			searchFlag='asesor';
		}
	});

	//PeriodPicker
	$('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});

	$('#f_fecha_aplicacion, #f_fecha_incidencia').periodpicker({
		norange: true, // use only one value
		cells: [1, 1], // show only one month
		maxDate: '<?php echo date('Y-m-d',strtotime('+1 days')); ?>'
	});

	$('#f_fecha_afectacion_inicio').periodpicker({
		end: '#f_fecha_afectacion_fin',
		minDate: '<?php echo date('Y-m-d'); ?>'
	});

	//Dialog
	newForm=$('#newAct').dialog({
		autoOpen: false,
      	width: 590,
		height: 650,
		dialogClass: 'dialog_fixed,ui-widget-header',
        modal: true,
		buttons: {
        	Cancel: function(){
	        	startFields();
	        	$( this ).dialog( "close" );
	        },
	        Ok: function() {
        		submitForm();
        		if(val_flag){
	          		$( this ).dialog( "close" );
	          }
	        }
      	}
	});

	folio=$( "#folio" ).dialog({
      autoOpen: false,
      modal: true,
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });



	//asesor search
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


		asesorSelected_search='';

		$( "#search_asesor" ).catcomplete({
			delay: 0,
			minLenght: 3,
			source: '../config/search_name.php',
			select: function(ev, ui){
				asesorSelected_search=ui.item.id;
				$('#search_asesor_h').val(asesorSelected_search);
				asesorSelected_text_search = ui.item.label;
				console.log("id asesor seleccionado: "+asesorSelected_search);
			}
		});

		$( "#f_selasesor" ).catcomplete({
      delay: 0,
      minLenght: 3,
      source: '../config/search_name.php',
      select: function(ev, ui){
        $('#f_asesor').val(ui.item.id);
        asesorSelected_text = ui.item.label;
        $('#baja_name').text(asesorSelected_text);
        //console.log("id asesor seleccionado: "+asesorSelected);
      }
    });

	$('#clearSearch').click(function(){
		$('#inicio').periodpicker('clear');
		$('#search_asesor, #search_asesor_h').val('');
		asesorSelected_search='';
		asesorSelected_text_search='';
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
            minWidth: 620,
            minHeight: 400,
            draggable:true,
            /*close: function () { $(this).remove(); },*/
            buttons: { "Ok": function () {         $(this).dialog("close"); } }
          });
          $dialog.dialog('open');
        }
    }

    function updateTable(id,field,newVal){
    	$.ajax({
            url: "update_table.php",
            type: 'POST',
            data: {id: id, field: field, newVal: newVal, user: <?php echo $_SESSION['asesor_id']; ?>},
            dataType: 'html', // will automatically convert array to JavaScript
            success: function(result) {
            	text= result;
                var status = text.match("status- (.*) -status");
                var startlogin='no';
                var notif_msg = text.match("msg- (.*) -msg");
                if(status[1]=='OK'){
                    tipo_noti='success';
                    $('#d'+id).hide('slow', function(){ $('#d'+id).remove(); });
                    status=true;
                }else{
                    tipo_noti='error';
                    status=false;
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
                    }
                });

               }
		});
    }

    function sendRequest(variables){



    var urlok='/json/formularios/query_func.php';
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
                var folio_id = text.match("id- (.*) -id");
                if(status[1]=='OK'){
                    tipo_noti='success';
                    $('#error').text("");
                    $('.input').val('');
                    $( '#regs' ).attr( 'src', function ( i, val ) { return val; });
                    $('#fol_assign').html('<b>'+ folio_id[1] +'</b>');
                    folio.dialog('open');
                    startFields();
                }else{
                	val_flag=false;
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

    function startFields(){
    	$('.form_input, .nosend').removeClass('error').val("").prop('checked',false);
    	$('#f_fecha_afectacion_inicio, #f_fecha_aplicacion, #f_fecha_incidencia').periodpicker('clear');
    }

    startFields();

	function submitForm(){
    	val_flag=true;
        $('.form_input, .nosend').each(function(){
        	if($(this).attr('req')==1){
        		if($(this).val()==""){
        			val_flag=false;
        			$(this).addClass('error');
        			//alert(this.id)
        		}else{
        			$(this).removeClass('error');
        		}
        	}else{
    			$(this).removeClass('error');
    		}
        });
        if(val_flag){
            var variables="db=Sanciones&f_registered_by=<?php echo $_SESSION['asesor_id']; ?>";
            $('.form_input').each(function(){
            	var temp=$(this).val();
		        if($(this).attr('type')=='checkbox'){
		        	if($(this).prop('checked')){
		        		temp=1;
		        	}else{
		        		temp=0;
		        	}
		        }
		        //TextArea Convert
            	if(this.id=='f_motivo'){
            		var temp=temp.replace(/\s/g, " ");
            	}

            	variables=variables+'&' + this.id +'=' + temp;
            });
		sendRequest(variables);

        }else{
        	$('#submit_form').prop('disabled',false);
        }
    };

    $('#new_form').click(function(){
    	newForm.dialog('open');
    });

    $('#act_table').tablesorter({
            theme: 'jui',
		    headerTemplate: '{content} {icon}',sortList: [[0,0]],
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output', 'editable','uitheme' ],
            widgetOptions: {

               columns_tfoot: false,
                columns_thead: true,
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
                resizable: true,
                saveSort: true,
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
                output_saveFileName  : 'Sanciones_<?php echo date('Ymd',strtotime($inicio))."_".date('Ymd',strtotime($fin));?>.xls',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                editable_columns       : [9,11],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
	            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
	            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
	            editable_autoResort    : true,         // auto resort after the content has changed.
	            editable_validate      : function(txt, orig, columnIndex, $element){
		                                        if(columnIndex==11){
		                                        	validation=true;
		                                        	return txt;
		                                        }else{
			                                        if(/(?:^|\s)([0-1])(?=\s|$)/.test(txt)){
			                                            validation=true;
		                                        		return txt;
			                                        }
			                                        validation=false;
		                                            new noty({
		                                                text: "Cambio no realizado, "+txt+" no corresponde al campo",
		                                                type: "error",
		                                                timeout: 10000,
		                                                animation: {
		                                                    open: {height: 'toggle'}, // jQuery animate function property object
		                                                    close: {height: 'toggle'}, // jQuery animate function property object
		                                                    easing: 'swing', // easing
		                                                    speed: 500 // opening & closing animation speed
		                                                }
		                                            });
		                                            return orig;

		                                      }
	                                     },          // return a valid string: function(text, original, columnIndex){ return text; }
	            editable_focused       : function(txt, columnIndex, $element) {
	              // $element is the div, not the td
	              // to get the td, use $element.closest('td')
	              $element.addClass('focused');
	            },
	            editable_blur          : function(txt, columnIndex, $element) {
	              // $element is the div, not the td
	              // to get the td, use $element.closest('td')
	              $element.removeClass('focused');
	            },
	            editable_selectAll     : function(txt, columnIndex, $element){
	              // note $element is the div inside of the table cell, so use $element.closest('td') to get the cell
	              // only select everthing within the element when the content starts with the letter "B"
	              return /^b/i.test(txt) && columnIndex === 0;
	            },
	            editable_wrapContent   : '<div>',       // wrap all editable cell content... makes this widget work in IE, and with autocomplete
	            editable_trimContent   : true,          // trim content ( removes outer tabs & carriage returns )
	            editable_noEdit        : 'no-edit',     // class name of cell that is not editable
	            editable_editComplete  : 'editComplete' // event fired after the table content has been edited

	        }
	    }).children('tbody').on('editComplete', 'td', function(event, config){
	      var $this = $(this),
	        newContent = $this.text(),
	        cellIndex = this.cellIndex, // there shouldn't be any colspans in the tbody
	        rowIndex = $this.closest('tr').attr('id'),// data-row-index stored in row id
	        col = $(this).attr('col');
	        if(validation==true){
	            updateTable(rowIndex,col,newContent);
	        }

	      // Do whatever you want here to indicate
	      // that the content was updated
	      $this.addClass( 'editable_updated' ); // green background + white text
	      setTimeout(function(){
	        $this.removeClass( 'editable_updated' );
	      }, 500);


            });

        $("#exporter").click(function(){
        	$('#act_table').trigger('outputTable');
        });


});
</script>

<style>

	.labcheck{
		width: 125;
		zoom: 0.6;
	}

	fieldset{
		height: 0;
		border: solid 0;
		margin-top: -27px;
	}

	.tablesorter tbody > tr > td[contenteditable=true]:focus {
	  outline: #08f 1px solid;
	  background: #eee;
	  resize: none;
	}
	td.no-edit, span.no-edit {
	  background-color: rgba(230,191,153,0.5);
	}
	.focused {
	  color: blue;
	}
	td.editable_updated {
	  background-color: green;
	  color: red;
	}

	.form_input{
		width: 185px;
	}

	.error{
	    background: #FFE8E0;
	    color: black;
	}

	#newAct{
		width: 530px;
		text-align: center;
	}

	.form_row{
		height: 50px;
		margin-bottom: 15px;
		text-align: center;
		vertical-align: middle;
	}

	.form_submit{
		text-align: right;
	}

	.field_title{
		display: inline-block;
		width: 250px;
		font-size: 17px;
		text-align: right;
		height: 100%;
		padding-top: 18px;
	}

	.field_input{
		display: inline-block;
		width: 272px;
		height: 100%;
		padding-top: 18px;
		text-align: left;
		padding-left: 15px;
	}
</style>
<div style="text-align:right;">
	<p><a href="Folio.docx" download>Descargar template Acta Administrativa</a></p>
	<p><a href="AccionDisciplinaria.pdf" download>Descargar template para Acción Disciplinaria</a></p>
</div>
<button class='button button_blue_w' id='new_form'>Agregar</button>
<button class='button button_red_w' id='exporter'>Exportar</button>
<br>
<div style='margin:auto; width: 90%'>
	<table id='act_table' style='width:90%; margin: auto; text-align: center'>
		<thead>
			<tr>
				<th>id</th>
				<?php
					foreach($data as $id => $info){
						foreach($info as $field => $info2){
							echo "<th>".ucwords(str_replace("_", " ", $field))."</th>\n\t";
						}
						unset($field,$info2);
						break;
					}
					unset($id,$info);
				?>
			</tr>
		</thead>
		<tbody>
			<?php
					foreach($data as $id => $info){
						echo "<tr id='$id'>\n\t";
						echo "<td>$id</td>\n\t";
						foreach($info as $field => $info2){
							echo "<td col='$field'>$info2</td>\n\t";
						}
						unset($field,$info2);
						echo "</tr>\n";
					}
					unset($id,$info);
				?>
		</tbody>
	</table>
</div>

<div id='newAct' title='Cargar Sanción'>
	<div class='form_row'>
		<div class='field_title'>Tipo</div>
		<div class='field_input'>
			<select id='f_tipo' class='form_input' req='1'>
				<option value=''>Selecciona...</option>
				<option value='1'>Acta Administrativa</option>
				<option value='2'>Acción Disciplinaria</option>
				<option value='3'>Plan Performance</option>
			</select>
		</div>
	</div>
	<div class='form_row'>
		<div class='field_title'>Asesor</div>
		<div class='field_input'>
			<input type='text' id='f_selasesor' placeholder='Nombre del asesor' size=20>
			<input type='hidden' id='f_asesor' class='form_input'>
		</div>
	</div>

	<div class='form_row'>
		<div class='field_title'>Fecha de la Incidencia</div>
		<div class='field_input'>
			<input type="text" id="f_fecha_incidencia" class='form_input' value='<?php echo $incidencia ?>' required>
		</div>
	</div>
	<div class='form_row'>
		<div class='field_title'>Fecha de Aplicación</div>
		<div class='field_input'>
			<input type="text" id="f_fecha_aplicacion" class='form_input' value='<?php echo $aplicacion ?>' required>
		</div>
	</div>
	<div class='form_row'>
		<div class='field_title'>Fechas de Afectación</div>
		<div class='field_input'>
			<input type="text" id="f_fecha_afectacion_inicio" class='form_input' value='<?php echo $afectacion_start ?>' required>
			<input type="text" id="f_fecha_afectacion_fin" class='form_input' value='<?php echo $afectacion_end ?>' required>
		</div>
	</div>
	<div class='form_row'>
		<div class='field_title'>Suspensiones Aplicables</div>
		<div class='field_input'>
			<input type="number" maxlength="1" max='5' min='0' size='4' step='1' id="f_no_suspensiones_aplicables" class='form_input'>
		</div>
	</div>
	<div class='form_row'>
		<div class='field_title'>Documento Entregado</div>
		<div class='field_input'>
			<input type="checkbox" id="f_documento_entregado" class='form_input'>
		</div>
	</div>
	<div class='form_row' style='height: 200px;'>
		<div class='field_title'>Descripción</div>
		<div class='field_input'>
			<textarea id='f_motivo' class='form_input' req='1' rows='10' ></textarea>
		</div>
	</div>
	<div class='form_row' style='height: 200px;'>
		<div class='field_title'>Comentarios</div>
		<div class='field_input'>
			<textarea id='f_observaciones' class='form_input' req='' rows='10' ></textarea>
		</div>
	</div>
</div>
<div id="folio" title="Registro Creado">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    El registro de la sanción ha sido exitoso.
  </p>
  <p>
    El número de folio asignado es: <fol id='fol_assign'></fol>.
  </p>
</div>
<div id='login'></div>
<?php $connectdb->close(); ?>

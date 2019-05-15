<?php

include_once("../modules/modules.php");

initSettings::start(true,'schedules_diaspendientes');
initSettings::printTitle('Dias Pendientes');

timeAndRegion::setRegion('Cun');

$tbody="<td><input type='text' id='name' placeholder='Nombre del asesor' size=50><input type='hidden' id='asesor'></td>";

Filters::showFilterNOFORM('consultar','Consultar',$tbody);
?>

<style>

	td, th{
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
				$('#titleAsesor').text(ui.item.label);
				depSel = ui.item.depid;
				puestoSel = ui.item.puestoid;
				$('#tipo').attr('disabled',false);
				//console.log("id asesor seleccionado: "+asesorSelected);
			}
		});

		$( "#motivo_add" ).catcomplete({
			delay: 0,
			minLenght: 3,
			source: 'search_motivo.php',

		});

		$('#display').hide();

		function printTable(){
			$('#display').show();
	    $('#result-table').empty();
	    showLoader('Obteniendo Información');

	  $.ajax({
	      url: 'get_info.php',
	      type: 'POST',
	      data: {asesor: $('#asesor').val()},
	      dataType: 'json',
	      success: function(array){
	          data=array;

	          dialogLoad.dialog('close');

	          $('#result-table').tablesorter({
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
	                      output_ignoreColumns : [0],          // columns to ignore [0, 1,... ] (zero-based index)
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
	                      output_saveFileName  : 'dias_pendientes'+$('#name').val()+'.csv',
	                      // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
	                      output_encoding      : 'data:application/octet-stream;charset=utf8,',

	                      //stickyHeaders
	                      stickyHeaders_attachTo: '#result-table',


	              }
	            });
	      }
	    });


	  }

		function searchAsesor(){
			printTable();
			$('#date_add').periodpicker('clear');
			$('#motivo_add, #dias_add').val('');
		}

		$('#consultar').click(function(){
			searchAsesor();
		});

		$('#date_add').periodpicker({
			norange: true,
			formatDate: 'YYYY-MM-DD'
		});

		function assignDays(){
			showLoader('Guardando Dias');

			$.ajax({
				url: 'assign.php',
				type: 'POST',
				data: {asesor: $('#asesor').val(), date: $('#date_add').val(), motivo: $('#motivo_add').val(), dias: $('#dias_add').val()},
				dataType: 'json',
				success: function(array){

					data=array;

					dialogLoad.dialog('close');

					if(data['status']==1){
						showNoty('success', 'Dias guardados correctamente',4000);
						$('#name').val($('#titleAsesor').val());
						searchAsesor();
					}else{
						showNoty('error', data['msg'],4000);
					}
				},
				error: function(){
					dialogLoad.dialog('close');
					showNoty('error', 'Error de conexión',4000);
				}
			});
		}

		$('#add').click(function(){
			assignDays();
		})
});


</script>
<br>
<div id='display'>
	<div id='result-table' style='margin: auto; width: 80%'></div>
	<br>
	<div id='assign' style='margin: auto; width: 80%'>
		<table class='t2' style="text-align: center; width: 100%";>
			<tr class='title'>
				<th colspan=100>Asignar Días a <span id='titleAsesor'></span></th>
			</tr>
			<tr class='title'>
				<th>Fecha</th>
				<th>Motivo</th>
				<th>Días</th>
				<th>Asignar</th>
			</tr>
			<tr>
				<td><input type='text' id='date_add' required></td>
				<td><input type='text' id='motivo_add' placeholder='Motivo' size=50 required></td>
				<td><input type='number' step=1 id='dias_add' required></td>
				<td><button class='button button_green_w' id='add'>Agregar</button></td>
			</tr>
		</table>
	</div>

</div>

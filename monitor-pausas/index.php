<?php
include_once("../modules/modules.php");

initSettings::start(true,'monitor_gtr');
initSettings::printTitle('Monitor de Pausas Excedidas');

timeAndRegion::setRegion('Cun');

$tbody="<td><input type='text' id='from' name='from' value='$from' required/><input type='text' id='to' name='to' value='$to' required/></td>";

Filters::showFilterNOFORM('load','Cargar',$tbody);
?>

<style> .center{
	text-align: center
}
</style>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-build-table.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script>
$(function(){
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		animation: true
	});

	flag=true;


	function printTable(){

		$('#result-table').tablesorter({
		    theme: 'jui',
		    headerTemplate: '{content} {icon}',
			widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders', 'editable' ],
		    tableClass: 'center',
		    widgetOptions: {
		    	//Builders
		    	build_type   : 'json',
      			build_source : {
      				url: 'pausas.php?inicio='+$('#from').val()+'&fin='+$('#to').val(),
      				dataType: 'json'
      				},
      			build_complete: "tableJsonOK",
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
                output_saveFileName  : 'Resultados_Dep_<?php echo $from."a".$to;?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',

                //Sticky
                stickyHeaders_attachTo : '#container-cuartiles',

                editable_columns       : [9],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
	            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
	            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
	            editable_autoResort    : false,         // auto resort after the content has changed.
	            editable_validate      : function(txt, orig, columnIndex, $element){
	            							var dlag=setComments($element.closest('tr').find('id').text(),txt,$element,orig);

	                                      },          // return a valid string: function(text, original, columnIndex){ return text; }
	            editable_focused       : function(txt, columnIndex, $element) {
								              $element.addClass('focused');
								         },
	            editable_blur          : function(txt, columnIndex, $element) {
						              		$element.removeClass('focused');
						            	},
	            editable_selectAll     : function(txt, columnIndex, $element){
							              	return /^b/i.test(txt) && columnIndex === 0;
							            },
	            editable_wrapContent   : '<div>',       // wrap all editable cell content... makes this widget work in IE, and with autocomplete
	            editable_trimContent   : true,          // trim content ( removes outer tabs & carriage returns )
	            editable_noEdit        : 'no-edit',     // class name of cell that is not editable
	            editable_editComplete  : 'saveCh' // event fired after the table content has been edited

	        }
	  	});
	}

	var resultado;

	function setComments(id, comments, $element, orig){
		
		showLoader('Guardando Cambios', {at: 'center', my: 'center', of: $element});

		$.ajax({
			url: 'setComments.php',
			type: "POST",
            data: {id: id, asesor: '<?php echo $_SESSION['asesor_id']; ?>', comentarios: comments},
            dataType: "html", // will automatically convert array to JavaScript
            success: function(data) {
            	datos=data;
            	resultado=data;
            	if(resultado=='OK'){
                	$element.addClass( 'editable_updated' ); // green background + white text
			      			setTimeout(function(){
			        			$element.removeClass( 'editable_updated' );
			      			}, 500);

									dialogLoad.dialog('close');

									showNoty('success', 'Comentario Guardado',4000);

			      			$element.text(comments).closest('td').next('td').text('<?php echo $_SESSION['name']; ?>');

							}else{
                	dialogLoad.dialog('close');

									showNoty('error', data, 4000);
                	$element.text(orig);
              }
            },
            error: function(){
            	dialogLoad.dialog('close');

							showNoty('error', 'Error de Conexión', 4000);

							$element.text(orig);
            }
		});


	}



	$('#load').click(function(){
		$("#result-table").empty();
		printTable();
	});

	$('#export').click(function(){
		$('.tablesorter').trigger('outputTable');
	})


	$('#test').click(function(){
		alert($('#result-table').find('id').text());
	})

});
</script>
<br>




<div id='buttons' style='width: 90%; margin: auto; overflow: auto;'>
	<button id='export' class='button button_red_w'>Exportar</button>
	<button id='test' class='button button_red_w'>Test</button>
</div>
<div style='width: 90%; margin: auto; overflow: auto;'>
<div id='result-table'></div>

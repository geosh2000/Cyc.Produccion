<?php
include_once("../modules/modules.php");
initSettings::start(true,'queries');

timeAndRegion::setRegion('Cun');

?>

<style> .center{
	text-align: center
}
</style>
<script>
$(function(){
	
    $tableresult = $('#result-table table');
	
	function printTable(){
		showLoader("Cargando Query");
		
		$('#result-table').empty();
		
		$.ajax({
			url: 'get_data.php',
			type: 'POST',
			data: {query: $('#query').val()},
			dataType: 'json',
			success: function(array){
					data=array;
					
					if(data['status']==1){
						drawTable(data['result']);
						showNoty('success',data['msg'],4000);
						$('#exportar').show();
					}else{
						showNoty('error', $data['msg'], 4000);
						$('#exportar').hide();
					}
					
					dialogLoad.dialog('close');
				  	
				},
				
				error: function(){
					dialogLoad.dialog('close');
					showNoty('error','Error al recibir informacion',4000);
				}
		});
		
		
	}
	
	
	function drawTable(data){
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
                stickyHeaders_attachTo : '#result-table'
		    }
	  	});
	}
	
	$('#consultar').click(function(){
		$('#exportar').hide();
		printTable();
	});
	
	$('#exportar').hide();
	
	$('#exportar').click(function(){
			$('#result-table table').trigger('outputTable');
	});
	
});
</script>
<div style='width:1200px; margin: auto; text-align: center'>
	<p style='text-align: center'>Query: <textarea rows="20" cols="180" wrap="hard" id='query'></textarea><button class='button button_green_w' id='consultar'>Consultar</button></p>
</div>

<br><button class='button button_red_w' id='exportar'>Exportar</button><br>
<div id='result-table' style='position: relative'></div>
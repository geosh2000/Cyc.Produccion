<?php
include_once('../modules/modules.php');

initSettings::start(true,'monitor_gtr');
initSettings::printTitle('Reporte de Deslogueos');

timeAndRegion::setRegion('Cun');

$tbody="<td><input type='text' id='from' name='from' value='' required/><input type='text' id='to' name='to' value='' required/></td>";

Filters::ShowFilterNOFORM('load', 'Consultar',$tbody);

?>

<style> .center{
	text-align: center
}
</style>

<script>
$(function(){
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		animation: true
	});
	
	
	flag=true;
	
	function search(){
    showLoader('Obteniendo informacion');
    
    $.ajax({
      url: 'logouts.php',
      data: {inicio: $('#from').val(), fin: $('#to').val()},
      type: 'GET',
      dataType: 'json',
      success: function(array){
        data=array;
        
        dialogLoad.dialog('close');
        
        printTable(data);
      },
      error: function( jqXHR, textStatus, errorThrown ) {
                dialogLoad.dialog('close');
                if (jqXHR.status === 0) {

                  showNoty('error','Not connect: Verify Network.',4000);

                } else if (jqXHR.status == 404) {

                  showNoty('error','Requested page not found [404]',4000);

                } else if (jqXHR.status == 500) {

                  showNoty('error','Internal Server Error [500].',4000);

                } else if (textStatus === 'parsererror') {

                  showNoty('error','Requested JSON parse failed.',4000);

                } else if (textStatus === 'timeout') {

                  showNoty('error','Time out error.',4000);

                } else if (textStatus === 'abort') {

                  showNoty('error','Ajax request aborted.',4000);

                } else {

                  showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);

                }

              }
    });
	}
	
	
	function printTable(dataOK){
		
		$('#result-table').tablesorter({
		    theme: 'jui',
		    headerTemplate: '{content} {icon}',
        widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders'],
		    tableClass: 'center',
		    data: dataOK,
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
                output_saveFileName  : 'Resultados_Dep_<?php echo $from."a".$to;?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                
                //Sticky
                stickyHeaders_attachTo : '#container-cuartiles'
                
                	
	        }
	  	});
	}
	
	
	
	$('#load').click(function(){
		$("#result-table").empty();
		search();
	});
	
	$('#export').click(function(){
		$('.tablesorter').trigger('outputTable');
	})
	
	
	

});
</script>


<br><br>




<div id='buttons' style='width: 90%; margin: auto; overflow: auto;'>
	<button id='export' class='button button_green_w'>Exportar</button> 

</div>
<div style='width: 90%; margin: auto; overflow: auto;'>
<div id='result-table'></div>








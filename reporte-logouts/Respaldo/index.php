<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="monitor_gtr";
$menu_asesores="class='active'";


?>

<?php
include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");

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
	
	dialogLoad=$( "#dialog-load" ).dialog({
      modal: true,
      autoOpen: false
    });
    
    progressbarload=$('#progressbarload').progressbar({
	      value: false
	});
	
	flag=true;
	
	
	function printTable(){
		
		$('#result-table').tablesorter({
		    theme: 'jui',
		    headerTemplate: '{content} {icon}',
			widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders'],
		    tableClass: 'center',
		    widgetOptions: {
		    	//Builders
		    	build_type   : 'json',
      			build_source : { 
      				url: 'logouts.php?inicio='+$('#from').val()+'&fin='+$('#to').val(), 
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
                stickyHeaders_attachTo : '#container-cuartiles'
                
                	
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
	
	
	

});
</script>

<table class='t2' style='width:800; margin: auto'>
    <tr class='title'>
        <th colspan=100>Reporte de Logouts</th>
    </tr>
    <tr class='subtitle'>
        <td >Periodo</td>
        <td class='pair'><input type="text" id='from' name='from' value='<?php echo $from; ?>' required/><input type="text" id='to' name='to' value='<?php echo $to; ?>' required/></td>
        <td class='total'><button class="button button_green_w" id="load">Load</button></td>
    </tr>
</table>
<br><br>




<div id='buttons' style='width: 90%; margin: auto; overflow: auto;'>
	<button id='export' class='button button_red_w'>Exportar</button> 

</div>
<div style='width: 90%; margin: auto; overflow: auto;'>
<div id='result-table'></div>

<div id="dialog-load" title="Loading" style='text-align: center'>
	<div id="progressbarload"></div>
</div>






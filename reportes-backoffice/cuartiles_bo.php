<?php
include_once('../modules/modules.php');

initSettings::start(true,'asesor_cuartiles');
initSettings::printTitle('Detalle de Casos BackOffice');

timeAndRegion::setRegion('Cun');

//GET Variables
$skill=$_POST['pcrc'];
$selected[$skill]="selected";
if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d',strtotime('-1 months'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d',strtotime('-1 days'));}

$tbody="<td><input type='text' id='from' name='from' value='$from' required/><input type='text' id='to' name='to' value='$to' required/><input type='hidden' name='consultar' value=1/></td>";
$tbody.="<td>Area</td><td><select id='pcrc' name='pcrc' required><option value=''>Selecciona...</option>";

$tbody.="<option value='48' $selected[48]>Afectaciones</option><option value='45' $selected[45]>Agencias</option><option value='38' $selected[38]>Confirming</option><option value='37' $selected[37]>Mailing</option><option value='40' $selected[40]>Mejora Continua</option><option value='39' $selected[39]>Reembolsos</option>";
$tbody.="</select></td>";

Filters::showFilter('','POST', 'send','Enviar',$tbody);

?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>
$(function(){
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		<?php
			if($cheat!=1){
				echo "minDate: '2016-07-11',";
			}
		?>
		animation: true
	});
});
</script>

<br>
<?php if(!isset($_POST['consultar'])){exit;} ?>
<style> .center{
	text-align: center
}
</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-build-table.js"></script>
<script>
$(function(){
	
	flag=true;
	
	$( "#progressbar" ).progressbar({
      value: false
    });
	
	function printTable(){
		
		$('#result-table').tablesorter({
		    theme: 'jui',
		    headerTemplate: '{content} {icon}',
			widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders'],
		    tableClass: 'center',
		    widgetOptions: {
		    	//Builders
		    	build_type   : 'json',
      			build_source : { url: 'cuartiles_json.php?<?php echo "from=$from&to=$to&skill=$skill"; ?>', dataType: 'json' },
      			
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
                output_saveFileName  : 'cuartiles_<?php echo $from."a".$to;?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                
                //Sticky
                stickyHeaders_attachTo : '#container-cuartiles'
		    }
	  	});
	}
	
	printTable();
	
	$('#export').click(function(){
		$('.tablesorter').trigger('outputTable');
	})
	
	$("#result-table").bind("DOMSubtreeModified", function() {
	    if(flag){
	    	$( "#progressbar" ).progressbar( "destroy" ).text("");
	    	flag=false;
	    }
	});
	
});
</script>

<div id='buttons' style='width: 90%; margin: auto; overflow: auto;'>
	<button id='export' class='button button_red_w'>Exportar</button> 
</div>
<div style='width: 90%; margin: auto; overflow: auto;'>
<div id='result-table'></div>

<div id='progressbar'>Procesando...</div>
</div>





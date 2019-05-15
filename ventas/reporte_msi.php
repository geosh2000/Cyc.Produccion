<?php
//Para hacerlo editable escribir en la direccion ?editable=1

include_once('../modules/modules.php');

initSettings::start(true,'asesor_cuartiles');
initSettings::printTitle('Detalle de MSI Banamex');

timeAndRegion::setRegion('Cun');

//GET Variables
if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d',strtotime('-1 months'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d',strtotime('-1 days'));}

$tbody="<td><input type='text' id='from' name='from' value='$from' required/><input type='text' id='to' name='to' value='$to' required/><input type='hidden' name='consultar' value=1/></td>";

Filters::showFilter('','POST', 'send','Enviar',$tbody);
?>

<script>
$(function(){
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		formatDate: 'YYYY-MM-DD',
		<?php
			if($cheat!=1){
				echo "minDate: '2016-07-11',";
			}
		?>
		animation: true
	});
});
</script>
<br><br>
<?php if(!isset($_POST['consultar'])){exit;} ?>
<style> .center{
	text-align: center
}

.f_recep{
	text-align: center
}
</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-build-table.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script>
$(function(){
	
	flag=true;
	
	$( "#progressbar" ).progressbar({
      value: false
    });
	
	$('body').on('focus',".f_recep", function(){
		$(this).periodpicker({
			norange: true, // use only one value
			cells: [1, 1], // show only one month
		
			resizeButton: false, // deny resize picker
			fullsizeButton: false,
			fullsizeOnDblClick: false,
		
			timepicker: true, // use timepicker
			formatDateTime: 'YYYY-MM-DD HH:mm:ss',
			timepickerOptions: {
				hours: true,
				minutes: true,
				seconds: false,
				ampm: true
			},
			
			onOkButtonClick: function(){
				reg_id=this.startinput.closest('td').find('.f_recep').attr('reg');
				reg_newVal=this.startinput.val();
				editDate(reg_id,reg_newVal);
			}
		});
	});
	
	
	function printTable(){
		
		$('#result-table').tablesorter({
		    theme: 'jui',
		    headerTemplate: '{content} {icon}',
			widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders', 'editable'],
		    tableClass: 'center',
		    widgetOptions: {
		    	//Builders
		    	build_type   : 'json',
      			build_source : { url: 'detalle_msi.php?tipo=1&from='+$('#from').val()+'&to='+$('#to').val(), dataType: 'json' },
      			
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
                output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                output_wrapQuotes    : false,       // wrap every cell output in quotes
                output_popupStyle    : 'width=580,height=310',
                output_saveFileName  : 'Detalle_MSI_<?php echo $from."a".$to;?>.csv',
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
	
	$('.f_recep').change(function(){
		fila = $(this).closest('tr').find('td');
		alert(fila);
	})
	
});
</script>

<div id='buttons' style='width: 90%; margin: auto; overflow: auto;'>
	<button id='export' class='button button_red_w'>Exportar</button> 
</div>
<div style='width: 90%; margin: auto; overflow: auto;'>
<div id='result-table'></div>

<div id='progressbar'>Procesando...</div>
</div>





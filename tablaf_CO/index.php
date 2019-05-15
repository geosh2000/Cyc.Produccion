<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="tablas_f";
$menu_asesores="class='active'";


?>

<?php
include("../connectMYSQLI.php");
//header("Content-Type: text/html;charset=utf-8");

//GET Variables
if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d',strtotime('-1 months'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d',strtotime('-1 days'));}
$perc_defined=0.8;

$cheat=$_GET['cheat'];


include("../common/scripts.php");
include("../common/menu.php");
$connectdb->close();

?>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-build-table.js"></script>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
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
	
	dialogLoad=$( "#dialog-load" ).dialog({
      modal: true,
      autoOpen: false
    });
    
    progressbarload=$('#progressbarload').progressbar({
	      value: false
	});
	
	//$('#result-table').tablesorter();
	
	$tableresult = $('#result-table table');
	
	function printTable(){
		dialogLoad.dialog('open');
		
		/*if($('#result-table table').length > 0){
			$tableresult = $('#result-table table');
			$.tablesorter.clearTableBody( $tableresult[0] );
		}*/
		
		$('#result-table').empty();
		
		if($('#mxn').prop('checked')){
			currency='mxn';
		}else if($('#usd').prop('checked')){
			currency='usd';
		}else{
			currency='cop';
		}
		
		$.ajax({
			url: 'get_data.php',
			type: 'GET',
			data: {from: $('#from').val() , to: $('#to').val(), currency: currency},
			dataType: 'json',
			success: function(array){
						data=array;
				
						drawTable(data);
					  	
					  	dialogLoad.dialog('close');
					  	$('#exportar').show();
				},
				
				error: function(){
					dialogLoad.dialog('close');
					alert('Error al recibir informacion');
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
		    	columns : [ "primary", "secondary", "tertiary" ],
		    	columns_tfoot : true,
		    	
		    	//Builder
		    	build_objectRowKey    : 'rows',    // object key containing table rows
				build_objectHeaderKey : 'headers', // object key containing table headers
				build_objectFooterKey : 'footers',  // object key containing table footers
				  
				  
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
                output_saveFileName  : 'Venta_CO_<?php echo $from."a".$to;?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                
                //Sticky
                stickyHeaders_attachTo : '#container-cuartiles'
		    }
	  	});
	}
	
	initdata = {"rows":[["Anayeli Rojas","Ulisses Serrano","8","Ventas MP","2016-11-09<br>a<br>2016-11-10","7","$140,316.54","11","$92,107.51","18","$232,424.05","36.00%","50","4","711.85","54","0","0.00%","4","4","1837.6833","100.20","0.00","94.55%","88.06%","2","0","0","1","2","0","0","0","0","0","0","0","0","0","0"]],"headers":[[{"text":"Asesor"},{"text":"Supervisor"},{"text":"Esquema"},{"text":"Departamento"},{"text":"Fechas"},{"text":"Localizadores MT"},{"text":"Monto MT"},{"text":"Localizadores MP"},{"text":"Monto MP"},{"text":"Localizadores Total"},{"text":"Monto Total"},{"text":"FC"},{"text":"Llamadas Reales"},{"text":"Transferidas 1min"},{"text":"AHT"},{"text":"Llamadas"},{"text":"Llamadas Colgadas"},{"text":"Porcentaje Colgadas"},{"text":"Transferidas"},{"text":"Transferidas 1min"},{"text":"Duracion Sesion"},{"text":"Pausas No Productivas"},{"text":"Pausas Productivas"},{"text":"Utilizacion"},{"text":"Adherencia"},{"text":"Retardos"},{"text":"Faltas"},{"text":"Ausentismos Autorizados"},{"text":"Traslados items"},{"text":"Traslados pax"},{"text":"Traslados noches"},{"text":"Tours items"},{"text":"Tours pax"},{"text":"Tours noches"},{"text":"Cruceros items"},{"text":"Cruceros pax"},{"text":"Cruceros noches"},{"text":"Circuitos items"},{"text":"Circuitos paxs"},{"text":"Circuitos noches"}]]};
	
	//drawTable(initdata);
	
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

<table class='t2' style='width:800; margin: auto'>
    <tr class='title'>
        <th colspan=100>Tabla F Colombia</th>
    </tr>
    <tr class='subtitle'>
        <td width='14%' >Periodo</td>
        <td width='14%'  class='pair'><input type="text" id='from' name='from' value='<?php echo $from; ?>' required/><input type="text" id='to' name='to' value='<?php echo $to; ?>' required/></td>
        <td width='14%'  class='pair'>USD: <input type="radio" id='usd' name='currency'><br>COP: <input type="radio" id='cop' name='currency' checked><br>MXN: <input type="radio" id='mxn' name='currency'></td>
        <td class='total'><button class='button button_blue_w' id='consultar'>Consultar</button></td>
    </tr>
</table>
<br><button class='button button_red_w' id='exportar'>Exportar</button><br>
<div id='result-table'></div>
<div id="dialog-load" title="Cargando Tabla" style='text-align: center'>
	<div id="progressbarload"></div>
</div>




<?
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="monitor_gtr";
$menu_programaciones="class='active'";

header('Content-Type: text/html; charset=utf-8');

include("../connectDB.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');
//echo session_id()."<br>";
include("../common/menu.php");

$_POST['submit']=1;
$_POST['start']='2016-07-01';
$_POST['end']='2016-09-30';
$_POST['skill']=$_GET['skill'];

include('functions.php');


?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>

$(function() {
    $('.tablesorter').tablesorter({
            theme: 'blue',
            sortList: [[0,0]],
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output' ],
            widgetOptions: {

               resizable_addLastColumn : true,
               resizable_widths : [ ,,,,,,'65px' ],
               uitheme: 'jui',
                columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
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
                output_saveFileName  : 'out.xls',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                


            }
        });
        
        $("#exporter").click(function(){
        	$('#info').trigger('outputTable');
        });
        
	        
     
});
</script>
<button id='exporter' class='button button_blue_w'>Exportar</button>
<table class='tablesorter' id='info'>
	<thead>
		<tr>
			<th>Fecha</th>
			<th>Hora</th>
			<th>Erlang</th>
			<th>Necesarios</th>
			<th>skill</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($data as $d_fecha => $d_info){
				foreach($d_info['forecast'] as $d_hora => $d_info2){
					echo "<tr>\n<td>$d_fecha</td>\n<td>$d_hora</td>\n";
					echo "<td>".$d_info['erlang'][$d_hora]."</td>\n";
					echo "<td>".$d_info['necesarios'][$d_hora]."</td>\n";	
					echo "<td>$skill</td>";	
					echo "</tr>\n";
				}
			}
		?>
	</tbody>
</table>

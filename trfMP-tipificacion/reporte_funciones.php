<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_cuartiles";

include("../connectDB.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');
//header('Content-Type: text/html; charset=utf-8');

$from=date('Y-m-d',strtotime($_POST['from']));
$to=date('Y-m-d',strtotime($_POST['to']));

if(isset($_POST['from'])){
	
	$query="SELECT * FROM trfMP_opts";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$numcols=mysql_num_fields($result);
	
	for($i=0;$i<$num;$i++){
		$titulo[mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'nivel')]=utf8_encode(mysql_result($result, $i, 'titulo'));
	}

	$query="SELECT 
				a.id, a.actividad as actividadid, date_created as Fecha, Nombre, b.actividad as Actividad,  
					c1.opcion as opt_1, c2.opcion as opt_2,
					c3.opcion as opt_3, c4.opcion as opt_4,
					c5.opcion as opt_5, c6.opcion as opt_6,
					c7.opcion as opt_7, c8.opcion as opt_8,
					c9.opcion as opt_9,
					item as 'Item Genérico', pnrs_en_cola as 'PNRs en Cola', codigo_aerolinea as 'Código de Aerolínea',
					a.em as EM, a.loc as Localizador, a.pnr as PNR
			FROM 
					trfMP_funciones a 
				LEFT JOIN 
					trfMP_actividad b ON a.actividad=b.id
				LEFT JOIN
					trfMP_opts c1 ON a.level1=c1.id
				LEFT JOIN
					trfMP_opts c2 ON a.level2=c2.id
				LEFT JOIN
					trfMP_opts c3 ON a.level3=c3.id
				LEFT JOIN
					trfMP_opts c4 ON a.level4=c4.id
				LEFT JOIN
					trfMP_opts c5 ON a.level5=c5.id
				LEFT JOIN
					trfMP_opts c6 ON a.level6=c6.id
				LEFT JOIN
					trfMP_opts c7 ON a.level7=c7.id
				LEFT JOIN
					trfMP_opts c8 ON a.level8=c8.id
				LEFT JOIN
					trfMP_opts c9 ON a.level9=c9.id
				LEFT JOIN
					Asesores d ON a.asesor=d.id
			WHERE
				CAST(a.date_created as DATE) BETWEEN '$from' AND '$to'
			ORDER BY a.id";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$numcols=mysql_num_fields($result);
	
	for($i=0;$i<$num;$i++){
		for($x=2;$x<$numcols;$x++){
			//Check NULL
			if(mysql_result($result, $i,mysql_field_name($result, $x))==NULL){continue;}
			
			//Name Titles
			if($titulo[mysql_result($result,$i,'actividadid')][str_replace("opt_", "", mysql_field_name($result, $x))]==NULL){
				$title=mysql_field_name($result, $x);
			}else{
				$title=$titulo[mysql_result($result,$i,'actividadid')][str_replace("opt_", "", mysql_field_name($result, $x))];
			}
			
			$data_titles[$title]=1;
			
			//Create Array
			$datatable[mysql_result($result,$i,'id')][$title]=utf8_encode(mysql_result($result, $i,mysql_field_name($result, $x)));
		}
	}
	
	
	
	
}else{
	$from=date('Y-m-d',strtotime('-7 days'));
	$to=date('Y-m-d');
}

include("../common/menu.php");

?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script>



$(function () {
	
	$('#from').periodpicker({
		end: '#to',
		lang: 'en',
		animation: true
	});

    $("#tablesorter").tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'uitheme','zebra', 'stickyHeaders', 'filter','output'],
            widgetOptions: {
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
                stickyHeaders_attachTo : '#config-contain',
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
                output_saveFileName  : 'funciones_tmp_<?php echo "from - $to";?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,'


            }
        });
        
       $('#export').click(function(){
            $('#tablesorter').trigger('outputTable');

        });
});	
	
</script>
<table class='t2' style='text-align: center; margin: auto; width: 600px;'>
	<tr class='title'><form id='dates' method='POST'>
		<th>Reporte de Funciones Tráfico MP</th>
		<th>Periodo</th>
		<th class='pair'><input type='text' value=<?php echo "'$from'"; ?> name='from' id='from' required><input type='text' value=<?php echo "'$to'"; ?> name='to' id='to' required></th>
		<th class='pair'><button class='button button_blue_w' id='submit'>Consultar</button></th>
	</tr></form>
	
</table>
<br>
<button id='export' class='button button_red_w'>Export</button>
<table id='tablesorter' style='text-align: center'>
	<thead>
		<?php 
			foreach($data_titles as $title => $info){
				echo "<th>$title</th>\n\t";
			}
			unset($title, $info);
		?>	
	</thead>
	<tbody>
		<?php 
			foreach($datatable as $id => $data){
				echo "<tr>\n";
				foreach($data_titles as $title => $info){
					echo "<td>".$data[$title]."</td>\n\t";
				}
				unset($title, $info);
				echo "</tr>\n";
			}
		?>
	</tbody>
</table>


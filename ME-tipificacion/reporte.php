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

	$titles[]="Asesor";
	$titles[]="Fecha";

	$query="SELECT nivel, titulo FROM ME_opts GROUP BY nivel ORDER BY nivel";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_array(MYSQL_BOTH)){
			$titles[]=$fila['titulo'];
		}
	}

	$query="SELECT a.id, NombreAsesor(asesor,1) as Asesor, date_created,
				IF(b.opcion IS NULL, level1, b.opcion) as level1,
				IF(c.opcion IS NULL, level2, c.opcion) as level2,
				IF(d.opcion IS NULL, level3, d.opcion) as level3,
				IF(e.opcion IS NULL, level4, e.opcion) as level4,
				IF(f.opcion IS NULL, level5, f.opcion) as level5,
				IF(g.opcion IS NULL, level6, g.opcion) as level6,
				IF(h.opcion IS NULL, level7, h.opcion) as level7,
				IF(i.opcion IS NULL, level8, i.opcion) as level8,
				IF(j.opcion IS NULL, level9, j.opcion) as level9  FROM ME_tipificacion a
			LEFT JOIN ME_opts b ON a.level1=b.id
			LEFT JOIN ME_opts c ON a.level2=c.id
			LEFT JOIN ME_opts d ON a.level3=d.id
			LEFT JOIN ME_opts e ON a.level4=e.id
			LEFT JOIN ME_opts f ON a.level5=f.id
			LEFT JOIN ME_opts g ON a.level6=g.id
			LEFT JOIN ME_opts h ON a.level7=e.id
			LEFT JOIN ME_opts i ON a.level8=f.id
			LEFT JOIN ME_opts j ON a.level9=g.id
			WHERE
				CAST(a.date_created as DATE) BETWEEN '$from' AND '$to'
			ORDER BY a.id";
	if($result=$connectdb->query($query)){
		$fields=$result->fetch_fields();
		while($fila=$result->fetch_array(MYSQL_BOTH)){
			for($i=1;$i<$result->field_count;$i++){
				$data[$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
			}
		}
	}else{
		echo "Error al obtener info: ".$connectdb->error."<br>";
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
		<th>Reporte de Tipificación<br>Mesa de Expertos</th>
		<th>Periodo</th>
		<th class='pair'><input type='text' value=<?php echo "'$from'"; ?> name='from' id='from' required><input type='text' value=<?php echo "'$to'"; ?> name='to' id='to' required></th>
		<th class='pair'><button class='button button_blue_w' id='submit'>Consultar</button></th>
	</tr></form>

</table>
<br>
<button id='export' class='button button_red_w'>Export</button>
<table id='tablesorter' style='text-align: center'>
	<thead>
		<tr>
		<?php
			foreach($data as $id => $info){
				$i=0;
				foreach($info as $field => $info2){
					if(isset($titles[$i])){
						$title=$titles[$i];
					}else{
						$title=$field;
					}
					echo "<th>$title</th>";
					$i++;
				}
				break;
			}

		?>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($data as $id => $info){
				echo "<tr>";
				foreach($info as $field => $info2){
					echo "<td>$info2</td>";
				}
				echo "</tr>\n\t";
			}
		?>
	</tbody>
</table>

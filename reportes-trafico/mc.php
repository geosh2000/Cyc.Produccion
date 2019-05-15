<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="tablas_f";
$menu_programaciones="class='active'";

header('Content-Type: text/html; charset=utf-8');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");
date_default_timezone_set('America/Bogota');

//POST
if(isset($_POST['start'])){
	$inicio=date('Y-m-d', strtotime($_POST['start']));	
}else{
	$inicio=date('Y-m-d', strtotime('-7 days'));	
}

if(isset($_POST['end'])){
	$fin=date('Y-m-d', strtotime($_POST['end']));	
}else{
	$fin=date('Y-m-d', strtotime('-1 days'));	
}


//QUERY
if(isset($_POST['submit'])){
	
	$query="SELECT 
			a.id, Nombre, caso, fecha_asignacion, fecha_respuesta, IF(primera_asignacion=1,'Si','No') as primer_contacto, b.Seguimiento
		FROM 
			trfMT_mejora_continua a
		LEFT JOIN
			trfMT_mejora_seguimientos b ON a.tipo_seguimiento=b.id
		LEFT JOIN
			Asesores c ON a.asesor=c.id
		WHERE
			fecha_respuesta BETWEEN '$inicio' AND '$fin'";
	$result=mysql_query($query);
	if(mysql_error()){
		echo "ERROR: ".mysql_error()."<br>";
	}
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$data[mysql_result($result,$i,'id')]['Asesor']=utf8_encode(mysql_result($result, $i, 'Nombre'));	
		$data[mysql_result($result,$i,'id')]['Fecha de Asignación']=mysql_result($result, $i, 'fecha_asignacion');
		$data[mysql_result($result,$i,'id')]['Fecha de Respuesta']=mysql_result($result, $i, 'fecha_respuesta');
		$data[mysql_result($result,$i,'id')]['Primer Contacto']=mysql_result($result, $i, 'primer_contacto');
		$data[mysql_result($result,$i,'id')]['Caso']=mysql_result($result, $i, 'caso');
		$data[mysql_result($result,$i,'id')]['Tipo de Seguimiento']=mysql_result($result, $i, 'Seguimiento');
			
	}
		
}

?>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>

$(function() {
    $('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});
	
	$('#result').tablesorter({
        theme: 'blue',
        headerTemplate: '{content}',
        widthFixed: false,
        widgets: [ 'zebra','filter', 'output', 'stickyHeaders'],
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
            output_saveFileName  : 'Mejora-<?php echo "$inicio a $fin";?>.csv',
            // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
            output_encoding      : 'data:application/octet-stream;charset=utf8,',
            stickyHeaders_attachTo : '#container'
        }
    });
    
    $('#export').click(function(){
        $('#result').trigger('outputTable');
    });

	
});

</script>

<table class='t2' style='width:600px; margin:auto'><form action="mc.php" method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=10>Reporte de Actividades <?php if(isset($_POST['submit'])){echo " ($inicio a $fin)";} ?></th>
	</tr>
	<tr class='title'>
		<td style='width:33%'>Periodo</td>
		<td><input type='text' name='start' id='inicio' value='<?php echo $inicio; ?>' required><input type='text' name='end' id='fin' value='<?php echo $fin; ?>' required></td>
		<td class='total'><input type="submit" value="Consultar" name="submit"></td>
	</tr>
	
	
</form></table>
<br><br>
<?php
	if(!isset($_POST['submit'])){exit;}	
?>
<div style='text-align:right; width: 1200px; margin:auto'><button class='button button_blue_w' id='export'>Exportar</button></div>
<div id='container' style='position: relative; max-height:630px; overflow: auto; margin: auto; width:1200px;'>
<table id='result' style='text-align: center'>
	<thead>
		<tr>
			<?php
				if(count($data)>1){
					foreach($data as $id => $info){
						foreach($info as $titles => $info2){
							echo "<th>$titles</th>";
						}
						unset($titles,$info2);
						break;
					}
					unset($id,$info);
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			if(count($data)>1){
				foreach($data as $id => $info){
					echo "<tr>\n\t";
					foreach($info as $titles => $info2){
						$size=100/count($info);
						echo "<td width='$size%'>$info2</td>";
					}
					unset($titles,$info2);
					echo "</tr>\n\t";
				}
				unset($id,$info);
			}
		?>
	</tbody>
</table>
</div>

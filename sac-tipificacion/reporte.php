<?php

include_once("../modules/modules.php");

initSettings::start(true, 'asesor_cuartiles');
timeAndRegion::setRegion('Cun');

initSettings::printTitle('Reporte Tipificación SAC');

Scripts::periodScript('from', 'to');

$connectdb=Connection::mysqliDB('CC');

$from=date('Y-m-d',strtotime($_POST['from']));
$to=date('Y-m-d',strtotime($_POST['to']));

if(isset($_POST['from'])){

	/*$query="SELECT 
		CAST(Last_Update as DATE) as Fecha, NombreAsesor(asesor,2) as Nombre, COUNT(*) as Registros 
	FROM 
		sac_tipificacion
	WHERE CAST(Last_Update as DATE) BETWEEN '$from' AND '$to'
	GROUP BY asesor, Fecha
	ORDER BY Nombre, Fecha";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$numcols=mysql_num_fields($result);
	
	for($i=0;$i<$num;$i++){
		for($x=0;$x<$numcols;$x++){
			$datatable[$i][mysql_field_name($result, $x)]=utf8_encode(mysql_result($result, $i,mysql_field_name($result, $x)));
		}
		
	}*/
	
	$query="SELECT 
		CAST(Last_Update as DATE) as Fecha, CONCAT(HOUR(Last_Update),IF(MINUTE(Last_Update)>=30,'.5','.0')) as Hora, Nombre, f.Canal, g.Producto, b.Motivo_General, c.Motivo_Especifico, d.Detalle, Localizador 
	FROM 
		sac_tipificacion a 
	LEFT JOIN
		sac_motivos_generales b ON a.motivo_general=b.id
	LEFT JOIN
		sac_motivos_especificos c ON a.motivo_especifico = c.id
	LEFT JOIN
		sac_detalle d ON a.detalle=d.id
	LEFT JOIN
		sac_canal f ON a.canal=f.id
	LEFT JOIN
		sac_productos g ON a.producto=g.id
	LEFT JOIN
		Asesores e ON a.asesor=e.id
	WHERE CAST(a.Last_Update as DATE) BETWEEN '$from' AND '$to'
	ORDER BY Last_Update";
	if($result=$connectdb->query($query)){
		$fields=$result->fetch_fields();
		$numcols=$result->field_count;
		$num=$result->field_count;
		$i=0;
		while($fila=$result->fetch_array(MYSQLI_BOTH)){
			$data[utf8_encode($fila['Motivo_General'])][utf8_encode($fila['Detalle'])]++;
			
			for($x=0;$x<$numcols;$x++){
				$datatable[$i][$fields[$x]->name]=utf8_encode($fila[$x]);
			}
		
		$i++;
		}
	}
	
	$connectdb->close();
	
	foreach($data as $index => $info){
		asort($data[$index]);
	}
	unset($index,$info);
}else{
	$from=date('Y-m-d',strtotime('-7 days'));
	$to=date('Y-m-d');
}


$tbody="<td>Periodo</td><td><input type='text' value='$from' name='from' id='from' required><input type='text' value='$to' name='to' id='to' required></td>";
Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'submit', 'Consultar', $tbody);




?>


<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script>



$(function () {
	
	

    var colors = ['#C390D4','#D4A190','#A1D490','#90C3D4','#D4C490'],
        categories = [<?php
        	foreach($data as $mg => $info){
        		echo "'$mg',";
        	}
			unset($mg,$info);
        ?>],
        <?php
        	$x=0;
			echo "data = [";
        	foreach($data as $mg => $info){
        		echo "{
				            y:". (number_format(array_sum($info)/$num,2,'.','')).","
				            ."color: colors[$x],
				            drilldown: {
				                name: 'Detalle',"
								."categories: [";
									foreach($info as $index => $info2){
										echo "'$index',";
									}
									unset($index,$info2);
				                echo "],
				                data: [";
				                	foreach($info as $index => $info2){
										echo number_format(($info2/$num),2,'.','').",";
									}
									unset($index,$info2);
								echo "],
				                color: colors[$x]
				            }
				        },";
        	$x++;
			}
			unset($mg,$info);
        	echo "],";
        ?>
        
        mgData = [],
        detData = [],
        i,
        j,
        dataLen = data.length,
        drillDataLen,
        brightness;


    // Build the data arrays
    for (i = 0; i < dataLen; i += 1) {

        // add mg data
        mgData.push({
            name: categories[i],
            y: data[i].y,
            color: data[i].color
        });

        // add det data
        drillDataLen = data[i].drilldown.data.length;
        for (j = 0; j < drillDataLen; j += 1) {
            brightness = 0.2 - (j / drillDataLen) / 5;
            detData.push({
                name: data[i].drilldown.categories[j],
                y: data[i].drilldown.data[j],
                color: Highcharts.Color(data[i].color).brighten(brightness).get()
            });
        }
    }

    // Create the chart
    $('#container').highcharts({
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Tipificacion de llamadas, <?php if(isset($_POST['from'])){echo $from." - ".$to;} ?>'
        },
        subtitle: {
            text: 'Fuente: Base SAC'
        },
        yAxis: {
            title: {
                text: 'Participación por motivo'
            }
        },
        plotOptions: {
            pie: {
                shadow: false,
                center: ['50%', '50%']
            }
        },
        tooltip: {
            valueSuffix: '%'
        },
        series: [{
            name: 'Motivo General',
            data: mgData,
            size: '60%',
            dataLabels: {
                formatter: function () {
                    return this.y > 5 ? this.point.name : null;
                },
                color: '#ffffff',
                distance: -30
            }
        }, {
            name: 'Detalle',
            data: detData,
            size: '80%',
            innerSize: '60%',
            dataLabels: {
                formatter: function () {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>' + this.point.name + ':</b> ' + this.y + '%' : null;
                }
            }
        }]
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
                output_saveFileName  : 'tipificacion_sac_<?php echo "from - $to";?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,'


            }
        });
        
       $('#export').click(function(){
            $('#tablesorter').trigger('outputTable');

        });
});	
	
</script>

<br>
<div id='container'>Error al Cargar Gráfica. Intenta nuevamente</div>
<br>
<button id='export' class='button button_red_w'>Export</button>
<table id='tablesorter' style='text-align: center'>
	<thead>
		<?php 
			foreach($datatable[0] as $title => $info){
				echo "<th>$title</th>\n\t";
			}
			unset($title, $info);
		?>	
	</thead>
	<tbody>
		<?php 
			foreach($datatable as $index => $info){
				echo "<tr>\n\t";
					foreach($info as $title => $info2){
						echo "<td>$info2</td>\n\t";
					}
					unset($title, $info2);
				echo "</tr>\n\t";
			}
			unset($index, $info);
		?>
	</tbody>
</table>


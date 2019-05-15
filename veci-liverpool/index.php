<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];


if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="afiliados";

include("../connectDB.php");
include("../common/scripts.php");

//default timezone
date_default_timezone_set('America/Bogota');

//Get Variables
$afiliado=$_POST['afiliado'];
$from=$_POST['from'];
if($from==NULL){$from=date('Y-m-d', strtotime('-5 days'));}else{$from=date('Y-m-d', strtotime($_POST['from']));  }
$to=$_POST['to'];
if($to==NULL){$to=date('Y-m-d', strtotime('-1 days'));}else{$to=date('Y-m-d', strtotime($_POST['to']));  }
$classid=1;


?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script>
  $(function() {
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		animation: true
	});

    $('.tablesorter-childRow td').toggle();
    $( "#tabs" ).tabs();

        $('#result').tablesorter({
            theme: 'blue',
            sortList: [[1,0],[0,0]],
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            cssChildRow : "tablesorter-childRow",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','filter','output', 'stickyHeaders'],
            widgetOptions: {
               uitheme: 'jui',
               columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_thead: true,
                filter_childRows: true,
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
                stickyHeaders_attachTo : '#day-contain',
                 output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                  output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)
                  output_hiddenColumns : false,       // include hidden columns in the output
                  output_includeFooter : true,        // include footer rows in the output
                  output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                  output_headerRows    : true,        // output all header rows (multiple rows)
                  output_delivery      : 'd',         // (p)opup, (d)ownload
                  output_saveRows      : 'v',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                  output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                  output_replaceQuote  : '\u201c;',   // change quote to left double quote
                  output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                  output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                  output_wrapQuotes    : false,       // wrap every cell output in quotes
                  output_popupStyle    : 'width=580,height=310',
                  output_saveFileName  : 'tabla_f_pordia.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });

        $('#acumulado').tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','output', 'stickyHeaders'],
            widgetOptions: {
               uitheme: 'jui',
               columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_thead: true,
                resizable: true,
                saveSort: true,
                stickyHeaders_attachTo : '#acum-contain',
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
                  output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                  output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                  output_wrapQuotes    : false,       // wrap every cell output in quotes
                  output_popupStyle    : 'width=580,height=310',
                  output_saveFileName  : 'tabla_f_acumulado.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });


    // clicking the download button; all you really need is to
    // trigger an "output" event on the table
    $('#exportaacumulado').click(function(){
        $('#acumulado').trigger('outputTable');

    });
     $('#exportapordia').click(function(){
        $('#result').trigger('outputTable');

    });


    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });


    }
);
  </script>

<table style='width:800px; margin: auto' class='t2'><form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
    <tr class='title'>
         <th colspan="100">Tabla F Veci / Liverpool</th>
    </tr>
    <tr class='title'>
         <td>Periodo</td>
         <td class='total' rowspan=2><input type="submit" name='consulta' value='consulta' /></td>
    </tr>
    <tr class='pair'>
         
         <td><input type="text" id="from" name="from" value='<?php  echo $from; ?>' required><input type="text" id="to" name="to" value='<?php  echo $to; ?>' required></td>
    </tr>

</form></table>
<br><br>
<?php 

    if(!isset($_POST['consulta'])){exit;}
?>
<div style='width:100%; text-align: right; vertical-align:top;'>
<button type='button' class='buttonlarge button_blue_w' id='exportapordia'>Exportar<br>Por Dia</button>
<button type="button" class='group_select buttonlarge button_redpastel_w' id='exportaacumulado'>Exportar<br>Acumulado</button>
</div>
<?php

	$query="SELECT 
					Calls.Fecha, Calls.Canal, Calls.Total, Calls.Contestadas, Calls.Abandonadas,Calls.AHT,Calls.ASA,Calls.Talking_Time,Calls.Total_Wait,Calls.SLA20,
					Reservas, Monto
			FROM
				(SELECT 
					a.Fecha, Canal, IF(Canal LIKE '%Veci%',1,0) as CH, COUNT(ac_id) as Total, COUNT(IF(Answered=1,ac_id,NULL)) as Contestadas, COUNT(IF(Answered=0,ac_id,NULL)) as Abandonadas,  
					AVG(IF(Answered=1,TIME_TO_SEC(Duracion_Real),NULL)) as AHT,  
					AVG(IF(Answered=1,TIME_TO_SEC(Espera),NULL)) as ASA, SUM(IF(Answered=1,TIME_TO_SEC(Duracion_Real),NULL)) as Talking_Time,
					SUM(TIME_TO_SEC(Espera)) as Total_Wait,
					COUNT(IF(Answered=1 AND Espera<='00:00:20',ac_id,NULL)) SLA20
				FROM
					t_Answered_Calls a
				LEFT JOIN
					Cola_Skill b ON a.Cola=b.Cola
				LEFT JOIN
					Dids c ON a.DNIS=c.DID
				WHERE
					(c.Canal LIKE '%Veci%' OR c.Canal LIKE '%Liverpool%')  AND
					a.Fecha BETWEEN '$from' AND '$to' AND
					Skill=3
				GROUP BY
					Fecha, Canal
				) Calls
			LEFT JOIN
				(SELECT
					a.Fecha, IF(Afiliado LIKE '%corteingles%',1,0) as CH, COUNT(a.locs_id) as Reservas, 
					IF(VentaMXN=0 AND OtrosIngresosMXN=0 AND EgresosMXN=0,SUM(Venta+OtrosIngresos+Egresos)*Dolar, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN)) as Monto 
				FROM 
					t_Locs a 
				LEFT JOIN
					Fechas b ON a.Fecha=b.Fecha
				WHERE 
					a.Fecha BETWEEN '$from' AND '$to' AND
					(Afiliado LIKE '%corteingles%' OR Afiliado LIKE '%liverpool%') AND
					asesor!=-1 AND
					Venta!=0
				GROUP BY
					a.Fecha, CH
				) Locs
			ON
				Calls.Fecha=Locs.Fecha AND
				Calls.CH=Locs.CH";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['Volumen']=mysql_result($result,$i,'Total');
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['Contestadas']=mysql_result($result,$i,'Contestadas');
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['Abandonadas']=mysql_result($result,$i,'Abandonadas');
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['SLA']=number_format(intval(mysql_result($result,$i,'SLA20'))/intval(mysql_result($result,$i,'Total'))*100,2).'%';
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['AHT']=number_format(mysql_result($result,$i,'AHT'),2);
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['ASA']=number_format(mysql_result($result,$i,'ASA'),2);
		@$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['Abandon']=number_format(mysql_result($result,$i,'Abandonadas')/mysql_result($result,$i,'Total'),2).'%';
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['Talking Time']=number_format(mysql_result($result,$i,'Talking_Time'),0);
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['Localizadores']=mysql_result($result,$i,'Reservas');
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['Monto']='$'.number_format(mysql_result($result,$i,'Monto'),2);
		$data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Canal')]['FC']=number_format(intval(mysql_result($result,$i,'Reservas'))/intval(mysql_result($result,$i,'Contestadas'))*100,2).'%';
		
		$acum[mysql_result($result,$i,'Canal')]['Volumen'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'Total');
		$acum[mysql_result($result,$i,'Canal')]['Contestadas'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'Contestadas');
		$acum[mysql_result($result,$i,'Canal')]['Abandonadas'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'Abandonadas');
		$acum[mysql_result($result,$i,'Canal')]['SLA'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'SLA20');
		$acum[mysql_result($result,$i,'Canal')]['Talking Time'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'Talking_Time');
		$acum[mysql_result($result,$i,'Canal')]['Wait Time'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'Total_Wait');
		$acum[mysql_result($result,$i,'Canal')]['Localizadores'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'Reservas');
		$acum[mysql_result($result,$i,'Canal')]['Monto'][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,'Monto');
	}

foreach($acum as $canal => $info){
	$acumulado[$canal]['Volumen']=number_format(array_sum($info['Volumen']),0);
	$acumulado[$canal]['Contestadas']=number_format(array_sum($info['Contestadas']),0);
	$acumulado[$canal]['Abandonadas']=number_format(array_sum($info['Abandonadas']),0);
	$acumulado[$canal]['SLA']=number_format(intval(array_sum($info['SLA']))/intval(array_sum($info['Volumen']))*100,2).'%';
	$acumulado[$canal]['AHT']=number_format(intval(array_sum($info['Talking Time']))/intval(array_sum($info['Contestadas'])),2);
	$acumulado[$canal]['ASA']=number_format(intval(array_sum($info['Wait Time']))/intval(array_sum($info['Volumen'])),2);
	$acumulado[$canal]['Abandon']=number_format(intval(array_sum($info['Abandonadas']))/intval(array_sum($info['Volumen']))*100,2).'%';
	$acumulado[$canal]['Talking Time']=number_format(array_sum($info['Volumen']),0);
	$acumulado[$canal]['Localizadores']=number_format(array_sum($info['Localizadores']),0);
	$acumulado[$canal]['Monto']='$'.number_format(array_sum($info['Monto']),2);
	$acumulado[$canal]['FC']=number_format(intval(array_sum($info['Localizadores']))/intval(array_sum($info['Contestadas']))*100,2).'%';
}
unset($canal,$info);
?>
<div id='tabs' style='width:1260px; margin: auto;'>
	<ul>
		<li><a href="#porDia">B2C - Info por Dia</a></li>
		<li><a href="#porAcumulado">B2C - Total Acumulado</a></li>
	</ul>
	<div id='porDia'>
		<div style='width: 1170px; max-height: 800px; margin: auto; color: white; background: #008CBA; font-weight: bold; font-size: 24px; height: 20px; padding: 15px'>Informacion por Dia</div>
		<div style='width: 1200px; max-height: 600px; overflow: auto; margin: auto; position: relative' id='day-contain'>
		<table id='result' style='text-align: center; width: 1200px; margin:auto'>
			<thead>
				<tr>
					<th>Fecha</th><th>Canal</th>
					<?php
						foreach($data as $date => $info){
							foreach($info as $canal => $info2){
								foreach($info2 as $kpi => $resultado){
									echo "<th>$kpi</th>";
								}
								unset($kpi,$resultado);
								break;
							}
							unset($canal,$info2);
							break;
						}
						unset($date,$info);
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($data as $date => $info){
						foreach($info as $canal => $info2){
							echo "<tr><td>$date</td><td>$canal</td>";
							foreach($info2 as $kpi => $resultado){
								if($resultado==NULL){
									$resultado=0;
								}
								echo "<td>$resultado</td>";
							}
							unset($kpi,$resultado);
						}
						echo "</tr>\n\t";
						unset($canal,$info2);
					}
					unset($date,$info);
				?>
			</tbody>
		</table>
		</div>
	</div>
	<div id='porAcumulado'>
		<div style='width: 1170px; max-height: 800px; margin: auto; color: white; background: #008CBA; font-weight: bold; font-size: 24px; height: 20px; padding: 15px'>Acumulado</div>
		<div style='width: 1200px; max-height: 800px; overflow: auto; margin: auto; position: relative' id='acum-contain'>
		<table id='acumulado' style='text-align: center; width: 1200px; margin:auto'>
			<thead>
				<tr>
					<th>Canal</th>
					<?php
						foreach($acumulado as $canal => $info){
							foreach($info as $kpi => $resultado){
									echo "<th>$kpi</th>";
								}
								unset($kpi,$resultado);
								break;
						}
						unset($canal,$info);
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($acumulado as $canal => $info){
						echo "<tr><td>$canal</td>";
							foreach($info as $kpi => $resultado){
								if($resultado==NULL){
									$resultado=0;
								}
								echo "<td>$resultado</td>";
							}
							unset($kpi,$resultado);
						echo "</tr>\n\t";
					}
					unset($canal,$info);
				?>
			</tbody>
		</table>
		</div>
	</div>
</div>
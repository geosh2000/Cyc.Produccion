<?php
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

include('functions.php');

?>
<style>
	.table-contain{
		width:95%;
		max-height: 440px;
		overflow: auto; 
		position: relative;
		margin: auto;
		padding: 13px;
		border: solid 2px gray;
	}
	.table-title{
		width:95%;
		margin: auto;
		text-align: left;
		font-size:24px;
		font-weight: bold;
		color: white;
		background: #3280cd;
		vertical-align: middle;
		height: 20px;
		padding: 15px
	}
	.exportable{
		float: right;
		width: 300px;
		text-align: right;
		margin-top: -11px;
	}
</style>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>

$(function() {
    $('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});
	
	tname="";
	
	$( "#tabs, #graphs_tabs" ).tabs();
	
	$('#graphs').accordion({
		active: false,
		collapsible: true
	});
	
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
                output_saveFileName  : tname + '_<?php echo date('Ymd',strtotime($inicio))."_".date('Ymd',strtotime($fin));?>.xls',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                


            }
        });
        
        $(".exporter").click(function(){
        	tname=$(this).attr('tabla');
        	$('#' + tname + "_table").trigger('outputTable');
        });
        
	        
     
});
</script>

<table class='t2' style='width:600px; margin:auto'><form action="consultas.php" method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=10>Consulta de Precisión <?php if(isset($_POST['submit'])){echo " ($depart $inicio a $fin)";} ?></th>
	</tr>
	<tr class='title'>
		<td style='width:33%'>Periodo</td>
		<td style='width:33%'>Programa</td>
		<td rowspan=2 class='total'><input type="submit" value="Consultar" name="submit"></td>
	</tr>
	<tr class='pair'>
		<td><input type='text' name='start' id='inicio' value='<?php echo $inicio; ?>' required><input type='text' name='end' id='fin' value='<?php echo $fin; ?>' required></td>
		<td class='pair'><select name="skill" required><option value=''>Selecciona...</option><?php  $query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
		 														$result=mysql_query($query);
																$num=mysql_numrows($result);
																for($i=0;$i<$num;$i++){
																	if($skill==mysql_result($result, $i, 'id')){$selected="selected";}else{$selected="";}
																	echo "<option value='".mysql_result($result, $i, 'id')."' $selected>".mysql_result($result, $i, 'Departamento')."</option>";
																} ?></select></td>
		
	</tr>
	
</form></table>
<br><br>
<?php

if(!isset($_POST['submit'])){exit;}

?>
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Forecast</a></li>
    <li><a href="#tabs-2">Real</a></li>
    <li><a href="#tabs-3">Precisión por Hora</a></li>
    <li><a href="#tabs-5">Erlang</a></li>
    <li><a href="#tabs-6">Necesarios</a></li>
    <li><a href="#tabs-7">Programados</a></li>
    <li><a href="#tabs-9">Asesores Sentados</a></li>
    <li><a href="#tabs-8">Calidad de Programación</a></li>
    <li><a href="#tabs-10">Cumplimiento de Programación</a></li>
    <li><a href="#tabs-4">Resumen</a></li>
  </ul>
  <div id="tabs-1">
	<div class='table-title'>Forecast<div class='exportable'><button class='button button_red_w exporter' tabla='forecast'>Export</button></div></div>
	<div class='table-contain' id='table-contain-forecast'>
		<table id='forecast_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
			<thead>
			<tr>
				<th style='width: 30px;'>Fecha</th>
				<?php
					$x=0;
					for($i=0;$i<48;$i++){
						echo "<th style='width: 10px;'>$x</th>\n\t";
						$x=$x+0.5;
					}
				?>
			</tr>
			</thead>
			<tbody>
			<?php 
				$x=0;
				for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
					echo "<tr style='text-align: center'>\n\t";	
						echo "<td>$i</td>";
						for($y=0;$y<48;$y++){
							echo "<td>".$data[$i]['forecast'][$y]."</td>\n\t";
						}
					echo "</tr>\n\t";
					
				}
			?>
			</tbody>
		</table>
	</div>
	</div>
	<div id="tabs-2">
	<div class='table-title'>Real<div class='exportable'><button class='button button_red_w exporter' tabla='real'>Export</button></div></div>
	<div class='table-contain' id='table-contain-real'>
		<table id='real_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
			<thead>
			<tr>
				<th style='width: 30px;'>Fecha</th>
				<?php
					$x=0;
					for($i=0;$i<48;$i++){
						echo "<th style='width: 10px;'>$x</th>\n\t";
						$x=$x+0.5;
					}
				?>
			</tr>
			</thead>
			<tbody>
			<?php 
				$x=0;
				for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
					echo "<tr style='text-align: center'>\n\t";	
						echo "<td>$i</td>";
						for($y=0;$y<48;$y++){
							if($data[$i]['real'][$y]==NULL){
								$data[$i]['real_ok'][$y]=0;
							}else{
								$data[$i]['real_ok'][$y]=$data[$i]['real'][$y];
							}
							echo "<td>".$data[$i]['real_ok'][$y]."</td>\n\t";
						}
					echo "</tr>\n\t";
					
				}
			?>
			</tbody>
	</table>
	</div>
	</div>
	<div id="tabs-3">
		<div class='table-title'>Precisión por Hora<div class='exportable'><button class='button button_red_w exporter' tabla='prec-hora'>Export</button></div></div>
		<div class='table-contain' id='table-contain-prec-hora'>
			<table id='prec-hora_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
				<thead>
				<tr>
					<th style='width: 30px;'>Fecha</th>
					<?php
						$x=0;
						for($i=0;$i<48;$i++){
							echo "<th style='width: 10px;'>$x</th>\n\t";
							$x=$x+0.5;
						}
					?>
				</tr>
				</thead>
				<tbody>
				<?php 
					$x=0;
					for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr style='text-align: center'>\n\t";	
							echo "<td>$i</td>";
							for($y=0;$y<48;$y++){
								if($data[$i]['forecast'][$y]==0){
									$data[$i]['prec-hora'][$y]=0;
								}else{
									$data[$i]['prec-hora'][$y]=$data[$i]['real_ok'][$y]/$data[$i]['forecast'][$y]*100;
								}
								echo "<td>".number_format($data[$i]['prec-hora'][$y],2)."%</td>\n\t";
							}
						echo "</tr>\n\t";
						
					}
				?>
				</tbody>
		</table>
		</div>
	</div>
	<div id="tabs-5">
		<div class='table-title'>Asesores Erlang<div class='exportable'><button class='button button_red_w exporter' tabla='erlang'>Export</button></div></div>
		<div class='table-contain' id='table-contain-erlang'>
			<table id='erlang_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
				<thead>
				<tr>
					<th style='width: 30px;'>Fecha</th>
					<?php
						$x=0;
						for($i=0;$i<48;$i++){
							echo "<th style='width: 10px;'>$x</th>\n\t";
							$x=$x+0.5;
						}
					?>
				</tr>
				</thead>
				<tbody>
				<?php 
					$x=0;
					for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr style='text-align: center'>\n\t";	
							echo "<td>$i</td>";
							for($y=0;$y<48;$y++){
								if($data[$i]['forecast'][$y]==NULL){
									$data[$i]['erlang_ok'][$y]=0;
								}else{
									$data[$i]['erlang_ok'][$y]=$data[$i]['erlang'][$y];
								}
								echo "<td>".$data[$i]['erlang_ok'][$y]."</td>\n\t";
							}
						echo "</tr>\n\t";
						
					}
				?>
				</tbody>
		</table>
		</div>
	</div>
	<div id="tabs-6">
		<div class='table-title'>Necesarios<div class='exportable'><button class='button button_red_w exporter' tabla='necesarios'>Export</button></div></div>
		<div class='table-contain' id='table-contain-necesarios'>
			<table id='necesarios_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
				<thead>
				<tr>
					<th style='width: 30px;'>Fecha</th>
					<?php
						$x=0;
						for($i=0;$i<48;$i++){
							echo "<th style='width: 10px;'>$x</th>\n\t";
							$x=$x+0.5;
						}
					?>
				</tr>
				</thead>
				<tbody>
				<?php 
					$x=0;
					for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr style='text-align: center'>\n\t";	
							echo "<td>$i</td>";
							for($y=0;$y<48;$y++){
								if($data[$i]['forecast'][$y]==NULL){
									$data[$i]['necesarios_ok'][$y]=0;
								}else{
									$data[$i]['necesarios_ok'][$y]=$data[$i]['necesarios'][$y];
								}
								echo "<td>".$data[$i]['necesarios_ok'][$y]."</td>\n\t";
							}
						echo "</tr>\n\t";
						
					}
				?>
				</tbody>
		</table>
		</div>
	</div>
	<div id="tabs-7">
		<div class='table-title'>Programados<div class='exportable'><button class='button button_red_w exporter' tabla='programados'>Export</button></div></div>
		<div class='table-contain' id='table-contain-programados'>
			<table id='programados_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
				<thead>
				<tr>
					<th style='width: 30px;'>Fecha</th>
					<?php
						$x=0;
						for($i=0;$i<48;$i++){
							echo "<th style='width: 10px;'>$x</th>\n\t";
							$x=$x+0.5;
						}
					?>
				</tr>
				</thead>
				<tbody>
				<?php 
					$x=0;
					for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr style='text-align: center'>\n\t";	
							echo "<td>$i</td>";
							for($y=0;$y<48;$y++){
								if($data[$i]['programados'][$y]==NULL){
									$data[$i]['programados'][$y]=0;
								}
								
								echo "<td>".$data[$i]['programados'][$y]."</td>\n\t";
							}
						echo "</tr>\n\t";
						
					}
				?>
				</tbody>
		</table>
		</div>
	</div>
	<div id="tabs-8">
		<div class='table-title'>Calidad Programación<div class='exportable'><button class='button button_red_w exporter' tabla='q_programados'>Export</button></div></div>
		<div class='table-contain' id='table-contain-q_programados'>
			<table id='q_programados_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
				<thead>
				<tr>
					<th style='width: 30px;'>Fecha</th>
					<?php
						$x=0;
						for($i=0;$i<48;$i++){
							echo "<th style='width: 10px;'>$x</th>\n\t";
							$x=$x+0.5;
						}
					?>
				</tr>
				</thead>
				<tbody>
				<?php 
					$x=0;
					for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr style='text-align: center'>\n\t";	
							echo "<td>$i</td>";
							for($y=0;$y<48;$y++){
								if($data[$i]['necesarios_ok'][$y]==0){
									$data[$i]['q_prog'][$y]=100;
								}else{
									$data[$i]['q_prog'][$y]=$data[$i]['programados'][$y]/$data[$i]['necesarios_ok'][$y]*100;	
								}
								
								echo "<td>".number_format($data[$i]['q_prog'][$y],2)."%</td>\n\t";
							}
						echo "</tr>\n\t";
						
					}
				?>
				</tbody>
		</table>
		</div>
	</div>
	<div id="tabs-9">
		<div class='table-title'>Asesores Sentados<div class='exportable'><button class='button button_red_w exporter' tabla='sentados'>Export</button></div></div>
		<div class='table-contain' id='table-contain-sentados'>
			<table id='sentados_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
				<thead>
				<tr>
					<th style='width: 30px;'>Fecha</th>
					<?php
						$x=0;
						for($i=0;$i<48;$i++){
							echo "<th style='width: 10px;'>$x</th>\n\t";
							$x=$x+0.5;
						}
					?>
				</tr>
				</thead>
				<tbody>
				<?php 
					$x=0;
					for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr style='text-align: center'>\n\t";	
							echo "<td>$i</td>";
							for($y=0;$y<48;$y++){
								if($data[$i]['sentados'][$y]==NULL){
									$data[$i]['sentados'][$y]=0;
								}
								
								echo "<td>".$data[$i]['sentados'][$y]."</td>\n\t";
							}
						echo "</tr>\n\t";
						
					}
				?>
				</tbody>
		</table>
		</div>
	</div>
	<div id="tabs-10">
		<div class='table-title'>Cumplimiento Programación<div class='exportable'><button class='button button_red_w exporter' tabla='c_programados'>Export</button></div></div>
		<div class='table-contain' id='table-contain-c_programados'>
			<table id='c_programados_table' class='tablesorter' style='width: auto; margin: auto; text-align: center'>
				<thead>
				<tr>
					<th style='width: 30px;'>Fecha</th>
					<?php
						$x=0;
						for($i=0;$i<48;$i++){
							echo "<th style='width: 10px;'>$x</th>\n\t";
							$x=$x+0.5;
						}
					?>
				</tr>
				</thead>
				<tbody>
				<?php 
					$x=0;
					for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr style='text-align: center'>\n\t";	
							echo "<td>$i</td>";
							for($y=0;$y<48;$y++){
								if($data[$i]['programados'][$y]==0){
									$data[$i]['c_prog'][$y]=100;
								}else{
									$data[$i]['c_prog'][$y]=$data[$i]['sentados'][$y]/$data[$i]['programados'][$y]*100;	
								}
								
								if($data[$i]['c_prog'][$y]>100){
									$data[$i]['c_prog'][$y]=100;
								}
								
								echo "<td>".number_format($data[$i]['c_prog'][$y],2)."%</td>\n\t";
							}
						echo "</tr>\n\t";
						
					}
				?>
				</tbody>
		</table>
		</div>
	</div>
	<div id="tabs-4">
	<div class='table-title'>Resumen<div class='exportable'><button class='button button_red_w exporter' tabla='resumen'>Export</button></div></div>
	<div class='table-contain' id='table-contain-resumen'>
		<table id='resumen_table' class='tablesorter' style='width: 600px; margin: auto; text-align: center'>
			<thead>
			<tr>
				<th style='width: 30px;'>Fecha</th>
				<th>Hora</th>
				<th>Dia</th>
				<th>Calidad Programación</th>
				<th>Cumplimiento Programación</th>
			</tr>
			</thead>
			<tbody>
			<?php 
				$x=9;
				for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
					echo "<tr style='text-align: center'>\n\t";	
						echo "<td>$i</td>";
						$prec_ok[$i]=0;
						$q_prog_ok[$i]=0;
						for($y=18;$y<44;$y++){
							//Precision Hora
							if($data[$i]['prec-hora'][$y]>=85 && $data[$i]['prec-hora'][$y]<=115){
								$prec_ok[$i]++;
							}
							
							//Calidad Programacion
							if($data[$i]['q_prog'][$y]>=80 && $data[$i]['q_prog'][$y]<=120){
								$q_prog_ok[$i]++;
							}
							
							//Cumplimiento Programacion
							if($data[$i]['c_prog'][$y]==100){
								$c_prog_ok[$i]++;
							}
							
						}
						
						$prec[$i]=$prec_ok[$i]/26*100;
						$q_prog[$i]=$q_prog_ok[$i]/26*100;
						$c_prog[$i]=$c_prog_ok[$i]/26*100;
						$prec_day[$i]=array_sum($data[$i]['real'])/$data[$i]['fc_volumen']*100;
						
						echo "<td>".number_format($prec[$i],2)."%</td>"; 
						echo "<td>".number_format($prec_day[$i],2)."%</td>"; 
						echo "<td>".number_format($q_prog[$i],2)."%</td>";
						echo "<td>".number_format($c_prog[$i],2)."%</td>";
					echo "</tr>\n\t";
					
				}
			?>
			<tr  style='font-weight: bold'>
				<td>TOTAL</td>
				<?php
	
					//Total Hora
					$prech_ok=array_sum($prec_ok)/(26*count($prec))*100;
					
					//Total Q Programacion
					$q_prog_ok_total=array_sum($q_prog_ok)/(26*count($q_prog))*100;
					
					//Total Dia
					$precd_total=0;
					foreach($prec_day as $day => $resultday){
						if($resultday>=75 && $resultday<=115){
							$precd_total++;
						}
					}
					unset($day,$resultday);
					$precd_ok=$precd_total/count($prec_day)*100;
					
					//Total Cumplimiento
					$c_prog_ok_total=array_sum($c_prog_ok)/(26*count($c_prog))*100;
									
					echo "<td>".number_format($prech_ok,2)."%</td>";
					echo "<td>".number_format($precd_ok,2)."%</td>";
					echo "<td>".number_format($q_prog_ok_total,2)."%</td>";
					echo "<td>".number_format($c_prog_ok_total,2)."%</td>";
							
				?>
			</tr>
			</tbody>
	</table>
	</div>
	</div>
</div>




</body>

</html>

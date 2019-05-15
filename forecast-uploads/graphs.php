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
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script>

$(function() {
    $('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true,
		norange: true
	});
	
	tname="";
	
	$('#graphs_tabs, #table_tabs' ).tabs();
	
	$('#graphs').accordion();
	
	
	$('#graphs_tabs-1').highcharts({
        data: {
            table: 'fc_v_real'
        },
        series: [{
		    dashStyle: 'longdash'
		}],
        chart: {
            type: 'line'
        },
        title: {
            text: 'Forecast vs. Real'
        },
        yAxis: {
            allowDecimals: false,
            title: {
                text: 'Llamadas'
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.series.name;
            }
        }
    });
    
    $('#graphs_tabs-2').highcharts({
        data: {
            table: 'need_v_prog'
        },
        series: [{
		    dashStyle: 'longdash'
		}],
        chart: {
            type: 'line'
        },
        title: {
            text: 'Necesarios vs. Programados'
        },
        yAxis: {
            allowDecimals: false,
            title: {
                text: 'Asesores'
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.series.name;
            }
        }
    });
    
});
</script>

<table class='t2' style='width:600px; margin:auto'><form action="graphs.php" method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=10>Consulta de Precisión <?php if(isset($_POST['submit'])){echo " ($depart $inicio a $fin)";} ?></th>
	</tr>
	<tr class='title'>
		<td style='width:33%'>Periodo</td>
		<td style='width:33%'>Programa</td>
		<td rowspan=2 class='total'><input type="submit" value="Consultar" name="submit"></td>
	</tr>
	<tr class='pair'>
		<td><input type='text' name='start' id='inicio' value='<?php echo $inicio; ?>' required></td>
		<td class='pair'><select name="skill" required><option value=''>Selecciona...</option>
															<?php  $query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
															
																	if ($resultado=$connectdb->query($query)) {
																	   while ($fila = $resultado->fetch_assoc()) {
																	   		if($skill==$fila['id']){$selected="selected";}else{$selected="";}
																			echo "<option value='".$fila['id']."' $selected>".$fila['Departamento']."</option>";
																		}
																	}else{
																		echo $connectdb->error."<br> ON <br>$query<br>";
																	}
																	
																
																?></select></td>
		
	</tr>
	
</form></table>
<br><br>
<?php

if(!isset($_POST['submit'])){exit;}

?>

<div id='graphs'>
	<h3>Gráfica</h3>
	<div id='graphs_tabs'>
		<ul>
			<li><a href="#graphs_tabs-1">Forecast vs. Real</a></li>
			<li><a href="#graphs_tabs-2">Necesarios vs. Programados</a></li>
		</ul>
		<div id='graphs_tabs-1' class='table-contain'>
			
		</div>
		<div id='graphs_tabs-2' class='table-contain'>
			
		</div>
	</div>	
	<h3>Tablas</h3>
	<div id='table_tabs'>
		<ul>
			<li><a href="#table_tabs-1">Forecast vs. Real</a></li>
			<li><a href="#table_tabs-2">Necesarios vs. Programados</a></li>
		</ul>
		<div id='table_tabs-1' class='table-contain'>
			<table id='fc_v_real'>
				<thead>
					<tr>
						<th>Hora</th>
						<th>Forecast</th>
						<th>Real</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($data as $date => $info){
							foreach($info['forecast'] as $hora => $info2){
								echo "<tr>\n\t";
									echo "<td>".(number_format($hora/2,2))."</td>\n\t";
									echo "<td>$info2</td>\n\t";
									echo "<td>".$info['real'][$hora]."</td>\n\t";
								echo "</tr>\n";
							}
						}
					?>
				</tbody>
			</table>
		</div>
		<div id='table_tabs-2' class='table-contain'>
			<table id='need_v_prog'>
				<thead>
					<tr>
						<th>Hora</th>
						<th>Necesarios</th>
						<th>Programados</th>
						<th>Reales</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($data as $date => $info){
							foreach($info['necesarios'] as $hora => $info2){
								echo "<tr>\n\t";
									echo "<td>".(number_format($hora/2,2))."</td>\n\t";
									echo "<td>$info2</td>\n\t";
									echo "<td>".$info['programados'][$hora]."</td>\n\t";
									echo "<td>".$info['sentados'][$hora]."</td>\n\t";
								echo "</tr>\n";
							}
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>



</body>

</html>

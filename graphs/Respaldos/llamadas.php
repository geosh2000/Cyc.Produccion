<?php
include_once("../modules/modules.php");

initSettings::start(true,"monitor_gtr");
initSettings::printTitle('Llamadas por Departamento');
timeAndRegion::setRegion('Cun');


//Declare Vars
if(isset($_POST['inicio'])){$inicio=date('Y-m-d', strtotime($_POST['inicio']));}else{$inicio=date('Y-m-d', strtotime('-7 days'));}
if(isset($_POST['fin'])){$fin=date('Y-m-d', strtotime($_POST['fin']));}else{$fin=date('Y-m-d');}
$dep=$_POST['dep'];
$tipo=$_POST['tipo'];
$flag=$_POST['flag'];
$presentacion=$_POST['presentacion'];
$operacion=$_POST['operacion'];
	//DOW
		if($_POST['l']=='on'){
			$dow[]=0;
		}
		if($_POST['m']=='on'){
			$dow[]=1;
		}
		if($_POST['x']=='on'){
			$dow[]=2;
		}
		if($_POST['j']=='on'){
			$dow[]=3;
		}
		if($_POST['v']=='on'){
			$dow[]=4;
		}
		if($_POST['s']=='on'){
			$dow[]=5;
		}
		if($_POST['d']=='on'){
			$dow[]=6;
		}

		if(count($dow)==0 || count($dow)==7){
			$q_dow="";
		}else{
			$q_dow=" AND WEEKDAY(Fecha) IN (";
			foreach($dow as $index => $info){
				$q_dow.=$info;
				if(end($dow)!=$info){
					$q_dow.= ",";
				}
			}
			unset($index,$info);
			$q_dow.=") ";
		}

$viz=$_POST['viz'];
switch($viz){
	case 1:
		$visual="percent";
		break;
	case 2:
		$visual="normal";
		break;
	case 3:
		$visual="";
		break;
	case 4:
		$visual="";
		break;
}


$horas=array(
				'00:00','00:30','01:00','01:30',
				'02:00','02:30','03:00','03:30',
				'04:00','04:30','05:00','05:30',
				'06:00','06:30','07:00','07:30',
				'08:00','08:30','09:00','09:30',
				'10:00','10:30','11:00','11:30',
				'12:00','12:30','13:00','13:30',
				'14:00','14:30','15:00','15:30',
				'16:00','16:30','17:00','17:30',
				'18:00','18:30','19:00','19:30',
				'20:00','20:30','21:00','21:30',
				'22:00','22:30','23:00','23:30'
);

if($flag==1){
//Query
	$query="SELECT Fecha, HoraGroup, Answered, COUNT(Llamadas) as Llamadas
					FROM
						(SELECT
							Fecha, CONCAT(IF(HOUR(Hora)<10,'0',''),HOUR(Hora),':',IF(MINUTE(Hora)<30,'00','30')) as HoraGroup, Answered, ac_id as Llamadas, Skill
						FROM t_Answered_Calls a
						LEFT JOIN Cola_Skill b ON a.Cola=b.Cola
						WHERE Fecha BETWEEN '$inicio' AND '$fin' $q_dow
						HAVING Skill=$dep) a
					GROUP BY Fecha, HoraGroup, Answered
					ORDER BY Fecha, HoraGroup, Answered";
	if(!$result=Queries::query($query)){
		echo Queries::error($query);
	}else{
		while($fila=$result->fetch_assoc()){
			$data_hour[$fila['Fecha']][$fila['HoraGroup']][$fila['Answered']]=$fila['Llamadas'];
		}
	}

//Hourly
	foreach($data_hour as $fecha => $info){
		foreach($info as $hour => $info2){
			$data_hourly[$hour][0]+=$info2[0];
			$data_hourly[$hour][1]+=$info2[1];
			$data_hourly_avg[$hour][0][]=$info2[0];
			$data_hourly_avg[$hour][1][]=$info2[1];
			$total_hourly[$hour]+=($info2[0]+$info2[1]);
		}
		unset($hour,$info2);

	}
	unset($fecha,$info);



//Hourly_AVG
	foreach($data_hourly as $hora => $info){
		$avg_hourly[$hora][0]=array_sum($data_hourly_avg[$hora][0])/count($data_hourly_avg[$hora][0]);
		$avg_hourly[$hora][1]=array_sum($data_hourly_avg[$hora][1])/count($data_hourly_avg[$hora][1]);
		$avg_hourly_all[$hora]=(array_sum($data_hourly_avg[$hora][0])+array_sum($data_hourly_avg[$hora][1]))/(count($data_hourly_avg[$hora][1])+count($data_hourly_avg[$hora][0]));
	}
	unset($hora,$info);

//Hourly Part
	foreach($total_hourly as $hour => $info){
		if($operacion==1){
			$part_hourly[$hour]=$total_hourly[$hour]/array_sum($total_hourly);
		}else{
			$part_hourly[$hour]=$avg_hourly_all[$hour]/array_sum($avg_hourly_all);
		}

	}
	unset($hour,$info);



//Daily
	foreach($data_hour as $fecha => $info){
		foreach($info as $hour => $info2){
			$data_day[$fecha][0]+=$info2[0];
			$data_day[$fecha][1]+=$info2[1];
			$data_day_avg[$fecha][0][]=$info2[0];
			$data_day_avg[$fecha][1][]=$info2[1];
			$total_dayly[$fecha]+=($info2[0]+$info2[1]);
		}
		unset($hour,$info2);
	}
	unset($fecha,$info);

//Daily_AVG
	foreach($data_hour as $fecha => $info){
		$avg_dayly[$fecha][0]=array_sum($data_day_avg[$fecha][0])/count($data_day_avg[$fecha][0]);
		$avg_dayly[$fecha][1]=array_sum($data_day_avg[$fecha][1])/count($data_day_avg[$fecha][1]);
	}
	unset($fecha,$info);

//Daily_Part
	foreach($total_dayly as $fecha => $info){
		$dayly_part[$fecha]=$info/array_sum($total_dayly);
	}
	unset($fecha,$info);

//Monthly
	foreach($data_day as $fecha => $info){
		$data_month[date('M', strtotime($fecha))][0]+=$info[0];
		$data_month[date('M', strtotime($fecha))][1]+=$info[1];
	}
	unset($fecha,$info);

}





?>

<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="/js/export-csv/export-csv.js"></script>
<script>

$(function () {

	$('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});

	$('#consultar').click(function(){
		$('#flag').val(1);
		$('#params').submit();
	});

	//Init Options
	$('.pres_opt').hide();
	$('.'+$("#tipo option:selected").attr('txt')).show();

	departamento=$("#dep option:selected").text();

	$('#tipo').change(function(){
		tmp=$("#tipo option:selected").attr('txt');
		$('#operacion').val('');
		$('.pres_opt').hide();
		$('.'+tmp).show();
	});

	 $('#container').highcharts({
        chart: {
            type: '<?php
            		if($viz==4){echo "line";}else{echo "column";}
            		?>'
        },
        title: {
            text: departamento+"  (del <?php echo $inicio; ?> al <?php echo $fin; ?>)"
        },
        xAxis: {
            categories: [<?php
            	switch($tipo){
					case 1:
		            	foreach($horas as $indice => $info){
		            		echo "'$info'";
							if(end($horas)!=$info){
								echo ",";
							}
		            	}
		            	unset($indice,$info);
						break;
					case 2:
						//for($i=date('Y-m-d',strtotime($inicio));$i<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						foreach($avg_dayly as $i => $info){
							echo "'$i',";
							if(end($avg_dayly)!=$info){
								//echo ",";
							}
						}
						unset($i,$info);
						break;
					case 3:
						foreach($data_month as $month => $info){
							echo "'$month'";

							if(end($data_month)!=$info){
								echo ",";
							}
						}
						break;
				}

            ?>]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total de llamadas'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: '<?php echo $visual; ?>',
                dataLabels: {
                    enabled: true,
                    rotation: 270,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },
        series: [{
            name: 'Abandonadas',
            color: '#800000',
            data: [<?php
            	switch($tipo){
					case 1:
						switch($operacion){
							case 1:
								switch($viz){
									case 4:
										foreach($horas as $indice => $info){
						            		if($data_hourly[$info][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo "0,";
						            		}


						            	}
						            	unset($indice,$info);
										break;
									default:
										foreach($horas as $indice => $info){
						            		if($data_hourly[$info][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][0].",";
						            		}


						            	}
						            	unset($indice,$info);
										break;
								}
				            	break;
							case 2:
								switch($viz){
									case 4:
										foreach($horas as $indice => $info){
						            		if($avg_hourly[$info][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo "0,";
						            		}


						            	}
						            	unset($indice,$info);
										break;
									default:
										foreach($horas as $indice => $info){
						            		if($avg_hourly[$info][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][0].",";
						            		}


						            	}
						            	unset($indice,$info);
										break;
								}
								break;

						}
						break;
					case 2:
						switch($operacion){
							case 1:
								switch($viz){
									case 4:
										//for($i=date('Y-m-d',strtotime($inicio));$i<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
										foreach($data_day as $i => $info){
											if($data_day[$i][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo "0,";
						            		}


										}
										unset($i,$info);
										break;
									default:
										//for($i=date('Y-m-d',strtotime($inicio));$i<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
										foreach($data_day as $i => $info){
											if($data_day[$i][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][0].",";
						            		}


										}
										unset($i,$info);
										break;
								}
								break;
							case 2:
								switch($viz){
									case 4:
										//for($i=date('Y-m-d',strtotime($inicio));$i<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
										foreach($avg_dayly as $i => $info){
											if($avg_dayly[$i][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo "0,";
						            		}


										}
										unset($i,$info);
										break;
									default:
										//for($i=date('Y-m-d',strtotime($inicio));$i<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
										foreach($avg_dayly as $i => $info){
											if($avg_dayly[$i][0]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_dayly[$i][0].",";
						            		}


										}
										unset($i,$info);
										break;
								}
								break;
						}
						break;
					case 3:
						foreach($data_month as $indice => $info){
							if($info[0]==NULL){
								echo "0,";
							}else{
								echo $info[0].",";
							}


						}
						break;
				}

            ?>]
        }, {
            name: 'Contestadas',
            color: '#009900',
            data: [<?php

            	switch($tipo){
					case 1:
						switch($operacion){
							case 1:
								switch($viz){
									case 4:
										foreach($horas as $indice => $info){
						            		if($part_hourly[$info]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $part_hourly[$info].",";
						            		}


						            	}
						            	unset($indice,$info);
										break;
									default:
						            	foreach($horas as $indice => $info){
						            		if($data_hourly[$info][1]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][1].",";
						            		}


						            	}
						            	unset($indice,$info);
										break;
								}
								break;
							case 2:
								switch($viz){
									case 4:
										foreach($horas as $indice => $info){
						            		if($part_hourly[$info]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $part_hourly[$info].",";
						            		}


						            	}
						            	unset($indice,$info);
										break;
									default:
										foreach($horas as $indice => $info){
						            		if($avg_hourly[$info][1]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][1].",";
						            		}


						            	}
						            	unset($indice,$info);
										break;
								}
								break;
						}
						break;
					case 2:
						switch($operacion){
							case 1:
								switch($viz){
									case 4:
										//for($i=date('Y-m-d',strtotime($inicio));$i<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
										foreach($data_day as $i => $info){
											if($dayly_part[$i]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $dayly_part[$i].",";
						            		}


										}
										unset($i,$info);
										break;
									default:
										//for($i=date('Y-m-d',strtotime($inicio));$i<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
										foreach($data_day as $i => $info){
											if($data_day[$i][1]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][1].",";
						            		}

										}
										unset($i,$info);
										break;
								}
								break;
						}
						break;
					case 3:
						foreach($data_month as $indice => $info){
							if($info[1]==NULL){
								echo "0,";
							}else{
								echo $info[1].",";
							}


						}
						break;

				}

            ?>]
        }]
    });

});

</script>

<table class='t2' style='width: 100%; margin: auto; text-align:center'>
	<tr class='title'>
		<th>Periodo</th>
		<th>DOW</th>
		<th>Departamento</th>
		<th>Intervalo</th>
		<th>Visualizacion</th>
		<th>Operacion</th>
		<th rowspan=2><button class='button button_green_w' id='consultar'>Consultar</button></th>
	</tr>
	<tr class='pair'>
		<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='POST' id='params' name='params'>
		<td><input type='text' id='inicio' name='inicio' value='<?php echo $inicio; ?>'><input type='text' id='fin' name='fin' value='<?php echo $fin; ?>'><input type='hidden' value='0' id='flag' name='flag'></td>
		<td>L <input type='checkbox' name='l' id='l' <?php if(count($dow)== 0 || isset($_POST['l'])){echo "checked";} ?>>
			 M <input type='checkbox' name='m' id='m' <?php if(count($dow)== 0 || isset($_POST['m'])){echo "checked";} ?>>
			 X <input type='checkbox' name='x' id='x' <?php if(count($dow)== 0 || isset($_POST['x'])){echo "checked";} ?>>
			 J <input type='checkbox' name='j' id='j' <?php if(count($dow)== 0 || isset($_POST['j'])){echo "checked";} ?>>
			 V <input type='checkbox' name='v' id='v' <?php if(count($dow)== 0 || isset($_POST['v'])){echo "checked";} ?>>
			 S <input type='checkbox' name='s' id='s' <?php if(count($dow)== 0 || isset($_POST['s'])){echo "checked";} ?>>
			 D <input type='checkbox' name='d' id='d' <?php if(count($dow)== 0 || isset($_POST['d'])){echo "checked";} ?>></td>
		<td><select id='dep' name='dep' ><option value='' required>Selecciona...</option>
				<?php  $query="SELECT * FROM PCRCs WHERE inbound_calls=1 ORDER BY Departamento";
            if($result=Queries::query($query)){
              while($fila=$result->fetch_assoc()){
                echo "<option value='".$fila['id']."' ";
                if($fila['id']==$_POST['dep']){ echo "selected";}
                echo ">".$fila['Departamento']."</option>";
              }
            }
     ?>
		</select></td>
		<td><select id='tipo' name='tipo' required>
				<option value=''>Selecciona...</option>
				<option txt='hour' value='1' <?php if($tipo==1){echo "selected";} ?>>Por Hora</option>
				<option txt='day' value='2' <?php if($tipo==2){echo "selected";} ?>>Por Día</option>
				<option txt='month' value='3' <?php if($tipo==3){echo "selected";} ?>>Por Mes</option>
		</select></td>
		<td><select id='viz' name='viz' required>
				<option value=''>Selecciona...</option>
				<option value='3' <?php if($viz==3){echo "selected";} ?>>Comparative</option>
				<option value='2' <?php if($viz==2){echo "selected";} ?>>Normal</option>
				<option value='1' <?php if($viz==1){echo "selected";} ?>>Percent</option>
				<option value='4' <?php if($viz==4){echo "selected";} ?>>Participacion</option>
			</select></td>
		<td><select id='operacion' name='operacion' required>
				<option value=''>Selecciona...</option>
				<option class='pres_opt hour' value='2' <?php if($operacion==2){echo "selected";} ?>>Promedio</option>
				<option class='pres_opt month day hour' value='1' <?php if($operacion==1){echo "selected";} ?>>Suma</option>
			</select></td>
		</form>
	</tr>
</table>


<div id='container' style='width: 100%; height: 800px; margin: auto'></div>

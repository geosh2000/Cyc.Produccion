<?php

include_once("../modules/modules.php");

initSettings::start(true,'tablas_f');
initSettings::printTitle('Llamadas MP');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

//Declare Vars
if(isset($_POST['inicio'])){$inicio=date('Y-m-d', strtotime($_POST['inicio']));}else{$inicio=date('Y-m-d', strtotime('-7 days'));}
if(isset($_POST['fin'])){$fin=date('Y-m-d', strtotime($_POST['fin']));}else{$fin=date('Y-m-d');}

$dep=35;
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
			$q_dow=" AND WEEKDAY(a.Fecha) IN (";
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
		$query="SELECT
					a.Fecha, CONCAT(IF(HOUR(Hora)<10,'0',''),HOUR(Hora),':',IF(MINUTE(Hora)<30,'00','30')) as HoraGroup, COUNT(IF(Canal='MP MX',ac_id,NULL)) as Main, COUNT(IF(Canal='MP MX Movil',ac_id,NULL)) as Movil,
					 COUNT(IF(Canal='MP MX Promo',ac_id,NULL)) as Promo, COUNT(IF(Canal NOT IN ('MP MX','MP MX Movil','MP MX Promo','MP MX Promo Aereo', 'PriceLab Puebla') OR Canal IS NULL,ac_id,NULL)) as Xfered,
					 COUNT(IF(Canal='MP MX Promo Aereo',ac_id,NULL)) as PromoAereo, COUNT(IF(Canal='PriceLab Puebla',ac_id,NULL)) as PriceLabPuebla, COUNT(ac_id) as Llamadas
					FROM
					(SELECT a.*, Canal, Skill FROM t_Answered_Calls a
					LEFT JOIN Cola_Skill b ON a.Cola=b.Cola LEFT JOIN Dids c ON a.DNIS=c.DID
					WHERE a.Fecha BETWEEN '$inicio' AND '$fin' $q_dow
					HAVING Skill=$dep) a
					GROUP BY a.Fecha, HoraGroup, Answered
					ORDER BY a.Fecha, HoraGroup, Answered";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$data_hour[$fila['Fecha']][$fila['HoraGroup']]['Main']=$fila['Main'];
			$data_hour[$fila['Fecha']][$fila['HoraGroup']]['Movil']=$fila['Movil'];
			$data_hour[$fila['Fecha']][$fila['HoraGroup']]['Promo']=$fila['Promo'];
			$data_hour[$fila['Fecha']][$fila['HoraGroup']]['PromoAereo']=$fila['PromoAereo'];
			$data_hour[$fila['Fecha']][$fila['HoraGroup']]['PriceLabPuebla']=$fila['PriceLabPuebla'];
			$data_hour[$fila['Fecha']][$fila['HoraGroup']]['Xfered']=$fila['Xfered'];
		}
	}else{
		echo "ERROR -> ".$connectdb->error." ON $query";
	}


//Hourly
	foreach($data_hour as $fecha => $info){
		foreach($info as $hour => $info2){
			$data_hourly[$hour]['Main']+=$info2['Main'];
			$data_hourly[$hour]['Movil']+=$info2['Movil'];
			$data_hourly[$hour]['Promo']+=$info2['Promo'];
			$data_hourly[$hour]['PromoAereo']+=$info2['PromoAereo'];
			$data_hourly[$hour]['PriceLabPuebla']+=$info2['PriceLabPuebla'];
			$data_hourly[$hour]['Xfered']+=$info2['Xfered'];
			$data_hourly_avg[$hour]['Main'][]=$info2['Main'];
			$data_hourly_avg[$hour]['Movil'][]=$info2['Movil'];
			$data_hourly_avg[$hour]['Promo'][]=$info2['Promo'];
			$data_hourly_avg[$hour]['PromoAereo'][]=$info2['PromoAereo'];
			$data_hourly_avg[$hour]['PriceLabPuebla'][]=$info2['PriceLabPuebla'];
			$data_hourly_avg[$hour]['Xfered'][]=$info2['Xfered'];
			$total_hourly[$hour]+=($info2['Main']+$info2['Movil']+$info2['Promo']+$info2['PromoAereo']+$info2['PriceLabPuebla']+$info2['Xfered']);
		}
		unset($hour,$info2);

	}
	unset($fecha,$info);



//Hourly_AVG
	foreach($data_hourly as $hora => $info){
		$avg_hourly[$hora]['Main']=array_sum($data_hourly_avg[$hora]['Main'])/count($data_hourly_avg[$hora]['Main']);
		$avg_hourly[$hora]['Movil']=array_sum($data_hourly_avg[$hora]['Movil'])/count($data_hourly_avg[$hora]['Movil']);
		$avg_hourly[$hora]['Promo']=array_sum($data_hourly_avg[$hora]['Promo'])/count($data_hourly_avg[$hora]['Promo']);
		$avg_hourly[$hora]['PromoAereo']=array_sum($data_hourly_avg[$hora][''])/count($data_hourly_avg[$hora]['PromoAereo']);
		$avg_hourly[$hora]['PriceLabPuebla']=array_sum($data_hourly_avg[$hora][''])/count($data_hourly_avg[$hora]['PriceLabPuebla']);
		$avg_hourly[$hora]['Xfered']=array_sum($data_hourly_avg[$hora]['Xfered'])/count($data_hourly_avg[$hora]['Xfered']);
		$avg_hourly_all[$hora]=(array_sum($data_hourly_avg[$hora]['Main'])+array_sum($data_hourly_avg[$hora]['Movil'])+array_sum($data_hourly_avg[$hora]['PriceLabPuebla'])+array_sum($data_hourly_avg[$hora]['Promo'])+array_sum($data_hourly_avg[$hora]['PromoAereo'])+array_sum($data_hourly_avg[$hora]['Xfered']))/(count($data_hourly_avg[$hora]['Main'])+count($data_hourly_avg[$hora]['Movil'])+count($data_hourly_avg[$hora]['Promo'])+count($data_hourly_avg[$hora]['Xfered']));
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
			$data_day[$fecha]['Main']+=$info2['Main'];
			$data_day[$fecha]['Movil']+=$info2['Movil'];
			$data_day[$fecha]['Promo']+=$info2['Promo'];
			$data_day[$fecha]['PromoAereo']+=$info2['PromoAereo'];
			$data_day[$fecha]['PriceLabPuebla']+=$info2['PriceLabPuebla'];
			$data_day[$fecha]['Xfered']+=$info2['Xfered'];
			$data_day_avg[$fecha]['Main'][]=$info2['Main'];
			$data_day_avg[$fecha]['Movil'][]=$info2['Movil'];
			$data_day_avg[$fecha]['Promo'][]=$info2['Promo'];
			$data_day_avg[$fecha]['PromoAereo'][]=$info2['PromoAereo'];
			$data_day_avg[$fecha]['PriceLabPuebla'][]=$info2['PriceLabPuebla'];
			$data_day_avg[$fecha]['Xfered'][]=$info2['Xfered'];
			$total_dayly[$fecha]+=($info2['Main']+$info2['Movil']+$info2['Promo']+$info2['PromoAereo']+$info2['PriceLabPuebla']+$info2['Xfered']);
		}
		unset($hour,$info2);
	}
	unset($fecha,$info);

//Daily_AVG
	foreach($data_hour as $fecha => $info){
		$avg_dayly[$fecha]['Main']=array_sum($data_day_avg[$fecha]['Main'])/count($data_day_avg[$fecha]['Main']);
		$avg_dayly[$fecha]['Movil']=array_sum($data_day_avg[$fecha]['Movil'])/count($data_day_avg[$fecha]['Movil']);
		$avg_dayly[$fecha]['Promo']=array_sum($data_day_avg[$fecha]['Promo'])/count($data_day_avg[$fecha]['Promo']);
		$avg_dayly[$fecha]['PromoAereo']=array_sum($data_day_avg[$fecha]['PromoAereo'])/count($data_day_avg[$fecha]['PromoAereo']);
		$avg_dayly[$fecha]['PriceLabPuebla']=array_sum($data_day_avg[$fecha]['PriceLabPuebla'])/count($data_day_avg[$fecha]['PriceLabPuebla']);
		$avg_dayly[$fecha]['Xfered']=array_sum($data_day_avg[$fecha]['Xfered'])/count($data_day_avg[$fecha]['Xfered']);
	}
	unset($fecha,$info);

//Daily_Part
	foreach($total_dayly as $fecha => $info){
		$dayly_part[$fecha]=$info/array_sum($total_dayly);
	}
	unset($fecha,$info);

//Monthly
	foreach($data_day as $fecha => $info){
		$data_month[date('M', strtotime($fecha))]['Main']+=$info['Main'];
		$data_month[date('M', strtotime($fecha))]['Movil']+=$info['Movil'];
		$data_month[date('M', strtotime($fecha))]['Promo']+=$info['Promo'];
		$data_month[date('M', strtotime($fecha))]['PromoAereo']+=$info['PromoAereo'];
		$data_month[date('M', strtotime($fecha))]['PriceLabPuebla']+=$info['PriceLabPuebla'];
		$data_month[date('M', strtotime($fecha))]['Xfered']+=$info['Xfered'];
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

	departamento="Ventas MP";

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
        	<?php $variable="Xfered"; ?>
            name: '<?php echo $variable; ?>',
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
						            		if($data_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][$variable].",";
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
						            		if($avg_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][$variable].",";
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
											if($data_day[$i][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][$variable].",";
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
							if($info[$variable]==NULL){
								echo "0,";
							}else{
								echo $info[$variable].",";
							}


						}
						break;

				}?>]
        	}, {
        	<?php $variable="Promo"; ?>
            name: '<?php echo $variable; ?>',
            color: '#002699',
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
						            		if($data_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][$variable].",";
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
						            		if($avg_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][$variable].",";
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
											if($data_day[$i][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][$variable].",";
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
							if($info[$variable]==NULL){
								echo "0,";
							}else{
								echo $info[$variable].",";
							}


						}
						break;

				}?>]
        	}, {

        	<?php $variable="PriceLabPuebla"; ?>
            name: '<?php echo $variable; ?>',
            color: '#ffaa5b',
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
						            		if($data_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][$variable].",";
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
						            		if($avg_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][$variable].",";
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
											if($data_day[$i][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][$variable].",";
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
							if($info[$variable]==NULL){
								echo "0,";
							}else{
								echo $info[$variable].",";
							}


						}
						break;

				}?>]
        	}, {

        	<?php $variable="PromoAereo"; ?>
            name: '<?php echo $variable; ?>',
            color: '#aedb60',
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
						            		if($data_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][$variable].",";
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
						            		if($avg_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][$variable].",";
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
											if($data_day[$i][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][$variable].",";
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
							if($info[$variable]==NULL){
								echo "0,";
							}else{
								echo $info[$variable].",";
							}


						}
						break;

				}?>]
        	}, {

        	<?php $variable="Movil"; ?>
            name: '<?php echo $variable; ?>',
            color: '#bb99ff',
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
						            		if($data_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][$variable].",";
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
						            		if($avg_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][$variable].",";
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
											if($data_day[$i][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][$variable].",";
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
							if($info[$variable]==NULL){
								echo "0,";
							}else{
								echo $info[$variable].",";
							}


						}
						break;

				}?>]
        	}, {
        	<?php $variable="Main"; ?>
            name: '<?php echo $variable; ?>',
            color: '#cc0052',
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
						            		if($data_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_hourly[$info][$variable].",";
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
						            		if($avg_hourly[$info][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $avg_hourly[$info][$variable].",";
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
											if($data_day[$i][$variable]==NULL){
						            			echo "0,";
						            		}else{
						            			echo $data_day[$i][$variable].",";
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
							if($info[$variable]==NULL){
								echo "0,";
							}else{
								echo $info[$variable].",";
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

<?php
$connectdb->close();
?>

<div id='container' style='width: 100%; height: 800px; margin: auto'></div>

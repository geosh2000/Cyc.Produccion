<?php
include_once('../modules/modules.php');
include_once("../common/erlangC.php");

initSettings::start(true);
initSettings::printTitle('Calculadora Erlang');

$skill=$_POST['skill'];

$tbody="<td>Total de Llamadas</td><td><input type='text' name='total' id='total' value='".$_POST['total']."' required></td>"
		."<td>AHT</td><td><input type='text' name='aht' id='aht' value='".$_POST['aht']."' required></td>"
		."<td>SLR</td><td><input type='text' name='slr' id='slr' value='".$_POST['slr']."' required></td>"
		."<td>TAT</td><td><input type='text' name='tat' id='tat' value='".$_POST['tat']."' required></td>"
		."<td>Reductores</td><td><input type='text' name='reductores' id='reductores' value='".$_POST['reductores']."' required></td>"
		."<td>Programa</td><td><select name='skill' required><option value=''>Selecciona...</option>";
$query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		if($skill==$fila['id']){$selected="selected";}else{$selected="";}
		$tbody.= "<option value='".$fila['id']."' $selected>".$fila['Departamento']."</option>";
	}
}
$tbody.="</select></td>";
Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'submit', 'Consultar', $tbody);

IF(isset($_POST['tope'])){
	$tope=$_POST['tope'];
}else{
	$tope=1000;
}

if(isset($_POST['skill'])){
	$query="SELECT * FROM forecast_participacion WHERE skill=35 AND Fecha='2016-10-19'";
	if($result=Queries::query($query)){
		while($fila=$result->fetch_assoc()){

			$data['forecast'][$fila['hora']]=$fila['participacion']*$_POST['total'];

			if(	$data['forecast'][$fila['hora']]/1800*$_POST['aht']==0){
				$data['erlang'][$fila['hora']]=0;
			}else{
				$data['erlang'][$fila['hora']]=intval(agentno(	$data['forecast'][$fila['hora']]/1800*$_POST['aht'], $_POST['tat'],$_POST['aht'],$_POST['slr'])/(1-$_POST['reductores']));
			}
		}
	}





			for($y=0;$y<48;$y++){
				if($y<4){
					$index=$y+48;
				}else{
					$index=$y;
				}

				if($data['forecast'][$y]==NULL){
					$td[$index]=0;
				}else{
					if($data['erlang'][$y]==NULL || $data['erlang'][$y]==""){
						$td[$index]=0;
					}else{
						$td[$index]=$data['erlang'][$y];
					}
				}
			}

$needed=$td;
$req=$needed;



function horario($x,$op,$esquema){
	global $extra;
	switch($op){
		case '+':
			switch($esquema){
				case 8:
					if($x>32){
						$sum=13+$extra;
					}elseif($x>29){
						$sum=14+$extra;
					}else{
						$sum=15+$extra;
					}

					return $x+$sum;

					break;
				default:

					return $x+$esquema;
					break;
			}
			break;
		case '-':
			switch($esquema){
				case 8:
					if($x>45){
						$sum=13+$extra;
					}elseif($x>43){
						$sum=14+$extra;
					}else{
						$sum=15+$extra;
					}

					return $x-$sum;

					break;
				default:

					return $x-$esquema;
					break;
			}
			break;
	}
}

function sumToNeeded($inicio,$esquema){
	global $needed, $req;
	for($i=$inicio;$i<=intval(horario($inicio,'+',$esquema));$i++){
		$req[$i]-=1;
	}
}

$x=38;
$y=51;
$index=0;
for($i=12;$i<=intval($x);$w=1){

	if(count($horario)>=$tope){break;}

	if($req[$i]>0){

		if($i==33 || $i==26){
			$horario[]=$i-1;
		}else{
			$horario[]=$i;
		}

		sumToNeeded($i,8);
		if($req[$i]==0){
			$i++;
		}
	}else{
		if($i<=28){
			$i++;
		}
	}

	if(count($horario)>=$tope){break;}

	if($req[$y]>0){

		if(horario($y,'-',8)==33 || horario($y,'-',8)==26){
			$horario[]=horario($y,'-',8)-1;
		}else{
			$horario[]=horario($y,'-',8);;
		}

		sumToNeeded(horario($y,'-',8),8);
		if($req[$y]==0){
			$y--;
		}
	}else{
		$y--;
	}

	$x=horario($y,'-',8);

asort($horario);



}

for($i=0;$i<48;$i++){
	$exist[$i]=0;
	foreach($horario as $index => $info){
		for($x=0;$x<16;$x++){
			if($i==($info+$x)){
				$exist[$i]++;
				//continue;
			}
		}
	}
}
?>
<br>
<p style='text-align: center; font-size: 60px; font-weight: normal; color: black'>Total de Asesores Necesarios</p>
<p style='text-align: center; font-size: 72px; font-weight: bold; color: red'><?php echo count($horario); ?></p>


<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="/js/export-csv/export-csv.js"></script>
<script>
function drawChart(){
		needed=<?php print json_encode($data['erlang'],JSON_PRETTY_PRINT); ?>;
		programmed=<?php print json_encode($exist,JSON_PRETTY_PRINT); ?>;

		$('#container').highcharts({
	        chart: {
	            type: 'column',
	            animation: false
	        },
	        title: {
	            text: "Programacion"
	        },
	        xAxis: {
	            categories: [<?php
	            	for($i=0;$i<48;$i++){
	            		$hora=$i/2;
	            		echo "'$hora',";
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
	        	name: 'Needed',
	        	type: 'line',
	            color: '#009900',
	            data: needed,
	            animation: false
	        	}, {
	        	name: 'Programmed',
	        	type: 'column',
	            color: '#002699',
	            data: programmed,
	            animation: false
	        }]
	    });
	}

$(function(){
	drawChart();
});

</script>
<br>
<div id='container' style='height:100%'></div>

<br>
<p style='text-align: center; font-size: 60px; font-weight: normal; color: black'>Sugerencia de Horarios</p>
<table class='t2' style='text-align:center; margin: auto;'>
<?php

	$i=1;
	foreach($horario as $index => $info){
		$horario_ok=intval($info/2);
		if($info % 2 != 0){
			$horario_ok.=":30";
		}else{
			$horario_ok.=":00";
		}

		echo "<tr><td>$i</td><td>$horario_ok</td></tr>\n";
		$i++;
	}



}
?>
</table>

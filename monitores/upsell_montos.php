<?php
//header('Location: http://wfm.pricetravel.com.mx/monitores/upsell_montos.php');
include("../connectDB.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');


//Declare Type of Output
$tipo=$_GET['tipo'];

//Function Declares

    //Data to Arrays
    function getData($input,$varname){
        global $block;

        preg_match_all("/<tr>(.*?)<\/tr>/s", $input, $resume);

        foreach($resume[1] as $index => $data){
            if($index==0){continue;}
            preg_match_all("/<td(.*?)<\/td>/s", $data, $tmp);
            foreach($tmp[1] as $ind => $info){
                $name=substr($info,strpos($info, "'")+1,strpos($info, ">")-strpos($info, "'")-2);
                $block[$varname][$index][$name]=substr($info,strpos($info, ">")+1,100);
                //echo "index // $ind // $name // ".$resumen[$index][$name]."<br>";
            }
            unset($ind,$info,$tmp);
        }
    }
	
	//explode sla info
	function getVarOcc($variable,$input){
		global $blockOcc;
		
		preg_match_all("/-$variable- (.*?) -$variable-/s", $input, $v_output);
		foreach($v_output[1] as $index => $info){
			if($index<=1){continue;}
			$blockOcc[$index][$variable]=$info;	
		}
		
	}
	
	//Build BlockSLA
	function getOccBlock($input){
		getVarOcc("Asesor",$input);
		getVarOcc("Sesion",$input);
		getVarOcc("Pausa",$input);
		getVarOcc("Call",$input);		
	}

//Get Last Update
$query="SELECT MAX(Last_Update) as Last_Update FROM d_Locs";
$result=mysql_query($query);
$lu=mysql_result($result,0,'Last_Update');

//Get Montos
$query="SELECT `N Corto`, COUNT(Localizador) as Locs, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto "
		."FROM d_Locs a LEFT JOIN Asesores b ON a.asesor=b.id "
		."WHERE `id Departamento`=5 AND Fecha=CURDATE() AND Venta!=0 "
		."GROUP BY asesor "
		."ORDER BY `Monto` DESC";
$result=mysql_query($query);
$num=mysql_numrows($result);
for($i=0;$i<$num;$i++){
	$datos[mysql_result($result,$i,'N Corto')]['Monto']=mysql_result($result,$i,'Monto');
	//$datos[mysql_result($result,$i,'N Corto')]['Locs']=mysql_result($result,$i,'Locs');
}

/*
echo "<pre>";
print_r($blockOcc);
echo "</pre>";
*/
?>

<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script>
	
$(function () {
	
	Highcharts.setOptions({
	    lang: {
	        decimalPoint: '.',
	        thousandsSep: ','
	    }
	});
	
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Montos por Asesor - Upsell (<?php echo $lu; ?>)'
        },
        xAxis: {
            categories: [
			<?php
        		foreach($datos as $index => $info){
        			echo "'$index'";
					$index_flag=$index;
					if(end($datos)!=$info){
						echo ",";
					}
				}
				unset($index,$info);
        		
        ?>
			],
			labels: {
				style: {
					fontSize: '25px'
				}	
			}
        },
        yAxis: [{
            min: 0,
            title: {
                text: 'Monto'
            }
        }],
        tooltip: {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            shared: true
        },
        plotOptions: {
            column: {
                dataLabels: {
                	enabled: true,
                	color: '#e292a6',
                	//format: '${y}',
                	pointFormat: "$ {point.y:,.2f}",
                	style: {
                		textShadow: '0 0 3px #215086',
                		fontSize: '30px'
                	}	
            	}
            }
        },
        series: [
        <?php
        		foreach($datos[$index_flag] as $type => $data){
        			switch($type){
						case 'Monto':
	        				$color="#215086";
							$yaxis=0;
							break;
						case 'Tiempo Disponible':
	        				$color="#00cc66";
							break;
						case 'Locs':
	        				$color="#e292a6";
							$yaxis=1;
							break;
        			}
        			
        				echo "{\n\t";
						echo "name: '$type',\n\t";
						echo "color: '$color',\n\t";
						echo "yAxis: $yaxis,\n\t";
						echo "data: [";
	        			foreach($datos as $index => $info){
			        		echo number_format($info[$type],2,".","");
							if(end($datos)!=$info){
								echo ",";
							}
						}
						echo "]\n\t";
						echo "}";
						if($type!='Locs'){
							echo ",";
						}	
        			
        		}
        		
        ?>
        ]
    });
});	
	
</script>

<div id='container' style='width: 100%; height: 1020px; margin: auto'></div>
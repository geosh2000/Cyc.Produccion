<?php
header('Location: http://wfm.pricetravel.com.mx/monitores/upsell_occupation.php');
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

//Get Occupancy
$query="SELECT * FROM mon_live_calls_row WHERE tipo=5 ORDER BY Last_Update DESC LIMIT 1";
$result=mysql_query($query);
$lu=mysql_result($result,0,'Last_Update');
$txt=str_replace(" &nbsp;","",utf8_encode(mysql_result($result,0,'live')));
$txt=str_replace("</td>","</td>\n",$txt);

//Explode Occ
getOccBlock($txt);
if(count($blockOcc)>0){
	foreach($blockOcc as $index => $data){
		//Sesion
		if(strlen($data['Sesion'])>=7){
			$blockOcc[$index]['Sesion_seg']=intval(substr($data['Sesion'],-8,2))*60+intval(substr($data['Sesion'],-5,2))+intval(substr($data['Sesion'],-2,2))/60;	
		}else{
			$blockOcc[$index]['Sesion_seg']=intval(substr($data['Sesion'],-5,2))+intval(substr($data['Sesion'],-2,2))/60;
		}
		
		//Pausa
		if(strlen($data['Pausa'])>=7){
			$blockOcc[$index]['Tiempo en Pausa']=intval(substr($data['Pausa'],-8,2))*60+intval(substr($data['Pausa'],-5,2))+intval(substr($data['Pausa'],-2,2))/60;	
		}else{
			$blockOcc[$index]['Tiempo en Pausa']=intval(substr($data['Pausa'],-5,2))+intval(substr($data['Pausa'],-2,2))/60;
		}
		
		//Call
		if(strlen($data['Call'])>=7){
			$blockOcc[$index]['Tiempo en Llamada']=intval(substr($data['Call'],-8,2))*60+intval(substr($data['Call'],-5,2))+intval(substr($data['Call'],-2,2))/60;	
		}else{
			$blockOcc[$index]['Tiempo en Llamada']=intval(substr($data['Call'],-5,2))+intval(substr($data['Call'],-2,2))/60;
		}
		
		//Tiempo Disponible_seg		
		$blockOcc[$index]['Tiempo Disponible']=$blockOcc[$index]['Sesion_seg']-$blockOcc[$index]['Tiempo en Pausa']-$blockOcc[$index]['Tiempo en Llamada'];
		
		//Occupancy
		$blockOcc[$index]['Call_p']=$blockOcc[$index]['Tiempo en Llamada']/$blockOcc[$index]['Sesion_seg']*100;
		
		//Utilization
		$blockOcc[$index]['Pause_p']=$blockOcc[$index]['Tiempo en Pausa']/$blockOcc[$index]['Sesion_seg']*100;
		
		//Tiempo Disponible		
		$blockOcc[$index]['Tiempo Disponible_p']=100-$blockOcc[$index]['Pause_p']-$blockOcc[$index]['Call_p'];
	}
	unset($index,$data);	
}

foreach($blockOcc as $index => $info){
	$blockOccOK[$info['Asesor']]=$info;
}
unset($index,$info);

asort($blockOccOK);

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
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Porcentaje de tiempos por Asesor - Upsell (<?php echo $lu; ?>)'
        },
        xAxis: {
            categories: [
			<?php
        		foreach($blockOccOK as $index => $info){
        			echo "'".$info['Asesor']."'";
					if(end($blockOccOK)!=$info){
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
        yAxis: {
            min: 0,
            title: {
                text: 'Porcentaje'
            }
        },
        tooltip: {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            shared: true
        },
        plotOptions: {
            column: {
                stacking: 'percent',
                dataLabels: {
                	enabled: true,
                	color: 'white',
                	format: '{y} min',
                	rotation: 270,
                	style: {
                		textShadow: '0 0 3px black',
                		fontSize: '30px'
                	}	
            	}
            }
        },
        series: [
        <?php
        		$datos=array('Tiempo Disponible'=>0,'Tiempo en Pausa'=>0,'Tiempo en Llamada'=>0);
        		foreach($datos as $type => $data){
        			switch($type){
						case 'Tiempo en Llamada':
	        				$color="#215086";
							break;
						case 'Tiempo Disponible':
	        				$color="#00cc66";
							break;
						case 'Tiempo en Pausa':
	        				$color="#e292a6";
							break;
        			}
        			if($type=='Tiempo en Pausa' || $type=='Tiempo en Llamada' || $type=='Tiempo Disponible'){
        				echo "{\n\t";
						echo "name: '$type',\n\t";
						echo "color: '$color',\n\t";
						echo "data: [";
	        			foreach($blockOccOK as $index => $info){
			        		echo number_format($info[$type],2,".","");
							if(end($blockOccOK)!=$info){
								echo ",";
							}
						}
						echo "]\n\t";
						echo "}";
						if($type!='Tiempo en Llamada'){
							echo ",";
						}	
        			}
        		}
        		
        ?>
        ]
    });
});	
	
</script>

<div id='container' style='width: 100%; height: 1020px; margin: auto'></div>
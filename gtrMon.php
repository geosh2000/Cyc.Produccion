<?php
include("connectDB.php");
include("DBCallsHoraV.php");
date_default_timezone_set('America/Bogota');
$height="87%";
$width="100%";
$reloadTime=10000;
$thisMonth=intval(date('m'));
$thisDay=intval(date('d'));
$thisYear=intval(date('Y'));
$showSkill=$_POST['skill'];
if($showSkill==NULL){$showSkill="Ventas";}
switch ($showSkill){
	case "Ventas":
		$opt_V="selected";
		$optSLA="&slap=80&slat=20";
		break;
	case "Servicio a Cliente":
		$opt_SC="selected";
		$optSLA="&slap=70&slat=30";
		break;
	case "Trafico MP":
		$opt_TMP="selected";
		$optSLA="&slap=70&slat=30";
		break;
	case "Trafico MT":
		$optSLA="&slap=70&slat=30";
		$opt_TMT="selected";
		break;
	case "Soporte Agencias":
		$optSLA="&slap=70&slat=30";
		$opt_SA="selected";
		break;
}
$Pstart=$_POST['start'];
if ($Pstart==NULL){
	$txtMonth=date('F');
	$txtDay=date('d');
	$Pstart="$txtDay $txtMonth $thisYear";
}
$Cdia=date("d", strtotime($Pstart));
$Cmes=date("m", strtotime($Pstart));
$Canio=date("Y", strtotime($Pstart));

$today=time();

$CalValue=floor(($today-strtotime($Pstart))/(60*60*24))*(-1);

$CFecha="?d=$Cdia&m=$Cmes&y=$Canio&skill=$showSkill";
$CTitle="Fecha: $Cdia-$Cmes-$Canio";

?>

<script>


</script>
<script>

function updateStatus() {
    
     
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("update").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","updatedCalls.php",true);
        xmlhttp.send();
        
        
        
        
    
}

</script>


  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
 
  
<script>
google.load('visualization', '1', {packages: ['corechart', 'line']});
google.setOnLoadCallback(Volumen);
google.setOnLoadCallback(AHT);
google.setOnLoadCallback(SLA);
google.setOnLoadCallback(drawLineColors4);

function Volumen() {
      

	//options para "no material"
	
	      var options = {
	        title:'Volumen: <?phpecho $Pstart;?>',
	        axes: {
	                y: {
	                Llamadas: {label: 'Llamadas'},
	                Prec: {label: 'Precision %', format: 'percent', maxValue: 10}        
	                },
	                x: {0: {label: 'Horas'}}
	        },
	        chartArea: {width: '80%', height: '70%'},
	        hAxis: {
	          title: 'Hora',
	          gridlines: {count:48}
	        },
	        crosshair: { trigger: 'both' },
	        vAxis: { 
	          0: {title: 'Llamadas'},
	          1: {format: 'percent'},
	          1: {title: 'Precision %'},
	        },
	        colors: ['#ff0066',  '#669999', '#ffaa00', '#00b300', '#00b300'],
	        lineWidth: 4,
	        animation: {duration: 2000, startup: 'true', easing: 'inAndOut'},
	        
	       //No Material       
	       series: {
	                0: {targetAxisIndex: 0, pointSize: 2},
	                1: {lineDashStyle: [4, 4], targetAxisIndex: 0},
	                2: {lineDashStyle: [2, 2], targetAxisIndex: 1},
	                3: {lineDashStyle: [4, 4], targetAxisIndex: 1, lineWidth: 2},
	                4: {lineDashStyle: [4, 4], targetAxisIndex: 1, lineWidth: 2}
	
	        }
	      };
	
	
	
	
	      //Draw para "no material"
	      var chart = new google.visualization.LineChart(document.getElementById('Volumen'));
	      
	      function drawChart(){
	      var jsonData = $.ajax({
	          url: "getData_gtrMonVol.php<?php echo $CFecha; ?>",
	          dataType: "json",
	         
	          async: false
	          }).responseText;
	          
	      var data = new google.visualization.DataTable(jsonData);
	
	      chart.draw(data);
	      }
	      drawChart();
	      var total =<?phpecho $reloadTime;?>;
		var myVar = setInterval(function(){ myTimer() }, 1000);
	
	function myTimer() {
		if (total == 1000){
		
		drawChart();
		updateStatus();
		total=<?phpecho $reloadTime;?>;}
	   	total= total-1000;
	   	document.getElementById("demo").innerHTML = "        (Reload in " + total/1000 + " sec.)";
	}
	
	updateStatus(); 
      
}
    
function AHT() {
      

	//options para "no material"
	
	      var options = {
	        title:'AHT: <?phpecho $Pstart;?>',
	        axes: {
	                y: {
	                Llamadas: {label: 'AHT'},
	                Prec: {label: 'Precision %', format: 'percent', maxValue: 10}        
	                },
	                x: {0: {label: 'Horas'}}
	        },
	        
	        chartArea: {width: '80%', height: '70%'},
	        hAxis: {
	          title: 'Hora',
	          gridlines: {count:48},
	          minValue: 0
	        },
	        crosshair: { trigger: 'both' },
	        vAxis: { 
	          0: {title: 'Llamadas'},
	          1: {format: 'percent'},
	          1: {title: 'Precision %'},
	        },
	        colors: ['#0000ff', '#00b300', '#00b300'],
	        lineWidth: 4,
	        animation: {duration: 2000, startup: 'true', easing: 'inAndOut'},
	        
	       //No Material       
	       series: {
	                0: {targetAxisIndex: 0, pointSize: 2},
	                1: {lineDashStyle: [4, 4], targetAxisIndex: 0, lineWidth: 2},
	                2: {lineDashStyle: [4, 4], targetAxisIndex: 0, lineWidth: 2}
	                
	
	        }
	      };
	
	
	
	
	      //Draw para "no material"
	      var chart = new google.visualization.LineChart(document.getElementById('AHT'));
	      
	      function drawChart(){
	      var jsonData = $.ajax({
	          url: "getData_gtrMonAht.php<?php echo $CFecha; ?>",
	          dataType: "json",
	         
	          async: false
	          }).responseText;
	          
	      var data = new google.visualization.DataTable(jsonData);
	
	      chart.draw(data, options);
	      }
	      drawChart();
	      var total =<?phpecho $reloadTime;?>;
		var myVar = setInterval(function(){ myTimer() }, 1000);
	
	function myTimer() {
		if (total == 1000){
		
		drawChart();
		updateStatus();
		total=<?phpecho $reloadTime;?>;}
	   	total= total-1000;
	    	
	}
	
	updateStatus(); 
      
}
    
function SLA() {
      

//options para "no material"

      var options = {
	        title:'SLA: <?phpecho $Pstart;?>',
	        chartArea: {width: '80%', height: '70%'},
	        vAxis: {title:'SLA'},
	        hAxis: {
	          title: 'Hora',
	          gridlines: {count:48},
	          minValue: 0
	        },
	        colors: ['#ffdd99', '#00b300', '#00b300'],
	        seriesType: 'bars',
	        
	        animation: {duration: 2000, startup: 'true', easing: 'inAndOut'},
	        
	       //No Material       
	       series: {
	                1: {type: 'bars', color: '#ffdd99'},
	                1: {type: 'line', lineDashStyle: [4, 4], lineWidth: 2},
	                2: {type: 'line', lineDashStyle: [4, 4], lineWidth: 2}
	                
	
	        }
	      };

      //Draw para "no material"
      var chart = new google.visualization.ComboChart(document.getElementById('SLA'));
      
      function drawChart(){
      var jsonData = $.ajax({
          url: "getData_gtrMonSla.php<?php echo $CFecha; ?><?php echo $optSLA; ?>",
          dataType: "json",
         
          async: false
          }).responseText;
          
      var data = new google.visualization.DataTable(jsonData);

      chart.draw(data, options);
      }
      drawChart();
      var total =<?phpecho $reloadTime;?>;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
	if (total == 1000){
	
	drawChart();
	updateStatus();
	total=<?phpecho $reloadTime;?>;}
   total= total-1000;
    
}

updateStatus(); 
      
    }
    
function drawLineColors4() {
      

//options para "no material"

      var options = {
        title:'<?phpecho $Pstart;?>',
        axes: {
                y: {
                Llamadas: {label: 'Llamadas'},
                Prec: {label: 'Precision %', format: 'percent', maxValue: 10}        
                },
                x: {0: {label: 'Horas'}}
        },
        
        chartArea: {width: '80%', height: '70%'},
        hAxis: {
          title: 'Hora',
          gridlines: {count:48}
        },
        crosshair: { trigger: 'both' },
        vAxis: { 
          0: {title: 'Llamadas'},
          1: {format: 'percent'},
          1: {title: 'Precision %'},
        },
        colors: ['#ff0066', '#0000ff', '#669999', '#ff0066', '#ffaa00', '#00b300', '#00b300'],
        lineWidth: 4,
        animation: {duration: 2000, startup: 'true', easing: 'inAndOut'},
        
       //No Material       
       series: {
                0: {targetAxisIndex: 0, pointSize: 2},
                1: {lineDashStyle: [1, 1], targetAxisIndex: 0},
                2: {lineDashStyle: [1, 1], targetAxisIndex: 0},
                3: {lineDashStyle: [4, 4], targetAxisIndex: 0},
                4: {lineDashStyle: [2, 2], targetAxisIndex: 1},
                5: {lineDashStyle: [4, 4], targetAxisIndex: 1, lineWidth: 2},
                6: {lineDashStyle: [4, 4], targetAxisIndex: 1, lineWidth: 2}

        }
      };




      //Draw para "no material"
      var chart = new google.visualization.LineChart(document.getElementById('dual_x_div4'));
      
      function drawChart(){
      var jsonData = $.ajax({
          url: "getData_chartCalls2.php<?php echo $CFecha; ?>",
          dataType: "json",
         
          async: false
          }).responseText;
          
      var data = new google.visualization.DataTable(jsonData);

      chart.draw(data, options);
      }
      drawChart();
      var total =<?phpecho $reloadTime;?>;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
	if (total == 1000){
	
	drawChart();
	updateStatus();
	total=<?phpecho $reloadTime;?>;}
   total= total-1000;
    
}

updateStatus(); 
      
    }
</script>


<body bgcolor=#000000>

<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:to;text-align:left;}
.tg .tg-yw4l1{vertical-align:to; color:#ffffff;text-align:center;font-size:46px;}
.tg .tg-lblp{font-weight:bold;font-size:22px;background-color:#2245a9;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-lblp1{font-weight:bold;font-size:22px;background-color:#FF681C;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-lblp2{font-weight:bold;font-size:22px;background-color:#FF0000;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-ouin{font-weight:bold;font-size:24px;background-color:#000000;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-ouin2{font-size:20px;background-color:#000000;color:#ffffff;text-align:center;vertical-align:center}
.tg .tg-date{font-weight:bold;font-size:20px;background-color:#000000;color:#ffffff;text-align:left;vertical-align:center}
}
</style>


<table class="tg" style="width:<?php echo $width; ?>">
<tr>
	<th class="tg-date" rowspan="2" colspan="5" style="width:20%"><?phpinclude("datePickerHist.php");?></th>
    <th class="tg-ouin" colspan="11">Dashboard Volumen <?php echo "$showSkill"; ?> (<?php echo "$Pstart"; ?>)</th>
  </tr>
  <tr>
  
    <th class="tg-ouin2" colspan="11"><lab id='update' style='font-weight:bold'>Ultima Actualizacion: <?php echo $CVdate[1]; ?></lab><lab id='demo' style='font-size:16px; vertical-align:center'></lab></th>
  </tr>
</table><br>
<table  class="tg" style="width:<?php echo $width; ?>;  height:90%">
	<tr>
		<th style="width:50%;  height:0%"></th>
		<th style="width:50%"></th>
		
	</tr>
	<tr>
		<th style="height:5%" class="tg-ouin2">Volumen</th>
		<th class="tg-ouin2">AHT</th>
		
	</tr>
  <tr>
  
    <th style="height:45%"><div id='Volumen' style="height:100%"></div></th>
    <th><div id='AHT' style="height:100%"></div></th>
    <th></th>
  </tr>
  <tr>
		<th style="height:5%" class="tg-ouin2">Adherencia</th>
		<th class="tg-ouin2">SLA</th>
		
	</tr>
  <tr>
  
    <th style="height:45%"><div id='dual_x_div3' style="height:100%"></div></th>
    <th><div id='SLA' style="height:100%"></div></th>
    
  </tr>
  
 
</table>




</body>


</body>
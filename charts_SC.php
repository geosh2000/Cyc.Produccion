<?php
include("DBahtsc.php");
?>
<script>
	
	    google.load('visualization', '1.1', {packages: ["corechart"]});
	google.setOnLoadCallback(drawChart);
	

 var current =1;
 var curtext = "AHT Mensual";
   function drawChart() {


		var rowData1 = [
	       
	        ['Nombre', 'AHT', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        $x=0;
	        $n=0;
	        while ($x<$numAHTsc){
	        if ($calls[$x]!=0){
	        	if ($aht[$x]>650){
	        		if ($aht[$x]>1000){
	        			$color[$n]="red";
	        		}else{
	        			$color[$n]="#FF681C";
	        		}
	        	}else{
	        	$color[$n]="blue";
	        	}
	        	echo "['".$name[$x]."', ".$aht[$x].", '".$aht[$x]."','".$color[$n]."'],";
	        	$index[$n]=$x;
	        	$n++;
	        }
	        $x++;
	        }
	        $totalindex=$n
	        	        
	        ?>
	        
	        
	        
	      ];
	      
	      var rowData2 =    [ ['Nombre', 'Llamadas', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        
	        $q=0;
	        while ($q<$totalindex){
	        
	        	echo "['".$name[$index[$q]]."', ".$calls[$index[$q]].", '".$calls[$index[$q]]."','blue'],";
	        
	        $q++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      
	      var rowData3 = [
	       
	        ['Nombre', 'AHT', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        $x=0;
	       
	        while ($x< $totalindex){
	        if ($calls[$index[$x]]!=0){
	        	if ($acum[$index[$x]]>650){
	        		if ($acum[$index[$x]]>1000){
	        			$color1="red";
	        		}else{
	        			$color1="#FF681C";
	        		}
	        	}else{
	        	$color1="blue";
	        	}
	        	echo "['".$name[$index[$x]]."', ".$acum[$index[$x]].", '".$acum[$index[$x]]."','".$color1."'],";
	        	
	        	
	        }
	        $x++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      	
	      var data = [];
	      data[1] = google.visualization.arrayToDataTable(rowData1);
	      data[2] = google.visualization.arrayToDataTable(rowData2);

	      
	
	      var options = {
         title: 'AHT Mensual por Asesor / Servicio a Clientes',
        subtitle: 'Tiempo promedio de llamada por asesor',
        backgroundColor: 'white',
        chartArea: {top: 50, left: '25%', height: '100%', width: '100%'},
        animation: {startup: 'true', duration: 1000, easing: 'inAndOut'},
   	annotations: {alwaysOutside: 'false'},
   	fontSize: 24,
  
        bar: {groupWidth: '90%'},
        legend: { position: "none" },
      };
	
	     var chart = new google.visualization.BarChart(document.getElementById("dual_x_div"));

	      chart.draw(data[current], options);
	      
	      setInterval(function() {
	      switch(current) {
		    case 1:
		        current=2;
		        curtext = "Llamadas de Hoy";
		        break;
		    case 2:
		        current=1;
		        curtext="AHT de Hoy";
		        break;
            case 3:
		        current=1;
		        curtext="AHT de Hoy";
		        break;
		};
	      options['title'] = curtext + ' por Asesor / Servicio a Clientes';
	       chart.draw(data[current], options);}, 10000);
	    }
$(window).resize(function(){
  drawChart();

});
  </script>
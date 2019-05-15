<?php
include("DBAsesores.php");
include("DBPerfUpsell.php");

?>
<script>
	
	    google.load('visualization', '1.1', {packages: ["corechart"]});
	google.setOnLoadCallback(drawChart);
	

 var current =3;
 var curtext = "Tiempo en Llamada";
   function drawChart() {


		var rowData1 = [
	       
	        ['Nombre', 'Monto Total', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        $x=0;
	        $n=0;
	        while ($x<$numPUp){
	        if ($PUpmonto[$index[$X]]>=0){
	        $color=$color2;
	        }else{
	        if($PUpmonto[$index[$X]]<0){
	        $color=$color3;
	        }else{
	        $color=$color2;
	        }
	        }
	        
	        if ($PUpht[$x]!=0){
	        	echo "['".$ASNCorto[$PUpid[$x]-1]."', ".$PUpmonto[$x].", '$".number_format($PUpmonto[$x])."', '".$color."'], ";
	        	$index[$n]=$x;
	        	$n++;
	        }
	        $x++;
	        }
	        $totalindex=$n;
	        	        
	        ?>
	        
	        
	        
	      ];
	      
	      var rowData2 =    [ ['Nombre', 'Tiempo en Llamada', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        
	        $q=0;
	        while ($q<$totalindex){
	        $color=$color2;
	        
	        	echo "['".$ASNCorto[$PUpid[$index[$q]]-1]."', ".$PUpht[$index[$q]].", '".$PUpht[$index[$q]]."', '".$color."'], ";
	        
	        $q++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      var rowData3 =    [ ['Nombre', 'Localizadores', { role: 'annotation' }],        	
	        <?php  
	       
	        
	        $q=0;
	        while ($q<$totalindex){
	        
	        	echo "['".$ASNCorto[$PUpid[$index[$q]]-1]."', ".$PUplocs[$index[$q]].", '".$PUplocs[$index[$q]]."'],";
	        
	        $q++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      
	      
	      
	      
	      	
	      var data = [];
	      data[1] = google.visualization.arrayToDataTable(rowData1);
	      data[2] = google.visualization.arrayToDataTable(rowData2);
	      data[3] = google.visualization.arrayToDataTable(rowData3);
	      
	      
	      
	      
	
	      var options = {
         title: 'Llamadas por Asesor / Upsell',
        subtitle: 'Tiempo promedio de llamada por asesor',
        
        backgroundColor: 'white',
        chartArea: {left: '7%', height: '85%', width: '87%'},
        animation: {startup: 'true', duration: 1000, easing: 'inAndOut'},
   	annotations: {alwaysOutside: 'false'},
   	fontSize: 24,
  
        bar: {groupWidth: '90%'},
        legend: { position: "none" },
        
      };
	
	     var chart = new google.visualization.ColumnChart(document.getElementById("dual_x_div"));
	
	      chart.draw(data[current], options);
	      
	      setInterval(function() {
	      switch(current) {
		    case 1:
		        current=2;
		        curtext = "Tiempo en Llamada";
		        break;
		    case 2:
		        current=3;
		        curtext= "Localizadores";
		        break;
		    case 3:
		        current=1;
		        curtext="Monto";
		        break;
	        
	        break;
		};
	      options['title'] = curtext + ' por Asesor / Upsell';
	       chart.draw(data[current], options);}, 10000);
	    }

$(window).resize(function(){
  drawChart();

});
	    
  </script>
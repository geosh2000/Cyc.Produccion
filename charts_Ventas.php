<?php
include("DBFcventas.php");
?>
<script>
	
	    google.load('visualization', '1.1', {packages: ["corechart"]});
	google.setOnLoadCallback(drawChart);
	

 var current =3;
 var curtext = "Llamadas del día";
   function drawChart() {


		var rowData1 = [
	       
	        ['Nombre', 'Monto Total', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        $x=0;
	        $n=0;
	        while ($x<$numFC){
	        if ($monto[$index[$X]]>=0){
	        $color=$color2;
	        }else{
	        if($monto[$index[$X]]<0){
	        $color=$color3;
	        }else{
	        $color=$color2;
	        }
	        }
	        
	        if ($calls[$x]!=0){
	        	echo "['".$ncorto[$x]."', ".$monto[$x].", '$".number_format($monto[$x])."', '".$color."'], ";
	        	$index[$n]=$x;
	        	$n++;
	        }
	        $x++;
	        }
	        $totalindex=$n
	        	        
	        ?>
	        
	        
	        
	      ];
	      
	      var rowData2 =    [ ['Nombre', 'Monto MP', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        
	        $q=0;
	        while ($q<$totalindex){
	         if ($mmp[$index[$q]]>=0){
	        $color=$color2;
	        }else{
	        if($MMP[$index[$q]]<0){
	        $color=$color3;
	        }else{
	        $color=$color2;
	        }
	        }
	        
	        	echo "['".$ncorto[$index[$q]]."', ".$mmp[$index[$q]].", '$".number_format($mmp[$index[$q]])."', '".$color."'], ";
	        
	        $q++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      var rowData3 =    [ ['Nombre', 'Llamadas', { role: 'annotation' }],        	
	        <?php  
	       
	        
	        $q=0;
	        while ($q<$totalindex){
	        
	        	echo "['".$ncorto[$index[$q]]."', ".$calls[$index[$q]].", '".$calls[$index[$q]]."'],";
	        
	        $q++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      var rowData4 =    [ ['Nombre', 'Localizadores', { role: 'annotation' }],        	
	        <?php  
	       
	        
	        $q=0;
	        while ($q<$totalindex){
	        
	        	echo "['".$ncorto[$index[$q]]."', ".$locs[$index[$q]].", '".$locs[$index[$q]]."'],";
	        
	        $q++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      var rowData5 =    [ ['Nombre', 'FC %', { role: 'annotation' }, { role: 'style' }],        	
	        <?php  
	       
	        
	        $q=0;
	        while ($q<$totalindex){
	        if ($fc[$index[$q]]>=$metaok){
	        $color=$color1;
	        }else{
	        if($fc[$index[$q]]<$metalow){
	        $color=$color3;
	        }else{
	        $color=$color2;
	        }
	        }
	        
	        	echo "['".$ncorto[$index[$q]]."', ".$fc[$index[$q]].", '".$fc[$index[$q]]."%', '".$color."'], ";
	        
	        $q++;
	        }
	        
	        	        
	        ?>
	        
	        
	        
	      ];
	      
	      
	      	
	      var data = [];
	      data[1] = google.visualization.arrayToDataTable(rowData1);
	      data[2] = google.visualization.arrayToDataTable(rowData2);
	      data[3] = google.visualization.arrayToDataTable(rowData3);
	      data[4] = google.visualization.arrayToDataTable(rowData4);
	      data[5] = google.visualization.arrayToDataTable(rowData5);
	      
	      
	
	      var options = {
         title: 'Llamadas por Asesor / Ventas',
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
		        curtext = "Monto Marcas Propias";
		        break;
		    case 2:
		        current=3;
		        curtext= "Llamadas";
		        break;
		    case 3:
		        current=4;
		        curtext="Localizadores";
		        break;
	        case 4:
		        current=5;
		        curtext="Factor de Conversion";
		        break;
	        case 5:
		        current=1;
		        curtext="Monto Total";
	        break;
		};
	      options['title'] = curtext + ' por Asesor / Ventas';
	       chart.draw(data[current], options);}, 10000);
	    }

$(window).resize(function(){
  drawChart();

});
	    
  </script>
<?php
include("connectDB.php");
include("DBCallsHoraV.php");

$height="80%";
$width="100%";
$reloadTime=60000;

/*$i=0;
while ($i<48){
if ($CVf[$i]!=0){
$pres[$i]=($CVt[$i]/$CVf[$i]-1);
}else{$pres[$i]=1;}
$i++;
}
*/
?>

<style>
	* {
	  @include box-sizing(border-box);
	}
	
	$pad: 20px;
	
	.grid {
	  background: white;
	  margin: 0 0 $pad 0;
	  
	  &:after {
	    /* Or @extend clearfix */
	    content: "";
	    display: table;
	    clear: both;
	  }
	}
	
	[class*='col-'] {
	  float: left;
	  padding-right: $pad;
	  .grid &:last-of-type {
	    padding-right: 0;
	  }
	}
	.col-2-3 {
	  width: 66.66%;
	}
	.col-1-3 {
	  width: 33.33%;
	}
	.col-1-2 {
	  width: <?php echo $width; ?>;
	}
	.col-1-4 {
	  width: 25%;
	}
	.col-1-8 {
	  width: 12.5%;
	}
	
	/* Opt-in outside padding */
	.grid-pad {
	  padding: $pad 0 $pad $pad;
	  [class*='col-']:last-of-type {
	    padding-right: $pad;
	  }
	}
	.chart {
	  width: 100%; 
	  height: <?php echo $height; ?>;
	}
</style>
<script>

var total =<?phpecho $reloadTime;?>;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
   total= total-1000;
    document.getElementById("demo").innerHTML = "Reload in " + total/1000 + " sec.";
}
</script>
<script>
	setTimeout(function() {
	    window.location.reload();
	}, <?phpecho $reloadTime;?>);
	</script>

  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
 <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
google.load('visualization', '1', {packages: ['corechart', 'line']});
google.setOnLoadCallback(drawLineColors);

function drawLineColors() {


      var jsonData = $.ajax({
          url: "getData_chartCalls.php",
          dataType: "json",
          async: false
          }).responseText;
          
      var data = new google.visualization.DataTable(jsonData);
      

//options para "no material"

      var options = {
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
        //Material       
       /*series: {
                0: {axis: 'Llamadas', pointSize: 2},
                1: {lineDashStyle: [1, 1], axis: 'Llamadas'},
                2: {lineDashStyle: [1, 1], axis: 'Llamadas'},
                3: {lineDashStyle: [4, 4], axis: 'Llamadas'},
                4: {lineDashStyle: [2, 2], axis: 'Prec'},
                5: {lineDashStyle: [4, 4], axis: 'Prec', lineWidth: 2},
                6: {lineDashStyle: [4, 4], axis: 'Prec', lineWidth: 2}

        }*/
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
      var chart = new google.visualization.LineChart(document.getElementById('dual_x_div'));
      
      //Draw para "material"
      //var chart = new google.charts.Line(document.getElementById('dual_x_div'));
      chart.draw(data, options);
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
.tg .tg-ouin{font-weight:bold;font-size:50px;background-color:#000000;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-ouin2{font-weight:bold;font-size:20px;background-color:#000000;color:#ffffff;text-align:center;vertical-align:top}
}
</style>



<table class="tg" style="width:<?php echo $width; ?>">
<tr>
    <th class="tg-ouin" colspan="15">Dashboard <?php echo $titulo; ?></th>
  </tr>
  <tr>
    <th class="tg-ouin2" colspan="15">Ultima Actualizacion: <?php echo $CVdate[1]; ?></th>
  </tr>
  <tr>
    
    <th class="tg-ouin2" colspan="15" id="demo"></th>
  </tr>
</table>

<div class="grid">
  <div class="col-1-2">
    <div id="dual_x_div" class="chart"></div>
  </div>
</div>
	

</body>
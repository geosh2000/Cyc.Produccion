<?php
include("connectDB.php");
include("DBCallsHoraV.php");
date_default_timezone_set('America/Bogota');
$height="80%";
$width="100%";
$reloadTime=10000;
$thisMonth=intval(date('m'));
$thisDay=intval(date('d'));
$thisYear=intval(date('y'))+2000;
$DPtitle="TEST DE VAR";

//GET from picker
$Pstart=$_GET['start'];
$Pend=$_GET['end'];

if($Pstart==NULL){ $Pstart="Last Week";}
if($Pend==NULL){ $Pend="Yesterday";}

$i=1;
while($i<=7){
	$PdwStatus=$_GET['dia'.$i];
	if($PdwStatus!=NULL){
		$Pdw[$i]=1;
	}else{$Pdw[$i]=0; }
	$GDWvalues="$GDWvalues&dia$i=$Pdw[$i]";
$i++;
}

//Convert Date
function ConvertMonth($mestxt){
	switch ($mestxt){
		case "January":
			$result=1;
			break;
		case "February":
			$result=2;
			break;
		case "March":
			$result=3;
			break;
		case "April":
			$result=4;
			break;
		case "May":
			$result=5;
			break;
		case "June":
			$result=6;
			break;
		case "July":
			$result=7;
			break;
		case "August":
			$result=8;
			break;
		case "September":
			$result=91;
			break;
		case "October":
			$result=10;
			break;
		case "November":
			$result=11;
			break;
		case "December":
			$result=12;
			break;
	
	}
	return $result;

}

if($Pstart=="Last Week"){
	$PSDay=date("d", time() - 60 * 60 * 24 * 7);
	$PSMonth=date("m", time() - 60 * 60 * 24 * 7);
	$PSYear=date("Y", time() - 60 * 60 * 24 * 7);
}else{
$PSMonth=ConvertMonth(substr($Pstart,3,-5));
$PSDay=substr($Pstart,0,2);
$PSYear=substr($Pstart,-4);
}

if($Pend=="Yesterday"){
	$PEDay=date("d", time() - 60 * 60 * 24);
	$PEMonth=date("m", time() - 60 * 60 * 24);
	$PEYear=date("Y", time() - 60 * 60 * 24);
}else{
$PEMonth=ConvertMonth(substr($Pend,3,-5));
$PEDay=substr($Pend,0,2);
$PEYear=substr($Pend,-4);
}



$Gvalues="?ds=$PSDay&ms=$PSMonth&ys=$PSYear&de=$PEDay&me=$PEMonth&ye=$PEYear$GDWvalues";

$i=1;
$fm=0;
$fy=0;
$fw=0;
while($i<=12){
	//Dia
	if($GDiaSem[$i]!=NULL){
		$fw++;
		if($fw==1){
			$queryW="?ds$fw=";
		}else{
			$queryW="$queryW&ds$fw=";
		}
		$queryW="$queryW$GDiaSem[$i]";
	}
	
	//MES
	if($GMes[$i]!=NULL){
		$fm++;
		if($fm==1){
			$queryM="&m$fm=";
		}else{
			$queryM="$queryM&m$fm=";
		}
		$queryM="$queryM$GMes[$i]";
	}
	
	//A√±o
	if($GAnio[$i]!=NULL){
		$fy++;
		if($fy==1){
			$queryY="&y$fy=";
		}else{
			$queryY="$queryY&y$fy=";
		}
		$queryY="$queryY$GAnio[$i]";
	}
	
$i++;
}

$getdate="$queryW$queryM$queryY";


/*$i=0;
while ($i<48){
if ($CVf[$i]!=0){
$pres[$i]=($CVt[$i]/$CVf[$i]-1);
}else{$pres[$i]=1;}
$i++;
}
*/
?>

<head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
    
</head>




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
google.setOnLoadCallback(draw);

function draw(){
	drawLineColors();
	drawToolbar();
};

function drawLineColors() {
      

//options para "no material"

      var options = {
        title:'<?phpecho $CTitle;?>',
        axes: {
                y: {
                Llamadas: {label: 'Llamadas'},
                      
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
          
        },
        colors: ['#ff0066'],
        lineWidth: 4,
        animation: {duration: 2000, startup: 'true', easing: 'inAndOut'},
        
       //No Material       
       series: {
                0: {targetAxisIndex: 0, pointSize: 2},
                

        }
      };




      //Draw para "no material"
      var chart = new google.visualization.LineChart(document.getElementById('dual_x_div'));
      
      function drawChart(){
      var jsonData = $.ajax({
          url: "getData_CallsPart.php<?php echo $Gvalues; ?>",
          dataType: "json",
         
          async: false
          }).responseText;
          
      var data = new google.visualization.DataTable(jsonData);
      
      //Draw para "material"
      //var chart = new google.charts.Line(document.getElementById('dual_x_div'));
      chart.draw(data, options);
      }
      drawChart();
      




      
    new google.visualization.Query('getData_CallsPart.php<?php echo $Gvalues; ?>').
          send(queryCallback);
    }

    function queryCallback(response) {
      visualization.draw(response.getDataTable(), {is3D: true});
    }
    
    function drawToolbar() {
      var components = [
          {type: 'igoogle', datasource: 'https://spreadsheets.google.com/tq?key=pCQbetd-CptHnwJEfo8tALA',
           gadget: 'https://www.google.com/ig/modules/pie-chart.xml',
           userprefs: {'3d': 1}},
          {type: 'html', datasource: 'https://spreadsheets.google.com/tq?key=pCQbetd-CptHnwJEfo8tALA'},
          {type: 'csv', datasource: 'getData_CallsPart.php<?php echo $Gvalues; ?>'},
          {type: 'htmlcode', datasource: 'https://spreadsheets.google.com/tq?key=pCQbetd-CptHnwJEfo8tALA',
           gadget: 'https://www.google.com/ig/modules/pie-chart.xml',
           userprefs: {'3d': 1},
           style: 'width: 800px; height: 700px; border: 3px solid purple;'}
      ];

      var container = document.getElementById('toolbar_div');
      google.visualization.drawToolbar(container, components);
    };
</script>
<script>

function showPicker() {
    
     
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("picker").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","datePicker.php?saludo=hola",true);
        xmlhttp.send();
        
        
        
        
    
}

showPicker();

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
.tg .tg-datep{font-weight:bold;background-color:#000000;color:#ffffff;}
.tg .tg-ouin2{font-weight:bold;font-size:20px;background-color:#000000;color:#ffffff;text-align:center;vertical-align:top}
}
</style>


<table class="tg" style="width:<?php echo $width; ?>">
<tr>
    <th class="tg-ouin">Participaciè´—n por dè´øa <?php echo "($Pstart to $Pend)"; ?></th>
  </tr>
  
</table>
<div>
<?php include ("datePicker.php"); ?>
</div>
<div class="grid">
  <div class="col-1-2">
    <div id="dual_x_div" class="chart"></div>
  </div>
</div>
<div id='toolbar_div'></div>
	




</body>
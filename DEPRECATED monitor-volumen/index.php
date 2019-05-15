<?
session_start();  
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="monitor_y_lw";

include("../connectDB.php");
include("../DBCallsHoraV.php");
$menu_monitores="class='active'";
date_default_timezone_set('America/Bogota');
$showDeps=1;
$height="87%";
$width="100%";
$reloadTime=10000;
$thisMonth=intval(date('m'));
$thisDay=intval(date('d'));
$thisYear=intval(date('Y'));
$Pstart=$_POST['start'];
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
if ($Pstart==NULL){
	$Pstart=date('Y-m-d');
}
$Cdia=date("d", strtotime($Pstart));
$Cmes=date("m", strtotime($Pstart));
$Canio=date("Y", strtotime($Pstart));

$today=time();

$CalValue=floor(($today-strtotime($Pstart))/(60*60*24))*(-1);

$CFecha="?d=$Cdia&m=$Cmes&y=$Canio&skill=$showSkill";
$CTitle="Fecha: $Cdia-$Cmes-$Canio";



include("../common/scripts.php");
?>



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
        xmlhttp.open("GET","../updatedCalls.php",true);
        xmlhttp.send();
        
        
        
        
    
}

</script>


  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
 
  
<script>
google.load('visualization', '1', {packages: ['corechart', 'line']});
google.setOnLoadCallback(drawLineColors);

function drawLineColors() {
      

//options para "no material"

      var options = {
        title:'<?echo $Pstart;?>',
        axes: {
                y: {
                Llamadas: {label: 'Llamadas'},
                Prec: {label: 'Precision %', format: 'percent', maxValue: 10}        
                },
                x: {0: {label: 'Horas'}}
        },

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
      var chart = new google.visualization.LineChart(document.getElementById('chart'));
      
      function drawChart(){
      var jsonData = $.ajax({
          url: "../getData_chartCalls2.php<? echo $CFecha; ?>",
          dataType: "json",

          async: false
          }).responseText;
          
      var data = new google.visualization.DataTable(jsonData);
      
      //Draw para "material"
      //var chart = new google.charts.Line(document.getElementById('dual_x_div'));
      chart.draw(data, options);
      }
      drawChart();
      var total =<?echo $reloadTime;?>;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
	if (total == 1000){
	
	drawChart();
	updateStatus();
	total=<?echo $reloadTime;?>;}
   total= total-1000;
    document.getElementById("demo").innerHTML = "        (Reload in " + total/1000 + " sec.)";
}

updateStatus(); 
      
    }
</script>

<style>
.topbar{
    display:block;
    width:100%;
    height:90%;

    }
</style>
<? include("../common/menu.php"); ?>

<table class="t2" style="width:100%"><form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" name="SelctDays" id="flight">
<tr class='title'>
<th class="title" colspan="11">Dashboard Volumen <? echo $showSkill; ?> Inbound (<?php echo "$Pstart"; ?>)</th>
</tr>
<tr class='title'>
	<? /* <th class="title" colspan="5" style="width:20%"> ?><?include("../common/datepicker.php");?><? </th> */ ?>
    <th>Fecha:</th>
    <th class='pair'><input type="date" name="start" data-value="<? echo $CalValue; ?>" value="<? echo $Pstart; ?>" onchange=""/></th>
    <th>Departamento:</th>
    <th class='pair'>
    <select name='skill'>

  	<option value='Servicio a Cliente' $opt_SC >Servicio a Cliente</option>
  	<option value='Soporte Agencias' $opt_SA >Soporte Agencias</option>
  	<option value='Trafico MP' $opt_TMP >Trafico MP</option>
  	<option value='Trafico MT' $opt_TMT >Trafico MT</option>
  	<option value='Ventas' $opt_V >Ventas</option>
  </select></th>

    <th class='total'><input type="submit" value="Consultar"></th>

  </tr>
  <tr>

    <th class="title" colspan="11"><lab id='update' style='font-weight:bold'>Ultima Actualizacion: <?php echo $CVdate[1]; ?></lab><lab id='demo' style='font-size:16px; vertical-align:center'></lab></th>
  </tr>

  
 
</form></table>

<br>
    <div id='chart' class='topbar'></div>

	

</body>


</body>
<script>
$(":date").dateinput({ trigger: true, format: 'dd mmmm yyyy', max:0 })

// use the same callback for two different events. possible with bind
$(":date").bind("onShow onHide", function()  {
	$(this).parent().toggleClass("active");
});
</script>
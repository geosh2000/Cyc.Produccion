<?
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="monitor_gtr";
$menu_monitores="class='active'";
$showDeps=1;
$direction=realpath('../');
include("$direction/connectDB.php");
include("$direction/DBCallsHoraV.php");
date_default_timezone_set('America/Bogota');
$reloadTime=20000;
$showSkill=$_POST['skill'];


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
<script type="text/javascript">
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(Ventas);
      google.charts.setOnLoadCallback(Ventas3);
      google.charts.setOnLoadCallback(SC);
      google.charts.setOnLoadCallback(SC3);
      google.charts.setOnLoadCallback(Corporativo);
      google.charts.setOnLoadCallback(Corporativo3);
      google.charts.setOnLoadCallback(TMP);
      google.charts.setOnLoadCallback(TMP3);
      google.charts.setOnLoadCallback(TMT);
      google.charts.setOnLoadCallback(TMT3);
      google.charts.setOnLoadCallback(AG);
      google.charts.setOnLoadCallback(AG3);
      function Ventas() {



        var options = {
          width: 595, height: 400,
          redFrom: 0, redTo: 70,
          yellowFrom:90, yellowTo: 100,
          greenFrom: 70, greenTo: 90,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('Ventas'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=ventas&type=1",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }


      function Ventas3() {



        var options = {
          width: 120, height: 400,
          redFrom: 0, redTo: 70,
          yellowFrom:90, yellowTo: 100,
          greenFrom: 70, greenTo: 90,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('Ventas3'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=ventas&type=2",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();



      }

      function SC() {



        var options = {
          width: 595, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('SC'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=sc&type=1",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function SC3() {



        var options = {
          width: 120, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('SC3'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=sc&type=2",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function Corporativo() {



        var options = {
          width: 595, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('Corp'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=corp&type=1",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function Corporativo3() {



        var options = {
          width: 120, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('Corp3'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=corp&type=2",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function TMP() {



        var options = {
          width: 595, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('TMP'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=tmp&type=1",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function TMP3() {



        var options = {
          width: 120, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('TMP3'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=tmp&type=2",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function TMT() {



        var options = {
          width: 595, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('TMT'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=tmt&type=1",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function TMT3() {



        var options = {
          width: 120, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('TMT3'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=tmt&type=2",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function AG() {



        var options = {
          width: 595, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('AG'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=ag&type=1",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

      function AG3() {



        var options = {
          width: 120, height: 400,
          redFrom: 0, redTo: 60,
          yellowFrom:80, yellowTo: 100,
          greenFrom: 60, greenTo: 80,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('AG3'));

        this.refreshData = function (){
	      var jsonData = $.ajax({
	          url: "../json/sla_dia.php?dep=ag&type=2",
	          dataType: "json",

	          async: false
	          }).responseText;

	      var data = new google.visualization.DataTable(jsonData);

	      chart.draw(data, options);
	      }
	      refreshData();


      }

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

      function Reloads(){


      var total=30000;



        function myTimer() {
            if(total==0){total=10000;

            }
            total= total-1000;
            document.getElementById("demo").innerHTML = "   //   Reload in " + total/1000 + " sec.";
        }

        setInterval(function() {
          if(total==0){
          (new Ventas()).refreshData();
          (new Ventas3()).refreshData();
          (new SC()).refreshData();
          (new SC3()).refreshData();
          (new Corporativo()).refreshData();
          (new Corporativo3()).refreshData();
          (new TMP()).refreshData();
          (new TMP3()).refreshData();
          (new TMT()).refreshData();
          (new TMT3()).refreshData();
          (new AG()).refreshData();
          (new AG3()).refreshData();
          updateStatus();

          }
          myTimer();
        }, 1000);

        }


        Reloads();





</script>




<? include("$direction/common/menu.php"); ?>


<table  class="t2" style="width:100%">


    <tr>

		<th colspan=2 class="title">Ventas</th>
		<th colspan=2 class="title">Servicio a Cliente</th>
		<th colspan=2 class='title'>Corporativo</th>

	</tr>
  <tr>
    <th class='pair' style="height:105px;" id='Ventas'></th>
    <th class='pair' style="height:300px;" id='Ventas3'></th>
    <th class='odd' id='SC'></th>
    <th class='odd' id='SC3'></th>
    <th class='pair' id='Corp'></th>
    <th class='pair' id='Corp3'></th>
  </tr>
  <tr>
		<th colspan=2 class="title">Trafico MP</th>
		<th colspan=2 class="title">Trafico MT</th>
		<th colspan=2 class='title'>Agencias</th>
	</tr>
  <tr>
    <th class='pair' style="height:105px;" id='TMP'></th>
    <th class='pair' style="height:300px;" id='TMP3'></th>
    <th class='odd' id='TMT'></th>
    <th class='odd' id='TMT3'></th>
    <th class='pair' id='AG'></th>
    <th class='pair' id='AG3'></th>
  </tr>


 
</table>
<table class="t2" style="width:100%">

  <tr>

    <td class="title" colspan="100"><lab id='update' style='font-weight:bold'>Ultima Actualizacion: <?php echo $CVdate[1]; ?></lab><lab id='demo' style='font-size:16px; vertical-align:center'></lab></td>
  </tr>
</table>
</div>
</div>




</body>


</body>
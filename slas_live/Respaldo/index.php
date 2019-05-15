<?php 
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="monitor_gtr";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");
?>
 
 <link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">  
 <script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>  
 <script src="https://code.highcharts.com/highcharts.js"></script>  
 <script src="https://code.highcharts.com/highcharts-more.js"></script>  
 <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>  
 <script src="https://code.highcharts.com/modules/exporting.js"></script>  
 <script src="/js/export-csv/export-csv.js"></script>  

  
  
 <script>  
  
 $(function(){  
 //Progress Bars  
 progressbar=$('#progressbar').progressbar({  
 value: false  
 });  
  
 progressbarload=$('#progressbarload').progressbar({  
 value: false  
 }).hide();  
  
 var gaugeOptions = {  
  
 chart: {  
 type: 'solidgauge',  
 height: 400  
 },  
  
 title: {  
 text: 'Titulo',  
 floating: true,  
 style: {  
 fontSize: '38px'  
 }  
 },  
 subtitle: {  
 text: 'Llamadas',  
 floating: true,  
 y: 48  
 },  
  
 pane: {  
 center: ['50%', '85%'],  
 size: '140%',  
 startAngle: -90,  
 endAngle: 90,  
 background: [{  
 backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',  
 innerRadius: '70%',  
 outerRadius: '100%',  
 shape: 'arc'  
 }, { // Track for Exercise  
 outerRadius: '60%',  
 innerRadius: '70%',  
 shape: 'arc',  
 backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[1]).setOpacity(0.3).get(),  
 borderWidth: 0  
 }]  
 },  
  
 tooltip: {  
 enabled: false  
 },  
  
 // the value axis  
 yAxis: {  
 lineWidth: 0,  
 minorTickInterval: null,  
 tickAmount: 2,  
 title: {  
 y: -70  
 },  
 labels: {  
 y: 16  
 }  
 },  
  
 plotOptions: {  
 solidgauge: {  
 dataLabels: {  
 y: 5,  
 borderWidth: 0,  
 useHTML: true  
 }  
 }  
 }  
 };  
  
 // The speed gauge  
 function createChart(departamento, meta, slamin, slamax){  
 $('#'+departamento).highcharts(Highcharts.merge(gaugeOptions, {  
 yAxis: {  
 stops: [  
 [0.1, '#DF5353'], // red  
 [(slamin-0.01), '#DF5353'], // red  
 [slamin, '#DDDF0D'], // yellow  
 [(slamin+0.1), '#55BF3B'], // green  
 [(slamax), '#DDDF0D'], // yellow  
 [0.99, '#DF5353'] // red  
 ],  
 min: 0,  
 max: 100  
  
 },  
  
 credits: {  
 enabled: false  
 },  
  
 series: [{  
 name: meta,  
 data: [{  
 color: Highcharts.getOptions().colors[0],  
 radius: '100%',  
 innerRadius: '70%',  
 y: 80}  
 ],  
 dataLabels: {  
 format: '<div style="text-align:center"><span style="font-size:38px;color:' +  
 ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y} %</span><br/><span style="font-size:24px;color:silver">'+meta+'</span><br>' +  
 '<span style="font-size:35px;color:#6f6f70">'+departamento+'</span></div>'  
 },  
 tooltip: {  
 valueSuffix: '%'  
 }  
 },{  
 name: meta,  
 data: [{  
 color: Highcharts.getOptions().colors[0],  
 radius: '70%',  
 innerRadius: '60%',  
 y: 80}  
 ],  
 tooltip: {  
 valueSuffix: '%'  
 }  
 }]  
  
 }));  
 }  
  
 createChart('Ventas','84/20',.74,.94);  
 createChart('VentasMP','84/20',.74,.94);  
 createChart('SAC','70/30',.6,.8);  
 createChart('TMP','83/30',.73,.93);  
 createChart('TMT','80/30',.70,.90);  
 createChart('Agencias','70/30',.6,.8);  
  
 function getSlas(){  
 $('#progressbarload').show();  
 $.ajax({  
 url: "/qm/qm_vars.php",  
 type: 'GET',  
 data: { tipo: 'slamon'},  
 dataType: 'json', // will automatically convert array to JavaScript  
 success: function(array) {  
 var data_sla=array;  
 $('.gauge').each(function(){  
 var element=$(this);  
 var skill=element.attr('skill');  
 var slat=element.attr('slat');  
 var chart = element.highcharts(),  
 point;  
  
 var newVal = (typeof data_sla[skill][slat] === 'undefined') ? 100 : parseFloat(data_sla[skill][slat]);  
 var newAb = (typeof data_sla[skill]['abandon'] === 'undefined') ? 0 : parseFloat(data_sla[skill]['abandon']);  
 var newCalls = (typeof data_sla[skill]['calls'] === 'undefined') ? 0 : data_sla[skill]['calls'];  
  
 if (chart) {  
 point = chart.series[0].points[0];  
 point.update(newVal);  
  
 point = chart.series[1].points[0];  
 point.update(newAb);  
  
 chart.setTitle({text: newCalls});  
 }  
 });  
  
 var newlu = (typeof data_sla['lu'] === 'undefined') ? 'unknown' : data_sla['lu'];  
 $('#lu').text('Last Update: '+newlu);  
  
 $('#progressbarload').hide();  
  
 },  
 error: function(a,b,c){  
 //alert("Error con AJAX: "+c);  
 $('#progressbarload').hide();  
 }  
  
 });  
  
 }  
  
 $('#load').click(function(){  
 getSlas();  
 });  
  
 getSlas();  
  
 setInterval(function(){  
 getSlas();  
 },10000);  
  
  
 });  
  
 </script>  
 <style>  
 #main{  
 width: 1810;  
 margin: auto;  
 }  
  
 .gauge{  
 display: inline-block;  
 width: 550;  
 height: 300px;  
 margin: 25px;  
 }  
 </style>  
 <div id='flag' hidden>0</div>  
  
 <table class='t2' style='width:100%; margin:auto; background: #215086'>  
 <tr class='title' style='background: #215086'>  
 <th colspan=10 style='font-size: 40px;'>Live SLA's<br><lu id='lu'></lu></th>  
 </tr>  
 </table>  
  
  
 <br>  
 <div id='main'>  
 <div class='gauge' id='Ventas' skill='3' slat='sla20'></div>  
 <div class='gauge' id='VentasMP' skill='35' slat='sla20'></div>  
 <div class='gauge' id='SAC' skill='4' slat='sla30'></div>  
 <div class='gauge' id='TMP' skill='9' slat='sla30'></div>  
 <div class='gauge' id='TMT' skill='8' slat='sla30'></div>  
 <div class='gauge' id='Agencias' skill='7' slat='sla30'></div>  
 </div>  
 <button id='load' class='button button_blue_w'>Load</button>  
 <div id="progressbarload"></div>  

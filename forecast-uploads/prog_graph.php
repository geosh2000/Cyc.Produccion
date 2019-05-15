<?php
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');
header('Content-Type: text/html; charset=utf-8');
	

?>	
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="/js/export-csv/export-csv.js"></script>
<script>
function drawChart(needed,programmed){
		$('#container').highcharts({
	        chart: {
	            type: 'column',
	            animation: false
	        },
	        title: {
	            text: "Programacion"
	        },
	        xAxis: {
	            categories: [<?php
	            	for($i=0;$i<48;$i++){
	            		$hora=$i/2;
	            		echo "'$hora',";
	            	}
	            	?>]
	        },
	        yAxis: {
	            min: 0,
	            title: {
	                text: 'Total de llamadas'
	            },
	            stackLabels: {
	                enabled: true,
	                style: {
	                    fontWeight: 'bold',
	                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
	                }
	            }
	        },
	        legend: {
	            align: 'right',
	            x: -30,
	            verticalAlign: 'top',
	            y: 25,
	            floating: true,
	            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
	            borderColor: '#CCC',
	            borderWidth: 1,
	            shadow: false
	        },
	        tooltip: {
	            headerFormat: '<b>{point.x}</b><br/>',
	            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
	        },
	        plotOptions: {
	            column: {
	                dataLabels: {
	                    enabled: true,
	                    rotation: 270,
	                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
	                    style: {
	                        textShadow: '0 0 3px black'
	                    }
	                }
	            }
	        },
	        series: [{
	        	name: 'Needed',
	        	type: 'line',
	            color: '#009900',
	            data: needed,
	            animation: false
	        	}, {
	        	name: 'Programmed',
	        	type: 'column',
	            color: '#002699',
	            data: programmed,
	            animation: false
	        }]
	    });
	}

$(function(){
	window.opener.document.getElementById('flag').innerHTML="1"
	window.opener.updateChart();
})
</script>
<div id='container' style='height:100%'></div>
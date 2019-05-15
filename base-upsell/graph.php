<?php

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");

date_default_timezone_set('America/Bogota');

if(isset($_POST['from'])){
	$from=date('Y-m-d',strtotime($_POST['from']));
}else{
	$from=date('Y-m-d');
}

if(isset($_POST['to'])){
	$to=date('Y-m-d',strtotime($_POST['to']));
}else{
	$to=date('Y-m-d');
}



?>
<script src="/js/highcharts/stock/highstock.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>

<script>
$(function(){
    $('#from').periodpicker({
    	end: '#to',
		lang: 'en',
		norange: true,
		animation: true
	});
	
	tipochart='column';
	
	$('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Stacked column chart'
        },
        xAxis: {
            categories: [<?php for($i=0;$i<24;$i=$i+0.5){$axis.="'".number_format($i,1)."',";} echo substr($axis, 0, -1); ?>]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total fruit consumption'
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
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                }
            }
        },
        series: [{
            name: 'No Reserva',
            data: [<?php $serie=""; for($i=0;$i<48;$i++){$serie.="0,";} echo substr($serie,0,-1); ?>],
            color: '#e5b506'
        }, {
            name: 'Online',
            data: [<?php $serie=""; for($i=0;$i<48;$i++){$serie.="0,";} echo substr($serie,0,-1); ?>],
            color: '#af1168'
        }, {
            name: 'Upsell',
            data: [<?php $serie=""; for($i=0;$i<48;$i++){$serie.="0,";} echo substr($serie,0,-1); ?>],
            color: '#4d548c'
        }]
    });
    
    
    graph = $('#container').highcharts();
    
    function chartUpdate(){
    	
    	$.ajax({
    		url: 'query_graph.php',
    		type: 'POST',
    		data: {fecha: $('#from').val()},
    		dataType: 'json',
    		success: function(array){
    			data=array;
    			
    			var point_total = graph.series[0];
    			var point_ol = graph.series[1];
    			var point_us = graph.series[2];
    			
    			point_total.setData(data['Total']);
    			point_ol.setData(data['OL']);
    			point_us.setData(data['US']);
    			
    			graph.setTitle({text: data['lu']});
    		},
    		error: function(){
    			$('#test').html("<p>Error al obtener info</p>");
    		}
    		
    	})
    	
    }
    
    $.each(['line', 'column', 'spline'], function (i, type) {
        $('#' + type).click(function () {
            graph.series[0].update({
                type: type
            });
            graph.series[1].update({
                type: type
            });
            graph.series[2].update({
                type: type
            });
        });
    });
    
    chartUpdate();
    
    setInterval(function(){
    	chartUpdate();
    },300000);
    	
    $("#search").click(function(){
    	chartUpdate();
    });
});
</script>

<style>
.main{
    width:auto;
    height: 80%;
    margin: auto;
    background: navy;
}

.container{
    width:40%;
    height: 450px;
    float: left;
    background: cyan;
}

</style>


<br>
<div style='text-align: right;'>
	<input type='text' id='from' name='from' value='<?php echo $from; ?>' required><input type='text' id='to' name='to' value='<?php echo $to; ?>' required>
	<button class='button button_blue_w' id='search'>Consultar</button>

</div>
<br>
<div class='main' id='container'>
</div>
<button class='button button_green_w tipo' id='line'>Line</button><button class='button button_green_w tipo' id='spline'>Spline</button><button class='button button_green_w tipo' id='column'>Column</button>
<div id='test'></div>

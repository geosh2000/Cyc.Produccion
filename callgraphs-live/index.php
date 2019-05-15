<?php

include_once("../modules/modules.php");

initSettings::start(true, 'monitor_gtr');
initSettings::printTitle('CallsFlow Live');
timeAndRegion::setRegion('Cun');

Scripts::periodScript('from', 'to', 'norange: true');

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


$tbody="<td>Fecha</td><td><input type='text' value='$from' name='from' id='from'><input type='text' value='$to' name='to' id='to'></td>";
Filters::showFilterNOFORM('search', 'Consultar', $tbody);

?>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>

<script>

$(function(){
	
	bars = [];

     //INIT glob_flag
     glob_flag=0;

    //drawChart();

    //Read W & H of window
    function resizeDiv(){
        var w = window.innerWidth;
        var h = window.innerHeight;
        $('.main').css('width',w*0.9);
        $('.main').css('height',h*0.22);
       
    }

    resizeDiv();

    $( window ).resize(function() {
      resizeDiv();
    });

    //AJAX for DataGet
    date='<?php echo $from; ?>';
    
    createChart('vmp');
    createChart('vmt');
    createChart('sacin');
    createChart('ag');
    
    function updateChart(container){
    	var skill=$('#'+container).attr('skill');
    	
    	bars[container].series[1].setData(data[skill]['Answered']);
        bars[container].series[0].setData(data[skill]['Abandoned']);
        bars[container].series[2].setData(data[skill]['forecast']);
        bars[container].series[3].setData(data[skill]['CallsYd']);
        bars[container].series[4].setData(data[skill]['CallsLW']);
        bars[container].series[5].setData(data[skill]['CallsLY']);
        bars[container].series[6].setData(data[skill]['AHT']);
        bars[container].series[7].setData(data[skill]['SLA']);
    }

    function sendRequest(date,date_to){
        $.ajax({
            url: "<?php echo MODULE_PATH; ?>mon_getdata2.php",
            type: 'POST',
            data:  {fecha: date, to: date_to},
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                data=array;
                
                updateChart('vmp');
                updateChart('vmt');
                updateChart('sacin');
                updateChart('ag');
       
                
                $('#lu').text("Last Update: " + data['lu'] + " // Visualizando: "+ date);
           }
            
        });


    }

    sendRequest(date);

    tout=60;

    setInterval(function(){
       sendRequest(date);
    },60000);
    
    $("#search").click(function(){
    	//alert(date + " // " + $('#from').val());
    	date=$('#from').val();
    	date_to=$('#to').val();
    	sendRequest(date, date_to);	
    });
    
    function createChart(container){
    	var title=$('#'+container).attr('dep');
    	
    	$('#'+container).highcharts({
		        chart: {
		            type: 'column',
		            animation: false
		        },
		        title: {
		            text: title
		        },
		        xAxis: {
		            categories: [<?php
		            	for($i=0;$i<48;$i++){
		            		$hora=$i/2;
		            		echo "'$hora',";
		            	}
		            	?>]
		        },
		        yAxis: [{
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
		        {
		            min: 0,
		            title: {
		                text: 'Promedio de tiempo'
		            },
		            opposite: true
		        },
		        {
		            min: 0,
		            title: {
		                text: 'SLA'
		            },
		            opposite: true
		        }],
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
		                     dataLabels: {
			                    enabled: true,
			                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
			                }
		                }
		            }
		        },
		        series: [{
		        	name: 'Abandoned',
		        	color: '#5b0003',
		            data: [0],
		            animation: false
		        	}, {
		        	name: 'Answered',
		        	data: [0],
		        	color: '#3f7200',
		            animation: false
		        }, {
		        	name: 'Forecast',
		        	type: 'spline',
		        	dashStyle: 'ShortDot',
		        	data: [0],
		        	color: '#ff0000',
		            animation: false
		        }, {
		        	name: '-1 Day',
		        	type: 'spline',
		        	dashStyle: 'ShortDot',
		        	data: [0],
		        	color: '#000000',
		            animation: false
		        }, {
		        	name: '-7 Day',
		        	type: 'spline',
		        	dashStyle: 'ShortDot',
		        	data: [0],
		        	color: '#4fbf00',
		            animation: false
		        }, {
		        	name: '-1 Year',
		        	type: 'spline',
		        	dashStyle: 'ShortDot',
		        	data: [0],
		        	color: '#f49842',
		            animation: false
		        }, {
		        	name: 'AHT',
		        	type: 'line',
		        	yAxis: 1,
		        	dashStyle: 'ShortDot',
		        	data: [0],
		        	color: '#1061e5',
		            animation: false
		        }, {
		        	name: 'SLA',
		        	type: 'line',
		        	yAxis: 2,
		        	dashStyle: 'ShortDot',
		        	data: [0],
		        	color: '#bb91c4',
		            animation: false
		        }]
		    });
		
			bars[container]=$('#'+container).highcharts();
			bars[container].series[3].hide();
			bars[container].series[4].hide();
			bars[container].series[5].hide();
    }
    
    


});

</script>

<style>
.main{
    width:auto;
    margin: auto;
    background: navy;
    height: 800px;
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
	<p id='lu'></p>

</div>
<br>
<div class='main' id='vmp' dep='Ventas MP' skill='35'></div>
<div class='main' id='vmt' dep='Ventas MT' skill='3'></div>
<div class='main' id='sacin' dep='SAC IN' skill='4'></div>
<div class='main' id='ag' dep='Agencias' skill='7'></div>
<div id='error'></div>

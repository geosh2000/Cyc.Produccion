<?php

include_once("../modules/modules.php");

initSettings::startScreen(false);
initSettings::printTitle('PT DID Monitor');
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



?>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>

<script>

$(function(){

     //INIT glob_flag
     glob_flag=0;

    //drawChart();

    //Read W & H of window
    function resizeDiv(){
        var w = window.innerWidth;
        var h = window.innerHeight;
        $('.main').css('width',w*0.9);
        $('.main').css('height',h*0.45);
       
    }

    resizeDiv();

    $( window ).resize(function() {
      resizeDiv();
    });

    //AJAX for DataGet
    date='<?php echo $from; ?>';

    function sendRequest(date,date_to){
        $.ajax({
            url: "mon_getdata2.php",
            type: 'POST',
            data:  {fecha: date, to: date_to},
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                data=array;
                pie.series[0].setData(data['pie']);
                bars.series[2].setData(data['Answered']);
                bars.series[1].setData(data['Desborde']);
                bars.series[0].setData(data['Abandoned']);
                bars.series[3].setData(data['forecast']);
                bars.series[4].setData(data['CallsYd']);
                bars.series[5].setData(data['CallsLW']);
                bars.series[6].setData(data['CallsLY']);
                $('#lu').text("Last Update: " + data['lu'] + " // Visualizando: "+ date);
           }
            
        });


    }

    sendRequest(date);

    tout=60;

    setInterval(function(){
       sendRequest(date);
    },60000);
    
    $('#main').highcharts({
	        chart: {
	            plotBackgroundColor: null,
	            plotBorderWidth: null,
	            plotShadow: false,
	            type: 'pie',
	            options3d: {
	                enabled: true,
	                alpha: 45
	            },
	            animation: false
	        },
	        title: {
	            text: "<br><br>" ,
	            align: 'center',
	            verticalAlign: 'middle',
	            y: 40
	        },
	        tooltip: {
	            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	        },
	         plotOptions: {
	            pie: {
	                allowPointSelect: true,
	                cursor: 'pointer',
	                dataLabels: {
	                    enabled: true,
	                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
	                    style: {
	                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
	                    }
	                }
	            }
	        },
	        series: [{
	            type: 'pie',
	            name: 'Group share',
	            innerSize: '50%',
	            data: [
	            		['Main',0],
	            		['Movil',0],
	            		['Promo',0],
	            		['Promo Aereo',0],
	            		['PiceLab Puebla',0],
	            		['Xfer',0]
	            		
	            		]
	        }]
	    });
         
    

    pie=$('#main').highcharts();
    
    
    $("#search").click(function(){
    	//alert(date + " // " + $('#from').val());
    	date=$('#from').val();
    	date_to=$('#to').val();
    	sendRequest(date, date_to);	
    });
    
    $('#bars').highcharts({
	        chart: {
	            type: 'column',
	            animation: false
	        },
	        title: {
	            text: "Calls"
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
	        	},{
	        	name: 'DesbordePDV',
	        	color: '#91e8e1',
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
	        }]
	    });
	
		bars=$('#bars').highcharts();


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
	<input type='text' id='from' name='from' value='<?php echo $from; ?>' required><input type='text' id='to' name='to' value='<?php echo $to; ?>' required>
	<button class='button button_blue_w' id='search'>Consultar</button>
	<p id='lu'></p>

</div>
<br>
<div class='main' id='main'>
</div>
<div class='main' id='bars'>
</div>
<div id='error'></div>

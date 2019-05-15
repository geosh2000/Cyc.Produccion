<?php

include_once("../modules/modules.php");

initSettings::startScreen(false);
timeAndRegion::setRegion("Cun");


?>
<script src="/js/highcharts/highcharts.js"></script>
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
});
</script>


<script>

$(function(){

     //INIT glob_flag
     glob_flag=0;
     
     //canal
     canal=$('#canal').val();

    //drawChart();

    //Read W & H of window
    function resizeDiv(){
        var w = window.innerWidth;
        var h = window.innerHeight;
        $('.main').css('width',w*0.9);
        $('.main').css('height',h*0.98);
       
    }

    resizeDiv();

    $( window ).resize(function() {
      //resizeDiv();
    });

    //AJAX for DataGet
    date='<?php echo $from; ?>';
    
    //Change Canal
    $('#canal').change(function(){
    	redrawChart(data);
    });
    
    function redrawChart(info){
    	bars.series[0].setData(data['Series']);
    	//bars.series[0].setData(data['Monday']);
    	bars.xAxis[0].update({
    		categories: data['Categories']
    	});
    }

    function sendRequest(dateo){
        $.ajax({
            url: "mon_getdata.php",
            type: 'POST',
            data:  {from: date},
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                data=array;
                redrawChart(data);
                $('#lu').text("Last Update: " + data['lu'] + " // Visualizando: "+ date);
           }
            
        });


    }

    sendRequest(date);
    setTimeout(function(){
    	sendRequest(date);
    },1000);

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
    
    $('#bars').highcharts({
	        chart: {
	        	type: 'bar'
	        },
	        
	        title: {
	            text: "Venta Marcas Propias"
	        },
	        yAxis: {
	             min: 0,
	            title: {
	                text: 'Monto (MXN)',
	                align: 'high'
	            },
	            labels: {
	                overflow: 'justify'
	            }
	        },
        	plotOptions: {
	            bar: {
	            	stacking: 'normal',
	            	dataLabels: {
	            		align: 'right',
	            		format: '${point.y:,.2f}',
	            		enabled: true
	            	}
	            }
	        },
	        tooltip: {
	            headerFormat: '<b>{point.x}</b><br/>',
	            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
	        },
	        series: [{
	        	name: 'Today',
	        	data: [0]
	        	
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
    height: 98%;
}

.container{
    width:40%;
    height: 100%;
    float: left;
    background: cyan;
}

</style>


<br>

<div class='main' id='bars'>
</div>
<div id='error'></div>

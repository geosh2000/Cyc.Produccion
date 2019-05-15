<?php
include_once("../modules/modules.php");
initSettings::start(false);
timeAndRegion::setRegion('Cun');

Scripts::periodScript('from','to', 'norange: true');

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
     
     //canal
     canal=$('#canal').val();

    //drawChart();

    //Read W & H of window
    function resizeDiv(){
        var w = window.innerWidth;
        var h = window.innerHeight;
        $('.main').css('width',w*0.9);
        $('.main').css('height',h*0.85);
       
    }

    resizeDiv();

    $( window ).resize(function() {
      //resizeDiv();
    });

    //AJAX for DataGet
    date='<?php echo $from; ?>';
    
    //Change Canal
    $('#canal, #servicio').change(function(){
		redrawChart(data);
    });
    
    function redrawChart(info){
    	bars.series[0].setData(data['TD'][$('#canal').val()][$('#servicio').val()]);
        bars.series[1].setData(data['YD'][$('#canal').val()][$('#servicio').val()]);
        bars.series[2].setData(data['LW'][$('#canal').val()][$('#servicio').val()]);
        bars.series[3].setData(data['LY'][$('#canal').val()][$('#servicio').val()]);
        //bars.series[1].setData(data['1bf'][$('#canal').val()][$('#servicio').val()]);
        //bars.series[4].setData(data['2bf'][$('#canal').val()][$('#servicio').val()]);
        bars.setTitle({text: $('#canal option:selected').text()});
        
       
    }

    function sendRequest(date,date_to){
        $.ajax({
            url: "mon_getdataServ.php",
            type: 'POST',
            data:  {from: date, to: date_to},
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                data=array;
                redrawChart(data);
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
    
    $('#bars').highcharts({
	        chart: {
	        	type: 'line'
	        },
	        
	        title: {
	            text: "Calls"
	        },
	        xAxis: {
	            categories: [<?php
	            	for($i=0;$i<96;$i++){
	            		$hora=$i/4;
	            		echo "'".floor($i/4).":".(($i/4-floor($i/4))*60)."',";
	            	}
	            	?>]
	        },
	         yAxis: {
	            title: {
	                text: 'Monto (MXN)'
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
	        series: [{
	        	name: 'Today',
	        	data: [0]
	        	
	        },{
	        	name: '-1 Day',
	        	dashStyle: 'Dot',
	        	data: [0]
	        	
	        }, {
	        	name: '-7 Day',
	        	dashStyle: 'Dot',
	        	data: [0]
	        }, {
	        	name: '-1 Year',
	        	dashStyle: 'Dot',
	        	data: [0]
	        }/*, {
	        	name: '-1 Year',
	        	dashStyle: 'Dot',
	        	data: [0]
	        }, {
	        	name: '2 BF 2016',
	        	dashStyle: 'Dot',
	        	data: [0]
	        }*/]
	    });
	
		bars=$('#bars').highcharts();
		
		$('#productos').click(function(){
			window.location.replace('indexServ.php');
		});
		
		$('#montostotales').click(function(){
			window.location.replace('index.php');
		});


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
	<select id='canal'>
		<option value='All' selected>All</option>
		<option value='ibMP'>IN</option>
		<option value='us'>OUT</option>
		<option value='ol'>Online</option>
		<option value='PDV'>PDV</option>
		<option value='PTMX'>PT MX</option>
	</select>
	<select id='servicio'>
		<option value='Hotel' selected>Hotel</option>
		<option value='Vuelo'>Vuelo</option>
		<option value='Paquete'>Paquete</option>
	</select>
	<input type='text' id='from' name='from' value='<?php echo $from; ?>' required><input type='text' id='to' name='to' value='<?php echo $to; ?>' required>
	<button class='button button_blue_w' id='search'>Consultar</button>
	<button class='buttonlarge button_red_w' id='montostotales'>Ver Todo</button>
	<p id='lu'></p>

</div>
<br>
<div class='main' id='bars'>
</div>
<div id='error'></div>

<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

include("../connectDB_cyc.php");
include("../common/scripts.php");

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


//Declare Departs
$departamentos=["Ventas","VentasMP","SAC","TMP","TMT","Agencias"];


switch($depart){
    case "Ventas":
        $q="224|227|232|234|259|207|206|208";
        $aht=550;
        $comida=1800;
        $dep=3;
        break;
    case "VentasMP":
        $q="207|206|208";
        $aht=550;
        $comida=1800;
        $dep=35;
        break;
    case "VentasMT":
        $q="224|227|232|234|259|";
        $aht=550;
        $comida=1800;
        break;
    case "SAC":
        $q="226|229|233|235|230|666";
        $aht=600;
        $comida=1800;
        $dep=4;
        break;
    case "Agencias":
        $q="222|223";
        $aht=550;
        $comida=3600;
        $dep=7;
        break;
    case "TMP":
        $q="231";
        $aht=300;
        $comida=1800;
        $dep=9;
        break;
    case "TMT":
        $q="236";
        $aht=241;
        $comida=3600;
        $dep=8;
        break;
   case "Upsell":
        $dep=5;
        $aht=261;
        $comida=1800;
        break;
}

?>
<script src="/js/highcharts/stock/highstock.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>
<script src="/js/export-csv/export-csv.js"></script>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>

<script>
$(function(){
    $('#from').periodpicker({
    	norange: true,
		end: '#to',
		lang: 'en',
		animation: true
	});
});
</script>


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
        $('.container').css('width',(w*0.9)/2);
        $('.main').css('height',h*0.85);
        $('.container').css('height',(h*0.85)/2);
    }

    resizeDiv();

    $( window ).resize(function() {
      resizeDiv();
    });

    //AJAX for DataGet
    date='<?php echo $from; ?>';
    function sendRequest(flag_ok_1,date){
        $.ajax({
            url: "http://wfm.pricetravel.com.mx/monitor-gtr/mon_getdata.php",
            type: 'GET',
            data:  {fecha: date},
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                data=array;
                drawChart(flag_ok_1,'SLA '+date,'SLA %',' %','SLA_chart',data['lu'],data['sla']['Ventas'], data['sla']['VentasMP'], data['sla']['SAC'], data['sla']['TMP'], data['sla']['TMT'], data['sla']['Agencias'] );
                drawChart(flag_ok_1,'AHT '+date,'segundos',' seg','AHT_chart',data['lu'],data['aht']['Ventas'], data['aht']['VentasMP'], data['aht']['SAC'], data['aht']['TMP'], data['aht']['TMT'], data['aht']['Agencias'] );
                //drawChart(flag_ok_1,'Precision de Pronostico '+date,'Precision %',' %','PRON_chart',data['lu'],data['prec']['Ventas'], data['prec']['VentasMP'], data['prec']['SAC'], data['prec']['TMP'], data['prec']['TMT'], data['prec']['Agencias'] );
                drawChart(flag_ok_1,'Llamadas Abandonadas '+date,'Llamadas',' %','PRON_chart',data['lu'],data['abandon']['Ventas'], data['abandon']['VentasMP'], data['abandon']['SAC'], data['abandon']['TMP'], data['abandon']['TMT'], data['abandon']['Agencias'] );
                drawChart(flag_ok_1,'Volumen '+date,'Llamadas','','ADH_chart',data['lu'],data['vol']['Ventas'], data['vol']['VentasMP'], data['vol']['SAC'], data['vol']['TMP'], data['vol']['TMT'], data['vol']['Agencias'] );
           
           }
            
        });


    }

    sendRequest(0,date);

    tout=60;

    setInterval(function(){
       
       if(tout==0){sendRequest(1,date); tout=60;}else{tout=tout-1; 
       	//console.log(tout);
       	}
           
    },1000);
    
    function drawChart(flag_ok,title,yaxis,suffix,container,lu,<?php

                foreach($departamentos as $index => $info){
                    echo "data$index";
                    if($index!=count($departamentos)-1){
                        echo ",";
                    }
                }
                unset($index,$info);
            ?>){
            	
         //Build Graph
         $('#'+container).highcharts({
            title: {
                text: title+' <?php echo $date; ?>',
                x: -20 //center
            },
            subtitle: {
                text: 'Last-Update: '+lu,
                x: -20
            },
            xAxis: {
                categories: [<?php

                for($i=0;$i<24;$i+=0.5){
                    echo "'$i',";
                }
            ?>
            ]
            },
            yAxis: {
                title: {
                    text: yaxis
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: suffix,
                crosshairs: true
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [
            <?php

                foreach($departamentos as $index => $info){
                    echo "{";
                        echo "name: '$info',\n\t"
                            ."data: data$index\n\t";
                    echo "}";
                    if($index!=count($departamentos)-1){
                        echo ",";
                    }
                }
                unset($index,$info);
            ?>
            ]

        });

        if(flag_ok==1){
        $('.series_ch').each(function(index){
            var serie = $(this).attr('indice');
//console.log(serie);
            var flag=$(this).attr('flag');
            var chart1=$('#SLA_chart').highcharts();
            var chart2=$('#AHT_chart').highcharts();
            var chart3=$('#PRON_chart').highcharts();
            var chart4=$('#ADH_chart').highcharts();
            var series1=chart1.series[serie];
            var series2=chart2.series[serie];
            var series3=chart3.series[serie];
            var series4=chart4.series[serie];
            if(flag==0){
                series1.hide();
                series2.hide();
                series3.hide();
                series4.hide();
            }
        });
        }


	}

    $('.series_ch').click(function(){
        var chart1=$('#SLA_chart').highcharts();
        var chart2=$('#AHT_chart').highcharts();
        var chart3=$('#PRON_chart').highcharts();
        var chart4=$('#ADH_chart').highcharts();
        var name=$(this).attr('name');
        var serie=$(this).attr('indice');
        var id="series_ch_"+serie;
        var series1=chart1.series[serie];
        var series2=chart2.series[serie];
        var series3=chart3.series[serie];
        var series4=chart4.series[serie];
        if(series1.visible){
            series1.hide();
            series2.hide();
            series3.hide();
            series4.hide();
            $('#'+id).html(name+ 'Off').removeClass('button_red_w').addClass('button_blue_w').attr('flag',0);
        }else{
            series1.show();
            series2.show();
            series3.show();
            series4.hide();
            $('#'+id).html(name+ 'On').removeClass('button_blue_w').addClass('button_red_w').attr('flag',1);
        }
    })
    
    $("#search").click(function(){
    	//alert(date + " // " + $('#from').val());
    	date=$('#from').val();
    	sendRequest(1,date);	
    });
    
	


});

</script>

<style>
.main{
    width:auto;
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
<?php

foreach($departamentos as $index => $info){
    echo "<button class='button_large button_red_w series_ch' id='series_ch_$index' indice='$index' name='$info' flag='1'>$info On</button>";
}

?>
</div>
<br>
<div class='main'>
    <div class='container' id='SLA_chart'></div>
    <div class='container' id='AHT_chart'></div>
    <div class='container' id='ADH_chart'></div>
    <div class='container' id='PRON_chart'></div>
</div>
<div id='error'></div>

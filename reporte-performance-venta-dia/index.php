<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential='performance_dia_ventas';
$menu_asesores="class='active'";
date_default_timezone_set('America/Bogota');
header('Content-Type: text/html; charset=utf-8');
include("../connectDB.php");
?>



<?php
include("../common/scripts.php");
include("../DBFcventas.php");
?>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>

<script>
google.load('visualization', '1', {packages: ['corechart', 'bar']});
google.charts.load('current', {packages:['corechart']});
google.setOnLoadCallback(Llamadas);
google.charts.setOnLoadCallback(Monto);
google.setOnLoadCallback(FC);
google.setOnLoadCallback(AHT);


function Llamadas() {
    var options = {
        title:'Montos',
        titlePosition: 'in',
        animation:{
            easing: 'inAndOut'
        },
	    chartArea: {width: '80%', height: '80%', top: '5%'},
        hAxis: {
            gridlines: {
                count: -1,
                color: '#000066',
            },
        },
        vAxis: {
            format: 'decimal',
        },
        legend: {position:'in'},
        focusTarget: 'category',
        tooltip: {
            showColorCode: true,

        },};

    var chart = new google.visualization.ColumnChart(document.getElementById('llamadas'));

    function drawChart(){
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Asesor');
        data.addColumn('number', 'Llamadas');
        data.addColumn('number', 'Reservas');

        data.addRows([
          <?php

            foreach($ncorto as $key => $asesor){
                if($calls[$key]==NULL){$call=0;}else{$call=$calls[$key];}
                if($locs[$key]==NULL){$loc=0;}else{$loc=$locs[$key];}
                echo "['$asesor',$call, $loc],\n";
            }
            unset($key,$asesor);
          ?>

      ]);

        chart.draw(data, options);
	}

    drawChart();
}

function Monto() {
    var options = {
        title:'Montos',
        titlePosition: 'in',
        animation:{
            easing: 'inAndOut'
        },
	    chartArea: {width: '80%', height: '80%', top: '5%'},
        hAxis: {
            gridlines: {
                count: -1,
                color: '#000066',
            },
        },
        vAxis: {
            format: 'currency',
        },
        legend: {position:'in'},
        focusTarget: 'category',
        tooltip: {
            showColorCode: true,

        },
        };

    var chart = new google.visualization.ColumnChart(document.getElementById('monto'));

    function drawChart(){
    var data = new google.visualization.DataTable();
          data.addColumn('string', 'Asesor');
          data.addColumn('number', 'Total');
          data.addColumn('number', 'MP');

          data.addRows([
          <?php

            foreach($ncorto as $key => $asesor){
                if($monto[$key]==NULL){$montoall=0;}else{$montoall=$monto[$key];}
                if($mmp[$key]==NULL){$montomp=0;}else{$montomp=$mmp[$key];}
                echo "['$asesor',".number_format($montoall,2,'.','').",".number_format($montomp,2,'.','')."],\n";
            }
            unset($key,$asesor);
          ?>

      ]);

        var formatter = new google.visualization.NumberFormat(
         {negativeColor: 'red', negativeParens: true, pattern: '$###,###.##'});
        formatter.format(data, 1);
        formatter.format(data, 2);

        chart.draw(data, options);
	}

    drawChart();
}

function FC() {
    var options = {
        title:'Montos',
        titlePosition: 'in',
        animation:{
            easing: 'inAndOut'
        },
	    chartArea: {width: '80%', height: '80%', top: '5%'},
        hAxis: {
            gridlines: {
                count: -1,
                color: '#000066',
            },
        },
        vAxis: {
            format: 'percent',
        },
        legend: {position:'in'},
        focusTarget: 'category',
        tooltip: {
            showColorCode: true,

        },};

    var chart = new google.visualization.ColumnChart(document.getElementById('fc'));

    function drawChart(){
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Asesor');
        data.addColumn('number', 'FC');


        data.addRows([
          <?php

            foreach($ncorto as $key => $asesor){
                if($fc[$key]==NULL){$fcok=0;}else{$fcok=$fc[$key];}
                echo "['$asesor',$fcok],\n";
            }
            unset($key,$asesor);
          ?>

      ]);

        chart.draw(data, options);
	}

    drawChart();
}

function AHT() {
    var options = {
       title:'Montos',
        titlePosition: 'in',
        animation:{
            easing: 'inAndOut'
        },
	    chartArea: {width: '80%', height: '80%', top: '5%'},
        hAxis: {
            gridlines: {
                count: -1,
                color: '#000066',
            },
        },
        vAxis: {
            format: 'decimal',
        },
        legend: {position:'in'},
        focusTarget: 'category',
        tooltip: {
            showColorCode: true,

        },};

    var chart = new google.visualization.ColumnChart(document.getElementById('aht'));

    function drawChart(){
    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Asesor');
        data.addColumn('number', 'AHT');


        data.addRows([
          <?php

            foreach($ncorto as $key => $asesor){
                if($aht[$key]==NULL){$ahtok=0;}else{$ahtok=$aht[$key];}
                echo "['$asesor',$ahtok],\n";
            }
            unset($key,$asesor);
          ?>

      ]);

        chart.draw(data, options);
	}

    drawChart();
}
</script>
<script type="text/javascript">
  $(document).ready(function() {
        //move he last list item before the first item. The purpose of this is if the user clicks to slide left he will be able to see the last item.
        $('#carousel_ul li:first').before($('#carousel_ul li:last'));


        //when user clicks the image for sliding right
        $('#right_scroll img').click(function(){

            //get the width of the items ( i like making the jquery part dynamic, so if you change the width in the css you won't have o change it here too ) '
            var item_width = $('#carousel_ul li').outerWidth() + 10;

            //calculae the new left indent of the unordered list
            var left_indent = parseInt($('#carousel_ul').css('left')) - item_width;

            //make the sliding effect using jquery's anumate function '
            $('#carousel_ul:not(:animated)').animate({'left' : left_indent},500,function(){

                //get the first list item and put it after the last list item (that's how the infinite effects is made) '
                $('#carousel_ul li:last').after($('#carousel_ul li:first'));

                //and get the left indent to the default -210px
                $('#carousel_ul').css({'left' : '-1100px'});
            });
        });

        //when user clicks the image for sliding left
        $('#left_scroll img').click(function(){

            var item_width = $('#carousel_ul li').outerWidth() + 10;

            /* same as for sliding right except that it's current left indent + the item width (for the sliding right it's - item_width) */
            var left_indent = parseInt($('#carousel_ul').css('left')) + item_width;

            $('#carousel_ul:not(:animated)').animate({'left' : left_indent},500,function(){

            /* when sliding to left we are moving the last item before the first list item */
            $('#carousel_ul li:first').before($('#carousel_ul li:last'));

            /* and again, when we make that change we are setting the left indent of our unordered list to the default -210px */
            $('#carousel_ul').css({'left' : '-1100px'});
            });


        });
  });
</script>
<style type="text/css">

#carousel_inner {
/*float:left;  important for inline positioning */
margin: auto;
width:1100px; /* important (this width = width of list item(including margin) * items shown */
overflow: hidden;  /* important (hide the items outside the div) */
/* non-important styling bellow */
background: #F0F0F0;
}

#carousel_ul {
position:relative;
left:-1100px; /* important (this should be negative number of list items width(including margin) */
list-style-type: none; /* removing the default styling for unordered list items */
margin: 0px;
padding: 0px;
width:9999px; /* important */
/* non-important styling bellow */
padding-bottom:10px;
}

#carousel_ul li{
float: left; /* important for inline positioning of the list items */
width:1090px;  /* fixed width, important */
/* just styling bellow*/
padding:0px;
height:520px;
background: #000000;
margin-top:10px;
margin-bottom:10px;
margin-left:5px;
margin-right:5px;
}

#carousel_ul li img {
.margin-bottom:-4px; /* IE is making a 4px gap bellow an image inside of an anchor (<a href...>) so this is to fix that*/
/* styling */
cursor:pointer;
cursor: hand;
border:0px;
}
#left_scroll, #right_scroll{
float:left;
height:540px;
width:15px;
background: #C0C0C0;
}
#left_scroll img, #right_scroll img{
/*styling*/
cursor: pointer;
cursor: hand;
}
</style>


<?php
include("../common/menu.php");

?>
<table width='100%' class='t2'>
    <tr class='title'>
        <th>Performance del dia: Ventas</th>
    </tr>
    <tr class='title'>
        <th><b>Ultima Actualizacion: </b><span id='last_update'></span></th>
    </tr>
</table>
<br>

<div id='carousel_container'>
  <div id='carousel_inner'>
        <div id='left_scroll' style="float: left;position: relative;width: 30px;height: 0;background: navy;top: 270px;z-index: 1000;"><img src='/images/left_arrow.png' /></div>
        <div id='right_scroll' style="float: right;position: relative;width: 30px;height: 0;background: navy;top: 270px;z-index: 1000;"><img src='/images/right-arrow.png' /></div>
        <ul id='carousel_ul'>
            <li  id='llamadas'></li>
            <li  id='monto'></li>
            <li  id='fc'></li>
            <li  id='aht'></li>
        </ul>
  </div>

</div>


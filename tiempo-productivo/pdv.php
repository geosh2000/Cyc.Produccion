<?php
include("../connectDB.php");




?>
<style>
#performance{
    margin:auto;
    width:950px;
    height:400px;
    background: navy;
}

</style>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>

<script>

setInterval(function(){ window.location.reload(); }, 300000);

$(function () {

    var optionsmonto={
        title: {
            text: 'Avance Diario de Venta - <?php echo $name; ?>',
            x: -20 //center
        },
        xAxis: {
            tickPixelInterval: 150,
            title: {
                text: 'Date'
            }
        },
        yAxis: [{

            min:0,
            tickInterval: 1000000,
            title: {
                text: 'Monto Acumulado ($)'
            },
            
        },{
            title: {
                text: 'Monto Diario ($)'
            },
            opposite: true,
            
        }],
        tooltip: {
            valueSuffix: ''
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: []
    };

    var montochart=  $('#monto').highcharts(optionsmonto);
    $.getJSON('query_pdv.php?type=montos&asesor=<?php echo $asesor; ?>&fechai=<?php echo $startmonth; ?>&fechaf=<?php echo $endmonth; ?>&mdt=<?php echo $metadt; ?>&mt=<?php echo $meta; ?>', function (activity) {
        $.each(activity.datasets, function (i, dataset) {
            optionsmonto.series.push({
                type: dataset.type,
                name: dataset.name,
                data: dataset.data,
                yAxis: dataset.yAxis,
                color: dataset.color,
                marker: dataset.marker
            });
        });
        var montochart=  $('#monto').highcharts(optionsmonto);
        $('#monto').highcharts().xAxis[0].setCategories(dataget.xData[0].categories);
    });






});
</script>
<script type="text/javascript">
  
</script>
<style type="text/css">

.carousel_inner {
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
height:400px;
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
height:150px;
width:15px;
background: #C0C0C0;
}
#left_scroll img, #right_scroll img{
/*styling*/
cursor: pointer;
cursor: hand;
}
</style>

<div class='carousel_container'>
  <div class='carousel_inner'  id='monto'>
        
  </div>

</div>

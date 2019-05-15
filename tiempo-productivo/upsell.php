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

    var optionsfc={
        title: {
            text: 'Avance Diario de FC - <?php echo $name; ?>',
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
            title: {
                text: 'FC Acumulado (%)'
            },
            plotLines: [{
                dashStyle:'ShortDot',
                width: 4,
                color: '#CC5120',
                value:13,
                label:{
                    text: 'FC Nivel 1',
                    style: {
                        color: '#606060'
                    },
                zIndex:100
                }
            },{
                dashStyle:'ShortDot',
                width: 4,
                color: '#F2E47D',
                value:15,
                label:{
                    text: 'FC Nivel 2',
                    style: {
                        color: '#606060'
                    },
                zIndex:100
                }
            },{
                dashStyle:'ShortDot',
                width: 4,
                color: '#68CC39',
                value:17,
                label:{
                    text: 'FC Nivel 3',
                    style: {
                        color: '#606060'
                    },
                zIndex:100
                }
            }]
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

    var optionsmonto={
        title: {
            text: 'Avance Diario de Venta - <?php echo $name; ?>',
            x: -20 //center
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150,
            title: {
                text: 'Date'
            }
        },
        yAxis: [{

            max:<?php echo $meta*1.5; ?>,
            min:0,
            tickInterval: 1000000,
            title: {
                text: 'Monto Acumulado ($)'
            },
            plotLines: [{
                dashStyle:'ShortDot',
                width: 4,
                color: '#6BCC3D',
                value:<?php echo $meta; ?>,
                label:{
                    text: 'Meta del Mes',
                    style: {
                        color: '#606060'
                    }
                },
                zIndex:100
            }]
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
    var fcchart= $('#fc').highcharts(optionsfc)
    $.getJSON('query_upsell.php?type=montos&asesor=<?php echo $asesor; ?>&fechai=<?php echo $startmonth; ?>&fechaf=<?php echo $endmonth; ?>&mdt=<?php echo $metadt; ?>&mt=<?php echo $meta; ?>', function (activity) {
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

    $.getJSON('query_upsell.php?type=fc&asesor=<?php echo $asesor; ?>&fechai=<?php echo $startmonth; ?>&fechaf=<?php echo $endmonth; ?>', function (activity) {
        $.each(activity.datasets, function (i, dataset) {
            optionsfc.series.push({
                type: dataset.type,
                name: dataset.name,
                data: dataset.data,
                yAxis: dataset.yAxis
            });
        });
        var fcchart= $('#fc').highcharts(optionsfc);
        $('#fc').highcharts().xAxis[0].setCategories(dataget.xData[0].categories);
    });




});
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

<div id='carousel_container'>
  <div id='carousel_inner'>
        <div id='left_scroll' style="float: left;position: relative;width: 30px;height: 0;background: navy;top: 150px;z-index: 1000;"><img src='/images/left_arrow.png' /></div>
        <div id='right_scroll' style="float: right;position: relative;width: 30px;height: 0;background: navy;top: 150px;z-index: 1000;"><img src='/images/right-arrow.png' /></div>
        <ul id='carousel_ul'>
            <li  id='monto'></li>
            <li  id='fc'></li>
        </ul>
  </div>

</div>

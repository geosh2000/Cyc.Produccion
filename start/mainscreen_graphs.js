//setInterval(function(){ window.location.reload(); }, 300000);

$(function () {

    var optionsfc={
        title: {
            text: 'Avance Diario de FC - '+nameasesor,
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
            }
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
            text: 'Avance Diario de Venta - '+nameasesor,
            x: -20 //center
        },
        xAxis: {
            tickPixelInterval: 150,
            title: {
                text: 'Date'
            }
        },
        yAxis: [{

            max: metamax,
            min:0,
            tickInterval: 1000000,
            title: {
                text: 'Monto Acumulado ($)'
            },
            plotLines: [{
                dashStyle:'ShortDot',
                width: 4,
                color: '#6BCC3D',
                value: metamonto,
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
    
    $.getJSON(querymonto, function (activity) {
    	dataget=activity;
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

    $.getJSON(queryfc, function (activity) {
    dataget=activity;
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
    
    function updateData(){
    
      function getMonto(){
        $.ajax({
          url: query,
          type: 'GET',
          data: {type: 'montos', dep: dep, asesor: thisasesor, fechai: fechai, fechaf: fechaf, mdt: mdt, mt: mt},
          dataType: 'json',
          success: function(array){
             data=array;
             $('#monto').highcharts().series[0].setData(data['datasets'][0]['data']);
             $('#monto').highcharts().series[1].setData(data['datasets'][1]['data']);
             $('#monto').highcharts().series[2].setData(data['datasets'][2]['data']);
             $('#monto').highcharts().series[3].setData(data['datasets'][3]['data']);
          }
        });
      }
      
      function getFC(){
        $.ajax({
          url: query,
          type: 'GET',
          data: {type: 'fc', dep: dep, asesor: thisasesor, fechai: fechai, fechaf: fechaf, mdt: mdt, mt: mt},
          dataType: 'json',
          success: function(array){
             data=array;
             $('#fc').highcharts().series[0].setData(data['datasets'][0]['data']);
             $('#fc').highcharts().series[1].setData(data['datasets'][1]['data']);
             $('#fc').highcharts().series[2].setData(data['datasets'][2]['data']);
          }
        });
      }
      
      setInterval(function(){
          getMonto();
          getFC();
        },60000);
    
    }
    
     setTimeout(function(){
        updateData();
      },15000);


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
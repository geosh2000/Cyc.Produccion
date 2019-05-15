<?php
include_once('../modules/modules.php');

initSettings::start(true,'monitor_gtr');
initSettings::printTitle('Participación CC en MP');

$connectdb=Connection::mysqliDB('CC');

$group[]='CC';

$query="SELECT DISTINCT cc FROM cc_apoyo WHERE '".date('Y-m-d')."' BETWEEN inicio AND fin";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $group[]=$fila['cc'];
  }
}

$group[]='PDV';

$title=implode(' | ',$group);
?>
<style>
  .charts{
    display: inline-block;
    width: 600px;
    height: 400px;
    margin: 0 auto;
  }
</style>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script>
$(function(){
  i=1;

  function getData(){

    console.log("inicio get: "+i);

    i++;

    $.ajax({
      url: 'getPart.php',
      type: 'POST',
      data: {fecha: 'hoy'},
      dataType: 'json',
      success: function(array){
        data=array;

        if(data['status']==1){
          console.log('info ok');

          partChart.setTitle({ text: 'Participación de Llamadas <?php echo $title; ?><br>Total Llamadas: ' + data['Total'] + '<br>' + data['lu']});

          <?php
            foreach ($group as $key => $value) {
              $partChart.="partChart.series[0].points[$key].update(parseFloat(data['$value']['part']));\n";
              $calls.="$('#$value').text(data['$value']['llamadas']);\n";
              $gauges.="chart$value.series[0].points[0].update(parseFloat(data['$value']['FC']));\n";
              $locsChart.="locsChart.series[3].points[$key].update(parseInt(data['$value']['LocsHotel']));\n
                            locsChart.series[2].points[$key].update(parseInt(data['$value']['LocsVuelo']));\n
                            locsChart.series[1].points[$key].update(parseInt(data['$value']['LocsPaquete']));\n
                            locsChart.series[0].points[$key].update(parseInt(data['$value']['LocsOtros']));\n";
              $montoChart.="montoChart.series[3].points[$key].update(parseFloat(data['$value']['Hotel']));\n
                            montoChart.series[2].points[$key].update(parseFloat(data['$value']['Vuelo']));\n
                            montoChart.series[1].points[$key].update(parseFloat(data['$value']['Paquetes']));\n
                            montoChart.series[0].points[$key].update(parseFloat(data['$value']['Otros']));\n";
              $locsChartPG.="locsChartPG.series[$key].points[0].update(parseInt(data['$value']['LocsHotel']));\n
                              locsChartPG.series[$key].points[1].update(parseInt(data['$value']['LocsVuelo']));\n
                              locsChartPG.series[$key].points[2].update(parseInt(data['$value']['LocsPaquete']));\n
                              locsChartPG.series[$key].points[3].update(parseInt(data['$value']['LocsOtros']));\n";
              $montoChartPG.="montoChartPG.series[$key].points[0].update(parseFloat(data['$value']['Hotel']));\n
                              montoChartPG.series[$key].points[1].update(parseFloat(data['$value']['Vuelo']));\n
                              montoChartPG.series[$key].points[2].update(parseFloat(data['$value']['Paquetes']));\n
                              montoChartPG.series[$key].points[3].update(parseFloat(data['$value']['Otros']));\n";
            }

            echo $partChart;
            echo $calls;
            echo $gauges;
            echo $locsChart;
            echo $montoChart;
            echo $locsChartPG;
            echo $montoChartPG;
          ?>

          $('#total').text(data['total']);

          if(parseFloat(data['pdv'])>=25){
            if(parseFloat(data['pdv'])>=30){
              $('#pdv').css('color','red');
            }else{
              $('#pdv').css('color','#ffa700');
            }
          }else{
            $('#pdv').css('color','blue');
          }


        }else{
          console.log('info with error');
        }
      },
      error: function(){
        console.log('error de conexion');
      }
    });


  }

  getData();

  timer=60;

  setInterval(function(){
    $('#timer').text(timer);
    timer--;
    if(timer==0){
      timer=60;
      getData();
    }

  },1000);

  //HIGHCHART
  // Radialize the colors
  Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
      return {
          radialGradient: {
              cx: 0.5,
              cy: 0.3,
              r: 0.7
          },
          stops: [
              [0, color],
              [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
          ]
      };
  });

  // Build the chart
  partChart = Highcharts.chart('container', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
      },
      title: {
          text: 'Participación de Llamadas CC | Mixcoac | PDV'
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
                  },
                  connectorColor: 'silver'
              }
          }
      },
      series: [{
          name: 'Departamentos',
          data: [
            <?php
              foreach ($group as $key => $value) {
                $series.= "{ name: '$value', y: 0 },";
              }

              echo SUBSTR($series,0,-1);
            ?>
          ]
      }]
  });

  //FC Gauge
  var gaugeOptions = {

        chart: {
            type: 'solidgauge'
        },

        title: null,

        pane: {
            center: ['50%', '85%'],
            size: '140%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: false
        },

        // the value axis
        yAxis: {
            stops: [
                [0.1, '#DF5353'], // red
                [0.18, '#DDDF0D'], // yellow
                [1, '#55BF3B'] // green
            ],
            lineWidth: 0,
            minorTickInterval: null,
            tickAmount: 2,
            title: {
                y: -70
            },
            labels: {
                y: 16
            }
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    <?php
      foreach ($group as $key => $value) {
        $gauge.="var chart$value = Highcharts.chart('fc-$value', Highcharts.merge(gaugeOptions, {\n
                  yAxis: {\n
                      min: 0,\n
                      max: 100,\n
                      title: {\n
                          text: '$value'\n
                      }\n
                  },\n\n

                  credits: {\n
                      enabled: false\n
                  },\n

                  series: [{\n
                      name: '$value',\n
                      data: [0],\n
                      dataLabels: {\n
                          format: '<div style=\"text-align:center\"><span style=\"font-size:25px;color:' +\n
                              ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '\">{y}</span><br/>' +\n
                                 '<span style=\"font-size:12px;color:silver\">locs/calls</span></div>'\n
                      },\n
                      tooltip: {\n
                          valueSuffix: ' locs/calls'\n
                      }\n
                  }]\n

              }));\n";
      }

      echo $gauge;
    ?>


    //Locs chart
    locsChart=Highcharts.chart('container-locs', {
      chart: {
          type: 'bar'
      },
      title: {
          text: 'Localizadores (por tipo)'
      },
      xAxis: {
          categories: [<?php
              foreach ($group as $key => $value) {
                $cat.="'$value',";
                $initVals.="0,";
                $PGcat.="{
                            name: '$value',
                            data: [0,0,0,0]
                        },\n";
              }
              $initVals=SUBSTR($initVals,0,-1);
              echo SUBSTR($cat,0,-1);
           ?>]
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Localizadores'
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
          bar: {
              stacking: 'normal',
              dataLabels: {
                  enabled: true,
                  color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
              }
          }
      },
      series: [{
          name: 'Otros',
          data: [<?php echo $initVals; ?>]
      }, {
          name: 'Paquete',
          data: [<?php echo $initVals; ?>]
      }, {
          name: 'Vuelo',
          data: [<?php echo $initVals; ?>]
      }, {
          name: 'Hotel',
          data: [<?php echo $initVals; ?>]
      }]
  });

  //Monto chart
  montoChart=Highcharts.chart('container-monto', {
      chart: {
          type: 'bar'
      },
      title: {
          text: 'Monto (por tipo)'
      },
      xAxis: {
          categories: [<?php
              echo SUBSTR($cat,0,-1);
           ?>]
      },
      yAxis: {
          min: 0,
          title: {
              text: '$'
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
          pointFormat: '{series.name}: ${point.y}<br/>Total: ${point.stackTotal}'
      },
      plotOptions: {
          bar: {
              stacking: 'normal',
              dataLabels: {
                  enabled: false,
                  color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',

              }
          }
      },
      series: [{
          name: 'Otros',
          data: [<?php echo $initVals; ?>]
      }, {
          name: 'Paquete',
          data: [<?php echo $initVals; ?>]
      }, {
          name: 'Vuelo',
          data: [<?php echo $initVals; ?>]
      }, {
          name: 'Hotel',
          data: [<?php echo $initVals; ?>]
      }]
  });

  //Locs por Grupo chart
  locsChartPG=Highcharts.chart('container-locsPG', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Localizadores (por grupo)'
    },
    xAxis: {
        categories: ['Hotel', 'Vuelo', 'Paquete', 'Otros']
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Localizadores'
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
        bar: {
            stacking: 'normal',
            dataLabels: {
                enabled: true,
                color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
            }
        }
    },
    series:  [
      <?php
          echo SUBSTR($PGcat,0,-1);
       ?>
      ]
  });

  //Monto chart por grupo
  montoChartPG=Highcharts.chart('container-montoPG', {
  chart: {
      type: 'bar'
  },
  title: {
      text: 'Monto (por tipo)'
  },
  xAxis: {
      categories: ['Hotel', 'Vuelo', 'Paquete', 'Otros']
  },
  yAxis: {
      min: 0,
      title: {
          text: '$'
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
      pointFormat: '{series.name}: ${point.y}<br/>Total: ${point.stackTotal}'
  },
  plotOptions: {
      bar: {
          stacking: 'normal',
          dataLabels: {
              enabled: false,
              color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',

          }
      }
  },
  series: [
    <?php
        echo SUBSTR($PGcat,0,-1);
     ?>
    ]
  });
});
</script>
<br>
<div style='max-width:1210; margin: auto'>
  <div class='charts' id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
  <div class='charts' style="">
    <?php
      foreach ($group as $key => $value) {
        $divs.="<div id='fc-$value' style='width: 300px; height: 200px; float: left'></div>";
      }
      echo $divs;
    ?>
  </div>
</div>
<div style='max-width:1210; margin: auto'>
  <div class='charts' id="container-locs" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
  <div class='charts' id="container-monto" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
</div>
<div style='max-width:1210; margin: auto'>
  <div class='charts' id="container-locsPG" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
  <div class='charts' id="container-montoPG" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
</div>
<br><br>
<div style='text-align: center; font-size:70px; height:55'>Total <span id='total' style='color: blue; font-size: 50'></span> llamadas</div>
<br>
<div style='text-align: center; font-size:40px; height:40; line-height: normal'>
  <?php
    foreach ($group as $key => $value) {
      $callsTable.="$value -> <span id='$value' style='color: blue; font-size: 40'></span><br>";
    }
    echo $callsTable;
  ?>
</div>
<div id='timer'></div>
<?php $connectdb->close(); ?>

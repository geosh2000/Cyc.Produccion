<?php
include_once('../modules/modules.php');

initSettings::start(true);
initSettings::printTitle('Dashboard Outlet');

$connectdb=Connection::mysqliDB('CC');

$query="SELECT MAX(Last_Update) as LU FROM d_Locs";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $lu=$fila['LU'];
}

$query="SELECT Fecha, Hora_int, Hora_pretty, IF(dep=5,'OB',b.Canal) as Afiliado, COUNT(DISTINCT NewLoc) as Locs, SUM(VentaMXN+EgresosMXN+OtrosIngresosMXN) as Monto 
        FROM 
          HoraGroup_Table15 a 
        RIGHT JOIN 
          (SELECT 
            a.*, IF(Venta!=0,Localizador,NULL) as NewLoc, AfiliadoOK as Canal, dep 
          FROM 
            t_Locs a 
          LEFT JOIN chanIds b ON a.chanId=b.id 
          LEFT JOIN dep_asesores c ON a.asesor=c.asesor AND a.Fecha=c.Fecha
          WHERE 
            a.Fecha BETWEEN '2016-05-12' AND '2016-05-15' 
            AND ((Canal='MP'
            AND Pais='MX') OR chanId=355)) b 
          ON b.Hora BETWEEN a.Hora_time AND ADDTIME(a.Hora_time,'00:14:59') 
        LEFT JOIN
          chanIds c ON b.chanId=c.id
        GROUP BY 
          Fecha, Hora_pretty, b.Canal
         ORDER BY
          Fecha, Hora_int";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    switch($fila['Fecha']){
      case '2016-05-12':
        $fecha=1;
        break;
      case '2016-05-13':
        $fecha=2;
        break;
      case '2016-05-14':
        $fecha=3;
        break;
      case '2016-05-15':
        $fecha=4;
        break;
    }
    
    $afiliado[$fila['Afiliado']]=1;
    
    //Data Segmentada
    $data[$fila['Afiliado']][2016][$fecha][$fila['Hora_int']]['Locs']=$fila['Locs'];
    $data[$fila['Afiliado']][2016][$fecha][$fila['Hora_int']]['Monto']=$fila['Monto'];
    
    switch($fila['Afiliado']){
      case 'COM':
      case 'CC':
      case 'OB':
        @$data['Total COM'][2016][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
        break;
    }
    
    @$data['Total'][2016][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
    
    //Data Acumulada
      //Suma a la hora actual, el acumulado anterior
    @$data[$fila['Afiliado']][2016][$fecha][$fila['Hora_int']]['LocsAcum']=$fila['Locs']+$data[$fila['Afiliado']][$fecha]['LocsAcum'];
    @$data[$fila['Afiliado']][2016][$fecha][$fila['Hora_int']]['MontoAcum']=$fila['Monto']+$data[$fila['Afiliado']][$fecha]['MontoAcum'];
      //Actualiza el acumulado
    @$data[$fila['Afiliado']][2016][$fecha]['LocsAcum']+=$fila['Locs'];
    @$data[$fila['Afiliado']][2016][$fecha]['MontoAcum']+=$fila['Monto'];
    
    @$data['Total'][2016][$fecha][$fila['Hora_int']]['MontoAcum']+=$fila['Monto'];
    
  }
}

$afiliado['Total']=1;
$afiliado['Total COM']=1;

$query="SELECT Fecha, Hora_int, Hora_pretty, IF(dep=5,IF(outlet IS NULL,'OB',b.Canal),b.Canal) as Afiliado, COUNT(DISTINCT NewLoc) as Locs, SUM(VentaMXN+EgresosMXN+OtrosIngresosMXN) as Monto 
        FROM 
          HoraGroup_Table15 a 
        RIGHT JOIN 
          (SELECT 
            a.*, IF(Venta!=0,Localizador,NULL) as NewLoc, AfiliadoOK as Canal, dep, outlet 
          FROM 
            d_Locs a 
          LEFT JOIN chanIds b ON a.chanId=b.id 
          LEFT JOIN dep_asesores c ON a.asesor=c.asesor AND a.Fecha=c.Fecha
          LEFT JOIN asesores_outlet d ON a.asesor=d.asesor AND a.Fecha BETWEEN d.inicio AND d.fin
          WHERE 
            a.Fecha BETWEEN '2017-05-11' AND '2017-05-14' 
            AND ((Canal='MP'
            AND Pais='MX') OR chanId=355)) b 
          ON b.Hora BETWEEN a.Hora_time AND ADDTIME(a.Hora_time,'00:14:59') 
        LEFT JOIN
          chanIds c ON b.chanId=c.id
        GROUP BY 
          Fecha, Hora_pretty, b.Canal
         ORDER BY
          Fecha, Hora_int";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    switch($fila['Fecha']){
      case '2017-05-11':
        $fecha=1;
        break;
      case '2017-05-12':
        $fecha=2;
        break;
      case '2017-05-13':
        $fecha=3;
        break;
      case '2017-05-14':
        $fecha=4;
        break;
    }
    
    $afiliado[$fila['Afiliado']]=1;
    
    //Data Segmentada
    $data[$fila['Afiliado']][2017][$fecha][$fila['Hora_int']]['Locs']=$fila['Locs'];
    $data[$fila['Afiliado']][2017][$fecha][$fila['Hora_int']]['Monto']=$fila['Monto'];
    
    switch($fila['Afiliado']){
      case 'COM':
      case 'CC':
      case 'OB':
        @$data['Total COM'][2017][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
        break;
    }
    
    @$data['Total'][2017][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
    
    //Data Acumulada
      //Suma a la hora actual, el acumulado anterior
    @$data[$fila['Afiliado']][2017][$fecha][$fila['Hora_int']]['LocsAcum']=$fila['Locs']+$data[$fila['Afiliado']][$fecha]['LocsAcum'];
    @$data[$fila['Afiliado']][2017][$fecha][$fila['Hora_int']]['MontoAcum']=$fila['Monto']+$data[$fila['Afiliado']][$fecha]['MontoAcum'];
      //Actualiza el acumulado
    @$data[$fila['Afiliado']][2017][$fecha]['LocsAcum']+=$fila['Locs'];
    @$data[$fila['Afiliado']][2017][$fecha]['MontoAcum']+=$fila['Monto'];
    
    @$data['Total'][2017][$fecha][$fila['Hora_int']]['MontoAcum']+=$fila['Monto'];
    
  }
}

$query="SELECT * FROM HoraGroup_Table15 ORDER BY Hora_int";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $hg[$fila['Hora_int']]=substr($fila['Hora_time'],0,5);
  }
}

//Acumulados por hora
for($year=2016;$year<=2017;$year++){
  foreach($afiliado as $title => $info){
    $x=0;
    
    switch($title){
      case 'Outlet':
        $fact=1.3;
        break;
      case 'Total':
        $fact=1.3;
        break;
      default:
        $fact=1.3;
        break;
    }
    
    for($i=1;$i<=4;$i++){
      foreach($hg as $index => $time){
        if(isset($data[$title][$year][$i][$index]['Locs'])){$tmp_locs=$data[$title][$year][$i][$index]['Locs'];}else{$tmp_locs=0;}
        if(isset($data[$title][$year][$i][$index]['Monto'])){$tmp_monto=$data[$title][$year][$i][$index]['Monto'];}else{$tmp_monto=0;}
        
        $acum['Monto'][$title][$year][$i][$index]=$tmp_monto+$acum['Monto'][$title][$year][$i][($index-1)];
        $acum['Locs'][$title][$year][$i][$index]=$tmp_locs+$acum['Locs'][$title][$year][$i][($index-1)];
        
        $total['Monto'][$title][$year][$x]=$tmp_monto+$total['Monto'][$title][$year][($x-1)];
        $total['Locs'][$title][$year][$x]=$tmp_locs+$total['Locs'][$title][$year][($x-1)];
        
        $totalDay['Monto'][$title][$year][$i][$x]=$tmp_monto+$totalDay['Monto'][$title][$year][$i][($x-1)];
        $totalDay['Locs'][$title][$year][$i][$x]=$tmp_locs+$totalDay['Locs'][$title][$year][$i][($x-1)];
        
        $chartAcum['Monto'][$year][$title][]=$total['Monto'][$title][$year][$x];
        $chartAcum['Locs'][$year][$title][]=$total['Locs'][$title][$year][$x];
        
        
        
        $chartDay['Monto'][$year][$i][$title][]=$totalDay['Monto'][$title][$year][$i][$x];
        $chartDay['Locs'][$year][$i][$title][]=$totalDay['Locs'][$title][$year][$i][$x];
        
        if($year==2016){
          $metaAcum['Monto'][$title][]=$total['Monto'][$title][$year][$x]*$fact;
          $metaDay['Monto'][$i][$title][]=$totalDay['Monto'][$title][$year][$i][$x]*$fact;
        }
        
        $x++;
      }
    }
  }
}
//Create Categories
for($i=1;$i<=4;$i++){

  switch($i){
    case 1:
      $day='11/05';
      break;
    case 2:
      $day='12/05';
      break;
    case 3:
      $day='13/05';
      break;
    case 4:
      $day='14/05';
      break;
  }

  foreach($hg as $index => $time){
    $categories[]=$day." ".$time;
  }
}

function printPre($datos){
  echo "<pre>";
  print_r($datos);
  echo "</pre>";
  
  exit;
}

function json($var){
  echo json_encode($var);
}

//printPre($acum);

$connectdb->close();

?>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>
<script>

  dataAcum=<?php json($chartAcum); ?>;
  dataDay=<?php json($chartDay); ?>;
  metaDay=<?php json($metaDay); ?>;
  metaAcum=<?php json($metaAcum); ?>;
  categories=<?php json($categories); ?>;

</script>
<script>
$(function(){

  $( ".chkbx" ).checkboxradio();
  
  function printChartTotal(container, group, bg){

    Highcharts.chart(container, {
        chart: {
          backgroundColor: bg,
        },
         
        title: {
            text: 'Monto Acumulado '+group+' ($'+number_format(dataAcum['Monto'][2017][group][383],2,'.',',')+')',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Outlet Viaja y Vuela del 11 al 14 de Mayo',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        xAxis: {
            categories: categories,
            labels: {
                style: {
                    color: '#eff3ff'
                }
            },
            title: {
                text: null
            },
            plotBands: [{ // 12-05
                from: 96,
                to: 192,
                label: {
                    text: '12 Mayo',
                    align: 'right',
                    x: -10,
                    style: {
                        color: '#eff3ff'
                    }
                },
                borderColor: 'rgba(109, 145, 25, .2)',
                borderWidth: 4
            },
            { //11-05
                from: 0,
                to: 96,
                label: {
                    text: '11 Mayo',
                    align: 'right',
                    x: -10,
                    style: {
                        color: '#eff3ff'
                    }
                },
                borderColor: 'rgba(109, 145, 25, .2)',
                borderWidth: 4
            },
            { //13-05
                from: 192,
                to: 288,
                label: {
                    text: '13 Mayo',
                    align: 'right',
                    x: -10,
                    style: {
                        color: '#eff3ff'
                    }
                },
                borderColor: 'rgba(109, 145, 25, .2)',
                borderWidth: 4
            },
            { //14-05
                from: 288,
                to: 1000,
                label: {
                    text: '14 Mayo',
                    align: 'right',
                    x: -10,
                    style: {
                        color: '#eff3ff'
                    }
                },
                borderColor: 'rgba(109, 145, 25, .2)',
                borderWidth: 4
            }]
        },
        yAxis: {
            min: 0,
            title: {
                text: '$ MXN'
            },
            labels: {
                style: {
                    color: '#eff3ff'
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            backgroundColor: '#eff3ff'
        },
        plotOptions: {
        },
        series: [{
            name: '2017',
            data: dataAcum['Monto'][2017][group],
            color: 'rgb(244, 182, 66)',
        }, {
            name: '2016',
            data: dataAcum['Monto'][2016][group],
            color: 'rgb(126, 147, 252)',
        }, {
            name: 'Meta',
            data: metaAcum['Monto'][group],
            dashStyle: 'ShortDot',
            color: 'rgb(83, 160, 6)',
        }]
    });
    
  }
  
  function number_format (number, decimals, dec_point, thousands_sep) {
      // Strip all characters but numerical ones.
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
      var n = !isFinite(+number) ? 0 : +number,
          prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
          sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
          dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
          s = '',
          toFixedFix = function (n, prec) {
              var k = Math.pow(10, prec);
              return '' + Math.round(n * k) / k;
          };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
          s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
          s[1] = s[1] || '';
          s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }
  
  function printChart(container, group, bg, day){
  
    switch(day){
      case '1':
        fecha='11 de Mayo';
        break;
      case '2':
        fecha='12 de Mayo';
        break;
      case '3':
        fecha='13 de Mayo';
        break;
      case '4':
        fecha='14 de Mayo';
        break;
    }

    Highcharts.chart(container, {
        chart: {
          backgroundColor: bg,
          type: 'spline'
        },
         
        title: {
            text: 'Monto '+fecha+' '+group+' ($'+number_format(dataDay['Monto'][2017][day][group][95],2,'.',',')+')',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Outlet Viaja y Vuela del 11 al 14 de Mayo',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        xAxis: {
            categories: categories,
            labels: {
                style: {
                    color: '#eff3ff'
                }
            },
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: '$ MXN'
            },
            labels: {
                style: {
                    color: '#eff3ff'
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            backgroundColor: '#eff3ff'
        },
        plotOptions: {
          spline: {
            lineWidth: 2,
            states: {
                hover: {
                    lineWidth: 5
                }
            },
            marker: {
                enabled: false
            }
          }
        },
        series: [{
            name: '2017',
            data: dataDay['Monto'][2017][day][group],
            color: 'rgb(244, 182, 66)',
        }, {
            name: '2016',
            data: dataDay['Monto'][2016][day][group],
            color: 'rgb(126, 147, 252)',
        }, {
            name: 'Meta',
            data: metaDay['Monto'][day][group],
            dashStyle: 'ShortDot',
            color: 'rgb(83, 160, 6)',
        }]
    });
    
  }
  
  $( "#accordion" ).accordion({
    collapsible: true,
    active: false,
    heightStyle: "content"
  });
  
  function pTotal(){
    printChartTotal('COM','COM','#67686d');
    printChartTotal('PDV','PDV','#30333d');
    printChartTotal('CC','CC','#67686d');
    printChartTotal('Outlet','Outlet','#30333d');
    printChartTotal('OB','OB','#67686d');
    printChartTotal('Total','Total','#30333d');
    printChartTotal('Total COM','Total COM','#30333d');
    
    det=0;
  }
  
  function pDay(day){
    printChart('COM','COM','#67686d',day);
    printChart('PDV','PDV','#30333d',day);
    printChart('CC','CC','#67686d',day);
    printChart('Outlet','Outlet','#30333d',day);
    printChart('OB','OB','#67686d',day);
    printChart('Total','Total','#30333d',day);
    printChart('Total COM','Total COM','#30333d',day);
    
    det=day;
  }
  
  det=0;
  
  $('.chkbx').click(function(){
    dia=$(this).val();
    if(dia==0){
      pTotal();
    }else{
      pDay(dia);
    }
  });
  
  <?php
    if(isset($_GET['det'])){
      if($_GET['det']==0 || $_GET['det']==''){
        echo "pTotal()";
      }else{
        echo "pDay('".$_GET['det']."');\n";
        echo "$('#radio-".$_GET['det']."').attr('checked',true);";
        echo "$('#radio-0').attr('checked',false);";
        echo "$('.chkbx').checkboxradio('refresh');";
        echo "det=".$_GET['det'].";";
      }
    }else{
      echo "pTotal()";
    }
  ?>
  
  time=300;
  
  setInterval(function(){
    $('#timer').text(time--);
    if(time==0){
      location = '/outlet/dashboard.php?det='+det;
    }
  },1000);
});
</script>
<style>
  .container{
    width: 95%;
    max-width: 1200px;
    margin: auto;
    background: #30333d;
  }
  .container div{
    width: 100%;
  }
  
  body{
    background: #30333d;
  }
</style>
<fieldset>
    <legend style='color: white'>Rango</legend>
    <label for='radio-1'>11-may</label>
    <input class='chkbx' type='radio' name='radio-1' id='radio-1' value=1>
    <label for='radio-2'>12-may</label>
    <input class='chkbx' type='radio' name='radio-1' id='radio-2' value=2>
    <label for='radio-3'>13-may</label>
    <input class='chkbx' type='radio' name='radio-1' id='radio-3' value=3>
    <label for='radio-4'>14-may</label>
    <input class='chkbx' type='radio' name='radio-1' id='radio-4' value=4>
    <label for='radio-5'>Todo</label>
    <input class='chkbx' type='radio' name='radio-1' id='radio-5' value=0 checked>
  </fieldset>
<p style='color: white'>Last Update: <?php echo $lu; ?> || Reload in: <span id='timer'></span> sec.</p>
<div class='container'>
  <div class='hc' id="Total COM"></div>
  <div id="accordion">
    <h3>Detalle .COM</h3>
    <div style='width:100%; padding: 0' >
      <div class='hc' style='width:95%; max-width: 1200px; margin: 0' id="COM"></div>
      <div class='hc' style='width:95%; max-width: 1200px; margin: 0' id="CC"></div>
      <div class='hc' style='width:95%; max-width: 1200px; margin: 0' id="OB"></div>
    </div>
  </div>
  <div class='hc' id="PDV"></div>
  <div class='hc' id="Outlet"></div>
  <div class='hc' id="Total"></div>
</div>
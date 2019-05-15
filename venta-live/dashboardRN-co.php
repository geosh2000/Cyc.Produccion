<?php
include_once('../modules/modules.php');

initSettings::start(true);
initSettings::printTitle('Dashboard Outlet');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');


$afiliado['Total']=1;


$query="SELECT 
	Hora_int,
	CASE
		WHEN chanId IN (193,178,410,556) THEN 'PTCO'
		WHEN chanId IN (601,602,603,636,637,708,806) THEN 'TB'
		WHEN chanId IN (192,891) THEN 'PDV'
	END as Grupo,
	SUM(RN) as RN
FROM 
	(SELECT * FROM (SELECT * FROM d_hoteles a LEFT JOIN HoraGroup_Table15 b ON a.Hora BETWEEN b.Hora_time AND ADDTIME(b.Hora_time,'00:14:59') WHERE Fecha='2017-06-12' AND chanId IN (295,355,192,891,193,178,410,556,601,602,603,636,637,708,806) AND Venta!=0 ORDER BY Hora DESC) a GROUP BY Localizador) a
GROUP BY
Grupo, Hora_int HAVING Grupo IS NOT NULL ORDER BY Hora_int";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $data[$fila['Grupo']]['hora'][$fila['Hora_int']]=intval($fila['RN']);
    @$data['Total']['hora'][$fila['Hora_int']]+=intval($fila['RN']);
    @$data['Total'][$fila['Grupo']]+=intval($fila['RN']);
    @$data['Total']['Total']+=intval($fila['RN']);
  }
}

$query="SELECT * FROM HoraGroup_Table15 ORDER BY Hora_int";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $cat[$fila['Hora_int']]=$fila['Hora_pretty'];
    $categories[]=$fila['Hora_pretty'];
  }
}

foreach($data as $group => $infoData){
  foreach($categories as $index => $info){
    @$chartacum[$group]+=$infoData['hora'][$index];
    @$chart[$group][]+=$chartacum[$group];
  }
}

$connectdb->close();

?>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>
<script>

  dataDay=<?php echo json_encode($chart); ?>;
  categories=<?php echo json_encode($categories); ?>;
  totales=<?php echo json_encode($data['Total']); ?>;

</script>
<script>
$(function(){
  
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
    
    var titleMont = (typeof totales[group] === 'undefined') ? 0 : totales[group];
     
    Highcharts.chart(container, {
        chart: {
          backgroundColor: bg,
          type: 'spline'
        },
         
        title: {
            text: 'RN '+group+' ('+titleMont+')',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'CyberLunes',
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
                text: 'RN'
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
            data: dataDay[group],
            color: 'rgb(244, 182, 66)',
        }]
    });
    
  }
  
  function pDay(){
    printChart('TB','TB','#67686d');
    printChart('PTCO','PTCO','#30333d');
    printChart('PDV','PDV','#67686d');
    printChart('Total','Total','#67686d');
    
    
  }
  
  pDay();
  time=300;
  
  setInterval(function(){
    $('#timer').text(time--);
    if(time==0){
      location = '/venta-live/dashboardRN-co.php';
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

<p style='color: white'>Last Update: <?php echo $lu; ?> || Reload in: <span id='timer'></span> sec.</p>
<div class='hc' id="TB"></div>
<div class='hc' id="PTCO"></div>
<div class='hc' id="PDV"></div>
<div class='hc' id="Total"></div>

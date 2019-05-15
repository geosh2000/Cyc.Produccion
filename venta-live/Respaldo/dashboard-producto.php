<?php
include_once('../modules/modules.php');

initSettings::start(true);
initSettings::printTitle('Dashboard por Producto');

$connectdb=Connection::mysqliDB('CC');

function printPre($datos){
  echo "<pre>";
  print_r($datos);
  echo "</pre>";
  
  exit;
}

function json($var){
  echo json_encode($var);
}

$query="SELECT * FROM config_dashboard WHERE tag='main'";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $var['crecimiento']=$fila['crecimiento'];
  $var['ly']['inicio']=$fila['ly_inicio'];
  $var['ly']['fin']=$fila['ly_fin'];
  $var['td']['inicio']=$fila['td_inicio'];
  $var['td']['fin']=$fila['td_fin'];
  $cortes=explode("|",$fila['cortes']);
}else{
    echo "ERROR en tabla de configuración -> ".$connectdb->error." ON $query<br>";
}

$x=1;
for($i=date('Y-m-d', strtotime($var['ly']['inicio']));$i<=date('Y-m-d', strtotime($var['ly']['fin']));$i=date('Y-m-d',strtotime($i.' +1 day'))){
  $tmpdate=date('Y-m-d',strtotime($var['td']['inicio']." +".($x-1)." day"));
  $fecha_int[$i]=$x;
  $fecha_int[$tmpdate]=$x;
  $fecha_json[$x]=date('d-M',strtotime($tmpdate));
  @$radioprint.="<label for='radio-$x'>".date('d-M',strtotime($tmpdate))."</label>
                <input class='chkbx' type='radio' name='radio-1' id='radio-$x' value=$x>\n ";
  @$plotbands.="{ //$x
                  from: ".(($x-1)*96).",
                  to: ".(($x)*96).",
                  label: {
                      text: '".date('d-M',strtotime($tmpdate))."',
                      align: 'right',
                      x: -10,
                      style: {
                          color: '#eff3ff'
                      }
                  },
                  borderColor: 'rgba(109, 145, 25, .2)',
                  borderWidth: 4
              },";
  $x++;
}

foreach($cortes as $i => $info){
  $cortesOK[$i]=$fecha_int[date("2017-$info")];
}



$query="SELECT MAX(Last_Update) as LU FROM d_Locs";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $lu=$fila['LU'];
}

$query="SELECT canales FROM PtChannels";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $ptChannels=$fila['canales'];
}

$query="SELECT dashboard,query FROM monitor_kpiLive_modules WHERE pais='MX' AND dashboard!='MT'";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $tmptxt=$fila['query'];
    @$canales.=str_replace('$ptChannels',$ptChannels,str_replace("DepOK","dep",str_replace("a.","b.",substr($tmptxt,0,strpos($tmptxt,"'")+1).$fila['dashboard']."' ")));
    
  }
}

$afiliado['Total']=1;
$afiliado['Total COM']=1;

$query="DROP TEMPORARY TABLE IF EXISTS dash_td; 
        DROP TEMPORARY TABLE IF EXISTS td_dash; 
        DROP TEMPORARY TABLE IF EXISTS td_created; 
        DROP TEMPORARY TABLE IF EXISTS locs_shown; 
        DROP TEMPORARY TABLE IF EXISTS creators_td; 
        DROP TEMPORARY TABLE IF EXISTS dashboard_venta; 
        DROP TEMPORARY TABLE IF EXISTS rnights; 

        CREATE TEMPORARY TABLE creators_td 
                SELECT Localizador, asesor
            FROM 
                        t_Locs
                WHERE 
                        Fecha BETWEEN '".$var['td']['inicio']."' AND '".$var['td']['fin']."'
                GROUP BY Localizador; 

        ALTER TABLE creators_td ADD PRIMARY KEY (Localizador);

        INSERT INTO creators_td (SELECT * FROM (SELECT Localizador, asesor FROM d_Locs WHERE Fecha BETWEEN ADDDATE(CURDATE(),-1) AND CURDATE()) a ) ON DUPLICATE KEY UPDATE asesor=a.asesor; 

        CREATE TEMPORARY TABLE locs_shown 
                SELECT 
                    *,
					CASE
						WHEN isPaq IS NOT NULL AND isPaq!=0 THEN 'Paquete'
						WHEN categoryId=3 THEN 'Vuelo'
						WHEN categoryId=1 THEN 'Hotel'
						ELSE 'Otros'
					END as tipoRsva
            FROM 
                        t_hoteles 
                WHERE 
                        Fecha BETWEEN '".$var['td']['inicio']."' AND '".$var['td']['fin']."'; 

        ALTER TABLE locs_shown ADD PRIMARY KEY (Localizador, item, Venta, Fecha, Hora);

        INSERT INTO locs_shown (SELECT * FROM (SELECT *,
					CASE
						WHEN isPaq IS NOT NULL AND isPaq!=0 THEN 'Paquete'
						WHEN categoryId=3 THEN 'Vuelo'
						WHEN categoryId=1 THEN 'Hotel'
						ELSE 'Otros'
					END as tipoRsva FROM d_hoteles WHERE Fecha BETWEEN ADDDATE(CURDATE(),-1) AND CURDATE()) a ) ON DUPLICATE KEY UPDATE Venta=a.Venta; 

        CREATE TEMPORARY TABLE td_dash 
                SELECT 
                        a.*, asesor,
                IF(Venta!=0,VentaMXN+OtrosIngresosMXN+EgresosMXN,0) as MontoVenta, 
                IF(Venta=0,IF(OtrosIngresosMXN!=0,OtrosIngresosMXN+EgresosMXN,0),0) as MontoOI, 
                IF(Venta=0,IF(OtrosIngresosMXN=0,EgresosMXN,0),0) as MontoEgresos 
                FROM locs_shown a
            LEFT JOIN creators_td b ON a.Localizador=b.Localizador
            WHERE chanId IN ($ptChannels);

        ALTER TABLE td_dash ADD PRIMARY KEY (Localizador, item, venta, Fecha, Hora);

        CREATE TEMPORARY TABLE td_created 
                SELECT 
                        Fecha, Localizador, item,
                IF(Venta!=0,Localizador,NULL) as VentaHoy 
                FROM 
                        td_dash 
                WHERE 
                        Venta!=0 
                GROUP BY Fecha, Localizador, item; 
            
        ALTER TABLE td_created ADD PRIMARY KEY (Fecha, Localizador, item); 

        CREATE TEMPORARY TABLE dashboard_venta 
                SELECT 
                        a.*, b.VentaHoy, 
                CASE 
                                WHEN b.VentaHoy IS NOT NULL THEN MontoVenta+MontoOI+MontoEgresos 
                    ELSE 
                                        IF(MontoOI>0 OR MontoEgresos>0,MontoVenta+MontoOI+MontoEgresos,0) 
                        END as MontoDia 
                FROM 
                        td_dash a 
                LEFT JOIN 
                        td_created b ON a.Localizador=b.Localizador AND a.item=b.item AND a.Fecha=b.Fecha; 
                
        ALTER TABLE dashboard_venta ADD PRIMARY KEY (Localizador, item, Venta, Fecha, Hora); 
        
        CREATE TEMPORARY TABLE dash_td (SELECT Fecha, Hora_int, Hora_pretty, tipoRsva,
        CASE WHEN chanId IN (295,355) THEN 'Outlet' $canales END as OAfiliado, 
        COUNT(DISTINCT VentaHoy) as Locs, SUM(MontoDia) as Monto, SUM(VentaMXN+OtrosIngresosMXN) as SoloVenta 
        FROM 
          HoraGroup_Table15 a 
        RIGHT JOIN 
          (SELECT 
            a.*, 
            AfiliadoOK as Canal, 
            dep 
          FROM 
            dashboard_venta a 
          LEFT JOIN 
            chanIds b ON a.chanId=b.id 
          LEFT JOIN 
            dep_asesores c ON a.asesor=c.asesor AND a.Fecha=c.Fecha 
          ) b 
          ON b.Hora BETWEEN a.Hora_time AND ADDTIME(a.Hora_time,'00:14:59') 
        LEFT JOIN
          chanIds c ON b.chanId=c.id
        GROUP BY 
          Fecha, Hora_pretty, OAfiliado, tipoRsva
         ORDER BY
          Fecha, Hora_int)";


$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}

$query="SELECT Fecha, OAfiliado, SUM(Monto) as Monto FROM dash_td WHERE YEAR(Fecha)=YEAR(CURDATE()) GROUP BY Fecha, Oafiliado";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    
    $fecha=$fecha_int[$fila['Fecha']];
    
    $tit_td[$fila['OAfiliado']][$fecha]=$fila['Monto'];
    @$tit_td['ad'][$fila['OAfiliado']]+=$fila['Monto'];
    
    @$tit_td['Total'][$fecha]+=$fila['Monto'];
    @$tit_td['ad']['Total']+=$fila['Monto'];
    
    switch($fila['OAfiliado']){
      case 'COM':
      case 'CC':
      case 'OB':
        @$tit_td['Total COM'][$fecha]+=$fila['Monto'];
        @$tit_td['ad']['Total COM']+=$fila['Monto'];
        break;
    }
  }
}else{
  echo "Error en info TD -> ".$connectdb->error." ON $query";
}
          
$query="SELECT * FROM dash_td WHERE YEAR(Fecha)=YEAR(CURDATE()) ORDER BY YEAR(Fecha)";
$x=0;
$flag=false;
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
  
    $fecha=$fecha_int[$fila['Fecha']];
    
    if($cortesOK[$x]==$fecha){
      
      if(intval(date('Y',strtotime($fila['Fecha'])))==intval(date('Y'))-1){
        $aly['Acum'][$cortesOK[$x]]=array_sum($aly['Corte']);
      }else{
        $acy['Acum'][$cortesOK[$x]]=array_sum($acy['Corte']);
      }
      
      $x++;
    }
    
    if($x!=0 && intval(date('Y',strtotime($fila['Fecha'])))==intval(date('Y')) && !$flag){
      $x=0; $flag=true;
    }
    
    //Info para cortes
    if(intval(date('Y',strtotime($fila['Fecha'])))==intval(date('Y'))-1){
      $aly['Corte'][$cortesOK[$x]]+=$fila['Monto'];
      $aly['Total']+=$fila['Monto'];
    }else{
      $acy['Corte'][$cortesOK[$x]]+=$fila['Monto'];
    }
    
    
    //echo intval(date('Y',strtotime($fila['Fecha'])))." || ".intval(date('Y'));
    
    $afiliado[$fila['OAfiliado']]=1;
    
    //Data Segmentada
    $data[$fila['OAfiliado']][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['Monto']=$fila['Monto'];
    $data[$fila['OAfiliado']][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['SoloVenta']=$fila['SoloVenta'];
    
    switch($fila['OAfiliado']){
      case 'COM':
      case 'CC':
      case 'OB':
        @$data['Total COM'][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
        @$data['Total COM'][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['SoloVenta']+=$fila['SoloVenta'];
        break;
    }
    
    @$data['Total'][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
    @$data['Total'][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['SoloVenta']+=$fila['SoloVenta'];
    
    //Data Acumulada
      //Suma a la hora actual, el acumulado anterior
    @$data[$fila['OAfiliado']][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcum']=$fila['Monto']+$data[$fila['OAfiliado']][$fila['tipoRsva']][$fecha]['MontoAcum'];
    @$data[$fila['OAfiliado']][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcumSV']=$fila['SoloVenta']+$data[$fila['OAfiliado']][$fila['tipoRsva']][$fecha]['MontoAcumSV'];
      //Actualiza el acumulado
    @$data[$fila['OAfiliado']][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha]['LocsAcum']+=$fila['Locs'];
    @$data[$fila['OAfiliado']][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha]['MontoAcum']+=$fila['Monto'];
    @$data[$fila['OAfiliado']][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha]['MontoAcumSV']+=$fila['SoloVenta'];
    
    @$data['Total'][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcum']+=$fila['Monto'];
    @$data['Total'][$fila['tipoRsva']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcumSV']+=$fila['SoloVenta'];
    
    
    
    
    //echo " || ".$x." || ".intval(date('Y',strtotime($fila['Fecha'])))." || ".$cortesOK[$x]."<br>";
    
  }
}



$query="SELECT * FROM HoraGroup_Table15 ORDER BY Hora_int";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $hg[$fila['Hora_int']]=substr($fila['Hora_time'],0,5);
  }
}

$servicios=array('Hotel','Vuelo','Paquete');

//Acumulados por hora
for($year=2017;$year<=2017;$year++){
  foreach($afiliado as $title => $info){
    $x=0;
    
    switch($title){
      case 'Outlet':
        $fact=1;
        break;
      case 'Total':
        $fact=$var['crecimiento'];
        break;
      default:
        $fact=$var['crecimiento'];
        break;
    }
    
    $corteX=0;
    for($i=1;$i<=count($fecha_json);$i++){
      foreach($hg as $index => $time){
          foreach($servicios as $index2 => $tipoRsva){
            
            if(isset($data[$title][$tipoRsva][$year][$i][$index]['Locs'])){
                $tmp_locs=$data[$title][$tipoRsva][$year][$i][$index]['Locs'];
            }else{
                $tmp_locs=0;
            }
            
            if(isset($data[$title][$tipoRsva][$year][$i][$index]['Monto'])){
                $tmp_monto=$data[$title][$tipoRsva][$year][$i][$index]['Monto'];
            }else{
                $tmp_monto=0;
            }
            
            if(isset($data[$title][$tipoRsva][$year][$i][$index]['SoloVenta'])){
                $tmp_montoSV=$data[$title][$tipoRsva][$year][$i][$index]['SoloVenta'];
            }else{
                $tmp_montoSV=0;
            }

            //$acum['Monto'][$tipoRsva][$title][$year][$i][$index]=$tmp_monto+$acum['Monto'][$tipoRsva][$title][$year][$i][($index-1)];
            //$acum['SoloVenta'][$tipoRsva][$title][$year][$i][$index]=$tmp_montoSV+$acum['SoloVenta'][$tipoRsva][$title][$year][$i][($index-1)];
            
            $total['Monto'][$tipoRsva][$title][$year][$x]=$tmp_monto+$total['Monto'][$tipoRsva][$title][$year][($x-1)];
            $total['SoloVenta'][$tipoRsva][$title][$year][$x]=$tmp_montoSV+$total['SoloVenta'][$tipoRsva][$title][$year][($x-1)];
            
            $totalDay['Monto'][$tipoRsva][$title][$year][$i][$x]=$tmp_monto+$totalDay['Monto'][$tipoRsva][$title][$year][$i][($x-1)];
            $totalDay['SoloVenta'][$tipoRsva][$title][$year][$i][$x]=$tmp_montoSV+$totalDay['SoloVenta'][$tipoRsva][$title][$year][$i][($x-1)];
            
            $chartAcum['Monto'][$tipoRsva][$year][$title][]=$total['Monto'][$tipoRsva][$title][$year][$x];
            $chartAcum['SoloVenta'][$tipoRsva][$year][$title][]=$total['SoloVenta'][$tipoRsva][$title][$year][$x];
            
            $chartDay['Monto'][$tipoRsva][$year][$i][$title][]=$totalDay['Monto'][$tipoRsva][$title][$year][$i][$x];
            $chartDay['SoloVenta'][$tipoRsva][$year][$i][$title][]=$totalDay['SoloVenta'][$tipoRsva][$title][$year][$i][$x];
          
          }
        $x++;
      }
    }
  }
}


//Create Categories
for($i=1;$i<=count($fecha_json);$i++){

  $day=$fecha_json[$i];

  foreach($hg as $index => $time){
    $categories[]=$day." ".$time;
  }
}

foreach($hg as $index => $time){
    $categories_hg[]=$time;
}



//printPre($acum);

$connectdb->close();

?>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>
<script>

  dataAcum=<?php json($chartAcum); ?>;
  dataDay=<?php json($chartDay); ?>;
  categories=<?php json($categories); ?>;
  categories_hg=<?php json($categories_hg); ?>;
  totales=<?php json($tit_td); ?>;
  fecha_json=<?php json($fecha_json); ?>;

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
            text: 'Monto Acumulado '+group,
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Venta por producto',
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
            plotBands: [
            <?php echo $plotbands; ?>]
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
            },
            gridLineColor: '#5B6073'
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
            name: 'Hotel',
            data: dataAcum['Monto']['Hotel'][2017][group],
            color: 'rgb(169,47,56)',
        }, {
            name: 'Vuelo',
            data: dataAcum['Monto']['Vuelo'][2017][group],
            color: 'rgb(145,199,169)',
        }, {
            name: 'Paquete',
            data: dataAcum['Monto']['Paquete'][2017][group],
            color: 'rgb(229,223,197)',
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
    
    var titleMont = (typeof totales[group] === 'undefined') ? 0 : totales[group][day];
     
    fecha=fecha_json[day];

    Highcharts.chart(container, {
        chart: {
          backgroundColor: bg,
          type: 'spline'
        },
         
        title: {
            text: 'Monto '+group,
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Venta por producto '+day+' <?php echo date('F', strtotime($var['td']['inicio'])); ?>',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        xAxis: {
            categories: categories_hg,
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
            },
            gridLineColor: '#5B6073'
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
            name: 'Hotel',
            data: dataDay['Monto']['Hotel'][2017][day][group],
            color: 'rgb(169,47,56)',
        }, {
            name: 'Vuelo',
            data: dataDay['Monto']['Vuelo'][2017][day][group],
            color: 'rgb(145,199,169)',
        }, {
            name: 'Paquete',
            data: dataDay['Monto']['Paquete'][2017][day][group],
            color: 'rgb(229,223,197)',
        }]
    });
    
  }
  
  $( "#accordion" ).accordion({
    collapsible: true,
    active: false,
    heightStyle: "content"
  });
  
  function pTotal(){
    printChartTotal('COM','COM','#000000');
    printChartTotal('PDV','PDV','#000000');
    printChartTotal('CC','CC','#000000');
    printChartTotal('OB','OB','#000000');
    printChartTotal('Total','Total','#000000');
    printChartTotal('Total COM','Total COM','#000000');
    
    det=0;
  }
  
  function pDay(day){
    printChart('COM','COM','#000000',day);
    printChart('PDV','PDV','#000000',day);
    printChart('CC','CC','#000000',day);
    printChart('OB','OB','#000000',day);
    printChart('Total','Total','#000000',day);
    printChart('Total COM','Total COM','#000000',day);
    
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
      location = '/venta-live/dashboard-producto.php?det='+det;
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
    <?php echo $radioprint; ?>
    <label for='radio-0'>Todo</label>
    <input class='chkbx' type='radio' name='radio-1' id='radio-0' value=0 checked>
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
  <div class='hc' id="Total"></div>
</div>
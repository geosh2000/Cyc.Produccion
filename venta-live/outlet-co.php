<?php

include_once("../modules/modules.php");

initSettings::start(true);
initSettings::printTitle('Outlet Colombia');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

if(isset($_POST['Fecha'])){
  $fecha=$_POST['Fecha'];
  $fecha_fin=$_POST['Fecha_fin'];
}else{
  $fecha=date('Y-m-d');
  $fecha_fin=date('Y-m-d');
}

if(isset($_GET['Fecha'])){
  $fecha=$_GET['Fecha'];
  $fecha_fin=$_GET['Fecha_fin'];
}

if(isset($_GET['cur'])){
  $cur=$_GET['cur'];
}else{
  $cur=2941.17647;
}

$tbody="<td><input type='text' value='$fecha' id='fecha' name='Fecha'><input type='text' value='$fecha_fin' id='fecha_fin' name='Fecha_fin'></td>";

Filters::showFilter('','POST','submit','Consultar',$tbody);

$query="SET @inicio = CAST('$fecha' as DATE);
        SET @fin = CAST('$fecha_fin' as DATE);
        SET @currency = $cur;

        DROP TEMPORARY TABLE IF EXISTS outletLocsCO;
        CREATE TEMPORARY TABLE outletLocsCO SELECT
            a.*, b.Fecha as FechaAsesor
        FROM
            t_Locs a
                LEFT JOIN
            co_outlet_dashboard_asesores b ON a.Nombre = b.Nombre
                AND a.Fecha = b.Fecha
        WHERE
            a.Fecha BETWEEN @inicio AND @fin HAVING FechaAsesor IS NOT NULL;

        ALTER TABLE outletLocsCO ADD PRIMARY KEY (`Localizador`, `Venta`, `Fecha`, `Hora`);

        INSERT INTO outletLocsCO (SELECT * FROM (SELECT
            a.*, b.Fecha as FechaAsesor
        FROM
            d_Locs a
                LEFT JOIN
            co_outlet_dashboard_asesores b ON a.Nombre = b.Nombre
                AND a.Fecha = b.Fecha
        WHERE
            a.Fecha BETWEEN @inicio AND @fin HAVING FechaAsesor IS NOT NULL) a) ON DUPLICATE KEY UPDATE asesor=a.asesor;

        DROP TEMPORARY TABLE IF EXISTS outletHotelesCO;
        CREATE TEMPORARY TABLE outletHotelesCO SELECT
            *
        FROM
            t_hoteles
            WHERE
        		Fecha BETWEEN @inicio AND @fin;

        ALTER TABLE outletHotelesCO ADD PRIMARY KEY (`Localizador`, `item`, `Venta`, `Fecha`, `Hora`);

        INSERT INTO outletHotelesCO (SELECT * FROM (SELECT
            *
        FROM
            d_hoteles
        WHERE
            Fecha BETWEEN @inicio AND @fin) a) ON DUPLICATE KEY UPDATE Hotel=a.Hotel;

        DROP TEMPORARY TABLE IF EXISTS resultsOutlet;
        CREATE TEMPORARY TABLE resultsOutlet SELECT
            asesor,
            Fecha,
            Nombre,
            COUNT(DISTINCT NewLoc) AS Locs,
            SUM(Venta + OtrosIngresos + Egresos) * @currency AS Monto,
            SUM(MontoHotel) * @currency AS MontoHotel,
            SUM(RN) AS RN
        FROM
            (SELECT
        		a.*,
        		IF(Venta != 0, a.Localizador, NULL) AS NewLoc,
        		Monto AS MontoHotel,
        		RN
        	FROM
        		outletLocsCO a
        			LEFT JOIN
        		(SELECT
        			Fecha, Localizador, SUM(Monto) AS Monto, SUM(RN) AS RN
        		FROM
        			(SELECT
        				Fecha,
        				Localizador,
        				item,
        				SUM(Venta + OtrosIngresos + Egresos) AS Monto,
        				IF(Venta != 0, RN, 0) AS RN
        			FROM
        				outletHotelesCO
        			GROUP BY Fecha , Localizador , item) a
        		GROUP BY Fecha , Localizador) b ON a.Fecha = b.Fecha
        			AND a.Localizador = b.Localizador) a
        GROUP BY Nombre;";

$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}

$query="SELECT
            b.asesor, a.Fecha, a.Nombre, Locs, Monto, MontoHotel, RN
        FROM
            co_outlet_dashboard_asesores a
                LEFT JOIN
            resultsOutlet b ON a.Fecha = b.Fecha
                AND a.Nombre = b.Nombre
        WHERE
            a.Fecha BETWEEN @inicio AND @fin;";

if($result=$connectdb->query($query)){
  $i=0;
  while($fila=$result->fetch_assoc()){
    $data[utf8_encode($fila['Nombre'])]['Monto']=$fila['Monto']/1000;
    $data[utf8_encode($fila['Nombre'])]['MontoHotel']=$fila['MontoHotel']/1000;
    $data[utf8_encode($fila['Nombre'])]['Locs']=$fila['Locs'];
    $data[utf8_encode($fila['Nombre'])]['RN']=intval($fila['RN']);
    @$total['Monto']+=$fila['Monto']/1000;
    @$total['MontoHotel']+=$fila['MontoHotel']/1000;
    @$total['Locs']+=$fila['Locs'];
    @$total['RN']+=$fila['RN'];
  }
}else{
  echo "ERROR! -> ".$connectdb->error." ON<br>$query<br><br>";
}

$query="SELECT MAX(Last_Update) as LU FROM d_Locs";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $lu=$fila['LU'];
}else{
  echo "ERROR! -> ".$connectdb->error." ON<br>$query<br><br>";
}

$connectdb->close();

if(isset($data)){
  foreach($data as $asesor => $info){
    foreach($info as $sub => $info2){
      $dataTable[$sub][]=array($asesor, $info2);
    }
  }
}else{
  echo utf8_encode("No existen datos de venta para este d�a");
}

?>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>
<script>
$(function(){

  dataTable=<?php echo json_encode($dataTable);?>;
  total=<?php echo json_encode($total);?>;

  $('#fecha').periodpicker({
    end: '#fecha_fin',
    clearButtonInButton: true,
    formatDateTime: 'YYYY-MM-DD'
  });


  function printChart(container, title, data, totalTitle, tipo){

    switch(tipo){
      case 'monto':
        titulo= 'Total: $'+totalTitle.toFixed(2)+'K';
        yaxis='COPs (K)';
        tooltip=title+': <b>${point.y:,.2f} K</b>';
        format='{point.y:,.2f} K';
        break;
      case 'cant':
        titulo= 'Total: '+totalTitle.toFixed(2);
        yaxis='Cantidad';
        tooltip=title+': <b>{point.y:,.0f}</b>';
        format='{point.y:,.0f}';
        break;
    }

    Highcharts.chart(container, {
        chart: {
            type: 'column'
        },
        title: {
            text: title
        },
        subtitle: {
            text: titulo
        },
        xAxis: {
            type: 'category',
            labels: {
                overflow: 'justify'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: yaxis
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: tooltip
        },
        series: [{
            name: 'Population',
            data: data,
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: format, // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
   }

   printChart('Monto','Monto Vendido', dataTable['Monto'],total['Monto'],'monto');
   printChart('MontoHotel','Monto Hotel', dataTable['MontoHotel'],total['MontoHotel'],'monto');
   printChart('RN','RN', dataTable['RN'],total['RN'],'cant');

   setTimeout(function(){
      location.replace("http://operaciones.pricetravel.com.mx/venta-live/outlet-co.php?inicio=<?php echo $fecha; ?>&fin=<?php echo $fecha_fin; ?>");
    },300000);
});
</script>
<style>
.chartOutlet{
  height: 600px
}
</style>
<p>Last Update: <span id='lu'><?php echo $lu; ?></span></p>
<div class='chartOutlet' id='Monto'></div>
<div class='chartOutlet' id='MontoHotel'></div>
<div class='chartOutlet' id='RN'></div>

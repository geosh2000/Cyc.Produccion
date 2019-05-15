<?php
include_once('../modules/modules.php');

initSettings::start(true);
initSettings::printTitle('Dashboard Venta de Hoteles');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

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

$connectdb->close();


?>
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>
<script>

  /*
  dataRN=<?php echo json_encode($dataRN); ?>;
  categories=<?php echo json_encode($cat); ?>;
  totales=<?php echo json_encode($total); ?>;
  totalesDay=<?php echo json_encode($totalDay); ?>;
  */
  
</script>
<script>
$(function(){
    
    function getData(){
        
        console.log('start getData');
        
        $.ajax({
            url: 'query_RN-CO.php',
            type: 'POST',
            dataType: 'json',
            success: function(array){
                data=array;
                dataRN=data['dataRN'];
                categories=data['cat'];
                totales=data['total'];
                totalesDay=data['totalDay'];
                
                $('#lu').text(data['lu']);
                
                $('.rnBlock').each(function(){
                   grupo=$(this).find('.rnTitle').text();
                   
                    if($(this).find('.rnInfo').find('.rns').attr('tipo')=='day'){
                        $(this).find('.rnInfo').find('.rns').text(number_format(totalesDay[grupo],0,'.',','));
                        //console.log(grupo+' -> day');
                    }else{
                        $(this).find('.rnInfo').find('.rns').text(number_format(totales[grupo][2017],0,'.',','));
                        //console.log(grupo+' -> total');
                    }
                    
                });
            },
            error: function(){
                console.log('error');
            }
        });
        
        
    }
    
    getData();
    
    timer=300;
    t=timer;
    setInterval(function(){
        $('#timer').text(t);
        t=t-1;
        if(t==0){
            getData();
            t=timer;
        }
    },1000);
    
    getData();
    
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
    
    function printBlock(id){
        $('#blockContainerDia').append("<div class='rnBlock' id='blockD_"+id+"'><div class='rnTitle'>"+id+"</div><div class='rnDIV'></div><div class='rnInfo'><span class='rns' tipo='day'>"+number_format(0,0,'.',',')+"</span><span class='rnRN'> RN </span></div></div>");
        
        $('#blockContainerTotal').append("<div class='rnBlock' id='blockT_"+id+"'><div class='rnTitle'>"+id+"</div><div class='rnDIV'></div><div class='rnInfo'><span class='rns' tipo='total'>"+number_format(0,0,'.',',')+"</span><span class='rnRN'> RN </span></div></div>");
    }
    
    printBlock('COM PT');
    printBlock('CC PT');
    printBlock('Total COM PT');
    printBlock('COM TB');
    printBlock('CC TB');
    printBlock('Total COM TB');
    printBlock('PDV');
    printBlock('Total');

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
    
    .rnBlock{
        width: 290px;
        height: 150px;
        background: white;
        display: inline-block;
        margin: 30px;
        color: black;
        border-radius: 10px;
    }
    
    .rnTitle{
        width: 100%;
        height: 45px;
        color: black;
        margin-bottom: 5px;
        font-size: 35px;
        line-height: normal;
        text-align: center;
    }
    
    .rnDIV{
        width: 90%;
        height: 2px;
        background: black;
        margin: auto;
        color: black;
    }
    
    .rnInfo{
        width: 100%;
        height: 90px;
        margin-top: 5px;
        color: black;
        font-size: 78px;
        font-weight: bold;
        text-align: center;
        line-height: normal;
    }
    
    .rnRN{
        font-size: 12px;
    }
    
    .blockContainer{
        text-align: center;
        width: 1050px;
        margin:auto;
    }
</style>
<p style='color: white'>Last Update: <span id='lu'></span> || Reload in: <span id='timer'></span> sec.</p>
<div><p style='color: white; font-size:35px; font-weight: bold; text-align: center'>Conteo de RN CO</p></div>
<div id='blockContainerDia' class='blockContainer'><p style='color: white; font-size:35px; font-weight: bold;'>Venta del Día <?php echo date('Y-m-d') ?></p></div>
<div id='blockContainerTotal' class='blockContainer'><p style='color: white; font-size:35px; font-weight: bold;'>Venta Acumulada <?php echo $var['td']['inicio']." a ".$var['td']['fin'];?></p></div>

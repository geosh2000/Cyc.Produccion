<?php
include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$query="SELECT * FROM config_dashboard WHERE tag='main'";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $var['crecimiento']=$fila['crecimiento'];
  $var['crecimiento_com']=$fila['meta_com'];
  $var['crecimiento_pdv']=$fila['meta_pdv'];
  $var['modo']=$fila['modo'];
  $var['ly']['inicio']=$fila['ly_inicio'];
  $var['ly']['fin']=$fila['ly_fin'];
  $var['td']['inicio']=$fila['td_inicio'];
  $var['td']['fin']=$fila['td_fin'];
  $cortes=explode("|",$fila['cortes']);
}else{
    echo "ERROR! CONFIG";
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
/*
$query="SELECT Fecha, Hora_int, Hora_pretty, 
        CASE WHEN chanId IN (295,355) THEN 'Outlet'
        $canales 
        END as OAfiliado, 
        COUNT(DISTINCT NewLoc) as Locs, SUM(VentaMXN+EgresosMXN+OtrosIngresosMXN) as Monto, SUM(VentaMXN+OtrosIngresosMXN) as SoloVenta 
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
            a.Fecha BETWEEN '".$var['ly']['inicio']."' AND '".$var['ly']['fin']."' 
            AND (chanId IN ($ptChannels,355,295))) b 
          ON b.Hora BETWEEN a.Hora_time AND ADDTIME(a.Hora_time,'00:14:59') 
        LEFT JOIN
          chanIds c ON b.chanId=c.id
        GROUP BY 
          Fecha, Hora_pretty, OAfiliado
         ORDER BY
          Fecha, Hora_int";
          
          
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
  
    
    $fecha=$fecha_int[$fila['Fecha']];
    
       
    $afiliado[$fila['OAfiliado']]=1;
    
    //Data Segmentada
    $data[$fila['OAfiliado']][2016][$fecha][$fila['Hora_int']]['Locs']=$fila['Locs'];
    $data[$fila['OAfiliado']][2016][$fecha][$fila['Hora_int']]['Monto']=$fila['Monto'];
    $data[$fila['OAfiliado']][2016][$fecha][$fila['Hora_int']]['SoloVenta']=$fila['SoloVenta'];
    
    switch($fila['OAfiliado']){
      case 'COM':
      case 'CC':
      case 'OB':
        @$data['Total COM'][2016][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
        @$data['Total COM'][2016][$fecha][$fila['Hora_int']]['SoloVenta']+=$fila['SoloVenta'];
        break;
    }
    
    @$data['Total'][2016][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
    @$data['Total'][2016][$fecha][$fila['Hora_int']]['SoloVenta']+=$fila['SoloVenta'];
    
    //Data Acumulada
      //Suma a la hora actual, el acumulado anterior
    @$data[$fila['OAfiliado']][2016][$fecha][$fila['Hora_int']]['LocsAcum']=$fila['Locs']+$data[$fila['OAfiliado']][$fecha]['LocsAcum'];
    @$data[$fila['OAfiliado']][2016][$fecha][$fila['Hora_int']]['MontoAcum']=$fila['Monto']+$data[$fila['OAfiliado']][$fecha]['MontoAcum'];
    @$data[$fila['OAfiliado']][2016][$fecha][$fila['Hora_int']]['MontoAcumSV']=$fila['SoloVenta']+$data[$fila['OAfiliado']][$fecha]['MontoAcumSV'];
      //Actualiza el acumulado
    @$data[$fila['OAfiliado']][2016][$fecha]['LocsAcum']+=$fila['Locs'];
    @$data[$fila['OAfiliado']][2016][$fecha]['MontoAcum']+=$fila['Monto'];
    @$data[$fila['OAfiliado']][2016][$fecha]['MontoAcumSV']+=$fila['SoloVenta'];
    
    @$data['Total'][2016][$fecha][$fila['Hora_int']]['MontoAcum']+=$fila['Monto'];
    @$data['Total'][2016][$fecha][$fila['Hora_int']]['MontoAcumSV']+=$fila['SoloVenta'];
    
  }
}
*/
$afiliado['Total']=1;
$afiliado['Total COM']=1;

$query="DROP TEMPORARY TABLE IF EXISTS dash_td;
        DROP TEMPORARY TABLE IF EXISTS td_dash;
        DROP TEMPORARY TABLE IF EXISTS td_created;
        DROP TEMPORARY TABLE IF EXISTS locs_shown;
        DROP TEMPORARY TABLE IF EXISTS dashboard_venta;

        CREATE TEMPORARY TABLE locs_shown SELECT * FROM t_Locs WHERE Fecha BETWEEN '".$var['ly']['inicio']."' AND '".$var['ly']['fin']."' OR Fecha BETWEEN '".$var['td']['inicio']."' AND '".$var['td']['fin']."';
        ALTER TABLE locs_shown
          ADD PRIMARY KEY (Localizador, Venta, Fecha, Hora);

        INSERT INTO locs_shown (SELECT * FROM (SELECT * FROM d_Locs WHERE Fecha BETWEEN ADDDATE(CURDATE(),-1) AND CURDATE()) a ) ON DUPLICATE KEY UPDATE asesor=a.asesor;

        CREATE TEMPORARY TABLE td_dash SELECT
          a.*, 
          IF(Venta!=0,VentaMXN+OtrosIngresosMXN+EgresosMXN,0) as MontoVenta,
          IF(Venta=0,IF(OtrosIngresosMXN!=0,OtrosIngresosMXN+EgresosMXN,0),0) as MontoOI,
          IF(Venta=0,IF(OtrosIngresosMXN=0,EgresosMXN,0),0) as MontoEgresos
        FROM 
          locs_shown a
        WHERE 
          chanId IN ($ptChannels);

        ALTER TABLE td_dash
          ADD PRIMARY KEY (Localizador, Venta, Fecha, Hora);
          
        CREATE TEMPORARY TABLE td_created SELECT Fecha, Localizador, IF(Venta!=0,Localizador,NULL) as VentaHoy FROM td_dash WHERE Venta!=0 GROUP BY Fecha, Localizador;

        ALTER TABLE td_created
          ADD PRIMARY KEY (Fecha, Localizador);
          
        CREATE TEMPORARY TABLE dashboard_venta SELECT
                        a.*, b.VentaHoy,
                        CASE
                          WHEN b.VentaHoy IS NOT NULL THEN MontoVenta+MontoOI+MontoEgresos
                          ELSE IF(MontoOI>0 OR MontoEgresos>0,MontoVenta+MontoOI+MontoEgresos,0)
                        END as MontoDia
                      FROM
                        td_dash a
                      LEFT JOIN
                        td_created b ON a.Localizador=b.Localizador AND a.Fecha=b.Fecha;

        ALTER TABLE dashboard_venta
          ADD PRIMARY KEY (Localizador, Venta, Fecha, Hora);

    CREATE TEMPORARY TABLE dash_td (SELECT Fecha, Hora_int, Hora_pretty, 
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
          Fecha, Hora_pretty, OAfiliado
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
          
$query="SELECT * FROM dash_td ORDER BY YEAR(Fecha)";
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
    $data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['Locs']=$fila['Locs'];
    $data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['Monto']=$fila['Monto'];
    $data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['SoloVenta']=$fila['SoloVenta'];
    
    switch($fila['OAfiliado']){
      case 'COM':
      case 'CC':
      case 'OB':
        @$data['Total COM'][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
        @$data['Total COM'][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['SoloVenta']+=$fila['SoloVenta'];
        break;
    }
    
    @$data['Total'][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['Monto']+=$fila['Monto'];
    @$data['Total'][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['SoloVenta']+=$fila['SoloVenta'];
    
    //Data Acumulada
      //Suma a la hora actual, el acumulado anterior
    @$data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['LocsAcum']=$fila['Locs']+$data[$fila['OAfiliado']][$fecha]['LocsAcum'];
    @$data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcum']=$fila['Monto']+$data[$fila['OAfiliado']][$fecha]['MontoAcum'];
    @$data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcumSV']=$fila['SoloVenta']+$data[$fila['OAfiliado']][$fecha]['MontoAcumSV'];
      //Actualiza el acumulado
    @$data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha]['LocsAcum']+=$fila['Locs'];
    @$data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha]['MontoAcum']+=$fila['Monto'];
    @$data[$fila['OAfiliado']][intval(date('Y',strtotime($fila['Fecha'])))][$fecha]['MontoAcumSV']+=$fila['SoloVenta'];
    
    @$data['Total'][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcum']+=$fila['Monto'];
    @$data['Total'][intval(date('Y',strtotime($fila['Fecha'])))][$fecha][$fila['Hora_int']]['MontoAcumSV']+=$fila['SoloVenta'];
    
    
    
    
    //echo " || ".$x." || ".intval(date('Y',strtotime($fila['Fecha'])))." || ".$cortesOK[$x]."<br>";
    
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
        if(isset($data[$title][$year][$i][$index]['Locs'])){$tmp_locs=$data[$title][$year][$i][$index]['Locs'];}else{$tmp_locs=0;}
        if(isset($data[$title][$year][$i][$index]['Monto'])){$tmp_monto=$data[$title][$year][$i][$index]['Monto'];}else{$tmp_monto=0;}
        if(isset($data[$title][$year][$i][$index]['SoloVenta'])){$tmp_montoSV=$data[$title][$year][$i][$index]['SoloVenta'];}else{$tmp_montoSV=0;}
        
        $acum['Monto'][$title][$year][$i][$index]=$tmp_monto+$acum['Monto'][$title][$year][$i][($index-1)];
        $acum['SoloVenta'][$title][$year][$i][$index]=$tmp_montoSV+$acum['SoloVenta'][$title][$year][$i][($index-1)];
        $acum['Locs'][$title][$year][$i][$index]=$tmp_locs+$acum['Locs'][$title][$year][$i][($index-1)];
        
        $total['Monto'][$title][$year][$x]=$tmp_monto+$total['Monto'][$title][$year][($x-1)];
        $total['SoloVenta'][$title][$year][$x]=$tmp_montoSV+$total['SoloVenta'][$title][$year][($x-1)];
        $total['Locs'][$title][$year][$x]=$tmp_locs+$total['Locs'][$title][$year][($x-1)];
        
        $totalDay['Monto'][$title][$year][$i][$x]=$tmp_monto+$totalDay['Monto'][$title][$year][$i][($x-1)];
        $totalDay['SoloVenta'][$title][$year][$i][$x]=$tmp_montoSV+$totalDay['SoloVenta'][$title][$year][$i][($x-1)];
        $totalDay['Locs'][$title][$year][$i][$x]=$tmp_locs+$totalDay['Locs'][$title][$year][$i][($x-1)];
        
        $chartAcum['Monto'][$year][$title][]=$total['Monto'][$title][$year][$x];
        $chartAcum['SoloVenta'][$year][$title][]=$total['SoloVenta'][$title][$year][$x];
        $chartAcum['Locs'][$year][$title][]=$total['Locs'][$title][$year][$x];
        
        
        $chartDay['Monto'][$year][$i][$title][]=$totalDay['Monto'][$title][$year][$i][$x];
        $chartDay['SoloVenta'][$year][$i][$title][]=$totalDay['SoloVenta'][$title][$year][$i][$x];
        $chartDay['Locs'][$year][$i][$title][]=$totalDay['Locs'][$title][$year][$i][$x];
        
        if($year==2016){
        
          if($i>=$cortesOK[$corteX]){
            if($corteX==0){
              $plan=$aly['Acum'][$cortesOK[$corteX]]*$fact;
            }else{
              $plan=$aly['Acum'][$cortesOK[$corteX]]*$fc;
            }
            
            $corteX++;
            
          }
          
          //metas dinamicas por cortes
          if($corteX==0){
              $fc=$fact;
          }else{
              
              if(isset($aly['Acum'][$cortesOK[$corteX]])){
                $lyChecked=$aly['Acum'][$cortesOK[$corteX]];
                $lastLy=$lyChecked;
              }else{
                $lyChecked=$lastLy;
              }
              
              if(isset($acy['Acum'][$cortesOK[$corteX-1]])){
                $cyChecked=$acy['Acum'][$cortesOK[$corteX-1]];
                $lastCy=$cyChecked;
                $fc=(($aly['Total']*$fact)-$cyChecked)/($aly['Total']-$lyChecked);
              }else{
                $cyChecked=$lastCy;
              }
            /*
              if($cyChecked<$plan){
                $fc=(($aly['Total']*$fact)-$cyChecked)/($aly['Total']-$lyChecked);
              }
             echo "$i || ".$cortesOK[$corteX]." || PL: ".($aly['Total']*$fact)." || ACY: ".$cyChecked." || LY: ".$aly['Total']." || ALY: ".$lyChecked." --> FC: $fc || Cubierto: ".(($aly['Total']*$fact)-$cyChecked)." || lyCob: ".($aly['Total']-$lyChecked)."<br>";
             
            */ 
          }
          
          $metaAcum['Monto'][$title][]=$total['Monto'][$title][$year][$x]*$fact;
          $metaAcum['SoloVenta'][$title][]=$total['SoloVenta'][$title][$year][$x]*$fact;
          $metaDay['Monto'][$i][$title][]=$totalDay['Monto'][$title][$year][$i][$x]*$fc;
        }
        
        $x++;
      }
    }
  }
}

//Ajuste de participaciones
foreach($metaAcum['Monto']['Total'] as $index => $info){
  $metaAcum['Monto']['COM'][$index]=$info*.7*.45;
  $metaAcum['Monto']['CC'][$index]=$info*.7*.55*.7;
  $metaAcum['Monto']['OB'][$index]=$info*.7*.55*.3;
  $metaAcum['Monto']['PDV'][$index]=$info*.3;
  $metaAcum['Monto']['Total COM'][$index]=$metaAcum['Monto']['COM'][$index]+$metaAcum['Monto']['CC'][$index]+$metaAcum['Monto']['OB'][$index];

  $totpdvmeta=$metaAcum['Monto']['PDV'][$index];
}

//ReCalc PDV
  $totpdvmonto=$chartAcum['Monto'][2016]['PDV'][(count($chartAcum['Monto'][2016]['PDV'])-1)];
  $tot_pdv=$totpdvmeta/$totpdvmonto;
  foreach($metaAcum['Monto']['Total'] as $index => $info){
    $metaAcum['Monto']['PDV'][$index]=$chartAcum['Monto'][2016]['PDV'][$index]*$tot_pdv;
  }



foreach($metaDay['Monto'] as $day => $info2){
  foreach($info2['Total'] as $index => $info){
  
  if($index<44 || $index>84){
    $fac_cc=0.7;
    $fac_pdv=0.3;
  }else{
    $fac_cc=0.7;
    $fac_pdv=0.3;
  }
  
    $metaDay['Monto'][$day]['COM'][$index]=$info*$fac_cc*.45;
    $metaDay['Monto'][$day]['CC'][$index]=$info*$fac_cc*.55*.7;
    $metaDay['Monto'][$day]['OB'][$index]=$info*$fac_cc*.55*.3;
    $metaDay['Monto'][$day]['PDV'][$index]=$info*$fac_pdv;
    $metaDay['Monto'][$day]['Total COM'][$index]=$metaDay['Monto'][$day]['COM'][$index]+$metaDay['Monto'][$day]['CC'][$index]+$metaDay['Monto'][$day]['OB'][$index];
  }
  
  //ReCalc PDV
  $tot_pdv=$metaDay['Monto'][$day]['PDV'][95]/$chartDay['Monto'][2016][$day]['PDV'][95];
  foreach($info2['Total'] as $index => $info){
    $metaDay['Monto'][$day]['PDV'][$index]=$chartDay['Monto'][2016][$day]['PDV'][$index]*$tot_pdv;
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
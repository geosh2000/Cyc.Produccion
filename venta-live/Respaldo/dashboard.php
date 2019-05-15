<?php
include_once('../modules/modules.php');

initSettings::start(true);
initSettings::printTitle('Dashboard Outlet');

$connectdb=Connection::mysqliDB('CC');

$query="SELECT * FROM config_dashboard WHERE tag='main'";
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $var['crecimiento_gen']=$fila['crecimiento'];
  $var['crecimiento_com']=$fila['meta_com'];
  $var['crecimiento_pdv']=$fila['meta_pdv'];
  $var['modo']=$fila['modo'];
  $var['ly']['inicio']=$fila['ly_inicio'];
  $var['ly']['fin']=$fila['ly_fin'];
  $var['td']['inicio']=$fila['td_inicio'];
  $var['td']['fin']=$fila['td_fin'];
  $cortes=explode("|",$fila['cortes']);
}

switch($var['modo']){
    case 1:
        $var['crecimiento']['GEN']=$var['crecimiento_gen'];
        $var['crecimiento']['COM']=$var['crecimiento_gen'];
        $var['crecimiento']['PDV']=$var['crecimiento_gen'];
        break;
    case 2:
        $var['crecimiento']['GEN']=$var['crecimiento_gen'];
        $var['crecimiento']['COM']=$var['crecimiento_com'];
        $var['crecimiento']['PDV']=$var['crecimiento_pdv'];
        break;
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



$query="SELECT MAX(Last_Update) as LU FROM d_Locs WHERE Fecha=CURDATE()";
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


//Ventas por fecha CURRENT YEAR
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


// Venta CURRENT YEAR
$query="SELECT * FROM dash_td ORDER BY YEAR(Fecha)";
$x=0;
$flag=false;
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){

    $fecha=$fecha_int[$fila['Fecha']];

    if($cortesOK[$x]==$fecha){

      if(intval(date('Y',strtotime($fila['Fecha'])))==intval(date('Y'))-1){
        @$aly['Acum'][$cortesOK[$x]]=array_sum($aly['Corte']);
      }else{
        @$acy['Acum'][$cortesOK[$x]]=array_sum($acy['Corte']);
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

// Categorias (Horas cada 15 min)
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
        $fact=$var['crecimiento']['GEN'];
        break;
      case 'Total COM':
      case 'OB':
      case 'COM':
      case 'CC':
        $fact=$var['crecimiento']['COM'];
        break;
      case 'PDV':
        $fact=$var['crecimiento']['PDV'];
        break;
      default:
        $fact=$var['crecimiento']['GEN'];
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

// ***** Last Version para calculo de metas *****
//
// //Ajuste de participaciones
// foreach($metaAcum['Monto']['Total'] as $index => $info){
//     switch($var['modo']){
//         case 1:
//             $metaAcum['Monto']['COM'][$index]=$info*.7*.45;
//             $metaAcum['Monto']['CC'][$index]=$info*.7*.55*.7;
//             $metaAcum['Monto']['OB'][$index]=$info*.7*.55*.3;
//             $metaAcum['Monto']['PDV'][$index]=$info*.3;
//             break;
//         case 2:
//             break;
//     }
//
//   $metaAcum['Monto']['Total COM'][$index]=$metaAcum['Monto']['COM'][$index]+$metaAcum['Monto']['CC'][$index]+$metaAcum['Monto']['OB'][$index];
//   $totpdvmeta=$metaAcum['Monto']['PDV'][$index];
// }
//
// if($var['modo']==1){
// //ReCalc PDV
//   $totpdvmonto=$chartAcum['Monto'][2016]['PDV'][(count($chartAcum['Monto'][2016]['PDV'])-1)];
//   $tot_pdv=$totpdvmeta/$totpdvmonto;
//   foreach($metaAcum['Monto']['Total'] as $index => $info){
//     $metaAcum['Monto']['PDV'][$index]=$chartAcum['Monto'][2016]['PDV'][$index]*$tot_pdv;
//   }
// }
//
//
// foreach($metaDay['Monto'] as $day => $info2){
//
//     if($var['modo']==1){
//         foreach($info2['Total'] as $index => $info){
//
//             switch($var['modo']){
//                 case 1:
//                     if($index<44 || $index>84){
//                         $fac_cc=0.7;
//                         $fac_pdv=0.3;
//                       }else{
//                         $fac_cc=0.7;
//                         $fac_pdv=0.3;
//                       }
//                     break;
//                 case 2:
//                     $fac_cc=0.7;
//                     $fac_pdv=0.3;
//                     break;
//             }
//
//
//             $metaDay['Monto'][$day]['COM'][$index]=$info*$fac_cc*.45;
//             $metaDay['Monto'][$day]['CC'][$index]=$info*$fac_cc*.55*.7;
//             $metaDay['Monto'][$day]['OB'][$index]=$info*$fac_cc*.55*.3;
//             $metaDay['Monto'][$day]['PDV'][$index]=$info*$fac_pdv;
//             $metaDay['Monto'][$day]['Total COM'][$index]=$metaDay['Monto'][$day]['COM'][$index]+$metaDay['Monto'][$day]['CC'][$index]+$metaDay['Monto'][$day]['OB'][$index];
//           }
//     }else{
// //        foreach($info2 as $canal => info3){
// //            foreach($info3 as $index => $info){
// //                switch($var['modo']){
// //                    case 1:
// //                        if($index<44 || $index>84){
// //                            $fac_cc=0.7;
// //                            $fac_pdv=0.3;
// //                          }else{
// //                            $fac_cc=1;
// //                            $fac_pdv=1;
// //                          }
// //                        break;
// //                    case 2:
// //                        $fac_cc=0.7;
// //                        $fac_pdv=0.3;
// //                        break;
// //                }
// //
// //
// //                $metaDay['Monto'][$day]['COM'][$index]=$info*$fac_cc*.45;
// //                $metaDay['Monto'][$day]['CC'][$index]=$info*$fac_cc*.55*.7;
// //                $metaDay['Monto'][$day]['OB'][$index]=$info*$fac_cc*.55*.3;
// //                $metaDay['Monto'][$day]['PDV'][$index]=$info*$fac_pdv;
// //                $metaDay['Monto'][$day]['Total COM'][$index]=$metaDay['Monto'][$day]['COM'][$index]+$metaDay['Monto'][$day]['CC'][$index]+$metaDay['Monto'][$day]['OB'][$index];
// //            }
// //        }
//     }
//
//
//   //ReCalc PDV
//   $tot_pdv=$metaDay['Monto'][$day]['PDV'][95]/$chartDay['Monto'][2016][$day]['PDV'][95];
//   foreach($info2['Total'] as $index => $info){
//     $metaDay['Monto'][$day]['PDV'][$index]=$chartDay['Monto'][2016][$day]['PDV'][$index]*$tot_pdv;
//   }
// }

// ***** Nueva Version para calculo de metas *****

//Venta por dia por canal LY
$query="SELECT
            Fecha,
            OAfiliado AS AfiliadoOK,
            SUM(Monto) AS Monto
        FROM
            (SELECT
                ADDDATE(Fecha, 364) AS Fecha, OAfiliado, SUM(Monto) AS Monto
            FROM
                dash_td
            WHERE
                YEAR(Fecha) < YEAR(CURDATE())
            GROUP BY Fecha , OAfiliado) a
        GROUP BY Fecha , AfiliadoOK
        HAVING AfiliadoOK IS NOT NULL";

        $qlast=$query;

if( $result = $connectdb -> query( $query ) ){
  while( $fila = $result -> fetch_assoc() ){
    $fecha = $fecha_int[ $fila[ 'Fecha' ] ];

    $calcMeta[ $fecha ][ $fila[ 'AfiliadoOK' ] ] = $fila[ 'Monto' ];

  }
}

//Venta por dia Last Year + crecimiento
$query="SELECT
            Fecha,
            IF(OAfiliado IS NOT NULL,
                IF(OAfiliado = 'PDV',
                    'PDV',
                    'Total COM'),
                NULL) AS AfiliadoOK,
            SUM(Monto) * IF(OAfiliado IS NOT NULL,
                IF(OAfiliado = 'PDV',
                    ".$var['crecimiento_pdv'].",
                    ".$var['crecimiento_com']."),
                0) AS Monto
        FROM
            (SELECT
                ADDDATE(Fecha, 364) AS Fecha, OAfiliado, SUM(Monto) AS Monto
            FROM
                dash_td
            WHERE
                YEAR(Fecha) < YEAR(CURDATE())
            GROUP BY Fecha , OAfiliado) a
        GROUP BY Fecha , AfiliadoOK
        HAVING AfiliadoOK IS NOT NULL";

if( $result = $connectdb -> query( $query ) ){
  while( $fila = $result -> fetch_assoc() ){
    $fecha = $fecha_int[ $fila[ 'Fecha' ] ];

    if( $fila['AfiliadoOK'] != 'PDV' ){

      $tmp['COM'] = $fila[ 'Monto' ] * 0.45;
      $tmp['CC'] = $fila[ 'Monto' ] * 0.55 * 0.7;
      $tmp['OB'] = $fila[ 'Monto' ] * 0.55 * 0.3;

      $factCrecimiento[ $fecha ][ 'COM' ]   = $tmp[ 'COM' ]   / $calcMeta[ $fecha ][ 'COM' ];
      $factCrecimiento[ $fecha ][ 'CC' ]    = $tmp[ 'CC' ]    / $calcMeta[ $fecha ][ 'CC' ];
      $factCrecimiento[ $fecha ][ 'OB' ]    = $tmp[ 'OB' ]    / $calcMeta[ $fecha ][ 'OB' ];
    }
  }
}

$metaDay = NULL;
$metaAcum = NULL;

//ADD Factor to ammounts LY
$i=0;
foreach($chartDay[ 'Monto' ][ '2016' ] as $day => $info){
  foreach($info['Total'] as $hour => $info2){

      $metaDay[ 'Monto' ][ $day ][ 'CC' ][ $hour ]  = $chartDay[ 'Monto' ][ '2016' ][ $day ][ 'CC' ][ $hour ] * $factCrecimiento[ $day ][ 'CC' ];
      $metaDay[ 'Monto' ][ $day ][ 'COM' ][ $hour ] = $chartDay[ 'Monto' ][ '2016' ][ $day ][ 'COM' ][ $hour ] * $factCrecimiento[ $day ][ 'COM' ];
      $metaDay[ 'Monto' ][ $day ][ 'OB' ][ $hour ]  = $chartDay[ 'Monto' ][ '2016' ][ $day ][ 'OB' ][ $hour ] * $factCrecimiento[ $day ][ 'OB' ];

      $metaDay[ 'Monto' ][ $day ][ 'PDV' ][ $hour ] = $chartDay[ 'Monto' ][ '2016' ][ $day ][ 'PDV' ][ $hour ] * $var['crecimiento_pdv'];

      $metaDay[ 'Monto' ][ $day ][ 'Total COM' ][ $hour ] = $metaDay[ 'Monto' ][ $day ][ 'CC' ][ $hour ] + $metaDay[ 'Monto' ][ $day ][ 'COM' ][ $hour ] + $metaDay[ 'Monto' ][ $day ][ 'OB' ][ $hour ];
      $metaDay[ 'Monto' ][ $day ][ 'Total' ][ $hour ]     = $metaDay[ 'Monto' ][ $day ][ 'Total COM' ][ $hour ] + $metaDay[ 'Monto' ][ $day ][ 'PDV' ][ $hour ];

      @$metaAcum[ 'Monto' ][ 'CC' ][]   = $metaAcum[ 'Monto' ]['CC'][$i-1] + ($metaDay[ 'Monto' ][ $day ][ 'CC' ][ $hour ] - $metaDay[ 'Monto' ][ $day ][ 'CC' ][ $hour - 1 ]);
      @$metaAcum[ 'Monto' ][ 'COM' ][]  = $metaAcum[ 'Monto' ]['COM'][$i-1]+ ($metaDay[ 'Monto' ][ $day ][ 'COM' ][ $hour ] - $metaDay[ 'Monto' ][ $day ][ 'COM' ][ $hour - 1 ]);
      @$metaAcum[ 'Monto' ][ 'OB' ][]   = $metaAcum[ 'Monto' ]['OB'][$i-1] + ($metaDay[ 'Monto' ][ $day ][ 'OB' ][ $hour ] - $metaDay[ 'Monto' ][ $day ][ 'OB' ][ $hour - 1 ]);

      @$metaAcum[ 'Monto' ][ 'PDV' ][]  = $metaAcum[ 'Monto' ]['PDV'][$i-1] + ($metaDay[ 'Monto' ][ $day ][ 'PDV' ][ $hour ] - $metaDay[ 'Monto' ][ $day ][ 'PDV' ][ $hour - 1 ]);

      @$metaAcum[ 'Monto' ][ 'Total COM' ][]  = $metaAcum[ 'Monto' ]['Total COM'][$i-1] + ($metaDay[ 'Monto' ][ $day ][ 'Total COM' ][ $hour ] - $metaDay[ 'Monto' ][ $day ][ 'Total COM' ][ $hour - 1 ]);
      @$metaAcum[ 'Monto' ][ 'Total' ][]      = $metaAcum[ 'Monto' ]['Total'][$i-1] + ($metaDay[ 'Monto' ][ $day ][ 'Total' ][ $hour ] - $metaDay[ 'Monto' ][ $day ][ 'Total' ][ $hour - 1 ]);

      $i++;
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
<script src="/js/highcharts/highcharts.js"></script>
<script src="/js/highcharts/modules/exporting.js"></script>
<script>

  dataAcum=<?php json($chartAcum); ?>;
  dataDay=<?php json($chartDay); ?>;
  metaDay=<?php json($metaDay); ?>;
  metaAcum=<?php json($metaAcum); ?>;
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
            text: 'Monto Acumulado '+group+' ($'+number_format(totales['ad'][group],2,'.',',')+')',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Venta 22 al 31 de Mayo',
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
            <?php
            echo $plotbands;
            ?>
          ]
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

    var titleMont = (typeof totales[group] === 'undefined') ? 0 : totales[group][day];

    fecha=fecha_json[day];

    Highcharts.chart(container, {
        chart: {
          backgroundColor: bg,
          type: 'spline'
        },

        title: {
            text: 'Monto '+fecha+' '+group+' ($'+number_format(titleMont,2,'.',',')+')',
            style: {
                color: '#eff3ff',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Venta 22 al 31 de Mayo',
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
    printChartTotal('OB','OB','#67686d');
    printChartTotal('Total','Total','#30333d');
    printChartTotal('Total COM','Total COM','#30333d');

    det=0;
  }

  function pDay(day){
    printChart('COM','COM','#67686d',day);
    printChart('PDV','PDV','#30333d',day);
    printChart('CC','CC','#67686d',day);
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
      location = '/venta-live/dashboard.php?det='+det;
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

<?php

include_once('../modules/modules.php');

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
  $ptChannels="295,355,192,891,193,178,410,556,601,602,603,636,637,708,806";
}

$query="SELECT dashboard,query FROM monitor_kpiLive_modules WHERE pais='CO' AND dashboard!='MT'";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $tmptxt=$fila['query'];
    @$canales.=str_replace('$ptChannels',$ptChannels,str_replace("DepOK","dep",str_replace("a.","b.",substr($tmptxt,0,strpos($tmptxt,"'")+1).$fila['dashboard']."' ")));
    
  }
}

$afiliado['Total']=1;
$afiliado['Total COM TB']=1;
$afiliado['Total COM PT']=1;

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
                        Fecha BETWEEN '".$var['ly']['inicio']."' AND '".$var['ly']['fin']."' OR Fecha BETWEEN '".$var['td']['inicio']."' AND '".$var['td']['fin']."'
                GROUP BY Localizador; 

        ALTER TABLE creators_td ADD PRIMARY KEY (Localizador);

        INSERT INTO creators_td (SELECT * FROM (SELECT Localizador, asesor FROM d_Locs WHERE Fecha BETWEEN IF(ADDDATE(CURDATE(),-1)<'".$var['td']['inicio']."','".$var['td']['inicio']."',ADDDATE(CURDATE(),-1)) AND CURDATE()) a ) ON DUPLICATE KEY UPDATE asesor=a.asesor; 

        CREATE TEMPORARY TABLE locs_shown 
                SELECT * 
            FROM 
                        t_hoteles 
                WHERE 
                        categoryId=1 AND
                        Fecha BETWEEN '".$var['ly']['inicio']."' AND '".$var['ly']['fin']."' OR Fecha BETWEEN '".$var['td']['inicio']."' AND '".$var['td']['fin']."'; 

        ALTER TABLE locs_shown ADD PRIMARY KEY (Localizador, item, Venta, Fecha, Hora);

        INSERT INTO locs_shown (SELECT * FROM (SELECT * FROM d_hoteles WHERE categoryId=1 AND Fecha BETWEEN IF(ADDDATE(CURDATE(),-1)<'".$var['td']['inicio']."','".$var['td']['inicio']."',ADDDATE(CURDATE(),-1)) AND CURDATE()) a ) ON DUPLICATE KEY UPDATE Venta=a.Venta; 

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
        
        CREATE TEMPORARY TABLE rnights SELECT Fecha, OAfiliado, SUM(Monto) as Monto, SUM(IF(VentaMXN>0,RN,0)) as RN
            FROM
                    (SELECT 
                                    Fecha, Localizador, item, SUM(VentaMXN) as VentaMXN,
                                    CASE 
                                            WHEN chanId IN (295,355) THEN 'Outlet' 
                                            $canales
                                    END as OAfiliado, 
                                    COUNT(DISTINCT VentaHoy) as Locs, 
                                    SUM(MontoDia) as Monto,RN 
                    FROM 
                            (SELECT 
                                    a.*, AfiliadoOK as Canal, dep 
                            FROM 
                                    dashboard_venta a 
                            LEFT JOIN 
                                    chanIds b ON a.chanId=b.id 
                            LEFT JOIN 
                                    dep_asesores c ON a.asesor=c.asesor AND a.Fecha=c.Fecha) b 
                    LEFT JOIN 
                            chanIds c ON b.chanId=c.id 
                    GROUP BY 
                            Fecha, Localizador, item
                    ORDER BY 
                            Fecha) a
            GROUP BY 
                    Fecha, Oafiliado";
//echo $query;

$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}

//Generate RN info
$query="SELECT * FROM rnights ORDER BY Fecha, OAfiliado";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    
    //Asigna entero a fecha para arreglos
    $fecha=$fecha_int[$fila['Fecha']];
    
    //Distribuye la información a un arreglo tipo Afiliado->Año->Fecha
    $rn[$fila['OAfiliado']][date('Y', strtotime($fila['Fecha']))][$fecha]=$fila['RN'];
      
    //Agrupa totales 
    switch($fila['OAfiliado']){
      case 'COM PT':
      case 'CC PT':
      case 'OB':
        @$rn['Total COM PT'][date('Y', strtotime($fila['Fecha']))][$fecha]+=$fila['RN'];
        break;
      case 'COM TB':
      case 'CC TB':
      case 'OB':
        @$rn['Total COM TB'][date('Y', strtotime($fila['Fecha']))][$fecha]+=$fila['RN'];
        break;
    }
      
    @$rn['Total'][date('Y', strtotime($fila['Fecha']))][$fecha]+=$fila['RN'];
  }
}else{
  echo "Error en info de RNights -> ".$connectdb->error." ON $query";
}

//Crea Categorías
$query="SELECT DISTINCT Fecha FROM rnights WHERE YEAR(Fecha)=YEAR(CURDATE()) ORDER BY Fecha";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    
    //Categoría por fecha para gráficas
    $cat[]=utf8_encode($fila['Fecha']);
  }
}else{
  echo "Error al obtener info de tabla para categorizar -> ".$connectdb->error." ON $query";
}

        
//Generate Totales
$query="SELECT YEAR(Fecha) as year, OAfiliado, SUM(RN) as RN FROM rnights GROUP BY YEAR(Fecha), OAfiliado ORDER BY Fecha";
if($result=$connectdb->query($query)){
    while($fila=$result->fetch_assoc()){
        $total[$fila['OAfiliado']][$fila['year']]=intval($fila['RN']);

        //Agrupa totales 
        switch($fila['OAfiliado']){
          case 'COM PT':
          case 'CC PT':
          case 'OB':
            @$total['Total COM PT'][$fila['year']]+=$fila['RN'];
            break;
          case 'COM TB':
          case 'CC TB':
          case 'OB':
            @$total['Total COM TB'][$fila['year']]+=$fila['RN'];
            break;
        }

        @$total['Total'][$fila['year']]+=$fila['RN'];
    }
}

//Create json data for graphics

foreach($rn as $afiliado => $info){
    foreach($info as $year => $info2){
        foreach($cat as $index => $category){
            $dataRN[$afiliado][$year][]=intval($info2[$index+1]);
            
            if($year==date('Y')){$totalDay[$afiliado]=intval($info2[$index+1]);}
        }
    }
}

$query="SELECT MAX(Last_Update) as lu FROM dashboard_venta";
if($result=$connectdb->query($query)){
    $fila=$result->fetch_assoc();
    $td['lu']=$fila['lu'];
}

$connectdb->close();

$td['dataRN']=$dataRN;
$td['cat']=$cat;
$td['total']=$total;
$td['totalDay']=$totalDay;

echo json_encode($td,JSON_PRETTY_PRINT);

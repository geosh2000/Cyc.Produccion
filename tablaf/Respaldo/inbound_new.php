<?php

include_once("../modules/modules.php");

initSettings::start(true,'tablas_f');
initSettings::printTitle('Tabla F Inbound');

$from=$_POST['start'];
$to=$_POST['end'];

$tbody="<td><input type='text' value='$from' id='start' name='start' required><input type='text' value='$to' id='end' name='end' required><input type='hidden' name='consultar'></td>";

Filters::showFilter('','POST','search','Consultar',$tbody);

?>
<script>
$(function(){
  $('#start').periodpicker({
    lang: 'en',
		formatDate: 'YYYY-MM-DD',
		end: '#end'
  });
});
</script>

<?php if(!isset($_POST['consultar'])){exit;} 

$connectdb=Connection::mysqliDB('CC');

$query="DROP TEMPORARY TABLE IF EXISTS LocsTabla;
DROP TEMPORARY TABLE IF EXISTS callsTable;
DROP TEMPORARY TABLE IF EXISTS f_locs_date;
DROP TEMPORARY TABLE IF EXISTS f_calls_date;
DROP TEMPORARY TABLE IF EXISTS f_locs_all;
DROP TEMPORARY TABLE IF EXISTS f_calls_all;

CREATE TEMPORARY TABLE LocsTabla
SELECT 
	*, 
	CASE
		WHEN Canal='MP' AND Pais IN ('MX', 'AR', 'EC', 'PA', 'US') THEN
			CASE
				WHEN locs.Afiliado LIKE '%shop%' AND tipo NOT IN (1,2) AND dep=29 AND cc IS NULL THEN 'PDV-Presencial'
				WHEN ((locs.Afiliado LIKE '%shop%' AND tipo IN (2)) OR locs.Afiliado NOT LIKE '%shop%') AND dep=29 AND cc IS NULL THEN 'PDV-IN'
				WHEN ((locs.Afiliado LIKE '%shop%' AND tipo IN (1)) OR locs.Afiliado NOT LIKE '%shop%') AND dep=29 AND cc IS NULL THEN 'PDV-Out'
				WHEN dep=29 AND cc IS NOT NULL THEN 'CC-Apoyo'
				WHEN dep=5 THEN 'MP-OUT'
				WHEN dep NOT IN (5,29) THEN 'MP-IN'
			END
		WHEN Canal='MT' THEN
			CASE
				WHEN dep IS NOT NULL AND dep NOT IN (7,8) THEN 'MT-B2C'			
			END
	END as Grupo,
	IF(Venta!=0,Localizador,NULL) as NewLoc
FROM
(SELECT 
	a.*, MontoHotel, dep, puesto, Canal, Pais, cc
FROM t_Locs a 
LEFT JOIN dep_asesores b ON a.asesor=b.asesor AND a.Fecha=b.Fecha  
LEFT JOIN cc_apoyo c ON a.asesor=c.asesor AND a.Fecha BETWEEN c.inicio AND c.fin
LEFT JOIN chanIds d ON a.chanId=d.id
LEFT JOIN 
	(SELECT Localizador, Fecha, Hora, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as MontoHotel FROM t_hoteles WHERE Fecha BETWEEN '$from' AND '$to' GROUP BY Localizador, Fecha, Hora) e 
	ON a.Localizador=e.Localizador AND a.Fecha=e.Fecha AND a.Hora=e.Hora
WHERE a.Fecha BETWEEN '$from' AND '$to' ) locs;

CREATE TEMPORARY TABLE callsTable 
SELECT a.*, dep, cc
FROM
(SELECT 
	a.*, Skill
FROM t_Answered_Calls a 
LEFT JOIN Cola_Skill b ON a.Cola=b.Cola 
WHERE a.Fecha BETWEEN '$from' AND '$to'
HAVING Skill IN (3,35))a
LEFT JOIN dep_asesores c ON a.asesor IS NOT NULL AND a.asesor=c.asesor AND a.Fecha=c.Fecha 
LEFT JOIN cc_apoyo d ON a.asesor IS NOT NULL AND a.asesor=d.asesor AND a.Fecha BETWEEN d.inicio AND d.fin;


CREATE TEMPORARY TABLE f_locs_date
SELECT 
	Fecha, Grupo, 
	SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(MontoHotel) as MontoHotel, COUNT(DISTINCT NewLoc) as Locs
FROM
LocsTabla a
GROUP BY
Fecha, Grupo;

CREATE TEMPORARY TABLE f_locs_all
SELECT 
	Fecha, Grupo, 
	SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(MontoHotel) as MontoHotel, COUNT(DISTINCT NewLoc) as Locs
FROM
LocsTabla a
GROUP BY
Grupo;

CREATE TEMPORARY TABLE f_calls_date
SELECT
	Fecha,
	Skill,
	CASE
		WHEN Skill=35 THEN
			CASE
				WHEN dep=29 AND cc IS NULL THEN 'PDV-IN'
				WHEN dep=29 AND cc IS NOT NULL THEN 'CC-Apoyo'
				ELSE 'MP-IN'
			END
		WHEN Skill=3 THEN 'MT-B2C'
	END as Grupo,
	COUNT(ac_id) as Ofrecidas,
	COUNT(IF(Answered=1,ac_id,NULL)) as Contestadas,
	COUNT(IF(Answered!=1,ac_id,NULL)) as Abandonadas,
	COUNT(IF(Answered=1 AND Desconexion='Transferida' AND Duracion_Real<'00:02:00',ac_id,NULL)) as Transferidas_min2,
	COUNT(IF(Answered=1 AND Espera<= 
		CASE
			WHEN Skill IN (3,35) THEN 20
			ELSE 30
		END
	,ac_id,NULL)) as SLA,
	SUM(TIME_TO_SEC(IF(Answered=1,Duracion_Real,0))) as TalkingTime,
	SUM(TIME_TO_SEC(IF(Answered=1,Espera,0))) as WaitTime
FROM
	callsTable a
GROUP BY
	Fecha, Grupo;

CREATE TEMPORARY TABLE f_calls_all
SELECT
	Fecha,
	Skill,
	CASE
		WHEN Skill=35 THEN
			CASE
				WHEN dep=29 AND cc IS NULL THEN 'PDV-IN'
				WHEN dep=29 AND cc IS NOT NULL THEN 'CC-Apoyo'
				ELSE 'MP-IN'
			END
		WHEN Skill=3 THEN 'MT-B2C'
	END as Grupo,
	COUNT(ac_id) as Ofrecidas,
	COUNT(IF(Answered=1,ac_id,NULL)) as Contestadas,
	COUNT(IF(Answered!=1,ac_id,NULL)) as Abandonadas,
	COUNT(IF(Answered=1 AND Desconexion='Transferida' AND Duracion_Real<'00:02:00',ac_id,NULL)) as Transferidas_min2,
	COUNT(IF(Answered=1 AND Espera<= 
		CASE
			WHEN Skill IN (3,35) THEN 20
			ELSE 30
		END
	,ac_id,NULL)) as SLA,
	SUM(TIME_TO_SEC(IF(Answered=1,Duracion_Real,0))) as TalkingTime,
	SUM(TIME_TO_SEC(IF(Answered=1,Espera,0))) as WaitTime
FROM
	callsTable a
GROUP BY
	Grupo;";

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
	b.*, (Locs / (Contestadas-Transferidas_min2))*100 as FC, Ofrecidas, Contestadas, Abandonadas, Abandonadas/Ofrecidas*100 as Abandon, Transferidas_min2, SLA/Ofrecidas*100 as SLA, TalkingTime/Contestadas as AHT, WaitTime/Contestadas as ASA,
	sLA as SLACalls, TalkingTime, WaitTime
FROM 
	f_calls_date a
RIGHT JOIN
	f_locs_date b
ON a.Fecha=b.Fecha AND a.Grupo=b.Grupo;";

if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array()){
    for($i=0;$i<$result->field_count;$i++){
      $data[$fila[0]][$fila[1]][$fields[$i]->name]=$fila[$i];
      
      switch($fila[1]){
        case 'MP-IN':
        case 'PDV-IN':
        case 'CC-Apoyo':
          @$data[$fila[0]]['All-MP-IN'][$fields[$i]->name]+=$fila[$i];
          break;
        case 'MP-OUT':
        case 'PDV-OUT':
          @$data[$fila[0]]['All-MP-OUT'][$fields[$i]->name]+=$fila[$i];
          break;
      }
    }
    
    if(isset($data[$fila[0]]['All-MP-IN'])){
      @$data[$fila[0]]['All-MP-IN']['SLA']=$data[$fila[0]]['All-MP-IN']['SLACalls']/$data[$fila[0]]['All-MP-IN']['Ofrecidas']*100;
      @$data[$fila[0]]['All-MP-IN']['Abandon']=$data[$fila[0]]['All-MP-IN']['Abandonadas']/$data[$fila[0]]['All-MP-IN']['Ofrecidas']*100;
      @$data[$fila[0]]['All-MP-IN']['AHT']=$data[$fila[0]]['All-MP-IN']['TalkingTime']/$data[$fila[0]]['All-MP-IN']['Contestadas'];
      @$data[$fila[0]]['All-MP-IN']['ASA']=$data[$fila[0]]['All-MP-IN']['WaitTime']/$data[$fila[0]]['All-MP-IN']['Contestadas'];
      @$data[$fila[0]]['All-MP-IN']['FC']=$data[$fila[0]]['All-MP-IN']['Locs']/($data[$fila[0]]['All-MP-IN']['Contestadas']-$data[$fila[0]]['All-MP-IN']['Transferidas_Min2'])*100;
    }
    
    if(isset($data[$fila[0]]['All-MP-OUT'])){
      @$data[$fila[0]]['All-MP-OUT']['SLA']=$data[$fila[0]]['All-MP-OUT']['SLACalls']/$data[$fila[0]]['All-MP-OUT']['Ofrecidas']*100;
      @$data[$fila[0]]['All-MP-OUT']['Abandon']=$data[$fila[0]]['All-MP-OUT']['Abandonadas']/$data[$fila[0]]['All-MP-OUT']['Ofrecidas']*100;
      @$data[$fila[0]]['All-MP-OUT']['AHT']=$data[$fila[0]]['All-MP-OUT']['TalkingTime']/$data[$fila[0]]['All-MP-OUT']['Contestadas'];
      @$data[$fila[0]]['All-MP-OUT']['ASA']=$data[$fila[0]]['All-MP-OUT']['WaitTime']/$data[$fila[0]]['All-MP-OUT']['Contestadas'];
      @$data[$fila[0]]['All-MP-OUT']['FC']=$data[$fila[0]]['All-MP-OUT']['Locs']/($data[$fila[0]]['All-MP-OUT']['Contestadas']-$data[$fila[0]]['All-MP-OUT']['Transferidas_Min2'])*100;
    }
  }
}else{
  echo "ERROR! -> ".$connectdb->error;
}

$cat=array('All-MP-IN'=>0,
            'MP-IN'=>0,
            'CC-Apoyo'=>0,
            'PDV-IN'=>0,
            'All-MP-OUT'=>0,
            'MP-OUT'=>0,
            'PDV-Out'=>0,
            'PDV-Presencial'=>0,
            'MT-B2C'=>0);

$query="SELECT
	b.*, Locs/(Contestadas-Transferidas_min2)*100 as FC, Ofrecidas, Contestadas, Abandonadas, Abandonadas/Ofrecidas*100 as Abandon, Transferidas_min2, SLA/Ofrecidas*100 as SLA, TalkingTime/Contestadas as AHT, WaitTime/Contestadas as ASA,
	sLA as SLACalls, TalkingTime, WaitTime
FROM 
	f_calls_all a
RIGHT JOIN
	f_locs_all b
ON a.Grupo=b.Grupo;";

if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array()){
    for($i=0;$i<$result->field_count;$i++){
      $data['Total'][$fila[1]][$fields[$i]->name]=$fila[$i];
      
      @$cat[$fila[1]]++;
      
      switch($fila[1]){
        case 'MP-IN':
        case 'PDV-IN':
        case 'CC-Apoyo':
          @$data['Total']['All-MP-IN'][$fields[$i]->name]+=$fila[$i];
          @$cat['All-MP-IN']++;
          break;
        case 'MP-OUT':
        case 'PDV-OUT':
          @$data['Total']['All-MP-OUT'][$fields[$i]->name]+=$fila[$i];
          @$cat['All-MP-OUT']++;
          break;
      }
      
    }
    
    if(isset($data['Total']['All-MP-IN'])){
      @$data['Total']['All-MP-IN']['SLA']=$data['Total']['All-MP-IN']['SLACalls']/$data['Total']['All-MP-IN']['Ofrecidas']*100;
      @$data['Total']['All-MP-IN']['Abandon']=$data['Total']['All-MP-IN']['Abandonadas']/$data['Total']['All-MP-IN']['Ofrecidas']*100;
      @$data['Total']['All-MP-IN']['AHT']=$data['Total']['All-MP-IN']['TalkingTime']/$data['Total']['All-MP-IN']['Contestadas'];
      @$data['Total']['All-MP-IN']['ASA']=$data['Total']['All-MP-IN']['WaitTime']/$data['Total']['All-MP-IN']['Contestadas'];
      @$data['Total']['All-MP-IN']['FC']=$data['Total']['All-MP-IN']['Locs']/($data['Total']['All-MP-IN']['Contestadas']-$data['Total']['All-MP-IN']['Transferidas_Min2'])*100;
    }
    
    if(isset($data['Total']['All-MP-OUT'])){
      @$data['Total']['All-MP-OUT']['SLA']=$data['Total']['All-MP-OUT']['SLACalls']/$data['Total']['All-MP-OUT']['Ofrecidas']*100;
      @$data['Total']['All-MP-OUT']['Abandon']=$data['Total']['All-MP-OUT']['Abandonadas']/$data['Total']['All-MP-OUT']['Ofrecidas']*100;
      @$data['Total']['All-MP-OUT']['AHT']=$data['Total']['All-MP-OUT']['TalkingTime']/$data['Total']['All-MP-OUT']['Contestadas'];
      @$data['Total']['All-MP-OUT']['ASA']=$data['Total']['All-MP-OUT']['WaitTime']/$data['Total']['All-MP-OUT']['Contestadas'];
      @$data['Total']['All-MP-OUT']['FC']=$data['Total']['All-MP-OUT']['Locs']/($data['Total']['All-MP-OUT']['Contestadas']-$data['Total']['All-MP-OUT']['Transferidas_Min2'])*100;
    }
  }
}else{
  echo "ERROR! -> ".$connectdb->error;
}

$connectdb->close();

for($i=0;$i<$result->field_count-3;$i++){
	$dataheaders[]=utf8_encode(ucwords(str_replace("_"," ",$fields[$i]->name)));
}

unset($result);

//Create Headers
foreach($dataheaders as $index => $info){
	$t_headers[]=array("text"=>$info);
}

foreach($cat as $category => $info){
  if($info>0){
    //echo "$category<br>";
    foreach($data as $date => $val){
      if($date!='Total'){
        if(isset($val[$category])){
          foreach($val[$category] as $fieldname => $val2){
            switch($fieldname){
              case 'Grupo':
                $valor=str_replace("-"," ",$category);
                $class='ts_center';
                break;
              case 'Fecha':
                $valor=$date;
                $class='ts_center';
                break;
              case 'Monto':
              case 'MontoHotel':
                $valor="$ ".number_format($val2,2);
                $class='ts_right';
                break;
              case 'Abandon':
              case 'SLA':
              case 'FC':
                $valor=number_format($val2,2)." %";
                $class='ts_center';
                break;
              case 'AHT':
              case 'ASA':
                $valor=number_format($val2,2)." seg.";
                $class='ts_right';
                break;
              case 'Locs':
              case 'Ofrecidas':
              case 'Contestadas':
              case 'Abandonadas':
                $valor=number_format($val2,0);
                $class='ts_center';
                break;
              default:
                $valor=$val2;
                $class='ts_center';
                break;
            }
            switch($fieldname){
              case 'SLACalls':
              case 'TalkingTime':
              case 'WaitTime':
                break;
              default:
                $tmp_array[]=array("text"=> utf8_encode($valor), "class"=>$class);
                break;
            }
          }
          $row[$category][]=$tmp_array;
          unset($tmp_array);
        }
      }
    }
    
    $i=0;
    foreach($data['Total'][$category] as $fieldname => $val2){
      if($i>=1){
        switch($fieldname){
            case 'Grupo':
              $valor=str_replace("-"," ",$category);
              $class='ts_center';
              break;
            case 'Monto':
            case 'MontoHotel':
              $valor="$ ".number_format($val2,2);
              $class='ts_right';
              break;
            case 'Abandon':
            case 'SLA':
            case 'FC':
              $valor=number_format($val2,2)." %";
              $class='ts_center';
              break;
            case 'AHT':
            case 'ASA':
              $valor=number_format($val2,2)." seg.";
              $class='ts_right';
              break;
            case 'Locs':
            case 'Ofrecidas':
            case 'Contestadas':
            case 'Abandonadas':
              $valor=number_format($val2,0);
              $class='ts_center';
                break;
            default:
              $valor=$val2;
              $class='ts_center';
              break;
         }
         switch($fieldname){
            case 'SLACalls':
            case 'TalkingTime':
            case 'WaitTime':
              break;
            default:
              $tmp_array[]=utf8_encode($valor);
              break;
          }
       }else{
        $tmp_array[]='TOTAL';
       }
      $i++;
    }
    $foot[$category]=array("text"=>$tmp_array);
    unset($tmp_array);
    
    $table[$category]=array('headers'=>array($t_headers), 'footers'=>$foot[$category], 'rows'=>$row[$category]);
    
    /*echo "$category: ";
    echo json_encode($table[$category],JSON_PRETTY_PRINT);
    echo "<br>";*/
  }
}

unset($table['']);


?>
<style>
.ts_right{
  text-align: right;
}

.ts_center{
  text-align: center;
}

.tablesorter-jui tfoot th{
  text-align: center;
  font-size: 12px;
  font-weight: bolder !important;
}
</style>
<script>

dataTables=<?php echo json_encode($table,JSON_PRETTY_PRINT); ?>;

$(function(){

  $.each(dataTables,function(key,value){
    $('#results ul').append("<li><a href='#content-"+key+"'>"+key+"</a></li>");
    $('#results').append("<div id='content-"+key+"'><div id='result-table-"+key+"'></div><button class='button button_green_w export' ref='result-table-"+key+"'>Exportar</button></div>");
    
    $("#result-table-"+key).tablesorter({
      theme: 'jui',
		  headerTemplate: '{content} {icon}',
		  widgets: ['zebra','uitheme','filter','output'],
      data : value, // same as using build_source (build_source would override this)
      widgetOptions : {
        // *** build object options ***
        build_objectRowKey    : 'rows',    // object key containing table rows
        build_objectHeaderKey : 'headers', // object key containing table headers
        build_objectFooterKey : 'footers',  // object key containing table footers
        
        //Filters
        filter_childRows: false,
        filter_columnFilters: true,
        filter_cssFilter: "tablesorter-filter",
        filter_functions: null,
        filter_hideFilters: false,
        filter_ignoreCase: true,
        filter_reset: null,
        filter_searchDelay: 300,
        filter_startsWith: false,
        filter_useParsedData: false,
                
        //Outputs
        output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
        output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)
        output_hiddenColumns : false,       // include hidden columns in the output
        output_includeFooter : true,        // include footer rows in the output
        output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
        output_headerRows    : true,        // output all header rows (multiple rows)
        output_delivery      : 'd',         // (p)opup, (d)ownload
        output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
        output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
        output_replaceQuote  : '\u201c;',   // change quote to left double quote
        output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
        output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
        output_wrapQuotes    : false,       // wrap every cell output in quotes
        output_popupStyle    : 'width=580,height=310',
        output_saveFileName  : 'tf_'+key+'_<?php echo $from."a".$to;?>.csv',
        // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
        output_encoding      : 'data:application/octet-stream;charset=utf8,'
      }
    });
  });

  $( "#results" ).tabs();
  
  $(document).on('click','.export',function(){
    $('#'+$(this).attr('ref')+' .tablesorter').trigger('outputTable');
  });
  
});
</script>
<br>
<div id='results' style='width: 90%; margin: auto'>
  <ul>
  </ul>
</div>

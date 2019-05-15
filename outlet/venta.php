<?php
include_once('../modules/modules.php');

initSettings::start(true);
initSettings::printTitle('Ventas Outlet');

$inicio=$_POST['inicio'];
$fin=$_POST['fin'];
$hg_i=$_POST['hg_i'];
$hg_f=$_POST['hg_f'];

$tbody="<td><input type='text' value='$inicio' name='inicio' id='inicio'><input type='text' value='$fin' name='fin' id='fin'></td>";
$tbody.="<td>Hora Inicio</td><td><input type='number' value='$hg_i' name='hg_i' id='hg_i' min=0 max=23 step=0.5></td>";
$tbody.="<td>Hora Fin</td><td><input type='number' value='$hg_f' name='hg_f' id='hg_f' min=0 max=23 step=0.5></td>";

Filters::showFilter('','POST','search','Consulta',$tbody);

?>
<script>

$(function(){
  
  $('#inicio').periodpicker({
    end: '#fin',
    lang: 'en',
    startMonth: 5,
    formatDate: 'YYYY-MM-DD',
    animation: true
  });
  
  $('#hg_i').change(function(){
    var hgf=$('#hg_f').val();
    
    if(hgf=='' || parseFloat(hgf)<=parseFloat($(this).val())){
      $('#hg_f').val(parseFloat($(this).val())+0.5).attr('min',parseFloat($(this).val())+0.5);
    }
  });

  

});
</script>
<?php

if(!isset($_POST['inicio'])){exit;}

$connectdb=Connection::mysqliDB('CC');

$query="SELECT 
          CONCAT(Fecha,'-',asesor,'-',branchid) as id,
          Fecha,
          NombreAsesor(asesor,2) as Nombre_Asesor,
          PDV, Grupo,
          COUNT(DISTINCT NewLoc) as Locs,
          SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto,
          SUM(MontoHotel) as MontoHotel,
          SUM(RN) as RN,
          outlet
        FROM 
          (SELECT a.*, RN, MontoHotel, outlet, IF(Venta!=0,a.Localizador,NULL) as NewLoc, HOUR(a.Hora)+IF(MINUTE(a.Hora)>=30,.5,0) as HG, PDV, 
          	CASE
          		WHEN chanId=355 AND a.branchid IN (305,352) THEN 'Interjet'
          		WHEN chanId=295 AND a.branchid IN (305,302,300,299,298,351) THEN 'PriceTravel'
          		WHEN chanId=295 AND a.branchid IN (161,349,350,159,165,440,316,73,429) THEN 'Inlets'
          		else 'Otro'
          	END as Grupo
            FROM 
              d_Locs a 
            LEFT JOIN 
              asesores_outlet b ON a.asesor=b.asesor AND Fecha BETWEEN b.inicio AND b.fin 
            LEFT JOIN
              (SELECT Fecha, Hora, Localizador, RN, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as MontoHotel FROM d_hoteles WHERE Fecha BETWEEN '$inicio' AND '$fin' GROUP BY Localizador) c 
              ON a.Localizador=c.Localizador AND a.Fecha=c.Fecha AND a.Hora=c.Hora
            LEFT JOIN
              PDVs d ON a.branchid=d.branchid
            WHERE a.Fecha BETWEEN '$inicio' AND '$fin'
            HAVING
              outlet LIKE '%VyV 2017%') a
        WHERE
          HG BETWEEN $hg_i AND $hg_f 
        GROUP BY 
          Fecha, Nombre_Asesor, Grupo
      	HAVING Grupo!='Otro'";

if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array()){
    for($i=1;$i<$result->field_count;$i++){
      $data[$fila[0]][]=utf8_encode($fila[$i]);
    }
  }
}else{
  echo "ERROR! -> ".$connectdb->error."<br>ON<br>$query";
}

for($i=1;$i<$result->field_count;$i++){
  $headers[]=array('text'=>$fields[$i]->name);
}

foreach($data as $line => $info){
  $row[]=$info;
}

if(count($row)==0){
  echo "No existen resultados";
}

$table=array('headers'=>array($headers), 'rows'=>$row);

$connectdb->close();
?>
<script>

$(function(){
  
  $("#result-table").tablesorter({
      theme: 'jui',
		  headerTemplate: '{content} {icon}',
		  widgets: ['zebra','uitheme','filter','output'],
      data : <?php echo json_encode($table,JSON_PRETTY_PRINT); ?>, // same as using build_source (build_source would override this)
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
        output_saveFileName  : 'resultados.csv',
        // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
        output_encoding      : 'data:application/octet-stream;charset=utf8,'
      }
    });
    
    $('#export').click(function(){
      $('.tablesorter').trigger('outputTable');
    });
});
</script>
<br><br>
<button class='button button_green_w' id='export'>Exportar</button>
<div id='result-table' style='width: 80%; margin: auto;'></div>
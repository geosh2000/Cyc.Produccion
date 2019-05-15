<?php
include_once('../modules/modules.php');

initSettings::start(true,'citas_outlet');
initSettings::printTitle('Ingreso Citas Outlet');

timeAndRegion::setRegion('Cun');

if(isset($_POST['search'])){
  $inicio=$_POST['inicio'];
  $fin=$_POST['fin'];
}

$tbody="<td><input type='text' name='inicio' id='inicio' value='$inicio'><input type='text' name='fin' id='fin' value='$fin'><input type='hidden' name='search'></td>";

Filters::showFilter('','POST','search','Consultar',$tbody);

?>
<script>
$(function(){
  $('#inicio').periodpicker({
    end: '#fin',
    lang: 'en',
    minDate: '2017-05-11',
    yearsPeriod: [2017,2019],
    yearSizeInPixels: 100,
    startMonth: 5,
    formatDate: 'YYYY-MM-DD',
    animation: true
  });
});
</script>
<?php if(!isset($_POST['search'])){exit;}

$connectdb=Connection::mysqliDB('CC');

$query="SELECT * FROM outlet_citas WHERE CAST(cita as DATE) BETWEEN '$inicio' AND '$fin'";
if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array()){
    for($i=0;$i<$result->field_count;$i++){
      $data[$fila[0]][]=utf8_encode($fila[$i]);
    }
  }
}

for($i=0;$i<$result->field_count;$i++){
  $dataheaders[]=utf8_encode(ucwords(str_replace("_"," ",$fields[$i]->name)));
}

foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

foreach($data as $fieldname => $info){
  $row[]=$info;
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
        output_saveFileName  : 'citasOutlet.csv',
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



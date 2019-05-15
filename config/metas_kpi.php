<?php

include_once("../modules/modules.php");

initSettings::start(true,'config_metas_kpi');
initSettings::printTitle('Metas BO');

$t_month=$_POST['month'];
$t_year=$_POST['year'];

//month Select
for($i=1;$i<=12;$i++){
  
  if($t_month==$i){
    $select="selected";
  }else{
    $select="";
  }
  
  @$month.="<option value='$i' $select>$i</option>";
}

//year Select
for($i=17;$i<=20;$i++){

  if($t_year==($i+2000)){
    $select="selected";
  }else{
    $select="";
  }
  
  @$year.="<option value='20$i' $select>20$i</option>";
}

$tbody="<td>Mes:</td><td><select id='month' name='month' required><option value=''>Selecciona...</option>$month</select></td>";
$tbody.="<td>Año:</td><td><select id='year' name='year' required><option value=''>Selecciona...</option>$year</select></td>";
Filters::showFilter('','POST','submit','Consultar',utf8_encode($tbody));

if(!isset($_POST['month'])){exit;}

$connectdb=Connection::mysqliDB('CC');

$tipos=array('sla','fc', 'aht', 'abandon');

$i=0;
$y=0;
foreach($tipos as $index => $tipoOK){

  $query="SELECT id, Departamento, $t_month as mes, $t_year as anio, '$tipoOK' as tipo, meta, secundaria FROM metas_kpi a RIGHT JOIN PCRCs b ON a.skill=b.id AND mes=$t_month AND anio=$t_year AND '$tipoOK'=a.tipo WHERE id IN (3,35,4,7,8,9);";
  if($result=$connectdb->query($query)){
    
    $fields=$result->fetch_fields();
    
    while($fila=$result->fetch_array()){
      for($x=0;$x<$result->field_count;$x++){
      
        if($x<2){
          $css='left';
        }else{
          $css='center';
        }
        
        if($fila[$x]==NULL){
          $cell="";
        }else{
          $cell=$fila[$x];
        }
      
        $data[$i][]=array("text" => $cell, 'skill' => $fila[0], 'tipo' => $fila[4], 'month' => $fila[2], 'year' => $fila[3], 'col' => $fields[$x]->name, 'class'=>$css);
        //$data[$i][]=$fila[$x];
      }
      $i++;
    }
    
    if($y==0){
      for($x=0;$x<$result->field_count;$x++){
        $dataheaders[]=$fields[$x]->name;
      }
      $y++;
    }
    
  }else{
    echo "ERROR! -> ".$connectdb->error." ON <br>$query";
  }
  
}

$connectdb->close();

if(isset($data)){

  //Create Headers
  foreach($dataheaders as $index => $title){
    $headers[]=array('text'=>utf8_encode($title));
  }
  
  //Create Rows
  foreach($data as $id =>$info){
  $row[]=$info;
  }
  
  $table = array("rows" => $row,"headers"=>array($headers));
}
?>
<style>

.left{
  text-align: left;
}

.center{
  text-align: center;
}

</style>

<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script>
dataTable=<?php echo json_encode($table,JSON_PRETTY_PRINT);?>;

$(function(){
  //Build Table
  $('#result-table').tablesorter({
    theme: 'jui',
    headerTemplate: '{content} {icon}',
    widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders', 'editable'],
    tableClass: 'center',
    data: dataTable,
    sortList: [[1,0]],
    widgetOptions: {
    
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
        output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
        output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
        output_wrapQuotes    : false,       // wrap every cell output in quotes
        output_popupStyle    : 'width=580,height=310',
        output_saveFileName  : 'metas_bo.csv',
      
      // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
        output_encoding      : 'data:application/octet-stream;charset=utf8,',
        
        editable_columns       : [5,6], 
        editable_enterToAccept : true,
        editable_autoAccept    : true,
        editable_autoResort    : false, 
        editable_validate      : function(txt, orig, columnIndex, $element){
        
                                        validation=true;
                                        
                                        var $this = $element,
                                          newVal = $this.text(),
                                          skill=$this.closest('td').attr('skill'),
                                          tipo=$this.closest('td').attr('tipo'),
                                          col=$this.closest('td').attr('col'),
                                          month=$this.closest('td').attr('month'),
                                          year=$this.closest('td').attr('year');

                                          elemento=$element;

                                          sendRequest(skill, tipo, month, year, newVal,col);
                                          
                                          return txt;
                                           
                                        
                                },     
        editable_focused       : function(txt, columnIndex, $element) {
          $element.addClass('focused');
        },
        editable_blur          : function(txt, columnIndex, $element) {
          $element.removeClass('focused');
        },
        editable_selectAll     : function(txt, columnIndex, $element){
          return /^b/i.test(txt) && columnIndex === 0;
        },
        editable_wrapContent   : '<div>',       // wrap all editable cell content... makes this widget work in IE, and with autocomplete
        editable_trimContent   : true,          // trim content ( removes outer tabs & carriage returns )
        editable_noEdit        : 'no-edit',     // class name of cell that is not editable
        editable_editComplete  : 'editComplete' // event fired after the table content has been edited

      }
    }).children('tbody').on('editComplete', 'td', function(event, config){
      alert('edited');
      var $this = $(this),
        newVal = $this.text(),
        skill=$this.closest('td').attr('skill'),
        tipo=$this.closest('td').attr('tipo'),
        month=$this.closest('td').attr('month'),
        year=$this.closest('td').attr('year');

				elemento=$(this);

        if(validation==true){
            sendRequest(skill, tipo, month, year, newVal);
        }
        
        $this.addClass( 'editable_updated' ); // green background + white text
        setTimeout(function(){
          $this.removeClass( 'editable_updated' );
        }, 500);

  });
  
  function sendRequest(skill, modo, month, year, newVal,col){
    showLoader('Guardando Info');
    
    $.ajax({
      url:    'metas_kpi_query.php',
      type:   'POST',
      data:   {skill:skill, tipo:modo, month:month, year:year, newVal:newVal, col: col},
      dataType: 'json',
      success: function(array){
        data=array;
        
        if(data['status']==1){
          dialogLoad.dialog('close');
          
          showNoty('success','Guardado',4000);
          
          flag=true;
          
        }else{
          dialogLoad.dialog('close');
          showNoty('error',data['msg'],4000);
          
          flag=false;
        }
      },
      error: function( jqXHR, textStatus, errorThrown ) {
        flag=false;
      
        dialogLoad.dialog('close');
        if (jqXHR.status === 0) {
          showNoty('error','Not connect: Verify Network.',4000);
        } else if (jqXHR.status == 404) {
          showNoty('error','Requested page not found [404]',4000);
        } else if (jqXHR.status == 500) {
          showNoty('error','Internal Server Error [500].',4000);
        } else if (textStatus === 'parsererror') {
          showNoty('error','Requested JSON parse failed.',4000);
        } else if (textStatus === 'timeout') {
          showNoty('error','Time out error.',4000);
        } else if (textStatus === 'abort') {
          showNoty('error','Ajax request aborted.',4000);
        } else {
          showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);
        }
      }
    });
    
    return flag;
  }
});
</script>
<br>
<div style='margin: auto; width: 80%' id='result-table'></div>

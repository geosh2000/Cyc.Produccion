<?php

include_once("../modules/modules.php");

initSettings::start(true,'tablas_f');
initSettings::printTitle('Venta en Vivo de Hoteles');


?>
<style>
  .ts_right{
    text-align: right;
  }
  
  .ts_left{
    text-align: left;
  }
  
  .ts_center{
    text-align: center;
  }
  
  .ts_total{
    text-align: right;
    font-weight: bolder !important;
  }
  
  .ts_total_c{
    text-align: center;
    font-weight: bolder !important;
  }
  
  .tablesorter th{
    text-align: center !important;
  }
  
  .tablesorter-jui tfoot th{
    text-align: center;
    font-size: 12px;
    font-weight: bolder !important;
  }
</style>
<script>
$(function(){

  requestData();

  function requestData(){
    $.ajax({
      url: 'query_cadenas.php',
      dataType: 'json',
      success: function(array){
        data=array;
        
        if(data['status']==1){
          $('#result-table').empty();
          formatTable(data['table']);
          $('#lu').text(data['lu']);
        }else{
          showNoty('error',data['msg'],4000);
        }
      },
      error: function(){
        showNoty('error','Error de conexión',4000);
      }
    });
    
    setTimeout(function(){requestData()},60000);
  }

  function formatTable(data){
    $('#result-table').tablesorter({
      theme: 'jui',
      headerTemplate: '{content} {icon}',
      widgets: ['zebra','uitheme','filter','output'],
      data : data, // same as using build_source (build_source would override this)
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
        output_saveFileName  : 'cadenas_live.csv',
        // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
        output_encoding      : 'data:application/octet-stream;charset=utf8,'
      }
    });
  }
  
  $('#export').click(function(){
    $('.tablesorter').trigger('outputTable');
  });
});
</script>
<br>
<div style='margin: auto; width: 80%; text-align: center; font-size: 20px; color: #008CBA'>
<p id='lu'></p>
</div>
<div id='result-table' style='margin: auto; width: 80%'></div>
<div style='margin: auto; width: 80%'>
  <button class='button button_green_w' id='export'>Exportar</button>
</div>

<?php

include_once('../modules/modules.php');

initSettings::start(true,'tablas_f');
initSettings::printTitle('Información CC');

timeAndRegion::setRegion('Cun');

if(isset($_POST['consulta'])){
  $inicio=$_POST['inicio'];
  $fin=$_POST['fin'];
}else{
  $inicio=date('Y-m-d', strtotime('-8 days'));
  $fin=date('Y-m-d', strtotime('-1 days'));
}

$tbody="<td><input type='text' value='$inicio' id='start' name='inicio' required><input type='text' value='$fin' id='end' name='fin' required><input type='hidden' name='consulta'></td>";

Filters::showFilterNOFORM('search','Consultar',$tbody);

?>
<style>
  .t_right{
    text-align: right;
  }

  .t_center{
    text-align: center;
  }
</style>
<script>

$(function(){

  $('#start').periodpicker({
    end: '#end',
    lang: 'en',
    animation: true,
    dateFormat: 'YYYY-MM-DD'
  });

  $('#display').hide();

  function printTable(data){

    $('#result-table').tablesorter({
      theme: 'jui',
      headerTemplate: '{content} {icon}',
      widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders', 'editable'],
      tableClass: 'center',
      data: data,
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
        output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
        output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
        output_wrapQuotes    : false,       // wrap every cell output in quotes
        output_popupStyle    : 'width=580,height=310',
        output_saveFileName  : 'Participacion_cc_<?php echo $from."a".$to;?>.csv',
        // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
        output_encoding      : 'data:application/octet-stream;charset=utf8,',

        //Sticky
        stickyHeaders_attachTo : '#result-table'
      }
    });
  }

  function getData(){

    showLoader('Obteniendo Información', {my: "center", at: "center", of: $('#loadRef')});

    $.ajax({
      url: 'getPartCC.php',
      type: 'POST',
      data: {inicio: $('#start').val(), fin: $('#end').val()},
      dataType: 'json',
      success: function(array){
          data=array;
          dialogLoad.dialog('close');

          $('#result-table').empty();

          if(data['status']==1){
            printTable(data['table']);
            $('#display').show();
          }else{
            showNoty('error', data['msg'], 4000);
            $('#display').hide();
          }

        },
      error: function(){
          dialogLoad.dialog('close');
          showNoty('error','Error de conexión',4000);
        }
    })
  }

  $('#search').click(function(){
    getData();
  });

  $(document).on('click','#export',function(){
    $('.tablesorter').trigger('outputTable');
  });
});

</script>
<br>
<div id='loadRef'></div>
<div id='display'>
  <button class='button button_green_w' id='export'>Exportar</button>
  <div id='result-table'></div>
</div>

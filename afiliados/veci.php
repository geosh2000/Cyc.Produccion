<?php
if($_SESSION['default']==0){
  $query="SELECT * FROM afiliados_view WHERE users LIKE '%".$_SESSION['id']."%' AND afiliado='veci' ORDER BY afiliado";
  if($result=Queries::query($query)){
    if($result->num_rows==0){
      echo "No cuentas con los permisos para ver este reporte";
      exit;
    }
  }
}

$tbody="<td>Periodo</td><td><input type='text' id='fecha'><input type='text' id='fecha_f'></td>";
Filters::showFilterNOFORM('search','Consultar',$tbody);
?>

<script>
$(function(){

  $('#fecha').periodpicker({
    end: '#fecha_f',
    clearButtonInButton: true,
    formatDateTime: 'YYYY-MM-DD'
  });


	$('#search').click(function(){
			printTable();
  });

	function printTable(){
    $('#result-table').empty();

    var position = {my: 'center', at: 'top', of: $('#result-table')};
    showLoader('Obteniendo Información', position);

    $.ajax({
      url: 'getList.php',
      type: 'POST',
      data: {fechai: $('#fecha').val(), fechaf: $('#fecha_f').val(), reporte: <?php echo "'".$_GET['reporte']."'"; ?>},
      dataType: 'json',
      success: function(array){
          data=array;

          dialogLoad.dialog('close');

					$('#result-table').tablesorter({
              theme: 'jui',
              headerTemplate: '{content} {icon}',
              widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders'],
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
                      output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
                      output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                      output_wrapQuotes    : false,       // wrap every cell output in quotes
                      output_popupStyle    : 'width=580,height=310',
                      output_saveFileName  : 'reporteVeci_'+$('#inicio').val()+'-'+$('#fin').val()+'-VECI.csv',
                      // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                      output_encoding      : 'data:application/octet-stream;charset=utf8,',


              }
            });

            $('#result-table').prepend("<div style='width: 85%; margin: auto;'><button class='button button_blue_w' id='export'>Exportar</button>");
      	},
			error: function(){
				showNoty('error','Error al obtener información',3000);
			}
    });
	}

    $(document).on('click','#export',function(){
      $('.tablesorter').trigger('outputTable');
    });



});
</script>
<style>

.active{
  text-align: center
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
fieldset { padding:0; border:0; margin-top:25px; }
h1 { font-size: 1.2em; margin: .6em 0; }

.ui-dialog .ui-state-error { padding: .3em; }
.right{
  text-align: right;
}
.center{
  text-align: center;
}

</style>

<br>
<div id='result-table' style='width:95%; margin: auto'></div>

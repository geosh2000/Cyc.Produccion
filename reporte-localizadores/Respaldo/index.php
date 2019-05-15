<?php

include_once("../modules/modules.php");

initSettings::start(true,'reportes_localizadores');
initSettings::printTitle('Reporte de Localizadores');

timeAndRegion::setRegion('Cun');

Scripts::periodScript('inicio','fin');

$deplist="<select id='depsel'><option value=''>Selecciona...</option>";

$query="SELECT id, Departamento FROM PCRCs WHERE parent=1 ORDER BY Departamento";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		@$deplist.="<option value='".$fila['id']."'>".$fila['Departamento']."</option>";
	}
}

$deplist.="</select>";


$tbody="<td><input type='text' id='inicio'><input type='text' id='fin'></td><td>$deplist</td><td><button class='button button_green_w' id='pordia' tipo='pordia'>Por Dia</td>";

Filters::showFilterNOFORM('search','Por Loc',$tbody);

 ?>
 <script>
$(function(){

	$('#showInfo').hide();

	$('#search, #pordia').click(function(){
		var flag=true;

		if($('#inicio').val()==''){
			flag=false;
			showNoty('error','Debes seleccionar fechas',3000);
		}

		if($('#depsel').val()==''){
			flag=false;
			showNoty('error','Debes seleccionar un departamento',3000);
		}

		if(flag){
			if($(this).attr('tipo')=='pordia'){
        var tipo='pordia';
      }else{
        var tipo='locs';
      }
			$('#result-table').empty();
			printTable(tipo);
		}
	});

	function printTable(type){
    $('#result-table').empty();

    showLoader('Obteniendo Información');

    $.ajax({
      url: 'getLocs.php',
      type: 'POST',
      data: {inicio: $('#inicio').val(), fin: $('#fin').val(), dep: $('#depsel').val(), tipo: type},
      dataType: 'json',
      success: function(array){
          data=array;

          dialogLoad.dialog('close');

					$('#showInfo').show();

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
                      output_saveFileName  : 'locs_'+$('#inicio').val()+'-'+$('#fin').val()+'-'+$('#depsel option:selected').text()+'.csv',
                      // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                      output_encoding      : 'data:application/octet-stream;charset=utf8,',


              }
            });
      	},
			error: function(){
				$('#showInfo').hide();
				showNoty('error','Error al obtener información',3000);
			}
    });
	}

	$('#export').click(function(){
		$('.tablesorter').trigger('outputTable');
	})

});
 </script>
 <div id='showInfo'>
	 <div  style='width: 1200px; margin: auto'><button class='button button_blue_w' id='export'>Exportar</button></div>
   <div id='result-table' style='width: 1200px; margin: auto'></div>
 </div>

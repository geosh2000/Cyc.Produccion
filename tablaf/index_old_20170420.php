<?php

include_once('../modules/modules.php');

initSettings::start(true,'tablas_f');
initSettings::printTitle('Tabla F - MX');

timeAndRegion::setRegion('Cun');

//Get Variables
$dept=$_POST['depto'];
$from=$_POST['from'];
if($from==NULL){$from=date('Y-m-d', strtotime('-5 days'));}else{$from=date('Y-m-d', strtotime($_POST['from']));  }
$to=$_POST['to'];
if($to==NULL){$to=date('Y-m-d', strtotime('-1 days'));}else{$to=date('Y-m-d', strtotime($_POST['to']));  }
$classid=1;
$cheat=$_GET['cheat'];


$tbody="<td><select name='depto' required><option value=''>Selecciona...</option>";

$query="SELECT * FROM PCRCs WHERE parent=1 ORDER BY Departamento";
if($result=Queries::query($query)){
  while($fila=$result->fetch_assoc()){
    if($dept==$fila['id']){$sel=" selected";}else{$sel="";}
    $tbody.= "<option value='".$fila['id']."' $sel>".$fila['Departamento']."</option>";
  }
}

$tbody.="</select></td><td><input type='text' id='from' name='from' value='$from' required><input type='text' id='to' name='to' value='$to' required><input type='hidden' name='consulta' value=1></td>";

Filters::showFilterNOFORM('consulta', 'Consultar', $tbody);

?>

<script>
  $(function() {
    $('#from').periodpicker({
  		end: '#to',
  		lang: 'en',
  		<?php if($cheat!=1){echo "minDate: '2016.07.11',";} ?>
  		animation: true,
      dateFormat: 'YYYY-MM-DD'
  	});

    $('.tablesorter-childRow td').toggle();

        $('#tablesorter').tablesorter({
            theme: 'blue',
            sortList: [[1,1]],
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            cssChildRow : "tablesorter-childRow",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','filter','output'],
            widgetOptions: {
               uitheme: 'jui',
               columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_thead: true,
                filter_childRows: true,
                filter_columnFilters: true,
                filter_cssFilter: "tablesorter-filter",
                filter_functions: null,
                filter_hideFilters: false,
                filter_ignoreCase: true,
                filter_reset: null,
                filter_searchDelay: 300,
                filter_startsWith: false,
                filter_useParsedData: false,
                resizable: true,
                saveSort: true,
                stickyHeaders: "tablesorter-stickyHeader",
                 output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                  output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)
                  output_hiddenColumns : false,       // include hidden columns in the output
                  output_includeFooter : true,        // include footer rows in the output
                  output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                  output_headerRows    : true,        // output all header rows (multiple rows)
                  output_delivery      : 'd',         // (p)opup, (d)ownload
                  output_saveRows      : 'v',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                  output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                  output_replaceQuote  : '\u201c;',   // change quote to left double quote
                  output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                  output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                  output_wrapQuotes    : false,       // wrap every cell output in quotes
                  output_popupStyle    : 'width=580,height=310',
                  output_saveFileName  : 'tabla_f_pordia.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });

        $('#acumulado').tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','output'],
            widgetOptions: {
               uitheme: 'jui',
               columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_thead: true,
                resizable: true,
                saveSort: true,
                stickyHeaders: "tablesorter-stickyHeader",
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
                  output_saveFileName  : 'tabla_f_acumulado.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });


    // clicking the download button; all you really need is to
    // trigger an "output" event on the table
    $('#exportaacumulado').click(function(){
        $('#acumulado').trigger('outputTable');

    });
     $('#exportapordia').click(function(){
        $('#tablesorter').trigger('outputTable');

    });


    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });


    }
);
  </script>
<br>
<div id='result-table' style='width: 80%; margin: auto;'></div>

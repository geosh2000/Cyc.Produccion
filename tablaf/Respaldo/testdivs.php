<?php
include("../connectDB.php");
include("../common/scripts.php");

?>

<script>

$(function(){

    $('#tablesorter').tablesorter({
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

});

</script>

<?php

echo "<table id='tablesorter'>
        <thead>
            <tr>
                <th>A</th><th>B</th>
            <tr>
        </thead>
        <tbody>";

for($i=0;$i<5;$i++){
    echo "<tr id='tr$i'>
            <td id='td$i>$i</td>
            <td id='test$i></td>
            </tr>";
}

echo "  </tbody>
        </table>";


?>
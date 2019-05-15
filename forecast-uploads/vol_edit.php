<?php
include_once("../modules/modules.php");

initSettings::start(true,"schedules_upload");

$inicio=date('Y-m-d', strtotime($_POST['start']));
$fin=date('Y-m-d', strtotime($_POST['end']));
$skill=$_POST['skill'];

$query="SELECT Fecha, skill, factor, volumen, AHT, Reductores FROM forecast_volume WHERE skill=$skill AND Fecha BETWEEN '$inicio' AND '$fin' ORDER BY Fecha";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		$data[$fila['Fecha']]['factor']=$fila['factor'];
		$data[$fila['Fecha']]['volumen']=$fila['volumen'];
		$data[$fila['Fecha']]['AHT']=$fila['AHT'];
		$data[$fila['Fecha']]['Reductores']=$fila['Reductores'];
	}	
}

$query="SELECT Departamento FROM PCRCs WHERE id=$skill";
if($result=Queries::query($query)){
	$fila=$result->fetch_assoc();
	$depart=$fila['Departamento'];
}

?>

<style>
.tablesorter tbody > tr > td[contenteditable=true]:focus {
  outline: #08f 1px solid;
  background: #eee;
  resize: none;
}
td.no-edit, span.no-edit {
  background-color: rgba(230,191,153,0.5);
}
.focused {
  color: blue;
}
td.editable_updated {
  background-color: green;
  color: red;
}
</style>

<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script>
var status;
function sendRequest(fecha,field,newVal){
        $( "#process" ).dialog("open");
        var urlsend= "/json/formularios/forecast_volume_edit.php?fecha="+fecha+"&skill=<?php echo $skill; ?>&field="+field+"&newVal="+newVal;
        //document.getElementById('testresult').innerText=urlsend;
        var xmlhttp;
        var text;

        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else { // code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
            xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                text= xmlhttp.responseText;
                var status = text.match("status- (.*) -status");
                 var startlogin='no';
                var notif_msg = text.match("msg- (.*) -msg");
                if(status[1]=='OK'){
                    tipo_noti='success';
                    //$('#d'+id).hide('slow', function(){ $('#d'+id).remove(); });
                    status=true;
                }else{
                    tipo_noti='error';
                    status=false;
                }
                $( "#process" ).dialog("close");
                new noty({
                    text: notif_msg[1],
                    type: tipo_noti,
                    timeout: 5000,
                    animation: {
                        open: {height: 'toggle'}, // jQuery animate function property object
                        close: {height: 'toggle'}, // jQuery animate function property object
                        easing: 'swing', // easing
                        speed: 500 // opening & closing animation speed
                    }
                });

            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();


    }

$(function(){

    var validation;

     function checkRegexp( o, regexp) {
      if ( !( regexp.test( o ) ) ) {
        return false;
      } else {
        return true;
      }
    }

    $('#tableedit').tablesorter({
        theme: 'blue',
        headerTemplate: '{content}',
        widthFixed: false,
        widgets: [ 'zebra','filter', 'output', 'editable' ],
        widgetOptions: {

           uitheme: 'jui',
            columns: [
                "primary",
                "secondary",
                "tertiary"
                ],
            columns_tfoot: false,
            columns_thead: true,
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
            resizable: true,
            saveSort: true,
            output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
            output_ignoreColumns : [0],          // columns to ignore [0, 1,... ] (zero-based index)
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
            output_saveFileName  : 'cuartiles_<?php echo "$year"."_$month"."_$dep";?>.csv',
            // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
            output_encoding      : 'data:application/octet-stream;charset=utf8,',

            editable_columns       : [1,2,3,4],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
            editable_autoResort    : false,         // auto resort after the content has changed.
            editable_validate      : function(text, original, columnIndex){ validation=true; return text; },
            editable_focused       : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.addClass('focused');
            },
            editable_blur          : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.removeClass('focused');
            },
            editable_selectAll     : function(txt, columnIndex, $element){
              // note $element is the div inside of the table cell, so use $element.closest('td') to get the cell
              // only select everthing within the element when the content starts with the letter "B"
              return /^b/i.test(txt) && columnIndex === 0;
            },
            editable_wrapContent   : '<div>',       // wrap all editable cell content... makes this widget work in IE, and with autocomplete
            editable_trimContent   : true,          // trim content ( removes outer tabs & carriage returns )
            editable_noEdit        : 'no-edit',     // class name of cell that is not editable
            editable_editComplete  : 'editComplete' // event fired after the table content has been edited

        }
    }).children('tbody').on('editComplete', 'td', function(event, config){
      var $this = $(this),
        newContent = $this.text(),
        cellIndex = this.cellIndex, // there shouldn't be any colspans in the tbody
        rowIndex = $this.closest('tr').attr('id'),// data-row-index stored in row id
        fecha= $(this).attr('fecha');
        col = $(this).attr('col');
        if(validation==true){
        	//alert("fecha: " + fecha + " <br> field: " + col);
            sendRequest(fecha,col,newContent);
        }

      // Do whatever you want here to indicate
      // that the content was updated
      $this.addClass( 'editable_updated' ); // green background + white text
      setTimeout(function(){
        $this.removeClass( 'editable_updated' );
      }, 500);

      /*
      $.post("mysite.php", {
        "row"     : rowIndex,
        "cell"    : cellIndex,
        "content" : newContent
      });
      */
    });

});
</script>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>

$(function() {
    $('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});
});
</script>
<br>
<table class='t2' style='width:600px; margin:auto'><form action="vol_edit.php" method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=10>Editor de Volumenes (Forecast)</th>
	</tr>
	<tr class='title'>
		<td style='width:33%'>Periodo</td>
		<td style='width:33%'>Programa</td>
		<td class='total' rowspan=2><input type="submit" value="Consultar" name="submit"></td>
	</tr>
	<tr class='pair'>
		<td><input type='text' name='start' id='inicio' value='<?php echo $_POST['start']; ?>' required><input type='text' name='end' id='fin' value='<?php echo $_POST['end']; ?>' required></td>
		<td class='pair'><select name="skill" required><option value=''>Selecciona...</option>
			<?php  $query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
            if($result=Queries::query($query)){
              while($fila=$result->fetch_assoc()){
                echo "<option value='".$fila['id']."' ";
                if($fila['id']==$skill){ echo "selected";}
                echo ">".$fila['Departamento']."</option>";
              }
            }
     ?></select></td>
		
	</tr>
	
</form></table>
<br>
<table class='t2' style='width:600px; margin:auto'>
	<tr class='title'>
		<th colspan=10>Editor de Volumenes (<?php echo "$depart $inicio a $fin"; ?>) </th>
	</tr>
</table>
<br>
<br>
<table id='tableedit' style='margin: auto; width: 600; text-align: center'>
	<thead>
		<tr>
			<th>Fecha</th>
			<th>Factor</th>
			<th>Volumen</th>
			<th>AHT</th>
			<th>Reductores</th>
		</tr>
	</thead>
	<tbody>
			<?php
				for($i=date('Y-m-d',strtotime($inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
					echo "<tr>\n\t\t";
						echo "<td>$i</td>\n\t\t";
						echo "<td fecha='$i' col='factor'>".$data[$i]['factor']."</td>\n\t\t";
						echo "<td fecha='$i' col='volumen'>".$data[$i]['volumen']."</td>\n\t";
						echo "<td fecha='$i' col='AHT'>".$data[$i]['AHT']."</td>\n\t";
						echo "<td fecha='$i' col='Reductores'>".$data[$i]['Reductores']."</td>\n\t";
					echo "</tr>\n\t";
				}
			?>
	</tbody>
</table>
</body>

</html>

<?php
include_once("../modules/modules.php");

initSettings::start(true,'schedules_change');
initSettings::printTitle('Editar Horarios');

timeAndRegion::setRegion('Cun');

if(isset($_POST['start'])){$start=date('Y-m-d',strtotime($_POST['start']));}else{$start=date('Y-m-d',strtotime('now - 7 days'));}
if(isset($_POST['end'])){$end=date('Y-m-d',strtotime($_POST['end']));}else{$end=date('Y-m-d',strtotime('now'));}
$skill=$_POST['skill'];

$cun_time = new DateTimeZone('America/Bogota');

$connectdb=Connection::mysqliDB('CC');

$tbody="<td><input type='text' id='start' name='start' value='$start'><input type='text' id='end' name='end' value='$end'></td><td class='pair'><select name='skill' required><option value=''>Selecciona...</option>";

    $query="SELECT * FROM PCRCs ORDER BY Departamento";
    if($result=$connectdb->query($query)){
      while($fila=$result->fetch_assoc()){
        if($skill==$fila['id']){
          $selected="selected";
        }else{
          $selected="";
        }

        $tbody.= "<option value='".$fila['id']."' $selected>".$fila['Departamento']."</option>";
      }
    }

$tbody.="</select><input type='hidden' name='consulta' value=1></td>";

Filters::showFilter('','POST', 'consultar', 'Consultar', $tbody);

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

<script>
var status;

function sendRequest(id,field,newVal,fecha){
	showLoader('Guardando',{ my: "left top", at: "left bottom", of: elemento });

	$.ajax({
		url: "/json/formularios/schedules_update.php",
		type: "POST",
		data: {id: id, field: field, newVal: newVal, fecha: fecha},
		dataType: 'json',
		success: function(array){
			data=array;
			if(data['status']=='OK'){
				showNoty('success',"Cambio Guardado",3000);
			}else{
				showNoty('error',data['msg'],3000);
			}

			dialogLoad.dialog('close');

		},
		error: function(){
      dialogLoad.dialog('close');
			showNoty('error','Error de conexión',3000);
		}
	});
}

$(function(){

  $('#start').periodpicker({
    end: '#end',
    lang: 'en',
    animation: true
  });



});

</script>


<?php
if($_POST['consulta']!=1){exit;}

$query="SELECT
          CONCAT(YEAR(a.Fecha),MONTH(a.Fecha),DAY(a.Fecha),a.id) as uniqueID, b.id, a.Fecha, Nombre, `jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`, `extra2 start`, `extra2 end`
        FROM
        (SELECT 
          a.id, a.Fecha, Nombre, dep 
        FROM 
          (SELECT Fecha, a.* FROM Asesores a JOIN Fechas b WHERE Fecha BETWEEN '$start' AND '$end' AND  (Egreso >= '$start' AND Egreso IS NOT NULL)) a
        LEFT JOIN
          dep_asesores b ON a.Fecha=b.Fecha AND a.id=b.asesor HAVING dep IN ($skill) ORDER BY Nombre) a
        LEFT JOIN
        (SELECT
        	id, Fecha, asesor, `jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`, `extra2 start`, `extra2 end`
        FROM
        	`Historial Programacion`
        WHERE
        	Fecha BETWEEN '$start' AND '$end') b
        ON a.id=b.asesor AND a.Fecha=b.Fecha
        HAVING b.id IS NOT NULL";
if($result=$connectdb->query($query)){
	$field=$result->fetch_fields();
	$fcount=$result->field_count;

	while($fila=$result->fetch_array()){
		for($i=1;$i<$fcount;$i++){
			switch($field[$i]->type){
				case 11:
					$tmp = new DateTime($fila[2]." ".$fila[$i]." America/Mexico_City");
					$tmp -> setTimezone($cun_time);
					$data[$fila[0]][]=array("text"=> utf8_encode($tmp -> format('H:i:s')), "row" => $fila[1], "col" => $field[$i]->orgname);
					break;
				default:
					$data[$fila[0]][]=array("text"=> utf8_encode($fila[$i]), "row" => $fila[1], "col" => $field[$i]->orgname);
					break;
			}

		}
	}
}

for($i=1;$i<$fcount;$i++){
  $dataheaders[]=ucwords($field[$i]->name);
}

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($data as $id =>$info){
  $row[]=array("cells" => $info);
}

$table=array();
$table = array("rows" => $row,"headers"=>array($headers));


?>
<script>
$(function(){

  dataTable = <?php print json_encode($table, JSON_PRETTY_PRINT); ?>;

  var validation;

   function checkRegexp( o, regexp) {
    if ( !( regexp.test( o ) ) ) {
      return false;
    } else {
      return true;
    }
  }


  $('.tablesorter').tablesorter({
    theme: 'jui',
    headerTemplate: '{content} {icon}',
    widgets: ['zebra','columns','uitheme','filter', 'stickyHeaders', 'editable'],
    tableClass: 'center',
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
          editable_columns       : [3,4,5,6,7,8,9,10],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
          editable_enterToAccept : true,          // press enter to accept content, or click outside if false
          editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
          editable_autoResort    : false,         // auto resort after the content has changed.
          editable_validate      : function(txt, orig, columnIndex, $element){
                                      if(txt==""){validation=true; return txt;}else{
                                       if(/(?:^|\s)([0-2][0-9])(?=\s|$)/.test(txt) || /(?:^|\s)([0-9])(?=\s|$)/.test(txt)){
                                          if(/(?:^|\s)([0-9])(?=\s|$)/.test(txt)){txt="0"+txt;}
                                          txt=txt+":00:00";
                                      }else{
                                          if(/(?:^|\s)([0-2][0-9]\:[0-5][0-9])(?=\s|$)/.test(txt) || /(?:^|\s)([0-9]\:[0-5][0-9])(?=\s|$)/.test(txt)){
                                              if(/(?:^|\s)([0-9]\:[0-5][0-9])(?=\s|$)/.test(txt)){txt="0"+txt;}
                                              txt=txt+":00";
                                          }
                                      }
                                      // only allow one word
                                      var t = /(?:^|\s)([0-2][0-9]\:[0-5][0-9]\:[0-5][0-9])(?=\s|$)/.test(txt);
                                      validation=t;
                                      if(t==false){

                                          new noty({
                                              text: "Cambio no realizado, "+txt+" no corresponde al formato ##:##:##",
                                              type: "error",
                                              timeout: 10000,
                                              animation: {
                                                  open: {height: 'toggle'}, // jQuery animate function property object
                                                  close: {height: 'toggle'}, // jQuery animate function property object
                                                  easing: 'swing', // easing
                                                  speed: 500 // opening & closing animation speed
                                              }
                                          });
                                           return orig;
                                      }else{
                                          return txt;
                                      }
                                    }},          // return a valid string: function(text, original, columnIndex){ return text; }
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
  }).children('tbody').on('editComplete', 'div', function(event, config){

    var $this = $(this);
      elemento=$(this);
      newContent = $this.text(),
      cellIndex = this.cellIndex, // there shouldn't be any colspans in the tbody
      rowIndex = $this.closest('td').attr('row'),// data-row-index stored in row id
      fecha = $this.closest('tr').find('.Fecha').text();
      col = $(this).closest('td').attr('col');
      if(validation==true){
        sendRequest(rowIndex,col,newContent, fecha);
        console.log("ID: "+rowIndex+" | COL: "+col+" | NEW: "+newContent+" | Fecha: "+fecha);
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
<div id='result-table' stle='width:80%; margin: auto;'>
<table class='tablesorter'>
  <thead>
    <tr>
      <?php
        foreach($headers as $index => $info){
          echo "<th>".$info['text']."</th>";
        }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach($row as $index => $cells){
        echo "<tr>";
          foreach($cells['cells'] as $cell => $info){
            echo "<td row='".$info['row']."' col='".$info['col']."' class='".$info['col']."'>".$info['text']."</td>";
          }
        echo "</tr>";
      }
    ?>
  </tbody>
</table>
</div>
<?php $connectdb->close(); ?>

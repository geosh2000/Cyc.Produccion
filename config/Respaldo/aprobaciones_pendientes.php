<?php
include_once('../modules/modules.php');

initSettings::start(true,'vacant_approve');
initSettings::printTitle('Aprobación de Vacantes');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$query="SELECT a.id, b.Departamento, c.Puesto, esquema as Esquema, f.Pais, e.Estado, d.Ciudad, inicio as Inicio, fin as Fin, comentarios as Notas, NombreAsesor(created_by,1) as Solicitada_por, date_created as Fecha_Solicitud
        FROM
        		asesores_plazas a
        	LEFT JOIN
        		PCRCs b ON a.departamento=b.id
        	LEFT JOIN
        		PCRCs_puestos c ON a.puesto=c.id
        	LEFT JOIN
        		db_municipios d ON a.ciudad=d.id
        	LEFT JOIN
        		db_estados e ON d.estado=e.id
        	LEFT JOIN
        		db_pais f ON e.pais=f.id
        WHERE
        	`Status`=0";

if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array()){
    for($i=0;$i<$result->field_count;$i++){
      $data[$fila[0]][]=array("text"=> utf8_encode($fila[$i]), "class"=>'tdcenter', "row" => $fila[0], "col" => $fields[$i]->orgname);
    }

    $data[$fila[0]][]=array("html"=> utf8_encode("<button class='button button_green_w action' action=1>Aprobar</button> <button class='button button_red_w action' action=0>Declinar</button>"), "class"=> "buttapp", "row" => $fila[0], "col" => $fields[$i]->orgname);
  }
}else{
  echo "ERROR! -> ".$connectdb->error." ON $query";
}

for($i=0;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$fields[$i]->name));
}
$dataheaders[]=ucwords("Accion");

unset($result);

$connectdb->close();

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($data as $id =>$info){
  $row[]=array("cells" => $info);
}
//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

?>
<style>
  .tdcenter{
    text-align: center;
  }
  .buttapp{
    width: 250px;
    text-align: center;
  }

</style>
<?php if(!isset($data)){ echo "<br><div style='text-align: center; font-size: 35px;'>No existen vacantes pendientes para aprobación</div>"; exit;} ?>
<script>
$(function(){

  data=<?php print json_encode($table); ?>;

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
            output_saveFileName  : 'movimientos_vacantes.csv',
            // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
            output_encoding      : 'data:application/octet-stream;charset=utf8,',


    }
  });

  $(document).on('click','.action',function(){
    var action=$(this).attr('action');
    var id=$(this).closest('td').attr('row');

    elemento=$(this);

    approve(id,action);
  });

  function approve(id, action){
    showLoader('Aplicando Acción', { my: "center", at: "center", of: elemento });

    $.ajax({
        url: 'approve.php',
        type: 'POST',
        data: {id: id, action: action},
        dataType: 'json',
        success: function(array){
            data=array;

            if(data['status']==1){
              showNoty('success', 'Cambio Aplicado Correctamente',4000);

              switch(action){
                case '3':
                  var classAp='black';
                  var titAp='Declinada';
                  elemento.closest('td').html("<button class='button button_"+classAp+"_w' disabled>"+titAp+"</button> <button class='button button_orange_w action' action=0>Deshacer</button>");
                  break;
                case '1':
                  var classAp='blue';
                  var titAp='Aprobada';
                  elemento.closest('td').html("<button class='button button_"+classAp+"_w' disabled>"+titAp+"</button> <button class='button button_orange_w action' action=0>Deshacer</button>");
                  break;
                case '0':
                  elemento.closest('td').html("<button class='button button_green_w action' action=1>Aprobar</button> <button class='button button_red_w action' action=3>Declinar</button>");
                  break;
              }


            }else{
              showNoty('error', data['msg'],4000);
            }

            dialogLoad.dialog('close');
        },
        error: function(){
          showNoty('error', 'Error en conexión', 4000);
          dialogLoad.dialog('close');
        }
    });
  }


});
</script>
<br>
<div id='result-table' style="width:80%; margin: auto;"></div>

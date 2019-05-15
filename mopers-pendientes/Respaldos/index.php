<?php

include_once("../modules/modules.php");

initSettings::start(true,'schedules_change');
initSettings::printTitle('Mopers Pendientes');

timeAndRegion::setRegion('Cun');

$query="SELECT ausent_id as id, NombreAsesor(asesor,2) as Asesor, Ausentismo, Inicio, Fin, Descansos, Beneficios, Caso, Moper, Comments, `Last Update`
	FROM Ausentismos a, `Tipos Ausentismos`b, Asesores c, userDB d
	WHERE
		a.tipo_ausentismo=b.id AND
		a.asesor=c.id AND
		a.User=d.userid AND
		Activo=1 AND
		needs_moper=1 AND
		(moper IS NULL OR moper='NULL') AND
        Inicio>='2017-01-01'
	ORDER BY
		Inicio";
if($result=Queries::query($query)){
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_array(MYSQLI_BOTH)){
		for($i=0;$i<$result->field_count;$i++){
			$data[$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
		}
		$data[$fila['id']]['edit']="<button row='".$fila['id']."' class='button button_green_w'>Editar</button>";

	}

	for($i=0;$i<$result->field_count;$i++){
		$headers[]=$fields[$i]->name;
	}
}
?>

<script>


$(function(){

	//elemento=$('#mopers');

	$('#mopers').tablesorter({
					theme: 'blue',
					headerTemplate: '{content}',
					stickyHeaders: "tablesorter-stickyHeader",
					widthFixed: false,
					widgets: [ 'zebra','filter','output', 'stickyHeaders'],
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
							stickyHeaders: "tablesorter-stickyHeader"
					}
			});



    function addUser() {

			var position = {my: 'center', at: 'top', of: elemento};
			showLoader('Guardando...', position);

			$.ajax({
				url: '../json/add_moper.php',
				type: 'POST',
				data: {id: $('#a_id').val(), moper: $("#mopernew").val()},
				dataType: 'json',
				success: function(array){
						data=array;

						if(data['status']==1){
							dialogLoad.dialog('close');
							dialog.dialog( "close" );
							showNoty('success', 'Moper '+$("#mopernew").val()+' guardado en id: '+$('#a_id').val(),3000);
							elemento.closest('tr').find('.moper').text($("#mopernew").val());
						}else{
							dialogLoad.dialog('close');
							showNoty('error', data['msg'],4000);
						}
					},
				error: function(){
						dialogLoad.dialog('close');
						showNoty('error', 'Error en conexión', 3000);
					}
			});
		}


    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 550,
      modal: true,
      buttons: {
        "Cargar Moper": function(){
						addUser()
					},
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        resetForm();
      }
    });

		function resetForm(){
			$('#a_id, #mopernew').val('');
		}

    $('.button_green_w').button().on( "click", function() {
			var id=$(this).attr('row');
			elemento=$(this);

			resetForm();

			$('#a_id').val(id);


      dialog.dialog( "option", "position", { my: "left bottom", at: "left top", of: elemento } );
      dialog.dialog( "open" );
    });

});
</script>
<br>
<table style='width:80%; margin: auto' class='t2'>
    <tr class='title'>
        <th colspan=100>Moper Pendientes</th>
    </tr>
</table>
<table style='width:80%; margin: auto; text-align: center' id='mopers'>
    <thead>
    <tr class='title'>
    <?php
    foreach($headers as $key => $title){
         echo "<th>".ucwords(str_replace("_"," ",$title))."</th>\n";
    }
    unset($key);

    ?>
        <th>Editar</th>

    </tr>
    </thead>
    <tbody>
    <?php
        foreach($data as $key => $info){
					echo "<tr>";
						foreach($info as $col => $info2){
							echo "<td class='col'>$info2</td>";
						}
					echo "</tr>";
        }
    ?>
    </tbody>
</table>
<div id="dialog-form" title="Cambiar Moper">
 <p class="validateTips">Fill the required Fields.</p>

  <form>
    <fieldset>
        <table width='480px'>
            <tr>
                <td width='30%'><label for="a_id">ID</label></td>
                <td><input type="text" name="a_id" id="a_id" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
            <tr>
                <td width='30%'><label for="date">Moper Nuevo</label></td>
                <td><input type="text" name="mopernew" id="mopernew" value="" class="text ui-widget-content ui-corner-all"></td>

            </tr>
            </table>
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>

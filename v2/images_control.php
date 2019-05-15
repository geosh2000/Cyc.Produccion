<?php

include_once("../modules/modules.php");

initSettings::start(true,'config_pantallas');
initSettings::printTitle('Pantallas Upload');

$tbody="<td><input type='text' placeholder='Categoria' id='cat'><input type='hidden' id='cat_id'></td><td><input type='text' placeholder='Proveedor' id='prov'><input type='hidden' id='prov_id'></td>";

Filters::showFilterNOFORM('search','Buscar',$tbody);

if(isset($_POST['submit'])){
  $cat=utf8_decode(strtoupper($_POST['categoria']));
  $prov=utf8_decode(strtoupper($_POST['proveedor']));
  $desc=utf8_decode($_POST['descripcion']);

  $normalizeChars = array(
  	    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
  	    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
  	    'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
  	    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
  	    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
  	    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
  	    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
  	    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
  	);

  $connectdb=Connection::mysqliDB('CC');

  $query="INSERT INTO pantallas_display (Categoria, proveedor, descripcion) VALUES ('$cat','$prov','$desc')";
  if($result=$connectdb->query($query)){
    $newID=$connectdb->insert_id;

    //Upload CSV File
    	$target_dir = "images/";
    	$target_file = $target_dir . $newID;
    	$uploadOK = 1;
    	$FileType = ".".substr($_FILES["fileToUpload"]['name'],strpos($_FILES["fileToUpload"]['name'],'.')+1,100);
    	$filename = $target_file . $FileType;

      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename)) {
          $status='success';
          $msg="Imagen guardada correctamente con id: $newID";

          $query="UPDATE pantallas_display SET path='$filename' WHERE id=$newID";
          $connectdb->query($query);
      } else {
          $status='error';
          $msg="Error al guardar imagen";

          $query="DELETE FROM pantallas_display WHERE id=$newID";
          $connectdb->query($query);
      }
  }else{
    echo "ERROR! -> ".$connectdb->error." ON<br>$query";
  }
  ?>
  <script>
  $(function(){
    showNoty(<?php echo "'$status','$msg',5000"; ?>);
  });
  </script>
  <?php
}
  ?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.timepicker.min.css">
<script src="/js/periodpicker/build/jquery.timepicker.min.js"></script>
<script>
$(function(){

  <?php
    if(isset($_POST['submit'])){
      echo "$('#cat').val('$cat'); $('#prov').val('$prov'); printTable();";
    }
  ?>

  $.widget( "custom.catcomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
      },
      _renderMenu: function( ul, items ) {
        var that = this,
          currentCategory = "";
        $.each( items, function( index, item ) {
          var li;
          if ( item.category != currentCategory ) {
            ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
            currentCategory = item.category;
          }
          li = that._renderItemData( ul, item );
          if ( item.category ) {
            li.attr( "aria-label", item.category + " : " + item.label );
          }
        });
      }
    });

    $( "#cat" ).catcomplete({
      delay: 0,
      minLenght: 3,
      source: 'search_cat.php'
    });

    $( "#prov" ).catcomplete({
      delay: 0,
      minLenght: 3,
      source: 'search_prov.php'
    });

    $('#showInfo').hide();

  	$('#search').click(function(){
  			printTable();
    });

  	function printTable(){
      $('#result-table').empty();

      var position = {my: 'center', at: 'center', of: $('#addform')};

      showLoader('Obteniendo Información', position);

      $.ajax({
        url: 'getList.php',
        type: 'POST',
        data: {cat: $('#cat').val(), prov: $('#prov').val()},
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

    $(document).on('click','.datechange',function(){
      elemento=$(this).find('p');
      $('#d_ap').val(elemento.val()).periodpicker('change');
      dialogDate.dialog('option', 'position',{my: 'center', at: 'center', of: elemento}).dialog('open');
    });

    $(document).on('click','.delete',function(){
      elemento=$(this);
      $('#baja_cat').text(elemento.attr('row'));
      dialogDelete.dialog('option', 'position',{my: 'center', at: 'center', of: elemento}).dialog('open');
    });

    $(document).on('click','.txtchange',function(){
      elemento=$(this).find('p');
      $('#d_tx').val(elemento.text());
      //$('#d_tx').val("col: "+elemento.attr('col')+" || row: "+elemento.attr('row'));
      dialogText.dialog('option', 'position',{my: 'center', at: 'center', of: elemento}).dialog('open');
    });

    $(document).on('change','.chk',function(){
      elemento=$(this);
      if($(this).prop('checked')){
        var valor=1;
      }else{
        var valor=0;
      }
      sendRequest(elemento.attr('row'),elemento.attr('col'),valor);
    });

    $('#d_ap').periodpicker({
			norange: true,
			clearButtonInButton: true,
			todayButton: true,
      formatDateTime: 'YYYY-MM-DD HH:mm:ss',

      timepicker: true, // use timepicker
    	timepickerOptions: {
    		hours: true,
    		minutes: true,
    		seconds: false,
    		ampm: true
    	},

      formatDecoreDateTimeWithYear: 'YYYY-MM-DD HH:mm:ss'
		});

    dialogDate = $('#dialog-date').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 300,
      buttons: {
        "Asignar": function(){
						var newContent = $('#d_ap').val(),
								rowIndex = elemento.attr('row'),// data-row-index stored in row id
								col = elemento.attr('col');
								if(newContent==""){
									showNoty('error','El campo de fecha es obligatorio',4000);
								}else{
									sendRequest(rowIndex,col,newContent);
									elemento.text($('#d_ap').val());
									dialogDate.dialog('close');
								}

					},
        Cancel: function(){
          dialogDate.dialog('close');
        }
      },
      close: function(){
          $('#d_ap').periodpicker('clear');
        }
    });

    dialogText = $('#dialog-text').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 300,
      buttons: {
        "Asignar": function(){
            var newContent = $('#d_tx').val(),
                rowIndex = elemento.attr('row'),// data-row-index stored in row id
                col = elemento.attr('col');
                if(newContent==""){
                  showNoty('error','El campo de fecha es obligatorio',4000);
                }else{
                  sendRequest(rowIndex,col,newContent);
                  elemento.text($('#d_tx').val());
                  dialogText.dialog('close');
                }

          },
        Cancel: function(){
          dialogText.dialog('close');
        }
      },
      close: function(){
          $('#d_tx').val('');
        }
    });

    function sendRequest(id,field,newVal){
      var position = {my: 'center', at: 'center', of: elemento};

      showLoader('Guardando Cambios', position);

			$.ajax({
				url: "pantallas_update.php",
				type: 'POST',
				data: {id: id, field: field, newVal: newVal},
				dataType: 'json',
				success: function(array){
						data=array;

						dialogLoad.dialog('close');

						if(data['status']==1){
							showNoty('success','Cambios Guardados',3000);
							$('#d'+id).hide('slow', function(){ $('#d'+id).remove(); });

              if(field=='delete'){
                elemento.closest('tr').remove();
              }

						}else{
							showNoty('error',data['msg'],4000);
						}

					},
				error: function(){
						dialogLoad.dialog('close');
						showNoty('error', 'Error de Conexión',4000);
					}
			});


  }

  dialogDelete=$('#dialog-delete').dialog({
    resizable: false,
    height: "auto",
    width: 400,
    modal: true,
    autoOpen: false,
    buttons: {
      "Borrar": function() {
        sendRequest(elemento.attr('row'),'delete',1);

        $( this ).dialog( "close" );

      },
      Cancel: function() {
        $( this ).dialog( "close" );
      }
    }
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

</style>

<br>
<table class='t2' id='addform' style='width:80%; margin:auto'>
	<tr class='title'>
		<th>Categoria</th><th>Proveedor</th><th>Descripción</th><th>Archivo</th>
	</tr>
	<tr class='pair'>
    <form action="" method="post" enctype="multipart/form-data">
		<td><input type='text' name='categoria' required></td>
    <td><input type='text' name='proveedor' required></td>
    <td><input type='text' name='descripcion' required></td>
    <td>Select file to upload: <input type="file" name="fileToUpload" id="fileToUpload" required></td>
	</tr>
	<tr class='total'>
		<td colspan=100><input type='submit' name='submit' value='Subir'></td>
	</tr>

</form></table>
<br>
<div id='showInfo'>
  <div id='result-table' style='width:95%; margin: auto'></div>
</div>

<div id="dialog-date" title="Cambiar Fecha" style='text-align:center;'>
  <form>
    <fieldset>
      <label for="d_ap">Nueva Fecha</label>
      <input type="text" name="d_ap" id="d_ap" value=''>
    </fieldset>
  </form>
</div>

<div id="dialog-text" title="Cambiar Datos" style='text-align:center;'>
  <form>
    <fieldset>
      <label for="d_tx">Nueva Información</label>
      <input type="text" name="d_tx" id="d_tx" value=''>
    </fieldset>
  </form>
</div>
<div id="dialog-delete" title="Eliminar Imagen">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Estás seguro que deseas borrar: <span id='baja_cat'></span>? </p>
</div>
</body>
</html>

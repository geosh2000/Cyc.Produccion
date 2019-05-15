<?php

include_once("../modules/modules.php");

initSettings::start(true,'config');
initSettings::printTitle('Configuración PDVs');

timeAndRegion::setRegion('Cun');

//Get Existing Users
$query="SELECT a.*, d.plazas as plazas, vacantes_disponibles, b.Ciudad, c.Estado
					FROM PDVs a
					LEFT JOIN db_municipios b ON a.ciudad=b.id
					LEFT JOIN db_estados c ON b.estado=c.id
					LEFT JOIN
						(SELECT oficina, COUNT(*) as plazas, COUNT(*)-COUNT(getVacante(id,CURDATE())) as vacantes_disponibles FROM asesores_plazas GROUP BY oficina) d ON a.id=d.oficina
					ORDER BY c.Estado, b.Ciudad, PDV";

if($result=Queries::query($query)){
	$fields=$result->fetch_fields();
	$columns=$result->field_count;
	while($fila=$result->fetch_array(MYSQLI_BOTH)){
		for($i=0;$i<$columns;$i++){
			$data[$fila['Estado']]['detalle'][$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
			$data_simple[$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
			$tableTitle[$i]=$fields[$i]->name;
		}

		if($fila['Activo']==1){
			@$data[$fila['Estado']]['Activos']++;
		}else{
			@$data[$fila['Estado']]['Inactivos']++;
		}
	}
}else{
	echo "<p>ERROR!!</p>";
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
.ui-autocomplete {
    max-height: 200px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
		z-index: 1000;
  }
.city_change, .profile_change{
	cursor: hand;
}

</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-stickyHeaders.js"></script>
<script type="text/javascript" src="/js/periodpicker/jquery.timepicker.js"></script>
<script>


var status;
function sendRequest(id,field,newVal){
				showLoader('Guardando Cambios',{my: 'center', at: 'center', of: elemento});

				$.ajax({
					url: "pdv_update.php",
					type: 'POST',
					data: {id: id, field: field, newVal: newVal},
					dataType: 'json',
					success: function(array){
							data=array;

							dialogLoad.dialog('close');

							if(data['status']==1){
								showNoty('success','Cambios Guardados',3000);
								$('#d'+id).hide('slow', function(){ $('#d'+id).remove(); });

								switch (field) {
									case 'profile':
										$('#profile_name_'+id).text(profileSelected);
										$('#profile_'+id).text(newVal);
										profileSelected='';
										break;
									case 'ciudad':
										$('#ciudad_'+id).text(newVal);
										$('#Ciudad_'+id).text(citySelected);
										citySelected='';
										break;
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

  $(function() {

    $("#dialogerror").dialog({
	      modal: true,
				autoOpen: false,
				width: 1000,
				position: {my: 'center top', at: 'center top'},
	      buttons: {
	        Ok: function() {
	          $( this ).dialog( "close" );
	        }
	      }
	    });

		$(".t2").tablesorter();

    $(".resetpswd").click(function(){
        var id=$(this).attr('asesor');
        sendRequest(id,'pswd','pricetravel2016');
    });

    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });

    $('.tablesorter').tablesorter({
        theme: 'blue',
        headerTemplate: '{content}',
        widthFixed: false,
        widgets: [ 'zebra','filter', 'output', 'editable', 'stickyHeaders' ],
        widgetOptions: {

          stickyHeaders_attachTo: '.tabcontain',

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
            output_saveFileName  : 'reporte_pdvs.csv',
            // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
            output_encoding      : 'data:application/octet-stream;charset=utf8,',

            editable_columns       : [2,3,4,6,7,8,9,10,11,12,13],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
            editable_autoResort    : false,         // auto resort after the content has changed.
            editable_validate      : function(txt, orig, columnIndex, $element){
                                        if(txt==""){validation=true; return txt;}else{
                                            validation=true;

                                            return txt;
                                        }
                                      },          // return a valid string: function(text, original, columnIndex){ return text; }
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
			elemento=$(this);
      var $this = $(this),
        newContent = $this.text(),
        cellIndex = this.cellIndex, // there shouldn't be any colspans in the tbody
        rowIndex = $this.closest('td').attr('a_id'),// data-row-index stored in row id
        col = $(this).attr('col');
        if(validation==true){
            sendRequest(rowIndex,col,newContent);
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

		$('#export').click(function(){
	    $('.tablesorter').trigger('outputTable');
	  });

		//ADD USER
		dialogCreate = $('#accordion-Create').accordion({
			heightStyle: "content",
			collapsible: true,
			active: false
		});

		$('#newAsesor').click(function(){
      $('#addForm').fadeIn(100,function(){
        dialogCreate.accordion('option','active',0);
        $('#newAsesor').fadeOut(100);
      });
    });

		function validateAdd(){
			flag=true;

			$('#fieldset_add input').each(function(){
				if($(this).prop('required')){
					if($(this).val()==''){
						flag=false;
						$(this).addClass('ui-state-error');
						showNoty('error','Campo '+$(this).attr('title')+' obligatorio',2000);
					}else{
						$(this).removeClass('ui-state-error');
					}
				}
			});

			$('#fieldset_add select').each(function(){
				if($(this).prop('required')){
					if($(this).val()==''){
						flag=false;
						$(this).prev('label').addClass('ui-state-error');
						showNoty('error','Campo '+$(this).attr('title')+' obligatorio',2000);
					}else{
						$(this).prev('label').removeClass('ui-state-error');
					}
				}


			});

			return flag;

		}

		function notSave(){
      dialogCreate.accordion('option','active',false);

      $('#addForm').fadeOut(100,function(){
        $('#newAsesor').fadeIn(100);
        $('#fieldset_add input').val('').removeClass('ui-state-error');
				$('#fieldset_add select').val('').removeClass('ui-state-error');
				$('#new_ingreso').periodpicker('clear');
        $('#new_fap').periodpicker('clear');
        $('#new_fc').periodpicker('clear');
				$('#new_activo').prop('checked',false).checkboxradio('refresh');
      });
    }

		$('.activebox').each(function(){
			$(this).checkboxradio({
				icon: false
			});
		});

		$('.activebox').change(function(){
			elemento=$(this);
			var id=$(this).closest('td').attr('a_id');
			var col='Activo';
			if($(this).prop('checked')){
				var nval=1;
				$(this).checkboxradio({
					label: 'Activo'
				});
			}else{
				var nval=0;
				$(this).checkboxradio({
					label: 'Inactivo'
				});
			}
			sendRequest(id,col,nval);

		});

    $('#notSaveAdd').click(function(){
      notSave();
    });

    $('#addForm').hide();

		$('#new_dep').selectmenu().selectmenu( "menuWidget" )
        .addClass( "overflow" );

		$('#new_puesto').selectmenu().selectmenu( "menuWidget" )
        .addClass( "overflow" );

		$('#new_profile').selectmenu().selectmenu( "menuWidget" )
        .addClass( "overflow" );


		$('#new_activo').checkboxradio();

		$('#new_ingreso, #d_ap, #new_fap, #new_fc').periodpicker({
			norange: true,
			clearButtonInButton: true,
			todayButton: true
		});

		$('#saveAdd').click(function(){
			if(validateAdd()){
				addUser();
			}
		});

		$('.picker').click(function(){
			elemento=$(this);
			$('#d_ap').val($(this).text());
			$('#d_ap').periodpicker('change');
			dialogDate.dialog('option', 'position',{my: 'center', at: 'center', of: elemento}).dialog('open');
		});

		$('.profile_change').click(function(){
			elemento=$(this);
			dialogProfile.dialog('option', 'position',{my: 'center', at: 'center', of: elemento}).dialog('open');
		});

		dialogDate = $('#dialog-date').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 300,
      buttons: {
        "Asignar": function(){
						var newContent = $('#d_ap').val(),
								rowIndex = elemento.closest('td').attr('a_id'),// data-row-index stored in row id
								col = elemento.closest('td').attr('col');
								sendRequest(rowIndex,col,newContent);
								elemento.text($('#d_ap').val());
								dialogDate.dialog('close');


					},
        Cancel: function(){
          dialogDate.dialog('close');
        }
      },
      close: function(){
          $('#d_ap').periodpicker('clear');
        }
    });

		$('.city_change').click(function(){
			elemento=$(this);
			dialogCity.dialog('option', 'position',{my: 'center', at: 'center', of: elemento}).dialog('open');
		});

		dialogCity = $('#dialog-ciudad').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 300,
      buttons: {
        "Asignar": function(){
					citySelected=$('#d_city').val();
					var newContent = $('#d_city_id').val(),
							rowIndex = elemento.attr('a_id'),// data-row-index stored in row id
							col = elemento.attr('col');
							if($('#d_city').val()!=""){
								sendRequest(rowIndex,'ciudad',newContent);
								dialogCity.dialog('close');
							}else{
								showNoty('error','Debes seleccionar una Ciudad');
							}
					},
        Cancel: function(){
          dialogCity.dialog('close');
        }
      },
      close: function(){
          $('#d_city').val('');
        }
    });

		dialogProfile = $('#dialog-profile').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 300,
			buttons: {
        "Asignar": function(){
					 	profileSelected=$('#d_profile option:selected').text();
						var newContent = $('#d_profile').val(),
								rowIndex = elemento.attr('a_id'),// data-row-index stored in row id
								col = elemento.attr('col');
								if($('#d_profile').val()!=""){
									sendRequest(rowIndex,'profile',newContent);
									dialogProfile.dialog('close');
								}else{
									showNoty('error','Debes seleccionar un Perfil');
								}
					},
        Cancel: function(){
          dialogProfile.dialog('close');
        }
      },
      close: function(){
          $('#d_profile').val('');
        }
    });

		nuevosAsesores=0;

		function addUser(){
			showLoader('Guardando Nuevo PDV', {my: 'center', at: 'center'});

			$.ajax({
				url: "newPDV.php",
				type: 'POST',
				data: {ciudad: $('#new_ciudad').val(), activo: $('#new_activo').prop('checked'), pdv: $('#new_pdv').val(), pdvRH: $('#new_pdv_rrhh').val(), branch: $('#new_branch').val(), dir: $('#new_dir').val(), corp: $('#new_corp').val(), nse: $('#new_nse').val(), tipo: $('#new_tipo').val(), tel: $('#new_tel').val(), ext: $('#new_ext').val(), hap: $('#new_hap').val(), hc: $('#new_hc').val(), open: $('#new_open').val(), fap: $('#new_fap').val(), fc: $('#new_fc').val()},
				dataType: 'json',
				success: function(array){
						data=array;

						dialogLoad.dialog('close');

						if(data['status']==1){
							showNoty('success', 'PDV Guardado correctamente', 3000);
              nuevosAsesores++;
							$('#num_new').text(nuevosAsesores);
							$('#new_notif').fadeIn(500);
							notSave();
						}else{
							showNoty('error', data['msg'], 5000);
						}
					},
				error: function(){
						dialogLoad.dialog('close');
						showNoty('error', 'Error de Conexión',3000);
					}
			})
		}

		$('#new_notif').hide();

		$('#reload').click(function(){
			window.location.reload();
		});

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

    $( "#new_place" ).catcomplete({
	    delay: 0,
	    minLenght: 3,
	    source: 'search_ciudad.php',
	    select: function(ev, ui){
	      $('#new_ciudad').val(ui.item.id);
	    }
	  });


  $( "#new_sup" ).catcomplete({
    delay: 0,
    minLenght: 3,
    source: 'search_name.php',
    select: function(ev, ui){
      $('#new_sup_id').val(ui.item.id);
    }
  });

		$( "#d_city" ).catcomplete({
	    delay: 0,
	    minLenght: 3,
	    source: 'search_ciudad.php',
	    select: function(ev, ui){
	      $('#d_city_id').val(ui.item.id);
	    }
	  });
});
</script>
<style>
#result-table td, #result-table th{
  text-align: center;
}

.picker, .city_change{
  cursor: hand;
}

  label, input { display:block; }
  input.text { margin-bottom:12px; width:95%; padding: .4em; }
  fieldset { padding:0; border:0; margin-top:25px; }
  h1 { font-size: 1.2em; margin: .6em 0; }
  div#users-contain { width: 350px; margin: 20px 0; }
  div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
  div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
  .ui-dialog .ui-state-error { padding: .3em; }
  .validateTips { border: 1px solid transparent; padding: 0.3em; }
  .d_ap_sel{ cursor: hand; }
	.overflow {
      height: 200px;
    }
		.ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }
</style>
<div id='new_notif' style='background: red; color: white; text-align: center; margin: 0;'><p>Existen <num id='num_new' style='font-weight: bold'></num> asesor(es) nuevos!. Da click <span id='reload' style='cursor: hand; color: yellow'>aquí</span> para volver a cargar</p></div>
<button class='button button_green_w' id='newAsesor'>Agregar</button><button class='button button_blue_w' id='export'>Exportar</button>
<div style='width: 100%; margin: auto;'>
<div id='addForm' style='display: block; width: 782px; margin: auto'>
  <div id="accordion-Create">
    <h3>Agregar PDV</h3>
    <div>
      <fieldset id='fieldset_add'>
				<div style='width: 235px; display: inline-block; vertical-align:top'>
					<label for="new_pdv">PDV Name</label>
	        <input title='PDV Name' type='text' id='new_pdv' name="new_pdv" required><br>
					<label for="new_pdv_rrhh">Clave RRHH <span style='font-size= 10px; font-style: oblique;'>(opcional)</span></label>
	        <input title='Clave RRHH' type='text' id='new_pdv_rrhh' name="new_pdv_rrhh"><br>
					<label for="new_branch">Branch <span style='font-size= 10px; font-style: oblique;'>(opcional)</span></label>
	        <input title='Branch' type='text' id='new_branch' name="new_branch"><br>
          <label for="new_dir">Dirección</label>
	        <input title='Dirección' type='text' id='new_dir' name="new_dir" required><br>
          <label for="new_place">Ciudad</label>
	        <input title='Ciudad' type='text' id='new_place' name="new_place" required><input type='hidden' id='new_ciudad' name="new_ciudad"><br><br>

				</div>
        <div style='width: 235px; display: inline-block; vertical-align:top'>
          <label for="new_corp">Corporativo</label>
	        <input title='Corporativo' type='text' id='new_corp' name="new_corp" required><br>
          <label for="new_nse">NSE</label>
	        <input title='NSE' type='text' id='new_nse' name="new_nse" required><br>
          <label for="new_tipo">Tipo</label>
	        <input title='Tipo' type='text' id='new_tipo' name="new_tipo" placeholder="PDV4 / PDV2..."required><br>
          <label for="new_tel">Tel Fijo <span style='font-size= 10px; font-style: oblique;'>(opcional)</span></label>
	        <input title='Tel Fijo' type='text' id='new_tel' name="new_tel" ><br>
          <label for="new_ext">Extensión <span style='font-size= 10px; font-style: oblique;'>(opcional)</span></label>
	        <input title='Extensión' type='text' id='new_ext' name="new_ext" ><br>
				</div>
        <div style='width: 235px; display: inline-block;'>
          <label for="new_hap">Hora Apertura</label>
	        <input title='Hora Apertura' type='text' id='new_hap' name="new_hap" placeholder='hh:mm' required><br>
          <label for="new_hc">Hora Cierre</label>
	        <input title='Hora Cierre' type='text' id='new_hc' name="new_hc" placeholder='hh:mm' required><br>
          <label for="new_open">Dias Abierto</label>
	        <input title='Dias Abierto' type='text' id='new_open' name="new_open" required><br>
          <label for="new_fap">Fecha Apertura <span style='font-size= 10px; font-style: oblique;'>(opcional)</span></label>
	        <input title='Tel Fijo' type='text' id='new_fap' name="new_fap" ><br><br>
          <label for="new_fc">Fecha Cierre <span style='font-size= 10px; font-style: oblique;'>(opcional)</span></label>
	        <input title='Fecha Cierre' type='text' id='new_fc' name="new_fc" ><br>
				</div>
				<br><br>
        <div style='width: 300px; margin: auto; text-align:center;'>
          <label for="new_activo">Activo</label><input type="checkbox" name="new_activo" id="new_activo" value=''>
        </div>
				<div style='width: 300px; margin: auto; text-align:center;'><button class='button button_green_w' id='saveAdd'>Guardar</button> <button class='button button_red_w' id='notSaveAdd'>Cancelar</button></div>
      </fieldset>
    </div>
  </div>
</div>
</div>
<br>

    <?php
		echo "<table width='100%'  class='tablesorter' style='text-align:center;'>\n";
		echo "<thead><tr class='title'>\n";

		foreach($tableTitle as $index => $title){
			echo "<th>$title</th>";
		}


		echo "</tr></thead><tbody>\n";
				foreach($data_simple as $id => $info){
						//-->

						//<--Print Body
						echo "<tr>";
						foreach($info as $col => $info3){

									switch($col){
										case 'apertura':
										case 'cierre':
                      $contenido=$info3;
                      $class="class='picker'";
											break;
										case 'Ciudad':
											$contenido=$info3;
											$class="class='city_change'";
											break;

										case 'Activo':
											if($info3==1){
												$activo=' checked';
												$checkTitle="Activo";
											}else{
												$activo=' ';
												$checkTitle="Inactivo";
											}
                      $class="";
											$contenido="<label for='check_$id' style='width:95px;'>$checkTitle</label><input name='check_$id' id='check_$id' type='checkbox' class='activebox' $activo>";
											break;
										default:
											$contenido=$info3;
											$class="";
											break;
									}

									echo "<td a_id='$id' col='$col' id='".$col."_$id' $class>$contenido</td>";



						}
						echo "</tr>";

				}
				echo "</tbody></table>";
				//-->

				//-->

				echo "</div>";


    ?>




<div id="dialog-date" title="Cambiar Fecha" style='text-align:center;'>
  <form>
    <fieldset>
      <label for="d_ap">Nueva Fecha</label>
      <input type="text" name="d_ap" id="d_ap" value=''>
    </fieldset>
  </form>
</div>

<div id="dialog-ciudad" title="Cambiar Ciudad" style='text-align:center;'>
  <form>
    <fieldset>
      <label for="d_ap">Nueva Ciudad</label>
      <input type="text" name="d_city" id="d_city" placeholder="Ciudad..." value=''><input type="hidden" name="d_city_id" id="d_city_id" value=''>
    </fieldset>
  </form>
</div>

<div id="dialog-profile" title="Cambiar Perfil" style='text-align:center;'>
  <form>
    <fieldset>
      <label for="d_profile">Nuevo Perfil</label>
			<select id="d_profile" name="d_profile" title="Profile" required><option value=''>Selecciona...</option>
				<?php
					$query="SELECT id, profile_name FROM profilesDB ORDER BY profile_name";
					if($result=Queries::query($query)){
						while($fila=$result->fetch_assoc()){
							echo "<option value='".$fila['id']."'>".$fila['profile_name']."</option>";
						}
					}
				?>
			</select>
    </fieldset>
  </form>
</div>

<div id="dialogerror" title="Error al guardar Asesor">
  <p>
    <span class="ui-icon ui-icon-closethick" style="float:left; margin:0 7px 50px 0;"></span>
    Existieron errores al guardar el Asesor en las bases de datos. Los registros exitosos fueron eliminados.
  </p><br>
  <table class='t2' style='margin: auto; width: 80%; text-align:center'>
		<thead>
			<tr>
				<th>Base Asesores</th>
				<th>Base Usuarios</th>
				<th>Base Supervisores</th>
				<th>Base Puesto</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td id='error_asesores'>Base Asesores</td>
				<td id='error_user'>Base Usuarios</td>
				<td id='error_super'>Base Supervisores</td>
				<td id='error_puesto'>Base Puesto</td>
        <td id='error_pdv'>Base PDV</td>
			</tr>
		</tbody>
	</table>
</div>

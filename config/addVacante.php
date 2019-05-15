<?php

include_once("../modules/modules.php");

initSettings::start(true,'config');
initSettings::printTitle('Vacantes');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

//Get Existing Users
$query="SELECT a.id, b.Departamento as departamento, c.Puesto as puesto, d.Ciudad as ciudad, e.PDV as oficina, a.inicio, a.fin, a.comentarios, NombreAsesor(getVacante(a.id,CURDATE()),2) as Asesor_Actual, a.Status, a.Activo, a.esquema, a.departamento as dep_id, a.puesto as puesto_id, a.oficina as oficina_id, a.ciudad as ciudad_id, NombreAsesor(approbed_by,1) as Aprobada_por, date_approbed as Fecha_Aprobacion "
      ."FROM asesores_plazas a LEFT JOIN PCRCs b ON a.departamento=b.id LEFT JOIN PCRCs_puestos c ON a.puesto=c.id LEFT JOIN db_municipios d ON a.ciudad=d.id LEFT JOIN PDVs e ON a.oficina=e.id ORDER BY b.Departamento, a.puesto, a.inicio";

if($result=$connectdb->query($query)){
	$fields=$result->fetch_fields();
	$columns=$result->field_count;
	while($fila=$result->fetch_array(MYSQLI_BOTH)){
		for($i=0;$i<$columns;$i++){
			$data[$fila['departamento']]['detalle'][$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
			$tableTitle[$i]=$fields[$i]->name;
		}



    if($fila['Status']==1 || $fila['Status']==2){
      if($fila['Activo']==1){

        //Activos
        @$data[$fila['departamento']]['Activos']++;

        if($fila['Asesor_Actual'] == NULL){
          @$data[$fila['departamento']]['Vacantes']++;

          //Puesto Vacantes
          @$puestos[$fila['departamento']][utf8_encode($fila['puesto'])]['Vacantes']++;
        }else{
          @$puestos[$fila['departamento']][utf8_encode($fila['puesto'])]['Cubiertas']++;
        }
      }else{
        $data[$fila['departamento']]['Inactivos']++;
      }
    }else{
      if($fila['Status']==0){
        @$data[$fila['departamento']]['Pendientes']++;
      }
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
				showLoader('Guardando Cambios',{ my: "left top", at: "left bottom", of: elemento });

				$.ajax({
					url: "vacante_update.php",
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
                  case 'Activo':
										switch(newVal){
                      case 1:
                        activeElement.closest('td').prev().html("<button class='button button_orange_w'>Pendiente</button>'");
                        break;
                      case 0:
                        activeElement.closest('td').prev().html("<button class='button button_red_w'>Desactivada</button>'");
                        break;
                    }
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
        elemento=$(this);
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

            editable_columns       : [5,6,7,11],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
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
      var $this = $(this),
        newContent = $this.text(),
        cellIndex = this.cellIndex, // there shouldn't be any colspans in the tbody
        rowIndex = $this.closest('td').attr('a_id'),// data-row-index stored in row id
        col = $(this).attr('col');

        elemento=$(this);

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
				$('#new_inicio').periodpicker('clear');
        $('#new_fin').periodpicker('clear');
        $('#new_dep').selectmenu('refresh');
        $('#new_puesto').selectmenu('refresh');
        $('#new_activo').prop('checked',false).checkboxradio('refresh');
      });
    }

		$('.activebox').each(function(){
			$(this).checkboxradio({
				icon: false
			});
		});

		$('.activebox').change(function(){
			var id=$(this).closest('td').attr('a_id');
			var col='Activo';
      activeElement=$(this);
      elemento=$(this);
			if($(this).prop('checked')){
				confirmActive.dialog('option', 'position', { my: "center", at: "center", of: activeElement }).dialog('open');
			}else{
				confirmInactive.dialog('option', 'position', { my: "center", at: "center", of: activeElement }).dialog('open');
			}


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


		$('#new_vacantes').spinner({
			incremental: false,
			step: 1,
			max: 3,
			min: 1
		});

    $('#new_esquema').spinner({
			incremental: false,
			step: 1,
			max: 10,
			min: 4
		});

    $('#new_cantidad').spinner({
			incremental: false,
			step: 1,
			max: 20,
			min: 1
		});

		$('#new_activo').checkboxradio();

		$('#new_inicio, #new_fin, #d_ap, #new_fap, #new_fc').periodpicker({
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
			showLoader('Guardando Nuevo PDV');

			$.ajax({
				url: "newVacante.php",
				type: 'POST',
				data: {nomail: <?php if($_GET['nomail']==1){ echo 1;}else{ echo 0;} ?>,esquema: $('#new_esquema').val(), ciudad: $('#new_ciudad').val(), activo: $('#new_activo').prop('checked'), pdv: $('#new_pdv_id').val(), departamento: $('#new_dep').val(), puesto: $('#new_puesto').val(), inicio: $('#new_inicio').val(), fin: $('#new_fin').val(), cantidad: $('#new_cantidad').val()},
				dataType: 'json',
				success: function(array){
						data=array;

						dialogLoad.dialog('close');

						if(data['status']==1){
							showNoty('success', 'Vacante Guardada Correctamente', 3000);
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

    $( "#new_pdv" ).catcomplete({
	    delay: 0,
	    minLenght: 3,
	    source: 'search_pdv.php',
	    select: function(ev, ui){
	      $('#new_pdv_id').val(ui.item.id);
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

    confirmInactive=$( "#dialog-confirmInactive" ).dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Desactivar": function() {
          activeElement.checkboxradio({
    					label: 'Inactivo'
    				});

    			sendRequest(activeElement.closest('td').attr('a_id'),'Activo',0);
          $( this ).dialog( "close" );
        },
        Cancel: function() {
          activeElement.prop('checked',true);
  				activeElement.checkboxradio({
  					label: 'Activo'
  				});
          $( this ).dialog( "close" );
        }
      }

    });

    confirmActive=$( "#dialog-confirmActive" ).dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Reactivar": function() {
          activeElement.checkboxradio({
    					label: 'Activo'
    				});

    			sendRequest(activeElement.closest('td').attr('a_id'),'Activo',1);
          $( this ).dialog( "close" );
        },
        Cancel: function() {
          activeElement.prop('checked',false);
  				activeElement.checkboxradio({
  					label: 'Inactivo'
  				});
          $( this ).dialog( "close" );
        }
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

  .acc_title{
    margin-left: 30px;
    margin-top: -15px;
    text-align: left;
    font-size: 13px;
  }

  .acc_title tr th{
    color: #008cba;
    width: 100px;
  }

  .puestos{
    border: solid 1px;
    min-width: 100px;
  }

  .numVac{
    color: #7bb661;
    text-align: center;
  }
</style>
<div id='new_notif' style='background: red; color: white; text-align: center; margin: 0;'><p>Existen <num id='num_new' style='font-weight: bold'></num> vacante(s) nueva(s)!. Da click <span id='reload' style='cursor: hand; color: yellow'>aquí</span> para volver a cargar</p></div>
<button class='button button_green_w' id='newAsesor'>Agregar</button>
<div style='width: 100%; margin: auto;'>
<div id='addForm' style='display: block; width: 277px; margin: auto'>
  <div id="accordion-Create">
    <h3>Agregar Vacante</h3>
    <div>
      <fieldset id='fieldset_add'>
				<div style='display: inline-block;'>
					<label for="new_dep">Departamento</label>
	        <select id="new_dep" name="new_dep" title="Departamento" required><option value=''>Selecciona...</option>
						<?php
							$query="SELECT id, Departamento FROM PCRCs ORDER BY Departamento";
							if($result=$connectdb->query($query)){
								while($fila=$result->fetch_assoc()){
									echo "<option value='".$fila['id']."'>".utf8_encode($fila['Departamento'])."</option>";
								}
							}
						?>
					</select><br><br>
					<label for="new_puesto">Puesto</label>
	        <select id="new_puesto" name="new_puesto" title="Puesto" required><option value=''>Selecciona...</option>
						<?php
							$query="SELECT id, Puesto FROM PCRCs_puestos ORDER BY Puesto";
							if($result=$connectdb->query($query)){
								while($fila=$result->fetch_assoc()){
									echo "<option value='".$fila['id']."'>".utf8_encode($fila['Puesto'])."</option>";
								}
							}
						?>
					</select><br><br>
          <div style='width: 48%; display: inline-block; margin:0; vertical-align: top; margin-top: -19'>
            <br>
						<label for="new_esquema">Esquema</label>
						<input title='Esquema' type='text' id='new_esquema' name="new_esquema" style='width: 60px;' required><br><br>
					</div>
					<div style='width: 48%; display: inline-block; margin:0;  padding-top: 21px; margin-top: -19'>
            <br>
						<!--<label for="new_activo" style='width: 75px;'>Activo</label>
	        	<input type="checkbox" name="new_activo" id="new_activo" value=''> -->
					</div><label for="new_inicio">Inicio</label>
	        <input title='Inicio' type='text' id='new_inicio' name="new_inicio" required><br><br>
          <label for="new_fin">Fin <span style='font-size= 10px; font-style: oblique;'>(opcional)</span></label>
	        <input title='Fin' type='text' id='new_fin' name="new_fin"><br><br>
					<label for="new_place">Ciudad</label>
	        <input title='Ciudad' type='text' id='new_place' name="new_place" required><input type='hidden' id='new_ciudad' name="new_ciudad"><br>
          <label for="new_pdv">Oficina</label>
	        <input title='PDV' type='text' id='new_pdv' name="new_pdv" required><input type='hidden' id='new_pdv_id' name="new_pdv_id"><br>
          <label for="new_cantidad">Número de Vacantes</label>
	        <input title='Cantidad' type='text' id='new_cantidad' name="new_cantidad" style='width: 60px;' required><br><br>
					<br>
				</div>
				<br>
				<div style='margin: auto; text-align:center;'><button class='button button_green_w' id='saveAdd'>Guardar</button> <button class='button button_red_w' id='notSaveAdd'>Cancelar</button></div>
      </fieldset>
    </div>
  </div>
</div>
</div>
<br>
<div id="accordion" style='width:95%; margin: auto;'>
    <?php
				foreach($data as $departamento => $info){
          if(!isset($info['Inactivos'])){
            $inactive=0;
          }else{
            $inactive=$info['Inactivos'];
          }

          if(!isset($info['Activos'])){
            $active=0;
          }else{
            $active=$info['Activos'];
          }

          if(!isset($info['Vacantes'])){
            $vacantes=0;
          }else{
            $vacantes=$info['Vacantes'];
          }

          if(!isset($info['Pendientes'])){
            $pending=0;
          }else{
            $pending=$info['Pendientes'];
          }

          $puestoPrint="";

          foreach ($puestos[$departamento] as $key => $value) {
            if(!isset($value['Cubiertas'])){
              $cubiertas=0;
            }else{
              $cubiertas=$value['Cubiertas'];
            }

            if(!isset($value['Vacantes'])){
              $pvacantes=0;
            }else{
              $pvacantes=$value['Vacantes'];
            }

            $puestoPrint.="<th class='puestos'>$key<br><span class='numVac'>($cubiertas | $pvacantes)</span></th>";
          }

					echo "<h3><table class='acc_title'><tr><th style='width: 170px; text-align: left'>$departamento</th><th>HeadCount:<br><span class='numVac'>$active</span></th>
                    <th>Vacantes:<br><span class='numVac'>$vacantes</span></th>
                    <th>Por Aprobar:<br><span class='numVac'>$pending</span></th>
                    <th>Inactivos:<br><span class='numVac'>$inactive</span></th>
                    $puestoPrint</tr></table></h3>"; //Title Print
					echo "<div>";

					//<--Print table

						//<--Print Titles
						echo "<table width='100%'  class='tablesorter' style='text-align:center;'>\n";
						echo "<thead><tr class='title'>\n";

						foreach($tableTitle as $index => $title){
							echo "<th>$title</th>";
						}


						echo "</tr></thead><tbody>\n";
						//-->

						//<--Print Body
						foreach($info['detalle'] as $id => $info2){
							echo "<tr>";
								foreach($info2 as $column => $info3){
									switch($column){
										case 'inicio':
										case 'fin':
                      $contenido=$info3;
                      $class="class='picker'";
											break;
										case 'Activo':
											if($info3==1){
												$activo=' checked';
												$checkTitle="Activo";
											}else{
												$activo=' ';
												$checkTitle="Inactivo";
											}

                      if($info2['Status']==3){
                        $prop="disabled";
                      }else{
                        $prop="";
                      }


                      $class="";
											$contenido="<label for='check_$id' style='width:95px;'>$checkTitle</label><input name='check_$id' id='check_$id' type='checkbox' class='activebox' $activo $prop>";
											break;
                    case 'Status':
                      switch($info3){
                        case 0:
                          $status=' button_orange_w';
                          $statusTitle="Pendiente";
                          break;
                        case 1:
                          $status=' button_green_w';
                          $statusTitle="Aprobado";
                          break;
                        case 2:
                          $status=' button_red_w';
                          $statusTitle="Desactivada";
                          break;
                        case 3:
                          $status=' button_black_w';
                          $statusTitle="Declinada";
                          break;
                      }

                      $class="";
											$contenido="<button class='button $status'>$statusTitle</button>";
											break;
										default:
											$contenido=$info3;
											$class="";
											break;
									}

									echo "<td a_id='$id' col='$column' id='".$column."_$id' $class>$contenido</td>";
								}


							echo "</tr>";
						}

						echo "</tbody></table>";
						//-->

					//-->

					echo "</div>";
				}


    ?>

</div>

<div id="dialog-confirmInactive" title="Deseas Desactivar la Vacante?">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Reactivar esta vacante requerirá autorización de Dirección General. Deseas continuar?</p>
</div>

<div id="dialog-confirmActive" title="Reactivar Vacante?">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Reactivar esta vacante requiere autorización de Dirección General. Deseas continuar?</p>
</div>

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
					if($result=$connectdb->query($query)){
						while($fila=$result->fetch_assoc()){
							echo "<option value='".$fila['id']."'>".$fila['profile_name']."</option>";
						}
					}
					
					$connectdb->close();
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

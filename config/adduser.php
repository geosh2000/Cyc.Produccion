<?php

include_once("../modules/modules.php");

initSettings::start(true,'config');
initSettings::printTitle('Configuración Usuarios');

timeAndRegion::setRegion('Cun');

//Get Existing Users
$query="SELECT
	x.*, p.PDV, m.Ciudad as Locacion, z.Departamento, y.Puesto, userid, profile, profile_name, q.esquema, if(CURDATE()<=Egreso,1,0) as Active
	FROM
		(
		SELECT
		*, IF(getVacanteAsesor(id,CURDATE()) IS NULL,IF(getLastVacante(id,0) IS NULL,0,getLastVacante(id,0)),getVacanteAsesor(id,CURDATE())) as AsPlaza, IF(getVacanteAsesor(id,CURDATE()) IS NULL,getLastDep(id),getDepartamento(id, CURDATE())) as AsDep, IF(getVacanteAsesor(id,CURDATE()) IS NULL,getLastPuesto(id),getPuesto(id,CURDATE())) as AsPuesto
		FROM
			Asesores
    	HAVING AsPlaza IS NOT NULL
		) as x
	LEFT JOIN
		(SELECT
			id as pcrc_id, Departamento
			FROM
				PCRCs
		) as z
	ON
		x.AsDep=z.pcrc_id
	LEFT JOIN
		PCRCs_puestos y
	ON
		x.AsPuesto=y.id
	LEFT JOIN
		asesores_plazas q ON x.AsPlaza=q.id
	LEFT JOIN
    PDVs p
  ON
    q.oficina=p.id
	LEFT JOIN
			(SELECT
				userid, username, profile, profile_name, asesor_id
				FROM
					userDB a, profilesDB b
				WHERE
					a.profile=b.id
			) as y
	ON
		x.id=y.asesor_id
	LEFT JOIN
		db_municipios m
	ON
		q.ciudad=m.id
	HAVING
		`id Departamento`!=-1
	ORDER BY
			z.Departamento, Nombre";

$shownCols=array(0,2,3,4,5,6,13,36,11,12,35,30,31,25,29,28,34,33,32,15,16,17,18,19,20,21);

if($result=Queries::query($query)){
	$fields=$result->fetch_fields();
	$columns=$result->field_count;
	while($fila=$result->fetch_array(MYSQLI_BOTH)){
		//for($i=0;$i<$columns;$i++){
		foreach($shownCols as $index => $columnToShow){
			$data[$fila['Departamento']]['detalle'][$fila['id']][$fields[$columnToShow]->name]=utf8_encode($fila[$columnToShow]);
			$tableTitle[$columnToShow]=$fields[$columnToShow]->name;
		}

		if($fila['Active']==1){
			@$data[$fila['Departamento']]['Activos']++;
		}else{
			@$data[$fila['Departamento']]['Inactivos']++;
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
<script>


var status;
function sendRequest(id,field,newVal,oval){
				showLoader('Guardando Cambios', { my: "left top", at: "left bottom", of: elemento });
				
				$.ajax({
					url: "/json/formularios/asesores_update.php",
					type: 'POST',
					data: {id: id, field: field, newVal: newVal, oldVal: oval},
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
										$('#Locacion_'+id).text(citySelected);
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
				elemento=$(this);
        sendRequest(id,'pswd','pricetravel2016','');
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

            editable_columns       : [1,2,3,4,5,6,8,19,20,21,22,23,24],       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
            editable_autoResort    : false,         // auto resort after the content has changed.
            editable_validate      : function(txt, orig, columnIndex, $element){
            
                                        oldVal=orig;
                                        
                                        if(txt==""){validation=true; return txt;}else{
                                            if(columnIndex==11 || columnIndex==11 || columnIndex==12){
                                                var t = /(?:^|\s)([0-9][0-9][0-9][0-9]\-[0-9][0-9]\-[0-9][0-9])(?=\s|$)/.test(txt);
                                                validation=t;
                                            }else{validation=true;}

                                        }
                                        // only allow one word

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
            sendRequest(rowIndex,col,newContent,oldVal);
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
			$('#new_ciudad').selectmenu({
				select: function(event, ui){
					populateDeps('pdv');
				}
			}).selectmenu( "menuWidget" )
	        .addClass( "overflow" );

			$('#new_pdv').selectmenu({
				select: function(event, ui){
					populateDeps('dep');
				}
			}).selectmenu( "menuWidget" )
	        .addClass( "overflow" );

			$('#new_puesto').selectmenu({
				select: function(event, ui){
					$('#new_esquema').val(ui.item.element.attr('esquema'));
					plazaSelected=ui.item.element.attr('plaza');
				}
			}).selectmenu( "menuWidget" )
	        .addClass( "overflow" );

			$('#new_dep').selectmenu({
				select: function(event, ui){
					populateDeps('puesto');
				}
			}).selectmenu( "menuWidget" )
	        .addClass( "overflow" );

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



		/*$('#new_dep').change(function(){
			populateDeps('puesto');
		});*/

		function populateDeps(tipo){
			switch(tipo){
				case 'dep':
					listElement = $('#new_dep');
					msgLoader = "Buscando departamentos vacantes";
					break;
				case 'puesto':
					listElement = $('#new_puesto');
					msgLoader = "Buscando puestos vacantes";
					break;
				case 'ciudad':
					listElement = $('#new_ciudad');
					msgLoader = "Buscando ciudades con vacantes";
					break;
				case 'pdv':
					listElement = $('#new_pdv');
					msgLoader = "Buscando PDVs con vacantes";
					break;
			}

			var position = {my: 'center', at: 'center', of: $('#fieldset_add')};

			showLoader(msgLoader, position);

			showLoader(msgLoader);

			$.ajax({
				url: 'vacantes_listPopulate.php',
				type: 'POST',
				data: {ingreso: $('#new_ingreso').val(), dep: $('#new_dep').val(), ciudad: $('#new_ciudad').val(), oficina: $('#new_pdv').val(), tipo: tipo},
				dataType: 'json',
				success: function(array){
						data=array;

						dialogLoad.dialog('close');

						switch(tipo){
							case 'ciudad':
								$('#new_ciudad').val('').empty().selectmenu('refresh');
								$('#new_pdv').val('').empty().selectmenu('refresh');
								$('#new_dep').val('').empty().selectmenu('refresh');
								$('#new_puesto').val('').empty().selectmenu('refresh');
								break;
							case 'pdv':
								$('#new_pdv').val('').empty().selectmenu('refresh');
								$('#new_dep').val('').empty().selectmenu('refresh');
								$('#new_puesto').val('').empty().selectmenu('refresh');
								break;
							case 'dep':
								$('#new_dep').val('').empty().selectmenu('refresh');
								$('#new_puesto').val('').empty().selectmenu('refresh');
								break;
							case 'puesto':
								$('#new_puesto').val('').empty().selectmenu('refresh');
								break;
						}

						if(data['error']==1){

							showNoty('error', data['msg'],4000);

						}else{

							listElement.append('<option value="">Selecciona...</option>');

							$.each(data['vac'], function(i,info){
								if(tipo=='puesto'){
									listElement.append('<option value="' + info.id + '" esquema="'+ info.esquema +'" plaza="'+ info.plaza+'">' + info.desc + '</option>');
								}else{
									listElement.append('<option value="' + info.id + '">' + info.desc + '</option>');
								}
							});


						}

						$('#new_dep').selectmenu('refresh');
						$('#new_puesto').selectmenu('refresh');
						$('#new_ciudad').selectmenu('refresh');
						$('#new_pdv').selectmenu('refresh');

					},
				error: function(){
					dialogLoad.dialog('close');
					showNoty('error', 'Error de conexión',4000);
				}

			});

		}

		function notSave(){
      dialogCreate.accordion('option','active',false);

      $('#addForm').fadeOut(100,function(){
        $('#newAsesor').fadeIn(100);
        $('#fieldset_add input').val('').removeClass('ui-state-error');
				$('#fieldset_add select').val('').removeClass('ui-state-error');
				$('#new_dep').on('selectmenuselect', function(){}).val('').selectmenu('refresh');
				$('#new_puesto').on('selectmenuselect', function(){}).val('').selectmenu('refresh');
				$('#new_profile').on('selectmenuselect', function(){}).val('').selectmenu('refresh');
				$('#new_ciudad').on('selectmenuselect', function(){}).val('').selectmenu('refresh');
				$('#new_pdv').on('selectmenuselect', function(){}).val('').selectmenu('refresh');
				$('#new_ingreso').periodpicker('clear');
				$('#new_profile').selectmenu('refresh');
				$('#new_ingreso').periodpicker('clear');
				$('#new_activo').prop('checked',false).checkboxradio('refresh');
      });
    }

		$('#new_pdv, #new_ciudad, #new_dep, #new_puesto').empty();

		$('.activebox').each(function(){
			$(this).checkboxradio({
				icon: false,
				disabled: true
			});
		});

		$('.activebox').change(function(){
			var id=$(this).closest('td').attr('a_id');
			var col='Active';
			if($(this).prop('checked')){
				var nval=1;
				var oval=0;
				$(this).checkboxradio({
					label: 'Activo',
					disabled: true
				});
			}else{
				var nval=0;
				var oval=1;
				$(this).checkboxradio({
					label: 'Inactivo',
					disabled: true
				});

				elemento=$('#Egreso_'+id+' div');

				dialogDate.dialog('option', 'position',{my: 'center', at: 'center', of: elemento}).dialog('open');
			}
			sendRequest(id,col,nval,oval);

		});

    $('#notSaveAdd').click(function(){
      notSave();
    });

    $('#addForm').hide();


		$('#new_profile').selectmenu().selectmenu( "menuWidget" )
        .addClass( "overflow" );


		$('#new_esquema').spinner({
			incremental: false,
			step: 1,
			max: 10,
			min: 4
		}).spinner('disable');

		$('#new_activo').checkboxradio();

		$('#d_ap').periodpicker({
			norange: true,
			clearButtonInButton: true,
			todayButton: true
		});

		$('#new_ingreso').periodpicker({
			norange: true,
			clearButtonInButton: true,
			todayButton: true,
			onAfterHide: function () {
			 		populateDeps('ciudad');
			 }
	 });

		$('#saveAdd').click(function(){
			if(validateAdd()){
				addUser();
			}
		});

		$('.picker div').click(function(){
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
            var oval=elemento.text();
						var newContent = $('#d_ap').val(),
								rowIndex = elemento.closest('td').attr('a_id'),// data-row-index stored in row id
								col = elemento.closest('td').attr('col');
								if(newContent==""){
									showNoty('error','El campo de fecha es obligatorio',4000);
								}else{
									$.when(sendRequest(rowIndex,col,newContent,oval)).done(function(){
                    elemento.text($('#d_ap').val());
									});
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
					var oval=elemento.text();
					var newContent = $('#d_city_id').val(),
							rowIndex = elemento.attr('a_id'),// data-row-index stored in row id
							col = elemento.attr('col');
							if($('#d_city').val()!=""){
								sendRequest(rowIndex,'ciudad',newContent,oval);
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
            var oval=elemento.text();
					 	profileSelected=$('#d_profile option:selected').text();
						var newContent = $('#d_profile').val(),
								rowIndex = elemento.attr('a_id'),// data-row-index stored in row id
								col = elemento.attr('col');
								if($('#d_profile').val()!=""){
									sendRequest(rowIndex,'profile',newContent,oval);
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
			showLoader('Guardando Nuevo Asesor');

			$.ajax({
				url: "newUser.php",
				type: 'POST',
				data: {plaza: plazaSelected, ciudad: $('#new_ciudad').val(), nombre: $('#new_nombre').val(), apellido: $('#new_apellido').val(), ncorto: $('#new_ncorto').val(), departamento: $('#new_dep').val(), activo: $('#new_activo').prop('checked'), ingreso: $('#new_ingreso').val(), esquema: $('#new_esquema').val(), profile: $('#new_profile').val(), num_colaborador: $('#new_numcol').val(), puesto: $('#new_puesto').val()},
				dataType: 'json',
				success: function(array){
						data=array;

						dialogLoad.dialog('close');

						if(data['global']==1){
							showNoty('success', 'Asesor guardado en todas las bases', 3000);
							nuevosAsesores++;
							$('#num_new').text(nuevosAsesores);
							$('#new_notif').fadeIn(500);
							notSave();
						}else{
							$('#error_asesores').text(data['msg']['AsesoresDB']);
							$('#error_user').text(data['msg']['userDB']);
							$('#error_super').text(data['msg']['supsDB']);
							$('#error_puesto').text(data['msg']['puestoDB']);
							$( "#dialogerror" ).dialog('open');
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
<button class='button button_green_w' id='newAsesor'>Agregar</button>
<div style='width: 100%; margin: auto;'>
<div id='addForm' style='display: block; width: 547px; margin: auto'>
  <div id="accordion-Create">
    <h3>Agregar Asesor</h3>
    <div>
      <fieldset id='fieldset_add'>
				<div style='width: 235px; display: inline-block; vertical-align:top'>
					<label for="new_nombre">Nombre(s)</label>
	        <input title='Nombre(s)' type='text' id='new_nombre' name="new_nombre" required><br>
					<label for="new_apellido">Apellidos</label>
	        <input title='Apellidos' type='text' id='new_apellido' name="new_apellido" required><br>
					<label for="new_ncorto">Nombre Corto</label>
	        <input title='Nombre Corto' type='text' id='new_ncorto' name="new_ncorto" required><br>
					<label for="new_numcol">Num. Colaborador</label>
	        <input title='Num Colaborador' type='text' id='new_numcol' name="new_numcol"><br><br>
					<div style='width: 48%; display: inline-block; margin:0; vertical-align: top; margin-top: -19'>
						<label for="new_esquema">Esquema</label>
						<input title='Esquema' type='text' id='new_esquema' name="new_esquema" style='width: 60px;' required><br><br>
					</div>
					<div style='width: 48%; display: inline-block; margin:0;  padding-top: 21px; margin-top: -19'>
						<label for="new_activo" style='width: 75px;'>Activo</label>
	        	<input type="checkbox" name="new_activo" id="new_activo" value=''>
					</div>
				</div>
				<div style='width: 235px; display: inline-block;'>
					<label for="new_ingreso">Ingreso</label>
					<input title='Ingreso' type='text' id='new_ingreso' name="new_ingreso" required><br><br>
					<label for="new_ciudad">Ciudad</label>
	        <select id="new_ciudad" name="new_ciudad" title="Ciudad" required><option value=''>Selecciona...</option></select><br><br>
					<label for="new_pdv">Oficina</label>
	        <select id="new_pdv" name="new_pdv" title="Oficina" required><option value=''>Selecciona...</option></select><br><br>
					<label for="new_dep">Departamento</label>
	        <select id="new_dep" name="new_dep" title="Departamento" required><option value=''>Selecciona...</option></select><br><br>
					<label for="new_puesto">Puesto</label>
	        <select id="new_puesto" name="new_puesto" title="Puesto" required><option value=''>Selecciona...</option></select><br><br>
					<label for="new_profile">Profile</label>
					<select id="new_profile" name="new_profile" title="Profile" required><option value=''>Selecciona...</option>
						<?php
							$query="SELECT id, profile_name FROM profilesDB ORDER BY profile_name";
							if($result=Queries::query($query)){
								while($fila=$result->fetch_assoc()){
									echo "<option value='".$fila['id']."'>".$fila['profile_name']."</option>";
								}
							}
						?>
					</select><br>
				</div>
				<br><br>
				<div style='width: 300px; margin: auto; text-align:center;'><button class='button button_green_w' id='saveAdd'>Guardar</button> <button class='button button_red_w' id='notSaveAdd'>Cancelar</button></div>
      </fieldset>
    </div>
  </div>
</div>
</div>
<br>
<div id="accordion" style='width:95%; margin: auto;'>
    <?php
				foreach($data as $departamento => $info){
					echo "<h3>$departamento (Activos: ".$info['Activos']." || Inactivos: ".$info['Inactivos'].")</h3>"; //Title Print
					echo "<div>";

					//<--Print table

						//<--Print Titles
						echo "<table width='100%'  class='tablesorter' style='text-align:center;'>\n";
						echo "<thead><tr class='title'>\n";

						foreach($tableTitle as $index => $title){
							echo "<th>$title</th>";
						}
						echo "<th>Reset</th>";

						echo "</tr></thead><tbody>\n";
						//-->

						//<--Print Body
						foreach($info['detalle'] as $id => $info2){
							echo "<tr>";
								foreach($info2 as $column => $info3){
									switch($column){
										case 'Fecha_Nacimiento':
										case 'Egreso':
										case 'Ingreso':
										case "Vigencia_Visa":
										case "Vigencia_Pasaporte":
											$contenido=$info3;
											$class="class='picker'";
											break;
										case 'profile_name':
											$contenido=$info3;
											$class="class='profile_change'";
											break;
										case 'Locacion':
											$contenido=$info3;
											$class="class='city_change'";
											break;

										case 'Active':
											if($info3==1){
												$activo=' checked';
												$checkTitle="Activo";
											}else{
												$activo=' ';
												$checkTitle="Inactivo";
											}
											$contenido="<label for='check_$id' style='width:95px;'>$checkTitle</label><input name='check_$id' id='check_$id' type='checkbox' class='activebox' $activo>";
											break;
										default:
											$contenido=$info3;
											$class="";
											break;
									}

									echo "<td a_id='$id' col='$column' id='".$column."_$id' $class>$contenido</td>";
								}

								//Reset button
								echo "<td><button asesor='$id' class='buttonlarge button_red_w resetpswd'>Reset Pswd</button></td>";
							echo "</tr>";
						}

						echo "</tbody></table>";
						//-->

					//-->

					echo "</div>";
				}


    ?>

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
			</tr>
		</tbody>
	</table>
</div>

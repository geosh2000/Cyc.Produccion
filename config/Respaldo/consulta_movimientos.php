<?php

include_once('../modules/modules.php');

initSettings::start(true,'config');
initSettings::printTitle('Consulta de Movimientos de Vacantes');
timeAndRegion::setRegion('Cun');


$tbody="<td><button class='buttonlarge button_red_w' id='s_disponibles'>Vacantes Disponibles</button></td>";
$tbody.="<td><button class='buttonlarge button_red_w' id='s_vacantes'>Movimientos Vacantes</button></td>";
$tbody.="<td><button class='buttonlarge button_red_w' id='s_departamento'>Movimientos Departamento</button></td>";


Filters::showFilterNOFORM('s_asesor','Movimientos Asesor',$tbody);

$query="SELECT id, Departamento FROM PCRCs ORDER BY Departamento";
if($result=Queries::query($query)){
  while($fila=$result->fetch_assoc()){
      @$listdep.= "<option value='".$fila['id']."'>".utf8_encode($fila['Departamento'])."</option>";
  }
}
$query="SELECT id, Puesto FROM PCRCs_puestos ORDER BY Puesto";
if($result=Queries::query($query)){
  while($fila=$result->fetch_assoc()){
    @$listpuesto.="<option value='".$fila['id']."'>".utf8_encode($fila['Puesto'])."</option>";
  }
}
 ?>
<style>
  .ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }
  .searchvac{
    cursor: hand;
  }
</style>

<script>
$(function(){

  $('#showFilterSecondary').hide();

  $('#s_disponibles, #s_vacantes, #s_departamento, #s_asesor').click(function(){
    id=$(this).attr('id');
    this_el=$(this);
    $('#s_disponibles, #s_vacantes, #s_departamento, #s_asesor').removeClass('button_red_w').removeClass('button_blue_w').addClass('button_red_w');
    $('#showFilterSecondary').fadeOut(500,function(){
      switch(id){
        case 's_disponibles':
          $('.f_fecha').show();
          $('#fecha').prop('required',true);
          $('.f_dep').show();
          $('#dep').prop('required',false);
          $('.f_asesor').hide();
          $('#name').prop('required',false);
          $('.f_vacante').hide();
          $('#vacante').prop('required',false);
          tipo='disponibles';
          break;
        case 's_vacantes':
          $('.f_fecha').hide();
          $('#fecha').prop('required',false);
          $('.f_dep').hide();
          $('#dep').prop('required',false);
          $('.f_asesor').hide();
          $('#name').prop('required',false);
          $('.f_vacante').show();
          $('#vacante').prop('required',true);
          tipo='vacante';
          break;
        case 's_departamento':
          $('.f_fecha').hide();
          $('#fecha').prop('required',false);
          $('.f_dep').show();
          $('#dep').prop('required',true);
          $('.f_asesor').hide();
          $('#name').prop('required',false);
          $('.f_vacante').hide();
          $('#vacante').prop('required',false);
          tipo='departamento';
          break;
        case 's_asesor':
          $('.f_fecha').hide();
          $('#fecha').prop('required',false);
          $('.f_dep').hide();
          $('#dep').prop('required',false);
          $('.f_asesor').show();
          $('#name').prop('required',true);
          $('.f_vacante').hide();
          $('#vacante').prop('required',false);
          tipo='asesor';
          break;
      }
      this_el.removeClass('button_red_w').addClass('button_blue_w');
    });

    $('#showFilterSecondary').fadeIn(500);
  });

  $('#fecha').periodpicker({
    norange: true,
    clearButtonInButton: true,
    todayButton: true
   });

   $('#d_ap').periodpicker({
     norange: true,
     clearButtonInButton: true,
     todayButton: true
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

    $( "#name" ).catcomplete({
      delay: 0,
      minLenght: 3,
      source: 'search_name.php',
      select: function(ev, ui){
        asesorSelected = ui.item.id;
        asesorSelected_text = ui.item.label;
        $('#baja_name').text(asesorSelected_text);
        console.log("id asesor seleccionado: "+asesorSelected);
      }
    });

    asesorSelected="";
  function printTable(){
    $('#result-table').empty();

    var position = {my: 'center', at: 'top', of: $('#result-table')};
    showLoader('Obteniendo Información',position);

    $.ajax({
      url: 'get_movimientos.php',
      type: 'POST',
      data: {tipo: tipo, fecha: $('#fecha').val(), dep: $('#dep').val(), asesor: asesorSelected, vacante: $('#vacante').val()},
      dataType: 'json',
      success: function(array){
          data=array;

          $('#result-table').empty();

          dialogLoad.dialog('close');

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

            $('#result-table').prepend("<div style='width: 85%; margin: auto;'><button class='button button_blue_w' id='export'>Exportar</button>");
      },
      error: function(){
        dialogLoad.dialog('close');
        showNoty('error','Error de conexión',3000);
      }
    })


  }


  function  searchMop(){

    printTable();

  }

  $('#search').click(function(){
      searchMop();
  });


  $(document).on('click','#export',function(){
    $('.tablesorter').trigger('outputTable');
  });

  $(document).on('click','.dateedit',function(){
    elemento=$(this);
    $('#d_ap').val(elemento.text()).periodpicker('change');
    var position = {my: 'center', at: 'center', of: elemento.closest('tr')};
    dialogDate.dialog('option','position',position).dialog('open');
  });

  $(document).on('click','.textedit',function(){
    elemento=$(this);
    $('#d_tx').val(elemento.text());
    var position = {my: 'center', at: 'center', of: elemento.closest('tr')};
    dialogText.dialog('option','position',position).dialog('open');
  });

  $(document).on('click','.searchvac',function(){
    elemento=$(this);
    $('#s_disponibles, #s_departamento, #s_asesor').removeClass('button_red_w').removeClass('button_blue_w').addClass('button_red_w');
    $('#s_vacantes').addClass('button_blue_w');
    $('.f_fecha').hide();
    $('#fecha').prop('required',false);
    $('.f_dep').hide();
    $('#dep').prop('required',false);
    $('.f_asesor').hide();
    $('#name').prop('required',false);
    $('.f_vacante').show();
    $('#vacante').prop('required',true).val(elemento.text());
    tipo='vacante';
    searchMop();
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
    width: 200,
    buttons: {
      "Asignar": function(){
          var newContent = $('#d_tx').val(),
              rowIndex = elemento.attr('row'),// data-row-index stored in row id
              col = elemento.attr('col');
              if(newContent==""){
                showNoty('error','El campo de fecha es obligatorio',4000);
              }else{
                sendRequest(rowIndex,col,newContent);
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
    var position = {my: 'center', at: 'center', of: elemento.closest('tr')};
		showLoader('Guardando Cambios', position);

		$.ajax({
			url: "mop_update.php",
			type: 'POST',
			data: {id: id, field: field, newVal: newVal},
			dataType: 'json',
			success: function(array){
					data=array;

					dialogLoad.dialog('close');

					if(data['status']==1){
						showNoty('success','Cambios Guardados',3000);

            elemento.text(newVal);

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

});
</script>
<style>
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    .overflow { height: 200px; }
  </style>
<div id="showFilterSecondary" style="background:#99bfe6; height: 43px; margin: 0;">
  <table style="width:100px; margin: auto; text-align: center; font-weight: normal; color: white;">
    <tbody>
      <tr>
        <td class='f_fecha'><input type='text' id='fecha'></td>
        <td class='f_vacante'>Vacante:</td><td class='f_vacante'><input type='text' id='vacante'></td>
        <td class='f_asesor'><input type='text' id='name' placeholder='Nombre del asesor' size=50></td>
        <td class='f_dep'>Departamento</td><td class='f_dep'>
          <select id='dep'>
            <option value=''>Selecciona...</option>
            <?php
              $query="SELECT id, Departamento FROM PCRCs ORDER BY Departamento";
              if($result=Queries::query($query)){
                while($fila=$result->fetch_assoc()){
                  echo "<option value='".$fila['id']."'>".$fila['Departamento']."</option>";
                }
              }
            ?>
          </select>
        </td>
        <td><button class='button button_green_w' id='search'>Consultar</button></td>
      </tr>
    </tbody>
  </table>
</div>
<br>
<div id='parameters'>
  <div id='result-table' style='width: 85%; margin: auto'></div>
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

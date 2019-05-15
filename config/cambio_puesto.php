<?php

include_once('../modules/modules.php');

initSettings::start(true,'admin');
initSettings::printTitle('Cambio de Puesto');
timeAndRegion::setRegion('Cun');


$tbody="<td><input type='text' id='name' placeholder='Nombre del asesor' size=50></td>";

Filters::showFilterNOFORM('search','Consultar',$tbody);

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
</style>

<script>
$(function(){

  $('#inicio').periodpicker({
    norange: true,
    clearButtonInButton: true,
    todayButton: true,
    onAfterHide: function () {
        populateDeps('ciudad');
     },
     formatDate: 'YYYY-MM-DD'
 });

  deletePuestoDialog=$( "#delete-confirm" ).dialog({
    resizable: false,
    height:190,
    width:400,
    autoOpen: false,
    modal: true,
    buttons: {
      "Eliminar Puesto": function() {
        deletePuesto(idPuesto);
        $( this ).dialog( "close" );
      },
      Cancel: function() {
        $( this ).dialog( "close" );
      }
    }
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
    
    

    $( "#new" ).autocomplete({
      delay: 0,
      minLenght: 3,
      source: 'search_pcrc.php',
      select: function(ev, ui){
        $('#pcrc_selected').val(ui.item.id);
      }
    });

  function printTable(){
    $('#result-table').empty();
    egreso='';
    showLoader('Obteniendo Información');
    delete dateEgreso;
    dateEgreso = {};
  $.ajax({
      url: 'getPuesto.php',
      type: 'POST',
      data: {id: asesorSelected},
      dataType: 'json',
      success: function(array){
          data=array;
          egreso=data['egresoCheck'];
          dateEgreso = new Date(data['egreso'])

          if(egreso==1){
            $('#newAssign').hide();
          }else{
            $('#newAssign').show();
          }

          dialogLoad.dialog('close');

          $('#result-table').tablesorter({
              theme: 'jui',
              headerTemplate: '{content} {icon}',
            widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders'],
              tableClass: 'center',
              data: data['table'],
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
                      output_saveFileName  : 'cuartiles_<?php echo $from."a".$to;?>.csv',
                      // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                      output_encoding      : 'data:application/octet-stream;charset=utf8,',

                      //stickyHeaders
                      stickyHeaders_attachTo: '#result-table',


              }
            });
      }
    })


  }

  function apply_change(tipo){

    flag=true;

    if(tipo!=3){
      if($('#inicio').prop('required') && $('#inicio').val()==''){
        flag=false;
        $(this).addClass('ui-state-error');
        showNoty('error', 'Fecha de cambio, si la vacante es reemplazable', 3000);
      }

      $('#newAssign select').each(function(){
        if($(this).prop('required') && $(this).val()==''){
          flag=false;
          $(this).addClass('ui-state-error');
          showNoty('error', $(this).attr('title')+', si la vacante es reemplazable', 3000);
        }else{
          $(this).removeClass('ui-state-error');
        }
      });

      if($('#newinicio').prop('required') && $('#newinicio').val()==''){
        flag=false;
        $(this).addClass('ui-state-error');
        showNoty('error', 'Fecha de liberación obligatoria, si la vacante es reemplazable', 3000);
      }
    }


    if(!flag){
      dialogLoad.dialog('close');
    }else{

        switch(tipo){
          case 1:
              ap_fecha_in=$('#inicio').val();
              ap_fecha_out=$('#newinicio').val();
              ap_plaza=plazaSelected;
              ap_id=searchedAsesor;
              ap_replace=$('#newreplace').prop('checked');
              tipo='change';
            break;
            case 3:
              ap_fecha_in=$('#inicio').val();
              ap_fecha_out=$('#d_replace').val();
              ap_plaza=0;
              ap_id=searchedAsesor;
              ap_replace=$('#replace').prop('checked');
              tipo='baja';
            break;
        }


        $.ajax({
          url: 'apply_change.php',
          type: 'POST',
          data: {id: ap_id, fecha_in: ap_fecha_in, fecha_out: ap_fecha_out, vacante: ap_plaza, replace: ap_replace, tipo: tipo},
          dataType: 'json',
          success: function(array){
            results=array;

            dialogLoad.dialog('close');
            showNoty(results['status_noty'], results['msg'], 4000);

            if(tipo==2){
              if(results['status']==1){
                dialogEdit.dialog( "close" );
                $('#asesor').val($('#text_asesor').val());
                searchAsesor();
              }
            }else{
              if(results['status']==1){
                $('#asesor').val($('#text_asesor').val());
                searchAsesor();
              }
            }

          },
          error: function(){
              dialogLoad.dialog('close');
              showNoty('error', 'Error en la conexión', 4000);
            }

        });
      }
  };

  function  searchAsesor(){


    $('#new').selectmenu().on('selectmenuselect', function(){}).val('').selectmenu('refresh').selectmenu({
      select: function(event, ui){
        populateDeps('puesto');
      }
    }).selectmenu( "menuWidget" )
        .addClass( "overflow" );

    $('#new_ciudad').selectmenu().on('selectmenuselect', function(){}).val('').selectmenu('refresh').selectmenu({
      select: function(event, ui){
        populateDeps('pdv');
      }
    }).selectmenu( "menuWidget" )
        .addClass( "overflow" );

    $('#new_pdv').selectmenu().on('selectmenuselect', function(){}).val('').selectmenu('refresh').selectmenu({
      select: function(event, ui){
        populateDeps('dep');
      }
    }).selectmenu( "menuWidget" )
        .addClass( "overflow" );

    $('#newpuesto').selectmenu().on('selectmenuselect', function(){}).val('').selectmenu('refresh').selectmenu({
      select: function(event, ui){
        plazaSelected=ui.item.element.attr('plaza');
      }
    }).selectmenu( "menuWidget" )
        .addClass( "overflow" );

    $('#newinicio').val('').periodpicker('clear');

    $('#newreplace').prop('checked',true).checkboxradio('refresh');

    printTable();
    $('#pcrc_selected').val('');
    searchedAsesor=asesorSelected;
    $('#text_asesor').val(asesorSelected_text);
    $('#newAssign').show();
    $('#inicio').periodpicker('clear');
  }
  asesorSelected = 14;
    asesorSelected_text = "Dulce Ivanova Seoane Cespedes";
    $('#baja_name').text(asesorSelected_text);
  $('#search').click(function(){
      searchAsesor();
  });


  $("#result-table").bind("DOMSubtreeModified", function() {
	    dialogLoad.dialog('close');
	});

  $('#newAssign').hide();

  // Source: http://stackoverflow.com/questions/497790
 dates = {
    convert:function(d) {
        // Converts the date in d to a date-object. The input can be:
        //   a date object: returned without modification
        //  an array      : Interpreted as [year,month,day]. NOTE: month is 0-11.
        //   a number     : Interpreted as number of milliseconds
        //                  since 1 Jan 1970 (a timestamp)
        //   a string     : Any format supported by the javascript engine, like
        //                  "YYYY/MM/DD", "MM/DD/YYYY", "Jan 31 2009" etc.
        //  an object     : Interpreted as an object with year, month and date
        //                  attributes.  **NOTE** month is 0-11.
        return (
            d.constructor === Date ? d :
            d.constructor === Array ? new Date(d[0],d[1],d[2]) :
            d.constructor === Number ? new Date(d) :
            d.constructor === String ? new Date(d) :
            typeof d === "object" ? new Date(d.year,d.month,d.date) :
            NaN
        );
    },
    compare:function(a,b) {
        // Compare two dates (could be of any type supported by the convert
        // function above) and returns:
        //  -1 : if a < b
        //   0 : if a = b
        //   1 : if a > b
        // NaN : if a or b is an illegal date
        // NOTE: The code inside isFinite does an assignment (=).
        return (
            isFinite(a=this.convert(a).valueOf()) &&
            isFinite(b=this.convert(b).valueOf()) ?
            (a>b)-(a<b) :
            NaN
        );
    },
    inRange:function(d,start,end) {
        // Checks if date in d is between dates in start and end.
        // Returns a boolean or NaN:
        //    true  : if d is between start and end (inclusive)
        //    false : if d is before start or after end
        //    NaN   : if one or more of the dates is illegal.
        // NOTE: The code inside isFinite does an assignment (=).
       return (
            isFinite(d=this.convert(d).valueOf()) &&
            isFinite(start=this.convert(start).valueOf()) &&
            isFinite(end=this.convert(end).valueOf()) ?
            start <= d && d <= end :
            NaN
        );
    }
}

  dateChange = {};
  dateEgreso = {};

  $('#assign').click(function(){
    delete dateChange;
    dateChange = new Date($('#inicio').val())
    if(dates.compare(dateChange,dateEgreso)>=0){
      showNoty('error','No es posible asignar cambios con fecha posterior al Egreso del asesor',4000);
    }else{
      showLoader('Aplicando Nuevo Puesto');
      apply_change(1);
    }
  });

<?php if($_SESSION['config']==1){ ?>
  function deletePuesto(id){
    showLoader('Elminiando Cambio');
    $.ajax({
      url: 'deletePuesto.php',
      type: 'POST',
      data: {id: id},
      dataType: 'json',
      success: function(array){
        results=array;

        dialogLoad.dialog('close');
        showNoty(results['status'], results['msg'], 4000);

        $('#asesor').val($('#text_asesor').val());
        searchAsesor();

        },
      error: function(){
          dialogLoad.dialog('close');
          showNoty('error', 'Error en la conexión', 4000);
        }

    });
  }
<?php } ?>
  $(document).on('click','.deletePuesto',function(){
    idPuesto=$(this).attr('puestoid');
    deletePuestoDialog.dialog('open');
  });

  $(document).on('click','.editPuesto',function(){
    idPuesto=$(this).attr('puestoid');
    $('#edit_id').val(idPuesto);
    $('#edit_name').val(asesorSelected_text);
    tds=$(this).closest('tr').find('td');
    $('#edit_fecha').val(tds[2].textContent);
    $('#edit_dep').val($('#dep_'+idPuesto).val());
    $('#edit_puesto').val($('#puesto_'+idPuesto).val());
    dialogEdit.dialog('open');
  });

  dialogEdit = $('#dialog-edit').dialog({
    autoOpen: false,
      height: 'auto',
      width: 650,
      modal: true,
      buttons: {
        "Save": function(){
          apply_change(2);
        },
        Cancel: function() {
          dialogEdit.dialog( "close" );
        }
      },
      close: function() {
        $('#editPuestoFieldset input').val('');
        $('#editPuestoFieldset select').val('');
      }
  });

  $('#new').selectmenu().selectmenu( "menuWidget" )
      .addClass( "overflow" );

  $('#newpuesto').selectmenu().selectmenu( "menuWidget" )
      .addClass( "overflow" );

      function populateDeps(tipo){
        switch(tipo){
          case 'dep':
            listElement = $('#new');
            msgLoader = "Buscando departamentos vacantes";
            break;
          case 'puesto':
            listElement = $('#newpuesto');
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



        showLoader(msgLoader);

        $.ajax({
          url: 'vacantes_listPopulate.php',
          type: 'POST',
          data: {ingreso: $('#inicio').val(), dep: $('#new').val(), ciudad: $('#new_ciudad').val(), oficina: $('#new_pdv').val(), tipo: tipo},
          dataType: 'json',
          success: function(array){
              data=array;

              dialogLoad.dialog('close');

              switch(tipo){
                case 'ciudad':
                  $('#new_ciudad').val('').empty();
                  $('#new_pdv').val('').empty();
                  $('#new').val('').empty();
                  $('#newpuesto').val('').empty();
                  break;
                case 'pdv':
                  $('#new_pdv').val('').empty();
                  $('#new').val('').empty();
                  $('#newpuesto').val('').empty();
                  break;
                case 'dep':
                  $('#new').val('').empty();
                  $('#newpuesto').val('').empty();
                  break;
                case 'puesto':
                  $('#newpuesto').val('').empty();
                  break;
              }

              if(data['error']==1){

                showNoty('error', data['msg'],4000);

              }else{

                listElement.append('<option value="">Selecciona...</option>');

                $.each(data['vac'], function(i,info){
                  if(tipo=='puesto'){
                    listElement.append('<option value="' + info.id + '" esquema="'+ info.esquema +'" plaza="'+info.plaza+'">' + info.desc + '</option>');
                  }else{
                    listElement.append('<option value="' + info.id + '">' + info.desc + '</option>');
                  }

                });


              }

              $('#new').selectmenu('refresh');
              $('#newpuesto').selectmenu('refresh');
              $('#new_ciudad').selectmenu('refresh');
              $('#new_pdv').selectmenu('refresh');

            },
          error: function(){
            dialogLoad.dialog('close');
            showNoty('error', 'Error de conexión',4000);
          }

        });

      }

      dialogBaja=$('#dialog-confirmBaja').dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        autoOpen: false,
        buttons: {
          "Asignar Baja": function() {
            if($('#replace').prop('checked')){
              if($('#d_replace').val()==''){
                showNoty('error','Debes seleccionar una fecha para liberar la vacante',4000);
              }else{
                apply_change(3);
                $( this ).dialog( "close" );
              }
            }else{
              apply_change(3);
              $( this ).dialog( "close" );
            }

          },
          Cancel: function() {
            $('#replace').prop('checked',true);
            $('#replace').checkboxradio('option','label','Reemplazable').checkboxradio('refresh');
            $('#d_replace').val('').periodpicker('change').periodpicker('enable');
            $( this ).dialog( "close" );
          }
        }
      });

      $('#baja').click(function(){
        if(egreso==1){
          showNoty('error', 'Este asesor ya cuenta con una baja asignada, por lo que no es posible asignar una nueva fecha',4000);
        }else{
          if($('#inicio').val()==''){
            showNoty('error', 'Debes seleccionar una fecha primero',4000);
          }else{
            $('#baja_date').val($('#inicio').val());
            dialogBaja.dialog('open');

          }
        }
      });

    $( ".active" ).checkboxradio({
      icon: false
    });

    $( "#replace" ).checkboxradio({
      icon: false
    });

    $('#replace').change(function(){
      if($(this).prop('checked')){
        $( "#replace" ).checkboxradio('option','label','Reemplazable');
        $('#d_replace').periodpicker('enable');
      }else{
        $( "#replace" ).checkboxradio('option','label','No Reemplazable');
        $('#d_replace').periodpicker('disable');
      }

      $( "#replace" ).checkboxradio('refresh');

    });

    $('#d_replace').periodpicker({
      norange: true,
      formatDate: 'YYYY-MM-DD'
    });

    $('#newinicio').periodpicker({
      norange: true,
      formatDate: 'YYYY-MM-DD'
    });

    $( "#newreplace" ).checkboxradio({
      icon: false
    });

    $('#newreplace').change(function(){
      if($(this).prop('checked')){
        $( "#newreplace" ).checkboxradio('option','label','Reemplazable');
        $('#newinicio').prop('required',true).periodpicker('enable');
        if($('#inicio').val()!=''){
          $('#newinicio').val($('#inicio').val()).periodpicker('change');
        }
      }else{
        $( "#newreplace" ).checkboxradio('option','label','No Reemplazable');
        $('#newinicio').prop('required',false).val('').periodpicker('clear').periodpicker('disable');
      }

      $( "#newreplace" ).checkboxradio('refresh');

    });

    $('#inicio').change(function(){
      if($('#newreplace').prop('checked')){
        $('#newinicio').val($(this).val()).periodpicker('change');
      }
    });

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
<br>
<div id='parameters'>
  <div id='result-table' style='width: 1510px; margin: auto; max-height:350px; overflow: auto; position: relative'></div>
  <br>
  <div id='newAssign' style='width: auto; margin: auto'>
    <table class='t2' style='margin: auto; text-align:center;'>
      <tr class='title'><th>Fecha</th><th>Ciudad</th><th>Oficina</th><th>Nuevo Departamento</th><th>Nuevo Puesto</th><th>Reemplazable</th><th>Fecha Liberación Vacante</th></tr>
      <tr class='pair'>
        <td><input type='text' id='inicio' title='Fecha de Cambio' required></td>
        <td><select id="new_ciudad" name="new_ciudad" title="Ciudad" required><option value=''>Selecciona...</option></select></td>
        <td><select id="new_pdv" name="new_pdv" title="PDV" required><option value=''>Selecciona...</option></select></td>
        <td><select id='new' name='new' title='Departamento' required><option value="">Selecciona...</option></select></td>
        <td><select id='newpuesto' name='newpuesto' title='Puesto' required><option value="">Selecciona...</option></select></td>
        <td><label for="newreplace">Reemplazable</label><input type='checkbox' name='newreplace' id='newreplace' checked></td>
        <td><input type='text' id='newinicio'></td>

      <tr class='odd'><td colspan=100><button class='button button_green_w' id='assign'>Asignar</button> <button class='button button_red_w' id='baja'>BAJA</button></td></tr>

    </table>
  </div>
</div>
<div id="delete-confirm" title="Eliminar Moper">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El puesto se eliminará del registro<br>¿Estás seguro?</p>
</div>
<div id="dialog-edit" title="Editar Puesto Existente">
    <fieldset id='editPuestoFieldset'>
      <div style='width:300px; display: inline-block; vertical-align: top;'>
        <label for="edit_id">id</label>
        <input type="text" name="edit_id" id="edit_id" value="" class="text ui-widget-content ui-corner-all" readonly disabled>
        <label for="edit_name">Asesor</label>
        <input type="text" name="edit_name" id="edit_name" value="" class="text ui-widget-content ui-corner-all" readonly disabled>
        <label for="edit_fecha">Fecha</label>
        <input type="text" name="edit_fecha" id="edit_fecha" value="" class="text ui-widget-content ui-corner-all" readonly disabled>
      </div>
      <div style='width:300px; display: inline-block; vertical-align: top;'>
        <label for="edit_dep">Departamento</label>
        <select id='edit_dep' name='edit_dep' required>
          <option value="">Selecciona...</option>
          <?php
            echo $listdep;
          ?>
        </select>
        <label for="edit_puesto">Puesto</label>
        <select id='edit_puesto' name='edit_puesto' required>
          <option value="">Selecciona...</option>
          <?php
            echo $listpuesto;
          ?>
        </select>

      </div>
    </fieldset>
</div>

<div id="dialog-confirmBaja" title="Asignar baja a asesor">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Estás seguro que deseas dar de baja a <span id='baja_name'></span> con fecha <span id='baja_date'></span>? </p>
  <div style='width: 95%; margin: auto'>
    <label for='replace'>Reemplazable</label><input type='checkbox' name='replace' id='replace' checked><br><br>
    <label for='d_replace'>Fecha de inicio para vacante <span style='font-size: 10px'>(Fecha para liberar vacante)</span></label><input type='text' name='d_replace' id='d_replace'>
  </div>
</div>

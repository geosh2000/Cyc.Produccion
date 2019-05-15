<?php
include_once("../modules/modules.php");

initSettings::start(true,'cxc_read');
initSettings::printTitle('Registro de CxC');

timeAndRegion::setRegion('Cun');
Scripts::periodScript('inicio','fin');

$tbody="<td><input type='text' id='inicio'><input type='text' id='fin'></td><td><input type='text' id='search_asesor' placeholder='Asesor (opcional)' size=25></td><td><button class='button button_blue_w' id='clearSearch'>Limpiar</button></td>";

Filters::showFilterNOFORM('search', 'Consultar', $tbody);

?>
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
</style>
<script>
  $(function(){

    $('#d_ap, #new_fechaCxC, #new_fechaAp').periodpicker({
      lang: 'en',
      animation: true,
      norange: true
    });

    $( "#new_firmado" ).checkboxradio();

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

      $( "#new_asesor" ).catcomplete({
        delay: 0,
        minLenght: 3,
        source: '../config/search_name.php',
        select: function(ev, ui){
          asesorSelected=ui.item.id;
          $('#new_asesor_hidden').val(ui.item.id);
          asesorSelected_text = ui.item.label;
          console.log("id asesor seleccionado: "+asesorSelected);
        }
      });

      asesorSelected_search='';

      $( "#search_asesor" ).catcomplete({
        delay: 0,
        minLenght: 3,
        source: '../config/search_name.php',
        select: function(ev, ui){
          asesorSelected_search=ui.item.id;
          asesorSelected_text_search = ui.item.label;
          console.log("id asesor seleccionado: "+asesorSelected_search);
        }
      });

    $('#clearSearch').click(function(){
      $('#inicio').periodpicker('clear');
      $('#search_asesor').val('');
      asesorSelected_search='';
      asesorSelected_text_search='';
    });

    dialogDelete = $( "#dialog-delete" ).dialog({
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Borrar CxC": function() {
          deleteCxC();
        },
        Cancel: function() {
          dialogDelete.dialog( "close" );
        }
      }
    });

    dialogDate = $('#dialog-date').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 300,
      buttons: {
        "Asignar": addDateAp,
        Cancel: function(){
          dialogDate.dialog('close');
        }
      },
      close: function(){
          $('#d_ap').periodpicker('clear');
        }
    });

    dialogCreate = $('#accordion-cxcCreate').accordion({
      heightStyle: "content",
      collapsible: true,
      active: false
    });

    form = dialogDate.find("form");

    function addDateAp(){
      updateSigned(idDateAp, $('#d_ap').val(), 'fecha_aplicacion', DateApSelector);
    }

    function deleteCxC(){
      updateSigned(idDelete, 1, 'delete', deleteSelector);
    }

    function updateSigned(id, val, field, thisField){
      showLoader('Guardando Cambios');

      $.ajax({
        url: 'updateCxC.php',
        type: 'POST',
        data: {id: id, val: val, field: field},
        dataType: 'json',
        success: function(array){
            data=array;

            if(data['status']==1){
              dialogLoad.dialog('close');
              showNoty('success','Cambios aplicados',4000);

              switch(field){
                case 'firmado':
                  html=thisField.parent().html();
                  parent=thisField.parent();
                  if(thisField.prop('checked')){
                    thisField.parent().html(html.replace("No","Si"));
                    parent.find('input').prop('checked',true);
                  }else{
                    thisField.parent().html(html.replace("Si","No"));
                    parent.find('input').prop('checked',false);
                  }
                  break;
                case 'comment':
                  thisField.val(val);
                  break;
                case 'fecha_aplicacion':
                  thisField.html("<p cxcid='"+id+"' class='d_ap_sel'>"+val+"</p>");
                  dialogDate.dialog('close');
                  break;
                case 'delete':
                  thisField.remove();
                  dialogDelete.dialog('close');
                  break;
                default:
                  thisField.text(val);
                  break;
              }

              if(field!='delete'){
                $('#updatedId_'+id).html('<p>'+data['updater']+'</p>');
                $('#lupdatedId_'+id).html('<p>'+data['l_updater']+'</p>');
              }
            }else{
              dialogLoad.dialog('close');
              dialogDate.dialog('close');
              dialogDelete.dialog('close');
              showNoty('error',data['msg'],4000);
            }

          },
        error: function(){
          dialogLoad.dialog('close');
          dialogDate.dialog('close');
          dialogDelete.dialog('close');
          showNoty('error', 'Error de conexión',4000);
        }

      });
    }

    function getCxC(){

      showLoader('Obteniendo información de CxC');

      $.ajax({
        url: 'getCxC.php',
        type: 'POST',
        data: {inicio: $('#inicio').val(), fin: $('#fin').val(), asesor: asesorSelected_search},
        dataType: 'json',
        success: function(array){
            data=array;

            printTable(data);
            dialogLoad.dialog('close');

          },
        error: function(){
          dialogLoad.dialog('close');
          showNoty('error', 'Error de conexión',4000);
        }

      });
    }

    function printTable(dataObject){
      $('#result-table').empty();

      $('#result-table').tablesorter({
          theme: 'jui',
          headerTemplate: '{content} {icon}',
          data: dataObject,
          widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders'],
          tableClass: 'center',
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
            }
        });
    }

    $('#search').click(function(){
      getCxC();
    });

    $(document).on('click','.docSigned',function(){
      idCxc=$(this).attr('id');
      if($(this).prop('checked')){
        newVal=1;
      }else{
        newVal=0;
      }
      updateSigned(idCxc,newVal, 'firmado', $(this));
    });

    $(document).on('change','.commentEdit',function(){
      idComment=$(this).attr('cxcid');
      newVal=$(this).val();
      updateSigned(idComment,newVal, 'comments', $(this));
    });

    $(document).on('click','.d_ap_sel',function(){
      idDateAp=$(this).attr('cxcid');
      DateApSelector=$(this);
      dialogDate.dialog('open');
    });

    $(document).on('click','.deletePuesto',function(){
      idDelete=$(this).attr('cxcid');
      deleteSelector=$(this).closest('tr');
      dialogDelete.dialog('open');
    });

    $('#newCxC').click(function(){
      $('#cxcForm').fadeIn(100,function(){
        dialogCreate.accordion('option','active',0);
        $('#newCxC').fadeOut(100);
      });
    });

    function notSave(){
      dialogCreate.accordion('option','active',false);

      $('#cxcForm').fadeOut(100,function(){
        $('#newCxC').fadeIn(100);
        $('#fieldset_CXC input').val('').removeClass('ui-state-error');
        $('#new_fechaCxC, #new_fechaAp').periodpicker('clear');
        $('#new_firmado').prop('checked',false).checkboxradio('refresh');
      });
    }

    $('#notSaveCxC').click(function(){
      notSave();
    });

    $('#cxcForm').hide();

    function addCxC(){
      showLoader('Guardando CxC');

      $.ajax({
        url: 'saveCxC.php',
        type: 'POST',
        data: {asesor: $('#new_asesor_hidden').val(), loc: $('#new_loc').val(), monto: $('#new_monto').val(), f_cxc: $('#new_fechaCxC').val(), f_ap: $('#new_fechaAp').val(), firmado: $('#new_firmado').prop('checked'), comments: $('#new_comments').val()},
        dataType: 'json',
        success: function(array){
            data=array;

            dialogLoad.dialog('close');

            if(data['status']==1){
              showNoty('success','CxC Guardado Correctamente', 4000);
              notSave();
            }else{
              showNoty('error',data['msg'],6000);
            }

          },
        error: function(){
          dialogLoad.dialog('close');
          showNoty('error', 'Error de conexión',4000);
        }

      });
    }

    function validateForm(){
      flag=true;

      $('#fieldset_CXC input').each(function(){
        if($(this).prop('required')){
          if($(this).val()==''){
            flag=false;
            $(this).addClass('ui-state-error');
            showNoty('error', 'Información de '+$(this).attr('title')+' obligatoria',4000);
          }else{
            $(this).removeClass('ui-state-error');
          }
        }
      });

      return flag;
    }

    $('#saveCxC').click(function(){
      if(validateForm()){
        addCxC();
      }
    });


  });
</script>
<?php if($_SESSION['cxc_registro']==1){ ?>
<button class='button button_green_w' id='newCxC'>Agregar</button>
<div style='width: 100%; margin: auto;'>
<div id='cxcForm' style='display: block; width: 300px; margin: auto'>
  <div id="accordion-cxcCreate">
    <h3>Agregar CxC</h3>
    <div>
      <fieldset id='fieldset_CXC'>
        <label for="new_asesor">Asesor</label>
        <input title='Asesor' type='text' id='new_asesor' name="new_asesor" placeholder='Nombre del asesor' required><input type='hidden' id='new_asesor_hidden' name="new_asesor_hidden"><br>
        <label for="new_loc">Localizador</label>
        <input title='Localizador' type='text' id='new_loc' name="new_loc" placeholder='Localizador' required><br>
        <label for="new_monto">Monto</label>
        <input title='Monto' type='text' id='new_monto' name="new_monto" placeholder='Monto' required><br>
        <label for="new_fechaCxC">Fecha CxC</label>
        <input title='Fecha CxC' type="text" name="new_fechaCxC" id="new_fechaCxC" value='' required><br>
        <label for="new_fechaAp">Fecha Aplicación</label>
        <input type="text" name="new_fechaAp" id="new_fechaAp" value=''><br><br>
        <label for="new_firmado">Firmado</label>
        <input type="checkbox" name="new_firmado" id="new_firmado" value=''><br><br>
        <label for="new_comments">Comentarios</label>
        <input type="text" name="new_comments" id="new_comments" value=''><br>
        <br><button class='button button_green_w' id='saveCxC'>Guardar</button> <button class='button button_red_w' id='notSaveCxC'>Cancelar</button>
      </fieldset>
    </div>
  </div>
</div>
</div>
<?php } ?>
<br>
<div id='result-table'></div>
<div id="dialog-date" title="Cambiar Fecha de Aplicación" style='text-align:center;'>
  <form>
    <fieldset>
      <label for="d_ap">Nueva Fecha</label>
      <input type="text" name="d_ap" id="d_ap" value=''>
    </fieldset>
  </form>
</div>
<div id="dialog-delete" title="Eliminar CxC">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Seguro que deseas eliminar este CxC de la base?</p>
</div>

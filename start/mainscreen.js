  google.charts.load("current", {packages:["timeline"]});
  google.charts.setOnLoadCallback(drawChartOK);
  function drawChartOK() {
    var container = document.getElementById('timeline');
    var chart = new google.visualization.Timeline(container);

    var options = {
      timeline: { groupByRowLabel: true }
    };

    function drawChart(){
      var jsonData = $.ajax({
          url: "/json/getData_ses_pausesv3.php?asesor="+thisasesor,
          dataType: "json",

          async: false
          }).responseText;

      var data = new google.visualization.DataTable(jsonData);

      chart.draw(data, options);

      }

    drawChart();

    setInterval(function(){ drawChart() }, 60000);

  }

  var status;
  function updateDetails(id,field,newVal){

      dialogLoader.dialog("open");

      $.ajax({
        url: '/json/formularios/asesores_update.php',
        type: 'POST',
        data: {id: id, field: field, newVal: newVal},
        dataType: 'json',
        success: function(array){

                      data=array;

                      if(data['status']==1){
                          showNoty('success','Cambio Guardado',4000);

                      }else{
                          showNoty('error',data['msg'],4000);
                      }

                    
                    dialogLoader.dialog("close");
                }


      });


  }

$(function(){

    function changeComida(){
      showLoader('Guardando Cambios');
      
      if($('#comida').prop('checked')){ var valor=1; }else{ var valor=0; }
      
      $.ajax({
        url: '/start/changeComida.php',
        type: 'POST',
        data: {asesor: thisasesor, status: valor},
        dataType: 'json',
        success: function(array){
          data=array;
          
          dialogLoad.dialog('close');
          
          if(data['status']==1){
            showNoty('success','Cambios Guardados',4000);
          }else{
            showNoty('error',data['msg'],4000);
            if($('#comida').prop('checked')){ 
              comidaFlag=false;
              comidaLabel='Sin Comida';
            }else{
              comidaFlag=true;
              comidaLabel='Con Comida';
            }
            
            $( "#comida" ).checkboxradio({
              icon: false,
              label: comidaLabel
            });
            
            $('#comida').prop('checked',comidaFlag);
          }
        },
        error: function(){
          dialogLoad.dialog('close');
          showNoty('error','Error de conexion',4000);
        }
      });
    }

    comidaFlag=false;
    comidaLabel='Sin Comida';
    
    if($('#comida').val()!=0){
      comidaFlag=true;
      comidaLabel='Con Comida';
    }
    
    $('#comida').prop('checked',comidaFlag);
    
    $('#comida').click(function(){
      if($('#comida').prop('checked')){
        comidaFlag=true;
        comidaLabel='Con Comida';
      }else{
        comidaFlag=false;
        comidaLabel='Sin Comida';
      }
      
      $( "#comida" ).checkboxradio({
        icon: false,
        label: comidaLabel
      });
      
      changeComida();
    });

    $( "#comida" ).checkboxradio({
      icon: false,
      label: comidaLabel
    });

    dialogLoader=$( "#dialog-loader" ).dialog({
      modal: true,
      autoOpen: false
    });

    progressbarloader=$('#progressbarloader').progressbar({
      value: false
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
        source: '/config/search_name.php',
        select: function(ev, ui){
          $('#asesorID').val(ui.item.id);
          asesorSelected_text = ui.item.label;
          $('#baja_name').text(asesorSelected_text);
          //console.log("id asesor seleccionado: "+asesorSelected);
        }
      });

    function getTiempos(){
    /*
        $.ajax({
            url: '/json/get_table_tiempos.php',
            type: 'GET',
            data: {esquema: esquema, asesor: thisasesor},
            dataType: 'json',
            success: function(array){
                          dataTiempos=array;
                          //C�digo para rellenar campos de tabla
                      }
        });

        setTimeout(function(){ getTiempos() }, 60000);*/
    }

    getTiempos();


      $( "#accordion" ).accordion({
        collapsible: true,
        heightStyle: "content",
        active: false
      });
      $( "#Ingreso, #Egreso, #Fecha Nacimiento" ).datepicker({
          dateFormat: "yyyy/mm/dd"
      });

      $('#visa, #pasaporte').datepicker({
        dateFormat: "yy-mm-dd"
      });

      $('#visa, #pasaporte').change(function(){
        sendRequestForm($(this).attr('index'),$(this).attr('col'),$(this).val());
      });

      var validation;

       function checkRegexp( o, regexp) {
        if ( !( regexp.test( o ) ) ) {
          return false;
        } else {
          return true;
        }
      }


      $('.tablesorter').tablesorter({
          theme: 'blue',
          headerTemplate: '{content}',
          widthFixed: false,
          widgets: [ 'zebra','editable' ],
          widgetOptions: {

             uitheme: 'jui',
              columns: [
                  "primary",
                  "secondary",
                  "tertiary"
                  ],
              columns_tfoot: false,
              columns_thead: true,
              editable_columns       : [3,4,5],
              editable_enterToAccept : true,
              editable_autoAccept    : true,
              editable_autoResort    : false,
              editable_validate      : function(txt, orig, columnIndex, $element){
                                          if(txt==""){
                                                  validation=true;
                                                  if(columnIndex==3 || columnIndex==4){
                                                      return "##########";
                                                  }else{
                                                      return "###@##.com";
                                                  }
                                          }else{
                                              if(columnIndex==3 || columnIndex==4){
                                                  var t = /(?:^|\s)([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])(?=\s|$)/.test(txt);
                                                  validation=t;
                                                  var mensaje="El formato telefonico debe ser de 10 numeros sin espacios";
                                              }else{
                                                  var t = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(txt);
                                                  validation=t;
                                                  var mensaje="El formato de correo electronico no es correcto";
                                              }

                                          }
                                          // only allow one word

                                          if(t==false){

                                              new noty({
                                                  text: mensaje,
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
                                        },
              editable_focused       : function(txt, columnIndex, $element) {
                $element.addClass('focused');
              },
              editable_blur          : function(txt, columnIndex, $element) {
                $element.removeClass('focused');
              },
              editable_selectAll     : function(txt, columnIndex, $element){
                return /^b/i.test(txt) && columnIndex === 0;
              },
              editable_wrapContent   : '<div>',
              editable_trimContent   : true,
              editable_noEdit        : 'no-edit',
              editable_editComplete  : 'editComplete'

          }
      }).children('tbody').on('editComplete', 'td', function(event, config){
        var $this = $(this),
          newContent = $this.text(),
          cellIndex = this.cellIndex,
          rowIndex = $this.closest('tr').attr('id'),
          col = $(this).attr('col');
          if(validation==true){
              updateDetails(rowIndex,col,newContent);
          }

        $this.addClass( 'editable_updated' );
        setTimeout(function(){
          $this.removeClass( 'editable_updated' );
        }, 500);


      });

      $('#viewAsesor').click(function(){
          $('#search').submit();
      });

      $('#viewAsesor').click(function(){
          $('#searchall').submit();
      });

      $('#dep').change(function(){
          var tmpval=$(this).val();
          $('#depall').val(tmpval);
      });

      $('#ventatodos').click(function(){
        window.location.assign('http://wfm.pricetravel.com.mx/mapa_venta');
      });

      //Asesores Selection
      $('#departamentoselection').change(function(){
        modSelAsesores();
        $('#asesorselection').val('');
      });

      function modSelAsesores(){
        $('.depselection').hide();
        $('.depsel_'+$('#departamentoselection').val()).show();
      }

      modSelAsesores();

  });

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
        type: 'GET',
        data: {id: id, field: field, newVal: newVal},
        dataType: 'html',
        success: function(data){
                      
                      text=data;
        
                      var status = text.match("status- (.*) -status");
                      var notif_msg = text.match("msg- (.*) -msg");
                      
                      if(status[1]=='OK'){
                          tipo_noti='success';
                          status=true;
                      }else{
                          tipo_noti='error';
                          status=false;
                      }
                      
                      new noty({
                          text: notif_msg[1],
                          type: tipo_noti,
                          timeout: 5000,
                          animation: {
                              open: {height: 'toggle'}, // jQuery animate function property object
                              close: {height: 'toggle'}, // jQuery animate function property object
                              easing: 'swing', // easing
                              speed: 500 // opening & closing animation speed
                          }
                    });
                    
                    dialogLoader.dialog("close");
                }
                
                
      });
      
      
  }

$(function(){

    dialogLoader=$( "#dialog-loader" ).dialog({
      modal: true,
      autoOpen: false
    });

    progressbarloader=$('#progressbarloader').progressbar({
      value: false
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
                          //Código para rellenar campos de tabla
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


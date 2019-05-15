function loginPop(variable) {
      if(variable=='ok'){
        var page="/common/login.php?modal=on";
        var $dialog = $('#login')
        .html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
        .dialog({
          title: "Login",
          autoOpen: false,
          dialogClass: 'dialog_fixed,ui-widget-header',
          modal: true,
          height: 500,
          minWidth: 600,
          minHeight: 400,
          draggable:true,
          /*close: function () { $(this).remove(); },*/
          buttons: { "Ok": function () {         $(this).dialog("close"); } }
        });
        $dialog.dialog('open');
      }
  }

  function sendRequest(variables){

    showLoader('Guardando Registro');

    $.ajax({
      url: 'query.php',
      type: 'POST',
      data: variables,
      dataType: 'html',
      success: function(data){
        text= data;

        var status = text.match("status- (.*) -status");
        var startlogin='no';
        var notif_msg = text.match("msg- (.*) -msg");

        dialogLoad.dialog('close');

        if(status[1]=='OK'){
            showNoty('success',notif_msg[1],3000);
            $('#error').text("");
            $('.input').val('');
            $('#date-in').periodpicker('clear');
            $( '#regs' ).attr( 'src', function ( i, val ) { return val; });
            start_fields();
        }else{
            if(status[1]=='DISC'){
              new noty({
                  text: notif_msg[1],
                  type: 'error',
                  timeout: 10000,
                  animation: {
                      open: {height: 'toggle'}, // jQuery animate function property object
                      close: {height: 'toggle'}, // jQuery animate function property object
                      easing: 'swing', // easing
                      speed: 500 // opening & closing animation speed
                  },
                  callback: {
                      onShow: function(){
                          loginPop('ok');
                      }
                      }
              });
            }else{
                showNoty('error',notif_msg[1],3000);
                $('#error').text(urlok+"?"+variables);
              }
        }

        $('#submit_form').prop('disabled',false);
      },
      error: function(){
        dialogLoad.dialog('close');
        $('#submit_form').prop('disabled',false);
        showNoty('error','Error de conexión. Intenta nuevamente',3000);
      }
    });


  }

$(function(){

  $('#date-in').periodpicker({
    norange: true,
    //inline: true,
    cells: [1,2],
    resizeButton: false,
    fullsizeButton: false,
    fullsizeOnDblClick: false,
    //withoutBottomPanel: true,
    yearsLine: false,

    maxDate: maxDate,

    timepicker: true,
    formatDateTime: 'YYYY-MM-DD HH:mm',
    timepickerOptions: {
      hours: true,
      minutes: true,
      seconds: false,
      ampm: true
    },

    formatDecoreDateTimeWithYear: 'YYYY-MM-DD HH:mm:ss',

    todayButton: true,
    onTodayButtonClick: function () {
        this.month = Datemonth;
        this.year = Dateyear;
        this.day = Dateday;
        this.regenerate();

        $('#date-in').val(todayDate);
        $('#date-in').periodpicker('change');
        return false;
    }

	});

  $('#date-in').periodpicker('clear');



    $("#login").hide();



});

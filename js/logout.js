function showLoader(title, pos){

  position = typeof pos !== 'undefined' ? pos : 0;

  dialogLoad.dialog("option", "title", title);
  if(position==0){
    dialogLoad.dialog('open');
  }else{
    dialogLoad.dialog('option', 'position', position).dialog('open');
  }
}

function showNoty(type, msg, time){
  switch(type){
    case 'error':
      open='animated headShake';
      close='animated bounceOutLeft';
      break;
    case 'success':
      open='animated headShake';
      close='animated bounceOutLeft';
      break;
  }
  new noty({
      text: msg,
      type: type,
      timeout: time,
      layout: 'topCenter',
      animation: {
          open: open, // jQuery animate function property object
          close: close, // jQuery animate function property object
          easing: 'swing', // easing
          speed: 500 // opening & closing animation speed
      }
  });
}

$(function(){

   //$('#full-loader').hide(); // Only hides the div in case you want to reuse it
  $('.fullloader').remove(); // Removes the div

  $('#logout').click(function(){
    $('.dynamic_data').remove();
    showLoader('Cerrar Sesion');
    getHorarioTm();
  });

  function reloadSes(){
    $.ajax({
      url: '/modules/refresh_session.php',
      type: 'POST',
      dataType: 'json',
      success: function(array){
        sesData=array;

        if(sesData['status']==0){
          showNoty('error','Sesión expirada, por favor vuelve a iniciar sesión',20000);
        }

        setTimeout(function(){
          reloadSes();
        },120000);
      },
    error: function(){
      setTimeout(function(){
        reloadSes();
      },30000);
    }

    });
  }

  setTimeout(function(){
    reloadSes();
  },120000);

  dialogLoad=$( "#dialog-load" ).dialog({
    modal: true,
    autoOpen: false
  });

  progressbarload=$('#progressbarload').progressbar({
    value: false
  });

  confirmLogout=$( "#dialog-message" ).dialog({
    modal: true,
    autoOpen: false,
    width: 700,
    height: 500,
    buttons: [{
                text: "Cancel",
                click: function() {
                  $( this ).dialog( "close" );
                }
            }],
    close: function() {
                $('#check_horario').prop('checked', false);
                $('#ok_logout').hide();
            }
  });

  okLogout=$( "#dialog-message-out" ).dialog({
    modal: true,
    autoOpen: false,
    buttons: [{
                text: "OK",
                click: function() {
                          $( this ).dialog( "close" );
                        }
            }],
    close: function() {
                window.location.replace('/common/login.php');
              }
   });

  function getHorarioTm(){

    $.ajax({
      url: '/json/getHorarios.php',
      type: "POST",
            data: {asesor: asesor, fecha: fechahorarios},
            dataType: "json",
            success: function(array) {
              data=array;

              if(data['status']!=0){
                var table=$('#nd_horario');

                for(i=0;i<=1;i++){
                  var flag = (typeof data['fecha'+i] === 'undefined') ? false : true;
                  if(flag){
                    table.find('.title').append("<th class='dynamic_data'>"+data['fecha'+i]['fecha']+"</th>");
                    table.find('.pair').append("<th class='dynamic_data'>"+data['fecha'+i]['horario']+"</th>");
                  }
                }
              }else{
                $('.check_horario').hide();
                $('#ok_logout').show();
              }
                dialogLoad.dialog('close');
                confirmLogout.dialog('open');

            },
            error: function(){
              dialogLoad.dialog('close');
              alert('Error al recibir informacion de Horarios. Intentalo nuevamente o revisa con GTR');

            }
    });
  }

  function logOutSet(){
    showLoader('Cerrar Sesion');
    $.ajax({
      url: '/json/logout.php',
      type: "POST",
            data: {asesor: asesor, fecha: today, horario: $('#nd_horario').html()},
            dataType: "html",
            success: function(data) {
              dialogLoad.dialog('close');
              if(data=='Done'){
                okLogout.dialog('open');
              }else{
                alert('Error, intenta de nuevo o acercate con GTR');
              }
            },
            error: function(){
              dialogLoad.dialog('close');
              alert('Error al enviar info, intenta de nuevo o acercate con GTR');

            }
    });
  }

  $('#check_horario').change(function(){
		var flag = $(this).prop('checked');
		if(flag){
			$('#ok_logout').show();
		}else{
			$('#ok_logout').hide();
		}
	});

	$('#ok_logout').click(function(){
		confirmLogout.dialog('close');
		logOutSet();
	});

  $('#ok_logout').hide();
});

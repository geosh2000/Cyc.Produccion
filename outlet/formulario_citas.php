<?php

include_once('../modules/modules.php');

initSettings::start(true,'citas_outlet');
initSettings::printTitle('Ingreso Citas Outlet');

timeAndRegion::setRegion('Cun');

?>
<style>
  .form-style-5{
      max-width: 500px;
      padding: 10px 20px;
      background: rgba(0, 158, 210, 0.35);
      margin: 10px auto;
      padding: 20px;
      background: rgba(0, 158, 210, 0.35);
      border-radius: 8px;
      font-family: Georgia, "Times New Roman", Times, serif;
  }
  .form-style-5 fieldset{
      border: none;
  }
  .form-style-5 legend {
      font-size: 1.4em;
      margin-bottom: 10px;
  }
  .form-style-5 label {
      display: block;
      margin-bottom: 8px;
  }
  .form-style-5 input[type="text"],
  .form-style-5 input[type="date"],
  .form-style-5 input[type="datetime"],
  .form-style-5 input[type="email"],
  .form-style-5 input[type="number"],
  .form-style-5 input[type="search"],
  .form-style-5 input[type="time"],
  .form-style-5 input[type="url"],
  .form-style-5 textarea,
  .form-style-5 select {
      font-family: Georgia, "Times New Roman", Times, serif;
      background: rgba(255,255,255,.1);
      border: none;
      border-radius: 4px;
      font-size: 16px;
      margin: 0;
      outline: 0;
      padding: 7px;
      width: 100%;
      box-sizing: border-box; 
      -webkit-box-sizing: border-box;
      -moz-box-sizing: border-box; 
      background-color: #f7ffff;
      color:#8a97a0;
      -webkit-box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
      box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
      margin-bottom: 30px;
      
  }
  .form-style-5 input[type="text"]:focus,
  .form-style-5 input[type="date"]:focus,
  .form-style-5 input[type="datetime"]:focus,
  .form-style-5 input[type="email"]:focus,
  .form-style-5 input[type="number"]:focus,
  .form-style-5 input[type="search"]:focus,
  .form-style-5 input[type="time"]:focus,
  .form-style-5 input[type="url"]:focus,
  .form-style-5 textarea:focus,
  .form-style-5 select:focus{
      background: #d2d9dd;
  }
  .form-style-5 select{
      -webkit-appearance: menulist-button;
      height:35px;
  }
  .form-style-5 .number {
      background: #5596e6;
      color: #fff;
      height: 30px;
      width: 30px;
      display: inline-block;
      font-size: 0.8em;
      margin-right: 4px;
      line-height: 30px;
      text-align: center;
      text-shadow: 0 1px 0 rgba(255,255,255,0.2);
      border-radius: 15px 15px 15px 0px;
  }

  .form-style-5 input[type="submit"],
  .form-style-5 input[type="button"]
  {
      position: relative;
      display: block;
      padding: 19px 39px 18px 39px;
      color: #FFF;
      margin: 0 auto;
      background: #44bfe6;
      font-size: 18px;
      text-align: center;
      font-style: normal;
      width: 100%;
      border: 1px solid #659bca;
      border-width: 1px 1px 3px;
      margin-bottom: 10px;
      height: initial;
  }
  .form-style-5 input[type="submit"]:hover,
  .form-style-5 input[type="button"]:hover
  {
      background: #008cba;
  }
  
  legend {
      display: block;
      width: 100%;
      padding: 0;
      margin-bottom: 20px;
      font-size: 21px;
      line-height: inherit;
      color: #717171;
      border: 0;
      border-bottom: 1px solid #e5e5e5;
  }
  
  .switch-field {
    font-family: "Lucida Grande", Tahoma, Verdana, sans-serif;
    padding: 0;
    padding-bottom: 13;
      overflow: hidden;
  }

  .switch-title {
    margin-bottom: 6px;
  }

  .switch-field input {
      position: absolute !important;
      clip: rect(0, 0, 0, 0);
      height: 1px;
      width: 1px;
      border: 0;
      overflow: hidden;
  }

  .switch-field label {
    float: left;
  }

  .switch-field label {
    display: inline-block;
    width: 60px;
    background-color: #e4e4e4;
    color: rgba(0, 0, 0, 0.6);
    font-size: 14px;
    font-weight: normal;
    text-align: center;
    text-shadow: none;
    padding: 6px 14px;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
    -webkit-transition: all 0.1s ease-in-out;
    -moz-transition:    all 0.1s ease-in-out;
    -ms-transition:     all 0.1s ease-in-out;
    -o-transition:      all 0.1s ease-in-out;
    transition:         all 0.1s ease-in-out;
  }

  .switch-field label:hover {
      cursor: pointer;
  }

  .switch-field input:checked + label {
    background-color: #44bfe6;
    -webkit-box-shadow: none;
    box-shadow: none;
  }

  .switch-field label:first-of-type {
    border-radius: 4px 0 0 4px;
  }

  .switch-field label:last-of-type {
    border-radius: 0 4px 4px 0;
  }
  
</style>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.timepicker.min.css">
<script src="/js/periodpicker/build/jquery.timepicker.min.js"></script>

<script>
$(function(){
 $('#inicio').periodpicker({
    end: '#fin',
    lang: 'en',
    minDate: '2017-05-11',
    yearsPeriod: [2017,2019],
    yearSizeInPixels: 100,
    startMonth: 5,
    formatDate: 'YYYY-MM-DD',
    animation: true
  });
  
  $('#cita').periodpicker({
    norange: true,
    lang: 'en',
    cells: [1,1],
    yearsPeriod: [2017,2017],
    animation: true,
    minDate: '2017-05-11',
    maxDate: '2017-05-15',
    startMonth: 5,
    timepicker: true,
    formatDateTime: 'YYYY-MM-DD HH:mm',
    timepickerOptions: {
      hours: true,
      minutes: true,
      seconds: false,
      ampm: true,
      defaultTime:'10:00'
    },

    formatDecoreDateTimeWithYear: 'YYYY-MM-DD HH:mm:ss',
  });
  
  $('.siviaja').hide();
  
  $('#v_si').change(function(){
    if($(this).prop('checked')){
      $('.siviaja').show();
      v_siReq(true);
    }
  });
  
  $('#v_no').change(function(){
    if($(this).prop('checked')){
      $('.siviaja').hide();
      v_siReq(false);
    }
  });
  
  function v_siReq(flag){
      $('.siviaja input').each(function(){
        $(this).prop('required',flag);
      });
  }
  
  $('#send').click(function(){
    //$('#results').empty();
  
    inputResults=$('#formulario').find('input');
    
    results=[];
    arrayData="";
    
    var flag=true;
    
    inputResults.each(function(){
      
      if($(this).prop('required')){
        if($(this).val()==""){
            flag=false;
            $(this).addClass( "ui-state-highlight" );
        }else{
            $(this).removeClass( "ui-state-highlight", 1500 );
        }
      }
    
      if($(this).attr('name')=='viaja'){
        if($(this).prop('checked')){
          //$('#results').append("<p>"+$(this).attr('name')+": "+$(this).val()+"</p>");
          results[$(this).attr('name')]=$(this).val();
          arrayData=arrayData+$(this).attr('name')+"="+$(this).val()+"&";
        }
      }else{
        //$('#results').append("<p>"+$(this).attr('name')+": "+$(this).val()+"</p>");
        results[$(this).attr('name')]=$(this).val();
        arrayData=arrayData+$(this).attr('name')+"="+$(this).val()+"&";
      }
    });
    
    if(flag){
        sendInfo(arrayData);
      }else{
        showNoty('error', 'Existen campos que requieren ser llenados',4000);
      }
  });
  
  function sendInfo(vars){
    showLoader('Guardando Informacion');
    
    $.ajax({
      url: 'save_cita.php',
      data: vars,
      type: 'POST',
      dataType: 'json',
      success: function(array){
        data=array;
        
        dialogLoad.dialog('close');
        
        if(data['status']==1){
          
          $('#folio').text(data['id']);
          showFolio.dialog('open');
        
          inputResults.each(function(){
            
            if($(this).attr('name')=='viaja'){
              if($(this).attr('id')=='v_no'){$(this).prop('checked',true);}else{$(this).prop('checked',false);}
            }else{
              $(this).val('');
            }
          });
          v_siReq(false);
          
          $('#send').val('Guardar');
          
          $('.siviaja').hide();
          
          $('#cita').val('2017-05-11 10:00');
          $('#cita').periodpicker('change');
          $('#inicio, #cita').periodpicker('clear');
          
        }else{
          showNoty('error', data['msg'],4000);
        }
        
      
      },
      error: function( jqXHR, textStatus, errorThrown ) {
                dialogLoad.dialog('close');
                if (jqXHR.status === 0) {

                  showNoty('error','Not connect: Verify Network.',4000);

                } else if (jqXHR.status == 404) {

                  showNoty('error','Requested page not found [404]',4000);

                } else if (jqXHR.status == 500) {

                  showNoty('error','Internal Server Error [500].',4000);

                } else if (textStatus === 'parsererror') {

                  showNoty('error','Requested JSON parse failed.',4000);

                } else if (textStatus === 'timeout') {

                  showNoty('error','Time out error.',4000);

                } else if (textStatus === 'abort') {

                  showNoty('error','Ajax request aborted.',4000);

                } else {

                  showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);

                }

              }
      
    });
  }
  
  showFolio=$( "#dialog-message" ).dialog({
    modal: true,
    autoOpen: false,
    buttons: {
      Ok: function() {
        $( this ).dialog( "close" );
      }
    }
  });
  
});
</script>

<div id='formulario' class="form-style-5">
    <fieldset>
      <legend><span class="number">1</span> Informacion del Cliente</legend>
      <input type="text" id='nombre' name="nombre" placeholder="Nombre *" required>
      <input type="text" id='localizador' name="localizador" placeholder="Localizador *" required>
      <input type="email" id='correo' name="correo" placeholder="Correo">
      <input type="text" id='telefono' name="telefono" placeholder="Telefono">
    </fieldset>
    <fieldset>
      <legend><span class="number">2</span> Informacion de Viaje</legend>
      <div class="switch-field">
        <div class="switch-title">Cliente Viaja?</div>
        <input type="radio" id="v_no" name="viaja" value="0" checked/>
        <label for="v_no">No</label>
        <input type="radio" id="v_si" name="viaja" value="1" />
        <label for="v_si">Si</label>
      </div>
      <div class='siviaja'>
        <input type="text" id='destino' name="destino" placeholder="Destino *" >
        <input type="number" id='pax' name="pax" placeholder="pax *" >
        <input type="text" id='servicio' name="servicio" placeholder="Servicio (Hotel,Vuelo,Paquete,Otros)" >
        <label for="inicio">Fechas de Viaje</label>
        <input type="text" id='inicio' name="inicio"><input type="text" id='fin' name="fin"><br>
        <br><label for="cita">Fecha y Hora Aproximada de visita</label>
        <input type="text" id='cita' name="cita"><br><br>
      </div>
    </fieldset>
    <input type="submit" id='send' value="Guardar" />
  
</div>
<br>
<div id="dialog-message" title="Folio Guardado">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    El registro se guardo correctamente.
  </p>
  <p>
    El numero de foio del cliente es <b id='folio'></b>.
  </p>
</div>
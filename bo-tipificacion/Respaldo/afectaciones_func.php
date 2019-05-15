<?php

include_once('../modules/modules.php');

/* Modulo Standard para Formularios */

//Información para personalizar

$init_Credential='asesor_formularios_bo'; //Permisos para formulario
$init_title='Afectaciones - Funciones';   //Titulo Formulario
$init_table='formulario_BO_Afectaciones'; //Tabla de opciones
$init_query='query_afectaciones.php';     //Archivo para subir resultados
$init_saveTo='bo_afectaciones_funciones';     //Tabla para guardar resultados

initSettings::start(true,$init_Credential);
initSettings::printTitle($init_title);

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$query="SELECT * FROM $init_table WHERE activo=1 ORDER BY parent, pos";
if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  
  while($fila=$result->fetch_array()){
    for($i=0;$i<$result->field_count;$i++){
      if($fila[6] == NULL){
        $form[$fila[0]][$fields[$i]->name]=utf8_encode($fila[$i]);
      }else{
        $form[$fila[6]]['sub'][$fila[0]][$fields[$i]->name]=utf8_encode($fila[$i]);
      }
    }
  }
  
}

$connectdb->close();

?>
<script>
 $(function(){
 
 $('.alert').hide();
  
  $('#send').click(function(){
    
    flag=true;
    dataToSend='asesor:<?php echo $_SESSION['asesor_id'];?>|';
    
    $.each($('.entrada'), function(){
      
      if($(this).prop('required')){
        if($(this).val()=='' || $(this).val()==' '){
          flag=false;
          $(this).prev('.alert').show();
        }else{
          $(this).prev('.alert').hide();
        }
      }
      
      dataToSend+=$(this).attr('id')+":"+$(this).val()+"|";
      
    });
    
    if(flag){
      sendData(dataToSend);
    }
  });
  
  function sendData(datos){
      
      showLoader('Guardando Informacion');
      
      $.ajax({
        url: '<?php echo $init_query; ?>',
        type: 'POST',
        data: {tabla: '<?php echo $init_saveTo; ?>', datos: datos},
        dataType: 'json',
        success: function(array){
          data=array;
          
          dialogLoad.dialog('close');
                    
          if(data['status']==1){
            showNoty('success','Guardado correctamente',4000);
            $('.entrada').val('');
          }else{
            showNoty('error',data['msg'],4000);
          }
          
          $('#regs').text(data['regs']);
          $('#last').text(data['lu']);
        },
        error: function(){
          dialogLoad.dialog('close');
          showNoty('error','Error en conexion',4000);
        }
        
      });
  }
 
 });
</script>
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
<br>
<div style='width: 600px; background: #a6ddef; color: #717171; margin: auto; font-size: 20px; text-align: center; padding-top: 1px; padding-bottom: 1px;'>
  <p style='font-size: 25px'>Registro de Afectaciones (Funciones)</p><p>Registros del dia: <span id='regs'></span> || Ultimo Registro: <span id='last'></span></p>
</div> 
<div id='formulario' class="form-style-5">
    <fieldset>
      <legend><span class="number">1</span> Informacion de la Afectacion</legend>
      <?php
        foreach($form as $id => $info){
          
          $valor='';

          if($info['prop']=='required'){
            $placeholder=$info['text']." *";
            $alert="<span class='alert' style='color: red'> Este campo es obligatorio </span>";
          }else{
            $placeholder=$info['text'];
            $alert="";
          }

          if($info['type']!='select'){
            echo "<label for='".$info['input_id']."'>$placeholder</label>$alert<input class='entrada' type=".$info['type']." placeholder='$placeholder' name='".$info['input_id']."' id='".$info['input_id']."' value='$valor' ".$info['prop']."></td>";
          }else{
            echo "<label for='".$info['input_id']."'>$placeholder</label>$alert<select class='entrada' placeholder='$placeholder' name='".$info['input_id']."' id='".$info['input_id']."' value='$valor' ".$info['prop'].">";
            if(isset($info['sub'])){
              foreach($info['sub'] as $id2 => $info2){
                
                if($info2['value']==NULL){
                  $valor=$info2['id'];
                }else{
                  if($info2['value']==' '){
                    $valor='';
                  }else{
                    $valor=$info2['value'];
                  }
                }
                echo "<option value='$valor'>".$info2['text']."</option>";
              }
            }
            echo "</select>";
          }
         
        }
      ?>
    </fieldset>
    <input type="submit" id='send' value="Guardar" />
</div>
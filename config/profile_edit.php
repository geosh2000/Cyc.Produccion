<?php
include_once('../modules/modules.php');

initSettings::start(true,'profile_config');
initSettings::printTitle('Edit Profiles');

$tbody="<td>Profile</td><td><select id='profile' required><option value=''>Selecciona...</option>";
$query="SELECT * FROM profilesDB ORDER BY profile_name";
if($result=Queries::query($query)){
  while($fila=$result->fetch_array()){
    $tbody.="<option value='".$fila['id']."'>".$fila['profile_name']."</option>";
  }
}

$tbody.="</select></td>";

Filters::showFilterNOFORM('search', 'Consultar', $tbody);

?>
<script>
$(function(){
  $('#save').hide();

  function getProfiles(){
    showLoader('Obteniendo Información');

    $.ajax({
      url: 'getPermissions.php',
      type: 'POST',
      data: {id: $('#profile').val()},
      dataType: 'json',
      success: function(array){
          data=array;

          $('#permissions').empty();

          var checked='';

          $.each(data,function(i,val){
              if(i!='profile_name'){

                if(val==1){
                  checked='checked';
                }else{
                  checked='';
                }

                $('#permissions').append("<p><input id='"+i+"' type='checkbox' class='profilecheck' "+checked+"> "+i+"</p>");

              }

            $('#save').show();
          });

          dialogLoad.dialog('close');
        }

    });
  }

  $('#search').click(function(){
    profileID=$('#profile').val();
    $('#save').hide();
    getProfiles();
    $('#debug').empty();
  });

  $('#save').click(function(){
    showLoader('Guardando cambios');
    arr_result={};
    arr_result['id']=profileID;
    $('.profilecheck').each(function(){
      arr_result[$(this).attr('id')]=$(this).prop('checked');
    });
    saveProfile(arr_result);
  });

  function saveProfile(datos){
    $.ajax({
      url: 'saveProfile.php',
      type: 'POST',
      data: datos,
      dataType: 'json',
      success: function(array){
          data=array;

          $('#permissions').empty();

          getProfiles();

          dialogLoad.dialog('close');

          if(data['status']==1){
            showNoty('success','Perfil Guardado',4000);
          }else{
            showNoty('error',data['msg'],4000);
            $('#debug').text(data['msg']);
          }


    },
    error: function(){
      dialogLoad.dialog('close');
      showNoty('error','Error de conexión',4000);
    }
  });

  }

});
</script>
<br>
<div id='permissions' style='width:50%; margin: auto; text-align: left;'>

</div>
<div id='savebut' style='width:50%; margin: auto; text-align: left;'>
<button id='save' class='button button_green_w'>Guardar</button>
</div>
<div id='debug'></div>

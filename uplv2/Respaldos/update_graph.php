<?php

include_once('../modules/modules.php');

initSettings::start(true);
initSettings::printTitle('Update Graficas');

?>
<script>
$(function(){
  calls1 = $.ajax({
    url: 'query_updateGraph.php',
    data: {q: 1},
    type: 'POST',
    dataType: 'json',
    success: function(array){
      data=array;
      
      if(data['status']==1){
        $('#calls1').text('Done!');
      }else{
        $('#calls1').text('Error');
      }
    },
    error: function(){
      $('#calls1').text('Error');
    }
  });
  
  calls2 = $.ajax({
    url: 'query_updateGraph.php',
    data: {q: 2},
    type: 'POST',
    dataType: 'json',
    success: function(array){
      data=array;
      
      if(data['status']==1){
        $('#calls2').text('Done!');
      }else{
        $('#calls2').text('Error');
      }
    },
    error: function(){
      $('#calls2').text('Error');
    }
  });
  
  monto = $.ajax({
    url: 'query_updateGraph.php',
    data: {q: 3},
    type: 'POST',
    dataType: 'json',
    success: function(array){
      data=array;
      
      if(data['status']==1){
        $('#monto').text('Done!');
      }else{
        $('#monto').text('Error');
      }
    },
    error: function(){
      $('#monto').text('Error');
    }
  });
  
  dias = $.ajax({
    url: 'query_updateGraph.php',
    data: {q: 4},
    type: 'POST',
    dataType: 'json',
    success: function(array){
      data=array;
      
      if(data['status']==1){
        $('#dias').text('Done!');
      }else{
        $('#dias').text('Error');
      }
    },
    error: function(){
      $('#dias').text('Error');
    }
  });
  
 $.when(calls1).done(
    
    function(){
      $.when(calls2).done(
        function(){
          $.when(monto).done(
        function(){
          dias;
        }
      );
        }
      );
    }
  );
  
  
});
</script>
<p>Llamadas 1: <span id='calls1'>obteniendo informacion...</span></p>
<p>Llamadas 2: <span id='calls2'>obteniendo informacion...</span></p>
<p>Montos y Locs: <span id='monto'>obteniendo informacion...</span></p>
<p>Dias Habiles: <span id='dias'>obteniendo informacion...</span></p>

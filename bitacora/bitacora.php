<?php

include_once("../modules/modules.php");

initSettings::start(true,'monitor_y_lw');
initSettings::printTitle('Bitacoras');

timeAndRegion::setRegion('Cun');

$tbody="<td><input type='text' value='".date('Y-m-d')."' id='fecha' name='fecha'></td>";

Filters::showFilterNOFORM('search','Consultar',$tbody);

$flag=true;

$query="SELECT id, Departamento FROM PCRCs WHERE id IN (3,35,7,8,9,4) ORDER BY Departamento";
if($result=Queries::query($query)){
    while($fila=$result->fetch_assoc()){
        if($fila['Departamento']=='Ventas MP'){
            $active="class='active'";
        }else{
            $active="class=''";
        }
        
        $navs.="<li role='presentation' $active><a href='#' tab='".$fila['id']."'>".$fila['Departamento']."</a></li>\n";
        
    }
}

$query="SELECT Hora_int, Hora_group FROM HoraGroup_Table ORDER BY Hora_int";
if($result=Queries::query($query)){
    while($fila=$result->fetch_assoc()){
        $table[]="<tr id='h_".$fila['Hora_int']."'><td class='hora' tipo='hora'>".$fila['Hora_group']."</td>
                    <td class='sla_meta text-right'></td>
                    <td class='sla text-right'>23%</td>
                    <td class='forecast text-center'></td>
                    <td class='llamadas text-center'></td>
                    <td class='forecast_cumplimiento text-right'></td>
                    <td class='programados text-center'></td>
                    <td class='sentados text-center'></td>
                    <td class='aht_meta text-right'></td>
                    <td class='aht text-right'></td>
                    <td class='abandon_meta text-right'></td>
                    <td class='abandon text-right'></td>
                    <td class='acc1' h='".$fila['Hora_int']."' level='1' style='max-width:200px;'></td>
                    <td class='acc2' h='".$fila['Hora_int']."' level='2' style='max-width:200px;'></td>
                    <td class='acc3' h='".$fila['Hora_int']."' level='3' style='max-width:200px;'></td></tr>\n";
    }
}

?>

<!-- Latest compiled and minified CSS -->
<link rel='stylesheet' href='/styles/bootstrap/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>

<!-- Optional theme -->
<link rel='stylesheet' href='/styles/bootstrap/css/bootstrap-theme.min.css' integrity='sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp' crossorigin='anonymous'>

<!-- Latest compiled and minified JavaScript -->
<script src='/styles/bootstrap/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>

<script>
$(function(){
    
    refresh=300;
    t=refresh;
    tab=35;
    flag=true;
    
    getData(tab);
    
    setInterval(function(){
        if(flag){
            $('#timer').text(t);
            t=t-1;
            if(t==0){
                getData(tab);
                t=refresh;
            }
        }
    },1000);
    
    $('.nav li a').click(function(){
        $('.nav li').removeClass('active');
        $(this).parent().addClass('active'); 
        
        //erease contents
        $('#bitacora tbody tr td').each(function(){
            if($(this).attr('tipo')!='hora'){
              $(this).text('').removeClass('danger').removeClass('warning').removeClass('success');  
            }
        });
        
        tab=$(this).attr('tab');
        
        //getData
        getData($(this).attr('tab'));
    });
    
    $('#search').click(function(){
        getData(tab);
    })
    
    $('#fecha').periodpicker({
        norange: true,
        dateFormat: 'YYYY-MM-DD'
    });
    
    $(document).on('click','.addAct',function(){
        element=$(this).closest('td');
        $('#new_h').val($(this).closest('td').attr('h'));
        $('#new_l').val($(this).closest('td').attr('level'));
        $('#new_comment, #new_accion').val('');
        $('#details').text("");
    });
    
    $(document).on('click','.editAct',function(){
        element=$(this).closest('td');
        showLoader('Cargando..',{ my: "left top", at: "left bottom", of: element });
        
        l=$(this).closest('td').attr('level');
        h=$(this).closest('td').attr('h');
        
        console.log("l: "+l+" || h: "+h);
        
        $.ajax({
            url: 'getActs.php',
            type: 'POST',
            data: {h: l, l: h, fecha: $('#fecha').val(), skill: tab},
            dataType: 'json',
            success: function(array){
                data=array;
                
                $('#new_accion').val(data['act']);
                $('#new_comment').val(data['comments']);
                $('#details').text("Ultima Edicion: "+data['asesor']);
                
                dialogLoad.dialog('close');
                $('#addAccion').modal();
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
        
        
    });
    
    function getData(skill){
        showLoader('Obteniendo Data');
        
        $.ajax({
            url: 'getData.php',
            type: 'POST',
            data: {skill: skill, fecha: $('#fecha').val()},
            dataType: 'json',
            success: function(array){
                data=array;
                
                dialogLoad.dialog('close');
                
                if(data['status']==1){
                    $.each(data['info'],function(i,val){
                        
                        $('#h_'+i+' .sla_meta').text(data['metas']['sla']+' %');
                        $('#h_'+i+' .sla').text(val['sla']+' %').addClass(data['class'][i]['sla']);
                        
                        $('#h_'+i+' .llamadas').text(val['llamadas']);
                        $('#h_'+i+' .forecast').text(data['forecast'][i]['calls']);
                        $('#h_'+i+' .forecast_cumplimiento').text(data['forecast'][i]['prec']+' %').addClass(data['class'][i]['prec']);
                        
                        $('#h_'+i+' .aht_meta').text(data['metas']['aht']+' seg.');
                        $('#h_'+i+' .aht').text(val['aht']+' seg.').addClass(data['class'][i]['aht']);
                        
                        $('#h_'+i+' .abandon_meta').text(data['metas']['abandon']+' %');
                        $('#h_'+i+' .abandon').text(val['abandon']+' %').addClass(data['class'][i]['abandon']);
                        
                        $('#h_'+i+' .programados').text(data['programados'][i]);
                        $('#h_'+i+' .sentados').text(data['sentados'][i]);
                    });
                    
                    $('.acc1, .acc2, .acc3').html("<p class='text-right'><button class='btn btn-info btn-xs addAct' data-toggle='modal' data-target='#addAccion'>Agregar</button></p>");
                    
                    $.each(data['acciones'],function(i,val){
                       $.each(val, function(x,valor){
                          $('#h_'+i+' .acc'+x).html(valor+"<p class='text-right'><button class='btn btn-warning btn-xs editAct' level='"+x+"' h='"+i+"'>Editar</button></p>");
                       }); 
                    });
                    
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
    
    function saveAct(){
        showLoader('Guardando...',{ my: "left top", at: "left bottom", of: element });
        
        $.ajax({
            url: 'saveAct.php',
            type: 'POST',
            data: {h: $('#new_h').val(), l: $('#new_l').val(), fecha: $('#fecha').val(), skill: tab, accion: $('#new_accion').val(), comments: $('#new_comment').val()},
            dataType: 'json',
            success: function(array){
                data=array;
                
                element.html($('#new_comment').val()+"<p class='text-right'><button class='btn btn-warning btn-xs editAct'>Editar</button></p>");
                
                $('#addAccion').modal('hide');
                
                dialogLoad.dialog('close');
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
    
    $('#saveAct').click(function(){
        saveAct();
    })
});
</script>


<br>

<div style='width: 90%; margin: auto' class='panel panel-info'>
    <div class="panel-heading"> 
        <ul class="nav nav-tabs">
          <?php echo $navs; ?>
        </ul>
    </div>
    
    <div class="panel-body">
       <p>Refresh in <span id='timer'></span> seg.</p>
        <br>
        <table id='bitacora' class='table table-striped table-bordered table-hover'>
            <thead>
                <tr>
                    <th class='text-center'>Hora</th>
                    <th class='text-center'>Meta SLA</th>
                    <th class='text-center'>SLA Actual</th>
                    <th class='text-center'>Forecast</th>
                    <th class='text-center'>Llamadas</th>
                    <th class='text-center'>Precisión</th>
                    <th class='text-center'>RACs Programados</th>
                    <th class='text-center'>RACs Sentados</th>
                    <th class='text-center'>AHT Meta</th>
                    <th class='text-center'>AHT Actual</th>
                    <th class='text-center'>Abandon Meta</th>
                    <th class='text-center'>Abandon</th>
                    <th class='text-center'>Acciones 1</th>
                    <th class='text-center'>Acciones 2</th>
                    <th class='text-center'>Acciones 3</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($table as $index => $info){
                        echo "$info";
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
  <div class="modal fade" id="addAccion" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Acción</h4>
        </div>
        <div class="modal-body">
          <p>Acción para Bitácoras</p>
            <div>
              <div class="form-group">
                <label for="new_accion">Acción</label>
                <select class="form-control" id="new_accion">
                    <option value=''>Selecciona</option>
                    <?php
                        $query="SELECT * FROM comeycom_WFM.bitacora_acciones ORDER BY indice";
                        if($result=Queries::query($query)){
                            while($fila=$result->fetch_assoc()){
                                echo "<option value='".$fila['id']."'>".utf8_encode($fila['Actividad'])."</option>\n";
                            }
                        }
                    ?>
                  </select>
              </div>
              <div class="form-group">
                <label for="new_comment">Comentarios</label>
                <input type="text" class="form-control" id="new_comment" placeholder="Acciones tomadas...">
              </div>
                <input type="hidden" class="form-control" id="new_h" placeholder="Acciones tomadas...">
                <input type="hidden" class="form-control" id="new_l" placeholder="Acciones tomadas...">
                
            
            </div>
            <p id="details" class="text-right"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button><button class="btn btn-success" id='saveAct'>Guardar</button>
        </div>
      </div>
      
    </div>
  </div>



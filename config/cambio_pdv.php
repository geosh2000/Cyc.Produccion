<?php

include_once('../modules/modules.php');

initSettings::start(true,'config');
initSettings::printTitle('Cambio de PDV');
timeAndRegion::setRegion('Cun');

$fecha=$_POST['inicio'];

$tbody="<td><input type='text' id='inicio' name='inicio' value='".$_POST['inicio']."'></td>";

Filters::showFilter('','POST','search','Consultar',$tbody);



$connectdb=Connection::mysqliDB('CC');


 ?>
<style>
  .ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
  }

  .ui-autocomplete {
      max-height: 200px;
      overflow-y: auto;
      /* prevent horizontal scrollbar */
      overflow-x: hidden;
  		z-index: 1000;
    }
</style>

<script>
$(function(){

  $('#inicio').periodpicker({
    norange: true,
    formatDate: 'YYYY-MM-DD'
  });

    $( ".column" ).sortable({
      connectWith: ".column",
      handle: ".portlet-header",
      cancel: ".portlet-toggle",
      placeholder: "portlet-placeholder ui-corner-all"
    });

    $( ".portlet" )
      .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
      .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");

    $( ".portlet-toggle" ).on( "click", function() {
      var icon = $( this );
      icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
      icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
    });

    $( ".portlet-toggle" ).each(function() {
      var icon = $( this );
      icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
      icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
    });

    $( "ul.droptrue" ).sortable({
      connectWith: "ul",
      items: "li:not(.ui-state-disabled)",
      receive: function(event,ui){
        elemento=ui;
        objetivo=$(this);

        //alert(objetivo.attr('type'));

        if (objetivo.children().length > 2 && objetivo.attr('type')!='notAssigned') {
            //alert(objetivo.attr('type'));
            $('#as_new').val('');
            $('#as_old').val('');
            moveConfirm.dialog('option', 'position', {my: 'center top', at: 'center top', of: objetivo}).dialog('open');
        }else{
          moveAsesor($(this),ui);
        }

        }
    });

    $( ".sortable" ).disableSelection();

    moveConfirm=$( "#dialog-confirm" ).dialog({
      resizable: false,
      height: 'auto',
      width:400,
      autoOpen: false,
      modal: true,
      buttons: {
        "Confirmar cambio": function() {

          flag=false;

          if(elemento.item["0"].previousElementSibling.tagName=='P'){
            chg=elemento.item["0"].nextElementSibling;
          }else{
            chg=elemento.item["0"].previousElementSibling;
          }

          if(apply_change('out', $(chg).attr('idasesor'), objetivo.attr('idPuesto'))){
            if(apply_change('out', $(elemento.item).attr('idasesor'), elemento.sender.attr('idPuesto'))){
              apply_change('in', $(elemento.item).attr('idasesor'), objetivo.attr('idPuesto'));
              flag=true;
            }else{
              apply_change('in', $(chg).attr('idasesor'), objetivo.attr('idPuesto'));
              flag=false;
            }
          }else{
            flag=false;
          }

          if(flag){
            $(chg).appendTo('#notAssigned');
          }else{
            $(elemento.sender).sortable('cancel');
          }


          $( this ).dialog( "close" );
        },
        Cancel: function() {
          $(elemento.sender).sortable('cancel');
          $( this ).dialog( "close" );
        }
      }
    });

    function moveAsesor(domReceive, ui){

      flag=false;

      if($(ui.sender).attr('idPuesto')!='notAssigned'){
        $.when(flag_inter=apply_change('out', $(ui.item).attr('idasesor'), $(ui.sender).attr('idPuesto'))).done(function(){
          if(flag_inter){
            console.log('move_out OK');
            if(domReceive.attr('idPuesto')!='notAssigned'){
              $.when(flag_inter2=apply_change('in', $(ui.item).attr('idasesor'), domReceive.attr('idPuesto'))).done(function(){
                if(flag_inter2){
                  console.log('move_in OK');
                  return true;
                }else{
                  console.log('move_in ERROR');
                  $(ui.sender).sortable('cancel');
                }

              });
            }
          }else{
            console.log('move_out Error');
            $(ui.sender).sortable('cancel');
          }
        });

      }else{
        if(apply_change('in', $(ui.item).attr('idasesor'), domReceive.attr('idPuesto'))){
          return true;
        }else{
          $(ui.sender).sortable('cancel');
        }
      }
    }

    function apply_change(tipo, asesor, vacante){
      showLoader('Guardando Cambios',{my: 'center top', at: 'center top', of: objetivo});

      result=false;

      return $.ajax({
        url: 'pdvChangeQuery.php',
        type: 'POST',
        data: {vacante: vacante, asesor: asesor, fecha: '<?php echo $fecha; ?>', tipo: tipo},
        dataType: 'json',
        success: function(array){
            console.log('Envío exitoso: '+vacante+' | '+asesor+' | '+'<?php echo $fecha; ?>'+' | '+tipo);
            data=array;

            dialogLoad.dialog('close');

            if(data['status']==0){
              showNoty('error', data['msg'], 4000);
              console.log("Status 0");
              result=false;
            }else{
              showNoty('success', data['msg'], 4000);
              console.log("Status 1");
              result=true;
            }

            return result;
          },
        error: function(){
          console.log("Error en conexión");
          dialogLoad.dialog('close');
          showNoty('error','Error de conexión',3000);
          return result;
        }
      });
    }

} );
</script>

<style>
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    .overflow { height: 200px; }
  </style>
<style>
  body {
    min-width: 520px;
  }
  .column {
    width: 19.9%;
    float: left;
    padding-bottom: 100px;
  }
  .portlet {
    margin: 0 1em 1em 0;
    padding: 0.3em;
  }
  .portlet-header {
    padding: 0.2em 0.3em;
    margin-bottom: 0.5em;
    position: relative;
    height: 80px;
  }
  .portlet-toggle {
    position: absolute;
    top: 50%;
    right: 0;
    margin-top: -8px;
  }
  .portlet-content {
    padding: 0.4em;
  }
  .portlet-placeholder {
    border: 1px dotted black;
    margin: 0 1em 1em 0;
    height: 50px;
  }
  #main-container{
    width: 95%;
    min-width: 1130px;
    margin: auto;
  }
  .sortable {
      list-style-type: none;
      margin: 10px; float: left;
      margin-right: 10px;
      background: #eee;
      padding: 5px;
      width: 95%;
      min-height: 60px;
      display: inline-block;
      border: solid 1px;
  }
  .draggy{
        float:left;
        width: 95%;
      color: white;
      background: #FF6666;
      padding: 0.5em;
      margin: 0.5em;
      cursor: pointer;


  }

  ul.droptrue p{
    margin-top: 2px;
    margin-bottom: 0;
  }

  .notAssigned li{
    width: 150px;
    height: 37px;
  }
  </style>
<?php

if(!isset($_POST['inicio'])){exit;}



$query="SELECT
        	a.id, CONCAT(e.Ciudad,'<br>',b.PDV,\"<br><span style='color: blue'>\",IF(FindSupPDVDay(a.oficina,'".$fecha."',2) IS NULL,'',FindSupPDVDay(a.oficina,'".$fecha."',2)),'</span>') as PDV, c.Puesto, NombreAsesor(getVacante(a.id,'".$fecha."'),2) as Asesor, getVacante(a.id,'".$fecha."') as idAsesor, b.id as idPDV, a.esquema
        FROM asesores_plazas a LEFT JOIN PDVs b ON a.oficina=b.id LEFT JOIN PCRCs_puestos c ON a.puesto=c.id LEFT JOIN db_municipios e ON a.ciudad=e.id LEFT JOIN db_estados f ON e.estado=f.id
        WHERE a.departamento=29 AND a.fin>'".$fecha."'
        ORDER BY PDV";
if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array(MYSQLI_BOTH)){
    for($i=0;$i<$result->field_count;$i++){
      $plazas[utf8_encode($fila[1])][$fila[0]][$fields[$i]->name]=utf8_encode($fila[$i]);
    }
    $vacantes[$fila[0]]=utf8_encode($fila[3]);
  }

}else{
  echo "ERROR! -> ".$connectdb->error." ON $query";
}

$query="SELECT Nombre as Name, a.id, CONCAT(Nombre,' (',a.Esquema,')') as Nombre, getVacanteAsesor(a.id, '".$fecha."') as VacID  FROM
	(SELECT *, getDepartamento(id,'".$fecha."') as dep FROM Asesores WHERE Ingreso<='".$fecha."' AND Egreso>'".$fecha."' HAVING dep=29) a";
if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array(MYSQLI_BOTH)){
    for($i=1;$i<$result->field_count;$i++){
      if($fila[3]==NULL){
        $asesoresNOT[$fila[1]][$fields[$i]->name]=utf8_encode($fila[$i]);
      }else{
        if($vacantes[$fila[3]]==utf8_encode($fila[0])){
          $asesores[$fila[3]][$fields[$i]->name]=utf8_encode($fila[$i]);
        }else{
          $asesoresNOT[$fila[1]][$fields[$i]->name]=utf8_encode($fila[$i]);
        }
      }

    }
  }

}else{
  echo "ERROR! -> ".$connectdb->error." ON $query";
}



$connectdb->close();
?>
<br>

<div id='main-container'>

  <div class='portlet' style='width: 95%'>
                <div class='portlet-header'>Sin Asignar</div>
                <div class='portlet-content'>
                  <ul class='droptrue sortable ui-sortable notAssigned' type='notAssigned' id='notAssigned' idPuesto='notAssigned'>
                  <?php foreach($asesoresNOT as $index => $info){
                    echo "<li idasesor='".$info['id']."' class='draggy ui-sortable-handle'>".$info['Nombre']."</li>";
                  }
                  ?>
                  </ul>
                </div>
  </div>

  <?php
    $colNum=0;

    function setCol(){
      global $colNum;

      $tmpcol=$colNum;

      if($colNum==4){
        $colNum=0;
      }else{
        $colNum++;
      }

      return $tmpcol;
    }

    foreach ($plazas as $pdv => $plaza_puestos) {
      $tmpContent="<div class='portlet'>
                    <div class='portlet-header'>".$pdv."</div>
                    <div class='portlet-content'>";

      foreach($plaza_puestos as $puesto => $info){
        $tmpContent.="<ul class='droptrue sortable ui-sortable' idPuesto='".$info['id']."' type='Assignable'>";
        $tmpContent.="<p class='ui-state-disabled'>".$info['Puesto']." (".$info['esquema'].")</p>";

        if(isset($asesores[$info['id']])){
          $tmpContent.="<li idasesor='".$asesores[$info['id']]['id']."' class='draggy ui-sortable-handle'>".$asesores[$info['id']]['Nombre']."</li>";
        }

        $tmpContent.="</ul>";
      }

      $tmpContent.="</div>
                    </div> ";

      @$columna[setCol()].=$tmpContent;
    }

    foreach($columna as $index => $html){
      echo "<div class='column'>$html</div>";
    }
  ?>

</div>


<div id="dialog-confirm" title="Cambio de Asesor en PDV">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Al asignar esta posición a <span id='as_new'></span>, el(la) asesor(a) <span id='as_old'></span> quedará sin asignación. Estás de acuerdo? </p>
</div>

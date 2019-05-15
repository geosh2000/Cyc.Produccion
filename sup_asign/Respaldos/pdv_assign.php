<?php
include_once("../modules/modules.php");
initSettings::start(true,'sup_asign');

timeAndRegion::setRegion('Cun');
Scripts::periodScript('inicio','fin', 'norange: true');
initSettings::printTitle('Asignacion de Supervisor a PDVs');

//GET VARIABLES

if(!isset($_POST['date'])){$fecha=date('Y-m-d');}else{$fecha=date('Y-m-d',strtotime($_POST['date']));}
if(!isset($_POST['datef'])){$fechaf=date('Y-m-d');}else{$fechaf=date('Y-m-d',strtotime($_POST['datef']));}

$tbody="<td>Inicio</td><td><input type='text' name='date' id='inicio' value='<?php echo $fecha;?>' required><input type='text' name='datef' id='fin' value='<?php echo $fechaf;?>'></td>";


Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'asignar', 'Consultar', $tbody);

?>


<style>
  #draggable, #draggable2 {
      width: 90px;
      height: 30px;
      padding: 0.5em;
      float: left;
      margin: 10px 10px 10px 0;
  }
  #droppable, #droppable2 {
      width: 120px;
      height: 120px;
      padding: 0.5em;
      float: left;
      margin: 10px;
  }
  h3 {
      clear: left;
  }
  .sortable {
      list-style-type: none;
      margin: 10px; float: left;
      margin-right: 10px;
      background: #eee;
      padding: 5px;
      width: 429px;
      height: 400px;
  }
  .draggy{
        float:left;
        width: 115px;
      color: white;
      background: #FF6666;
      padding: 0.5em;
      margin: 0.5em;
      cursor: pointer;


  }
  .titles{
      width:429px;
      background:#eee;
      height:auto;
      margin:10px;
      padding-left: 10px;
      margin-left: 10px;
      font-weight: bold;
      font-size: 12px;
      margin-bottom: -10px;
      padding-bottom: 0;
      padding-top: 5px;
      float:left;
      margin-top:30px;
  }

  </style>
  <script>
  $(function() {

  $('#lists').hide();

  function sendRequest(asesor,supervisor){

      showLoader('Guardando Cambios', { my: "left top", at: "left bottom", of: elemento });

  		$.ajax({
  			url: "update_superpdv.php",
  			type: 'POST',
  			data: {asesor: asesor, super: supervisor, fecha: '<?php echo $fecha;?>'},
  			dataType: 'json',
  			success: function(array){
  				data = array;

                if(data['status']==1){
                  showNoty('success', 'Cambio aplicado', 4000);
                 }else{
                  showNoty('error', data['msg'],4000);
                 }

                 dialogLoad.dialog('close');


         },
         error: function(){
            showNoty('error', 'Error en conexi�n',4000);
            dialogLoad.dialog('close');
         }

  		})
  	}



    $( "ul.droptrue" ).sortable({
      connectWith: "ul",
      receive: function(event,ui){
          elemento = $(this);
          var receiver=$(this).attr("id");
          var asesor=ui.item.attr("id");
          var form=document.getElementById('as_'+asesor);
          form.value=receiver;
          sendRequest(asesor,receiver);
      }
    });



    $( ".sortable" ).disableSelection();
  });
  </script>


<br>
<?php if(!isset($_POST['date'])){exit;} ?>

<br>
<?php

$query="SELECT id, `N Corto`, getDepartamento(id,'$fecha') as Departamento, getPuesto(id,'$fecha') as PuestoOK FROM Asesores WHERE Egreso>'$fecha' AND Ingreso<='$fecha' HAVING PuestoOk IN (11,18,21) AND Departamento IN (29) ORDER BY `N Corto`";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		$sup[]=$fila['N Corto'];
    	$idsup[]=$fila['id'];
	}
}

$sup[]="0nosup";
$idsup[]="";

$query="SELECT a.id, PDV, Supervisor, getIdAsesor(Supervisor,1) as idsuper FROM
	(SELECT
		a.id, PDV, IF(FindSupPDVDay(a.id,'$fecha',1) IS NULL,'0nosup',FindSupPDVDay(a.id,'$fecha',1)) as Supervisor
	FROM
		PDVs a
	) a
ORDER BY
		Supervisor, PDV";
if($result=Queries::query($query)){
	$i=0;
	while($fila=$result->fetch_assoc()){
		if($i==0){$tmp=$fila['Supervisor']; $z=0;}
	    if($tmp!=$fila['Supervisor']){
	        $tmp=$fila['Supervisor']; $z=0;
	    }else{$z++;}

      if(!in_array($fila['idsuper'],$idsup)){
        $sup[]=$fila['Supervisor'];
      	$idsup[]=$fila['idsuper'];
      }

	    $data[$fila['Supervisor']][$z]=$fila['PDV'];
	    $color[$fila['Supervisor']][$z]=$fila['color'];
	    $id[$fila['Supervisor']][$z]=$fila['id'];
	    $supid[$fila['Supervisor']][$z]=$fila['idsuper'];
	    $tmp=$fila['Supervisor'];
	    $i++;
	}
}

?>

<div class='titles' style='width:1348px; margin-bottom: -10px;'>Sin Supervisor</div>
<ul id='' class='droptrue sortable' style='width:1348px; height:auto;'>

        <?php
		  foreach($sup as $ind => $super){
		        if($super=="0nosup"){
		            $supervisor="Sin Supervisor";
		            foreach($data[$super] as $key => $asesor){
		                echo "<li id='".$id[$super][$key]."' class='draggy' style='background:".$color[$super][$key]."'>$asesor</li>\n";
		            }
		        unset($key,$asesor);
		        }
		  }
		   unset($ind,$super);
	  ?>
    </ul>
  <?php
  foreach($sup as $ind => $super){
        if($super!="0nosup"){$supervisor="$super";
        echo "<div class='titles'>$supervisor";
        echo "<ul id='$idsup[$ind]' class='droptrue sortable' style='padding: 0; margin: 0;'>";
            foreach($data[$super] as $key => $asesor){
                echo "<li id='".$id[$super][$key]."' class='draggy' style='background:".$color[$super][$key]."'>$asesor</li>\n";
            }
        unset($key,$asesor);
        echo "</ul></div>";
        }
  }
   unset($ind,$super);
  ?>

<h3></h3>
 <br><br>
 <div id='lists'>
 <?php foreach($data as $key => $super){
        foreach($super as $key2 =>$asesor){
            echo "<label for='".$id[$key][$key2]."'>$asesor</label>
<input id='as_".$id[$key][$key2]."' name='".$id[$key][$key2]."' type='text' value='".$supid[$key][$key2]."'/><br>\n";
        }
 }
 ?>
 </div>

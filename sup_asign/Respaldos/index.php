<?php
include_once("../modules/modules.php");
initSettings::start(true,'sup_asign');

timeAndRegion::setRegion('Cun');
Scripts::periodScript('inicio','fin', 'norange: true');
initSettings::printTitle('Asignacion de Supervisor');

//GET VARIABLES
$dept=$_POST['dept'];
$asesor=$_POST['asesor'];
if(!isset($_POST['date'])){$fecha=date('Y-m-d');}else{$fecha=date('Y-m-d',strtotime($_POST['date']));}
if(!isset($_POST['datef'])){$fechaf=date('Y-m-d');}else{$fechaf=date('Y-m-d',strtotime($_POST['datef']));}
$sup=$_POST['super'];
$i=0;
while($i<200){
	if(isset($_POST['asesor'.$i])){$asesor[$i]=$_POST['asesor'.$i]; $check[$i]="checked";}else{$asesor[$i]=$_POST['asesor'.$i]; $check[$i]="";}
$i++;
}

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

  		$.ajax({
  			url: "update_super.php",
  			type: 'GET',
  			data: {asesor: asesor, super: supervisor, fecha: '<?php echo $fecha;?>'},
  			dataType: 'html',
  			success: function(data){
  				text = data;
                var status = text.match("status- (.*) -status");
                var notif_msg = text.match("msg- (.*) -msg");
                if(status[1]=='OK'){
                    tipo_noti='success';
                }else{
                    tipo_noti='error';

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
                    },

                });
  			}
  		})
  	}



    $( "ul.droptrue" ).sortable({
      connectWith: "ul",
      receive: function(event,ui){
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

$query="SELECT a.id, Asesor, Supervisor, b.id as idsuper, color FROM
	(SELECT
		a.id, `N Corto` as Asesor, color, IF(FindSupDay(a.id,'$fecha') IS NULL,'0nosup',FindSupDay(a.id,'$fecha')) as Supervisor, `id Departamento`
	FROM
		Asesores a
	LEFT JOIN
		PCRCs b ON a.`id Departamento`=b.id
	WHERE
		((Activo=1 AND Ingreso<='$fecha') OR
         (Activo=0 AND Egreso>='$fecha')
    )
	HAVING
		`id Departamento` NOT IN (12,1,29,30,31)
        ) a
	LEFT JOIN
		Asesores b
	ON
		a.Supervisor=b.`N Corto`

	ORDER BY
		Supervisor, Asesor";
if($result=Queries::query($query)){
	$i=0;
	while($fila=$result->fetch_assoc()){
		if($i==0){$tmp=$fila['Supervisor']; $z=0;}
	    if($tmp!=$fila['Supervisor']){
	        $tmp=$fila['Supervisor']; $z=0;
	    }else{$z++;}

	    $data[$fila['Supervisor']][$z]=$fila['Asesor'];
	    $color[$fila['Supervisor']][$z]=$fila['color'];
	    $id[$fila['Supervisor']][$z]=$fila['id'];
	    $supid[$fila['Supervisor']][$z]=$fila['idsuper'];
	    $tmp=$fila['Supervisor'];
	    $i++;
	}
}

$query="SELECT id, `N Corto`, getDepartamento(id,'$fecha') as dep, getPuesto(id,'$fecha') as Super FROM Asesores WHERE Egreso>'$fecha' AND Ingreso<='$fecha' HAVING dep NOT IN (29) AND Super IN (11,18,19,21) ORDER BY `N Corto`";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		$sup[]=$fila['N Corto'];
    	$idsup[]=$fila['id'];
	}
}

$sup[]="0nosup";
$idsup[]="";

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

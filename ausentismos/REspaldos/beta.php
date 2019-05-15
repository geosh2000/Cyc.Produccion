<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="schedules_change";
$menu_programaciones="class='active'";
include("../connectDB.php");
include("../DBAsesores.php");
include("../DBPcrcs.php");

//Get Variables
$casereq="required";
$dep=$_POST['dep'];
$asesor=$_POST['asesor'];
$ausentismo=$_POST['tipo'];
$dias=$_POST['dias'];
$motivo=$_POST['motivo'];
if(isset($asesor)){
	$asesor_name=mysql_result(mysql_query("SELECT * FROM Asesores WHERE id='$asesor'"),0,'N Corto');
}
if(isset($ausentismo)){
	$ausentismo_name=mysql_result(mysql_query("SELECT * FROM `Tipos Ausentismos` WHERE id='$ausentismo'"),0,'Ausentismo');
}

if($ausentismo==5){$titulo_select="Motivo:";}else{$titulo_select="Beneficios:";}
if($ausentismo==10){$comreq="required"; $casereq="";}

//List Depts
	$i=0;
	$departs="<option value'' selected>Selecciona...</option>";
	while($i<$pcrcs_num){
		if($pcrcs_id_Sorted[$i]==$dep){$sel="selected";}else{$sel="";}
		$departs="$departs\t\t<option value='$pcrcs_id_Sorted[$i]' $sel>$pcrcs_departamento_Sorted[$i]</option>\n";
	$i++;
	}

//List Ausentismos
	$query="SELECT * FROM `Tipos Ausentismos`ORDER BY Ausentismo";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	$i=0;
	while($i<$num){
		$aus_id[$i]=mysql_result($result,$i,'id');
		$aus_ausentismo[$i]=mysql_result($result,$i,'ausentismo');
		$aus_days[$i]=mysql_result($result,$i,'max_days');
	$i++;
	}
	$tipos="<option value'' selected>Selecciona...</option>";
	foreach($aus_id as $key => $ausid){
		if($ausid==$ausentismo){$sel="selected";}else{$sel="";}
		$tipos="$tipos\t\t<option value='$ausid' $sel>$aus_ausentismo[$key]</option>\n";
	}

//Function for listing Asesores
	function print_options($dept){
		global $asesor, $ASnum, $ASNCorto_Sorted, $ASdepto_Sorted, $ASactive_Sorted, $ASid_Sorted;
		$as=$asesor;
		$optprint="<option value=''>Selecciona...</option>";
		$i=0;
		while ($i<$ASnum){
			//Print only Dept Asesores
			if($ASdepto_Sorted[$i]==$dept && $ASactive_Sorted[$i]==1){
				if($ASid_Sorted[$i]==$as){$sel=" selected";}else{$sel="";}
				$optprint="$optprint<option value='$ASid_Sorted[$i]'$sel>$ASNCorto_Sorted[$i]</option>";
			}
		$i++;
		}
		
		echo $optprint;
	}
	
//Function for listing Days
	function print_days($dept){
		global $asesor, $motivo, $ausentismo, $aus_days, $aus_id, $dias;
		$as=$asesor;
		$optprint="<option value=''>Selecciona...</option>";
		if($ausentismo!=5){
			$i=1;
			$query="SELECT * FROM `Tipos Ausentismos` WHERE id='$dept'";
			while ($i<=mysql_result(mysql_query($query),0,'max_days')){
				if($i==$dias){$sel=" selected";}else{$sel="";}
				$optprint="$optprint<option value='$i'$sel>$i</option>";
			$i++;
			}
		}else{
			$i=1;
			$query="SELECT sum(dias) as dias FROM `Dias Pendientes Redimidos` WHERE id='$asesor' AND motivo='$motivo'";
			$dp_dr=mysql_result(mysql_query($query),0,'dias');
			if($dp_dr==NULL){$dp_dr=0;}
			
			$query="SELECT sum(`dias asignados`) as dias FROM `Dias Pendientes` WHERE id='$asesor' AND motivo='$motivo'";
			$dp_da=mysql_result(mysql_query($query),0,'dias');
			if($dp_da==NULL){$dp_da=0;}
			
			$dp_total=$dp_da-$dp_dr;
			
			
			while ($i<=$dp_total){
				if($i==$dias){$sel=" selected";}else{$sel="";}
				$optprint="$optprint<option value='$i'$sel>$i</option>";
			$i++;
			}
		}
		
		echo $optprint;
	}
	
//Function for listing Days 2
	function print_daysinter($days){
		
		$i=0;
		while ($i<=$days){
			$optprint="$optprint<option value='$i'>$i</option>";
		$i++;
		}
		
		echo $optprint;
	}
	
//Function Motivos por asesor para Dias Pendientes
	function print_motivos($id){
	global $motivo;
		$query="SELECT DISTINCT motivo FROM `Dias Pendientes` WHERE id='$id'";
		$result=mysql_query($query);
		
		
		echo "<tr class='title'><td>Motivo:</td><td class='pair'><select name='motivo' onchange='this.form.submit()' required><option value=''>Selecciona...</option>";
		
		$i=0;
		while($i<mysql_numrows($result)){
			$as="";
			if(mysql_result($result,$i,'motivo')==$motivo){$as="selected";}
			echo "\t<option value='".mysql_result($result,$i,'motivo')."' $as>".mysql_result($result,$i,'motivo')."</option>\n";
		$i++;
		}
		echo "</select></td></tr>";
	
	}
	
if($ausentismo==NULL || $ausentismo==5){ $showdays="hidden";}
if($ausentismo==5 && $motivo!=NULL){ $showdays=""; }

?>
<?php include("../common/scripts.php"); ?>



<?php include("../common/menu.php"); ?>

<?php
if(isset($_POST['upload'])){
$datestart=$_POST['inicio'];
$dateend=$_POST['fin'];
$descansos=$_POST['descansos'];
$beneficios=$_POST['beneficios'];
$caso=$_POST['caso'];
$comment=$_POST['comment'];
$moper=$_POST['moper'];

if($comment==NULL){$comment="NULL";}
if($moper==NULL){$moper="NULL";}
if(isset($_POST['isichk'])){$isi=1;}else{$isi=0;}

include("upload.php");
$success=1;
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script>
$(window).load(function(){
$('#isichk').prop('checked', false);
$('#moper').prop('readonly', true);
$('#moper').prop('disabled', true);
$('#moper').prop('required', false);

$('#isichk').on('click', function() {
  if ($(this).is(':checked')) {
    $('#moper').prop('readonly', false);
    $('#moper').prop('disabled', false);
    $('#moper').prop('required', true);
    //alert('checked');
  } else {
    $('#moper').prop('readonly', true);
    $('#moper').prop('disabled', true);
    $('#moper').prop('required', false);
    //alert('not checked');
  }
});

//$('#checker').button(); //Requires jQuery UI

});

$(document).ready(function() {

//Script -- Define "Asesores" listed depending on "Departamento"
    $("#pcrc_select").change(function() {
        var val = $(this).val();
        switch(val){
        	<?php
        	
        	$i=0;
        	while($i<$pcrcs_num){
        		echo "case '$i':\n";
        		echo "$(\"#sel\").html(\"";
        		print_options($i);
        		echo "\");\n break;\n";
        		
        	$i++;
        	}
        	
        	?>
        	
        }
        	
   
        	
   });
   
   //Script -- Define "Days" listed depending on "Ausentismo"
    $("#type").change(function() {
        var val = $(this).val();
        switch(val){
        	<?php
        	
        	$i=0;
        	foreach($aus_id as $key2 => $id2){
        		echo "case '$id2':\n";
        		echo "$(\"#dias\").html(\"";
        		print_days($id2);
        		echo "\");\n break;\n";
        	}
        	
        	?>
        	
        }
        	
   
        	
   });
   
 <?php if($dias!=NULL && $success!=1){
 
 echo"
   //Script -- Change end dates
   
    $('#datestart').change(function(){
    	
   	var val = $('#datestart').val();
   	var descansos = $('#descansos').val()*86400000;
   	var beneficios = $('#beneficios').val()*86400000;
        var days = $dias*86400000;
        var extra=days+descansos+beneficios;
        var dateend = Date.parse(val);
        var dateend = new Date(dateend + extra);
        var month = dateend.getMonth()+1;
        if(month<10){month= '0'+month;}
        var day = dateend.getDate();
        if(day<10){day= '0'+day;}
        var year  = dateend.getFullYear();
        

	//add a day to the date
	//dateend.setDate(dateend.getDate() + days);
        $('#dateend').val(year + '-' + month + '-' + day);   
   
    });
    
   $('#descansos').change(function(){
    	
   	var val = $('#datestart').val();
   	var descansos = $('#descansos').val()*86400000;
   	var beneficios = $('#beneficios').val()*86400000;
        var days = $dias*86400000;
        var extra=days+descansos+beneficios;
        var dateend = Date.parse(val);
        var dateend = new Date(dateend + extra);
        var month = dateend.getMonth()+1;
        if(month<10){month= '0'+month;}
        var day = dateend.getDate();
        if(day<10){day= '0'+day;}
        var year  = dateend.getFullYear();
        

	//add a day to the date
	//dateend.setDate(dateend.getDate() + days);
        $('#dateend').val(year + '-' + month + '-' + day);   
   
    });
    
    $('#beneficios').change(function(){
    	
   	var val = $('#datestart').val();
   	var descansos = $('#descansos').val()*86400000;
   	var beneficios = $('#beneficios').val()*86400000;
        var days = $dias*86400000;
        var extra=days+descansos+beneficios;
        var dateend = Date.parse(val);
        var dateend = new Date(dateend + extra);
        var month = dateend.getMonth()+1;
        if(month<10){month= '0'+month;}
        var day = dateend.getDate();
        if(day<10){day= '0'+day;}
        var year  = dateend.getFullYear();
        

	//add a day to the date
	//dateend.setDate(dateend.getDate() + days);
        $('#dateend').val(year + '-' + month + '-' + day);   
   
    });
    
";} ?>    
});
</script>
<script>
$('#isichk').prop('checked', true);

$('#isichk').on('click', function(){
    if($(this).is(':checked')){
        $('#moper').prop('readonly', true);
        $('#moper').prop('disabled', false);    
        //alert('checked');
    }else{
        $('#moper').prop('readonly', true);
        $('#moper').prop('disabled', true);    
        //alert('not checked');
    }    
});

//$('#checker').button(); //Requires jQuery UI

</script>
<script>
  $(function() {
    var dialog, form,

      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
      name = $( "#name" ),
      email = $( "#email" ),
      password = $( "#password" ),
      allFields = $( [] ).add( name ).add( email ).add( password ),
      tips = $( ".validateTips" );

    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }

    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }
    function deleteMoper() {

     var id=$("#a_id");
     var moper=$("#mopernew");
     var ok_target=$("#target");
     var target=ok_target.val();


     var ok_url="edit_aus.php?id="+id.val()+"&moper=NULL";

     if (id == "") {
        document.getElementById(target).innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById(target).innerHTML = xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET",ok_url,true);
        xmlhttp.send();


      dialog.dialog( "close" );
    }

    }

    function deleteAusentismo() {

     var id=$("#a_id");
     var moper=$("#mopernew");
     var ok_target=$("#target");
     var target=ok_target.val();


     var ok_url="edit_aus.php?id="+id.val()+"&delete=ok";

     if (id == "") {
        document.getElementById(target).innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var respuesta=xmlhttp.responseText;
                if(respuesta=='Done'){
                    $('table#ausen tr#'+target).remove();
                }else{alert(respuesta);}
            }
        }

        xmlhttp.open("GET",ok_url,true);
        xmlhttp.send();



    }

    }

    $( "#dialog-confirm" ).dialog({
      resizable: false,
      height:150,
      width:400,
      autoOpen: false,
      modal: true,
      buttons: {
        "Delete Moper": function() {
            deleteMoper();
          $( this ).dialog( "close" );
          dialog.dialog( "close" );
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });

    $( "#dialog-confirm2" ).dialog({
      resizable: false,
      height:150,
      width:400,
      autoOpen: false,
      modal: true,
      buttons: {
        "Delete Moper": function() {
          deleteAusentismo();
          $( this ).dialog( "close" );

        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });

    function confirmDelete(){
            $( "#dialog-confirm" ).dialog("open");

    }

    function addUser() {

     var id=$("#a_id");
     var moper=$("#mopernew");
     var ok_target=$("#target");
     var target=ok_target.val();

        var valid = true;
      allFields.removeClass( "ui-state-error" );

      valid = valid && checkRegexp( moper, /^([0-9])+$/, "Moper only accepts 0-9" );

      if ( valid ) {

     var ok_url="edit_aus.php?id="+id.val()+"&moper="+moper.val();

     if (id == "") {
        document.getElementById(target).innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById(target).innerHTML = xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET",ok_url,true);
        xmlhttp.send();


      dialog.dialog( "close" );
    }
    }
      return valid;
    }

    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 530,
      modal: true,
      buttons: {
        "Enviar": addUser,
        "Eliminar":confirmDelete,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "ui-state-error" );
      }
    });

    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });

    $( '#0, sh:gt(0):lt(5000)' ).button().on( "click", function() {
      var x=this.id;
      var f_id=document.getElementById('id'+x);
      var f_moper=document.getElementById('moper'+x);
      var n_target=document.getElementById('target');
      var n_id=document.getElementById('a_id');
      var n_moper=document.getElementById('moperold');
      n_moper.value=f_moper.innerText;
      n_id.value=f_id.innerText;
      n_target.value='moper'+x;

      dialog.dialog( "open" );
    });

    $( '#1000, zh:gt(0):lt(5000)' ).button().on( "click", function() {
        var z=this.id-1000;
        var x_target=document.getElementById('target');
        var fx_id=document.getElementById('id'+z);
        var x_id=document.getElementById('a_id');
        x_id.value=fx_id.innerText;
        x_target.value='div'+z;
      $( "#dialog-confirm2" ).dialog("open");
    });



  });


  </script>
<script>
  $(function() {
    $( "#accordion" ).accordion({
      collapsible: true,
      active: false
    });
  });
  </script>
<table width='100%' class='t2'><form name='aus' method='post' action='<?php $_SERVER['PHP_SELF']; ?>'>
	<tr class='title'>
		<td colspan=100>Asignaci&oacuten de Ausentismos</td>
	</tr>
	<tr class='title'>
		<td width='25%'>Departamento:</td>
		<td  class='pair' width='25%'><select name='dep' id="pcrc_select" onchange='this.form.submit()' required><?php echo $departs; ?></select></td>
		<td width='25%'>Asesor:</td>
		<td class='pair' width='25%'><select name='asesor' id="sel" onchange='this.form.submit()' required><?php print_options($dep); ?></select></td>
	</tr>
	<tr class='title'>
		<td width='25%'>Tipo:</td>
		<td  class='pair' width='25%'><select name='tipo' id="type" onchange="this.form.submit()" required><?php echo $tipos; ?></select></td>
		<td width='25%'>Dias:</td>
		<td class='pair' width='25%'><select name='dias' id="dias" required <?php echo $showdays; ?>><?php print_days($ausentismo); ?></select></td>
	</tr>
	<?php if($ausentismo==5){ print_motivos($asesor);} ?>
	<tr class='total'>
		<td colspan=100><input type='submit' name='Consultar' value='Consultar'></td>
	</tr>
</form></table>

<br>

<?php if($dias==NULL  || $success==1){exit;}
$query="SELECT * FROM Ausentismos a, `Tipos Ausentismos` b, Asesores c WHERE a.tipo_ausentismo=b.id AND a.asesor=c.id AND a.asesor=$asesor ORDER BY Inicio DESC";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $a_id[$i]=mysql_result($result,$i,'ausent_id');
    $a_tipo[$i]=mysql_result($result,$i,'Ausentismo');
     $a_caso[$i]=mysql_result($result,$i,'caso');
     $a_fi[$i]=mysql_result($result,$i,'Inicio');
     $a_ff[$i]=mysql_result($result,$i,'Fin');
     $a_descansos[$i]=mysql_result($result,$i,'Descansos');
     $a_beneficios[$i]=mysql_result($result,$i,'Beneficios');
     $a_dias[$i]=(strtotime($a_ff[$i])-strtotime($a_fi[$i]))/60/60/24-$a_beneficios[$i]-$a_descansos[$i];
     $a_moper[$i]=mysql_result($result,$i,'Moper');
     $a_obs[$i]=mysql_result($result,$i,'Comments');
     $a_fasign[$i]=date('Y-m-d', strtotime(mysql_result($result,$i,'Last Update')));
     $a_uasign[$i]=mysql_result($result,$i,'Usuario');
$i++;
}


?>

<div id="accordion">
  <h3><?php echo $num;?> Ausentismos Registrados</h3>
  <div>
    <table width='100%' class='t2' id='ausen'>
	<tr class='title'>
		<td>id</td>
        <td>Tipo</td>
        <td>Caso</td>
        <td>Fecha Inicio</td>
        <td>Fecha Fin</td>
        <td>Dias</td>
        <td>Descansos</td>
        <td>Beneficios</td>
        <td>Moper ISI</td>
        <td>Observaciones</td>
        <td>Fecha de Asignacion</td>
        <td>Asignado por:</td>
        <td colspan=2>Editar</td>
	</tr>
    <?php
      foreach($a_tipo as $keytipo => $type){
          $akeytipo=$keytipo+1000;
          if($keytipo % 2 == 0){$classtipo="class='pair'";}else{$classtipo="class='odd'";}
          echo "\t<tr $classtipo id='div$keytipo'>\n";
            echo "\t\t<td id='id$keytipo'>$a_id[$keytipo]</td>\n";
            echo "\t\t<td id='tipo$keytipo'>$type</td>\n";
            echo "\t\t<td id='caso$keytipo'>$a_caso[$keytipo]</td>\n";
            echo "\t\t<td id='inicio$keytipo'>$a_fi[$keytipo]</td>\n";
            echo "\t\t<td id='fin$keytipo'>$a_ff[$keytipo]</td>\n";
            echo "\t\t<td id='dias$keytipo'>$a_dias[$keytipo]</td>\n";
            echo "\t\t<td id='descansos$keytipo'>$a_descansos[$keytipo]</td>\n";
            echo "\t\t<td id='beneficios$keytipo'>$a_beneficios[$keytipo]</td>\n";
            echo "\t\t<td id='moper$keytipo'>$a_moper[$keytipo]</td>\n";
            echo "\t\t<td id='obs$keytipo'>$a_obs[$keytipo]</td>\n";
            echo "\t\t<td id='la$keytipo'>$a_fasign[$keytipo]</td>\n";
            echo "\t\t<td id='usr$keytipo'>$a_uasign[$keytipo]</td>\n";
          echo "<td><sh style=width='100%' id='$keytipo' auid='$a_id[$i]'>Moper</sh></td>\n";
          echo "<td><zh style=width='100%' id='$akeytipo' auid='$a_id[$i]'>Eliminar</zh></td>\n";
          echo "\t</tr>\n";
      }
    ?>
	</table>
  </div>

</div>
<br>

<table width='100%' class='t2'><form name='senddias' method='post' action='<?php $_SERVER['PHP_SELF']; ?>'>
	<tr class='title'>
		<th colspan=100><?php echo "Seleccion de $ausentismo_name para $asesor_name ($dias dias)"; ?></th>
	</tr>
	<input type='text' name='asesor' value='<?php echo $asesor; ?>' hidden>
	<input type='text' name='tipo' value='<?php echo $ausentismo; ?>' hidden>
	<input type='text' name='dias' value='<?php echo $dias; ?>' hidden>
	<input type='text' name='motivo' value='<?php echo $motivo; ?>' hidden>
	<tr class='title'>
		<td width='25%'>Descansos Intermedios:</td>
		<td  class='pair' width='25%'><select name='descansos' id="descansos" required><?php print_daysinter(8); ?></select></td>
		<td width='25%'>Beneficios:</td>
		<td class='pair' width='25%'><select name='beneficios' id="beneficios" required <?php if($ausentismo!=1){ echo "hidden"; } ?>><?php print_daysinter(8); ?></select></td>
	</tr>
	<tr class='title'>
		<td width='25%'>Fecha Inicial:</td>
		<td  class='odd' width='25%'><input type='date' name='inicio' id="datestart" required></td>
		<td width='25%'>Fecha Final:</td>
		<td class='odd' width='25%'><input type='date' name='fin' id="dateend" readonly required></td>
	</tr>
	<tr class='title'>
		<td width='25%'>Caso:</td>
		<td  class='pair' width='25%'><input type='text' name='caso' id="test" size=8 <?php echo $casereq; ?>></td>
		<td width='25%'>Observaciones:</td>
		<td class='pair' width='25%'><input type='text' name='comment' id="test" <?php echo $comreq; ?>></td>
	</tr>
<?php if($ausentismo==1 || $ausentismo==2 || $ausentismo==3 || $ausentismo==9){ 

echo "
	<tr class='title'>
		<td width='25%'>ISI RRHH:</td>
		<td  class='odd' width='25%'>Hecho: <input type='checkbox' name='isichk' id='isichk'></td>
		<td width='25%'>Moper:</td>
		<td class='odd' width='25%'><input type='text' name='moper' id='moper'</td>
	</tr>";} ?>
	<tr class='total'>
		<td colspan=100><input type='submit' name='upload' value='Guardar'></td>
	</tr>
</form></table>

 <div id="dialog-form" title="Cambiar Moper">
 <p class="validateTips">Fill the required Fields.</p>

  <form>
    <fieldset>
        <table width='480px'>
            <tr>
                <td width='30%'><label for="a_id">ID</label></td>
                <td><input type="text" name="a_id" id="a_id" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
            <tr>
                <td width='30%'><label for="date">Moper Anterior</label></td>
                <td><input type="text" name="moperold" id="moperold" value="" class="text ui-widget-content ui-corner-all" readonly></td>

            </tr>
            <tr>
                <td width='30%'><label for="date">Moper Nuevo</label></td>
                <td><input type="text" name="mopernew" id="mopernew" value="" class="text ui-widget-content ui-corner-all">
                <input type="text" name="target" id="target" value="" class="text ui-widget-content ui-corner-all" hidden></td>

            </tr>
            </table>
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
<div id="dialog-confirm" title="Eliminar Moper">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El moper se eliminar� del registro. Estas seguro?</p>
</div>
<div id="dialog-confirm2" title="Eliminar Ausentismo">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>El AUSENTISMO se eliminar� del registro. Estas seguro?</p>
</div>

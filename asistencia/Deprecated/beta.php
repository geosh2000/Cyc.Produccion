<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="payroll";
$menu_programaciones="class='active'";

include("../connectDB.php");
include("../common/scripts.php");
include("../common/list_asesores.php");


//Get Variables

if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d', strtotime('-15 days'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d', strtotime('-1 days'));}
$showh="checked";
$showexc="checked";
$p_dep="all";
if(isset($_POST['submit'])){
     if(isset($_POST['showh'])){$showh="checked";}else{$showh="";}
     if(isset($_POST['showexc'])){$showexc="checked";}else{$showexc="";}
     if(isset($_POST['showret'])){$showret="checked";}else{$showret="";}
     $p_dep=$_POST['dep'];
}
if($p_dep!="all"){$sel_dep=" AND `id Departamento`='$p_dep' ";};




?>

<script>
  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "-2w",

      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );

      }
    });
    $( "#to" ).datepicker({
      defaultDate: "0",

      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

  });
  </script>
<?php  if($_SESSION['monitor_pya_exceptions']!=1){goto EndFuncExcep;} ?>
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

    function addUser() {
     var str=$("#excep");
     var id=$("#a_id");
     var caso=$("#case");
     var ok_target=$("#target");
     var notes=$("#notes");
     var target=ok_target.val();
     var hid_ok=$("#hid");
     var ok_url="http://pt.comeycome.com/pya-monitor/exceptions.php?excep="+str.val()+"&asesor="+id.val()+"&h="+hid_ok.val()+"&caso1="+caso.val()+"&notes="+notes.val();
     if (str == "") {
        document.getElementById("a"+target).innerHTML = "";
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
                document.getElementById("a"+target).innerHTML = "<br>"+xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET",ok_url,true);
        xmlhttp.send();


      dialog.dialog( "close" );
    }
    }

    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 400,
      width: 620,
      modal: true,
      buttons: {
        "Enviar": addUser,
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

    $( ".editable, .flashred, .rojo, .orange" ).button().on( "click", function() {
      var x=this.id;
      var name_id=$('#'+x).attr("nameid");
      var name_corto=$('#'+x).attr("ncorto");
      var fecha=$('#'+x).attr("fecha");
      var hid=$('#'+x).attr("horaid");
      var f_name=document.getElementById('name');
      var f_id=document.getElementById('a_id');
      var f_fecha=document.getElementById('date');
      var f_div=document.getElementById('target');
      var f_hid=document.getElementById('hid');
      f_name.value=name_corto;
      f_id.value=name_id;
      f_fecha.value=fecha;
      f_div.value=x;
      f_hid.value=hid;

      dialog.dialog( "open" );
    });

    $("#excep").change(function(){
       switch($(this).val()){
           case '3':
           case '8':
            $("#case").attr("readonly",false);
            break;
           default:
            $("#case").attr("readonly",true);
            break;


       }
    });

  });


  </script>

<?php
EndFuncExcep:
include("../common/menu.php");
function printOptions(){

   	echo "<option value='0'>Selecciona...</option>";
		$query="SELECT * FROM `Tipos Excepciones` ORDER BY Excepcion";
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		$i=0;
		while($i<$num){
			echo "<option value='".mysql_result($result,$i,'exc_type_id')."'>".mysql_result($result,$i,'Excepcion')."</option>";
		$i++;
		}


}
?>
<?php  if($_SESSION['monitor_pya_exceptions']!=1){goto EndFormExcep;} ?>
 <div id="dialog-form" title="Nueva Excepcion">
  <p class="validateTips">Fill the required Fields.</p>

  <form>
    <fieldset>
        <table width='550px'>
            <tr>
                <td width='30%'><label for="date">Fecha</label></td>
                <td><input type="text" name="date" id="date" value="" class="text ui-widget-content ui-corner-all" readonly>
                <input type="text" name="target" id="target" value="" hidden />
                <input type="text" name="hid" id="hid" value="" hidden /></td>
            </tr>
            <tr>
                <td width='30%'><label for="a_id">ID</label></td>
                <td><input type="text" name="a_id" id="a_id" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
            <tr>
                <td width='30%'><label for="name">Asesor</label></td>
                <td><input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
      <tr><td width='30%'><label for="excep">Excepcion</label></td>
      <td><select  class="option ui-widget-content ui-corner-all" name="excep" id="excep" required><?php printOptions(); ?></select></td></tr>
      <tr><td width='30%'><label for="case">Caso</label></td>
      <td><input type="text" name="case" id="case" value="" class="text ui-widget-content ui-corner-all" required='true' readonly></td></tr>
      <tr><td width='30%'><label for="notes">Notas</label></td>
      <td><input type="text" name="notes" id="notes" value="" class="text ui-widget-content ui-corner-all" required='true'></td></tr>
      </table>
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>

<?php EndFormExcep: ?>
<table width=100% class='t2'><form action="<?php $_SERVER['PHP_SELF'] ?>" method='post'>
    <tr class='title'>
        <th colspan=100 id='demotitle'>Consulta de Asistencia CC</th>
    </tr>
    <tr class='title'>
        <th>Fecha Inicio:</th>
        <td class='pair'><input type="text" id="from" name="from" value='<?php echo $from ?>' required></td>
        <th>Fecha Fin:</th>
        <td class='pair'><input type="text" id="to" name="to" value='<?php echo $to ?>' required></td>
        <th>Departamento</th>
        <td class='pair'><select name="dep" required><?php list_departamentos($p_dep); ?><option value="all" <?php if($dep=="all"){echo "selected";}?>>Todos</option></select></td>
        <th>Mostrar</th>
        <td class='pair' style='text-align:right'><label for="showh">Horarios</label><input type="checkbox" id="showh" name="showh" <?php echo $showh ?>><br>
                        <label for="showexc">Excepciones</label><input type="checkbox" id="showexc" name="showexc" <?php echo $showexc ?>><br>
                        <label for="showret">Retardos</label><input type="checkbox" id="showret" name="showret" <?php echo $showret ?>>
        </td>
        <td class='total'><input type="submit" name="submit" value="consulta" /></td>
    </tr>
</form></table>

<br>

<?php
if(!isset($_POST['submit'])){exit;}

$query="SELECT
	Fecha, id_asesor, id_Dep, Departamento, `N Corto`, Nombre, Ingreso, id_hora, `jornada start`, `jornada end`,
	Sesiones.asesor, Fecha_in, Hora_in, Fecha_out, Hora_out, Excepciones.asesor, Inicio, Fin, Descansos, Beneficios, Ausentismo, Code,
	caso, Nota, Codigo, IF(`jornada start`!='00:00:00' AND `jornada end`!='00:00:00',(TIME_TO_SEC(Hora_in)-TIME_TO_SEC(`jornada start`))/60,NULL) as Retardos
	FROM
	(SELECT
	Dates.Fecha as Fecha, Dates.id as id_asesor, Dates.`id Departamento` as id_Dep, Dates.`Departamento` as Departamento, Ingreso, Dates.`N Corto` as 'N Corto', Dates.Nombre as Nombre, Histo.id_hora, Histo.`jornada start`as 'jornada start',
	Histo.`jornada end` as 'jornada end'
	FROM
		(
		SELECT
		Fecha, id, `N Corto`, Nombre, `id Departamento`, Departamento, Ingreso
		FROM
			Fechas
		JOIN
			(SELECT a.id, `N Corto`, Nombre, `id Departamento`, Departamento, Activo, Ingreso FROM Asesores a, PCRCs b WHERE a.`id Departamento`=b.id) as Asesores
		WHERE
			Fecha>='$from' AND
			Fecha<='$to' AND
			Activo=1 $sel_dep
		ORDER BY
			`N Corto`
		) as Dates
	LEFT JOIN
		(SELECT
		a.id as id_hora, asesor as id_asesor, `N Corto`, Fecha, `jornada start`, `jornada end`, b.`id Departamento`
		FROM
			`Historial Programacion` a,
			Asesores b

		WHERE
			a.asesor=b.id
		) as Histo
	ON
		Dates.id=Histo.id_asesor AND
		Dates.Fecha=Histo.Fecha) as Horarios

	LEFT JOIN
		(SELECT
	asesor, Fecha_in, MIN(Hora_in) as Hora_in, Fecha_out, MAX(Hora_out) as Hora_out
	FROM
		t_Sesiones
	WHERE
		Hora_in>'04:00:00' AND
		Hora_in<'22:00:00'
	GROUP BY
		asesor, Fecha_in) as Sesiones
	ON
		Horarios.Fecha=Sesiones.Fecha_in AND
		Horarios.id_asesor=Sesiones.asesor
	LEFT JOIN
		(SELECT
	asesor, Inicio, Fin, Descansos, Beneficios, Ausentismo, Code
	FROM
		Ausentismos a,
		`Tipos Ausentismos` b
	WHERE
		a.tipo_ausentismo=b.id) Excepciones
	ON
		Horarios.id_asesor=Excepciones.asesor AND
		Horarios.Fecha BETWEEN Excepciones.Inicio AND Excepciones.Fin
	LEFT JOIN
		(SELECT
	horario_id, caso, Nota, Codigo
	FROM
		PyA_Exceptions a,
		`Tipos Excepciones`b

	WHERE
		a.tipo=b.exc_type_id AND
		tipo!=1
		) as Retardos
	ON
		id_hora=horario_id

	ORDER BY
		`Nombre`, Fecha";
$result=mysql_query($query);
$num=mysql_numrows($result);


?>



<table width=100% class='t2' id='horarios'>
    <tr class='title'>
        <th>Asesor</th><th>Departamento</th>
        <?php
        $query="SELECT DISTINCT Fecha FROM Fechas WHERE Fecha>='$from' AND Fecha<='$to'";
        $resultf=mysql_query($query);
        $numf=mysql_numrows($resultf);
        $x=0;
        $csv_hdr="Asesor,Departamento,Tipo,";
        while($x<$numf){
             echo "\t\t<td>".date('D', strtotime(mysql_result($resultf,$x,'Fecha')))."<br>".mysql_result($resultf,$x,'Fecha')."</td>\n";
             if($x==($numf-1)){$sep="";}else{$sep=",";}
             $csv_hdr .= mysql_result($resultf,$x,'Fecha').$sep;
        $x++;
        }
        ?>

    </tr>
<?php
$i=0;
$y=0;
while($i<$num){

    $asesor[$y]=mysql_result($result,$i,'Nombre');
    $corto[$y]=mysql_result($result,$i,'N Corto');
    $dep[$y]=mysql_result($result,$i,'Departamento');
    $as_id[$y]=mysql_result($result,$i,'id_asesor');
    $x=0;
    while(mysql_result($result,$i,'Nombre') == $asesor[$y]){
        $fecha[$y][$x]=mysql_result($result,$i,'Fecha');
        $horario_in[$y][$x]=mysql_result($result,$i,'jornada start');
        $horario_out[$y][$x]=mysql_result($result,$i,'jornada end');
        $hid[$y][$x]=mysql_result($result,$i,'id_hora');
        $sesion_in[$y][$x]=mysql_result($result,$i,'Hora_in');
        $sesion_out[$y][$x]=mysql_result($result,$i,'Hora_out');
        $aus_desc[$y][$x]=mysql_result($result,$i,'Descansos');
        $aus_benef[$y][$x]=mysql_result($result,$i,'Beneficios');
        $aus_aus[$y][$x]=mysql_result($result,$i,'Ausentismo');
        $aus_code[$y][$x]=mysql_result($result,$i,'Code');
        $retardo[$y][$x]=mysql_result($result,$i,'Codigo');
        $nota[$y][$x]=mysql_result($result,$i,'Nota');
        $calc_ret[$y][$x]=mysql_result($result,$i,'Retardos');
        $ingreso[$y][$x]=mysql_result($result,$i,'Ingreso');
        if(date('I',strtotime($fecha[$y][$x]))==0 && $calc_ret[$y][$x]!=NULL){$calc_ret[$y][$x]=$calc_ret[$y][$x]+60;}


    $i++;
    $x++;
    }


$y++;
}
$z=0;
foreach($asesor as $key => $name){
    if($key % 2 == 0){$class='pair';}else{$class='odd';}
    echo "<tr class='$class'><td>$name</td><td>$dep[$key]</td>";
    $x=0;
    $row1="";
    $row2="";
    $row3="";
    while($x<$numf){
        $tmp_class="editable";
        if($x==($numf-1)){$separator="\n";}else{$separator=",";}

        if($horario_in[$key][$x]=="00:00:00" && $horario_out[$key][$x]="00:00:00"){
            $tmp_turno="Descanso";}else{$tmp_turno=$horario_in[$key][$x]." - ".$horario_out[$key][$x];}
        if($sesion_in[$key][$x]!=NULL){$tmp_asistencia="A";}else{$tmp_asistencia="";}
        if($tmp_asistencia=="A"){
            if($calc_ret[$key][$x]>0 && $calc_ret[$key][$x]!=NULL){
            $tmp_retardo="RT";}else{$tmp_retardo="";}
            if($retardo[$key][$x]!=NULL){$tmp_retardo=$retardo[$key][$x];}
        }else{$tmp_retardo="";}
        if($tmp_asistencia==""){$tmp_asistencia=$aus_code[$key][$x];}
        if($tmp_asistencia=="" && $tmp_turno!="Descanso"){$tmp_asistencia="FA";}
        if($tmp_asistencia=="" && $tmp_turno=="Descanso"){$tmp_asistencia="D";}
        if($retardo[$key][$x]=="F"){$tmp_asistencia="F";}
        if($tmp_retardo=="RT" && $showret=="checked"){$tmp_class="orange";}
        if($tmp_turno=="Descanso" && $tmp_asistencia=="A" && $showexc=="checked"){$tmp_class="flashred";}

        if(strtotime($ingreso[$key][$x])>strtotime($fecha[$key][$x])){$tmp_asistencia="*";}
        if($tmp_asistencia=="FA" && strtotime($fecha[$key][$x])>=strtotime('Today')){$tmp_asistencia="";}
        if($tmp_asistencia=="FA" && $showexc=="checked"){$tmp_class="rojo";}

        if($showh=="checked"){$show="$tmp_turno<br>"; $row1 .=$tmp_turno.$separator;}else{$show="";}
        if($showexc=="checked"){$exce="$tmp_asistencia<br>"; $row2 .=$tmp_asistencia.$separator;}else{$exce="";}

        if($showret=="checked"){$sret=$tmp_retardo."<br>"; $row3 .=$tmp_retardo.$separator;}else{$sret="";}
        echo "\t\t<td class='$tmp_class' id='$z' ncorto='$corto[$key]' horaid='".$hid[$key][$x]."' nameid='$as_id[$key]' fecha='".$fecha[$key][$x]."'>$show $exce $sret<z id='a$z'></z></td>\n";
        $z++;
    $x++;
    }
    if($showh=="checked"){$csv_output.="$corto[$key],$dep[$key],Horario,".$row1;}
    if($showexc=="checked"){$csv_output.="$corto[$key],$dep[$key],Excepcion,".$row2;}
    if($showret=="checked"){$csv_output.="$corto[$key],$dep[$key],Retardos,".$row3;}

    echo "</tr>";

}

?>
</table>
<form action="http://pt.comeycome.com/common/exportcsv.php" method="post" name="export"><input type="submit" value="Export" />
<input type="hidden" name="csv_hdr" value="<? echo $csv_hdr; ?>" />
<input type="hidden" name="csv_output" value="<? echo $csv_output; ?>" /></form>
<?php

?>


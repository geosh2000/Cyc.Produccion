<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="payroll";
$menu_programaciones="class='active'";

include("../connectDB.php");
include("../common/scripts.php");
include("../common/listAsesores.php");

//Get Variables
if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d', strtotime('-15 days'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d', strtotime('-1 days'));}
$showh="checked";
if(isset($_POST['submit']) && !isset($_POST['showh'])){$showh="";}
$showexc="checked";
if(isset($_POST['submit']) && !isset($_POST['submit'])){$showexc="";}
if(isset($_POST['showret'])){$showret="checked";}

?>

<script>
  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "-2w",
      maxDate: "+0d",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );

      }
    });
    $( "#to" ).datepicker({
      defaultDate: "0",
      maxDate: "+0d",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

  });
  </script>

<?php
include("../common/menu.php");
?>
<table width=100% class='t2'><form action="<?php $_SERVER['PHP_SELF'] ?>" method='post'>
    <tr class='title'>
        <th colspan=100>Consulta de Asistencia CC</th>
    </tr>
    <tr class='title'>
        <th>Fecha Inicio:</th>
        <td class='pair'><input type="text" id="from" name="from" value='<?php echo $from ?>'></td>
        <th>Fecha Fin:</th>
        <td class='pair'><input type="text" id="to" name="to" value='<?php echo $to ?>'></td>
        <th>Mostrar</th>
        <td class='pair' style='text-align:right'><label for="showh">Horarios</label><input type="checkbox" id="showh" name="showh" <?php echo $showh ?>><br>
                        <label for="showexc">Excepciones</label><input type="checkbox" id="showexc" name="showexc" <?php echo $showexc ?>><br>
                        <label for="showret">Retardos</label><input type="checkbox" id="showret" name="showret" <?php echo $showret ?> disabled>
        </td>
        <td class='total'><input type="submit" name="submit" value="consulta" /></td>
    </tr>
</form></table>
<br><br>

<?php
if(!isset($_POST['submit'])){exit;}

$query="SELECT
	*
	FROM
	(SELECT
	Dates.Fecha as Fecha, Dates.id as id_asesor, Dates.`id Departamento` as Departamento, Dates.Nombre, Dates.`N Corto` as 'N Corto', Histo.`jornada start`as 'jornada start', Histo.`jornada end` as 'jornada end'
	FROM
		(
		SELECT
		Fecha, id, Nombre, `N Corto`, `id Departamento`
		FROM
			Fechas
		JOIN
			Asesores
		WHERE
			Fecha>='$from' AND
			Fecha<='$to' AND
			Activo=1
		ORDER BY
			`N Corto`
		) as Dates
	LEFT JOIN
		(SELECT
		asesor as id_asesor, `N Corto`, Fecha, `jornada start`, `jornada end`
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

	ORDER BY
		`N Corto`, Fecha";
$result=mysql_query($query);
$num=mysql_numrows($result);
?>


<table width=100% class='t2'>
    <tr class='title'>
        <th>Asesor</th>
        <?php
        $query="SELECT DISTINCT Fecha_in FROM t_Sesiones WHERE Fecha_in>='$from' AND Fecha_in<='$to'";
        $resultf=mysql_query($query);
        $numf=mysql_numrows($resultf);
        $x=0;
        while($x<$numf){
             echo "\t\t<td>".date('D', strtotime(mysql_result($resultf,$x,'Fecha_in')))."<br>".mysql_result($resultf,$x,'Fecha_in')."</td>\n";
        $x++;
        }
        ?>

    </tr>
<?php
$i=0;
$y=0;
while($i<$num){

    $asesor[$y]=mysql_result($result,$i,'Nombre');
    $x=0;
    while(mysql_result($result,$i,'Nombre') == $asesor[$y]){
        $fecha[$y][$x]=mysql_result($result,$i,'Fecha');
        $horario_in[$y][$x]=mysql_result($result,$i,'jornada start');
        $horario_out[$y][$x]=mysql_result($result,$i,'jornada end');
        $sesion_in[$y][$x]=mysql_result($result,$i,'Hora_in');
        $sesion_out[$y][$x]=mysql_result($result,$i,'Hora_out');
        $aus_desc[$y][$x]=mysql_result($result,$i,'Descansos');
        $aus_benef[$y][$x]=mysql_result($result,$i,'Beneficios');
        $aus_aus[$y][$x]=mysql_result($result,$i,'Ausentismo');
        $aus_code[$y][$x]=mysql_result($result,$i,'Code');

    $i++;
    $x++;
    }


$y++;
}

foreach($asesor as $key => $name){
    if($key % 2 == 0){$class='pair';}else{$class='odd';}
    echo "<tr class='$class'><td>$name</td>";
    $x=0;
    while($x<$numf){
        if($horario_in[$key][$x]=="00:00:00" && $horario_out[$key][$x]="00:00:00"){
            $tmp_turno="Descanso";}else{$tmp_turno=$horario_in[$key][$x]." - ".$horario_out[$key][$x];}
        if($sesion_in[$key][$x]!=NULL){$tmp_asistencia="A";}else{$tmp_asistencia="";}
        if($tmp_asistencia==""){$tmp_asistencia=$aus_code[$key][$x];}
        if($tmp_asistencia=="" && $tmp_turno!="Descanso"){$tmp_asistencia="FA";}
        if($tmp_asistencia=="" && $tmp_turno=="Descanso"){$tmp_asistencia="D";}
        if($tmp_turno=="Descanso" && $tmp_asistencia=="A"){$tmp_class="rojo";}else{$tmp_class="";}
        if($showh=="checked"){$show="$tmp_turno<br>";}else{$show="";}
        if($showh=="checked"){$exce="$tmp_asistencia<br>";}else{$exce="";}
        echo "\t\t<td class='$tmp_class'>$show $exce</td>\n";
    $x++;
    }
    echo "</tr>";

}

?>
</table>

<?php

?>


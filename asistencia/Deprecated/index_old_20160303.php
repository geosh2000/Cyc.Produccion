<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Mexico_City');
$credential="payroll";
$menu_programaciones="class='active'";

?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
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
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
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
  <script>
  $(function() {
     $(  '#0, sh:gt(0):lt(5000)' ).tooltip({

        track: true,
        show: {
            effect: "slideDown",
            delay: 250
        }
    });

    $('#horarios').tablesorter({

            theme: 'blue',

            sortList: [[0,0],[1,0]],

            headerTemplate: '{content}',

            stickyHeaders: "tablesorter-stickyHeader",

            cssChildRow : "tablesorter-childRow",

            // fix the column widths

            widthFixed: false,

            widgets: [ 'zebra','filter','output'],

            widgetOptions: {

               uitheme: 'jui',

               columns: [

                    "primary",

                    "secondary",

                    "tertiary"

                    ],

                columns_thead: true,

                filter_childRows: true,

                filter_columnFilters: true,

                filter_cssFilter: "tablesorter-filter",

                filter_functions: null,

                filter_hideFilters: false,

                filter_ignoreCase: true,

                filter_reset: null,

                filter_searchDelay: 300,

                filter_startsWith: false,

                filter_useParsedData: false,

                resizable: true,

                saveSort: true,

                stickyHeaders: "tablesorter-stickyHeader",

                 output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')

                  output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)

                  output_hiddenColumns : false,       // include hidden columns in the output

                  output_includeFooter : true,        // include footer rows in the output

                  output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text

                  output_headerRows    : true,        // output all header rows (multiple rows)

                  output_delivery      : 'd',         // (p)opup, (d)ownload

                  output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function

                  output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan

                  output_replaceQuote  : '\u201c;',   // change quote to left double quote

                  output_includeHTML   : false,        // output includes all cell HTML (except the header cells)

                  output_trimSpaces    : true,       // remove extra white-space characters from beginning & end

                  output_wrapQuotes    : false,       // wrap every cell output in quotes

                  output_popupStyle    : 'width=580,height=310',

                  output_saveFileName  : 'nomina_cc_cun_<?php echo date('Ymd',strtotime($from))."-".date('Ymd',strtotime($to)); ?>.csv',

                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required

                  output_encoding      : 'data:application/octet-stream;charset=utf8,'



            }

        });

         $('#Exporttable').click(function(){

        $('#horarios').trigger('outputTable');



    });

  });
  </script>
  <style>
    .ui-tooltip {
    width: 120px;
    height: auto;
    padding: 10px 20px;
    color: black;
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-align: center;
    box-shadow: 0 0 7px black;
  }
</style>


<?php
if($_SESSION['monitor_pya_exceptions']==1){include("../common/add_exception.php");}
include("../common/menu.php");

?>

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
	Sesiones.asesor, Fecha_in, Hora_in, Fecha_out, Hora_out, Excepciones.asesor, Inicio, Fin, Descansos, Beneficios, Ausentismo, Code, Comments, aus_user,
	caso, Nota, Codigo, IF(`jornada start`!='00:00:00' AND `jornada end`!='00:00:00',(TIME_TO_SEC(Hora_in)-TIME_TO_SEC(`jornada start`))/60,NULL) as Retardos, PyAAus, pya_user, pya_caso
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
	asesor, Inicio, Fin, Descansos, Beneficios, Ausentismo, Code, Comments, username as aus_user
	FROM
		Ausentismos a,
		`Tipos Ausentismos` b,
		userDB c
	WHERE
		a.tipo_ausentismo=b.id AND
		a.User=c.userid)
        Excepciones
	ON
		Horarios.id_asesor=Excepciones.asesor AND
		Horarios.Fecha BETWEEN Excepciones.Inicio AND Excepciones.Fin
	LEFT JOIN
		(SELECT
	horario_id, caso, Nota, b.Excepcion as PyAAus, Codigo, username as pya_user, caso as pya_caso
	FROM
		PyA_Exceptions a,
		`Tipos Excepciones`b,
		userDB c

	WHERE
		a.tipo=b.exc_type_id AND
		a.changed_by=c.userid
        ) as Retardos
	ON
		id_hora=horario_id

	ORDER BY
		`Nombre`, Fecha";
$result=mysql_query($query);
$num=mysql_numrows($result);


?>

<button type="button" class='button button_blue_w'id="Exporttable">Export</button>
<br>
<table width=100% id='horarios' class='t2' style='text-align: center;'>
    <thead>
    <tr>
        <th>Asesor</th><th>Departamento</th>
        <?php
        $query="SELECT DISTINCT Fecha FROM Fechas WHERE Fecha>='$from' AND Fecha<='$to'";
        $resultf=mysql_query($query);
        $numf=mysql_numrows($resultf);
        $x=0;
        $csv_hdr="Asesor,Departamento,Tipo,";
        while($x<$numf){
             echo "\t\t<th>".date('D', strtotime(mysql_result($resultf,$x,'Fecha')))."<br>".mysql_result($resultf,$x,'Fecha')."</th>\n";
             if($x==($numf-1)){$sep="";}else{$sep=",";}
             $csv_hdr .= mysql_result($resultf,$x,'Fecha').$sep;
        $x++;
        }
        ?>

    </tr>
    </thead>
    <tbody>
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
        $comments[$y][$x]=mysql_result($result,$i,'Comments');
        $pyaaus[$y][$x]=mysql_result($result,$i,'PyAAus');
        $calc_ret[$y][$x]=mysql_result($result,$i,'Retardos');
        $ingreso[$y][$x]=mysql_result($result,$i,'Ingreso');
        $aus_user[$y][$x]=mysql_result($result,$i,'aus_user');
        $pya_user[$y][$x]=mysql_result($result,$i,'pya_user');
        $pya_caso[$y][$x]=mysql_result($result,$i,'pya_caso');
        if(date('I',strtotime($fecha[$y][$x]))==0 && $calc_ret[$y][$x]!=NULL){$calc_ret[$y][$x]=$calc_ret[$y][$x]+60;}


    $i++;
    $x++;
    }


$y++;
}
$z=0;
foreach($asesor as $key => $name){
    if($key % 2 == 0){$class='pair';}else{$class='odd';}
    echo "<tr><td style='text-align:left;'>$name</td><td>$dep[$key]</td>";
    $x=0;
    $row1="";
    $row2="";
    $row3="";
    while($x<$numf){
        $tmp_class="editable";
        $title="";
        if($x==($numf-1)){$separator="\n";}else{$separator=",";}

        if($horario_in[$key][$x]=="00:00:00" && $horario_out[$key][$x]="00:00:00"){
            $tmp_turno="Descanso";}else{$tmp_turno=$horario_in[$key][$x]." - ".$horario_out[$key][$x];}
        if($sesion_in[$key][$x]!=NULL){$tmp_asistencia="A";}else{$tmp_asistencia="";}
        if($tmp_asistencia=="A"){
            if($calc_ret[$key][$x]>=1 && $calc_ret[$key][$x]!=NULL){
            $tmp_retardo="RT";}else{$tmp_retardo="";}
            if($retardo[$key][$x]!=NULL){$tmp_retardo=$retardo[$key][$x];}
        }else{$tmp_retardo="";}
        if($tmp_asistencia==""){$tmp_asistencia=$aus_code[$key][$x];}
        if($tmp_asistencia=="" && $tmp_turno!="Descanso"){$tmp_asistencia="FA";}
        if($tmp_asistencia=="" && $tmp_turno=="Descanso"){$tmp_asistencia="D";}
        if($tmp_asistencia=="INC" && $tmp_turno=="Descanso"){$tmp_asistencia="D";}
        if($retardo[$key][$x]=="FA"){

            $tmp_asistencia="FA";}
        if($tmp_retardo=="RT" && $showret=="checked"){$tmp_class="orange";}
        if($tmp_retardo=="RJ" && $showret=="checked"){$tmp_class="green";}
        if($tmp_turno=="Descanso" && $tmp_asistencia=="A" && $showexc=="checked"){$tmp_class="flashred";}

        if(strtotime($ingreso[$key][$x])>strtotime($fecha[$key][$x])){$tmp_asistencia="*";}
        if($tmp_asistencia=="FA" && strtotime($fecha[$key][$x])>=strtotime('Today')){$tmp_asistencia="";}
        if($tmp_asistencia=="FA" && $showexc=="checked"){$tmp_class="rojo";}

        if($showh=="checked"){$show="$tmp_turno<br>"; $row1 .=$tmp_turno.$separator;}else{$show="";}
        if($showexc=="checked"){$exce="$tmp_asistencia<br>"; $row2 .=$tmp_asistencia.$separator;}else{$exce="";}

        if($showret=="checked"){$sret=$tmp_retardo."<br>"; $row3 .=$tmp_retardo.$separator;}else{$sret="";}

        if($showexc=="checked" && $tmp_asistencia!="A"){
            if($retardo[$key][$x]=="FA"){
                $title.=" // ".$pyaaus[$key][$x]." asignado por ".$pya_user[$key][$x]." (".$nota[$key][$x].")";
            }elseif($aus_aus[$key][$x]!=NULL){
                $title.=" // ".$aus_aus[$key][$x]." asignado por ".$aus_user[$key][$x]." (".$comments[$key][$x].")";

                }

        }
        if($showret=="checked" && $tmp_retardo!=""){
               if($tmp_retardo=="RT"){
                   $title.=" // Retardo: (".$calc_ret[$key][$x]." min.)";
                   if($pyaaus[$key][$x]!=NULL){
                   $title.=" // ".$pyaaus[$key][$x]." asignado por: ".$pya_user[$key][$x]." (".$nota[$key][$x].")";
                   }
               }elseif($tmp_retardo=="RJ"){
                   $title.=" // Retardo Justificado por: ".$pya_user[$key][$x]." en caso: ".$pya_caso[$key][$x]." (".$nota[$key][$x].")";
               }
        }
        echo "\t\t<td title='$title'><sh style='width:100%' class='$tmp_class' id='$z' ncorto='$corto[$key]' horaid='".$hid[$key][$x]."' nameid='$as_id[$key]' fecha='".$fecha[$key][$x]."'>$show $exce $sret<z id='a$z'></z></sh></td>\n";
        $z++;
    $x++;
    }
    //if($showh=="checked"){$csv_output.="$asesor[$key],$dep[$key],Horario,".$row1;}
    //if($showexc=="checked"){$csv_output.="$asesor[$key],$dep[$key],Excepcion,".$row2;}
    //if($showret=="checked"){$csv_output.="$asesor[$key],$dep[$key],Retardos,".$row3;}

    echo "</tr>";

}

?>
</tbody>
</table>


<?php

?>
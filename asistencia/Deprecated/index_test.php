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
	Esquema,
	Fecha,
	DOW,
	DATEDIFF(Fin, Inicio) as d_ausentismo,
	id as id_asesor,
	id_Dep,
	Departamento,
	`N Corto`,
	Nombre,
	Ingreso,
	Programacion_id as id_hora,
	LogAsesor(Fecha, id, 'in') as Hora_in,
	LogAsesor(Fecha, id, 'out') as Hora_out,
	`jornada start`,`jornada end`,
	Inicio, Fin, Descansos, Beneficios, Ausentismo, Code, Comments, aus_user,
	pya_caso as caso, Nota, Codigo,
	IF(`jornada start`!='00:00:00' AND `jornada end`!='00:00:00',(TIME_TO_SEC(LogAsesor(Fecha, id, 'in'))-TIME_TO_SEC(`jornada start`))/60,NULL) as Retardos,PyAAus, pya_user, pya_caso

	FROM
		(
			SELECT
				Fecha, if(DAYOFWEEK(Fecha)=1,7,DAYOFWEEK(Fecha)-1) as DOW
			FROM
				Fechas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
		) Fechas
	JOIN
		(
			SELECT
				a.id, Nombre, `N Corto`, num_colaborador, Esquema, `id Departamento` as id_Dep, Departamento, Ingreso
			FROM
				Asesores a
			LEFT JOIN
				PCRCs b
			ON
				a.`id Departamento`=b.id
			WHERE
				Activo=1 $sel_dep
		) Asesores
	LEFT JOIN
		(
			SELECT
				Fecha as Programacion_Fecha, id as Programacion_id, asesor as Programacion_asesor, `jornada start`, `jornada end`
			FROM
				`Historial Programacion`
			WHERE
				Fecha BETWEEN '$from' AND '$to'
		) Programacion
	ON
		Fecha=Programacion_Fecha AND
		id=Programacion_asesor
	LEFT JOIN
		(
			SELECT
				Fecha as Ausentismos_Fecha, asesor as Ausentismos_asesor, Inicio, Fin,Ausentismo, Code, Comments, Descansos,Beneficios, username as Aus_user
			FROM
				Fechas a
			LEFT JOIN
				Ausentismos b
			ON
				Fecha BETWEEN Inicio AND Fin
			LEFT JOIN
				`Tipos Ausentismos` c
			ON
				b.tipo_ausentismo=c.id
			LEFT JOIN
				userDB d
			ON
				b.`user`=d.userid
			WHERE
				Fin >= '$from'
		) Ausentismos
	ON
		Fecha=Ausentismos_Fecha AND
		id=Ausentismos_asesor
	LEFT JOIN
		(
			SELECT
				horario_id as Retardos_id, asesor as Retardos_asesor, caso as pya_caso, Nota, Codigo, username as pya_user, Excepcion as PyAAus
			FROM
				PyA_Exceptions a
			LEFT JOIN
				`Tipos Excepciones` b
			ON
				a.tipo=b.exc_type_id
			LEFT JOIN
				userDB c
			ON
				a.changed_by=c.userid

		) Retardos
	ON
		Programacion_id=Retardos_id AND
		id=Retardos_asesor
	ORDER BY
		Nombre, Fecha";

$result=mysql_query($query);

$num=mysql_numrows($result);

//echo "$query<br>";



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

        $v_inicio[$y][$x]=mysql_result($result,$i,'Inicio');

        $v_fin[$y][$x]=mysql_result($result,$i,'Fin');

        $v_descansos[$y][$x]=mysql_result($result,$i,'Descansos');

        $v_beneficios[$y][$x]=mysql_result($result,$i,'Beneficios');

        $v_esquema[$y][$x]=mysql_result($result,$i,'Esquema');

        $v_dow[$y][$x]=mysql_result($result,$i,'DOW');

        $v_d_aus[$y][$x]=mysql_result($result,$i,'d_ausentismo');

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



        //Ausentismos Programados

        if($tmp_asistencia==""){

            switch($v_esquema[$key][$x]){

                case 10:

                    if($v_d_aus[$key][$x]-$v_descansos[$key][$x]-$v_beneficios[$key][$x]<5){

                        if($v_fin[$key][$x]==$fecha[$key][$x] || $v_inicio[$key][$x]==$fecha[$key][$x])

                            {

                                if($v_descansos[$key][$x]!=0){$tmp_asistencia="D";}else{$tmp_asistencia=$aus_code[$key][$x];}

                            }

                        else

                            {$tmp_asistencia=$aus_code[$key][$x];}

                    }else{

                        $v_difdays=date('z',strtotime($fecha[$key][$x]))-date('z',strtotime($v_inicio[$key][$x]));

                        $v_domingos=intval($v_difdays/7);

                        $v_thisdow=$v_difdays%7;

                        $v_tomadas=($v_difdays-intval($v_difdays/7))-(intval(($v_difdays-intval($v_difdays/7))/5));

                        $v_ben_tom=intval($v_tomadas/5);

                        if(($v_dow[$key][$x]==7 || $v_dow[$key][$x]==6) && $v_domingos<$v_descansos[$key][$x])

                            {$tmp_asistencia="D";}

                        else

                            {

                                $tmp_asistencia=$aus_code[$key][$x];

                            }

                    }

                    break;

                default:

                    if($v_d_aus[$key][$x]-$v_descansos[$key][$x]-$v_beneficios[$key][$x]<5){

                        if($v_fin[$key][$x]==$fecha[$key][$x])

                            {

                                if($v_descansos[$key][$x]!=0){$tmp_asistencia="D";}else{$tmp_asistencia=$aus_code[$key][$x];}

                            }

                        else

                            {$tmp_asistencia=$aus_code[$key][$x];}

                    }else{

                        $v_difdays=date('z',strtotime($fecha[$key][$x]))-date('z',strtotime($v_inicio[$key][$x]));

                        $v_domingos=intval($v_difdays/7);

                        $v_thisdow=$v_difdays%7;

                        $v_tomadas=($v_difdays-intval($v_difdays/7))-(intval(($v_difdays-intval($v_difdays/7))/5));

                        $v_ben_tom=intval($v_tomadas/5);

                        if($v_dow[$key][$x]==7 && $v_domingos<$v_descansos[$key][$x])

                            {$tmp_asistencia="D";}

                        else

                            {

                                if($v_ben_tom<$v_beneficios[$key][$x] && $v_dow[$key][$x]==6)

                                    {$tmp_asistencia="B";}

                                else

                                    {$tmp_asistencia=$aus_code[$key][$x];}

                            }

                    }

                    break;

            }

        }



        //Faltas y Descansos

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


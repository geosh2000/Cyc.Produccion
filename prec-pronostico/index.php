<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$menu_asesores="class='active'";
date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/erlangC.php");


if(isset($_GET['inicio'])){$fecha_i=date('Y-m-d',strtotime($_GET['inicio']));}else{$fecha_i=date('Y-m-d',strtotime('-7 days'));}
if(isset($_GET['fin'])){$fecha_f=date('Y-m-d',strtotime($_GET['fin']));}else{$fecha_f=date('Y-m-d',strtotime('-1 days'));}

function parameters($skill){
    global $aht, $tat, $slr;
    switch($skill){
        case 3:
    		$aht=500;
            $tat=20;
            $slr=0.8;
    		break;
    	case "4":
    		$aht=600;
            $tat=30;
            $slr=0.7;
    		break;
    	case "9":
    		$aht=600;
            $tat=30;
            $slr=0.7;
    		break;
    	case "8":
    		$aht=250;
            $tat=30;
            $slr=0.7;
    		break;
    	case "13":
    		$aht=600;
            $tat=30;
            $slr=0.7;
    		break;
        case "7":
    		$aht=600;
            $tat=30;
            $slr=0.7;
    		break;
		case 35:
    		$aht=500;
            $tat=20;
            $slr=0.8;
    		break;
    }

}




//echo $query."<br>";
//print_r($data);
include("../common/menu.php");

?>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script>
$(function(){

    $('#data').accordion({
        collapsible: true,
        heightStyle: 'content'
    });

    $('#info').tablesorter({
            theme: 'blue',
            sortList: [[0]],
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output', 'stickyHeaders'],
            widgetOptions: {

               uitheme: 'jui',
                columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_tfoot: false,
                columns_thead: true,
                filter_childRows: false,
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
                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                output_wrapQuotes    : false,       // wrap every cell output in quotes
                output_popupStyle    : 'width=580,height=310',
                output_saveFileName  : 'precision_detalle<?php echo $fecha_i."_".$fecha_f;?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                stickyHeaders_attachTo : '#intervalContainer'
            }
        });

        $('#info_resumen').tablesorter({
            theme: 'blue',
            sortList: [[0]],
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output', 'stickyHeaders'],
            widgetOptions: {

               uitheme: 'jui',
                columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_tfoot: false,
                columns_thead: true,
                filter_childRows: false,
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
                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                output_wrapQuotes    : false,       // wrap every cell output in quotes
                output_popupStyle    : 'width=580,height=310',
                output_saveFileName  : 'precision_resumen_diario<?php echo $fecha_i."_".$fecha_f;?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                stickyHeaders_attachTo : '#intervalContainer_resumen'
            }
        });

        $('#info_resumen_total').tablesorter({
            theme: 'blue',
            sortList: [[0]],
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output', 'stickyHeaders'],
            widgetOptions: {

               uitheme: 'jui',
                columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_tfoot: false,
                columns_thead: true,
                filter_childRows: false,
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
                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                output_wrapQuotes    : false,       // wrap every cell output in quotes
                output_popupStyle    : 'width=580,height=310',
                output_saveFileName  : 'precision_resumen_rango<?php echo $fecha_i."_".$fecha_f;?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                stickyHeaders_attachTo : '#intervalContainer_resumen_total'
            }
        });

        $('#export_detalle').click(function(){
            $('#info').trigger('outputTable');
        });

        $('#export_diario').click(function(){
            $('#info_resumen').trigger('outputTable');
        });

        $('#export_rango').click(function(){
            $('#info_resumen_total').trigger('outputTable');
        });

        $( "#inicio" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#fin" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#fin" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

});
</script>
<table class='t2' width='100%'>
    <tr>
        <th class='title' colspan=100>Precision de Pronosticos</th>
    </tr>
    <tr class='title'><form action='<?php $_SERVER['PHP_SELF']; ?>' method='GET'>
        <td>Fecha Inicio</td>
        <td><input type='text' name='inicio' id='inicio' value='<?php echo $fecha_i; ?>'></td>
        <td>Fecha Fin</td>
        <td><input type='text' name='fin' id='fin' value='<?php echo $fecha_f; ?>'></td>
        <td><input type='submit' class='button button_blue_w' id='consulta' name='consulta' value='Consultar'></td>
    </form></tr>
</table>

<?php if(!isset($_GET['consulta'])){exit;}

$query="SELECT
	Semana, DiaSemana, factores.Fecha, skill, PCRCs.Departamento, HoraGroup,
	fc_CallsOffered*(CASE skill when '3' then forecast_3
										when '4' then forecast_4
										when '7' then forecast_7
										when '8' then forecast_8
										when '9' then forecast_9
										when '13' then forecast_13
										when '35' then forecast_35
										END) as CallsForecasted,
	CallsOffered,
	COUNT(asesor) as Programados
FROM
	(
		SELECT * FROM Fechas WHERE Fecha BETWEEN '$fecha_i' AND '$fecha_f'
	) factores
LEFT JOIN
	(
		SELECT
			WEEKOFYEAR(Fecha) as Semana,DAYOFWEEK(Fecha) as DiaSemana, Fecha,
			CONCAT(HOUR(Hora),IF(MINUTE(Hora)>=30,'.5','.0')) as HoraGroup,
			COUNT(ac_id) as CallsOffered,
			skill, a.Cola
		FROM
			t_Answered_Calls a
		LEFT JOIN
			Cola_Skill b
		ON
			a.Cola=b.Cola
		WHERE
			Hora BETWEEN '09:00:00' AND '22:30:00' AND
			skill IN (3,4,7,8,9,13,35) AND
			Fecha BETWEEN '$fecha_i' AND '$fecha_f'
		GROUP BY
			Fecha, HoraGroup, skill
	) actual
ON actual.Fecha=factores.Fecha
LEFT JOIN
	(
		SELECT
			WEEKOFYEAR(Fecha)-1 as fc_Semana,DAYOFWEEK(Fecha) as fc_DiaSemana, Fecha as fc_Fecha,
			CONCAT(HOUR(Hora),IF(MINUTE(Hora)>=30,'.5','.0')) as fc_HoraGroup,
			COUNT(ac_id) as fc_CallsOffered,
			skill as fc_skill, a.Cola as fc_Cola
		FROM
			t_Answered_Calls a
		LEFT JOIN
			Cola_Skill b
		ON
			a.Cola=b.Cola
		WHERE
			Hora BETWEEN '09:00:00' AND '22:30:00' AND
			Fecha BETWEEN '".date('Y-m-d',strtotime($fecha_i.' -365 days'))."' AND '".date('Y-m-d',strtotime($fecha_f.' -350 days'))."'
		GROUP BY
			fc_Fecha, fc_HoraGroup, fc_skill
	) fc
ON
	Semana=fc_Semana AND
	DiaSemana=fc_DiaSemana AND
	HoraGroup=fc_HoraGroup AND
	skill=fc_skill
LEFT JOIN
	(
		SELECT
			Fecha as p_fecha, Inicio, Fin, asesor, skill as p_skill, if(Fecha<=Egreso OR Egreso IS NULL,1,0) as Activo
		FROM
			(
				SELECT
					Fecha
				FROM
					Fechas
				WHERE
					Fecha BETWEEN '2016-04-01' AND '2016-04-30'
			) Fechas
		LEFT JOIN
			(
				SELECT
					a.Fecha as Prog_Fecha,
					CONCAT(HOUR(IF(Verano=1,`jornada start`,ADDTIME(`jornada start`,'-01:00:00'))),IF(MINUTE(`jornada start`)>=30,'.5','.0')) as Inicio,
					CONCAT(IF(HOUR(IF(Verano=1,`jornada end`,ADDTIME(`jornada end`,'-01:00:00')))<4,HOUR(IF(Verano=1,`jornada end`,ADDTIME(`jornada end`,'-01:00:00')))+24,HOUR(IF(Verano=1,`jornada end`,ADDTIME(`jornada end`,'-01:00:00')))),IF(MINUTE(`jornada end`)>=30,'.5','.0')) as Fin,
					a.asesor, b.tipo_ausentismo
				FROM
					Fechas z
				LEFT JOIN
					`Historial Programacion` a
				ON
					z.Fecha=a.Fecha
				LEFT JOIN
					Ausentismos b
				ON
					a.Fecha BETWEEN b.Inicio AND b.Fin AND
					a.asesor=b.asesor
				WHERE
					tipo_ausentismo IS NULL AND
					a.Fecha BETWEEN '$fecha_i' AND '$fecha_f'  AND
					`jornada start` != `jornada end`

			) Prog
		ON
			Fecha=Prog_Fecha
		LEFT JOIN
			(
				SELECT
					id, `id Departamento` as skill, Egreso
				FROM
					Asesores
			) Asesores
		ON
			asesor=Asesores.id
		Having
			Activo=1
	) programacion
ON
	factores.Fecha=p_Fecha AND
	HoraGroup+0.2 BETWEEN Inicio AND Fin AND
	skill=p_skill
LEFT JOIN
    PCRCs
ON
    skill=id
GROUP BY
	factores.Fecha, HoraGroup, Skill

ORDER BY
    skill, actual.Fecha, HoraGroup
	";
$result=mysql_query($query);
if(mysql_error()){echo mysql_error()."<br>";}

$num=mysql_numrows($result);
$num_fields=mysql_num_fields($result);
$i=0;
while($i<$num){
    $data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'skill')][mysql_result($result,$i,'HoraGroup')]['forecast']=mysql_result($result,$i,'CallsForecasted');
    $data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'skill')][mysql_result($result,$i,'HoraGroup')]['offered']=mysql_result($result,$i,'CallsOffered');
    $data[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'skill')][mysql_result($result,$i,'HoraGroup')]['programed']=mysql_result($result,$i,'Programados');
    $skill[mysql_result($result,$i,'skill')]=mysql_result($result,$i,'Departamento');
$i++;
}

$query="SELECT Fecha, CONCAT(IF(area=2,'Mailing',IF(area=3,'Quejas',IF(area=1,'Confirming','Otro')))) as Area, participacion*fc_semana as fc_casos FROM forecast_participacion_bo WHERE Fecha BETWEEN '$fecha_i' AND '$fecha_f'";
$result=mysql_query($query);
if(mysql_error()){echo mysql_error()."<br>";}

$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $data_bo[mysql_result($result,$i,'Fecha')][mysql_result($result,$i,'Area')]['forecast']=mysql_result($result,$i,'fc_casos');

$i++;
}

$query="SELECT Fecha, COUNT(confirming_id) as casos FROM Fechas a LEFT JOIN (SELECT * FROM bo_mailing WHERE actividad!=8) b ON Fecha=fecha_recepcion WHERE Fecha BETWEEN '$fecha_i' AND '$fecha_f' GROUP BY Fecha";
$result=mysql_query($query);
if(mysql_error()){echo mysql_error()."<br>";}

$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $data_bo[mysql_result($result,$i,'Fecha')]['Mailing']['offered']=mysql_result($result,$i,'casos');
$i++;
}

$query="SELECT Fecha, COUNT(mejora_id) as casos FROM Fechas a LEFT JOIN (SELECT * FROM bo_mejora_continua) b ON Fecha=fecha_recepcion WHERE Fecha BETWEEN '$fecha_i' AND '$fecha_f' GROUP BY Fecha";
$result=mysql_query($query);
if(mysql_error()){echo mysql_error()."<br>";}

$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $data_bo[mysql_result($result,$i,'Fecha')]['Quejas']['offered']=mysql_result($result,$i,'casos');
$i++;
}

$query="SELECT Fecha, COUNT(confirming_id) as casos FROM Fechas a LEFT JOIN (SELECT * FROM bo_confirming) b ON Fecha=fecha_recepcion WHERE Fecha BETWEEN '$fecha_i' AND '$fecha_f' GROUP BY Fecha";
$result=mysql_query($query);
if(mysql_error()){echo mysql_error()."<br>";}

$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $data_bo[mysql_result($result,$i,'Fecha')]['Confirming']['offered']=mysql_result($result,$i,'casos');
$i++;
}

?>
<br>
<button class='button button_red_w' id='export_detalle'>Exportar<br>Detalle</button>
<button class='button button_red_w' id='export_diario'>Exportar<br>Diario</button>
<button class='button button_red_w' id='export_rango'>Exportar<br>Rango</button>
<br>
<div id='data'>
    <h3>Detalle</h3>
    <div style='max-height:700px; overflow: scroll; position: relative' id='intervalContainer'>
        <table id='info' style='text-align:center'>
            <thead>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Skill</th>
                <th>Llamadas<br>Ofrecidas</th>
                <th>Llamadas<br>Pronosticadas</th>
                <th>Precision<br>Pronostico</th>
                <th>Status<br>Precision</th>
                <th>Asesores<br>Programados</th>
                <th>Asesores<br>Necesarios</th>
                <th>Calidad<br>Programacion</th>
                <th>Status<br>Calidad</th>
            </thead>
            <tbody>
            <?php
                foreach($data as $datekey => $skillData){
                    foreach($skillData as $skillkey => $horas){
                        parameters($skillkey);
                        $i=18;
                        while($i<44){
                            $acumulado[$datekey][$skillkey]['offered']+=$horas[$index]['offered'];
                            $acumulado[$datekey][$skillkey]['forecast']+=$horas[$index]['forecast'];
                            if(intval($i/2)!=($i/2)){
                                $index=intval($i/2).".5";
                            }else{
                                $index=intval($i/2).".0";
                            }
                            $precision=number_format($horas[$index]['offered']/$horas[$index]['forecast'],2);
                            $needed=intval(agentno(($horas[$index]['forecast'])/1800*$aht,$tat,$aht,$slr));
                            $programed=$horas[$index]['programed'];
                            $calidad=number_format($horas[$index]['programed']/$needed,2);
                            if($calidad>1.2 || $calidad<0.85){$status_calidad="wrong"; $text_status_calidad="no";}else{$status_calidad="ok"; $text_status_calidad="ok"; $precision_acumulado[$datekey][$skillkey]['programacion']++;}
                            if($precision>1.15 || $precision<0.85){$status_precision="wrong"; $text_status_precision="no";}else{$status_precision="ok"; $text_status_precision="ok"; $precision_acumulado[$datekey][$skillkey]['pronostico']++; $total[$skillkey]['intervalo']++;}
                            echo "<tr><td>$datekey</td><td>$index</td><td>$skill[$skillkey]</td><td>".$horas[$index]['offered']."</td><td>".intval($horas[$index]['forecast'])."</td><td>$precision</td>
                                    <td>$text_status_precision <img src='../images/$status_precision.png' style='vertical-align:middle;' height='20' width='20'></td>
                                    <td>$programed</td><td>$needed</td><td>$calidad</td>
                                    <td>$text_status_calidad <img src='../images/$status_calidad.png' style='vertical-align:middle;' height='20' width='20'></td></tr>\n";
                        $i++;
                        }
                    }

                }
            ?>
            </tbody>
        </table>
    </div>
    <h3>Resumen Diario</h3>
    <div style='max-height:700px; overflow: scroll; position: relative' id='intervalContainer_resume'>
        <table id='info_resumen' style='text-align:center'>
            <thead>
                <th>Fecha</th>
                <th>Skill</th>
                <th>Intervalos OK<br>Pronostico</th>
                <th>Precision Intervalo<br>Pronostico</th>
                <th>Status Intervalo<br>Pronostico</th>
                <th>Precision Dia<br>Pronostico</th>
                <th>Status Dia<br>Pronostico</th>
                <th>Intervalos OK<br>Programacion</th>
                <th>Calidad<br>Programacion</th>
                <th>Status<br>Calidad</th>
            </thead>
            <tbody>
            <?php
                foreach($precision_acumulado as $p_datekey => $p_skillData){
                    foreach($p_skillData as $p_skillkey => $info){
                        $pr_pronostico=number_format($info['pronostico']/26,2);
                        $pr_programacion=number_format($info['programacion']/26,2);
                        $pr_dia=number_format($acumulado[$p_datekey][$p_skillkey]['offered']/$acumulado[$p_datekey][$p_skillkey]['forecast'],2);
                        if($pr_pronostico>1.15 || $pr_pronostico<0.85){$status_calidad="wrong"; $text_status_calidad="no";}else{$status_calidad="ok"; $text_status_calidad="ok";}
                        if($pr_programacion>1.2 || $pr_programacion<0.80){$status_precision="wrong"; $text_status_precision="no";}else{$status_precision="ok"; $text_status_precision="ok"; $total[$p_skillkey]['programacion']++;}
                        if($pr_dia>1.15 || $pr_dia<0.85){$status_precision_dia="wrong"; $text_status_precision_dia="no";}else{$status_precision_dia="ok"; $text_status_precision_dia="ok"; $total[$p_skillkey]['dia']++;}

                        echo "<tr><td>$p_datekey</td>
                                    <td>$skill[$p_skillkey]</td>
                                    <td>".$info['pronostico']."</td>
                                    <td>$pr_pronostico</td>
                                    <td>$text_status_precision <img src='../images/$status_precision.png' style='vertical-align:middle;' height='20' width='20'></td>
                                    <td>$pr_dia</td>
                                    <td>$text_status_precision_dia <img src='../images/$status_precision_dia.png' style='vertical-align:middle;' height='20' width='20'></td>
                                    <td>".$info['programacion']."</td>
                                    <td>$pr_programacion</td>
                                    <td>$text_status_calidad <img src='../images/$status_calidad.png' style='vertical-align:middle;' height='20' width='20'></td>\n";
                    }
                $dates++;
                }
            ?>
            <?php
                unset($fecha);
                foreach($data_bo as $fecha => $data2){
                    foreach($data2 as $area => $data3){
                        $prec=number_format($data3['offered']/$data3['forecast'],2);
                        if($prec>1.2 || $prec<0.80){$status_precision="wrong"; $text_status_precision="no";}else{$status_precision="ok"; $text_status_precision="ok"; $total_bo[$area]++;}
                        echo "<tr><td>$fecha</td>
                                <td>$area</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>$prec</td>
                                <td>$text_status_precision <img src='../images/$status_precision.png' style='vertical-align:middle;' height='20' width='20'></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                </tr>
                                ";
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
    <h3>Resumen Rango Seleccionado</h3>
    <div style='max-height:700px; overflow: scroll; position: relative' id='intervalContainer_resumen_total'>
        <table id='info_resumen_total' style='text-align:center'>
            <thead>
                <th>Skill</th>
                <th>Intervalos OK<br>Pronostico</th>
                <th>Intervalos Consultados<br>Pronostico</th>
                <th>Precision Total<br>Intervalos Pronostico</th>
                <th>Status Intervalo<br>Pronostico</th>
                <th>Dias OK<br>Pronostico</th>
                <th>Dias Consultados<br>Pronostico</th>
                <th>Precision Total<br>Dias Pronostico</th>
                <th>Status Dias<br>Pronostico</th>
                <th>Dias OK<br>Programacion</th>
                <th>Dias Consultados<br>Programacion</th>
                <th>Calidad Dias<br>Programacion</th>
                <th>Status P<br>Programacion</th>

            </thead>
            <tbody>
            <?php
                foreach($total as $t_skillkey => $t_info){
                        $int_pronostico=number_format($t_info['intervalo']/($dates*26),2);
                        $dia_pronostico=number_format($t_info['dia']/($dates),2);
                        $total_programacion=number_format($t_info['programacion']/($dates),2);
                        if($int_pronostico>1.2 || $int_pronostico<0.85){$status_calidad="wrong"; $text_status_calidad="no";}else{$status_calidad="ok"; $text_status_calidad="ok";}
                        if($total_programacion>1.3 || $total_programacion<0.7){$status_precision="wrong"; $text_status_precision="no";}else{$status_precision="ok"; $text_status_precision="ok";}
                        if($dia_pronostico>1.15 || $dia_pronostico<0.85){$status_precision_dia="wrong"; $text_status_precision_dia="no";}else{$status_precision_dia="ok"; $text_status_precision_dia="ok";}
                        echo "<tr><td>$skill[$t_skillkey]</td>
                                    <td>".$t_info['intervalo']."</td>
                                    <td>".($dates*26)."</td>
                                    <td>$int_pronostico</td>
                                    <td>$text_status_calidad <img src='../images/$status_calidad.png' style='vertical-align:middle;' height='20' width='20'></td>
                                    <td>".$t_info['dia']."</td>
                                    <td>$dates</td>
                                    <td>$dia_pronostico</td>
                                    <td>$text_status_precision_dia <img src='../images/$status_precision_dia.png' style='vertical-align:middle;' height='20' width='20'></td>
                                    <td>".$t_info['programacion']."</td>
                                    <td>$dates</td>
                                    <td>$total_programacion</td>
                                    <td>$text_status_precision <img src='../images/$status_precision.png' style='vertical-align:middle;' height='20' width='20'></td>\n";
                    }


            foreach($total_bo as $area =>$total2){
                $total_dia_bo=number_format($total2/$dates,2);
                if($total_dia_bo>1.15 || $total_dia_bo<0.85){$status_precision_dia="wrong"; $text_status_precision_dia="no";}else{$status_precision_dia="ok"; $text_status_precision_dia="ok";}
                echo "<tr><td>$area</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$total2</td>
                        <td>$dates</td>
                        <td>$total_dia_bo</td>
                        <td>$text_status_precision_dia <img src='../images/$status_precision_dia.png' style='vertical-align:middle;' height='20' width='20'></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<pre>
	<?php // print_r($data_bo); ?>
</pre>


























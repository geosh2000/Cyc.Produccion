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
//header("Content-Type: text/html;charset=utf-8");
include("../common/scripts.php");
include("../common/menu.php");
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
if($p_dep!="all"){
	$sel_dep=" AND `id Departamento`='$p_dep' ";
}else{
	$sel_dep=" AND `id Departamento` NOT IN (29,30,31) ";
}




?>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>
  $(function() {
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		animation: true
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
            widgets: [ 'zebra','filter','output', 'stickyHeaders'],
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


?>

<table style='width:600px; margin:auto' class='t2'><form action="<?php $_SERVER['PHP_SELF'] ?>" method='post'>
    <tr class='title'>
        <th colspan=100 id='demotitle'>Consulta de Asistencia CC</th>
    </tr>
    <tr class='title'>
        <th>Periodo:</th>
        <td class='pair'><input type="text" id="from" name="from" value='<?php echo $from ?>' required><input type="text" id="to" name="to" value='<?php echo $to ?>' required></td>
        <th>Departamento</th>
        <td class='pair'><select name="dep" required><option value=''>Selecciona</option>
        <?php

        function list_deps(){
            global $p_dep;
            $query="SELECT * FROM PCRCs WHERE parent=1 AND Departamento NOT LIKE '%pdv%' ORDER BY Departamento";
            $result=mysql_query($query);
            $num=mysql_numrows($result);
            for($i=0;$i<$num;$i++){
                if($p_dep==mysql_result($result,$i,'id')){$seldep=" selected";}else{$seldep=" ";}
                echo "<option value='".mysql_result($result,$i,'id')."' title='$p_dep'  $seldep >".mysql_result($result,$i,'Departamento')."</option>\n\t";
            }
        }

        list_deps();

        ?>
        <option value="all" <?php if($dep=="all"){echo "selected";}?>>Todos</option></select></td>
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


//echo "$query<br>";

//Query Asesores

$query="SELECT a.id, Nombre, `N Corto`, Departamento, num_colaborador FROM Asesores a LEFT JOIN PCRCs b ON a.`id Departamento`=b.id WHERE Activo=1 $sel_dep";
//echo "$query<br>";
$result=mysql_query($query);
$num=mysql_numrows($result);
//echo "$num<br>";
for($i=0;$i<$num;$i++){
	//echo "$i<br>";
	$data[mysql_result($result,$i,'id')]['Nombre']=mysql_result($result,$i,'Nombre');
	$data[mysql_result($result,$i,'id')]['N Corto']=mysql_result($result,$i,'N Corto');
	$data[mysql_result($result,$i,'id')]['Departamento']=mysql_result($result,$i,'Departamento');
	$data[mysql_result($result,$i,'id')]['num']=mysql_result($result,$i,'num_colaborador');
}

//Query Horarios

$query="SELECT asesor, Fecha, `jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`, `extra2 start`, `extra2 end`, a.id FROM `Historial Programacion` a LEFT JOIN Asesores b ON a.asesor=b.id LEFT JOIN PCRCs c ON b.`id Departamento`=c.id WHERE Fecha BETWEEN '$from' AND '$to' $sel_dep AND Activo=1";
$result=mysql_query($query);
$num=mysql_numrows($result);
for($i=0;$i<$num;$i++){
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['jstart']=mysql_result($result,$i,'jornada start');
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['jend']=mysql_result($result,$i,'jornada end');
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['cstart']=mysql_result($result,$i,'comida start');
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['cend']=mysql_result($result,$i,'comida end');
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['x1start']=mysql_result($result,$i,'extra1 start');
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['x1end']=mysql_result($result,$i,'extra1 end');
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['x2start']=mysql_result($result,$i,'extra2 start');
	$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['x2end']=mysql_result($result,$i,'extra2 end');
	$horario[mysql_result($result,$i,'id')]['Fecha']=mysql_result($result,$i,'Fecha');
	$horario[mysql_result($result,$i,'id')]['Asesor']=mysql_result($result,$i,'asesor');
	$fechas[mysql_result($result,$i,'Fecha')]=1;
}


//Query Sesiones

foreach($data as $asesor => $info){
	foreach($info['Fechas'] as $date => $tmp){
		$query="SELECT LogAsesor('$date',$asesor,'in') as login, LogAsesor('$date',$asesor,'out') as logout";
		$result=mysql_query($query);
		$data[$asesor]['Fechas'][$date]['login']=mysql_result($result,0,'login');
		$data[$asesor]['Fechas'][$date]['logout']=mysql_result($result,0,'logout');
		
		if($data[$asesor]['Fechas'][$date]['login']!=NULL){
			$data[$asesor]['Fechas'][$date]['Asistencia']='A';		
		}else{
			if($data[$asesor]['Fechas'][$date]['jstart']==$data[$asesor]['Fechas'][$date]['jend']){
				$data[$asesor]['Fechas'][$date]['Asistencia']='D';
			}else{
				$data[$asesor]['Fechas'][$date]['Asistencia']='FA';
			}
		}
		
		if($data[$asesor]['Fechas'][$date]['Asistencia']=='A' && date('H:i:s', strtotime($data[$asesor]['Fechas'][$date]['login']))>=date('H:i:s', strtotime($data[$asesor]['Fechas'][$date]['jstart']. '+1 minutes'))){
			$data[$asesor]['Fechas'][$date]['Retardo_Check']=1;	
		}else{
			$data[$asesor]['Fechas'][$date]['Retardo_Check']=0;
		}
	}
	unset($date,$tmp);
}
unset($asesor,$info);


//Query Excepciones

if($showexc=="checked"){
	
	$query="SELECT asesor, Fecha, Code, caso, Moper, username, Comments, Ausentismo, b.`Last Update` FROM Fechas a "
			."LEFT JOIN Ausentismos b ON a.Fecha BETWEEN Inicio AND Fin "
			."LEFT JOIN Asesores c ON b.asesor=c.id "
			."LEFT JOIN `Tipos Ausentismos` d ON b.tipo_ausentismo=d.id "
			."LEFT JOIN userDB e ON b.User=e.userid "
			."WHERE Fecha BETWEEN '$from' AND '$to' $sel_dep AND Activo=1";
	//echo "$query<br>";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	for($i=0;$i<$num;$i++){
		$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['Ausentismo']['Code']=mysql_result($result,$i,'Code');
		$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['Ausentismo']['Caso']=mysql_result($result,$i,'caso');
		$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['Ausentismo']['ISI']=mysql_result($result,$i,'Moper');
		$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['Ausentismo']['Fecha Asignacion']=mysql_result($result,$i,'Last Update');
		$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['Ausentismo']['Asignado']=mysql_result($result,$i,'username');
		$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['Ausentismo']['Comments']=mysql_result($result,$i,'Comments');
		$data[mysql_result($result,$i,'asesor')]['Fechas'][mysql_result($result,$i,'Fecha')]['Ausentismo']['Tipo']=mysql_result($result,$i,'Ausentismo');	
	}
}

//Query Retardos

if($showret=="checked"){
	foreach($horario as $id => $info){
		$query="SELECT * FROM PyA_Exceptions a LEFT JOIN `Tipos Excepciones` b ON a.tipo=b.exc_type_id LEFT JOIN userDB c ON a.changed_by=userid WHERE horario_id=$id";
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		if(mysql_error()){
			$data[$info['Asesor']]['Fechas'][$info['Fecha']]['Retardos']['Error']=mysql_error();	
		}else{
			if($num>0){
				$data[$info['Asesor']]['Fechas'][$info['Fecha']]['Retardos']['Comments']=mysql_result($result,0,'Nota');
				$data[$info['Asesor']]['Fechas'][$info['Fecha']]['Retardos']['Asignado']=mysql_result($result,0,'username');
				$data[$info['Asesor']]['Fechas'][$info['Fecha']]['Retardos']['Caso']=mysql_result($result,0,'caso');
				$data[$info['Asesor']]['Fechas'][$info['Fecha']]['Retardos']['Fecha Asignacion']=mysql_result($result,0,'Last Update');
				$data[$info['Asesor']]['Fechas'][$info['Fecha']]['Retardos']['Excepcion']=mysql_result($result,0,'Excepcion');
				$data[$info['Asesor']]['Fechas'][$info['Fecha']]['Retardos']['Codigo']=mysql_result($result,0,'Codigo');	
			}
		}
	}
	unset($id,$info);
}


?>
<pre>
	<?php print_r($data); ?>
</pre>
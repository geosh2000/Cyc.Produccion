<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];


if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="afiliados";

include("../connectDB.php");
include("../common/scripts.php");

//default timezone
date_default_timezone_set('America/Bogota');

//Get Variables
$dept=$_POST['dept'];
$name_dep=$_POST['ndep'];
$from=$_POST['from'];
if($from==NULL){$from=date('Y-m-d', strtotime('-5 days'));}else{$from=date('Y-m-d', strtotime($_POST['from']));  }
$to=$_POST['to'];
if($to==NULL){$to=date('Y-m-d', strtotime('-1 days'));}else{$to=date('Y-m-d', strtotime($_POST['to']));  }
$classid=1;


?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script>
  $(function() {
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		animation: true
	});

    $('.tablesorter-childRow td').toggle();
    $( "#tabs" ).tabs();

        $('#result').tablesorter({
            theme: 'blue',
            sortList: [[1,0],[0,0]],
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
                stickyHeaders_attachTo : '#day-contain',
                 output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                  output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)
                  output_hiddenColumns : false,       // include hidden columns in the output
                  output_includeFooter : true,        // include footer rows in the output
                  output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                  output_headerRows    : true,        // output all header rows (multiple rows)
                  output_delivery      : 'd',         // (p)opup, (d)ownload
                  output_saveRows      : 'v',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                  output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                  output_replaceQuote  : '\u201c;',   // change quote to left double quote
                  output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                  output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                  output_wrapQuotes    : false,       // wrap every cell output in quotes
                  output_popupStyle    : 'width=580,height=310',
                  output_saveFileName  : 'tabla_f_pordia.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });

        $('#acumulado').tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','output', 'stickyHeaders'],
            widgetOptions: {
               uitheme: 'jui',
               columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_thead: true,
                resizable: true,
                saveSort: true,
                stickyHeaders_attachTo : '#acum-contain',
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
                  output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                  output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                  output_wrapQuotes    : false,       // wrap every cell output in quotes
                  output_popupStyle    : 'width=580,height=310',
                  output_saveFileName  : 'tabla_f_acumulado.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });


    // clicking the download button; all you really need is to
    // trigger an "output" event on the table
    $('#exportaacumulado').click(function(){
        $('#acumulado').trigger('outputTable');

    });
     $('#exportapordia').click(function(){
        $('#result').trigger('outputTable');

    });


    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });


    }
);
  </script>

<table style='width:800px; margin: auto' class='t2'><form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
    <tr class='title'>
         <th colspan="100">Tabla F</th>
    </tr>
    <tr class='title'>
         <td>Periodo</td>
         <td>Departamento</td>
         <td class='total' rowspan=2><input type="submit" name='consulta' value='consulta' /></td>
    </tr>
    <tr class='pair'>
         
         <td><input type="text" id="from" name="from" value='<?php  echo $from; ?>' required><input type="text" id="to" name="to" value='<?php  echo $to; ?>' required></td>
         <td><select id='dept' name='dept' required><option value=''>Selecciona...</option>
         	<?php
         		$query="SELECT * FROM PCRCs WHERE parent=1 ORDER BY Departamento";
				$result=mysql_query($query);
				$num=mysql_numrows($result);
				for($i=0;$i<$num;$i++){
					if($dept==mysql_result($result,$i,'id')){$sel="selected"; $ndep=mysql_result($result,$i,'Departamento');}else{$sel="";}
					echo "<option value='".mysql_result($result,$i,'id')."' $sel>".mysql_result($result,$i,'Departamento')."</option>\n\t\t";
				}
         	?>
         	</select><input type='hidden' name='ndep' id='ndep' value='<?php echo $ndep; ?>'</td>
    </tr>

</form></table>
<br><br>
<?php 

    if(!isset($_POST['consulta'])){exit;}
?>
<div style='width:100%; text-align: right; vertical-align:top;'>
<button type='button' class='buttonlarge button_blue_w' id='exportapordia'>Exportar<br>Por Dia</button>
<button type="button" class='group_select buttonlarge button_redpastel_w' id='exportaacumulado'>Exportar<br>Acumulado</button>
</div>
<?php

include("inbound_new.php");
	
?>
<div id='tabs' style='width:1260px; margin: auto;'>
	<ul>
		<li><a href="#porDia">B2C - Info por Dia</a></li>
		<li><a href="#porAcumulado">B2C - Total Acumulado</a></li>
	</ul>
	<div id='porDia'>
		<div style='width: 1170px; max-height: 800px; margin: auto; color: white; background: #008CBA; font-weight: bold; font-size: 24px; height: 20px; padding: 15px'>Informacion por Dia</div>
		<div style='width: 1200px; max-height: 600px; overflow: auto; margin: auto; position: relative' id='day-contain'>
		<table id='result' style='text-align: center; width: 1200px; margin:auto'>
			<thead>
				<tr>
					<th>Fecha</th><th>Canal</th>
					<?php
						foreach($data as $date => $info){
							foreach($info as $canal => $info2){
								foreach($info2 as $kpi => $resultado){
									echo "<th>$kpi</th>";
								}
								unset($kpi,$resultado);
								break;
							}
							unset($canal,$info2);
							break;
						}
						unset($date,$info);
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($data as $date => $info){
						foreach($info as $canal => $info2){
							echo "<tr><td>$date</td><td>$canal</td>";
							foreach($info2 as $kpi => $resultado){
								if($resultado==NULL){
									$resultado=0;
								}
								echo "<td>$resultado</td>";
							}
							unset($kpi,$resultado);
						}
						echo "</tr>\n\t";
						unset($canal,$info2);
					}
					unset($date,$info);
				?>
			</tbody>
		</table>
		</div>
	</div>
	<div id='porAcumulado'>
		<div style='width: 1170px; max-height: 800px; margin: auto; color: white; background: #008CBA; font-weight: bold; font-size: 24px; height: 20px; padding: 15px'>Acumulado</div>
		<div style='width: 1200px; max-height: 800px; overflow: auto; margin: auto; position: relative' id='acum-contain'>
		<table id='acumulado' style='text-align: center; width: 1200px; margin:auto'>
			<thead>
				<tr>
					<th>Canal</th>
					<?php
						foreach($acumulado as $canal => $info){
							foreach($info as $kpi => $resultado){
									echo "<th>$kpi</th>";
								}
								unset($kpi,$resultado);
								break;
						}
						unset($canal,$info);
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($acumulado as $canal => $info){
						echo "<tr><td>$canal</td>";
							foreach($info as $kpi => $resultado){
								if($resultado==NULL){
									$resultado=0;
								}
								echo "<td>$resultado</td>";
							}
							unset($kpi,$resultado);
						echo "</tr>\n\t";
					}
					unset($canal,$info);
				?>
			</tbody>
		</table>
		</div>
	</div>
</div>
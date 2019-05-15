<?php
if($_SESSION['default']==0){
  $query="SELECT * FROM afiliados_view WHERE users LIKE '%".$_SESSION['id']."%' AND afiliado='copa' ORDER BY afiliado";
  if($result=Queries::query($query)){
    if($result->num_rows==0){
      echo "No cuentas con los permisos para ver este reporte";
      exit;
    }
  }
}

$tbody="<td>Periodo</td><td><input type='text' id='fecha' name='from'><input type='text' id='fecha_f' name='to'><input type='hidden' name='consulta'></td>";
Filters::showFilter('','POST','search','Consultar',$tbody);

//Get Variables
$dept=$_POST['depto'];
$from=$_POST['from'];
if($from==NULL){$from=date('Y-m-d', strtotime('-5 days'));}else{$from=date('Y-m-d', strtotime($_POST['from']));  }
$to=$_POST['to'];
if($to==NULL){$to=date('Y-m-d', strtotime('-1 days'));}else{$to=date('Y-m-d', strtotime($_POST['to']));  }
$classid=1;

$report_name="ReporteCopaVacations_".date('Ymd',strtotime($from))."-".date('Ymd',strtotime($to)).".xls";
$report_name_total="ReporteCopaVacations_acumulado_".date('Ymd',strtotime($from))."-".date('Ymd',strtotime($to)).".xls";

//Metas
$b2b['AHT']=400;
$b2b['SLA30']=0.7;
$b2b['ASA']=17;
$b2b['Abandon']=0.04;
$b2b['Adherencia']=0.85;
$b2b['Ocupacion']=0.77;
$b2b['Rotacion']=0.05;
$b2b['FC']=0.13;
$b2c['AHT']=400;
$b2c['SLA20']=0.8;
$b2c['ASA']=17;
$b2c['Abandon']=0.04;
$b2c['Adherencia']=0.85;
$b2c['Ocupacion']=0.77;
$b2c['Rotacion']=0.05;
$b2c['FC']=0.13;

//comparacion de metricas
function compare($metrica,$skill,$val){
    global $b2b,$b2c;
    if($skill="B2C"){
        $valor=$b2c;
    }else{
        $valor=$b2b;
    }

    switch($metrica){
        case 'AHT':
        case 'ASA':
        case 'Abandon':
            IF($val<=$valor[$metrica]){
                $result=1;
            }else{
                $result=0;
            }
            break;
        default:
            IF($val>=$valor[$metrica]){
                $result=1;
            }else{
                $result=0;
            }
            break;
    }
    return $result;
}

//Function PrintRows

function printRows($variable,$title,$group,$format,$type='td'){
     global    $data,$TotalFechas,$class, $classid;
     if($classid % 2 == 0){$class="pair";}else{$class="odd";}
     if($classid==1){$class="title";}
     echo "\t<tr>\n";
     echo "\t\t<$type style='text-align:left'>$title</$type>\n<$type>$group</$type>\n";
     $x=0;
     while($x<=$TotalFechas){
         echo "\t\t<$type>";
         switch($format){
             case "num":
                 echo number_format($data[$variable][$x]);
                 break;
             case "%":
                 echo number_format(($data[$variable][$x]*100),2)."%";
                 break;
             case "dec":
                 echo number_format(($data[$variable][$x]),2);
                 break;
             case "$":
                 echo "$".number_format($data[$variable][$x],2);
                 break;
             case "na":
                 echo $data[$variable][$x];
                 break;
             case "fecha":
                 echo date('l',strtotime($data[$variable][$x]))."<br>".date('d-M-y',strtotime($data[$variable][$x]));
                 break;
             default:
                 echo $data[$variable][$x];
                 break;
         }

        echo "</$type>\n";
     $x++;
     }
     echo "\t</tr>\n";
     $classid++;
 }

//Function PrintRows  AC BO

function printRows_ac_bo($variable,$title,$group,$format,$type='td'){
     global    $data_ac,$TotalFechas,$class, $classid;
     if($classid % 2 == 0){$class="pair";}else{$class="odd";}
     if($classid==1){$class="title";}
     echo "\t<tr>\n";
     echo "\t\t<$type style='text-align:left'>$title</$type>\n<$type>$group</$type>\n";
     $x=0;

         echo "\t\t<$type>";
         switch($format){
             case "num":
                 echo number_format($data_ac[$variable][$x]);
                 break;
             case "%":
                 echo number_format(($data_ac[$variable][$x]*100),2)."%";
                 break;
             case "dec":
                 echo number_format(($data_ac[$variable][$x]),2);
                     break;
                 case "$":
                     echo "$".number_format($data_ac[$variable][$x],2);
                     break;
                 case "na":
                     echo $data_ac[$variable][$x];
                     break;
                 case "fecha":
                     echo date('l',strtotime($data_ac[$variable][$x]))."<br>".date('d-M-y',strtotime($data_ac[$variable][$x]));
                     break;
                 default:
                     echo $data_ac[$variable][$x];
                     break;
             }

        echo "</$type>\n";

    echo "\t</tr>\n";
    $classid++;
}



//Function PrintRowsAcumulated
function printRows_ac($variable,$title,$group,$format,$type='td'){
    global    $data_ac,$TotalFechas,$class, $classid;
    if($classid % 2 == 0){$class="pair";}else{$class="odd";}
    if($classid==1){$class="title";}
    echo "\t<tr>\n";
    echo "\t\t<$type style='text-align:left'>$title</$type>\n";
    $x=1;
    while($x<=5){
        switch($x){
            case 1:
                $canal="Total";
                break;
            case 2:
                $canal="MP";
                break;
            case 3:
                $canal="IT";
                break;
            case 4:
                $canal="COPA";
                break;
            case 5:
                $canal="COOMEVA";
                break;
        }
        echo "\t\t<$type>";
        switch($format){
            case "num":
                echo number_format($data_ac[$variable."$canal"]);
                break;
            case "%":
                echo number_format(($data_ac[$variable."$canal"]*100),2)."%";
                break;
            case "$":
                echo "$".number_format($data_ac[$variable."$canal"],2);
                break;
            case "na":
                echo $data_ac[$variable."$canal"];
                break;
            case "fecha":
                echo date('l',strtotime($data_ac[$variable."$canal"]))."<br>".date('d-M-y',strtotime($data_ac[$variable."$canal"]));
                break;
            default:
                echo $data_ac[$variable."$canal"];
                break;
        }

        echo "</$type>\n";
    $x++;
    }
    echo "\t</tr>\n";
    $classid++;
}

//Function PrintRowsAcumulado
function printRowsAc($title,$format,$var){
    global    $data_ac,$class, $classid;
    $var1=$var.'mp';
    $var2=$var.'it';
    $var3=$var.'copa';
    $var4=$var.'COOMEVA';
    if($classid % 2 == 0){$class="pair";}else{$class="odd";}
    if($classid==1){$class="title";}
    echo "\t<tr class='$class'>\n";
    echo "\t\t<td class='title'>$title</td>\n";

        switch($format){
            case "num":
                echo "\t\t<td>";
                echo number_format($data_ac[$var]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var1]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var2]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var3]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var4]);
                echo "\t\t</td>\n";
                break;
            case "%":
                echo "\t\t<td>";
                echo number_format(($data_ac[$var]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var1]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var2]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var3]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var4]*100),2)."%";
                echo "\t\t</td>\n";
                break;
            case "$":
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var],2);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var1],2);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var2],2);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var3],2);
               echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var4],2);
                echo "\t\t</td>\n";
                break;
            case "na":
                 echo "\t\t<td>";
                echo $data_ac[$var];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var1];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var2];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var3];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var4];
                echo "\t\t</td>\n";
                break;
            default:
                echo $data[$variable][$x];
                break;
        }


    echo "\t</tr>\n";
    $classid++;
}

function createVariable($nombre,$variable1,$variable2,$operacion){
    global $data, $TotalFechas;
        $x=0;
        while($x<=$TotalFechas){
            switch($operacion){
                    case "+":
                        $data[$nombre][$x]=$data[$variable1][$x] + $data[$variable2][$x];
                        break;
                    case "*":
                        $data[$nombre][$x]=$data[$variable1][$x] * $data[$variable2][$x];
                        break;
                    case "/":
                        $data[$nombre][$x]=$data[$variable1][$x] / $data[$variable2][$x];
                        break;
                    case "-":
                        $data[$nombre][$x]=$data[$variable1][$x] - $data[$variable2][$x];
                        break;
            }
        $x++;
        }
}

function createVariable_ac($nombre,$variable1,$variable2,$operacion){
    global $data_ac, $TotalFechas;
        $x=0;
        while($x<=5){
        switch($x){
            case 1:
                $canal="Total";
                break;
            case 2:
                $canal="MP";
                break;
            case 3:
                $canal="IT";
                break;
            case 4:
                $canal="COPA";
                break;
            case 5:
                $canal="COOMEVA";
                break;
        }
            switch($operacion){
                    case "+":
                        $data_ac[$nombre."$canal"]=$data_ac[$variable1."$canal"] + $data_ac[$variable2."$canal"];
                        break;
                    case "*":
                        $data_ac[$nombre."$canal"]=$data_ac[$variable1."$canal"] * $data_ac[$variable2."$canal"];
                        break;
                    case "/":
                        $data_ac[$nombre."$canal"]=$data_ac[$variable1."$canal"] / $data_ac[$variable2."$canal"];
                        break;
                    case "-":
                        $data_ac[$nombre."$canal"]=$data_ac[$variable1."$canal"] - $data_ac[$variable2."$canal"];
                        break;
            }
        $x++;
        }
}

function createVariableAc($nombre,$variable1,$variable2,$operacion){
    global $data_ac;
    $nombre1=$nombre.'mp';
    $nombre2=$nombre.'it';
    $nombre3=$nombre.'copa';
    $nombre4=$nombre.'COOMEVA';
    $variable11=$variable1.'mp';
    $variable12=$variable1.'it';
    $variable13=$variable1.'copa';
    $variable14=$variable1.'COOMEVA';
    $variable21=$variable2.'mp';
    $variable22=$variable2.'it';
    $variable23=$variable2.'copa';
    $variable24=$variable2.'COOMEVA';
        $x=0;
        switch($operacion){
                    case "+":
                        $data_ac[$nombre]=$data_ac[$variable1] + $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] + $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] + $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] + $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] + $data_ac[$variable24];
                        break;
                    case "*":
                        $data_ac[$nombre]=$data_ac[$variable1] * $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] * $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] * $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] * $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] * $data_ac[$variable24];
                        break;
                    case "/":
                        $data_ac[$nombre]=$data_ac[$variable1] / $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] / $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] / $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] / $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] / $data_ac[$variable24];
                        break;
                    case "-":
                        $data_ac[$nombre]=$data_ac[$variable1] - $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] - $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] - $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] - $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] - $data_ac[$variable24];
                        break;
            }
        $x++;
}
?>

<script>

$(function() {
  $('#fecha').periodpicker({
    end: '#fecha_f',
    clearButtonInButton: true,
    formatDateTime: 'YYYY-MM-DD'
  });

    $('.tablesorter-childRow td').toggle();

    $('#tablesorter').tablesorter({
        theme: 'blue',
        sortList: [[1,1]],
        headerTemplate: '{content}',
        stickyHeaders: "tablesorter-stickyHeader",
        cssChildRow : "tablesorter-childRow",
        // fix the column widths
        widthFixed: false,
        widgets: [ 'zebra','filter','output', 'stickyHeaders'],
        widgetOptions: {
            stickyHeaders_attachTo : '#container',
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
            output_hiddenColumns : true,       // include hidden columns in the output
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
            output_saveFileName  : '<?php echo $report_name; ?>',
            // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
            output_encoding      : 'data:application/octet-stream;charset=utf8,'
        }

    });

        $('#tablesorter_total').tablesorter({
            theme: 'blue',
            sortList: [[1,1]],
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
                 output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                 output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)
                 output_hiddenColumns : true,       // include hidden columns in the output
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
                 output_saveFileName  : '<?php echo $report_name_total; ?>',
                 // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                 output_encoding      : 'data:application/octet-stream;charset=utf8,'
            }

        });


    $('#exportapordia').click(function(){
        $('#tablesorter').trigger('outputTable');
    });

    $('#exportaacumulado').click(function(){
        $('#tablesorter_total').trigger('outputTable');
    });

    $( "#accordion" ).accordion({
             collapsible: true,
             heightStyle: "content",
             active: false
    });

    $( "#accordion_info" ).accordion({
            heightStyle: "content",
            collapsible: true
    });

    $('#fecha_f').val('<?php echo $to; ?>');
    $('#fecha').val('<?php echo $from; ?>').periodpicker('change');


});

  </script>
  <style>
  table.tablesorter tbody td .nocumple {
    background-color: #9E1500;
    color: white;
}
  </style>


<br>

<?php



    if(!isset($_POST['consulta'])){exit;}
?>
<div style='width:100%; text-align: right; vertical-align:top;'>
<button type='button' class='buttonlarge button_blue_w' id='exportapordia'>Exportar<br>Resumen Diario</button>
<button type='button' class='buttonlarge button_red_w' id='exportaacumulado'>Exportar<br>a Acumulado</button>
</div>
<?php

function query_dep($dept){
    global $from, $to, $TotalFechas, $data;
    switch($dept){
        case 3:
            $skill='B2C';
            break;
        case 7:
            $skill='B2B';
            break;
    }
    $query="SELECT
		Fecha,
		SUM(VolumenCOPA) as Volumen,
		SUM(AnsweredCOPA) as Contestadas,
		SUM(AbandonedCOPA) as Abandonadas,
		SUM(TransferidasCOPA) as Transferidas,
		SUM(TransferidasMinCOPA) as Transferidas_1_Min,
		SUM(SLA20CallsCOPA)/SUM(VolumenCOPA) as SLA20,
		SUM(SLA30CallsCOPA)/SUM(VolumenCOPA) as SLA30,
		SUM(AbandonedCOPA)/SUM(VolumenCOPA) as Abandon,
		SUM(TalkingTimeCOPA)/SUM(AnsweredCOPA) as AHT,
		SUM(WaitingTimeCOPA)/SUM(AnsweredCOPA) as ASA,
		SUM(TalkingTimeCOPA) as TalkingTime,
		SUM(LocalizadoresCOPA)/(SUM(AnsweredCOPA)-SUM(TransferidasMinCOPA)) as FC,
		SUM(LocalizadoresCOPA) as Localizadores,
		1-SUM(PNP)/SUM(DuracionSesiones) as Utilizacion,
		(SUM(TalkingTimeTotal)+SUM(PP))/(SUM(DuracionSesiones)-SUM(PNP)) as Ocupacion,
		SUM(Adherencia) as Adherencia
	FROM
		(
			SELECT
				Fecha, Dolar
			FROM
				Fechas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha
		) D_Fechas
	LEFT JOIN
		(
      SELECT
				a.Fecha as Telefonia_Fecha, Skill, Canal,
        SUM(IF(Answered=1, TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeTotal,
				COUNT(IF(Canal LIKE '%copa%',ac_id,NULL)) as VolumenCOPA,
				COUNT(IF(Answered=1 AND Canal LIKE '%copa%',ac_id,NULL)) as AnsweredCOPA,
				COUNT(IF(Answered=0 AND Canal LIKE '%copa%',ac_id,NULL)) as AbandonedCOPA,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasCOPA,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasMinCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA20CallsCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA30CallsCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Espera),0)) as WaitingTimeCOPA
			FROM
				(SELECT a.Fecha, ac_id, Answered, Desconexion, Duracion_Real, Espera, Canal, Skill FROM t_Answered_Calls a
			LEFT JOIN
				Cola_Skill b
			ON
				a.Cola=b.Cola
			LEFT JOIN
				Dids d
			ON
				a.DNIS=d.DID AND
				a.Fecha>=d.Fecha
			WHERE
				a.Fecha BETWEEN '$from' AND '$to'
			HAVING
				Skill=$dept) a
			GROUP BY
				a.Fecha
		) Telefonia
	ON
		Fecha=Telefonia_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Localizadores_Fecha,
				COUNT(IF(`id Departamento`=$dept OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresTotal,
				SUM(IF(`id Departamento`=$dept OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoTotal,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresMP,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoMP,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%intertours%'),localizador,NULL)) as LocalizadoresIT,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%intertours%'),Monto,NULL)) as MontoIT,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%copa%'),localizador,NULL)) as LocalizadoresCOPA,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%copa%'),Monto,NULL)) as MontoCOPA,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%coomeva%'),localizador,NULL)) as LocalizadoresCOOMEVA,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%coomeva%'),Monto,NULL)) as MontoCOOMEVA

			FROM
				(
					SELECT
						Fecha, Localizador, asesor, `id Departamento`, Afiliado, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta
					FROM
							t_Locs a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						a.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						a.Fecha, localizador
					HAVING
						Monto!=0 AND
						Venta!=0
				) Locs
			GROUP BY
				Fecha
		) Localizadores
	ON
		Fecha=Localizadores_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Pausas_Fecha,
				SUM(IF(Skill=$dept AND Productiva=0,TIME_TO_SEC(Duracion),0)) as PNP,
				SUM(IF(Skill=$dept AND Productiva=1,TIME_TO_SEC(Duracion),0)) as PP
			FROM
				t_pausas a
			LEFT JOIN
				Tipos_pausas b
			ON
				a.codigo=b.pausa_id
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha
		) Pausas
	ON
		Fecha=Pausas_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha_in as Sesiones_Fecha,
				SUM(IF(Skill=$dept,TIME_TO_SEC(Duracion),0)) as DuracionSesiones
			FROM
				t_Sesiones
			WHERE
				Fecha_in BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha_in
		) Sesiones
	ON
		Fecha=Sesiones_Fecha
	LEFT JOIN
		(
			SELECT
				adh_s_fecha, IF(TotalSesiones/TiempoSesion>1,1,TotalSesiones/TiempoSesion) as Adherencia
			FROM
				(
					SELECT
						Fecha_in as adh_s_fecha, sum(TIME_TO_SEC(Duracion)) as TotalSesiones
					FROM
						t_Sesiones a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						Skill=$dept AND
						`id Departamento`=$dept AND
						Fecha_in BETWEEN '$from' AND '$to'
					GROUP BY
						adh_s_fecha
				) adh_s
			LEFT JOIN
				(
					SELECT
						a.Fecha as adh_p_fecha, SUM(TIME_TO_SEC(IF(`jornada end`<'09:00:00','24:00:00',`jornada end`))-TIME_TO_SEC(`jornada start`)) as TiempoSesion
					FROM
						`Historial Programacion` a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					LEFT JOIN
						Ausentismos c
					ON
						a.asesor=c.asesor AND
						a.Fecha BETWEEN c.Inicio AND c.Fin
					WHERE
						`id Departamento`=$dept AND
						a.Fecha < Egreso AND
						c.tipo_ausentismo IS NULL AND
						`jornada start`!=`jornada end` AND
						a.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						adh_p_fecha
				) adh_p
			ON
				adh_s_fecha=adh_p_fecha
		) adh_total
	ON
		Fecha=adh_s_fecha
	GROUP BY
		Fecha
    ORDER BY
        Fecha";

    if($result=Queries::query($query)){
      $TotalFechas=0;
      $fields=$result->fetch_fields();
      while($fila=$result->fetch_array(MYSQLI_BOTH)){
        for($i=0;$i<$result->field_count;$i++){
          $data[$fields[$i]->name][$skill][$fila['Fecha']]=$fila[$i];
        }
      $TotalFechas++;
      }
    }

    if($TotalFechas>0){
      $TotalFechas--;
    }

}

function query_total_dep($dept){
    global $from, $to, $TotalFechas, $data_total;
    switch($dept){
        case 3:
            $skill='B2C';
            break;
        case 7:
            $skill='B2B';
            break;
    }
    $query="SELECT
		SUM(VolumenCOPA) as Volumen,
		SUM(AnsweredCOPA) as Contestadas,
		SUM(AbandonedCOPA) as Abandonadas,
		SUM(TransferidasCOPA) as Transferidas,
		SUM(TransferidasMinCOPA) as Transferidas_1_Min,
		SUM(SLA20CallsCOPA)/SUM(VolumenCOPA) as SLA20,
		SUM(SLA30CallsCOPA)/SUM(VolumenCOPA) as SLA30,
		SUM(AbandonedCOPA)/SUM(VolumenCOPA) as Abandon,
		SUM(TalkingTimeCOPA)/SUM(AnsweredCOPA) as AHT,
		SUM(WaitingTimeCOPA)/SUM(AnsweredCOPA) as ASA,
		SUM(TalkingTimeCOPA) as TalkingTime,
		SUM(LocalizadoresCOPA)/(SUM(AnsweredCOPA)-SUM(TransferidasMinCOPA)) as FC,
		SUM(LocalizadoresCOPA) as Localizadores,
		1-SUM(PNP)/SUM(DuracionSesiones) as Utilizacion,
		(SUM(TalkingTimeTotal)+SUM(PP))/(SUM(DuracionSesiones)-SUM(PNP)) as Ocupacion,
		SUM(Adherencia) as Adherencia
	FROM
		(
      SELECT
				a.Fecha as Telefonia_Fecha, Skill, Canal,
        SUM(IF(Answered=1, TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeTotal,
				COUNT(IF(Canal LIKE '%copa%',ac_id,NULL)) as VolumenCOPA,
				COUNT(IF(Answered=1 AND Canal LIKE '%copa%',ac_id,NULL)) as AnsweredCOPA,
				COUNT(IF(Answered=0 AND Canal LIKE '%copa%',ac_id,NULL)) as AbandonedCOPA,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasCOPA,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND Canal LIKE '%copa%',ac_id,NULL)) as TransferidasMinCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA20CallsCOPA,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%copa%',ac_id,NULL)) as SLA30CallsCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeCOPA,
				SUM(IF(Answered=1 AND Canal LIKE '%copa%', TIME_TO_SEC(Espera),0)) as WaitingTimeCOPA
			FROM
				(SELECT a.Fecha, ac_id, Answered, Desconexion, Duracion_Real, Espera, Canal, Skill FROM t_Answered_Calls a
			LEFT JOIN
				Cola_Skill b
			ON
				a.Cola=b.Cola
			LEFT JOIN
				Dids d
			ON
				a.DNIS=d.DID AND
				a.Fecha>=d.Fecha
			WHERE
				a.Fecha BETWEEN '$from' AND '$to'
			HAVING
				Skill=$dept) a

		) Telefonia
	JOIN
		(
			SELECT
				Fecha as Localizadores_Fecha,
				COUNT(IF(`id Departamento`=$dept OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresTotal,
				SUM(IF(`id Departamento`=$dept OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoTotal,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL)) as LocalizadoresMP,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%pricetravel.com.mx%') OR (`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL)) as MontoMP,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%intertours%'),localizador,NULL)) as LocalizadoresIT,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%intertours%'),Monto,NULL)) as MontoIT,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%copa%'),localizador,NULL)) as LocalizadoresCOPA,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%copa%'),Monto,NULL)) as MontoCOPA,
				COUNT(IF((`id Departamento`=$dept AND Afiliado LIKE '%coomeva%'),localizador,NULL)) as LocalizadoresCOOMEVA,
				SUM(IF((`id Departamento`=$dept AND Afiliado LIKE '%coomeva%'),Monto,NULL)) as MontoCOOMEVA

			FROM
				(
					SELECT
						Fecha, Localizador, asesor, `id Departamento`, Afiliado, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta
					FROM
							t_Locs a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						a.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						a.Fecha, localizador
					HAVING
						Monto!=0 AND
						Venta!=0
				) Locs

		) Localizadores
	JOIN
		(
			SELECT
				Fecha as Pausas_Fecha,
				SUM(IF(Skill=$dept AND Productiva=0,TIME_TO_SEC(Duracion),0)) as PNP,
				SUM(IF(Skill=$dept AND Productiva=1,TIME_TO_SEC(Duracion),0)) as PP
			FROM
				t_pausas a
			LEFT JOIN
				Tipos_pausas b
			ON
				a.codigo=b.pausa_id
			WHERE
				Fecha BETWEEN '$from' AND '$to'

		) Pausas
	JOIN
		(
			SELECT
				Fecha_in as Sesiones_Fecha,
				SUM(IF(Skill=$dept,TIME_TO_SEC(Duracion),0)) as DuracionSesiones
			FROM
				t_Sesiones
			WHERE
				Fecha_in BETWEEN '$from' AND '$to'

		) Sesiones
	JOIN
		(
			SELECT
				adh_s_fecha, IF(TotalSesiones/TiempoSesion>1,1,TotalSesiones/TiempoSesion) as Adherencia
			FROM
				(
					SELECT
						Fecha_in as adh_s_fecha, sum(TIME_TO_SEC(Duracion)) as TotalSesiones
					FROM
						t_Sesiones a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						Skill=$dept AND
						`id Departamento`=$dept AND
						Fecha_in BETWEEN '$from' AND '$to'

				) adh_s
			JOIN
				(
					SELECT
						a.Fecha as adh_p_fecha, SUM(TIME_TO_SEC(IF(`jornada end`<'09:00:00','24:00:00',`jornada end`))-TIME_TO_SEC(`jornada start`)) as TiempoSesion
					FROM
						`Historial Programacion` a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					LEFT JOIN
						Ausentismos c
					ON
						a.asesor=c.asesor AND
						a.Fecha BETWEEN c.Inicio AND c.Fin
					WHERE
						`id Departamento`=$dept AND
						a.Fecha < Egreso AND
						c.tipo_ausentismo IS NULL AND
						`jornada start`!=`jornada end` AND
						a.Fecha BETWEEN '$from' AND '$to'

				) adh_p

		) adh_total
   ";

   if($result=Queries::query($query)){
     $fields=$result->fetch_fields();
     while($fila=$result->fetch_array(MYSQLI_BOTH)){
       for($i=0;$i<$result->field_count;$i++){
         $data_total[$fields[$i]->name][$skill]=$fila[$i];
       }
     }
   }else{
     echo "Error!";
   }


}

function tipificacion(){
    global $from, $to, $tipif, $motivos, $tipif_motivo, $tipos, $tipif_tipo, $soportes, $tipif_soporte, $localidades, $total_tipif ;
    $query="SELECT b.motivo, c.tipo, d.tipo_soporte, nombre_agencia, localidad_agencia FROM ag_tipificacion a LEFT JOIN ag_motivos b ON a.motivo=b.id LEFT JOIN ag_tipo c ON a.tipo=c.id LEFT JOIN ag_soporte d ON a.soporte=d.id WHERE canal=4 AND a.date_created BETWEEN '$from' AND '$to'";

    if($result=Queries::query($query)){
      $total_tipif=$result->num_rows;
      while($fila=$result->fetch_assoc()){
        $motivo=$fila['motivo'];
        $tipo=$fila['tipo'];
        $soporte=$fila['tipo_soporte'];
        $localidad=$fila['localidad_agencia'];


        if($motivo!=NULL){
            $motivos++;
            $tipif_motivo[$motivo]++;}
        if($tipo!=NULL){
            $tipos[$motivo]++;
            $tipif_tipo[$motivo][$tipo]++;}
        if($soporte!=NULL){
            $soportes[$motivo][$tipo]++;
            $tipif_soporte[$motivo][$tipo][$soporte]++;}
        if($localidad!=NULL){
            $localidades[$localidad]++;}
      }
    }else{
      echo "Error!";
    }


}

function format_var($variable,$var){
    switch ($variable){
        case "SLA20":
        case "SLA30":
        case "Abandon":
        case "FC":
        case "Utilizacion":
        case "Ocupacion":
        case "Adherencia":
            $format="%";
            break;
        default:
            $format="num";

    }


    switch ($format){
        case '$':
            $result="$".number_format($var,2);
            break;
        case '%':
            $result=number_format($var*100,2)."%";
            break;
        case 'num':
            $result=number_format($var,0);
            break;

    }

    return $result;
}

query_dep(3);
query_dep(7);
query_total_dep(3);
query_total_dep(7);
tipificacion();



?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script>

/*
<?php print_r($tipif_tipo); ?>
*/
$(function () {

    var colors = Highcharts.getOptions().colors,
        categories = [<?php foreach($tipif_motivo as $motivo => $info){ echo "'$motivo',"; } unset($info,$motivo);?>],
        data = [
            <?php
                $color=0;
                foreach($tipif_motivo as $motivo => $info){
                    $perc=number_format($info/$total_tipif*100,2);
                    echo "{
                            y: $perc,
                            color: colors[$color],
                            drilldown: {
                                name: '$motivo',
                                categories: ["; foreach($tipif_tipo[$motivo] as $tipo => $info_tipo){ echo "'$tipo',"; } unset($tipo,$info_tipo); echo"],
                                data: ["; foreach($tipif_tipo[$motivo] as $tipo => $info_tipo){ $perc=number_format($info_tipo/$total_tipif*100,2); echo "$perc,"; } unset($tipo,$info_tipo); echo"],
                                color: colors[0]
                            }
                        },";
                    $color++;
                }
            ?>
        ],
        browserData = [],
        versionsData = [],
        i,
        j,
        dataLen = data.length,
        drillDataLen,
        brightness;


    // Build the data arrays
    for (i = 0; i < dataLen; i += 1) {

        // add browser data
        browserData.push({
            name: categories[i],
            y: data[i].y,
            color: data[i].color
        });

        // add version data
        drillDataLen = data[i].drilldown.data.length;
        for (j = 0; j < drillDataLen; j += 1) {
            brightness = 0.2 - (j / drillDataLen) / 5;
            versionsData.push({
                name: data[i].drilldown.categories[j],
                y: data[i].drilldown.data[j],
                color: Highcharts.Color(data[i].color).brighten(brightness).get()
            });
        }
    }

    // Create the chart
    $('#container_tipificacion').highcharts({
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Tipificacion de Llamadas, <?php echo $from." a ".$to; ?>'
        },
        subtitle: {
            text: 'Informacion de B2B'
        },
        yAxis: {
            title: {
                text: 'Total percent market share'
            }
        },
        plotOptions: {
            pie: {
                shadow: false,
                center: ['50%', '50%']
            }
        },
        tooltip: {
            valueSuffix: '%'
        },
        series: [{
            name: 'Motivo Llamada',
            data: browserData,
            size: '60%',
            dataLabels: {
                formatter: function () {
                    return this.y > 5 ? this.point.name : null;
                },
                color: '#ffffff',
                distance: -30
            }
        }, {
            name: 'Tipo',
            data: versionsData,
            size: '80%',
            innerSize: '60%',
            dataLabels: {
                formatter: function () {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>' + this.point.name + ':</b> ' + this.y + '%' : null;
                }
            }
        }]
    });
});
</script>
<div style='width: 95%; margin: auto;'>
<div id="accordion">

  <h3>Metas</h3>
    <div>
    <table style="margin: auto; width: 80%;text-align:center" class='t2'>
        <tr class='title'>
            <th>Métrica</th><th>AHT</th><th>SLA</th><th>ASA</th><th>Abandon</th><th>Adherencia</th><th>Ocupación</th><th>Rotación</th><th>FC (conversión)</th>
        </tr>
        <tr class='pair'>
            <td class='subtitle'>Fórmula</td><td>Tiempo promedio de transacción sumando (Talk time + ACW + Hold)</td><td>Total de llamadas Contestadas &#60= Umbral /Llamadas Ofrecidas</td>
            <td>Umbral en el cual se están contestando las llamadas</td><td>Llamadas abandonadas / Llamadas ofrecidas</td><td>Cantidad de FTEs Reales/cantidad FTEs programados</td>
            <td>(Tiempo de conversación inbound + tiempos conversión outbound) /horas de conexión</td><td>(Rotación personal voluntario + involuntario) / total personal al final de mes</td>
            <td>Cantidad de ventas realizadas por mes / total de llamadas del skill de ventas</td>
        </tr>
        <tr class='odd'>
            <td class='subtitle'>Meta B2C</td><td><?php echo $b2c['AHT']; ?> seg</td><td><?php echo $b2c['SLA20']*100; ?>/20 B2C</td><td><?php echo $b2c['ASA']; ?> seg</td>
            <td><?php echo $b2c['Abandon']*100; ?>%</td><td><?php echo $b2c['Adherencia']*100; ?>%</td><td><?php echo $b2c['Ocupacion']*100; ?>%</td>
            <td>Promedio del <?php echo $b2c['Rotacion']*100; ?>%</td><td><?php echo $b2c['FC']*100; ?>%</td>
        </tr>
        <tr class='pair'>
            <td class='subtitle'>Meta B2B</td><td><?php echo $b2b['AHT']; ?> seg</td><td><?php echo $b2b['SLA30']*100; ?>/30 B2B</td><td><?php echo $b2b['ASA']; ?> seg</td>
            <td><?php echo $b2b['Abandon']*100; ?>%</td><td><?php echo $b2b['Adherencia']*100; ?>%</td><td><?php echo $b2b['Ocupacion']*100; ?>%</td>
            <td>Promedio del <?php echo $b2b['Rotacion']*100; ?>%</td><td><?php echo $b2b['FC']*100; ?>%</td>
        </tr>
    </table>
    </div>
</div>    <br>
<div id="accordion_info">

  <h3>Información Por Día</h3>
    <div>
        <div id='container' style='height: 800px; overflow: scroll; position: relative'>
        <table width='100%' class='tablesorter' id='tablesorter' style='text-align:center'>
            <thead>
                <tr>
                    <th>Métrica</th>
                    <th>Canal</th>
            <?php
                $i=0;
                while($i<=$TotalFechas){
                    echo "<th>".date('d-m-Y',strtotime($from.' +'.$i.' days'))."</th>\n\t";
                $i++;
                }
            ?>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach($data as $metrica => $data2){
                    if($metrica=='Fecha'){ continue; }
                    foreach($data2 as $skill => $data3){
                        echo "<tr>";
                            echo "<td>$metrica</td><td>$skill</td>";
                            foreach($data3 as $fecha => $data4){
                                switch($metrica){
                                    case 'Adherencia':
                                    case 'Ausentismo':
                                    case 'Rotacion':
                                        if(compare($metrica,$skill,$data4)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                        break;
                                    case 'SLA20':
                                        if($data['Volumen'][$skill][$fecha]==0){
                                            $img="";
                                        }else{
                                            if($skill=='B2C'){
                                                if(compare($metrica,$skill,$data4)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                            }else{
                                                $img="";
                                            }
                                        }
                                        break;
                                    case 'SLA30':
                                        if($data['Volumen'][$skill][$fecha]==0){
                                            $img="";
                                        }else{
                                            if($skill=='B2B'){
                                                if(compare($metrica,$skill,$data4)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                            }else{
                                                $img="";
                                            }
                                        }
                                        break;
                                    case 'AHT':
                                    case 'ASA':
                                    case 'Abandon':
                                    case 'Ocupacion':
                                    case 'FC':
                                        if($data['Volumen'][$skill][$fecha]==0){
                                            $img="";
                                        }else{
                                            if(compare($metrica,$skill,$data4)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                        }
                                        break;
                                    default:
                                        $img="";
                                        break;
                                }

                                echo "<td>".format_var($metrica,$data4)."<br>$img</br></td>";
                            }
                            unset($fecha,$data4);
                        echo "\n\t</tr>\n\t";
                    }
                    unset($skill,$data3);
                }
                unset($metrica,$data2);
            ?>
            </tbody>
        </table>
        </div>
    </div>
    <h3>Información Acumulado</h3>
        <div>
        <div id='container_total' style='height: 800px; overflow: scroll; position: relative; width:400px; margin:auto;'>
        <table width='50%' class='tablesorter' id='tablesorter_total' style='text-align:center'>
            <thead>
                <tr>
                    <th>Métrica</th>
                    <th>Canal</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach($data_total as $metrica_total => $data2_total){
                    if($metrica_total=='Fecha'){ continue; }
                    foreach($data2_total as $skill_total => $data3_total){
                        echo "<tr>";
                            echo "<td>$metrica_total</td><td>$skill_total</td>";

                                switch($metrica_total){
                                    case 'Adherencia':
                                    case 'Ausentismo':
                                    case 'Rotacion':
                                        if(compare($metrica_total,$skill_total,$data3_total)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                        break;
                                    case 'SLA20':
                                        if($data_total['Volumen'][$skill_total]==0){
                                            $img="";
                                        }else{
                                            if($skill_total=='B2C'){
                                                if(compare($metrica_total,$skill_total,$data3_total)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                            }else{
                                                $img="";
                                            }
                                        }
                                        break;
                                    case 'SLA30':
                                        if($data_total['Volumen'][$skill_total]==0){
                                            $img="";
                                        }else{
                                            if($skill_total=='B2B'){
                                                if(compare($metrica_total,$skill_total,$data3_total)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                            }else{
                                                $img="";
                                            }
                                        }
                                        break;
                                    case 'AHT':
                                    case 'ASA':
                                    case 'Abandon':
                                    case 'Ocupacion':
                                    case 'FC':
                                        if($data_total['Volumen'][$skill_total]==0){
                                            $img="";
                                        }else{
                                            if(compare($metrica_total,$skill_total,$data3_total)==1){$img="<img src='/images/greenflag.png'>";}else{$img="<img src='/images/redflag.png'>";}
                                        }
                                        break;
                                    default:
                                        $img="";
                                        break;
                                }

                                echo "<td>".format_var($metrica_total,$data3_total)."<br>$img</br></td>";

                        echo "\n\t</tr>\n\t";
                    }
                    unset($skill_total,$data3_total);
                }
                unset($metrica_total,$data2_total);
            ?>
            </tbody>
        </table>
        </div>
    </div>
    <h3>Tipificación</h3>
    <div>
        <div id="container_tipificacion" style="width: 600px; height: 400px; margin: 0 auto"></div>
    </div>
</div></div>

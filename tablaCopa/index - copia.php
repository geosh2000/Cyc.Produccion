<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="reporte_copa";

include("../connectDB.php");
include("../common/list_asesores.php");

//default timezone
date_default_timezone_set('America/Bogota');

//Get Variables
$dept=$_POST['depto'];
$from=$_POST['from'];
if($from==NULL){$from=date('Y-m-d', strtotime('-5 days'));}else{$from=date('Y-m-d', strtotime($_POST['from']));  }
$to=$_POST['to'];
if($to==NULL){$to=date('Y-m-d', strtotime('-1 days'));}else{$to=date('Y-m-d', strtotime($_POST['to']));  }
$classid=1;

$report_name="ReporteCopa_".date('Ymd',strtotime($from))."-".date('Ymd',strtotime($to)).".xls";

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











include("../common/scripts.php");



?>

<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>

<script>

  $(function() {

    $( "#from" ).datepicker({

      defaultDate: "+1w",

      changeMonth: true,

      numberOfMonths: 3,

      onClose: function( selectedDate ) {

        $( "#to" ).datepicker( "option", "minDate", selectedDate );

      }

    });

    $( "#to" ).datepicker({

      defaultDate: "+1w",

      changeMonth: true,

      numberOfMonths: 3,

      onClose: function( selectedDate ) {

        $( "#from" ).datepicker( "option", "maxDate", selectedDate );

      }

    });



    $('.tablesorter-childRow td').toggle();



        $('#tablesorter, #tablesorter_export, #tablesorter_total').tablesorter({

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

                  output_hiddenColumns : false,       // include hidden columns in the output

                  output_includeFooter : true,        // include footer rows in the output

                  output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text

                  output_headerRows    : true,        // output all header rows (multiple rows)

                  output_delivery      : 'd',         // (p)opup, (d)ownload

                  output_saveRows      : 'v',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function

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

        $('#tablesorter').trigger('outputTable');



    });

    $('#container_export').hide();





    $( "#accordion" ).accordion({

      collapsible: true,

      heightStyle: "content",

      active: false

    });

    $( "#accordion_info" ).accordion({
            heightStyle: "content",
            collapsible: true

    });




    }

);

  </script>
  <style>
  table.tablesorter tbody td .nocumple {
    background-color: #9E1500;
    color: white;
}
  </style>



<?php

include("../common/menu.php");



?>



<table width='100%' class='t2'><form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">

    <tr class='title'>

         <th colspan="100">Resultados Copa</th>

    </tr>

    <tr class='title'>

         <td>Fecha inicial</td>

         <td>Fecha final</td>

         <td class='total' rowspan=2><input type="submit" name='consulta' value='consulta' /></td>

    </tr>

    <tr class='pair'>

         <td><input type="text" id="from" name="from" value='<?php  echo $from; ?>' required></td>

         <td><input type="text" id="to" name="to" value='<?php  echo $to; ?>' required></td>

    </tr>



</form></table>

<br><br>

<?php



    if(!isset($_POST['consulta'])){exit;}
?>
<div style='width:100%; text-align: right; vertical-align:top;'>
<button type='button' class='buttonlarge button_blue_w' id='exportapordia'>Exportar<br>a Excel</button>
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
				a.Fecha as Telefonia_Fecha, Skill,
				COUNT(ac_id) as VolumenTotal,
				COUNT(IF(Answered=1,ac_id,NULL)) as AnsweredTotal,
				COUNT(IF(Answered=0,ac_id,NULL)) as AbandonedTotal,
				COUNT(IF(Desconexion='Transferida',ac_id,NULL)) as TransferidasTotal,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00',ac_id,NULL)) as TransferidasMinTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:20',ac_id,NULL)) as SLA20CallsTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:30',ac_id,NULL)) as SLA30CallsTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Espera),0)) as WaitingTimeTotal,
				COUNT(IF(Canal LIKE '%MP MX%',ac_id,NULL)) as VolumenMP,
				COUNT(IF(Answered=1 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AnsweredMP,
				COUNT(IF(Answered=0 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AbandonedMP,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMP,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMinMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA20CallsMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA30CallsMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Espera),0)) as WaitingTimeMP,
				COUNT(IF(Canal LIKE '%Intertours%',ac_id,NULL)) as VolumenIT,
				COUNT(IF(Answered=1 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AnsweredIT,
				COUNT(IF(Answered=0 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AbandonedIT,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasIT,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasMinIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA20CallsIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA30CallsIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Espera),0)) as WaitingTimeIT,
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
				t_Answered_Calls a
			LEFT JOIN
				Cola_Skill b
			ON
				a.Cola=b.Cola
			LEFT JOIN
				Dids d
			ON
				a.DNIS=d.DID AND
				a.Fecha>=d.Fecha
			LEFT JOIN
				Asesores c
			ON
				a.asesor=c.id
			WHERE
				Skill=$dept AND
				a.Fecha BETWEEN '$from' AND '$to'
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

    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $numfield=mysql_num_fields($result);
    $i=0;
    while($i<$num){
        $x=0;
        while($x<$numfield){
            $data[mysql_field_name($result,$x)][$skill][mysql_result($result,$i,'Fecha')]=mysql_result($result,$i,mysql_field_name($result,$x));
        $x++;
        }
        $TotalFechas=$i;
    $i++;
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
				a.Fecha as Telefonia_Fecha, Skill,
				COUNT(ac_id) as VolumenTotal,
				COUNT(IF(Answered=1,ac_id,NULL)) as AnsweredTotal,
				COUNT(IF(Answered=0,ac_id,NULL)) as AbandonedTotal,
				COUNT(IF(Desconexion='Transferida',ac_id,NULL)) as TransferidasTotal,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00',ac_id,NULL)) as TransferidasMinTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:20',ac_id,NULL)) as SLA20CallsTotal,
				COUNT(IF(Answered=1 AND Espera<='00:00:30',ac_id,NULL)) as SLA30CallsTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeTotal,
				SUM(IF(Answered=1, TIME_TO_SEC(Espera),0)) as WaitingTimeTotal,
				COUNT(IF(Canal LIKE '%MP MX%',ac_id,NULL)) as VolumenMP,
				COUNT(IF(Answered=1 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AnsweredMP,
				COUNT(IF(Answered=0 AND Canal LIKE '%MP MX%',ac_id,NULL)) as AbandonedMP,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMP,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND Canal LIKE '%MP MX%',ac_id,NULL)) as TransferidasMinMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA20CallsMP,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%MP MX%',ac_id,NULL)) as SLA30CallsMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeMP,
				SUM(IF(Answered=1 AND Canal LIKE '%MP MX%', TIME_TO_SEC(Espera),0)) as WaitingTimeMP,
				COUNT(IF(Canal LIKE '%Intertours%',ac_id,NULL)) as VolumenIT,
				COUNT(IF(Answered=1 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AnsweredIT,
				COUNT(IF(Answered=0 AND Canal LIKE '%Intertours%',ac_id,NULL)) as AbandonedIT,
				COUNT(IF(Desconexion='Transferida' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasIT,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND Canal LIKE '%Intertours%',ac_id,NULL)) as TransferidasMinIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:20' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA20CallsIT,
				COUNT(IF(Answered=1 AND Espera<='00:00:30' AND Canal LIKE '%Intertours%',ac_id,NULL)) as SLA30CallsIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeIT,
				SUM(IF(Answered=1 AND Canal LIKE '%Intertours%', TIME_TO_SEC(Espera),0)) as WaitingTimeIT,
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
				t_Answered_Calls a
			LEFT JOIN
				Cola_Skill b
			ON
				a.Cola=b.Cola
			LEFT JOIN
				Dids d
			ON
				a.DNIS=d.DID AND
				a.Fecha>=d.Fecha
			LEFT JOIN
				Asesores c
			ON
				a.asesor=c.id
			WHERE
				Skill=$dept AND
				a.Fecha BETWEEN '$from' AND '$to'

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

    $result=mysql_query($query);
    if(mysql_error()){
        echo mysql_error()."<br>";
    }
    $num=mysql_numrows($result);
    $numfield=mysql_num_fields($result);
    $i=0;
    while($i<$num){
        $x=0;
        while($x<$numfield){
            $data_total[mysql_field_name($result,$x)][$skill]=mysql_result($result,$i,mysql_field_name($result,$x));
        $x++;
        }

    $i++;
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


?>

<div id="accordion">

  <h3>Metas</h3>
    <div>
    <table style="margin: auto; width: 80%;text-align:center" class='t2'>
        <tr class='title'>
            <th>Metrica</th><th>AHT</th><th>SLA</th><th>ASA</th><th>Abandon</th><th>Adherencia</th><th>Ocupacion</th><th>Rotacion</th><th>FC (conversion)</th>
        </tr>
        <tr class='pair'>
            <td class='subtitle'>Formula</td><td>Tiempo promedio de transaccion sumando (Talk time + ACW + Hold)</td><td>Total de llamadas Contestadas &#60= Umbral /Llamadas Ofrecidas</td>
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

  <h3>Informacion Por Dia</h3>
    <div>
        <div id='container' style='height: 800px; overflow: scroll; position: relative'>
        <table width='100%' class='tablesorter' id='tablesorter' style='text-align:center'>
            <thead>
                <tr>
                    <th>Metrica</th>
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
    <h3>Informacion Acumulado</h3>
        <div>
        <div id='container_total' style='height: 800px; overflow: scroll; position: relative'>
        <table width='100%' class='tablesorter' id='tablesorter_total' style='text-align:center'>
            <thead>
                <tr>
                    <th>Metrica</th>
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
</div>

<div id='container_export' style='height: 800px; overflow: scroll; position: relative'>
<table width='100%' class='tablesorter' id='tablesorter_export' style='text-align:center'>
    <thead>
        <tr>
            <th>Metrica</th>
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
                        if($metrica=='Abandon'){$class="class='nocumple'";}else{$class="";}
                        echo "<td $class>".format_var($metrica,$data4)."</td>";
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

<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];


if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="tablas_f";

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
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script>
  $(function() {
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		maxDate: '2016.07.11',
		animation: true
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
            widgets: [ 'zebra','output'],
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
        $('#tablesorter').trigger('outputTable');

    });


    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });


    }
);
  </script>

<?php 
include("../common/menu.php");

?>

<table style='width:800px; margin: auto' class='t2'><form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
    <tr class='title'>
         <th colspan="100">Tabla F <?php  echo $data[departamento][0]; ?></th>
    </tr>
    <tr class='title'>
         <td>Departamento:</td>
         <td>Fecha inicial</td>
         <td class='total' rowspan=2><input type="submit" name='consulta' value='consulta' /></td>
    </tr>
    <tr class='pair'>
         <td class='pair'><select name="depto" required><?php  list_departamentos($dept); ?></select></td>
         <td><input type="text" id="from" name="from" value='<?php echo $from;?>' required><input type="text" id="to" name="to" value='<?php echo $to;?>' required></td>
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
    $query="SELECT * FROM PCRCs WHERE id=$dept";
    $kind=mysql_result(mysql_query($query),0,'inbound_calls');
    if($_GET['reports']==1){$r="r";}
    switch($kind){
        case 1:
            include($r."inbound.php");
            break;
        default:
            switch($dept){
                case 6:
                    include($r."bo.php");
                    break;
                default:
                   echo "En construccion";
           }
            break;

    }
?>

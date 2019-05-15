<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="asesor_cuartiles";
$menu_asesores="class='active'";

$cheat=$_GET['cheat'];
?>

<?php
include("../connectDB.php");
//header("Content-Type: text/html;charset=utf-8");

//GET Variables
$dep=$_POST['pcrc'];
if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d',strtotime('-1 months'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d',strtotime('-1 days'));}
$perc_defined=0.8;

//SELECT functions
function printDeps($variable){
    $query="SELECT a.id as id, Departamento
            FROM PCRCs_Parent a LEFT JOIN PCRCs b
            ON a.id=b.id
            WHERE Cuartiles=1 ORDER BY Departamento";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while($i<$num){
        if($variable==mysql_result($result,$i,'id')){$selected="selected";}else{$selected="";}
        echo "<option value='".mysql_result($result,$i,'id')."' $selected>";
        echo mysql_result($result,$i,'Departamento');
        echo "</option>\n";
    $i++;
    }

}

function printMonth($variable){
    $i=1;
    while($i<=12){
        if($variable==$i){$selected="selected";}else{$selected="";}
        echo "<option value='$i' $selected>";
        $date="2016-$i-01";
        echo date('F',strtotime($date));
        echo "</option>\n";
    $i++;
    }
}

function printYear($variable){
    $query="SELECT DISTINCT YEAR(Fecha) as year FROM t_Answered_Calls";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while($i<$num){
        if($variable==mysql_result($result,$i,'year')){$selected="selected";}else{$selected="";}
        echo "<option value='".mysql_result($result,$i,'year')."' $selected>";
        echo mysql_result($result,$i,'year');
        echo "</option>\n";
    $i++;
    }
}

include("../common/scripts.php");

?>
<style>
    .selector {
        width: 140px;
        padding:5px;
        border: 0px solid;
        margin: auto;
       }
       .selector .option1{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: green;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option2{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: #b3b300;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option3{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: orange;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option4{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: red;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .qlegend{
            font: 12px arial, sans-serif;
            background-color:  #fffae6;
            color: orange;
            width: 100%;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 0px;
            margin-top:2px;
            margin-bottom:2px;
        }

</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>

<script>
$(function(){
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		<?php
			if($cheat!=1){
				echo "maxDate: '2016.07.11',";
			}
		?>
		animation: true
	});
});
</script>
<?php
include("../common/menu.php");
?>
<table class='t2' style='width:800; margin: auto'><form action="<?php $_SERVER['PHP_SELF'] ?>" method="Post">
    <tr class='title'>
        <th colspan=100>Cuartiles por Programa</th>
    </tr>
    <tr class='subtitle'>
        <td width='14%' >Periodo</td>
        <td width='14%'  class='pair'><input type="text" id='from' name='from' value='<?php echo $from; ?>' required/><input type="text" id='to' name='to' value='<?php echo $to; ?>' required/></td>
        <td width='14%' >PCRC</td>
        <td width='14%'  class='pair'><select name="pcrc" id="pcrc" required><option value="">Select...</option>><?php printDeps($dep); ?></select></td>
        <td class='total'><input type="submit" name="consultar"></td>
    </tr>
</form></table>
<br><br>


<?php if(!isset($_POST['consultar'])){exit;}

$query="SELECT * FROM PCRCs WHERE id=$dep";
$tipo_dep=mysql_result(mysql_query($query),0,'inbound_calls');

switch($tipo_dep){
    case 1:
        include("inbound.php");
        break;
    case 0:
        switch($dep){
            case 5:
                include("upsell.php");
                break;
            default:
                include("backoffice.php");
                break;
        }
        break;

}

?>

